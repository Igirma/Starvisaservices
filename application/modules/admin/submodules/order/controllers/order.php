<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';
require_once SYS_PATH . 'core/photo.php';
require_once SYS_PATH . 'core/twitter.php';


class order extends admin
{
	function __construct()
	{
		parent::__construct();
		
		$this->lang->load('order');

	}
	
	function index()
	{
		$this->form->set_rules('active[]', 'Online plaatsen?', 'numeric');
		
		if($this->form->run())
		{
			$this->order_model->update_overview($_POST);
			
			if(!$this->db->error)
			{
				$this->alert->add($this->lang->line('success'), 'success');
				$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
			}
		}
		
		$data = $this->order_model->fetch_all();
		$data['first_month'] = $this->order_model->getFirstMonth();
		
		$this->load->view('order_overview', $data);
	}

	function add()
	{
		
		$this->form->set_rules('order[order_number]', $this->lang->line('order_number'), 'required');
		
		if($this->form->run())
		{
			$order_id = $this->order_model->add($_POST);
				
			if(!$this->db->error)
			{
				$this->alert->add($this->lang->line('success'), 'success');
				
				if(isset($_POST['save_and_back']))
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
				}
				else
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $order_id );
				}
			}
		}
		
		$data['order_status'] = $this->order_model->getStatusList();
		$data['payment'] = $this->order_model->getPaymentList();
		$data['client'] = $this->order_model->fetch_all_clients();
		$data['products'] = $this->order_model->getProducts();
		$data['order_number'] = $this->order_model->getOrder_number();
		$vat = getSettings();
		$data['vat_costs'] = $vat['vat'];
		
		$this->load->view('order_add', $data);
	}
	
	function edit()
	{
		$this->form->set_rules('order[order_number]', $this->lang->line('order_number'), 'required');
		
		$order_id = $this->url->segment(3);
		
		if($this->form->run())
		{
			$this->order_model->edit($_POST, $order_id);
			
			if(!$this->db->error)
			{
				$this->alert->add($this->lang->line('success'), 'success');
					
				if(isset($_POST['save_and_back']))
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
				}
				else
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $order_id);
				}
			}
		}

		$data = $this->order_model->fetch($order_id);

		$this->load->view('order_edit', $data);
	}
	
	function delete()
	{
		$id = $this->url->segment(3);
		
		$this->order_model->delete($id);
		
		if(!$this->db->error)
		{
			$this->alert->add($this->lang->line('success'), 'success');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
		}
	}
	function export()
	{
		$this->order_model->export();
		
		if(!$this->db->error)
		{
			$this->alert->add($this->lang->line('success'), 'success');
		}
	}
	
	
	
	function delete_product()
	{
		$product_id 	= $this->url->segment(3);
		$page_id 		= $this->url->segment(4);
		
		$this->order_model->delete_product($product_id, $page_id);
		
		if(!$this->db->error)
		{
			$this->alert->add($this->lang->line('success'), 'success');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $page_id );
		}
	}

	function change_status(){
		$order_id = $this->url->segment(3);
		$status = $this->url->segment(4);
		
		$this->order_model->edit('', $order_id, $status);
		
		$this->alert->add($this->lang->line('success'), 'success');
		$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER );
	}
	
	function change_order(){
		$order_type = $this->url->segment(3);
		$_SESSION['order_type'] = $order_type;
		$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
	}
	
}
?>