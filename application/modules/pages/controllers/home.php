<?php

class home extends controller
{
    function __construct()
    {
        parent::__construct();
        $this->lang->load('front');
        $this->load->model('home_model');
        $this->load->model('pages_model');
        $this->load->model('news_model');
    }

    function index()
    {
        if (preg_match('/ajax/', $this->url->segment(1))) {
            $this->ajax();
            exit;
        }
		if (preg_match('/processwo/', $this->url->segment(1))) {
            $this->processwo();
            exit;
        }
        if (preg_match('/process/', $this->url->segment(1))) {
            $this->process();
            exit;
        }

        $data['slugs'] = array(
            'login' => getSlugOnController('login')
        );

        $data['page'] = $this->home_model->fetch_home(CUR_LANG);
        $data['about'] = $this->pages_model->fetch_page('', CUR_LANG, 0, 'about');

        $data['news'] = $this->pages_model->fetch_page('', CUR_LANG, 0, 'news');
        if ($data['news'] !== false) {
            $data['news']['news_items'] = $this->news_model->fetch_news(CUR_LANG, 4);
        }

        $data['users_destination_id'] = prepend_select($this->load->users->destinations(), 'Select Destination');

        $this->load->view('home', $data);
    }

    function ajax()
    {
		if(isset($_POST['args']['users_option_id'])) {
			$_SESSION['invitation'] = (int)$_POST['args']['users_option_id'];
		}
		
        if (!isset($_POST['args']) || !is_array($_POST['args'])) {
            echo result(array('content' => 'Missing params'), 'error');
            exit;
        }

        $show = isset($_POST['args']['show_content']);
        if ($show) {
            unset($_POST['args']['show_content']);
        }

        $update = array();
        
        $info = '<br><br><center>No information for your selection.</center><br><br><br><br>';
        $data['content'] = !$show ? strip_tags($info) : $info;

        if (isset($_POST['args']['users_destination_id']))
        {
            $data['users_nationality_id'] = $this->load->users->nationalities($_POST['args']['users_destination_id']);
        }
        elseif (isset($_POST['args']['users_nationality_id']))
        {
            $data['users_type_id'] = $this->load->users->get_types($_POST['args']['users_nationality_id']);
        }
        elseif (isset($_POST['args']['users_type_id']))
        {
            $this->load->users->saveType($_POST['args']['users_type_id']);
        }
		elseif (isset($_POST['args']['users_option_id']))
        {
			$data['users_option_id'] = $_POST['args']['users_option_id'];
        }
		
        if ($show) 
        {
            if (!$user = $this->load->users->get_temporary_user()) {
                echo result($data, 'error');
                exit;
            } else {
                if (isset($user['users_params']['selected']['users_type_id'])) 
                {
                    $params = reset($user['users_params']['params']);
                    $params['users_country_group_id'] = $params['users_destination_id'];
                    $params['users_type_id'] = $user['users_params']['selected']['users_type_id'];
					$params['users_option_id'] = $data['users_option_id'];
                    $data['content'] = $this->load->users->format_costs($params, isset($_SESSION['invitation']) ? $_SESSION['invitation'] : 0);
                    if (!$data['content']) 
                    {
                        $data['content'] = $info;
                        echo result($data, 'error');
                        exit;
                    }
                } else {
                    echo result($data, 'error');
                    exit;
                }
            }
        } else {
            if (isset($_POST['args']['users_type_id'])) 
            {
                if (!$user = $this->load->users->get_temporary_user()) {
                    echo result($data, 'error');
                    exit;
                } else {
                    if (isset($user['users_params']['selected']['users_type_id'])) 
                    {
                        $params = reset($user['users_params']['params']);
                        $params['users_country_group_id'] = $params['users_destination_id'];
                        $params['users_type_id'] = $user['users_params']['selected']['users_type_id'];
						$params['users_option_id'] = $data['users_option_id'];
                        $data['content'] = $this->load->users->format_costs($params, isset($_SESSION['invitation']) ? $_SESSION['invitation'] : 0);
                        if (!$data['content']) 
                        {
                            $data['content'] = strip_tags($info);
                            $data['results'] = strip_tags($info);
                            echo result($data, 'error');
                            exit;
                        }
                    } else {
                        echo result($data, 'error');
                        exit;
                    }
                }
            }
        }

        echo result($data, 'success');
        exit;
    }
    
    function process()
    {
        if (!isset($_POST['args']) || !is_array($_POST['args'])) {
            echo result('Missing params', 'error');
            exit;
        }
        
        if (!$this->load->users->user) {
            echo result('Please select your options', 'error');
            exit;
        }
        if ($_POST['args']['users_nationality_id'] == 0) {
            echo result('Please select your nationality', 'error');
            exit;
        }
        if ($_POST['args']['users_destination_id'] == 0) {
            echo result('Please select your destination', 'error');
            exit;
        }
        if ($_POST['args']['users_type_id'] == 0) {
            echo result('Please select your visa type', 'error');
            exit;
        }
		if ($_POST['args']['users_conditions_agree'] != true) {
			echo result('You have to agree with our Terms and Conditions', 'error');
            exit;
		}
		if (isset($_POST['args']['users_option_id']) && $_POST['args']['users_option_id'] != -1) {
			$_SESSION['invitation'] = (int)$_POST['args']['users_option_id'];
		}
		elseif (isset($_POST['args']['users_option_id']) && $_POST['args']['users_option_id'] == -1) {
			echo result('Please select your invitation type', 'error');
            exit;
		}
		elseif (!isset($_POST['args']['users_option_id']) && isset($_POST['args']['users_option_id_disabled'])) {
			$_POST['args']['users_option_id'] = 0;
		}
		else {
			echo result('Please select your invitation type', 'error');
            exit;
		}


        echo result(SITE_URL . getSlugOnController('info'), 'success');
        exit;
    }
}

?>