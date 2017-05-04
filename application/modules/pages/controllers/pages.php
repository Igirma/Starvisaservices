<?php

class pages extends controller
{
    function __construct()
    {
        parent::__construct();
        $this->lang->load('front.php');
        $this->load->model('pages_model');
    }
    
    function index()
    {
        $data['page'] = $this->pages_model->fetch_page(end($this->url->segments), CUR_LANG);

        if ($data['page'] != false) {
            $this->load->view('page', $data);
        } else {
            $this->url->redirect(BASE_URL);
        }
    }
}

?>