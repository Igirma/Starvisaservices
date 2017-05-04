<?php

class about extends controller
{
    function __construct()
    {
        parent::__construct();
        $this->lang->load('front', LANG_CODE);
        $this->load->model('pages_model');
    }
    function index()
    {
        $data['slugs'] = array(
            'login' => getSlugOnController('login'),
            'signin' => getSlugOnController('signin'),
            'profile' => getSlugOnController('profile')
        );

        $data['page'] = $this->pages_model->fetch_page($this->url->segment(0), CUR_LANG);

        if ($data['page'] != false) {
            $this->load->view('about', $data);
        } else {
            $this->url->redirect(BASE_URL);
        }
    }
}

?>