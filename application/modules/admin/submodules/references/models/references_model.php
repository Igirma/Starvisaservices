<?php

class references_model extends model
{
  function fetch_all()
  {
    $sql = '
      SELECT `reference`.*, `reference_content`.*, `blog_content`.title AS blog_title FROM `reference`
      INNER JOIN `reference_content`
      ON `reference`.reference_id = `reference_content`.reference_id
      LEFT JOIN `blog` ON `blog`.blog_id = `reference`.blog_id 
      LEFT JOIN `blog_content` ON `blog_content`.blog_id = `reference`.blog_id AND `blog_content`.language_id = ? 
      WHERE `reference_content`.language_id = ? 
      ORDER BY `reference`.blog_id DESC, `reference`.order ASC
    ';
    $data['references'] = $this->db->query($sql, array($this->config->item('default_language'), $this->config->item('default_language')));
    return $data;
  }
  
  function fetch_dash()
  {
    $sql = '
      SELECT * 
      FROM `reference`, `reference_content` 
      WHERE `reference`.reference_id = `reference_content`.reference_id 
      AND `reference_content`.language_id = :lang
      AND `reference`.last_update = ""
      ORDER BY `reference`.date_created DESC
      LIMIT 5
    ';
    $data['add'] = $this->db->query($sql, array('lang' => $this->config->item('default_language')));
    
    $sql = '
      SELECT * 
      FROM `reference`, `reference_content` 
      WHERE `reference`.reference_id = `reference_content`.reference_id 
      AND `reference_content`.language_id = :lang 
      AND `reference`.last_update != ""
      ORDER BY `reference`.last_update DESC
      LIMIT 5
    ';
    $data['edit'] = $this->db->query($sql, array('lang' => $this->config->item('default_language')));
    
    return $data;
  }
  
  function fetch($reference_id, $language_id)
  {	
    $sql = '
    SELECT *
    FROM `reference`
    INNER JOIN `reference_content`
    ON `reference_content`.reference_id = `reference`.reference_id
    WHERE `reference`.reference_id = ?
    AND `reference_content`.language_id = ?
    ';
    
    $r = $this->db->query($sql, array($reference_id, $language_id));
    $data['reference'] = $r[0];

    $sql = '
      SELECT *
      FROM `media`
      INNER JOIN `media_content`
      ON `media_content`.media_id = `media`.media_id
      INNER JOIN `media_type`
      ON `media_type`.media_type_id = `media`.media_type_id
      WHERE `media`.table_id = ?
      AND `media_type`.name = "img"
      AND `media`.controller = ?
      AND `media_content`.language_id = ?
      ORDER BY `media`.order ASC
    ';
  
    $data['media'] = $this->db->query($sql, array($data['reference']['reference_id'], CONTROLLER, $language_id));
    
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
    
    $data['docs'] = $this->db->query($sql, array($data['reference']['reference_id'], CONTROLLER));
  
    return $data;
  }
  
  function fetch_blog()
  {
    $sql = '
      SELECT * FROM `blog` 
        INNER JOIN `blog_content` ON `blog_content`.blog_id = `blog`.blog_id 
      WHERE `blog_content`.language_id = ? 
        AND `blog_content`.sub_active = ? 
        AND `blog`.active = ? 
      ORDER BY `blog`.date_created DESC
    ';
    $data = $this->db->query($sql, array($this->config->item('default_language'), 1, 1));
    if (empty($data)) {
        return false;
    }
    return $data;
  }

  function add($post)
  {
    $order = $this->db->query('SELECT MAX(`order`) + 1 AS `order` FROM `reference`');

    if($order[0]['order'] == '')
    {
      $order = 0;
    }
    else
    {
      $order = $order[0]['order'];
    }

    $sql = '
    INSERT INTO `reference`
    (
      blog_id,
      reference_date,
      date_created,
      edit_by,
      `order`,
      highlight
    )
    VALUES
    (
      :blog_id,
      :reference_date,
      :date_created,
      :edit_by,
      :order,
      :highlight
    )';
      
    
    $this->db->query($sql, array(
        'blog_id' 		=> $post['reference']['blog_id'],
        'reference_date' 	=> strtotime($post['reference']['reference_date']),
        'date_created' 		=> time(),
        'edit_by' 			=> $_SESSION['username'],
        'order' 			=> $order,
        'highlight' 		=> $post['reference']['highlight']
    ));
    
    $id = $this->db->last_insert_id;

    $sql = '
    INSERT INTO `reference_content`
    (
      reference_id,
      language_id,
      slug,
      title,
      description,
      content,
      meta_title,
      meta_desc,
      meta_keyw,
      sub_active
    )
    VALUES
    (
      :reference_id,
      :language_id,
      :slug,
      :title,
      :description,
      :content,
      :meta_title,
      :meta_desc,
      :meta_keyw,
      :sub_active
    )';
      
    $this->db->query($sql, array(
        'reference_id' 	=> $id,
        'language_id' 	=> $this->config->item('default_language'),
        'slug' 			=> $this->url->string_to_url($post['reference']['slug']),
        'title' 		=> $post['reference']['title'],
        'description' 	=> $post['reference']['description'],
        'content' 		=> $post['reference']['content'],
        'meta_title' 	=> $post['reference']['meta_title'],
        'meta_desc' 	=> $post['reference']['meta_desc'],
        'meta_keyw' 	=> $post['reference']['meta_keyw'],
        'sub_active' 	=> $post['reference']['sub_active']
    ));
    
    $languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array($this->config->item('default_language')));
    
    foreach($languages as $language)
    {				
      $sql = '
      INSERT INTO `reference_content`
      (
        reference_id,
        language_id,
        sub_active
      )
      VALUES
      (
        :reference_id,
        :language_id,
        :sub_active
      )';
      
      $this->db->query($sql, array(
        'reference_id' 	=> $id,
        'language_id' 	=> $language['language_id'],
        'sub_active' 	=> 0
      ));
    }
    
    return $id;
  }
  
  function edit($post, $id, $language_id)
  {
    $sql = 'SELECT `reference_content`.slug FROM `reference_content` WHERE `reference_content`.reference_id = ? AND `reference_content`.language_id = ?';
    $content = $this->db->query($sql, array($id, $language_id));
    
    $old_slug = $content[0]['slug'];
    
    if($old_slug != $this->url->string_to_url($post['reference']['slug']))
    {
      $sql = '
      UPDATE `reference_content`
      SET `reference_content`.slug_301 = :slug_301
      WHERE `reference_content`.reference_id = :reference_id
      AND `reference_content`.language_id = :language_id
      ';
      
      $this->db->query($sql, array(
        'reference_id' 	=> $id,
        'language_id' 	=> $language_id,
        'slug_301' 		=> $old_slug
      ));
    }
    
    $sql = '
    UPDATE `reference`, `reference_content`
    SET
      `reference`.blog_id 			= :blog_id,
      `reference`.reference_date 			= :reference_date,
      `reference`.last_update 			= :last_update,
      `reference`.edit_by 				= :edit_by,
      `reference`.highlight 				= :highlight,
      `reference_content`.title 			= :title,
      `reference_content`.description 	= :description,
      `reference_content`.content 		= :content,
      `reference_content`.meta_title 		= :meta_title,
      `reference_content`.meta_desc 		= :meta_desc,
      `reference_content`.meta_keyw 		= :meta_keyw,
      `reference_content`.sub_active 		= :sub_active,
      `reference_content`.slug 			= :slug
    WHERE `reference`.reference_id 			= :reference_id
    AND `reference_content`.reference_id 	= :reference_id
    AND `reference_content`.language_id 	= :language_id
    ';
    
    $this->db->query($sql, array(
      'blog_id' 		=> $post['reference']['blog_id'],
      'reference_date' 	=> strtotime($post['reference']['reference_date']),
      'last_update' 		=> time(),
      'edit_by' 			=> $_SESSION['username'],
      'title' 			=> ucfirst($post['reference']['title']),
      'description' 		=> $post['reference']['description'],
      'content' 			=> $post['reference']['content'],
      'meta_title' 		=> $post['reference']['meta_title'],
      'meta_desc' 		=> $post['reference']['meta_desc'],
      'meta_keyw' 		=> $post['reference']['meta_keyw'],
      'sub_active' 		=> $post['reference']['sub_active'],
      'highlight' 		=> $post['reference']['highlight'],
      'slug' 				=> $this->url->string_to_url($post['reference']['slug']),
      'reference_id' 		=> $id,
      'language_id' 		=> $language_id
    ));
    
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
  
  function update_overview($post)
  {
    foreach($post['active'] as $k => $v)
    {
      $this->db->query('UPDATE `reference` SET `reference`.active = ? WHERE `reference`.reference_id = ? LIMIT 1', array($v, $k));
    }
    foreach($post['highlight'] as $k => $v)
    {
      $this->db->query('UPDATE `reference` SET `reference`.highlight = ? WHERE `reference`.reference_id = ? LIMIT 1', array($v, $k));
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
    $sql = 'SELECT * FROM `reference` WHERE `reference`.reference_id = ?';
    $r = $this->db->query($sql, array($id));

    $this->db->query('DELETE FROM `reference` WHERE `reference`.reference_id = ?', array($id));
    $this->db->query('DELETE FROM `reference_content` WHERE `reference_content`.reference_id = ?', array($id));
    
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
  
  function order($direction, $current_order, $reference_id)
  {
    switch($direction)
    {
      case 'up':
        $from = $this->db->query('SELECT `reference`.order, `reference`.reference_id FROM `reference` WHERE `reference`.reference_id = ?', array($reference_id));
        $to = $this->db->query('SELECT `reference`.order, `reference`.reference_id FROM `reference` WHERE `reference`.order < ? ORDER BY `reference`.order DESC', array($current_order));
        
        if(!empty($to))
        {
          $this->db->query('UPDATE `reference` SET `reference`.order = ? WHERE `reference`.reference_id = ?', array($to[0]['order'], $from[0]['reference_id']));
          $this->db->query('UPDATE `reference` SET `reference`.order = ? WHERE `reference`.reference_id = ?', array($from[0]['order'], $to[0]['reference_id']));
        }
      break;
        
      case 'down':
        $from = $this->db->query('SELECT `reference`.order, `reference`.reference_id FROM `reference` WHERE `reference`.reference_id = ?', array($reference_id));
        $to = $this->db->query('SELECT `reference`.order, `reference`.reference_id FROM `reference` WHERE `reference`.order > ? ORDER BY `reference`.order ASC', array($current_order));
        
        if(!empty($to))
        {
          $this->db->query('UPDATE `reference` SET `reference`.order = ? WHERE `reference`.reference_id = ?', array($to[0]['order'], $from[0]['reference_id']));
          $this->db->query('UPDATE `reference` SET `reference`.order = ? WHERE `reference`.reference_id = ?', array($from[0]['order'], $to[0]['reference_id']));
        }
      break;
    }
  }
    
}

?>