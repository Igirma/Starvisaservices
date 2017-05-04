<?php 
class preview extends controller
{
	function __construct()
	{
		parent::__construct();
		$this->lang->load('front.php');
		$this->load->model('preview_model', 'page');
	}

	function index()
	{
		if(isset($_SESSION['login_salt']))
		{
			$data = $this->preview_model->fetch($this->url->segment(1), $this->url->segment(2));
			
			if(!empty($data))
			{
				$this->load->view('page', $data);
			}
			else
			{
				$this->url->redirect(SITE_URL);
			}
		}
		else
		{
			$this->url->redirect(SITE_URL);
		}
		
	}
}
?>