<?php
class AdminController extends Controller {
    public function __construct() {
        parent::__construct();
        // Check if user is admin
        if (!isset($_SESSION['user_id']) || !$this->isAdmin()) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit();
        }
    }

    private function isAdmin() {
        $user = $this->db->query("SELECT is_admin FROM users WHERE id = ? LIMIT 1", 
                                [$_SESSION['user_id']]);
        return $user && $user->is_admin == 1;
    }

    public function index() {
        $content = $this->db->query("SELECT * FROM content ORDER BY created_at DESC");
        $this->view('admin/dashboard', ['content' => $content]);
    }

    public function upload() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
            
            if (isset($_FILES['content']) && $_FILES['content']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['content'];
                $fileType = mime_content_type($file['tmp_name']);
                $isVideo = strpos($fileType, 'video') !== false;
                $isImage = strpos($fileType, 'image') !== false;
                
                if ($isVideo || $isImage) {
                    // Generate unique filename
                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = uniqid() . '.' . $extension;
                    $uploadPath = 'uploads/' . $filename;
                    
                    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                        $this->db->query(
                            "INSERT INTO content (title, description, file_path, content_type) 
                             VALUES (?, ?, ?, ?)",
                            [$title, $description, $uploadPath, $fileType]
                        );
                        
                        header('Location: ' . BASE_URL . '/admin');
                        exit();
                    }
                }
            }
        }
        
        $this->view('admin/upload');
    }

    public function delete($id) {
        if (!$id) return;
        
        $content = $this->db->query("SELECT file_path FROM content WHERE id = ? LIMIT 1", [$id]);
        if ($content && file_exists($content->file_path)) {
            unlink($content->file_path);
            $this->db->query("DELETE FROM content WHERE id = ?", [$id]);
        }
        
        header('Location: ' . BASE_URL . '/admin');
    }

    public function edit($id) {
        if (!$id) return;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
            
            $this->db->query(
                "UPDATE content SET title = ?, description = ? WHERE id = ?",
                [$title, $description, $id]
            );
            
            header('Location: ' . BASE_URL . '/admin');
            exit();
        }
        
        $content = $this->db->query("SELECT * FROM content WHERE id = ? LIMIT 1", [$id]);
        $this->view('admin/edit', ['content' => $content]);
    }
}