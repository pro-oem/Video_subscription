<?php
class AdminController extends Controller {
    public function __construct() {
        parent::__construct();
        try {
            $this->requireAdmin();
        } catch (Exception $e) {
            error_log("Admin access error: " . $e->getMessage());
            header('Location: ' . BASE_URL . '/auth/login');
            exit();
        }
    }

    private function isAdmin() {
        try {
            $user = $this->db->query(
                "SELECT is_admin FROM users WHERE id = ? AND status = 'active' LIMIT 1", 
                [$_SESSION['user_id']]
            );
            return $user && $user->is_admin == 1;
        } catch (Exception $e) {
            error_log("isAdmin check error: " . $e->getMessage());
            return false;
        }
    }

    public function index() {
        try {
            $content = $this->db->query("SELECT * FROM content ORDER BY created_at DESC");
            $this->view('admin/dashboard', ['content' => $content]);
        } catch (Exception $e) {
            error_log("Admin dashboard error: " . $e->getMessage());
            $this->view('errors/error', [
                'message' => 'An error occurred while loading the dashboard'
            ]);
        }
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