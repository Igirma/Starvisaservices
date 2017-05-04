<?php 
require_once APP_PATH . 'modules/admin/controllers/admin.php';

class webshop extends admin
{
	function __construct()
	{
		parent::__construct();
		$this->lang->load('webshop');
	}
	
	function index()
	{
		$PHPMAILER =& load_class('PHPGraphLib', 'core');

		//$graph = new PHPGraphLib(650,350, BASE_PATH.MEDIA_DIR.'graph.png');
		$month = date('n');
		$year = date("Y");
		
		if(isset($_POST['month'])){
			$date_info = explode("-",$_POST['month']);
			$month = $date_info[1];
			$year = $date_info[0];
		}
		
		$data = $this->webshop_model->getSales($year, $month);
		
		//$graph->addData($data['graph_data']);
		//$graph->setTitle('');
		//$graph->setGradient('silver', 'silver');
		//$graph->createGraph();
		
		$data['year'] = $year;
		$data['month'] = $month;
		
		$data['vat_total'] = $this->webshop_model->getVatTotal($year, $month);
		$data['order_total'] = $data['order_total'];
		$data['transport_total'] = $data['transport_total'];
		$data['drop_down_date'] = $this->webshop_model->getFirstMonth();
		$data['products'] = $this->webshop_model->get_products($year, $month);
		
		$this->load->view('webshop_overview', $data);
	}
	
	function month()
	{
		$PHPMAILER =& load_class('PHPGraphLib', 'core');

		//$graph = new PHPGraphLib(650,350, BASE_PATH.MEDIA_DIR.'graph.png');
		$month = date('n');
		$year = date("Y");
		
		$month_data = $this->url->segment(3);
		
		if(isset($month_data)){
			$date_info = explode("-",$month_data);
			$month = $date_info[1];
			$year = $date_info[0];
		}
		
		$data = $this->webshop_model->getSales($year, $month);
		
		//$graph->addData($data['graph_data']);
		//$graph->setTitle('');
		//$graph->setGradient('silver', 'silver');
		//$graph->createGraph();
		
		$data['year'] = $year;
		$data['month'] = $month;
		
		$data['vat_total'] = $this->webshop_model->getVatTotal($year, $month);
		$data['order_total'] = $data['order_total'];
		$data['transport_total'] = $data['transport_total'];
		$data['drop_down_date'] = $this->webshop_model->getFirstMonth();
		$data['products'] = $this->webshop_model->get_products($year, $month);
		
		$this->load->view('webshop_overview', $data);
	}

}

?>