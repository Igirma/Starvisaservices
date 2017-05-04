<?php

class brands_model extends model
{
    function fetch_all()
    {
        $sql = '
          SELECT * FROM `brands`
          INNER JOIN `brands_content`
          ON `brands`.brand_id = `brands_content`.brand_id
          WHERE `brands_content`.language_id = ?
          ORDER BY `brands`.order ASC
        ';
        
        return $this->db->query($sql, array(
            $this->config->item('default_language')
        ));
    }
    
    function fetch($brand_id, $language_id)
    {
        $sql = '
          SELECT *
          FROM `brands`
          INNER JOIN `brands_content`
          ON `brands_content`.brand_id = `brands`.brand_id
          WHERE `brands`.brand_id = ?
          AND `brands_content`.language_id = ?
        ';
        
        $r = $this->db->query($sql, array(
            $brand_id,
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
          AND `media`.album_id = ? 
          ORDER BY `media`.order ASC
        ';
      
        $r[0]['media']['photos'] = $this->db->query($sql, array($brand_id, 'brands', $language_id, 0));
        
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
      
        $r[0]['media']['logo'] = $this->db->query($sql, array($brand_id, 'brands', $language_id, 1));
      
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
            $r[0]['brand_id'],
            CONTROLLER
        ));

        return $r[0];
    }
    
    function add($post)
    {
        $o = $this->db->query('SELECT MAX(`order`) + 1 AS `order` FROM `brands`');

        if($o[0]['order'] == '')
        {
          $order = 1;
        }
        else
        {
          $order = $o[0]['order'];
        }

        $sql = '
        INSERT INTO `brands`
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
        INSERT INTO `brands_content`
        (
          brand_id,
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
          :brand_id,
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
            'brand_id' => $id,
            'language_id' => $this->config->item('default_language'),
            'slug' => $this->url->string_to_url($post['brand']['slug']),
            'title' => $post['brand']['title'],
            'description' => $post['brand']['description'],
            'content' => $post['brand']['content'],
            'meta_title' => $post['brand']['meta_title'],
            'meta_desc' => $post['brand']['meta_desc'],
            'meta_keyw' => $post['brand']['meta_keyw'],
            'sub_active' => $post['brand']['sub_active']
        ));
        
        $languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array(
            $this->config->item('default_language')
        ));
        
        foreach ($languages as $language) {
            $sql = '
            INSERT INTO `brands_content`
            (
              brand_id,
              language_id,
              sub_active
            )
            VALUES
            (
              :brand_id,
              :language_id,
              :sub_active
            )
            ';
            
            $this->db->query($sql, array(
                'brand_id' => $id,
                'language_id' => $language['language_id'],
                'sub_active' => 0
            ));
        }
        
        return $id;
    }
    
    function edit($post, $id, $language_id)
    {
        $sql = 'SELECT `brands_content`.slug FROM `brands_content` WHERE `brands_content`.brand_id = ? AND `brands_content`.language_id = ?';
        $content = $this->db->query($sql, array(
            $id,
            $language_id
        ));
        
        $old_slug = $content[0]['slug'];
        
        if ($old_slug != $this->url->string_to_url($post['brand']['slug'])) {
            $sql = '
              UPDATE `brands_content`
              SET `brands_content`.slug_301 = :slug_301
              WHERE `brands_content`.brand_id = :brand_id
              AND `brands_content`.language_id = :language_id
            ';
            
            $this->db->query($sql, array(
                'brand_id' => $id,
                'language_id' => $language_id,
                'slug_301' => $old_slug
            ));
        }
        
        $sql = '
          UPDATE `brands`, `brands_content`
          SET
            `brands_content`.title = :title,
            `brands_content`.description = :description,
            `brands_content`.content = :content,
            `brands_content`.meta_title = :meta_title,
            `brands_content`.meta_desc = :meta_desc,
            `brands_content`.meta_keyw = :meta_keyw,
            `brands_content`.sub_active = :sub_active,
            `brands_content`.slug = :slug
          WHERE `brands`.brand_id = :brand_id
          AND `brands_content`.brand_id = :brand_id
          AND `brands_content`.language_id = :language_id
        ';
        
        $this->db->query($sql, array(
            'title' => ucfirst($post['brand']['title']),
            'description' => $post['brand']['description'],
            'content' => $post['brand']['content'],
            'meta_title' => $post['brand']['meta_title'],
            'meta_desc' => $post['brand']['meta_desc'],
            'meta_keyw' => $post['brand']['meta_keyw'],
            'sub_active' => $post['brand']['sub_active'],
            'slug' => $this->url->string_to_url($post['brand']['slug']),
            'brand_id' => $id,
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
            $this->db->query('UPDATE `brands` SET `brands`.active = ? WHERE `brands`.brand_id = ? LIMIT 1', array(
                $v,
                $k
            ));
        }
        foreach ($post['highlight'] as $k => $v) {
            $this->db->query('UPDATE `brands` SET `brands`.highlight = ? WHERE `brands`.brand_id = ? LIMIT 1', array(
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
        $sql = 'SELECT * FROM `brands` WHERE `brands`.brand_id = ?';
        $r = $this->db->query($sql, array(
            $id
        ));
        
        $this->db->query('DELETE FROM `brands` WHERE `brands`.brand_id = ?', array(
            $id
        ));
        $this->db->query('DELETE FROM `brands_content` WHERE `brands_content`.brand_id = ?', array(
            $id
        ));
        $this->db->query('DELETE FROM `brands_selected` WHERE `brands_selected`.brand_id = ?', array(
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
    

    function order($direction, $current_order, $brand_id)
    {
        switch ($direction) {
            case 'up':
                $from = $this->db->query('SELECT `brands`.order, `brands`.brand_id FROM `brands` WHERE `brands`.brand_id = ?', array(
                    $brand_id
                ));
                $to = $this->db->query('SELECT `brands`.order, `brands`.brand_id FROM `brands` WHERE `brands`.order < ? ORDER BY `brands`.order DESC', array(
                    $current_order
                ));
                
                if (!empty($to)) {
                    $this->db->query('UPDATE `brands` SET `brands`.order = ? WHERE `brands`.brand_id = ?', array(
                        $to[0]['order'],
                        $from[0]['brand_id']
                    ));
                    $this->db->query('UPDATE `brands` SET `brands`.order = ? WHERE `brands`.brand_id = ?', array(
                        $from[0]['order'],
                        $to[0]['brand_id']
                    ));
                }
                break;
            
            case 'down':
                $from = $this->db->query('SELECT `brands`.order, `brands`.brand_id FROM `brands` WHERE `brands`.brand_id = ?', array(
                    $brand_id
                ));
                $to = $this->db->query('SELECT `brands`.order, `brands`.brand_id FROM `brands` WHERE `brands`.order > ? ORDER BY `brands`.order ASC', array(
                    $current_order
                ));
                
                if (!empty($to)) {
                    $this->db->query('UPDATE `brands` SET `brands`.order = ? WHERE `brands`.brand_id = ?', array(
                        $to[0]['order'],
                        $from[0]['brand_id']
                    ));
                    $this->db->query('UPDATE `brands` SET `brands`.order = ? WHERE `brands`.brand_id = ?', array(
                        $from[0]['order'],
                        $to[0]['brand_id']
                    ));
                }
                break;
        }
    }
    
    function fetch_brands_selected($table_id, $controller = 'projects')
    {
        $r = $this->db->query('SELECT brand_id FROM `brands_selected` WHERE `brands_selected`.table_id = ? AND `brands_selected`.controller = ?', array($table_id, $controller));
        if (isset($r) && count($r) > 0) {
            $items = array();
            foreach ($r as $item) {
                $items[] = $item['brand_id'];
            }
            return $items;
        }
        return false;
    }

    function attach_filters($post, $id, $controller = 'projects')
    {
        $this->db->query('DELETE FROM `brands_selected` WHERE `brands_selected`.table_id = ? AND `brands_selected`.controller = ?', array($id, $controller));
        if (!isset($post) || count($post) < 1) {
            return false;
        }
        foreach ($post as $brand_id)
        {
            if ((int) $brand_id > 0) {
                $this->db->query('INSERT INTO `brands_selected` (brand_id, controller, table_id) VALUES (?, ?, ?)', array($brand_id, $controller, $id));
            }
        }
    }

    function delete_filters($table_id, $controller = 'projects')
    {
        return $this->db->query('DELETE FROM `brands_selected` WHERE `brands_selected`.table_id = ? AND controller = ?', array($table_id, $controller));
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