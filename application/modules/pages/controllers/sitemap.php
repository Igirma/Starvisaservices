<?php 
class sitemap extends controller
{
	function __construct()
	{
		parent::__construct();
		$this->lang->load('front.php');
		$this->load->model('pages_model');
		$this->load->model('sitemap_model');
	}
	
	function index()
	{	
				
		if(!isset($_SESSION['login_salt'])) $_SESSION['login_salt'] = '';
		if(end($this->url->segments) != $_SESSION['login_salt']) $segment = end($this->url->segments);
		else{
			if($this->url->segment(2) == $_SESSION['login_salt']){
				$parent = $this->url->segment(0);
				$segment = $this->url->segment(1);
			}
			else{
				$segment = $this->url->segment(0);
				
			}
		}
		if($segment == end($this->url->segments) && end($this->url->segments) != $this->url->segment(0)) $parent = $this->url->segment(0);
		$data['page'] = $this->pages_model->fetch_page($segment, CUR_LANG);
		
		// if data exists load the view else redirect to home
		if($data['page'] != false)
		{
			$data['sitemaps_items'] = $this->sitemap_model->fetch_sitemap(CUR_LANG);
				
			$this->load->view('sitemap', $data);
		}
		else
		{
			$this->url->redirect(BASE_URL);
		}
		
	}
}
?>