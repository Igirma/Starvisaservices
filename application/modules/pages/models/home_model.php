<?php 

class home_model extends model
{
  function fetch_home($language)
  {
      $sql = '
        SELECT * 
        FROM `page`
        LEFT JOIN `page_content`
        ON `page`.page_id = `page_content`.page_id
        WHERE `page_content`.language_id = ?
        AND `page`.controller = ?
        AND `page`.active = 1
        AND `page_content`.sub_active = 1
      ';
      
      $data = $this->db->query($sql, array($language, 'home'));
      if(!empty($data) && !$this->db->error)
      {
        $sql = '
        SELECT * FROM `media` 
        INNER JOIN `media_content`
          ON `media_content`.media_id = `media`.media_id
        WHERE `media`.table_id = ?
          AND `media`.controller = ?
          AND `media_content`.language_id = ?
          AND `media`.album_id = ?
        ORDER BY `media`.order ASC
        ';
        $data[0]['media']['photos'] = $this->db->query($sql, array($data[0]['page_id'], 'pages', $language, 0));

        $sql = '
        SELECT * FROM `media` 
        INNER JOIN `media_content`
          ON `media_content`.media_id = `media`.media_id
        WHERE `media`.table_id = ?
          AND `media`.controller = ?
          AND `media_content`.language_id = ?
          AND `media`.album_id = ?
        ORDER BY `media`.order ASC
        ';
        $data[0]['media']['home'] = $this->db->query($sql, array($data[0]['page_id'], 'pages', $language, 1));

        $sql = '
        SELECT * FROM `media` 
        INNER JOIN `media_content`
          ON `media_content`.media_id = `media`.media_id
        WHERE `media`.table_id = ?
          AND `media`.controller = ?
          AND `media_content`.language_id = ?
          AND `media`.album_id = ?
        ORDER BY `media`.order ASC
        ';
        $data[0]['media']['slide'] = $this->db->query($sql, array($data[0]['page_id'], 'pages', $language, 2));

        $sql = '
          SELECT * FROM `media`
          INNER JOIN `media_type`
            ON `media_type`.media_type_id = `media`.media_type_id
          WHERE `media`.table_id = ?
            AND `media_type`.name = "doc"
            AND `media`.controller = ?
          ORDER BY `media`.filename ASC
        ';
        $data[0]['docs'] = $this->db->query($sql, array($data[0]['page_id'], 'pages'));
        return $data[0];
      }
      return false;
  }
 
}

?>