<?php

class login extends controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('login_model');
        
        $this->load->model('admin_model');
        
        $this->lang->load('admin');
    }
    
    function index()
    {
        $language = 'en';
        $this->form->set_rules('username', $this->lang->line('username'), 'required');
        $this->form->set_rules('password', $this->lang->line('password'), 'required');
        
        if($this->form->run()) {
            if($this->login_model->login($this->generate_login_salt(), $_POST) != false) {
                switch($_POST['language']) {
                    default:
                    case 'English':
                        $language = 'en';
                        break;
                    case 'Rom&#226;n&#259;':
                        $language = 'ro';
                        break;
                }
                
                $this->url->redirect(SITE_URL . 'en/admin');
            } else {
                // $this->alert->clear();
                $this->alert->add($this->lang->line('invalid_cred'), 'error', 'topRight', 5000);
            }
        }
        
        $data['languages'] = $this->admin_model->fetch_languages();
        
        $data['modules'] = $this->admin_model->fetch_modules($_SESSION['site']);
        
        $this->load->view('login', $data);
    }
    
    function generate_login_salt()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $random = 'h987gsdo8n7g4s8o7n9isg49n7';
        $rand = rand();
        
        $salt = sha1($ip . $random . $rand);
        
        return $salt;
    }
}

?>