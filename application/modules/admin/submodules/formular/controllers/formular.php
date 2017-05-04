<?php

require_once APP_PATH . 'modules/admin/controllers/admin.php';
require_once SYS_PATH . 'core/photo.php';
require_once SYS_PATH . 'core/twitter.php';


class formular extends admin
{
    function __construct()
    {
        parent::__construct();
        $this->lang->load('formular');
    }
    
    function index()
    {
        $this->form->set_rules('active[]', 'Online plaatsen?', 'numeric');
        
        if ($this->form->run()) {
            $this->formular_model->update_overview($_POST);
            
            if (!$this->db->error) {
                $this->alert->add($this->lang->line('success'), 'success');
                $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
            }
        }
        
        $data = $this->formular_model->fetch_all();
        $data['languages'] = $this->languages_model->fetch_all();
        $this->load->view('formular_overview', $data);
    }
    
    function add()
    {
        $this->form->set_rules('formular[title]', $this->lang->line('title'), 'required');
        $this->form->set_message('formular_date', $this->lang->line('formular_date_length'));
        
        if ($this->form->run()) {
            $formular_id = $this->formular_model->add($_POST);
            
            if (!$this->db->error) {
                $this->alert->add($this->lang->line('success'), 'success');

                if (isset($_POST['save_and_back'])) {
                    $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
                } else {
                    $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $formular_id . '/' . $this->config->item('default_language'));
                }
            }
        }
        
        $data['drop_down'] = $this->pages_model->fetch_drop_down();
        $this->load->view('formular_add_edit', $data);
    }
    
    function edit()
    {
        $this->form->set_rules('formular[title]', $this->lang->line('title'), 'required');
        $this->form->set_message('formular_date', $this->lang->line('formular_date_length'));
        
        $formular_id = $this->url->segment(3);
        $language_id = $this->url->segment(4);
        
        if ($this->form->run()) {
            
            $this->formular_model->edit($_POST, $formular_id, $language_id);
            
            if (!$this->db->error) {
                $this->alert->add($this->lang->line('success'), 'success');
                
                if (isset($_POST['save_and_back'])) {
                    $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
                } elseif (isset($_POST['upload'])) {
                    $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $formular_id . '/' . $language_id . '/anchor_media');
                } else {
                    $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $formular_id . '/' . $language_id);
                }
            }
        }
        
        $data = $this->formular_model->fetch($formular_id, $language_id);
        
        $data['formular']['language'] = $this->languages_model->fetch($language_id);
        $data['drop_down'] = $this->pages_model->fetch_drop_down();
        $data['formular']['page_id'] = $this->formular_model->fetch_all_pages_selected($formular_id);
        $data['formular']['language_id'] = $language_id;
        $data['form_items'] = $this->formular_model->fetch_items($formular_id, $language_id);
        
        $data['languages'] = $this->languages_model->fetch_all();
        
        $this->load->view('formular_add_edit', $data);
    }
    
    function delete()
    {
        $id = $this->url->segment(3);
        
        $this->formular_model->delete($id);
        
        if (!$this->db->error) {
            $this->alert->add($this->lang->line('success'), 'success');
            $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
        }
    }
    
}

?>