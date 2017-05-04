<?php 
class news_model extends model
{	
  function fetch($news_id, $language_id)
  {	
    $sql = '
    SELECT *
    FROM `news`
    INNER JOIN `news_content`
    ON `news_content`.news_id = `news`.news_id
    WHERE `news`.news_id = ?
    AND `news_content`.language_id = ?
    ';
    
    $r = $this->db->query($sql, array($news_id, $language_id));
    $data['news'] = $r[0];

    $sql = '
      SELECT *
      FROM `media`
      INNER JOIN `media_content`
      ON `media_content`.media_id = `media`.media_id
      WHERE `media`.table_id = ?
      AND `media`.controller = "news"
      AND `media_content`.language_id = ?
      ORDER BY `media`.order ASC
    ';
  
    $data['media'] = $this->db->query($sql, array($data['news']['news_id'], $language_id));
    
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
    
    $data['docs'] = $this->db->query($sql, array($data['news']['news_id'], CONTROLLER));
  
    return $data;
  }
  
  function fetch_dash()
  {
    $sql = '
      SELECT * 
      FROM `news`, `news_content` 
      WHERE `news`.news_id = `news_content`.news_id 
      AND `news_content`.language_id = :lang 
      ORDER BY `news`.date_created DESC, `news_content`.last_update DESC
      LIMIT 5
    ';
    $data = $this->db->query($sql, array('lang' => $this->config->item('default_language')));
    
    return $data;
  }
  
  function fetch_all($archive)
  {
    $sql = '
    SELECT
      `news`.news_id,
      `news`.date_created,
      `news`.start_date,
      `news`.end_date,
      `news`.archive,
      `news`.active,
      `news`.highlight,
      `news`.no_end_date,
      `news`.page_news,
      `news`.page_school,
      `news_content`.language_id,
      `news_content`.title
    FROM `news`
    INNER JOIN `news_content`
      ON `news_content`.news_id = `news`.news_id
    WHERE `news`.archive = ' . $archive . '
      AND `news_content`.language_id = ?
    ORDER BY `news`.start_date DESC
    ';
    
    return $this->db->query($sql, array($this->config->item('default_language')));
  }
  
  function add($post)
  {	
    $sql = '
    INSERT INTO `news`
    (
      edit_by,
      date_created,
      no_end_date,
      page_news,
      page_school,
      start_date,
      end_date
    )
    VALUES
    (
      :edit_by,
      :date_created,
      :no_end_date,
      :page_news,
      :page_school,
      :start_date,
      :end_date
    )';
      
    
    $this->db->query($sql, array(
        'edit_by' 		=> $_SESSION['username'],
        'date_created' 	=> time(),
        'no_end_date' 	=> $post['news']['no_end_date'],
        'page_news' 	=> (isset($post['news']['page_news']) && $post['news']['page_news'] == 1 ? 1 : 0),
        'page_school' 	=> (isset($post['news']['page_school']) && $post['news']['page_school'] == 1 ? 1 : 0),
        'start_date' 	=> strtotime($post['news']['start_date']),
        'end_date' 		=> strtotime($post['news']['end_date'])
    ));
    
    $id = $this->db->last_insert_id;
    
    $sql = '
    INSERT INTO `news_content`
    (
      news_id,
      language_id,
      slug,
      title,
      description,
      content,
      extra_content,
      meta_title,
      meta_desc,
      meta_keyw,
      sub_active
    )
    VALUES
    (
      :news_id,
      :language_id,
      :slug,
      :title,
      :description,
      :content,
      :extra_content,
      :meta_title,
      :meta_desc,
      :meta_keyw,
      :sub_active
    )';
      
    $this->db->query($sql, array(
        'news_id' 		=> $id,
        'language_id' 	=> $this->config->item('default_language'),
        'slug' 			=> $this->url->string_to_url($post['news']['slug']),
        'title' 		=> $post['news']['title'],
        'description' 	=> $post['news']['description'],
        'content' 		=> $post['news']['content'],
        'extra_content' => $post['news']['extra_content'],
        'meta_title' 	=> $post['news']['meta_title'],
        'meta_desc' 	=> $post['news']['meta_desc'],
        'meta_keyw' 	=> $post['news']['meta_keyw'],
        'sub_active' 	=> $post['news']['sub_active']
    ));
    
    if(haveFilters('news')) 
      $this->addFilters($id, $post['news']['filters'], $this->config->item('default_language'), 'news');
    
    $languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array($this->config->item('default_language')));
    
    foreach($languages as $language)
    {
      $sql = '
      INSERT INTO `news_content`
      (
        news_id,
        language_id,
        sub_active
      )
      VALUES
      (
        :news_id,
        :language_id,
        :sub_active
      )';
      
      $this->db->query($sql, array(
        'news_id' 		=> $id,
        'language_id' 	=> $language['language_id'],
        'sub_active' 	=> 0
      ));
    }
    $this->members_model->attach_caregories($post['news']['category_id'], $id, 'news');		
    
    return $id;
  }
  
  function edit($post, $id, $language_id)
  {
    $sql = 'SELECT `news_content`.slug FROM `news_content` WHERE `news_content`.news_id = ? AND `news_content`.language_id = ?';
    $content = $this->db->query($sql, array($id, $language_id));
    
    $old_slug = $content[0]['slug'];
    
    if($old_slug != $this->url->string_to_url($post['news']['slug']))
    {
      $sql = '
      UPDATE `news_content`
      SET `news_content`.slug_301 = :slug_301
      WHERE `news_content`.news_id = :news_id
      AND `news_content`.language_id = :language_id
      ';
      
      $this->db->query($sql, array(
        'news_id' => $id,
        'language_id' => $language_id,
        'slug_301' => $old_slug
      ));
    }
    
    $sql = '
    UPDATE `news`, `news_content`
    SET
      `news`.start_date 			= :start_date,
      `news`.end_date 			= :end_date,
      `news`.edit_by				= :edit_by,
      `news_content`.last_update 	= :last_update,
      `news_content`.title 		= :title,
      `news_content`.description = :description,
      `news_content`.content = :content,
      `news_content`.extra_content = :extra_content,
      `news_content`.meta_title = :meta_title,
      `news_content`.meta_desc = :meta_desc,
      `news_content`.meta_keyw = :meta_keyw,
      `news_content`.sub_active 	= :sub_active,
      `news`.no_end_date 	= :no_end_date,
      `news`.page_news 	= :page_news,
      `news`.page_school 	= :page_school,
      `news_content`.slug 		= :slug
    WHERE `news`.news_id 			= :news_id
    AND `news_content`.news_id 		= :news_id
    AND `news_content`.language_id 	= :language_id
    ';
    
    $this->db->query($sql, array(
      'start_date' 	=> strtotime($post['news']['start_date']),
      'end_date' 		=> strtotime($post['news']['end_date']),
      'edit_by'		=> $_SESSION['username'],
      'last_update' 	=> time(),
      'title' => ucfirst($post['news']['title']),
      'description' => $post['news']['description'],
      'content' => $post['news']['content'],
      'extra_content' => $post['news']['extra_content'],
      'meta_title' => $post['news']['meta_title'],
      'meta_desc' => $post['news']['meta_desc'],
      'meta_keyw' => $post['news']['meta_keyw'],
      'sub_active' => $post['news']['sub_active'],
      'no_end_date' => $post['news']['no_end_date'],
      'page_news' => (isset($post['news']['page_news']) && $post['news']['page_news'] == 1 ? 1 : 0),
      'page_school' => (isset($post['news']['page_school']) && $post['news']['page_school'] == 1 ? 1 : 0),
      'slug' => $this->url->string_to_url($post['news']['slug']),
      'news_id' => $id,
      'language_id' => $language_id
    ));
    
    if(haveFilters('news')) 
      $this->addFilters($id, $post['news']['filters'], $language_id, 'news');
    
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
    $this->members_model->attach_caregories($post['news']['category_id'], $id, 'news');		
    
  }
  
  function delete($id)
  {
    $this->db->query('DELETE FROM `news` WHERE `news`.news_id = ?', array($id));
    $this->db->query('DELETE FROM `news_content` WHERE `news_content`.news_id = ?', array($id));
    if(haveFilters('news')){
        $sql = '
        SELECT * 
        FROM `filter`
        WHERE `filter`.controller = :controller
        ';	
        $data = $this->db->query($sql, array('controller' => 'news'));
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
      $this->db->query('UPDATE `news` SET `news`.active = ? WHERE `news`.news_id = ? LIMIT 1', array($v, $k));
    }
    foreach($post['no_end_date'] as $k => $v)
    {
      $this->db->query('UPDATE `news` SET `news`.no_end_date = ? WHERE `news`.news_id = ? LIMIT 1', array($v, $k));
    }
    foreach($post['highlight'] as $k => $v)
    {
      $this->db->query('UPDATE `news` SET `news`.highlight = ? WHERE `news`.news_id = ? LIMIT 1', array($v, $k));
    }
    foreach($post['page_news'] as $k => $v)
    {
      $this->db->query('UPDATE `news` SET `news`.page_news = ? WHERE `news`.news_id = ? LIMIT 1', array($v, $k));
    }
    foreach($post['page_school'] as $k => $v)
    {
      $this->db->query('UPDATE `news` SET `news`.page_school = ? WHERE `news`.news_id = ? LIMIT 1', array($v, $k));
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
    $sql = 'SELECT * FROM `news_content` WHERE `news_content`.news_id = ? AND `news_content`.language_id = ?';
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