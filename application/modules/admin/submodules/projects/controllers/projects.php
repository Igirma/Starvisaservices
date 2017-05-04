<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';
require_once SYS_PATH . 'core/photo.php';
require_once SYS_PATH . 'core/twitter.php';


class projects extends admin
{
  function __construct()
  {
    parent::__construct();
    
    $this->lang->load('projects');

    $this->image_settings = array(
      array(
        'sub_folder' => 'thumb',
        'width' => PROJECTS_IMG_THUMB_W,
        'height' => PROJECTS_IMG_THUMB_H,
        'type' => 'crop'
      ),
      array(
        'sub_folder' => 'normal',
        'width' => PROJECTS_IMG_NORMAL_W,
        'height' => PROJECTS_IMG_NORMAL_H,
        'type' => 'crop'
      ),
      array(
        'sub_folder' => 'square',
        'width' => PROJECTS_IMG_SQUARE_W,
        'height' => PROJECTS_IMG_SQUARE_H,
        'type' => 'crop'
      ),
      array(
        'sub_folder' => 'logo',
        'width' => PROJECTS_IMG_THUMBNAIL_W,
        'height' => PROJECTS_IMG_THUMBNAIL_H,
        'type' => 'resize'
      ),
      array(
        'sub_folder' => 'max',
        'width' => PROJECTS_IMG_MAX_W,
        'height' => PROJECTS_IMG_MAX_H,
        'type' => 'resize'
      )
    );
  }
  
  function index()
  {
    $this->form->set_rules('active[]', 'Online plaatsen?', 'numeric');
    
    if($this->form->run())
    {
      $this->projects_model->update_overview($_POST);
      
      if(!$this->db->error)
      {
        // $this->alert->clear();
        $this->alert->add($this->lang->line('success'), 'success');
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
      }
    }
    
    $data = $this->projects_model->fetch_all();
    $data['languages'] = $this->languages_model->fetch_all();	
    $this->load->view('projects_overview', $data);
  }
  
  function add()
  {
    (isset($_POST['project']['slug']) && $_POST['project']['slug'] == '' ? $_POST['project']['slug'] = $this->url->string_to_url($_POST['project']['title']) : '');
    
    $this->form->set_rules('project[title]', $this->lang->line('title'), 'required');
    $this->form->set_message('project_date', $this->lang->line('project_date_length'));
    
    //$this->form->set_rules('project[slug]', $this->lang->line('slug'), 'unique_url_add');
    
    if($this->form->run())
    {
      $project_id = $this->projects_model->add($_POST);
      $this->brands_model->attach_filters($_POST['project']['partners'], $project_id);

      $_FILES['photos'] = rearrange_files($_FILES['photos']);
      $_FILES['photos_2'] = rearrange_files($_FILES['photos_2']);
      $_FILES['photos_3'] = rearrange_files($_FILES['photos_3']);
      $_FILES['cover'] = rearrange_files($_FILES['cover']);
      $_FILES['logo'] = rearrange_files($_FILES['logo']);
      $_FILES['docs'] = rearrange_files($_FILES['docs']);
      
      if(!empty($_FILES['photos']))
      {
        foreach($_FILES['photos'] as $media)
        {
          $photo = new photo($media, $this->image_settings, MEDIA_DIR, CONTROLLER, $project_id, 0, $media['name']);
        }
      }
      if(!empty($_FILES['photos_2']))
      {
        foreach($_FILES['photos_2'] as $media)
        {
          $photo = new photo($media, $this->image_settings, MEDIA_DIR, CONTROLLER, $project_id, 3, $media['name']);
        }
      }
      if(!empty($_FILES['photos_3']))
      {
        foreach($_FILES['photos_3'] as $media)
        {
          $photo = new photo($media, $this->image_settings, MEDIA_DIR, CONTROLLER, $project_id, 4, $media['name']);
        }
      }
      if(!empty($_FILES['logo']))
      {
        foreach($_FILES['logo'] as $media)
        {
          $photo = new photo($media, $this->image_settings, MEDIA_DIR, CONTROLLER, $project_id, 2, $media['name']);
        }
      }
      if(!empty($_FILES['cover']))
      {
        foreach($_FILES['cover'] as $media)
        {
          $photo = new photo($media, $this->image_settings, MEDIA_DIR, CONTROLLER, $project_id, 1, $media['name']);
        }
      }
      if(!empty($_FILES['docs']))
      {
        foreach($_FILES['docs'] as $doc)
        {
          add_doc($doc);
          $this->admin_model->add_doc($doc, $project_id);
        }
      }
        
      if(!$this->db->error)
      {
        // $this->alert->clear();
        $this->alert->add($this->lang->line('success'), 'success');
        
        if(isset($_POST['save_and_back']))
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
        }
        else
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $project_id . '/' . $this->config->item('default_language'));
        }
      }
    }
    
    //$data['project']['filters'] = $this->projects_model->getFilters('projects', $this->config->item('default_language'));
    $data['drop_down'] = $this->projects_model->fetch_drop_down(CONTROLLER);
    $data['categories'] = $this->projects_model->projects_categories();
    $data['partners'] = $this->projects_model->partners();
    
    $this->load->view('projects_add_edit', $data);
  }
  
  function edit()
  {
    $this->form->set_rules('project[title]', $this->lang->line('title'), 'required');
    $this->form->set_message('project_date', $this->lang->line('project_date_length'));
    
    (isset($_POST['project']['slug']) && $_POST['project']['slug'] == '' ? $_POST['project']['slug'] = $this->url->string_to_url($_POST['project']['title']) : '');

    $project_id = $this->url->segment(3);
    $language_id = $this->url->segment(4);
    
    //$this->form->set_rules('project[slug]', $this->lang->line('slug'), 'unique_url_edit[' . $project_id . ']');

    if($this->form->run())
    {
      $_FILES['photos'] = rearrange_files($_FILES['photos']);
      $_FILES['photos_2'] = rearrange_files($_FILES['photos_2']);
      $_FILES['photos_3'] = rearrange_files($_FILES['photos_3']);
      $_FILES['cover'] = rearrange_files($_FILES['cover']);
      $_FILES['logo'] = rearrange_files($_FILES['logo']);
      $_FILES['docs'] = rearrange_files($_FILES['docs']);
      
      if(!empty($_FILES['photos']))
      {
        foreach($_FILES['photos'] as $media)
        {
          $photo = new photo($media, $this->image_settings, MEDIA_DIR, CONTROLLER, $project_id, 0, $media['name']);
        }
      }
      if(!empty($_FILES['photos_2']))
      {
        foreach($_FILES['photos_2'] as $media)
        {
          $photo = new photo($media, $this->image_settings, MEDIA_DIR, CONTROLLER, $project_id, 3, $media['name']);
        }
      }
      if(!empty($_FILES['photos_3']))
      {
        foreach($_FILES['photos_3'] as $media)
        {
          $photo = new photo($media, $this->image_settings, MEDIA_DIR, CONTROLLER, $project_id, 4, $media['name']);
        }
      }
      if(!empty($_FILES['logo']))
      {
        foreach($_FILES['logo'] as $media)
        {
          $photo = new photo($media, $this->image_settings, MEDIA_DIR, CONTROLLER, $project_id, 2, $media['name']);
        }
      }
      if(!empty($_FILES['cover']))
      {
        foreach($_FILES['cover'] as $media)
        {
          $photo = new photo($media, $this->image_settings, MEDIA_DIR, CONTROLLER, $project_id, 1, $media['name']);
        }
      }

      if(!empty($_FILES['docs']))
      {
        foreach($_FILES['docs'] as $doc)
        {
          add_doc($doc);
          $this->admin_model->add_doc($doc, $project_id);
        }
      }

      $this->projects_model->edit($_POST, $project_id, $language_id);
      $this->brands_model->attach_filters($_POST['project']['partners'], $project_id);

      if(!$this->db->error)
      {
        // $this->alert->clear();
        $this->alert->add($this->lang->line('success'), 'success');
          
        if(isset($_POST['save_and_back']))
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
        }
        elseif(isset($_POST['upload']))
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $project_id . '/' . $language_id . '/anchor_media');
        }
        else
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $project_id . '/' . $language_id);
        }
      }
    }
    
    if(isset($_POST['ajax']))
    {
      $filename = $_POST['filename'];
      
      $crop = new crop($filename, PROJECTS_DIR_CROP_REPLACE);
      $crop->resizeImage(PROJECTS_CROP_MAX_W, PROJECTS_CROP_MAX_H, $_POST['x'], $_POST['x2'], 'crop');
      $crop->saveImage($filename, 100);
    }
    
    $data = $this->projects_model->fetch($project_id, $language_id);
    
    $data['project']['language'] = $this->languages_model->fetch($language_id);
    
    $data['languages'] = $this->languages_model->fetch_all();
    $data['drop_down'] = $this->projects_model->fetch_drop_down(CONTROLLER);
    $data['count_children'] = $this->projects_model->count_children($project_id);
    //$data['project']['category_id'] = $this->members_model->fetch_all_category_selected($project_id, CONTROLLER);
    
    if(isset($data['project']['category_id'])) 
      $data['project']['filters'] = $this->projects_model->getFilters('projects', $this->config->item('default_language'), $project_id, $data['project']['project_id']);
    
    $data['categories'] = $this->projects_model->projects_categories();
    $data['partners'] = $this->projects_model->partners();
    $data['partners_selected'] = $this->brands_model->fetch_brands_selected($project_id);

    $this->load->view('projects_add_edit', $data);
  }
  
  function delete()
  {
    $id = $this->url->segment(3);
    
    $this->projects_model->delete($id);
    
    if(!$this->db->error)
    {
      // $this->alert->clear();
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
    }
  }
  
  function delete_media()
  {
    $media_id 		= $this->url->segment(3);
    $page_id 		= $this->url->segment(4);
    $language_id 	= $this->url->segment(5);
    
    $this->projects_model->delete_media($media_id);
    
    if(!$this->db->error)
    {	
      // $this->alert->clear();
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $page_id . '/' . $language_id . '/anchor_media');
    }
  }
  
  function order_media()
  {
    $direction 		= $this->url->segment(3);
    $table_id 		= $this->url->segment(4);
    $language_id 	= $this->url->segment(5);
    $current_order 	= $this->url->segment(6);
    
    $this->projects_model->order_media($direction, $table_id, $language_id, $current_order);
    
    if(!$this->db->error)
    {
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $table_id . '/' . $language_id . '/anchor_media');
    }
  }
  
  function postTwitter()
  {
    $message = $_POST['twitter_post_text'] . ' ' . $_POST['twitter_post_link'];
    $db = checkSocialSettings();

    if(
    $db['setOAuthToken'] != '' &&
    $db['setOAuthTokenSecret'] != '' &&
    $db['setConsumerKey'] != '' &&
    $db['setConsumerSecret'] != ''
    )
    {
      if(str_replace(" ", "", $message) != '')
      {
        //$this->projects_model->updateTwitter($message);
        // $this->alert->clear();
        $this->alert->add($this->lang->line('success'), 'success');
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
      }
    }
    else
    {
      // $this->alert->clear();
      $this->alert->add($this->lang->line('no_social'), 'error');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
    }
  }
  
  function postFacebook()
  {
    $message = $_POST['facebook_post_text'] . ' ' . $_POST['facebook_post_link'];
    $db = checkSocialSettings();
    
    if(
    $db['page_id'] != '' &&
    $db['appId'] != '' &&
    $db['secret'] != '' &&
    $db['url'] != '' &&
    $db['accestoken_db'] != ''
    )
    {
      if(str_replace(" ", "", $message) != '')
      {
        //$this->projects_model->updateFacebook($message);
        // $this->alert->clear();
        $this->alert->add($this->lang->line('success'), 'success');
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
      }
    }
    else
    {
      // $this->alert->clear();
      $this->alert->add($this->lang->line('no_social'), 'error');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
    }
  }
  
  function order()
  {
    $direction 		= $this->url->segment(3);
    $current_order 	= $this->url->segment(4);
    $project_id 		= $this->url->segment(5);
  
    $this->projects_model->order($direction, $current_order, $project_id);
    
    if(!$this->db->error)
    {
      // $this->alert->clear();
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/success');
    }
  }
}
?>