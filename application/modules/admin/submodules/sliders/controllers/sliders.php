<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';
require_once SYS_PATH . 'core/photo.php';

class sliders extends admin
{
    function __construct()
    {
        parent::__construct();
        
        $this->lang->load('admin');
        $this->lang->load('sliders');
        
        $this->image_settings = array(
            array(
                'sub_folder' => 'thumb',
                'width' => SLIDERS_IMG_THUMB_W,
                'height' => SLIDERS_IMG_THUMB_H,
                'type' => 'crop'
            ),
            array(
                'sub_folder' => 'slider',
                'width' => SLIDERS_IMG_SLIDER_W,
                'height' => SLIDERS_IMG_SLIDER_H,
                'type' => 'crop'
            ),
            array(
                'sub_folder' => 'max',
                'width' => SLIDERS_IMG_MAX_W,
                'height' => SLIDERS_IMG_MAX_H,
                'type' => 'resize'
            )
        );
    }
    
    function index()
    {
        $this->form->set_rules('active[]', 'Online plaatsen?', 'numeric');
        
        if ($this->form->run()) {
            $this->sliders_model->update_overview($_POST);
            
            if (!$this->db->error) {
                // $this->alert->clear();
                $this->alert->add($this->lang->line('success'), 'success');
                $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
            }
        }
        
        $data['sliders'] = $this->sliders_model->fetch_all();
        $data['languages'] = $this->languages_model->fetch_all();
        
        $this->load->view('sliders_overview', $data);
    }
    
    function add()
    {
        $this->form->set_rules('slider[title]', 'title', 'required');
        
        if ($this->form->run()) {
            $slider_id = $this->sliders_model->add($_POST);
            $language_id = $this->config->item('default_language');
            
            $_FILES = rearrange_files($_FILES['photo']);
            
            if (!empty($_FILES)) {
                foreach ($_FILES as $media) {
                    $photo = new photo($media, $this->image_settings, MEDIA_DIR, CONTROLLER, $slider_id, 0, $media['name'], 0);
                }
            }
            
            $this->sliders_model->attach_pages($_POST, $slider_id);
            
            if (!$this->db->error) {
                // $this->alert->clear();
                $this->alert->add($this->lang->line('success'), 'success');
                
                if (isset($_POST['save_and_back'])) {
                    $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
                } else {
                    $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $slider_id . '/' . $language_id);
                }
            }
       }
        
        $data['drop_down'] = $this->pages_model->fetch_drop_down();

        $this->load->view('sliders_add_edit', $data);
    }
    
    function edit()
    {
        $this->form->set_rules('slider[title]', $this->lang->line('title'), 'required');
        
        $slider_id = $this->url->segment(3);
        $language_id = $this->url->segment(4);
        
        if ($this->form->run()) {
            $_FILES = rearrange_files($_FILES['photo']);
            
            if (!empty($_FILES)) {
                foreach ($_FILES as $media) {
                    $photo = new photo($media, $this->image_settings, MEDIA_DIR, CONTROLLER, $slider_id, 0, $media['name'], 0);
                }
            }
            
            $this->sliders_model->edit($_POST, $slider_id, $language_id);
            $this->sliders_model->attach_pages($_POST, $slider_id);
            
            if (!$this->db->error) {
                // $this->alert->clear();
                $this->alert->add($this->lang->line('success'), 'success');
                
                if (isset($_POST['save_and_back'])) {
                    $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
                } elseif (isset($_POST['upload'])) {
                    $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $slider_id . '/' . $language_id . '/anchor_media');
                } else {
                    $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $slider_id . '/' . $language_id);
                }
            }
        }
        
        if (isset($_POST['ajax'])) {
            $filename = $_POST['filename'];
            
            $crop = new crop($filename, SLIDERS_DIR_CROP_REPLACE);
            $crop->resizeImage(SLIDERS_CROP_MAX_W, SLIDERS_CROP_MAX_H, $_POST['x'], $_POST['x2'], 'crop');
            $crop->saveImage($filename, 100);
        }
        
        $data = $this->sliders_model->fetch($slider_id, $language_id);
        
        $data['slider']['language'] = $this->languages_model->fetch($language_id);
        
        $data['languages'] = $this->languages_model->fetch_all();
        
        $data['drop_down'] = $this->pages_model->fetch_drop_down();
        $data['pages_selected'] = $this->sliders_model->fetch_pages_selected($slider_id);

        $this->load->view('sliders_add_edit', $data);
    }
    
    function delete()
    {
        $id = $this->url->segment(3);
        
        $this->sliders_model->delete($id);
        
        if (!$this->db->error) {
            // $this->alert->clear();
            $this->alert->add($this->lang->line('success'), 'success');
            $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
        }
    }
    
    function order_media()
    {
        $direction = $this->url->segment(3);
        $table_id = $this->url->segment(4);
        $language_id = $this->url->segment(5);
        $current_order = $this->url->segment(6);
        
        $this->sliders_model->order_media($direction, $table_id, $language_id, $current_order);
        
        if (!$this->db->error) {
            // $this->alert->clear();
            $this->alert->add($this->lang->line('success'), 'success');
            $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $table_id . '/' . $language_id . '/anchor_media');
        }
    }
    
    function delete_media()
    {
        $media_id = $this->url->segment(3);
        $table_id = $this->url->segment(4);
        $language_id = $this->url->segment(5);
        
        $this->sliders_model->delete_media($media_id);
        
        if (!$this->db->error) {
            // $this->alert->clear();
            $this->alert->add($this->lang->line('success'), 'success');
            $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $table_id . '/' . $language_id . '/anchor_media');
        }
    }
    
    
    function order()
    {
        $direction = $this->url->segment(3);
        $current_order = $this->url->segment(4);
        $slider_id = $this->url->segment(5);
        
        $this->sliders_model->order($direction, $current_order, $slider_id);
        
        if (!$this->db->error) {
            // $this->alert->clear();
            $this->alert->add($this->lang->line('success'), 'success');
            $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/success');
        }
    }
}

?>