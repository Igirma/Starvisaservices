<?php

class courses_model extends model
{
    function fetch_all()
    {
        $sql = '
          SELECT * FROM `courses`
          INNER JOIN `courses_content`
          ON `courses`.course_id = `courses_content`.course_id
          WHERE `courses_content`.language_id = ?
          ORDER BY `courses`.order ASC
        ';
        
        return $this->db->query($sql, array(
            $this->config->item('default_language')
        ));
    }
    
    function fetch_active_courses($language)
    {
        $sql = '
          SELECT * FROM `courses`
          INNER JOIN `courses_content`
          ON `courses`.course_id = `courses_content`.course_id
          WHERE `courses_content`.language_id = ?
          AND `courses`.active = 1
          AND `courses_content`.sub_active = 1
          ORDER BY `courses`.order ASC
        ';

        return $this->db->query($sql, array($language));
    }
    
    function fetch($course_id, $language_id)
    {
        $sql = '
          SELECT *
          FROM `courses`
          INNER JOIN `courses_content`
          ON `courses_content`.course_id = `courses`.course_id
          WHERE `courses`.course_id = ?
          AND `courses_content`.language_id = ?
        ';
        
        $r = $this->db->query($sql, array(
            $course_id,
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
      
        $r[0]['media'] = $this->db->query($sql, array($course_id, 'courses', $language_id));
      
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
        
        $r[0]['docs'] = $this->db->query($sql, array($r[0]['course_id'], CONTROLLER));

        return $r[0];
    }
    
    function add($post)
    {
        $o = $this->db->query('SELECT MAX(`order`) + 1 AS `order` FROM `courses`');

        if($o[0]['order'] == '')
        {
          $order = 1;
        }
        else
        {
          $order = $o[0]['order'];
        }

        $sql = '
        INSERT INTO `courses`
        (
          active,
          `order`,
          gender
        )
        VALUES
        (
          :active,
          :order,
          :gender
        )';

        $this->db->query($sql, array(
          'active' => 1,
          'order' => $order,
          'gender' => ($post['course']['gender'] == 'f' ? 'f' : 'm')
        ));

        $id = $this->db->last_insert_id;
        
        $sql = '
        INSERT INTO `courses_content`
        (
          course_id,
          language_id,
          slug,
          title,
          subtitle,
          description,
          content_left,
          content_right,
          sub_active
        )
        VALUES
        (
          :course_id,
          :language_id,
          :slug,
          :title,
          :subtitle,
          :description,
          :content_left,
          :content_right,
          :sub_active
        )';
        
        $this->db->query($sql, array(
            'course_id' => $id,
            'language_id' => $this->config->item('default_language'),
            'slug' => $this->url->string_to_url($post['course']['slug']),
            'title' => $post['course']['title'],
            'subtitle' => $post['course']['subtitle'],
            'description' => $post['course']['description'],
            'content_left' => $post['course']['content_left'],
            'content_right' => $post['course']['content_right'],
            'sub_active' => $post['course']['sub_active']
        ));
        
        $languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array(
            $this->config->item('default_language')
        ));
        
        foreach ($languages as $language) {
            $sql = '
            INSERT INTO `courses_content`
            (
              course_id,
              language_id,
              sub_active
            )
            VALUES
            (
              :course_id,
              :language_id,
              :sub_active
            )
            ';
            
            $this->db->query($sql, array(
                'course_id' => $id,
                'language_id' => $language['language_id'],
                'sub_active' => 0
            ));
        }

        return $id;
    }
    
    function edit($post, $id, $language_id)
    {
        $sql = '
          UPDATE `courses`, `courses_content`
          SET
            `courses_content`.title = :title,
            `courses_content`.subtitle = :subtitle,
            `courses_content`.description = :description,
            `courses_content`.content_left = :content_left,
            `courses_content`.content_right = :content_right,
            `courses_content`.sub_active = :sub_active,
            `courses_content`.slug = :slug,
            `courses`.gender = :gender
          WHERE `courses`.course_id = :course_id
          AND `courses_content`.course_id = :course_id
          AND `courses_content`.language_id = :language_id
        ';
        
        $this->db->query($sql, array(
            'title' => ucfirst($post['course']['title']),
            'subtitle' => ucfirst($post['course']['subtitle']),
            'description' => $post['course']['description'],
            'content_left' => $post['course']['content_left'],
            'content_right' => $post['course']['content_right'],
            'sub_active' => $post['course']['sub_active'],
            'slug' => $this->url->string_to_url($post['course']['slug']),
            'gender' => ($post['course']['gender'] == 'f' ? 'f' : 'm'),
            'course_id' => $id,
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
            $this->db->query('UPDATE `courses` SET `courses`.active = ? WHERE `courses`.course_id = ? LIMIT 1', array(
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
        $this->db->query('DELETE FROM `courses` WHERE `courses`.course_id = ?', array(
            $id
        ));
        $this->db->query('DELETE FROM `courses_content` WHERE `courses_content`.course_id = ?', array(
            $id
        ));
        $this->db->query('DELETE FROM `courses_selected` WHERE `courses_selected`.course_id = ?', array(
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

        //$dirs = glob(BASE_PATH . MEDIA_DIR . CONTROLLER . '/*', GLOB_ONLYDIR);
        $dirs = glob(MEDIA_DIR . CONTROLLER . '/*', GLOB_ONLYDIR);

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
    
    function order($direction, $current_order, $course_id)
    {
        switch ($direction) {
            case 'up':
                $from = $this->db->query('SELECT `courses`.order, `courses`.course_id FROM `courses` WHERE `courses`.course_id = ?', array(
                    $course_id
                ));
                $to = $this->db->query('SELECT `courses`.order, `courses`.course_id FROM `courses` WHERE `courses`.order < ? ORDER BY `courses`.order DESC', array(
                    $current_order
                ));
                
                if (!empty($to)) {
                    $this->db->query('UPDATE `courses` SET `courses`.order = ? WHERE `courses`.course_id = ?', array(
                        $to[0]['order'],
                        $from[0]['course_id']
                    ));
                    $this->db->query('UPDATE `courses` SET `courses`.order = ? WHERE `courses`.course_id = ?', array(
                        $from[0]['order'],
                        $to[0]['course_id']
                    ));
                }
                break;
            
            case 'down':
                $from = $this->db->query('SELECT `courses`.order, `courses`.course_id FROM `courses` WHERE `courses`.course_id = ?', array(
                    $course_id
                ));
                $to = $this->db->query('SELECT `courses`.order, `courses`.course_id FROM `courses` WHERE `courses`.order > ? ORDER BY `courses`.order ASC', array(
                    $current_order
                ));
                
                if (!empty($to)) {
                    $this->db->query('UPDATE `courses` SET `courses`.order = ? WHERE `courses`.course_id = ?', array(
                        $to[0]['order'],
                        $from[0]['course_id']
                    ));
                    $this->db->query('UPDATE `courses` SET `courses`.order = ? WHERE `courses`.course_id = ?', array(
                        $from[0]['order'],
                        $to[0]['course_id']
                    ));
                }
                break;
        }
    }
}

?>