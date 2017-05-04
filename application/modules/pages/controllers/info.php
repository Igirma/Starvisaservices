<?php

class info extends controller
{
    function __construct()
    {
        parent::__construct();
        $this->lang->load('front.php');
        $this->load->model('pages_model');
        $this->load->model('home_model');
    }
    
    function index()
    {
        if (!$data['page'] = $this->pages_model->fetch_page($this->url->segment(0), CUR_LANG)) {
            $this->url->redirect(BASE_URL);
        }

        if ((!isset($this->load->users->user['users_params']['selected']['users_type_id']) || !isset($this->load->users->user['users_params']['params'])) && !isset($_SESSION['preview'])) {
            $this->url->redirect(BASE_URL);
        }

        $data['slugs'] = array(
            'home' => getSlugOnController('home')
        );

        $data['user'] = $this->load->users->user['users_params'];
        $data['home'] = $this->pages_model->fetch_page('', CUR_LANG, 0, 'home');

		$data['users_destination_id'] = prepend_select($this->load->users->destinations(), 'Select Destination');
		$data['users_nationality_id'] = $this->load->users->nationalities($data['user']['selected']['users_destination_id'], false);
		$data['users_nationality_id'] = prepend_select($data['users_nationality_id'], 'Select Nationality');
		$data['users_type_id'] = prepend_select($this->load->users->get_types($data['user']['selected']['users_nationality_id']), 'Select Type');
		
		if(isset($_SESSION['preview'])) {
			$params = $_SESSION['preview'];
		}
		else {
			$params = reset($data['user']['params']);
			$params['users_country_group_id'] = $params['users_destination_id'];

			$data['typeData'] = $this->load->users->get_type($params);
		}
		
		$params['users_type_id'] = $data['user']['selected']['users_type_id'];
		
		// format_costs($params, $invitation)
		// $invitation ::	0 => Unspecified
		//					2 => With Invitation
		//					3 => Without Invitation

        $data['costs'] = $this->load->users->format_costs($params, isset($_SESSION['invitation']) ? $_SESSION['invitation'] : 0);

        //debug($params);

        $this->load->view('info', $data);
    }
}

?>