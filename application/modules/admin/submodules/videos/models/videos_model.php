<?php

class videos_model extends model
{
    function fetch_all()
    {
        $sql = '
          SELECT * FROM `videos`
          INNER JOIN `videos_content`
          ON `videos`.video_id = `videos_content`.video_id
          WHERE `videos_content`.language_id = ?
          ORDER BY `videos`.order ASC
        ';
        
        return $this->db->query($sql, array(
            $this->config->item('default_language')
        ));
    }
    
    function fetch_active_videos($language)
    {
        $sql = '
          SELECT * FROM `videos`
          INNER JOIN `videos_content`
          ON `videos`.video_id = `videos_content`.video_id
          WHERE `videos_content`.language_id = ?
          AND `videos`.active = 1
          AND `videos_content`.sub_active = 1
          ORDER BY `videos`.order ASC
        ';

        return $this->db->query($sql, array($language));
    }
    
    function fetch($video_id, $language_id)
    {
        $sql = '
          SELECT *
          FROM `videos`
          INNER JOIN `videos_content`
          ON `videos_content`.video_id = `videos`.video_id
          WHERE `videos`.video_id = ?
          AND `videos_content`.language_id = ?
        ';
        
        $r = $this->db->query($sql, array(
            $video_id,
            $language_id
        ));
        
        if (!isset($r[0])) {
            return false;
        }

        $sql = '
          SELECT *
          FROM `media`
          INNER JOIN `media_content`
          ON `media_content`.media_id = `media`.media_id
          WHERE `media`.table_id = ?
          AND `media`.controller = ?
          AND `media_content`.language_id = ?
          ORDER BY `media`.order ASC
        ';
      
        $r[0]['media'] = $this->db->query($sql, array($video_id, 'videos', $language_id));
      
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
        
        $r[0]['docs'] = $this->db->query($sql, array($r[0]['video_id'], CONTROLLER));

        return $r[0];
    }
    
    function add($post)
    {
        $o = $this->db->query('SELECT MAX(`order`) + 1 AS `order` FROM `videos`');

        if($o[0]['order'] == '')
        {
          $order = 1;
        }
        else
        {
          $order = $o[0]['order'];
        }

        $sql = '
        INSERT INTO `videos`
        (
          active,
          `order`
        )
        VALUES
        (
          :active,
          :order
        )';

        $this->db->query($sql, array(
            'active' => 1,
            'order' => $order
        ));

        $id = $this->db->last_insert_id;
        
        $sql = '
        INSERT INTO `videos_content`
        (
          video_id,
          language_id,
          slug,
          title,
          description,
          url,
          sub_active
        )
        VALUES
        (
          :video_id,
          :language_id,
          :slug,
          :title,
          :description,
          :url,
          :sub_active
        )';
        
        $this->db->query($sql, array(
            'video_id' => $id,
            'language_id' => $this->config->item('default_language'),
            'slug' => $this->url->string_to_url($post['video']['slug']),
            'title' => $post['video']['title'],
            'description' => $post['video']['description'],
            'url' => $post['video']['url'],
            'sub_active' => $post['video']['sub_active']
        ));
        
        $languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array(
            $this->config->item('default_language')
        ));
        
        foreach ($languages as $language) {
            $sql = '
            INSERT INTO `videos_content`
            (
              video_id,
              language_id,
              sub_active
            )
            VALUES
            (
              :video_id,
              :language_id,
              :sub_active
            )
            ';
            
            $this->db->query($sql, array(
                'video_id' => $id,
                'language_id' => $language['language_id'],
                'sub_active' => 0
            ));
        }

        return $id;
    }
    
    function edit($post, $id, $language_id)
    {
        $sql = '
          UPDATE `videos`, `videos_content`
          SET
            `videos_content`.title = :title,
            `videos_content`.description = :description,
            `videos_content`.url = :url,
            `videos_content`.sub_active = :sub_active,
            `videos_content`.slug = :slug
          WHERE `videos`.video_id = :video_id
          AND `videos_content`.video_id = :video_id
          AND `videos_content`.language_id = :language_id
        ';
        
        $this->db->query($sql, array(
            'title' => ucfirst($post['video']['title']),
            'description' => $post['video']['description'],
            'url' => $post['video']['url'],
            'sub_active' => $post['video']['sub_active'],
            'slug' => $this->url->string_to_url($post['video']['slug']),
            'video_id' => $id,
            'language_id' => $language_id
        ));
        
        if (isset($post['media']) && $post['media'] && count($post['media']) > 0)
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
    
    function update_overview($post)
    {
        foreach ($post['active'] as $k => $v) {
            $this->db->query('UPDATE `videos` SET `videos`.active = ? WHERE `videos`.video_id = ? LIMIT 1', array(
                $v,
                $k
            ));
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
    
    function delete($id)
    {
        $this->db->query('DELETE FROM `videos` WHERE `videos`.video_id = ?', array(
            $id
        ));
        $this->db->query('DELETE FROM `videos_content` WHERE `videos_content`.video_id = ?', array(
            $id
        ));
        $this->db->query('DELETE FROM `videos_selected` WHERE `videos_selected`.video_id = ?', array(
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
        
        $this->db->query('DELETE FROM `media` WHERE `media`.media_id = ?', array(
            $media_id
        ));
        $this->db->query('DELETE FROM `media_content` WHERE `media_content`.media_id = ?', array(
            $media_id
        ));
    }
    
    function order($direction, $current_order, $video_id)
    {
        switch ($direction) {
            case 'up':
                $from = $this->db->query('SELECT `videos`.order, `videos`.video_id FROM `videos` WHERE `videos`.video_id = ?', array(
                    $video_id
                ));
                $to = $this->db->query('SELECT `videos`.order, `videos`.video_id FROM `videos` WHERE `videos`.order < ? ORDER BY `videos`.order DESC', array(
                    $current_order
                ));
                
                if (!empty($to)) {
                    $this->db->query('UPDATE `videos` SET `videos`.order = ? WHERE `videos`.video_id = ?', array(
                        $to[0]['order'],
                        $from[0]['video_id']
                    ));
                    $this->db->query('UPDATE `videos` SET `videos`.order = ? WHERE `videos`.video_id = ?', array(
                        $from[0]['order'],
                        $to[0]['video_id']
                    ));
                }
                break;
            
            case 'down':
                $from = $this->db->query('SELECT `videos`.order, `videos`.video_id FROM `videos` WHERE `videos`.video_id = ?', array(
                    $video_id
                ));
                $to = $this->db->query('SELECT `videos`.order, `videos`.video_id FROM `videos` WHERE `videos`.order > ? ORDER BY `videos`.order ASC', array(
                    $current_order
                ));
                
                if (!empty($to)) {
                    $this->db->query('UPDATE `videos` SET `videos`.order = ? WHERE `videos`.video_id = ?', array(
                        $to[0]['order'],
                        $from[0]['video_id']
                    ));
                    $this->db->query('UPDATE `videos` SET `videos`.order = ? WHERE `videos`.video_id = ?', array(
                        $from[0]['order'],
                        $to[0]['video_id']
                    ));
                }
                break;
        }
    }
    
    function fetch_videos_selected($page_id)
    {
        $r = $this->db->query('SELECT video_id FROM `videos_selected` WHERE `videos_selected`.page_id = ?', array($page_id));
        if (isset($r) && count($r) > 0) {
            $items = array();
            foreach ($r as $item) {
                $items[] = $item['video_id'];
            }
            return $items;
        }
        return false;
    }
    
    function attach_videos($post, $page_id)
    {
        $this->db->query('DELETE FROM `videos_selected` WHERE `videos_selected`.page_id = ?', array($page_id));

        if (isset($post) && count($post) > 0)
        foreach ($post as $video_id) {
            $sql = '
            INSERT INTO `videos_selected`
            (
              video_id,
              page_id
            )
            VALUES
            (
              :video_id,
              :page_id
            )';
            
            $this->db->query($sql, array(
                'video_id' => $video_id,
                'page_id' => $page_id
            ));
        }
    }
}

?>