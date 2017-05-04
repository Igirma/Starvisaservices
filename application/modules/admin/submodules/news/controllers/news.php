<?php 
require_once APP_PATH . 'modules/admin/controllers/admin.php';
require_once SYS_PATH . 'core/photo.php';

class news extends admin
{
  var $image_settings = array();

  function __construct()
  {
    parent::__construct();
    $this->lang->load('news');
    
    $this->image_settings = array(
      array(
        'sub_folder' => 'thumb',
        'width' => NEWS_IMG_THUMB_W,
        'height' => NEWS_IMG_THUMB_H,
        'type' => 'crop'
      ),
      /*
      array(
        'sub_folder' => 'detail',
        'width' => NEWS_IMG_DETAIL_W,
        'height' => NEWS_IMG_DETAIL_H,
        'type' => 'crop'
      ),
      */
      array(
        'sub_folder' => 'page',
        'width' => NEWS_IMG_PAGE_W,
        'height' => NEWS_IMG_PAGE_H,
        'type' => 'crop'
      ),
      array(
        'sub_folder' => 'max',
        'width' => NEWS_IMG_MAX_W,
        'height' => NEWS_IMG_MAX_H,
        'type' => 'resize'
      )
    );
  }
  
  function index()
  {	
    $this->form->set_rules('active[]', $this->lang->line('active'), 'numeric');
    
    if($this->form->run())
    {
      $this->news_model->update_overview($_POST);
      
      if(!$this->db->error)
      {
        // $this->alert->clear();
        $this->alert->add($this->lang->line('success'), 'success');
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . ($this->url->segment(2) == 'archive' ? '/' . $this->url->segment(2) : ''));
      }
    }
    
    $archive = 0;
    
    if($this->url->segment(2) == 'archive')
    {
      $archive = 1;
    }
    
    $data['news'] 		= $this->news_model->fetch_all($archive);
    $data['languages'] 	= $this->languages_model->fetch_all();
    
    $this->load->view('news_overview', $data);
  }
  
  function add()
  {
    $this->form->set_rules('news[title]', $this->lang->line('title'), 'required');
    $this->form->set_rules('news[start_date]', $this->lang->line('start_date'), 'required|exact_length[10]');
    $this->form->set_rules('news[end_date]', $this->lang->line('end_date'), 'required|exact_length[10]|end_less_than_start_date');
    (isset($_POST['news']['slug']) && $_POST['news']['slug'] == '' ? $_POST['news']['slug'] = $this->url->string_to_url($_POST['news']['title']) : '');
    
    $this->form->set_rules('news[slug]', $this->lang->line('slug'), 'unique_url_add');
    
    $this->form->set_message('start_date', $this->lang->line('start_date_length'));
    $this->form->set_message('end_less_than_start_date', $this->lang->line('end_date_preceeds_start'));
    
    if($this->form->run())
    {
      $news_id = $this->news_model->add($_POST);

      $_FILES['photo'] = rearrange_files($_FILES['photo']);
      $_FILES['docs'] = rearrange_files($_FILES['docs']);
      
      if(!empty($_FILES['photo']))
      {
        foreach($_FILES['photo'] as $media)
        {
          $photo = new photo(
            $media,
            $this->image_settings,
            MEDIA_DIR,
            CONTROLLER,
            $news_id,
            0,
            $media['name']
          );
        }
      }
      
      if(!empty($_FILES['docs']))
      {
        foreach($_FILES['docs'] as $doc)
        {
          add_doc($doc);
          $this->admin_model->add_doc($doc, $news_id);
        }
      }
        
      if(!$this->db->error)
      {
        $this->alert->add($this->lang->line('success'), 'success');
      
        if(isset($_POST['save_and_back']))
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
        }
        else
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $news_id . '/' . $this->config->item('default_language'));
        }
      }
    }
    $data['news']['filters'] = $this->news_model->getFilters('news', $this->config->item('default_language'));
    $data['drop_down'] = $this->projects_model->fetch_drop_down('blog');
    
    $this->load->view('news_add_edit', $data);
  }
  
  function edit()
  {
    $news_id 		= $this->url->segment(3);
    $language_id 	= $this->url->segment(4);
    
    $this->form->set_rules('news[title]', $this->lang->line('title'), 'required');
    $this->form->set_rules('news[start_date]', $this->lang->line('start_date'), 'required|exact_length[10]');
    $this->form->set_rules('news[end_date]', $this->lang->line('end_date'), 'required|exact_length[10]|end_less_than_start_date');
    
    (isset($_POST['news']['slug']) && $_POST['news']['slug'] == '' ? $_POST['news']['slug'] = $this->url->string_to_url($_POST['news']['title']) : '');
    
    $this->form->set_rules('news[slug]', $this->lang->line('slug'), 'unique_url_edit[' . $news_id . ']');
    
    $this->form->set_message('start_date', $this->lang->line('start_date_length'));
    $this->form->set_message('end_less_than_start_date', $this->lang->line('end_date_preceeds_start'));
    
    if($this->form->run())
    {
      $_FILES['photo'] = rearrange_files($_FILES['photo']);
      $_FILES['docs'] = rearrange_files($_FILES['docs']);
      
      if(!empty($_FILES['photo']))
      {
        foreach($_FILES['photo'] as $media)
        {
          $photo = new photo(
            $media,
            $this->image_settings,
            MEDIA_DIR,
            CONTROLLER,
            $news_id,
            0,
            $media['name']
          );
        }
      }

      if(!empty($_FILES['docs']))
      {
        foreach($_FILES['docs'] as $doc)
        {
          add_doc($doc);
          $this->admin_model->add_doc($doc, $news_id);
        }
      }
      
      $this->news_model->edit($_POST, $news_id, $language_id);
      
      if(!$this->db->error)
      {
        $this->alert->add($this->lang->line('success'), 'success');
        
        if(isset($_POST['save_and_back']))
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
        }
        elseif(isset($_POST['upload']))
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $news_id . '/' . $language_id . '/anchor_media');
        }
        else
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $news_id . '/' . $language_id);
        }
      }
    }
    
    if(isset($_POST['ajax']))
    {
      $filename = $_POST['filename'];
      
      $crop = new crop($filename, NEWS_DIR_CROP_REPLACE);
      $crop->resizeImage(NEWS_CROP_MAX_W, NEWS_CROP_MAX_H, $_POST['x'], $_POST['x2'], 'crop');
      $crop->saveImage($filename, 100);
    }

    $data = $this->news_model->fetch($news_id, $language_id);
    
    $data['news']['language'] = $this->languages_model->fetch($language_id);
    
    $data['languages'] = $this->languages_model->fetch_all();
    $data['news']['filters'] = $this->news_model->getFilters('news', $language_id, $news_id);
    $data['drop_down'] = $this->projects_model->fetch_drop_down('blog');
    $data['news']['category_id'] = $this->members_model->fetch_all_category_selected($news_id, 'news');
    
    $this->load->view('news_add_edit', $data);
  }
  
  function delete()
  {
    $id = $this->url->segment(3);
    
    $this->news_model->delete($id);
    
    if(!$this->db->error)
    {
      // $this->alert->clear();
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . ($this->url->segment(2) == 'archive' ? '/' . $this->url->segment(2) : ''));
    }
  }
  
  function order_media()
  {
    $direction 		= $this->url->segment(3);
    $table_id 		= $this->url->segment(4);
    $language_id 	= $this->url->segment(5);
    $current_order 	= $this->url->segment(6);
    
    $this->news_model->order_media($direction, $table_id, $language_id, $current_order);
    
    if(!$this->db->error)
    {
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $table_id . '/' . $language_id . '/anchor_media');
    }
  }
  
  function delete_media()
  {
    $media_id 		= $this->url->segment(3);
    $table_id 		= $this->url->segment(4);
    $language_id 	= $this->url->segment(5);
    
    $this->news_model->delete_media($media_id);
    
    if(!$this->db->error)
    {
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $table_id . '/' . $language_id . '/anchor_media');
    }
  }
  
  function postTwitter()
  {
    $message = $_POST['twitter_post_text'] . ' ' . $_POST['twitter_post_link'];
    $db = getSettings();

    if(
      $db['setOAuthToken'] != '' &&
      $db['setOAuthTokenSecret'] != '' &&
      $db['setConsumerKey'] != '' &&
      $db['setConsumerSecret'] != ''
    )
    {
      if(str_replace(" ", "", $message) != '')
      {
        $this->news_model->updateTwitter($message);
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
    $db = getSettings();
    
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
        $this->news_model->updateFacebook($message);
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
  
  function postLinkedin()
  {		
    $table_id 		= $this->url->segment(3);
    $language_id 	= $this->url->segment(4);
    $client_id 		= $this->url->segment(5);
    
    $message = $_POST['linkedin_post_text'] . ' ' . $_POST['linkedin_post_link'];
    $db = getSettings();
    
    $fblinkedtwit   =   new FbLinkedTwit();
    
    $fblinkedtwit->setLinkedin_access($db['linkedin_access']);
    $fblinkedtwit->setLinkedin_secret($db['linkedin_secret']);
    
    if (isset($db['requestToken']) && isset($db['oauth_verifier']) && isset($db['oauth_access_token'])){
      //$this->news_model->updateLinkedin($table_id, $language_id);
      $res = $fblinkedtwit->linkedinStatusUpdate($message, $db['requestToken'], $db['oauth_verifier'], $db['oauth_access_token']);
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $table_id . '/' . $language_id . '/' . $client_id);
    }
    else
    {
      // $this->alert->clear();
      $this->alert->add($this->lang->line('no_social'), 'error');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $table_id . '/' . $language_id . '/' . $client_id);
    }
  }
  
}

?>