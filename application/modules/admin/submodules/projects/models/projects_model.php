<?php

class projects_model extends model
{
  function fetch_all()
  {
    $sql = '
    SELECT * FROM `project`
    INNER JOIN `project_content`
    ON `project`.project_id = `project_content`.project_id
    WHERE `project_content`.language_id = ?
    ORDER BY `project`.order ASC
    ';
    
    $data['projects'] = $this->db->query($sql, array($this->config->item('default_language')));
    return $data;
  }

  function partners()
  {
    $sql = '
      SELECT * FROM `brands`
        INNER JOIN `brands_content` ON `brands`.brand_id = `brands_content`.brand_id
      WHERE `brands_content`.language_id = ?
        AND `brands_content`.sub_active = ? 
        AND `brands`.active = ? 
      ORDER BY `brands`.order ASC
    ';
    return $this->db->query($sql, array($this->config->item('default_language'), 1, 1));
  }

  function fetch($project_id, $language_id)
  {
    $sql = '
    SELECT *
    FROM `project`
    INNER JOIN `project_content`
    ON `project_content`.project_id = `project`.project_id
    WHERE `project`.project_id = ?
    AND `project_content`.language_id = ?
    ';
    
    $r = $this->db->query($sql, array($project_id, $language_id));
    $data['project'] = $r[0];

    $sql = '
      SELECT * FROM `media`
      INNER JOIN `media_content`
      ON `media_content`.media_id = `media`.media_id
      INNER JOIN `media_type`
      ON `media_type`.media_type_id = `media`.media_type_id
      WHERE `media`.table_id = ?
      AND `media_type`.name = ? 
      AND `media`.controller = ? 
      AND `media_content`.language_id = ? 
      AND `media`.album_id = ? 
      ORDER BY `media`.order ASC
    ';
    $data['media']['logo'] = $this->db->query($sql, array($data['project']['project_id'], 'img', CONTROLLER, $language_id, 2));
    
    $sql = '
      SELECT * FROM `media`
      INNER JOIN `media_content`
      ON `media_content`.media_id = `media`.media_id
      INNER JOIN `media_type`
      ON `media_type`.media_type_id = `media`.media_type_id
      WHERE `media`.table_id = ?
      AND `media_type`.name = ? 
      AND `media`.controller = ? 
      AND `media_content`.language_id = ? 
      AND `media`.album_id = ? 
      ORDER BY `media`.order ASC
    ';
    $data['media']['cover'] = $this->db->query($sql, array($data['project']['project_id'], 'img', CONTROLLER, $language_id, 1));
    
    $sql = '
      SELECT * FROM `media`
      INNER JOIN `media_content`
      ON `media_content`.media_id = `media`.media_id
      INNER JOIN `media_type`
      ON `media_type`.media_type_id = `media`.media_type_id
      WHERE `media`.table_id = ?
      AND `media_type`.name = ? 
      AND `media`.controller = ? 
      AND `media_content`.language_id = ? 
      AND `media`.album_id = ? 
      ORDER BY `media`.order ASC
    ';
    $data['media']['photos'] = $this->db->query($sql, array($data['project']['project_id'], 'img', CONTROLLER, $language_id, 0));
    
    $sql = '
      SELECT * FROM `media`
      INNER JOIN `media_content`
      ON `media_content`.media_id = `media`.media_id
      INNER JOIN `media_type`
      ON `media_type`.media_type_id = `media`.media_type_id
      WHERE `media`.table_id = ?
      AND `media_type`.name = ? 
      AND `media`.controller = ? 
      AND `media_content`.language_id = ? 
      AND `media`.album_id = ? 
      ORDER BY `media`.order ASC
    ';
    $data['media']['photos_2'] = $this->db->query($sql, array($data['project']['project_id'], 'img', CONTROLLER, $language_id, 3));
    
    $sql = '
      SELECT * FROM `media`
      INNER JOIN `media_content`
      ON `media_content`.media_id = `media`.media_id
      INNER JOIN `media_type`
      ON `media_type`.media_type_id = `media`.media_type_id
      WHERE `media`.table_id = ?
      AND `media_type`.name = ? 
      AND `media`.controller = ? 
      AND `media_content`.language_id = ? 
      AND `media`.album_id = ? 
      ORDER BY `media`.order ASC
    ';
    $data['media']['photos_3'] = $this->db->query($sql, array($data['project']['project_id'], 'img', CONTROLLER, $language_id, 4));

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
    
    $data['docs'] = $this->db->query($sql, array($data['project']['project_id'], CONTROLLER));
  
    return $data;
  }
  
  function add($post)
  {
    $order = $this->db->query('SELECT MAX(`order`) + 1 AS `order` FROM `project`');

    if($order[0]['order'] == '')
    {
      $order = 0;
    }
    else
    {
      $order = $order[0]['order'];
    }

    $sql = '
    INSERT INTO `project`
    (
      project_date,
      category_id,
      highlight,
      `order`
    )
    VALUES
    (
      :project_date,
      :category_id,
      :highlight,
      :order
    )';
      
    //'project_date' => strtotime($post['project']['project_date']),
    
    $this->db->query($sql, array(
        'project_date' => date('d-m-Y'),
        'category_id' => 0,
        'highlight' => $post['project']['highlight'],
        'order' => $order
    ));
    
    $id = $this->db->last_insert_id;

    $sql = '
      INSERT INTO `project_content`
      (
        project_id,
        language_id,
        slug,
        title,
        description,
        content,
        content_2,
        content_3,
        website,
        summary,
        meta_title,
        meta_desc,
        meta_keyw,
        sub_active
      )
      VALUES
      (
        :project_id,
        :language_id,
        :slug,
        :title,
        :description,
        :content,
        :content_2,
        :content_3,
        :website,
        :summary,
        :meta_title,
        :meta_desc,
        :meta_keyw,
        :sub_active
      )
    ';
    $this->db->query($sql, array(
        'project_id' 	=> $id,
        'language_id' 	=> $this->config->item('default_language'),
        'slug' 			=> $this->url->string_to_url($post['project']['slug']),
        'title' 		=> $post['project']['title'],
        'description' 	=> $post['project']['description'],
        'content' 		=> $post['project']['content'],
        'content_2' 		=> $post['project']['content_2'],
        'content_3' 		=> $post['project']['content_3'],
        'website' 		=> $post['project']['website'],
        'summary' 		=> $post['project']['summary'],
        'meta_title' 	=> $post['project']['meta_title'],
        'meta_desc' 	=> $post['project']['meta_desc'],
        'meta_keyw' 	=> $post['project']['meta_keyw'],
        'sub_active' 	=> $post['project']['sub_active']
    ));

    $languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array($this->config->item('default_language')));
    
    foreach($languages as $language)
    {				
      $sql = '
      INSERT INTO `project_content`
      (
        project_id,
        language_id,
        sub_active
      )
      VALUES
      (
        :project_id,
        :language_id,
        :sub_active
      )';
      
      $this->db->query($sql, array(
        'project_id' 	=> $id,
        'language_id' 	=> $language['language_id'],
        'sub_active' 	=> 0
      ));
    }
    
    if(haveFilters('projects')) 
      $this->addFilters($id, $post['project']['filters'], $this->config->item('default_language'), 'projects');
    
    $this->members_model->attach_caregories($post['project']['category_id'], $id, CONTROLLER);		
    
    return $id;
  }
  
  function edit($post, $id, $language_id)
  {
    $sql = 'SELECT `project_content`.slug FROM `project_content` WHERE `project_content`.project_id = ? AND `project_content`.language_id = ?';
    $content = $this->db->query($sql, array($id, $language_id));
    
    $old_slug = $content[0]['slug'];
    
    if($old_slug != $this->url->string_to_url($post['project']['slug']))
    {
      $sql = '
      UPDATE `project_content`
      SET `project_content`.slug_301 = :slug_301
      WHERE `project_content`.project_id = :project_id
      AND `project_content`.language_id = :language_id
      ';
      
      $this->db->query($sql, array(
        'project_id' => $id,
        'language_id' => $language_id,
        'slug_301' => $old_slug
      ));
    }
    
    $sql = '
    UPDATE `project`, `project_content`
    SET
      `project`.project_date 			= :project_date,
      `project`.highlight 			= :highlight,
      `project`.category_id 			= :category_id,
      `project_content`.last_update 	= :last_update,
      `project_content`.title 		= :title,
      `project_content`.description 	= :description,
      `project_content`.content 		= :content,
      `project_content`.content_2 		= :content_2,
      `project_content`.content_3 		= :content_3,
      `project_content`.website 		= :website,
      `project_content`.summary 		= :summary,
      `project_content`.meta_title 	= :meta_title,
      `project_content`.meta_desc 	= :meta_desc,
      `project_content`.meta_keyw 	= :meta_keyw,
      `project_content`.sub_active 	= :sub_active,
      `project_content`.slug 			= :slug
    WHERE `project`.project_id 			= :project_id
    AND `project_content`.project_id 	= :project_id
    AND `project_content`.language_id 	= :language_id
    ';
    
    $this->db->query($sql, array(
      'project_date' 	=> strtotime($post['project']['project_date']),
      'category_id' 	=> $post['project']['category_id'],
      'last_update' 	=> time(),
      'title' 		=> ucfirst($post['project']['title']),
      'description' 	=> $post['project']['description'],
      'content' 		=> $post['project']['content'],
      'content_2' 		=> $post['project']['content_2'],
      'content_3' 		=> $post['project']['content_3'],
      'website' 		=> $post['project']['website'],
      'summary' 		=> $post['project']['summary'],
      'meta_title' 	=> $post['project']['meta_title'],
      'meta_desc' 	=> $post['project']['meta_desc'],
      'meta_keyw' 	=> $post['project']['meta_keyw'],
      'sub_active' 	=> $post['project']['sub_active'],
      'highlight' 	=> $post['project']['highlight'],
      'slug' 			=> $this->url->string_to_url($post['project']['slug']),
      'project_id' 	=> $id,
      'language_id' 	=> $language_id
    ));
    
    if(haveFilters('projects')) 
      $this->addFilters($id, $post['project']['filters'], $this->config->item('default_language'), 'projects');
    
    if(isset($post['media']) && $post['media'] && count($post['media']) > 0)
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
    
    $this->members_model->attach_caregories($post['project']['category_id'], $id, CONTROLLER);		
    
  }
  
  function projects_categories()
  {
    $sql = '
      SELECT * FROM `category` 
      INNER JOIN `category_content` 
        ON `category_content`.category_id = `category`.category_id
      WHERE `category_content`.language_id = ? 
        AND `category`.controller = ? 
        AND `category`.active = ? 
        AND `category_content`.sub_active = ? 
      ORDER BY `category`.order ASC
    ';

    $r = $this->db->query($sql, array($this->config->item('default_language'), 'projects', 1, 1));

    if (empty($r) || !isset($r) || !count($r)) {
        return false;
    }
    
    $categories = array();

    foreach($r as $k => $v)
    {
      $categories[$v['category_id']] = $v['title'];
    }
    
    return $categories;
  }
  
  function update_overview($post)
  {
    foreach($post['active'] as $k => $v)
    {
      $this->db->query('UPDATE `project` SET `project`.active = ? WHERE `project`.project_id = ? LIMIT 1', array($v, $k));
    }
    foreach($post['highlight'] as $k => $v)
    {
      $this->db->query('UPDATE `project` SET `project`.highlight = ? WHERE `project`.project_id = ? LIMIT 1', array($v, $k));
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
  
  function delete($id)
  {
    $sql = 'SELECT * FROM `project` WHERE `project`.project_id = ?';
    $r = $this->db->query($sql, array($id));

    $this->db->query('DELETE FROM `project` WHERE `project`.project_id = ?', array($id));
    $this->db->query('DELETE FROM `project_content` WHERE `project_content`.project_id = ?', array($id));
    $this->db->query('DELETE FROM `brands_selected` WHERE `brands_selected`.table_id = ? AND `brands_selected`.controller = ?', array($id, CONTROLLER));

    $this->members_model->delete_categories($id, CONTROLLER);
    if(haveFilters('projects')){
        $sql = '
        SELECT * 
        FROM `filter`
        WHERE `filter`.controller = :controller
        ';
        $data = $this->db->query($sql, array('controller' => 'projects'));
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
  
  function delete_media($media_id)
  {
    $sql = 'SELECT `media`.filename FROM `media` WHERE `media`.media_id = ?';
    $data = $this->db->query($sql, array($media_id));
    
    $filename = $data[0]['filename'];

    //$dirs = glob(BASE_PATH . MEDIA_DIR . CONTROLLER . '/*', GLOB_ONLYDIR);
    $dirs = glob(MEDIA_DIR . CONTROLLER . '/*', GLOB_ONLYDIR);

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

    $this->db->query('DELETE FROM `media` WHERE `media`.media_id = ?', array($media_id));
    $this->db->query('DELETE FROM `media_content` WHERE `media_content`.media_id = ?', array($media_id));
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


  function fetch_category_filters($language_id, $controller, $prod_id = 0)
  {
    
    $sql = '
    SELECT * 
    FROM `filter`, `filter_heading`
    WHERE `filter`.filter_id = `filter_heading`.filter_id
    AND `filter_heading`.language_id = :lang
    AND `filter`.controller = :controller
    ';
    
    $data = $this->db->query($sql, array('lang' => $this->config->item('default_language'),'controller' => $controller));
    
    $extra_sql = '';
    $extra_sql .= " AND `filter_item_category`.category_id IN (
                    SELECT category_id FROM `category_selected` WHERE `category_selected`.table_id = ".$prod_id." AND `category_selected`.controller = '".$controller."' 
                    ) ";
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
          '.$extra_sql.'
          AND `filter_item_category`.saved = 1
          AND `filter_heading`.language_id = :language_id
          AND `filter`.filter_id = :filter_id
          
        ';
        
        $subelements2 = $this->db->query($sql, array('language_id' => $language_id, 'filter_id' => $filter['filter_id']));
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
  
  function fetch_filters($controller, $language_id, $project_id = 0)
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
        $extra_sql = '';
        $extra_sql .= " AND  `filter_item_saved`.category_id IN (
                        SELECT category_id FROM `category_selected` WHERE `category_selected`.table_id = ".$prod_id." AND `category_selected`.controller = '".$controller."' 
                        ) ";
        if(isset($subelements2))
          foreach($subelements2 as $j => $sub_element){
            $return[$i]['subelements'][$sub_element['filter_item_identify']] = $sub_element;

            $sql = '
            SELECT *
            FROM `filter_item_saved`
            WHERE `filter_item_saved`.filter_item_id = :filter_item_id
            '.$extra_sql.'
            AND `filter_item_saved`.saved = 1
            ';
            
            $selected = $this->db->query($sql, array('filter_item_id' => $sub_element['filter_item_id_number']));
            
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
  
  function getFilters($controller, $language_id, $id = 0){
    if(haveFilters($controller)){
      if(!haveCategories($controller)) return $this->fetch_filters($controller, $language_id, $id);
      else return $this->fetch_category_filters($language_id, $controller, $id);
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

  function fetch_drop_down($controller, $parent_id = 0)
  {
    $return = array();
    
    $sql = '
    SELECT `category`.category_id, `category`.parent_id, `category_content`.title
    FROM `category`, `category_content` 
    WHERE `category`.category_id = `category_content`.category_id 
    AND `category_content`.language_id = :lang 
    AND `category`.parent_id = :parent_id
    AND `category`.controller = :controller
    ORDER BY `category`.order ASC
    ';
    
    $data = $this->db->query($sql, array('lang' => $this->config->item('default_language'), 'parent_id' => $parent_id, 'controller' => $controller));
    
    $i = 0;
    
    foreach($data as $category)
    {
      $return[$i] = $category;
    
      $return[$i]['children'] = $this->fetch_drop_down($controller,$category['category_id']);
    
      $i++;
    }
    return $return;
  }
  
  function count_children($category_id = 0)
  {
    $sql = 'SELECT `category`.category_id FROM `category` WHERE `category`.parent_id = ?';
    $categorys = $this->db->query($sql, array($category_id));
    
    $sub_sub = 0;
    
    foreach($categorys as $category)
    {
      $sql = 'SELECT `category`.category_id FROM `category` WHERE `category`.parent_id = ?';
      $this->db->query($sql, array($category['category_id']));
      if($this->db->num_rows > 0)
      {
        $sub_sub = $sub_sub + $this->db->num_rows;
      }
    }
    
    $count['sub'] = count($categorys);
    $count['sub_sub'] = $sub_sub;
    
    return $count;
  }
  
  function order($direction, $current_order, $project_id)
  {
    switch($direction)
    {
      case 'up':
        $from = $this->db->query('SELECT `project`.order, `project`.project_id FROM `project` WHERE `project`.project_id = ?', array($project_id));
        $to = $this->db->query('SELECT `project`.order, `project`.project_id FROM `project` WHERE `project`.order < ? ORDER BY `project`.order DESC', array($current_order));
        
        if(!empty($to))
        {
          $this->db->query('UPDATE `project` SET `project`.order = ? WHERE `project`.project_id = ?', array($to[0]['order'], $from[0]['project_id']));
          $this->db->query('UPDATE `project` SET `project`.order = ? WHERE `project`.project_id = ?', array($from[0]['order'], $to[0]['project_id']));
        }
      break;
        
      case 'down':
        $from = $this->db->query('SELECT `project`.order, `project`.project_id FROM `project` WHERE `project`.project_id = ?', array($project_id));
        $to = $this->db->query('SELECT `project`.order, `project`.project_id FROM `project` WHERE `project`.order > ? ORDER BY `project`.order ASC', array($current_order));
        
        if(!empty($to))
        {
          $this->db->query('UPDATE `project` SET `project`.order = ? WHERE `project`.project_id = ?', array($to[0]['order'], $from[0]['project_id']));
          $this->db->query('UPDATE `project` SET `project`.order = ? WHERE `project`.project_id = ?', array($from[0]['order'], $to[0]['project_id']));
        }
      break;
    }
  }
  
}

?>