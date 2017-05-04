<?php

class stores_model extends model
{
    function fetch_all()
    {
        $sql = '
          SELECT * FROM `stores`
          INNER JOIN `stores_content`
          ON `stores`.store_id = `stores_content`.store_id
          WHERE `stores_content`.language_id = ?
          ORDER BY `stores`.order ASC
        ';
        
        return $this->db->query($sql, array(
            $this->config->item('default_language')
        ));
    }
    
    function fetch($store_id, $language_id)
    {
        $sql = '
          SELECT *
          FROM `stores`
          INNER JOIN `stores_content`
          ON `stores_content`.store_id = `stores`.store_id
          WHERE `stores`.store_id = ?
          AND `stores_content`.language_id = ?
        ';
        
        $r = $this->db->query($sql, array(
            $store_id,
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
      
        $r[0]['media'] = $this->db->query($sql, array($store_id, 'stores', $language_id));
        
        /*
        $sql = '
          SELECT *
          FROM `media`
          INNER JOIN `media_content`
          ON `media_content`.media_id = `media`.media_id
          WHERE `media`.table_id = ?
          AND `media`.controller = ?
          AND `media_content`.language_id = ?
          AND `media`.album_id = ? 
          ORDER BY `media`.order ASC
        ';
      
        $r[0]['media']['logo'] = $this->db->query($sql, array($store_id, 'stores', $language_id, 1));
        */
      
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
        
        $r[0]['docs'] = $this->db->query($sql, array(
            $r[0]['store_id'],
            CONTROLLER
        ));

        return $r[0];
    }
    
    function add($post)
    {
        $o = $this->db->query('SELECT MAX(`order`) + 1 AS `order` FROM `stores`');

        if($o[0]['order'] == '')
        {
          $order = 1;
        }
        else
        {
          $order = $o[0]['order'];
        }

        $sql = '
        INSERT INTO `stores`
        (
          highlight,
          active,
          `order`
        )
        VALUES
        (
          :highlight,
          :active,
          :order
        )';

        $this->db->query($sql, array(
            'highlight' 	=> 0,
            'active' 	=> 1,
            'order' 		=> $order
        ));

        $id = $this->db->last_insert_id;
        
        $sql = '
        INSERT INTO `stores_content`
        (
          store_id,
          language_id,
          slug,
          title,
          address,
          phone,
          description,
          content,
          google_lat,
          google_lng,
          meta_title,
          meta_desc,
          meta_keyw,
          sub_active
        )
        VALUES
        (
          :store_id,
          :language_id,
          :slug,
          :title,
          :address,
          :phone,
          :description,
          :content,
          :google_lat,
          :google_lng,
          :meta_title,
          :meta_desc,
          :meta_keyw,
          :sub_active
        )';
        
        $this->db->query($sql, array(
            'store_id' => $id,
            'language_id' => $this->config->item('default_language'),
            'slug' => $this->url->string_to_url($post['store']['slug']),
            'title' => $post['store']['title'],
            'address' => $post['store']['address'],
            'phone' => $post['store']['phone'],
            'description' => $post['store']['description'],
            'content' => $post['store']['content'],
            'google_lat' => $post['store']['google_lat'],
            'google_lng' => $post['store']['google_lng'],
            'meta_title' => $post['store']['meta_title'],
            'meta_desc' => $post['store']['meta_desc'],
            'meta_keyw' => $post['store']['meta_keyw'],
            'sub_active' => $post['store']['sub_active']
        ));
        
        $languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array(
            $this->config->item('default_language')
        ));
        
        foreach ($languages as $language) {
            $sql = '
            INSERT INTO `stores_content`
            (
              store_id,
              language_id,
              sub_active
            )
            VALUES
            (
              :store_id,
              :language_id,
              :sub_active
            )
            ';
            
            $this->db->query($sql, array(
                'store_id' => $id,
                'language_id' => $language['language_id'],
                'sub_active' => 0
            ));
        }
        
        return $id;
    }
    
    function edit($post, $id, $language_id)
    {
        $sql = 'SELECT `stores_content`.slug FROM `stores_content` WHERE `stores_content`.store_id = ? AND `stores_content`.language_id = ?';
        $content = $this->db->query($sql, array(
            $id,
            $language_id
        ));
        
        $old_slug = $content[0]['slug'];
        
        if ($old_slug != $this->url->string_to_url($post['store']['slug'])) {
            $sql = '
              UPDATE `stores_content`
              SET `stores_content`.slug_301 = :slug_301
              WHERE `stores_content`.store_id = :store_id
              AND `stores_content`.language_id = :language_id
            ';
            
            $this->db->query($sql, array(
                'store_id' => $id,
                'language_id' => $language_id,
                'slug_301' => $old_slug
            ));
        }
        
        $sql = '
          UPDATE `stores`, `stores_content`
          SET
            `stores_content`.title = :title,
            `stores_content`.address = :address,
            `stores_content`.phone = :phone,
            `stores_content`.description = :description,
            `stores_content`.content = :content,
            `stores_content`.google_lat = :google_lat,
            `stores_content`.google_lng = :google_lng,
            `stores_content`.meta_title = :meta_title,
            `stores_content`.meta_desc = :meta_desc,
            `stores_content`.meta_keyw = :meta_keyw,
            `stores_content`.sub_active = :sub_active,
            `stores_content`.slug = :slug
          WHERE `stores`.store_id = :store_id
          AND `stores_content`.store_id = :store_id
          AND `stores_content`.language_id = :language_id
        ';
        
        $this->db->query($sql, array(
            'title' => ucfirst($post['store']['title']),
            'address' => $post['store']['address'],
            'phone' => $post['store']['phone'],
            'description' => $post['store']['description'],
            'content' => $post['store']['content'],
            'google_lat' => $post['store']['google_lat'],
            'google_lng' => $post['store']['google_lng'],
            'meta_title' => $post['store']['meta_title'],
            'meta_desc' => $post['store']['meta_desc'],
            'meta_keyw' => $post['store']['meta_keyw'],
            'sub_active' => $post['store']['sub_active'],
            'slug' => $this->url->string_to_url($post['store']['slug']),
            'store_id' => $id,
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
            $this->db->query('UPDATE `stores` SET `stores`.active = ? WHERE `stores`.store_id = ? LIMIT 1', array(
                $v,
                $k
            ));
        }
        foreach ($post['highlight'] as $k => $v) {
            $this->db->query('UPDATE `stores` SET `stores`.highlight = ? WHERE `stores`.store_id = ? LIMIT 1', array(
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
        $sql = 'SELECT * FROM `stores` WHERE `stores`.store_id = ?';
        $r = $this->db->query($sql, array(
            $id
        ));
        
        $this->db->query('DELETE FROM `stores` WHERE `stores`.store_id = ?', array(
            $id
        ));
        $this->db->query('DELETE FROM `stores_content` WHERE `stores_content`.store_id = ?', array(
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
            'message' => $message
        );
        
        $this->facebook->api('/me/feed', 'POST', $attachment);
    }
    

    function order($direction, $current_order, $store_id)
    {
        switch ($direction) {
            case 'up':
                $from = $this->db->query('SELECT `stores`.order, `stores`.store_id FROM `stores` WHERE `stores`.store_id = ?', array(
                    $store_id
                ));
                $to = $this->db->query('SELECT `stores`.order, `stores`.store_id FROM `stores` WHERE `stores`.order < ? ORDER BY `stores`.order DESC', array(
                    $current_order
                ));
                
                if (!empty($to)) {
                    $this->db->query('UPDATE `stores` SET `stores`.order = ? WHERE `stores`.store_id = ?', array(
                        $to[0]['order'],
                        $from[0]['store_id']
                    ));
                    $this->db->query('UPDATE `stores` SET `stores`.order = ? WHERE `stores`.store_id = ?', array(
                        $from[0]['order'],
                        $to[0]['store_id']
                    ));
                }
                break;
            
            case 'down':
                $from = $this->db->query('SELECT `stores`.order, `stores`.store_id FROM `stores` WHERE `stores`.store_id = ?', array(
                    $store_id
                ));
                $to = $this->db->query('SELECT `stores`.order, `stores`.store_id FROM `stores` WHERE `stores`.order > ? ORDER BY `stores`.order ASC', array(
                    $current_order
                ));
                
                if (!empty($to)) {
                    $this->db->query('UPDATE `stores` SET `stores`.order = ? WHERE `stores`.store_id = ?', array(
                        $to[0]['order'],
                        $from[0]['store_id']
                    ));
                    $this->db->query('UPDATE `stores` SET `stores`.order = ? WHERE `stores`.store_id = ?', array(
                        $from[0]['order'],
                        $to[0]['store_id']
                    ));
                }
                break;
        }
    }
    
    function attach_categories($post, $id, $controller)
    {
        
        $this->db->query('DELETE FROM `category_selected` WHERE `category_selected`.table_id = ? AND controller = ?', array(
            $id,
            $controller
        ));
        if (isset($post) && count($post) > 0)
            foreach ($post as $category_id) {
                $sql = '
                INSERT INTO `category_selected`
                (
                  table_id,
                  controller,
                  category_id
                )
                VALUES
                (
                  :table_id,
                  :controller,
                  :category_id
                )';
                
                $this->db->query($sql, array(
                    'table_id' => $id,
                    'controller' => $controller,
                    'category_id' => $category_id
                ));
            }
        
    }
    
    function delete_categories($id, $controller)
    {
        $this->db->query('DELETE FROM `category_selected` WHERE `category_selected`.table_id = ? AND controller = ?', array(
            $id,
            $controller
        ));
    }
    
}

?>