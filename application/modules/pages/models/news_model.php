<?php 
class news_model extends model
{
  function __construct()
  {
    
  }
  
  function fetch_news($language, $limit = 0)
  {
    $sql = '
      SELECT * 
      FROM `news`
      LEFT JOIN `news_content`
      ON `news`.news_id = `news_content`.news_id
      WHERE `news_content`.language_id = ?
      AND `news`.active = 1
      AND `news`.archive = 0
      AND `news_content`.sub_active = 1
      ORDER BY start_date DESC';
    if ($limit != 0) {
      $sql .= ' LIMIT ' . (int) $limit;
    }
    
    $r = $this->db->query($sql, array($language));

    if(!empty($r) && !$this->db->error)
    {
      $result = $r;
      foreach($result as $k => $item){
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
      
        $result[$k]['media'] = $this->db->query($sql, array($item['news_id'], $language));
      }
      return $result;
    }
    return false;
  }
  
  function fetch_last_news($language, $action = '')
  {
    if($action != 0){
    if($action == 2) $action = 0;
    $sql = '
      SELECT * 
      FROM `news`
      LEFT JOIN `news_content`
      ON `news`.news_id = `news_content`.news_id
      WHERE `news_content`.language_id = ?
      AND `news`.active = 1
      AND `news`.archive = 0
      AND `news`.action = ?
      AND `news_content`.sub_active = 1
      ORDER BY start_date DESC, `news`.news_id DESC
      LIMIT 2';
    
    $r = $this->db->query($sql, array($language, $action));
  }
  else{
    $sql = '
      SELECT * 
      FROM `news`
      LEFT JOIN `news_content`
      ON `news`.news_id = `news_content`.news_id
      WHERE `news_content`.language_id = ?
      AND `news`.active = 1
      AND `news`.archive = 0
      AND `news_content`.sub_active = 1
      ORDER BY start_date DESC, `news`.news_id DESC
      LIMIT 2';
    
    $r = $this->db->query($sql, array($language));
    

  }	
    if(!empty($r) && !$this->db->error)
    {
      $result = $r;
      foreach($result as $k => $item){
        $sql = '
          SELECT *
          FROM `media`
          INNER JOIN `media_content`
          ON `media_content`.media_id = `media`.media_id
          WHERE `media`.table_id = ?
          AND `media`.controller = "news"
          AND `media_content`.language_id = ?
          ORDER BY `media`.order ASC
          LIMIT 1
        ';
      
        $result[$k]['media'] = $this->db->query($sql, array($item['news_id'], $language));
      }
      return $result;
    }
    else
    {
      return false;
    }
  }

  function fetch_news_item($language, $segment)
  {
    $sql = '
      SELECT * 
      FROM `news`
      LEFT JOIN `news_content`
      ON `news`.news_id = `news_content`.news_id
      WHERE `news_content`.language_id = ?
      AND `news_content`.slug = ?
      AND `news`.active = 1
      AND `news`.archive = 0
      AND `news_content`.sub_active = 1';
    
    $r = $this->db->query($sql, array($language, $segment));
    
    if(!empty($r) && !$this->db->error)
    {
      $result = $r[0];
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
    
      $result['media'] = $this->db->query($sql, array($result['news_id'], $language));
      
      return $result;
    }
    else
    {
      return false;
    }
  }

  function fetch_filters($language){
    $sql = '
    SELECT * 
    FROM `filter`, `filter_heading` 
    WHERE `filter`.filter_id = `filter_heading`.filter_id 
    AND `filter`.controller = :controller
    AND `filter_heading`.language_id = :lang 
    ORDER BY `filter`.order ASC
    ';
    
    $return = $this->db->query($sql, array('lang' => $this->config->item('default_language'), 'controller' => 'news'));
    $data = $return[0];
    
    $sql = '
      SELECT *
      FROM `filter_heading`
      INNER JOIN `filter_item`
      ON `filter_item`.filter_heading_id = `filter_heading`.filter_heading_id
      WHERE `filter_heading`.language_id = ?
      AND `filter_heading`.filter_heading_id = ?
      ORDER BY `filter_item`.filter_item_id
    ';
  
    $data['options'] = $this->db->query($sql, array($language, $data['filter_heading_id']));
    
    return $data;
  }
}
?>