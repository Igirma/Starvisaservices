<?php

require_once APP_PATH . 'modules/admin/controllers/admin.php';

class colors extends admin
{
    function __construct()
    {
        parent::__construct();
        
        $this->lang->load('colors');
        
    }
    
    function index()
    {
        $this->form->set_rules('active[]', 'Online plaatsen?', 'numeric');
        if ($this->form->run()) {
            $this->colors_model->update_overview($_POST);
            
            if (!$this->db->error) {
                $this->alert->add($this->lang->line('success'), 'success');
                $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
            }
        }
        $data = $this->colors_model->fetch_all();
        $data['languages'] = $this->languages_model->fetch_all();

        $this->load->view('colors_overview', $data);
    }
    
    function add()
    {
        $this->form->set_rules('colors[title]', $this->lang->line('title'), 'required');

        if ($this->form->run()) {
            $colors_id = $this->colors_model->add($_POST);
            if (!$this->db->error) {
                $this->alert->add($this->lang->line('success'), 'success');

                if (isset($_POST['save_and_back'])) {
                    $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
                } else {
                    $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $colors_id . '/' . $this->config->item('default_language'));
                }
            }
        }
        $this->load->view('colors_add_edit');
    }
    
    function edit()
    {
        $this->form->set_rules('colors[title]', $this->lang->line('title'), 'required');
        
        $colors_id = $this->url->segment(3);
        $language = $this->url->segment(4);
        
        if ($language == '') {
            $language = $this->config->item('default_language');
        }
        if ($this->form->run()) {
            
            $this->colors_model->edit($_POST, $colors_id, $language);
            if (!$this->db->error) {
                $this->alert->add($this->lang->line('success'), 'success');
                
                if (isset($_POST['save_and_back'])) {
                    $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
                } else {
                    $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $colors_id . '/' . $language);
                }
            }
        }
        $data = $this->colors_model->fetch($colors_id, $language);
        $data['languages'] = $this->languages_model->fetch_all();
        
        $this->load->view('colors_add_edit', $data);
    }
    
    function delete()
    {
        $id = $this->url->segment(3);
        $this->colors_model->delete($id);
        
        if (!$this->db->error) {
            $this->alert->add($this->lang->line('success'), 'success');
            $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
        }
    }
    
}

?>