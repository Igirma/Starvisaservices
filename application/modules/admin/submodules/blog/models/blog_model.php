<?php 
class blog_model extends model
{	
  function fetch($blog_id, $language_id)
  {	
    $sql = '
    SELECT *
    FROM `blog`
    INNER JOIN `blog_content`
    ON `blog_content`.blog_id = `blog`.blog_id
    WHERE `blog`.blog_id = ?
    AND `blog_content`.language_id = ?
    ';
    
    $r = $this->db->query($sql, array($blog_id, $language_id));
    $data['blog'] = $r[0];

    $sql = '
      SELECT *
      FROM `media`
      INNER JOIN `media_content`
      ON `media_content`.media_id = `media`.media_id
      WHERE `media`.table_id = ?
      AND `media`.controller = "blog"
      AND `media_content`.language_id = ?
      ORDER BY `media`.order ASC
    ';
  
    $data['media'] = $this->db->query($sql, array($data['blog']['blog_id'], $language_id));
    
    $sql = '
      SELECT *
      FROM `media`
      INNER JOIN `media_type`
      ON `media_type`.media_type_id = `media`.media_type_id
      WHERE `media`.table_id = ?
      AND `media_type`.name = "doc"
      AND `media`.controller = ?
      ORDER BY `media`.filename ASC
    ';
    
    $data['docs'] = $this->db->query($sql, array($data['blog']['blog_id'], CONTROLLER));
  
    return $data;
  }
  
  function fetch_dash()
  {
    $sql = '
      SELECT * 
      FROM `blog`, `blog_content` 
      WHERE `blog`.blog_id = `blog_content`.blog_id 
      AND `blog_content`.language_id = :lang 
      ORDER BY `blog`.date_created DESC, `blog_content`.last_update DESC
      LIMIT 5
    ';
    $data = $this->db->query($sql, array('lang' => $this->config->item('default_language')));
    
    return $data;
  }
  
  function fetch_all($archive)
  {
    $sql = '
    SELECT
      `blog`.blog_id,
      `blog`.date_created,
      `blog`.start_date,
      `blog`.end_date,
      `blog`.archive,
      `blog`.active,
      `blog_content`.language_id,
      `blog_content`.title
    FROM `blog`
    INNER JOIN `blog_content`
      ON `blog_content`.blog_id = `blog`.blog_id
    WHERE `blog`.archive = ' . $archive . '
      AND `blog_content`.language_id = ?
    ORDER BY `blog`.date_created DESC
    ';
    
    return $this->db->query($sql, array($this->config->item('default_language')));
  }
  
  function fetch_all_comments($language_id, $blog_id)
  {	
    $sql = '
      SELECT *
      FROM `comment`, `comment_content`
      WHERE `comment`.comment_id = `comment_content`.comment_id
      AND `comment`.language_id = ?
      AND `comment`.blog_id = ?
      ORDER BY `comment`.comment_date DESC
    ';
    
    $data = $this->db->query($sql, array($language_id, $blog_id));
    if(isset($data) && count($data) > 0){
      foreach($data as $k => $item){
        $data[$k]['subitems'] = $this->fetch_all_reacts($language_id, $item['comment_id']);
        if(isset($data[$k]['subitems']) && count($data[$k]['subitems']) > 0){
          foreach($data[$k]['subitems'] as $l => $subitem){
            $data[$k]['subitems'][$l]['subitems'] = $this->fetch_all_reacts($language_id, $subitem['comment_id']);
          }
        }
      }
    }
    return $data;
  }
  
  function fetch_all_reacts($language_id, $comment_id)
  {	
    $sql = '
      SELECT *
      FROM `comment`, `comment_content`
      WHERE `comment`.comment_id = `comment_content`.comment_id
      AND `comment`.language_id = ?
      AND `comment`.react_id = ?
      ORDER BY `comment`.comment_date DESC
    ';
    
    $data = $this->db->query($sql, array($language_id, $comment_id));
    
    return $data;
  }
  
  function add($post)
  {	
    $sql = '
    INSERT INTO `blog`
    (
      edit_by,
      date_created,
      start_date,
      end_date
    )
    VALUES
    (
      :edit_by,
      :date_created,
      :start_date,
      :end_date
    )';
      
    
    $this->db->query($sql, array(
        'edit_by' 		=> $_SESSION['username'],
        'date_created' 	=> time(),
        'start_date' 	=> strtotime($post['blog']['start_date']),
        'end_date' 		=> strtotime($post['blog']['end_date'])
    ));
    
    $id = $this->db->last_insert_id;
    
    $sql = '
    INSERT INTO `blog_content`
    (
      blog_id,
      language_id,
      slug,
      title,
      description,
      content,
      tags,
      meta_title,
      meta_desc,
      meta_keyw,
      sub_active
    )
    VALUES
    (
      :blog_id,
      :language_id,
      :slug,
      :title,
      :description,
      :content,
      :tags,
      :meta_title,
      :meta_desc,
      :meta_keyw,
      :sub_active
    )';
      
    $this->db->query($sql, array(
        'blog_id' 		=> $id,
        'language_id' 	=> $this->config->item('default_language'),
        'slug' 			=> $this->url->string_to_url($post['blog']['slug']),
        'title' 		=> $post['blog']['title'],
        'description' 	=> $post['blog']['description'],
        'content' 		=> $post['blog']['content'],
        'tags' 				=> $post['blog']['tags'],
        'meta_title' 	=> $post['blog']['meta_title'],
        'meta_desc' 	=> $post['blog']['meta_desc'],
        'meta_keyw' 	=> $post['blog']['meta_keyw'],
        'sub_active' 	=> $post['blog']['sub_active']
    ));
    
    if(haveFilters('blog')) 
      $this->addFilters($id, $post['blog']['filters'], $this->config->item('default_language'), 'blog');
    
    $languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array($this->config->item('default_language')));
    
    foreach($languages as $language)
    {				
      $sql = '
      INSERT INTO `blog_content`
      (
        blog_id,
        language_id,
        sub_active
      )
      VALUES
      (
        :blog_id,
        :language_id,
        :sub_active
      )';
      
      $this->db->query($sql, array(
        'blog_id' 		=> $id,
        'language_id' 	=> $language['language_id'],
        'sub_active' 	=> 0
      ));
    }
    
    return $id;
  }
  
  function delete_comment($comment_id){
    $this->db->query('DELETE FROM `comment` WHERE `comment`.comment_id = ? LIMIT 1', array($comment_id));
    $this->db->query('DELETE FROM `comment_content` WHERE `comment_content`.comment_id = ? LIMIT 1', array($comment_id));
    $to_delete = $this->db->query('SELECT * FROM `comment` WHERE `comment`.react_id = ? LIMIT 1', array($comment_id));
    
    if(isset($to_delete) && count($to_delete) > 0){
      foreach($to_delete as $item){
        $this->db->query('DELETE FROM `comment` WHERE `comment`.comment_id = ? ', array($item['comment_id']));
        $this->db->query('DELETE FROM `comment_content` WHERE `comment_content`.comment_id = ? ', array($item['comment_id']));
        $to_delete_level2 = $this->db->query('SELECT * FROM `comment` WHERE `comment`.react_id = ? ', array($item['comment_id']));
        if(isset($to_delete_level2) && count($to_delete_level2) > 0){
          foreach($to_delete_level2 as $item){
            $this->db->query('DELETE FROM `comment` WHERE `comment`.comment_id = ? ', array($item['comment_id']));
            $this->db->query('DELETE FROM `comment_content` WHERE `comment_content`.comment_id = ? ', array($item['comment_id']));
            
          }
        }
      }
    }
  }
  
  function edit($post, $id, $language_id)
  {
    $sql = 'SELECT `blog_content`.slug FROM `blog_content` WHERE `blog_content`.blog_id = ? AND `blog_content`.language_id = ?';
    $content = $this->db->query($sql, array($id, $language_id));
    
    $old_slug = $content[0]['slug'];
    
    if($old_slug != $this->url->string_to_url($post['blog']['slug']))
    {
      $sql = '
      UPDATE `blog_content`
      SET `blog_content`.slug_301 = :slug_301
      WHERE `blog_content`.blog_id = :blog_id
      AND `blog_content`.language_id = :language_id
      ';
      
      $this->db->query($sql, array(
        'blog_id' => $id,
        'language_id' => $language_id,
        'slug_301' => $old_slug
      ));
    }
    
    $sql = '
    UPDATE `blog`, `blog_content`
    SET 
      `blog`.start_date 			= :start_date,
      `blog`.end_date 			= :end_date,
      `blog`.edit_by				= :edit_by,
      `blog_content`.last_update 	= :last_update,
      `blog_content`.title 		= :title,
      `blog_content`.description 	= :description,
      `blog_content`.content 		= :content,
      `blog_content`.tags 		= :tags,
      `blog_content`.meta_title 	= :meta_title,
      `blog_content`.meta_desc 	= :meta_desc,
      `blog_content`.meta_keyw 	= :meta_keyw,
      `blog_content`.sub_active 	= :sub_active,
      `blog_content`.slug 		= :slug
    WHERE `blog`.blog_id 			= :blog_id
    AND `blog_content`.blog_id 		= :blog_id
    AND `blog_content`.language_id 	= :language_id
    ';
    
    $this->db->query($sql, array(
      'start_date' 	=> strtotime($post['blog']['start_date']),
      'end_date' 		=> strtotime($post['blog']['end_date']),
      'edit_by'		=> $_SESSION['username'],
      'last_update' 	=> time(),
      'title' 		=> ucfirst($post['blog']['title']),
      'description' 	=> $post['blog']['description'],
      'content' 		=> $post['blog']['content'],
      'tags' 		=> $post['blog']['tags'],
      'meta_title' 	=> $post['blog']['meta_title'],
      'meta_desc' 	=> $post['blog']['meta_desc'],
      'meta_keyw' 	=> $post['blog']['meta_keyw'],
      'sub_active' 	=> $post['blog']['sub_active'],
      'slug' 			=> $this->url->string_to_url($post['blog']['slug']),
      'blog_id' 		=> $id,
      'language_id' 	=> $language_id
    ));
    
    if(haveFilters('blog')) 
      $this->addFilters($id, $post['blog']['filters'], $language_id, 'blog');
    
    foreach($post['active'] as $k => $v)
    {
      $this->db->query('UPDATE `comment` SET `comment`.active = ? WHERE `comment`.comment_id = ? LIMIT 1', array($v, $k));
    }
    
    if(isset($post['media']))
    {
      foreach($post['media'] as $media)
      {
        $sql = '
          UPDATE `media`, `media_content`
          SET
            `media`.album_thumb 		= :album_thumb
          WHERE `media`.media_id = :media_id
          AND `media_content`.media_id = :media_id
          AND `media_content`.language_id = :language_id
        ';
        
        $this->db->query($sql, array(
          'album_thumb' 	=> (isset($_POST['set_thumbnail']) && $_POST['set_thumbnail'] == $media['media_id'] ? 1 : 0),
          'media_id'		=> $media['media_id'],
          'language_id'	=> $language_id
        ));
      }
    }
  }
  
  function delete($id)
  {
    $this->db->query('DELETE FROM `blog` WHERE `blog`.blog_id = ?', array($id));
    $this->db->query('DELETE FROM `blog_content` WHERE `blog_content`.blog_id = ?', array($id));
    if(haveFilters('blog')){
        $sql = '
        SELECT * 
        FROM `filter`
        WHERE `filter`.controller = :controller
        ';	
        $data = $this->db->query($sql, array('controller' => 'blog'));
        if(!empty($data))
        {
          foreach($data as $filter)
          {
            $sql = '
              SELECT *
                FROM `filter`, `filter_item`, `filter_heading`
                WHERE `filter_heading`.filter_heading_id = `filter_item`.filter_heading_id
                AND `filter_heading`.filter_id = `filter`.filter_id
                AND `filter`.filter_id = :filter_id	
              ';
              $subelements = $this->db->query($sql, array('filter_id' => $filter['filter_id']));
              if(isset($subelements) && $subelements && count($subelements) > 0)
                foreach($subelements as $j => $sub_element)
                  $this->db->query('DELETE FROM `filter_item_saved` WHERE `filter_item_saved`.table_id = ? AND `filter_item_saved`.filter_item_id = ?', array($id, $sub_element['filter_item_id']));
          }
        }
      }
    
    $sql = '
    SELECT *
    FROM `media`
    WHERE `media`.table_id = ?
    AND `media`.controller = ?
    ';
    
    $media_ar = $this->db->query($sql, array($id, CONTROLLER));
    
    foreach($media_ar as $media)
    {
      $this->delete_media($media['media_id']);
    }
  }
  
  function update_overview($post)
  {
    foreach($post['active'] as $k => $v)
    {
      $this->db->query('UPDATE `blog` SET `blog`.active = ? WHERE `blog`.blog_id = ? LIMIT 1', array($v, $k));
    }
  }
  
  function order_media($direction, $table_id, $language_id, $current_order)
  {
    switch($direction)
    {
      case 'left':
        $from = $this->db->query('SELECT `media`.order, `media`.media_id FROM `media` WHERE `media`.table_id = ? AND `media`.order = ?', array($table_id, $current_order));
        $to = $this->db->query('SELECT `media`.order, `media`.media_id FROM `media` WHERE `media`.order < ? AND `media`.table_id = ? ORDER BY `media`.order DESC', array($current_order, $table_id));
        
        if(!empty($to))
        {
          $this->db->query('UPDATE `media` SET `media`.order = ? WHERE `media`.media_id = ?', array($to[0]['order'], $from[0]['media_id']));
          $this->db->query('UPDATE `media` SET `media`.order = ? WHERE `media`.media_id = ?', array($from[0]['order'], $to[0]['media_id']));
        }
      break;
      
      case 'right':
        $from = $this->db->query('SELECT `media`.order, `media`.media_id FROM `media` WHERE `media`.table_id = ? AND `media`.order = ?', array($table_id, $current_order));
        $to = $this->db->query('SELECT `media`.order, `media`.media_id FROM `media` WHERE `media`.order > ? AND `media`.table_id = ? ORDER BY `media`.order ASC', array($current_order, $table_id));
        
        if(!empty($to))
        {
          $this->db->query('UPDATE `media` SET `media`.order = ? WHERE `media`.media_id = ?', array($to[0]['order'], $from[0]['media_id']));
          $this->db->query('UPDATE `media` SET `media`.order = ? WHERE `media`.media_id = ?', array($from[0]['order'], $to[0]['media_id']));
        }
      break;
    }
  }
  
  function delete_media($media_id)
  {
    $sql = 'SELECT `media`.filename FROM `media` WHERE `media`.media_id = ?';
    $data = $this->db->query($sql, array($media_id));
    
    $filename = $data[0]['filename'];

    $dirs = glob(BASE_PATH . MEDIA_DIR . CONTROLLER . '/*', GLOB_ONLYDIR);
    
        foreach($dirs as $dir)
        {
            if(is_dir($dir))
      {
                if(file_exists($dir.'/'.$filename))
        {
          unlink($dir.'/'.$filename);
                }
            }
        }
    
    $this->db->query('DELETE FROM `media` WHERE `media`.media_id = ? LIMIT 1', array($media_id));
    $this->db->query('DELETE FROM `media_content` WHERE `media_content`.media_id = ? LIMIT 1', array($media_id));
  }
  
  function updateTwitter($message)
  {
    $this->twitter->statusesUpdate($message);
  }

  function updateFacebook($message)
  {
    //accestoken uit db halen
    $accestoken2 = $this->facebook->accestoken_db;
    
    $attachment = array(
      'access_token' => $accestoken2,
      'message'=> $message
    );
    
    $this->facebook->api('/me/feed','POST', $attachment);
  }
  
  function getSocialContent($id, $language_id)
  {
    $sql = 'SELECT * FROM `blog_content` WHERE `blog_content`.blog_id = ? AND `blog_content`.language_id = ?';
    $data = $this->db->query($sql, array($id, $language_id));
    
    $this->googleurl->longUrl = SITE_URL . $data[0]['slug'];
    $url_short = $this->googleurl->makeRequest();
    
    return $data[0]['description'] . ' ' . $url_short;
  }
  
  
  function fetch_category_filters($language_id, $controller, $category_id = 0, $prod_id = 0)
  {
    
    $sql = '
    SELECT * 
    FROM `filter`, `filter_heading`
    WHERE `filter`.filter_id = `filter_heading`.filter_id
    AND `filter_heading`.language_id = :lang
    AND `filter`.controller = :controller
    ';
    
    $data = $this->db->query($sql, array('lang' => $this->config->item('default_language'),'controller' => $controller));
    
    $i = 0;
        
    if(!empty($data))
    {
      foreach($data as $filter)
      {
        $return[$i] = $filter;
        $return[$i]['selected'] = array();
        
        $sql = '
        SELECT *, `filter_item`.filter_item_id as filter_item_id_number, `filter_item`.title as filter_item_title		
          FROM `filter`, `filter_item`, `filter_heading`, `filter_item_category`
          WHERE `filter_heading`.filter_id = `filter`.filter_id
          
          AND `filter_item_category`.filter_item_id = `filter`.filter_id				
          AND `filter_item`.filter_heading_id = `filter_heading`.filter_heading_id
          AND `filter_item_category`.category_id = :category_id
          AND `filter_item_category`.saved = 1
          AND `filter_heading`.language_id = :language_id
          AND `filter`.filter_id = :filter_id
          
        ';
        
        $subelements2 = $this->db->query($sql, array('category_id' => $category_id, 'language_id' => $language_id, 'filter_id' => $filter['filter_id']));
        //debug($subelements2);
        
        if(isset($subelements2) && $subelements2 && count($subelements2) > 0){
          foreach($subelements2 as $j => $sub_element){
            $return[$i]['subelements'][$sub_element['filter_item_identify']] = $sub_element;

            $sql = '
            SELECT *
            FROM `filter_item_saved`
            WHERE `filter_item_saved`.filter_item_id = :filter_item_id
            AND `filter_item_saved`.table_id = :table_id
            AND `filter_item_saved`.saved = 1
            ';
            
            $selected = $this->db->query($sql, array('filter_item_id' => $sub_element['filter_item_id_number'], 'table_id' => $prod_id));
            if($selected && count($selected) >= 1){
              foreach($selected as $option){
                $return[$i]['selected'][] = $option['filter_item_id'];
              }
            }
            //else $return[$i]['selected'] = array();
          }	
        }
        else{
          unset($return[$i]);
        }
        $i++;
      }
    }
    if(isset($return) && $return && count($return) > 0) return $return;
    else return false;
  }
  
  function fetch_filters($controller, $language_id, $category_id = 0)
  {
    
    $sql = '
    SELECT * 
    FROM `filter`, `filter_heading`
    WHERE `filter`.filter_id = `filter_heading`.filter_id
    AND `filter_heading`.language_id = :lang
    AND `filter`.controller = :controller
    ';
    
    $data = $this->db->query($sql, array('lang' => $this->config->item('default_language'),'controller' => $controller));
    
    $i = 0;
        
    if(!empty($data))
    {
      foreach($data as $filter)
      {
        $return[$i] = $filter;
        $return[$i]['selected'] = array();
        
        $sql = '
        SELECT *, `filter_item`.filter_item_id as filter_item_id_number, `filter_item`.title as filter_item_title		
          FROM `filter`, `filter_item`, `filter_heading`
          WHERE `filter_heading`.filter_id = `filter`.filter_id
          
          AND `filter_item`.filter_heading_id = `filter_heading`.filter_heading_id
          AND `filter_heading`.language_id = :language_id
          AND `filter`.filter_id = :filter_id
          
        ';
        
        $subelements2 = $this->db->query($sql, array('language_id' => $language_id, 'filter_id' => $filter['filter_id']));
        //debug($subelements2);
        
        if(isset($subelements2))
          foreach($subelements2 as $j => $sub_element){
            $return[$i]['subelements'][$sub_element['filter_item_identify']] = $sub_element;

            $sql = '
            SELECT *
            FROM `filter_item_saved`
            WHERE `filter_item_saved`.filter_item_id = :filter_item_id
            AND `filter_item_saved`.table_id = :table_id
            AND `filter_item_saved`.saved = 1
            ';
            
            $selected = $this->db->query($sql, array('filter_item_id' => $sub_element['filter_item_id_number'], 'table_id' => $category_id));
            if($selected && count($selected) >= 1){
              foreach($selected as $option){
                $return[$i]['selected'][] = $option['filter_item_id'];
              }
            }
            //else $return[$i]['selected'] = array();
          }
        $i++;
      }
    }
    if(isset($return) && $return && count($return) > 0) return $return;
    else return false;
  }
  
  function getFilters($controller, $language_id, $id = 0, $category_id = 0){
    if(haveFilters($controller)){
      if(!haveCategories($controller)) return $this->fetch_filters($controller, $language_id, $id);
      else return $this->fetch_category_filters($language_id, $controller, $category_id, $id);
    }
    else return false;	
  }
  
  function addFilters($id, $posted_filters, $language_id, $controller){
    
    $sql = '
          SELECT * 
          FROM `filter`
          WHERE `filter`.controller = :controller
          ';	
      $data = $this->db->query($sql, array('controller' => $controller));
      if(!empty($data))
      {
        foreach($data as $filter)
        {
          $sql = '
                SELECT *
                  FROM `filter`, `filter_item`, `filter_heading`
                  WHERE `filter_heading`.filter_heading_id = `filter_item`.filter_heading_id
                  AND `filter_heading`.filter_id = `filter`.filter_id
                  AND `filter`.filter_id = :filter_id	
          ';
          $subelements = $this->db->query($sql, array('filter_id' => $filter['filter_id']));
          if(isset($subelements) && $subelements && count($subelements) > 0)
            foreach($subelements as $j => $sub_element)
              $this->db->query('DELETE FROM `filter_item_saved` WHERE `filter_item_saved`.table_id=? AND `filter_item_saved`.filter_item_id = ?', array($id, $sub_element['filter_item_id']));		
        }
      }	
    
    if(isset($posted_filters) && $posted_filters && count($posted_filters) > 0)
        foreach($posted_filters as $k => $filters)
        {
          if(is_array($filters)){
            foreach($filters as $l => $filters_subelements){
              $sql = 'INSERT INTO `filter_item_saved`
              (
                `filter_item_saved`.saved,
                `filter_item_saved`.table_id,
                `filter_item_saved`.filter_item_id
              )
              VALUES
              (
                :saved,
                :table_id,
                :filter_item_id
              )
              ';
              $this->db->query($sql, array(
                'saved' 					=> 1,
                'table_id' 					=> $id,
                'filter_item_id' 			=> $filters_subelements
              ));
              $arr_filters[] = $filters_subelements;
              
              $sql2 = '
              SELECT `filter_item`.* 
              FROM `filter`, `filter_item`
              WHERE `filter_item`.filter_item_id = "'.$filters_subelements.'"
              AND `filter`.controller = "'.$controller.'"';
              
              $elem = $this->db->query($sql2);
              
              if(isset($elem) && count($elem) > 0){
                $sql2 = '
                    SELECT `filter_item`.* 
                    FROM `filter`, `filter_item`, `filter_heading` 
                    WHERE `filter_item`.filter_heading_id = `filter_heading`.filter_heading_id 
                    AND `filter_item`.filter_item_identify = "'.$elem[0]['filter_item_identify'].'"
                    AND `filter_item`.filter_item_id <> "'.$filters_subelements.'" 
                    AND `filter`.filter_id = `filter_heading`.filter_id 
                    AND `filter`.controller = "'.$controller.'"';
                    
                $return = $this->db->query($sql2);
                    
                foreach($return as $item){
                  $sql = 'INSERT INTO `filter_item_saved`
                    (
                      `filter_item_saved`.saved,
                      `filter_item_saved`.table_id,
                      `filter_item_saved`.filter_item_id
                    )
                    VALUES
                    (
                      :saved,
                      :table_id,
                      :filter_item_id
                    )
                    ';
                    $this->db->query($sql, array(
                      'saved' 					=> 1,
                      'table_id' 					=> $id,
                      'filter_item_id' 			=> $item['filter_item_id']
                    ));
                  $arr_filters[] = $item['filter_item_id'];
                }
              }		
            }
          }else{
            $sql = 'INSERT INTO `filter_item_saved`
            (
              `filter_item_saved`.saved,
              `filter_item_saved`.table_id,
              `filter_item_saved`.filter_item_id
            )
            VALUES
            (
              :saved,
              :table_id,
              :filter_item_id
            )
            ';
            $this->db->query($sql, array(
              'saved' 					=> 1,
              'table_id' 					=> $id,
              'filter_item_id' 			=> $filters
            ));
            $arr_filters[] = $filters;
            
            $sql2 = '
              SELECT `filter_item`.* 
              FROM `filter`, `filter_item`
              WHERE `filter_item`.filter_item_id = "'.$filters.'"
              AND `filter`.controller = "'.$controller.'"';
              
              $elem = $this->db->query($sql2);
              
              if(isset($elem) && count($elem) > 0){
                $sql2 = '
                    SELECT `filter_item`.* 
                    FROM `filter`, `filter_item`, `filter_heading` 
                    WHERE `filter_item`.filter_heading_id = `filter_heading`.filter_heading_id 
                    AND `filter_item`.filter_item_identify = "'.$elem[0]['filter_item_identify'].'"
                    AND `filter_item`.filter_item_id <> "'.$filters.'" 
                    AND `filter`.filter_id = `filter_heading`.filter_id 
                    AND `filter`.controller = "'.$controller.'"';
                    
                $return = $this->db->query($sql2);
                    
                foreach($return as $item){
                  $sql = 'INSERT INTO `filter_item_saved`
                    (
                      `filter_item_saved`.saved,
                      `filter_item_saved`.table_id,
                      `filter_item_saved`.filter_item_id
                    )
                    VALUES
                    (
                      :saved,
                      :table_id,
                      :filter_item_id
                    )
                    ';
                    $this->db->query($sql, array(
                      'saved' 					=> 1,
                      'table_id' 					=> $id,
                      'filter_item_id' 			=> $item['filter_item_id']
                    ));
                  $arr_filters[] = $item['filter_item_id'];
                }
              }		
          }
        }
      
      $sql2 = '
      SELECT `filter_item`.* 
      FROM `filter`, `filter_item`, `filter_heading` 
      WHERE `filter_item`.filter_heading_id = `filter_heading`.filter_heading_id 
      AND `filter`.filter_id = `filter_heading`.filter_id 
      AND `filter_heading`.language_id = '.$language_id.'
      AND `filter`.controller = "'.$controller.'"';
      
      $return = $this->db->query($sql2);
      
      if(isset($return))
      foreach($return as $item){
        if(!in_array($item['filter_item_id'], $arr_filters)){
          
          $sql = 'INSERT INTO `filter_item_saved`
          (
            `filter_item_saved`.saved,
            `filter_item_saved`.table_id,
            `filter_item_saved`.filter_item_id
          )
          VALUES
          (
            :saved,
            :table_id,
            :filter_item_id
          )
          ';
          $this->db->query($sql, array(
            'saved' 					=> 0,
            'table_id' 					=> $id,
            'filter_item_id' 			=> $item['filter_item_id']
          ));
        }
      }
    
  }

}
?>