<?php
class ContentController extends Controller {
    private $allowedMimes = [
        'video/mp4', 'video/webm', 
        'image/jpeg', 'image/png', 'image/webp'
    ];
    private $accessLogger;
    private $middleware;

    public function __construct() {
        parent::__construct();
        $this->accessLogger = AccessLogger::getInstance();
        $this->middleware = Middleware::getInstance();
        
        // Verify subscription and check for restrictions
        if (!$this->isSubscribed() || $this->accessLogger->checkUserRestrictions($_SESSION['user_id'])) {
            if ($this->isApiRequest()) {
                header('HTTP/1.1 403 Forbidden');
                echo json_encode(['error' => 'Access denied']);
                exit;
            }
            header('Location: ' . BASE_URL . '/auth/subscribe');
            exit();
        }
    }

    public function index() {
        $this->view('content/index', [
            'title' => 'Browse Content',
            'content' => $this->db->query("SELECT * FROM content ORDER BY created_at DESC")
        ]);
    }

    public function viewContent($id) {
        if (!$id) return;
        
        // Validate content access
        $content = $this->middleware->validateContentAccess($id);
        
        // Log content access
        $this->accessLogger->logAccess($id, $_SESSION['user_id'], 'view');
        
        // Generate secure access token for streaming
        $accessToken = $this->accessLogger->generateAccessToken($_SESSION['user_id'], $id);
        
        // Set security headers
        $this->preventContentCopy();
        
        $this->view('content/view', [
            'content' => $content,
            'accessToken' => $accessToken,
            'chatEnabled' => true
        ]);
    }

    public function stream($id, $token) {
        if (!$id || !$token) {
            header('HTTP/1.0 403 Forbidden');
            exit('Access Denied');
        }
        
        // Validate access token
        if (!$this->accessLogger->validateAccessToken($token, $_SESSION['user_id'], $id)) {
            header('HTTP/1.0 403 Forbidden');
            exit('Invalid or expired access token');
        }
        
        $content = $this->db->query("SELECT * FROM content WHERE id = ? LIMIT 1", [$id]);
        if (!$content || !in_array($content->content_type, $this->allowedMimes)) {
            header('HTTP/1.0 403 Forbidden');
            exit('Access Denied');
        }

        $file = $content->file_path;
        if (!file_exists($file)) {
            header('HTTP/1.0 404 Not Found');
            exit('File not found');
        }

        // Log streaming access
        $this->accessLogger->logAccess($id, $_SESSION['user_id'], 'stream');

        // Set security headers for streaming
        header('Content-Type: ' . $content->content_type);
        header('Content-Length: ' . filesize($file));
        header('Accept-Ranges: bytes');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('X-Content-Type-Options: nosniff');
        
        if (strpos($content->content_type, 'video') !== false) {
            // Add dynamic watermark for videos
            $watermarkedPath = Utils::addWatermark($file, $_SESSION['user_id']);
            $this->streamFile($watermarkedPath);
            unlink($watermarkedPath); // Clean up temporary file
        } else {
            // Handle range requests for video streaming
            if (isset($_SERVER['HTTP_RANGE'])) {
                $this->handleRangeRequest($file, $content->content_type);
            } else {
                $this->streamFile($file);
            }
        }
        exit;
    }

    private function streamFile($file) {
        $handle = fopen($file, 'rb');
        while (!feof($handle)) {
            echo fread($handle, 8192);
            flush();
        }
        fclose($handle);
    }

    private function handleRangeRequest($file, $contentType) {
        $size = filesize($file);
        $length = $size;
        $start = 0;
        $end = $size - 1;
        
        if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches)) {
            $start = intval($matches[1]);
            if (!empty($matches[2])) {
                $end = intval($matches[2]);
            }
        }
        
        if ($start > $end || $start > $size - 1 || $end >= $size) {
            header('HTTP/1.1 416 Requested Range Not Satisfiable');
            header("Content-Range: bytes $start-$end/$size");
            exit;
        }
        
        $length = $end - $start + 1;
        
        header('HTTP/1.1 206 Partial Content');
        header("Content-Range: bytes $start-$end/$size");
        header("Content-Length: " . $length);
        header("Content-Type: $contentType");
        
        $handle = fopen($file, 'rb');
        fseek($handle, $start);
        $buffer = 8192;
        while ($length) {
            $read = ($length > $buffer) ? $buffer : $length;
            $length -= $read;
            echo fread($handle, $read);
            flush();
        }
        fclose($handle);
    }

    public function chat() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        // Check rate limiting for chat
        if (!Utils::checkRateLimit($_SESSION['user_id'], 'chat', 60, 300)) { // 60 messages per 5 minutes
            echo json_encode(['error' => 'Rate limit exceeded']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $message = $input['message'] ?? '';

        if (empty($message)) {
            echo json_encode(['error' => 'No message provided']);
            exit;
        }

        try {
            $response = $this->getGeminiResponse($message);
            
            // Log chat interaction
            $this->db->query(
                "INSERT INTO chat_history (user_id, message, response) VALUES (?, ?, ?)",
                [$_SESSION['user_id'], $message, $response]
            );
            
            echo json_encode(['response' => $response]);
        } catch (Exception $e) {
            error_log("Chat API Error: " . $e->getMessage());
            echo json_encode(['error' => 'Failed to process message']);
        }
        exit;
    }

    private function getGeminiResponse($message) {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "x-goog-api-key: " . GEMINI_API_KEY
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                "contents" => [
                    ["parts" => [["text" => $message]]]
                ],
                "safetySettings" => [
                    [
                        "category" => "HARM_CATEGORY_HARASSMENT",
                        "threshold" => "BLOCK_MEDIUM_AND_ABOVE"
                    ]
                ],
                "generationConfig" => [
                    "temperature" => 0.7,
                    "topP" => 0.8,
                    "topK" => 40
                ]
            ])
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            throw new Exception("cURL Error: " . $err);
        }

        $result = json_decode($response, true);
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            return $result['candidates'][0]['content']['parts'][0]['text'];
        }

        throw new Exception("Invalid API response");
    }    protected function isApiRequest() {
        return (
            isset($_SERVER['HTTP_ACCEPT']) && 
            strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false
        ) || (
            isset($_SERVER['CONTENT_TYPE']) && 
            strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false
        );
    }
}