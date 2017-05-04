<?php

class sliders_model extends model
{
    function fetch_all()
    {
        $sql = '
          SELECT * FROM `slider`
          INNER JOIN `slider_content`
          ON `slider_content`.slider_id = `slider`.slider_id
          WHERE `slider_content`.language_id = ? 
          ORDER BY `order` ASC
        ';
        
        return $this->db->query($sql, array(
            $this->config->item('default_language')
        ));
    }
    
    function fetch($slider_id, $language_id)
    {
        $sql = '
          SELECT * FROM `slider`
          INNER JOIN `slider_content`
          ON `slider_content`.slider_id = `slider`.slider_id
          WHERE `slider_content`.language_id = ?
          AND `slider`.slider_id = ?
        ';
        
        $temp = $this->db->query($sql, array(
            $language_id,
            $slider_id
        ));
        
        if (!isset($temp[0])) {
            return false;
        }
        
        $data['slider'] = $temp[0];
        
        $sql = '
          SELECT *
          FROM `media`
          INNER JOIN `media_content`
          ON `media_content`.media_id = `media`.media_id
          WHERE `media`.table_id = ?
          AND `media`.controller = "sliders"
          AND `media_content`.language_id = ?
          ORDER BY `media`.order ASC
        ';
        
        $data['media'] = $this->db->query($sql, array(
            $slider_id,
            $language_id
        ));
        return $data;
    }
    
    function add($post)
    {
        //debug($post);
        
        $order = $this->db->query('SELECT MAX(`slider`.order) AS `order` FROM `slider`');
        
        $sql = '
        INSERT INTO `slider`
        (
          `slider`.order
        )
        VALUES
        (
          :order
        )';
        
        $this->db->query($sql, array(
            'order' => (isset($order[0]['order']) ? $order[0]['order'] + 1 : 1)
        ));
        
        $slider_id = $this->db->last_insert_id;
        
        $sql = '
          INSERT INTO `slider_content`
          (
            `slider_content`.slider_id,
            `slider_content`.language_id,
            `slider_content`.title,
            `slider_content`.description,
            `slider_content`.content,
            `slider_content`.button_text,
            `slider_content`.button_url,
            `slider_content`.button_page_id,
            `slider_content`.sub_active
          )
          VALUES
          (
            :slider_id,
            :language_id,
            :title,
            :description,
            :content,
            :button_text,
            :button_url,
            :button_page_id,
            :sub_active
          )
        ';
        
        $this->db->query($sql, array(
            'slider_id' => $slider_id,
            'language_id' => $this->config->item('default_language'),
            'title' => $post['slider']['title'],
            'description' => $post['slider']['description'],
            'content' => $post['slider']['content'],
            'button_text' => $post['slider']['button_text'],
            'button_url' => ((int) $post['slider']['button_page_id'] > 0 ? '' : $post['slider']['button_url']),
            'button_page_id' => $post['slider']['button_page_id'],
            'sub_active' => $post['sub_active']
        ));
        
        $languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array(
            $this->config->item('default_language')
        ));
        
        foreach ($languages as $language) {
            $sql = '
            INSERT INTO `slider_content`
            (
              slider_id,
              language_id
            )
            VALUES
            (
              :slider_id,
              :language_id
            )';
            
            $this->db->query($sql, array(
                'slider_id' => $slider_id,
                'language_id' => $language['language_id']
            ));
        }
        
        return $slider_id;
    }
    
    function edit($post, $slider_id, $language_id)
    {
        $sql = '
          UPDATE `slider`, `slider_content`
          SET
            `slider_content`.title 			= :title,
            `slider_content`.description 	= :description,
            `slider_content`.content 		= :content,
            `slider_content`.sub_active 	= :sub_active,
            `slider_content`.button_text 	= :button_text,
            `slider_content`.button_url 	= :button_url,
            `slider_content`.button_page_id = :button_page_id
          WHERE `slider`.slider_id 			= :slider_id
          AND `slider_content`.slider_id 		= :slider_id
          AND `slider_content`.language_id 	= :language_id
        ';
        
        $this->db->query($sql, array(
            'title' => ucfirst($post['slider']['title']),
            'description' => $post['slider']['description'],
            'content' => $post['slider']['content'],
            'sub_active' => $post['sub_active'],
            'button_text' => $post['slider']['button_text'],
            'button_url' => ((int) $post['slider']['button_page_id'] > 0 ? '' : $post['slider']['button_url']),
            'button_page_id' => $post['slider']['button_page_id'],
            'slider_id' => $slider_id,
            'language_id' => $language_id
        ));
        
        foreach ($post['media'] as $media) {
            $sql = '
              UPDATE `media`, `media_content`
              SET
                `media`.album_thumb = :album_thumb
              WHERE `media`.media_id = :media_id
              AND `media_content`.media_id = :media_id
              AND `media_content`.language_id = :language_id
            ';
            
            $this->db->query($sql, array(
                'album_thumb' => (isset($_POST['set_thumbnail']) && $_POST['set_thumbnail'] == $media['media_id'] ? 1 : 0),
                'media_id' => $media['media_id'],
                'language_id' => $language_id
            ));
        }
    }
    
    function delete($id)
    {
        $this->db->query('DELETE FROM `slider` WHERE `news`.slider_id = ?', array(
            $id
        ));
        $this->db->query('DELETE FROM `slider_content` WHERE `slider_content`.slider_id = ?', array(
            $id
        ));
        $this->db->query('DELETE FROM `slider_selected` WHERE `slider_selected`.slider_id = ?', array(
            $id
        ));
        
        $sql = '
        SELECT *
        FROM `media`
        WHERE `media`.table_id = ?
        AND `media`.controller = ?
        ';
        
        $media_ar = $this->db->query($sql, array(
            $id,
            CONTROLLER
        ));
        
        foreach ($media_ar as $media) {
            $this->delete_media($media['media_id']);
        }
    }
    
    function order_media($direction, $table_id, $language_id, $current_order)
    {
        switch ($direction) {
            case 'left':
                $from = $this->db->query('SELECT `media`.order, `media`.media_id FROM `media` WHERE `media`.table_id = ? AND `media`.order = ?', array(
                    $table_id,
                    $current_order
                ));
                $to = $this->db->query('SELECT `media`.order, `media`.media_id FROM `media` WHERE `media`.order < ? AND `media`.table_id = ? ORDER BY `media`.order DESC', array(
                    $current_order,
                    $table_id
                ));
                
                if (!empty($to)) {
                    $this->db->query('UPDATE `media` SET `media`.order = ? WHERE `media`.media_id = ?', array(
                        $to[0]['order'],
                        $from[0]['media_id']
                    ));
                    $this->db->query('UPDATE `media` SET `media`.order = ? WHERE `media`.media_id = ?', array(
                        $from[0]['order'],
                        $to[0]['media_id']
                    ));
                }
                break;
            
            case 'right':
                $from = $this->db->query('SELECT `media`.order, `media`.media_id FROM `media` WHERE `media`.table_id = ? AND `media`.order = ?', array(
                    $table_id,
                    $current_order
                ));
                $to = $this->db->query('SELECT `media`.order, `media`.media_id FROM `media` WHERE `media`.order > ? AND `media`.table_id = ? ORDER BY `media`.order ASC', array(
                    $current_order,
                    $table_id
                ));
                
                if (!empty($to)) {
                    $this->db->query('UPDATE `media` SET `media`.order = ? WHERE `media`.media_id = ?', array(
                        $to[0]['order'],
                        $from[0]['media_id']
                    ));
                    $this->db->query('UPDATE `media` SET `media`.order = ? WHERE `media`.media_id = ?', array(
                        $from[0]['order'],
                        $to[0]['media_id']
                    ));
                }
                break;
        }
    }
    
    function delete_media($media_id)
    {
        $sql = 'SELECT `media`.filename FROM `media` WHERE `media`.media_id = ?';
        $data = $this->db->query($sql, array(
            $media_id
        ));
        
        $filename = $data[0]['filename'];
        
        $dirs = glob(BASE_PATH . MEDIA_DIR . CONTROLLER . '/*', GLOB_ONLYDIR);
        
        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                if (file_exists($dir . '/' . $filename)) {
                    unlink($dir . '/' . $filename);
                }
            }
        }
        
        $this->db->query('DELETE FROM `media` WHERE `media`.media_id = ? LIMIT 1', array(
            $media_id
        ));
        $this->db->query('DELETE FROM `media_content` WHERE `media_content`.media_id = ? LIMIT 1', array(
            $media_id
        ));
    }
    
    function update_overview($post)
    {
        foreach ($post['active'] as $k => $v) {
            $this->db->query('UPDATE `slider` SET `slider`.active = ? WHERE `slider`.slider_id = ? LIMIT 1', array(
                $v,
                $k
            ));
        }
    }
    
    function order($direction, $current_order, $slider_id)
    {
        switch ($direction) {
            case 'up':
                $from = $this->db->query('SELECT `slider`.order, `slider`.slider_id FROM `slider` WHERE `slider`.slider_id = ?', array(
                    $slider_id
                ));
                $to = $this->db->query('SELECT `slider`.order, `slider`.slider_id FROM `slider` WHERE `slider`.order < ? ORDER BY `slider`.order DESC', array(
                    $current_order
                ));
                
                if (!empty($to)) {
                    $this->db->query('UPDATE `slider` SET `slider`.order = ? WHERE `slider`.slider_id = ?', array(
                        $to[0]['order'],
                        $from[0]['slider_id']
                    ));
                    $this->db->query('UPDATE `slider` SET `slider`.order = ? WHERE `slider`.slider_id = ?', array(
                        $from[0]['order'],
                        $to[0]['slider_id']
                    ));
                }
                break;
            
            case 'down':
                $from = $this->db->query('SELECT `slider`.order, `slider`.slider_id FROM `slider` WHERE `slider`.slider_id = ?', array(
                    $slider_id
                ));
                $to = $this->db->query('SELECT `slider`.order, `slider`.slider_id FROM `slider` WHERE `slider`.order > ? ORDER BY `slider`.order ASC', array(
                    $current_order
                ));
                
                if (!empty($to)) {
                    $this->db->query('UPDATE `slider` SET `slider`.order = ? WHERE `slider`.slider_id = ?', array(
                        $to[0]['order'],
                        $from[0]['slider_id']
                    ));
                    $this->db->query('UPDATE `slider` SET `slider`.order = ? WHERE `slider`.slider_id = ?', array(
                        $from[0]['order'],
                        $to[0]['slider_id']
                    ));
                }
                break;
        }
    }
    
    function fetch_pages_selected($slider_id)
    {
        $rows = $this->db->query('SELECT page_id FROM `slider_selected` WHERE `slider_selected`.slider_id = ?', array(
            $slider_id
        ));
        
        if (empty($rows) || !count($rows)) {
            return array();
        }
        
        $items = array();
        foreach ($rows as $row) {
            $items[] = $row['page_id'];
        }
        
        return $items;
    }
    
    function attach_pages($post, $id)
    {
        
        $this->db->query('DELETE FROM `slider_selected` WHERE `slider_selected`.slider_id = ?', array(
            $id
        ));
        
        if (isset($post['slider']['page_id']) && count($post['slider']['page_id']) > 0) {
            foreach ($post['slider']['page_id'] as $page_id) {
                $sql = '
                INSERT INTO `slider_selected`
                (
                  slider_id,
                  page_id
                )
                VALUES
                (
                  :slider_id,
                  :page_id
                )
                ';
                
                $this->db->query($sql, array(
                    'slider_id' => $id,
                    'page_id' => $page_id
                ));
            }
        }
    }

}

?>