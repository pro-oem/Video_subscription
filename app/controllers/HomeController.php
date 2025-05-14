<?php
class HomeController extends Controller {
    public function index() {
        $this->preventContentCopy();
        $data = [
            'title' => 'Premium Content Platform',
            'isSubscribed' => $this->isSubscribed()
        ];
        
        if ($this->isSubscribed()) {
            $data['content'] = $this->db->query("SELECT * FROM content ORDER BY created_at DESC LIMIT 10");
        }
        
        $this->view('home/index', $data);
    }
}