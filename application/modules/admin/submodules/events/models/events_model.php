<?php

class events_model extends model
{
  function fetch_all()
  {
    $sql = '
    SELECT * FROM `event`
    INNER JOIN `event_content`
    ON `event`.event_id = `event_content`.event_id
    WHERE `event_content`.language_id = ?
    ORDER BY `event_content`.title ASC
    ';
    
    $data['events'] = $this->db->query($sql, array($this->config->item('default_language')));
    return $data;
  }
  
  function fetch($event_id, $language_id)
  {	
    $sql = '
    SELECT *
    FROM `event`
    INNER JOIN `event_content`
    ON `event_content`.event_id = `event`.event_id
    WHERE `event`.event_id = ?
    AND `event_content`.language_id = ?
    ';
    
    $r = $this->db->query($sql, array($event_id, $language_id));
    $data['events'] = $r[0];

    return $data;
  }
  
  function add($post)
  {
    $sql = '
    INSERT INTO `event`
    (
      date,
      time
    )
    VALUES
    (
      :date,
      :time
    )';
      
    
    $this->db->query($sql, array(
        'date' 	=> strtotime($post['events']['date']),
        'time' 	=> $post['events']['time']
    ));
    
    $id = $this->db->last_insert_id;

    $sql = '
    INSERT INTO `event_content`
    (
      event_id,
      language_id,
      title,
      slug,
      content,
      description,
      sub_active
    )
    VALUES
    (
      :event_id,
      :language_id,
      :title,
      :slug,
      :content,
      :description,
      :sub_active
    )';
      
      
    $this->db->query($sql, array(
        'event_id' 	=> $id,
        'language_id' 	=> $this->config->item('default_language'),
        'title' 		=> $post['events']['title'],
        'slug' 	=> $this->url->string_to_url($post['events']['slug']),
        'content' 		=> $post['events']['content'],
        'description' 	=> $post['events']['description'],
        'sub_active' 	=> $post['events']['sub_active']
    ));
    
    $languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array($this->config->item('default_language')));
    
    foreach($languages as $language)
    {				
      $sql = '
      INSERT INTO `event_content`
      (
        event_id,
        language_id,
        sub_active
      )
      VALUES
      (
        :event_id,
        :language_id,
        :sub_active
      )';
      
      $this->db->query($sql, array(
        'event_id' 	=> $id,
        'language_id' 	=> $language['language_id'],
        'sub_active' 	=> 0
      ));
    }

    return $id;
  }
  
  function edit($post, $id, $language_id)
  {
    
    $sql = '
    UPDATE `event`, `event_content`
    SET
      `event`.date 					= :date,
      `event_content`.title 			= :title,
      `event`.time 					= :time,
      `event_content`.slug 	= :slug,
      `event_content`.content 		= :content,
      `event_content`.description 	= :description,
      `event_content`.sub_active 		= :sub_active
    WHERE `event`.event_id 				= :event_id
    AND `event_content`.event_id 		= :event_id
    AND `event_content`.language_id 	= :language_id
    ';
    
    $this->db->query($sql, array(
      'date' 				=> strtotime($post['events']['date']),
      'title' 			=> $post['events']['title'],
      'time' 				=> $post['events']['time'],
      'slug' 	=> $this->url->string_to_url($post['events']['slug']),
      'content' 			=> $post['events']['content'],
      'description' 		=> $post['events']['description'],
      'sub_active' 		=> $post['events']['sub_active'],
      'event_id' 			=> $id,
      'language_id' 		=> $language_id
    ));

  }
  
  function update_overview($post)
  {
    foreach($post['active'] as $k => $v)
    {
      $this->db->query('UPDATE `event` SET `event`.active = ? WHERE `event`.event_id = ? LIMIT 1', array($v, $k));
    }
  }

  function delete($id)
  {
    $sql = 'SELECT * FROM `event` WHERE `event`.event_id = ?';
    $r = $this->db->query($sql, array($id));

    $this->db->query('DELETE FROM `event` WHERE `event`.event_id = ?', array($id));
    $this->db->query('DELETE FROM `event_content` WHERE `event_content`.event_id = ?', array($id));
    
    foreach($media_ar as $media)
    {
      $this->delete_media($media['media_id']);
    }			

  }

}

?>