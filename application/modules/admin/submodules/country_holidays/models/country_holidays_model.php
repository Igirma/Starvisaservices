<?php

class country_holidays_model extends model
{
  function fetch_all()
  {
    return $this->db->query('SELECT * FROM `country_holidays` ORDER BY `country_holidays`.holiday_name ASC');
  }
  
  function fetch($holiday_id)
  {
    $r = $this->db->query('SELECT * FROM `country_holidays` WHERE `country_holidays`.holiday_id = ?', array($holiday_id));
    return isset($r[0]) ? $r[0] : false;
  }

  function get_countries()
  {
    $sql = '
      SELECT * FROM `country`
      INNER JOIN `country_content`
      ON `country`.country_id = `country_content`.country_id
      WHERE `country_content`.language_id = ?
      ORDER BY `country_content`.name ASC
    ';
    return $this->db->query($sql, array($this->config->item('default_language')));
  }

  function add($post)
  {
      $this->db->query('INSERT INTO `country_holidays` ( holiday_country, holiday_name, holiday_day, holiday_month ) VALUES (?, ?, ?, ?)', array(
          $post['holiday']['holiday_country'],
          $post['holiday']['holiday_name'],
          $post['holiday']['holiday_day'],
          $post['holiday']['holiday_month']
      ));
      $id = $this->db->last_insert_id;
      return $id;
  }
  
  function edit($post, $id)
  {
    $sql = '
      UPDATE `country_holidays`
      SET
        `country_holidays`.holiday_country = ?,
        `country_holidays`.holiday_name = ?,
        `country_holidays`.holiday_day = ?,
        `country_holidays`.holiday_month = ?
      WHERE 
        `country_holidays`.holiday_id = ?
    ';
    return $this->db->query($sql, array(
        $post['holiday']['holiday_country'],
        $post['holiday']['holiday_name'],
        $post['holiday']['holiday_day'],
        $post['holiday']['holiday_month'],
        $id
    ));
    
  }

  function delete($id)
  {
    return $this->db->query('DELETE FROM `country_holidays` WHERE `country_holidays`.holiday_id = ?', array($id));
  }
}

?>