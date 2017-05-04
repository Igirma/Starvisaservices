<?php

class contact extends controller
{
    function __construct()
    {
        parent::__construct();
        $this->lang->load('front', LANG_CODE);
        $this->load->model('pages_model');
        $this->load->model('home_model');
        $this->load->model('contact_model');
    }

    function index()
    {
        $data['slugs'] = array();

        if (preg_match('/contact_form/', $this->url->segment(1))) {
            $this->contact_form();
            exit;
        }

        $data['page'] = $this->pages_model->fetch_page($this->url->segment(0), CUR_LANG);

        if ($data['page'] != false) {
            $this->load->view('contact', $data);
        } else {
            $this->url->redirect(BASE_URL);
        }
    }

    private function contact_form()
    {
      if (!isset($_POST['args'])) {
          echo result('Missing params', 'error');
          exit;
      }

      $args = $_POST['args'];
      $error = array();

      if (strlen($args['name']) < 1) {
          $error[] = '- Please fill in your name';
      }
      if (strlen($args['email']) < 1) {
          $error[] = '- Please fill in your e-mail address';
      } elseif (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$/i", $args['email'])) {
          $error[] = '- Invalid e-mail address';
      }
      if (strlen($args['message']) < 1) {
          $error[] = '- Please fill in your enquiry';
      }

      if (count($error) > 0) {
          echo result(implode('<br>', $error), 'error');
          exit;
      }

      $sent = $this->contact_model->send_form($_POST['args']);
      if ($sent)
          echo result('Thank you! Your enquiry has been successfully sent.', 'success');
      else
          echo result('Server mail error. Please try again later.', 'error');
    }
}

?>