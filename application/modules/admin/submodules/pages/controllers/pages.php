<?php

require_once APP_PATH . 'modules/admin/controllers/admin.php';
require_once SYS_PATH . 'core/photo.php';

class pages extends admin
{
    function __construct()
    {
        parent::__construct();
        
        $this->lang->load('pages');
        
        $this->image_settings = array(
            array(
                'sub_folder' => 'thumb',
                'width' => PAGES_IMG_THUMB_W,
                'height' => PAGES_IMG_THUMB_H,
                'type' => 'crop'
            ),
            array(
                'sub_folder' => 'normal',
                'width' => PAGES_IMG_NORMAL_W,
                'height' => PAGES_IMG_NORMAL_H,
                'type' => 'resize'
            ),
            array(
                'sub_folder' => 'image',
                'width' => PAGES_IMG_DETAIL_W,
                'height' => PAGES_IMG_DETAIL_H,
                'type' => 'crop'
            ),
            array(
                'sub_folder' => 'background',
                'width' => 1280,
                'height' => 850,
                'type' => 'crop'
            ),
            array(
                'sub_folder' => 'max',
                'width' => PAGES_IMG_MAX_W,
                'height' => PAGES_IMG_MAX_H,
                'type' => 'resize'
            )
        );
        
    }

    function index()
    {
        $this->form->set_rules('active[]', 'Online plaatsen?', 'numeric');
        $this->form->set_rules('main_menu[]', 'In menu tonen?', 'numeric');
        
        if($this->form->run()) {
            $this->pages_model->update_overview($_POST);
            
            if(!$this->db->error) {
                $this->alert->add($this->lang->line('success'), 'success');
                $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
            }
        }
        
        $data['pages'] = $this->pages_model->fetch_all();
        
        $data['languages'] = $this->languages_model->fetch_all();
        
        $data['default_language'] = $this->config->item('default_language');
        
        $this->load->view('pages_overview', $data);
    }
    
    function add()
    {
        if(isset($_POST['external']) && $_POST['external'] == 1) {
            $this->form->set_rules('form[page_content][ex_url]', $this->lang->line('ex_url'), 'required|prep_url');
        }
        
        (isset($_POST['form']['page_content']['slug']) && $_POST['form']['page_content']['slug'] == '' ? $_POST['form']['page_content']['slug'] = $this->url->string_to_url($_POST['form']['page_content']['content_title']) : '');
        
        if(isset($_POST['external']) && $_POST['external'] == 0) {
            $this->form->set_rules('form[page_content][content_title]', $this->lang->line('content_title'), 'required');
            $this->form->set_rules('form[page_content][slug]', $this->lang->line('slug'), 'unique_url_add');
            
            if($this->config->item('mobile_website') == 1) {
                (isset($_POST['form']['mobile_content']['slug']) && $_POST['form']['mobile_content']['slug'] == '' ? $_POST['form']['mobile_content']['slug'] = $this->url->string_to_url($_POST['form']['mobile_content']['content_title']) : '');
                $this->form->set_rules('form[mobile_content][content_title]', $this->lang->line('content_title'), 'required');
                $this->form->set_rules('form[mobile_content][menu_title]', $this->lang->line('menu_title'), 'required');
                $this->form->set_rules('form[mobile_content][slug]', $this->lang->line('slug'), 'unique_url_add');
            }
        }
        
        $this->form->set_rules('form[page_content][menu_title]', $this->lang->line('menu_title'), 'required');
        
        if($this->form->run()) {
            $id = $this->pages_model->add($_POST);
            
            $_FILES['photos'] = rearrange_files($_FILES['photos']);
            $_FILES['home'] = rearrange_files($_FILES['home']);
            $_FILES['slide'] = rearrange_files($_FILES['slide']);
            $_FILES['docs'] = rearrange_files($_FILES['docs']);
            
            if(!empty($_FILES['photos'])) {
                foreach($_FILES['photos'] as $media) {
                    $photo = new photo($media, $this->image_settings, MEDIA_DIR, CONTROLLER, $id, 0, $media['name']);
                }
            }

            if(!empty($_FILES['home'])) {
                foreach($_FILES['home'] as $media) {
                    $photo = new photo($media, $this->image_settings, MEDIA_DIR, CONTROLLER, $id, 1, $media['name']);
                }
            }
            
            if(!empty($_FILES['slide'])) {
                foreach($_FILES['slide'] as $media) {
                    $photo = new photo($media, $this->image_settings, MEDIA_DIR, CONTROLLER, $id, 2, $media['name']);
                }
            }

            if(!empty($_FILES['docs']))
            {
              foreach($_FILES['docs'] as $doc)
              {
                add_doc($doc);
                $this->admin_model->add_doc($doc, $id);
              }
            }
            
            $this->videos_model->attach_videos($_POST['page']['video_id'], $id);

            if(!$this->db->error) {
                if(!$this->db->error) {
                    $this->alert->add($this->lang->line('success'), 'success');
                    
                    if(isset($_POST['save_and_back'])) {
                        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
                    } else {
                        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $id . '/' . $this->config->item('default_language'));
                    }
                }
            }
        }
        
        $data['drop_down'] = $this->pages_model->fetch_drop_down();
        $data['page']['filters'] = $this->pages_model->getFilters('pages', $this->config->item('default_language'));
        //$data['videos'] = $this->videos_model->fetch_active_videos($this->config->item('default_language'));
        
        $this->load->view('pages_add_edit', $data);
    }
    
    function edit()
    {
        $page_id = $this->url->segment(3);
        $language = $this->url->segment(4);
        
        if($language == '') {
            $language = $this->config->item('default_language');
        }
        
        if(isset($_POST['external']) && $_POST['external'] == 1) {
            $this->form->set_rules('form[page_content][ex_url]', $this->lang->line('ex_url'), 'required|prep_url');
        }
        
        (isset($_POST['form']['page_content']['slug']) && $_POST['form']['page_content']['slug'] == '' ? $_POST['form']['page_content']['slug'] = $this->url->string_to_url($_POST['form']['page_content']['content_title']) : '');
        
        if(isset($_POST['external']) && $_POST['external'] == 0) {
            $this->form->set_rules('form[page_content][content_title]', $this->lang->line('content_title'), 'required');
            $this->form->set_rules('form[page_content][slug]', $this->lang->line('slug'), 'unique_url_edit[' . $page_id . ']');
            
            if($this->config->item('mobile_website') == 1) {
                (isset($_POST['form']['mobile_content']['slug']) && $_POST['form']['mobile_content']['slug'] == '' ? $_POST['form']['mobile_content']['slug'] = $this->url->string_to_url($_POST['form']['mobile_content']['content_title']) : '');
                $this->form->set_rules('form[mobile_content][content_title]', $this->lang->line('content_title'), 'required');
                $this->form->set_rules('form[mobile_content][menu_title]', $this->lang->line('menu_title'), 'required');
                $this->form->set_rules('form[mobile_content][slug]', $this->lang->line('slug'), 'unique_url_edit[' . $page_id . ']');
            }
        }
        
        $this->form->set_rules('form[page_content][menu_title]', $this->lang->line('menu_title'), 'required');
        
        if($this->form->run()) {
            $info = $this->pages_model->fetch($page_id, $language);
            
            $this->pages_model->edit($page_id, $language, $_POST);
            
            if($info['form']['page_content']['slug'] != $_POST['form']['page_content']['slug']) {
                $this->admin_model->links_crawl(SITE_URL_WOHTTP . $info['form']['page_content']['slug'], SITE_URL_WOHTTP . $this->url->string_to_url($_POST['form']['page_content']['slug']));
            }
            
            $_FILES['photos'] = rearrange_files($_FILES['photos']);
            $_FILES['home'] = rearrange_files($_FILES['home']);
            $_FILES['slide'] = rearrange_files($_FILES['slide']);
            $_FILES['docs'] = rearrange_files($_FILES['docs']);
            
            if (!empty($_FILES['photos'])) {
                foreach($_FILES['photos'] as $media) {
                    $photo = new photo($media, $this->image_settings, MEDIA_DIR, CONTROLLER, $page_id, 0, $media['name']);
                }
            }
            if (!empty($_FILES['home'])) {
                foreach($_FILES['home'] as $media) {
                    $photo = new photo($media, $this->image_settings, MEDIA_DIR, CONTROLLER, $page_id, 1, $media['name']);
                }
            }
            if (!empty($_FILES['slide'])) {
                foreach($_FILES['slide'] as $media) {
                    $photo = new photo($media, $this->image_settings, MEDIA_DIR, CONTROLLER, $page_id, 2, $media['name']);
                }
            }
            if(!empty($_FILES['docs']))
            {
              foreach($_FILES['docs'] as $doc)
              {
                add_doc($doc);
                $this->admin_model->add_doc($doc, $page_id);
              }
            }

            //$this->videos_model->attach_videos($_POST['page']['video_id'], $page_id);

            if(!$this->db->error) {
                $this->cache->clear();

                if(!$this->db->error && empty($this->form->_error_messages)) {
                    $this->alert->add($this->lang->line('success'), 'success');
                    if(isset($_POST['save_and_back'])) {
                        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
                    } elseif(isset($_POST['upload'])) {
                        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $page_id . '/' . $language . '/anchor_media');
                    } else {
                        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $page_id . '/' . $language);
                    }
                }
            }
        }
        
        if(isset($_POST['ajax'])) {
            $filename = $_POST['filename'];
            
            $crop = new crop($filename, PAGES_DIR_CROP_REPLACE);
            $crop->resizeImage(PAGES_CROP_MAX_W, PAGES_CROP_MAX_H, $_POST['x'], $_POST['x2'], 'crop');
            $crop->saveImage($filename, 100);
        }
        
        $data['page'] = $this->pages_model->fetch($page_id, $language);
        $data['count_children'] = $this->pages_model->count_children($page_id);
        $data['languages'] = $this->languages_model->fetch_all();
        $data['drop_down'] = $this->pages_model->fetch_drop_down();
        $data['page']['filters'] = $this->pages_model->getFilters('pages', $language, $page_id);
        
        $data['page']['selected_videos'] = $this->videos_model->fetch_videos_selected($page_id);
        $data['videos'] = $this->videos_model->fetch_active_videos($this->config->item('default_language'));
        
        $this->load->view('pages_add_edit', $data);
    }
    
    function delete()
    {
        $id = $this->url->segment(3);
        
        $this->pages_model->delete($id);
        
        if(!$this->db->error) {
            $this->alert->add($this->lang->line('success'), 'success');
            $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
        }
    }
    
    function order()
    {
        $direction = $this->url->segment(3);
        $current_order = $this->url->segment(4);
        $parent_id = $this->url->segment(5);
        $page_id = $this->url->segment(6);
        
        $this->pages_model->order($direction, $current_order, $parent_id, $page_id);
        
        if(!$this->db->error) {
            $this->alert->add($this->lang->line('success'), 'success');
            $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/success');
        }
    }
    
    function order_media()
    {
        $direction = $this->url->segment(3);
        $table_id = $this->url->segment(4);
        $language_id = $this->url->segment(5);
        $current_order = $this->url->segment(6);
        
        $this->pages_model->order_media($direction, $table_id, $language_id, $current_order);
        
        if(!$this->db->error) {
            $this->alert->add($this->lang->line('success'), 'success');
            $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $table_id . '/' . $language_id . '/anchor_media');
        }
    }
    
    function delete_media()
    {
        $media_id = $this->url->segment(3);
        $page_id = $this->url->segment(4);
        $language_id = $this->url->segment(5);
        
        $this->pages_model->delete_media($media_id);
        
        if(!$this->db->error) {
            $this->alert->add($this->lang->line('success'), 'success');
            $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $page_id . '/' . $language_id . '/anchor_media');
        }
    }
}

?>