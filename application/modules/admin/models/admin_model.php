<?php

class admin_model extends model
{
    /* 
    function add_language($lang) OLD?
    {
    $sql = '
    SELECT * 
    FROM page
    LEFT JOIN page_prop
    ON page.id = `page_prop`.page_id
    WHERE page_prop.lang = ?
    ';
    
    $r = $this->db->query($sql, array('nl'));
    } */
    
    function links_crawl($old, $new)
    {
        $tables = $this->db->dbh->query('show tables from ' . $this->config->item('database', 'default_site') . ' like "%content%"');
        $row = $tables->fetchAll(PDO::FETCH_ASSOC);
        
        $content_tables = array();
        
        foreach($row as $key => $value) {
            foreach($value as $k => $v) {
                $content_tables[] = $v;
            }
        }
        
        //die(debug($content_tables));
        
        foreach($content_tables as $key => $value) {
            $con = $this->db->query('SELECT * FROM `' . $value . '`');
            
            $select_field = '';
            
            foreach($con as $k => $v) {
                if(isset($v['content_text'])) {
                    $select_field = 'content_text';
                } elseif(isset($v['content'])) {
                    $select_field = 'content';
                }
            }
            if($select_field != '') {
                $all_content = $this->db->query('SELECT * FROM `' . $value . '` WHERE `' . $value . '`.' . $select_field . ' LIKE \'%' . $old . '%\'');
                $table = $value;
                
                foreach($all_content as $k => $v) {
                    $key = key($all_content[$k]);
                    $id = $v[$key];
                    
                    if(isset($v['content_text'])) {
                        $field = 'content_text';
                        $content_search = $v['content_text'];
                    } elseif(isset($v['content'])) {
                        $field = 'content';
                        $content_search = $v['content'];
                    }
                    
                    if($content_search != '') {
                        $new_content = str_replace($old, $new, $content_search);
                        $sql = 'UPDATE `' . $table . '` SET `' . $table . '`.' . $field . ' = "' . addslashes($new_content) . '" WHERE `' . $table . '`.' . $key . ' = ' . $id . '';
                        $this->db->query($sql);
                    }
                }
            }
        }
    }
    
    function fetch_languages()
    {
        return $this->db->query('SELECT * FROM language');
    }
    
    function fetch_email($username)
    {
        return $this->db->query('SELECT `user`.email FROM `user` WHERE `user`.username = ? LIMIT 1', array(
            $username
        ));
    }
    
    function set_forgot_salt($username, $email)
    {
        $forgot_salt = sha1($email . FORGOT_SALT);
        
        $sql = '
        UPDATE `user`
        SET
          `user`.forgot_salt = :forgot_salt
        WHERE `user`.email = :email';
        
        $this->db->query($sql, array(
            'forgot_salt' => $forgot_salt,
            'email' => $email
        ));
        
        return $forgot_salt;
    }
    
    function fetch_forgot_salt($salt)
    {
        $this->db->query('SELECT `user`.forgot_salt FROM `user` WHERE `user`.forgot_salt = ?', array(
            $salt
        ));
        
        if($this->db->num_rows == 0) {
            return false;
        }
        
        return true;
    }
    
    function fetch_modules()
    {
        $sql = 'SELECT * FROM `module`';
        return $this->db->query($sql);
    }
    
    function update_password($post, $salt)
    {
        $this->db->query('UPDATE `user` SET `user`.password = ? WHERE `user`.forgot_salt = ?', array(
            sha1($post['password']),
            $salt
        ));
    }
    
    function update_overview($post)
    {
        foreach($post['permission'] as $k => $v) {
            $rights_id = $k;
            foreach($v as $module_id => $permission) {
                $sql = 'UPDATE `rights_module` SET `rights_module`.permission = :permission WHERE `rights_module`.rights_id = :rights_id AND `rights_module`.module_id = :module_id';
                $this->db->query($sql, array(
                    ':permission' => $permission,
                    ':rights_id' => $rights_id,
                    ':module_id' => $module_id
                ));
            }
        }
        
        foreach($post['handle_permissions'] as $k => $v) {
            $rights_id = $k;
            foreach($v as $module_id => $permissions) {
                foreach($permissions as $k => $v) {
                    if($v == 'on') {
                        $post['handle_permissions'][$rights_id][$module_id][$k] = 1;
                    } elseif($v == 'off') {
                        $post['handle_permissions'][$rights_id][$module_id][$k] = 0;
                    }
                }
            }
        }
        
        foreach($post['handle_permissions'] as $k => $v) {
            $rights_id = $k;
            foreach($v as $module_id => $permissions) {
                $sql = 'SELECT * FROM `rights_module`, `permission` 
                WHERE `rights_module`.rights_id = :rights_id 
                AND `rights_module`.module_id = :module_id 
                AND `rights_module`.rights_module_id = `permission`.rights_module_id
                LIMIT 1';
                
                $data = $this->db->query($sql, array(
                    'rights_id' => $rights_id,
                    'module_id' => $module_id
                ));
                
                $permission_id = $data[0]['permission_id'];
                
                $sql = '
                UPDATE `permission`
                SET
                `permission`.add = :add,
                `permission`.edit = :edit,
                `permission`.delete = :delete
                WHERE `permission`.permission_id = :permission_id
                ';

                $this->db->query($sql, array(
                    'add' => $permissions['add'],
                    'edit' => $permissions['edit'],
                    'delete' => $permissions['delete'],
                    'permission_id' => $permission_id
                ));
            }
        }
    }
    
    function module_active($module)
    {
        $modules = $this->fetch_modules();
        
        foreach($modules as $mod) {
            if($mod['dirname'] == $module) {
                
                $sql = 'SELECT `module`.dirname, `module`.active FROM `module` WHERE `module`.dirname = ? AND `module`.active = 1';
                $this->db->query($sql, array(
                    $module
                ));
                
                if($this->db->num_rows > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        return true;
    }
    
    function check_permission_overall($module)
    {
        if($this->url->segment(1) != '') {
            $sql = '
            SELECT `module`.dirname
            FROM `module`
            WHERE `module`.dirname = :dirname
            ';
            
            $this->db->query($sql, array(
                'dirname' => $module
            ));
            
            if($this->db->num_rows > 0) {
                $sql = '
                SELECT * FROM
                `module`, `user`, `rights_module`
                WHERE
                  `module`.dirname = :module
                AND
                  `rights_module`.rights_id = `user`.rights_id
                AND
                  `rights_module`.module_id = `module`.module_id
                AND
                  `rights_module`.permission = 1
                AND
                  `user`.user_id = :user_id
                AND `module`.active = 1
                ';
                
                $this->db->query($sql, array(
                    'module' => $module,
                    'user_id' => $_SESSION['user_id']
                ));
                
                if($this->db->num_rows > 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        } else {
            return true;
        }
        
    }
    
    function check_permission($method)
    {
        $methods = array(
            'add',
            'edit',
            'delete'
        );
        
        if(in_array($method, $methods)) {
            $sql = '
            SELECT
              `user`.rights_id,
              `rights`.rights_id,
              `module`.module_id,
              `rights_module`.rights_id,
              `rights_module`.rights_module_id,
              `rights_module`.module_id,
              `permission`.rights_module_id,
              `permission`.' . $method . '
            FROM `user`
            INNER JOIN `rights`
              ON `rights`.rights_id = `user`.rights_id
              INNER JOIN `rights_module`
              ON `rights_module`.rights_id = `rights`.rights_id
              INNER JOIN `module`
              ON `module`.module_id = `rights_module`.module_id
              INNER JOIN `permission`
              ON `permission`.rights_module_id = `rights_module`.rights_module_id
            WHERE `user`.user_id = ?
            AND `user`.login_salt = ?
            AND `permission`.' . $method . ' = 1
            AND `module`.dirname = ?
            AND `rights_module`.module_id = `module`.module_id
            ';
            
            $this->db->query($sql, array(
                $_SESSION['user_id'],
                $_SESSION['login_salt'],
                CONTROLLER
            ));
            
            if($this->db->num_rows > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
        
    }
    
    function setTwitter()
    {
        $settings = $this->db->query('SELECT * FROM `settings`');
        $settings = $settings[0];
        
        /*
        $this->twitter->setOAuthToken($settings['setOAuthToken']);
        $this->twitter->setOAuthTokenSecret($settings['setOAuthTokenSecret']);
        $this->twitter->setConsumerKey($settings['setConsumerKey']);
        $this->twitter->setConsumerSecret($settings['setConsumerSecret']);
        */
    }
    
    function setFacebook()
    {
        $settings = $this->db->query('SELECT * FROM `settings`');
        $settings = $settings[0];
        /*
        $this->facebook->page_id = $settings['page_id'];
        $this->facebook->appId = $settings['appId'];
        $this->facebook->secret = $settings['secret'];
        $this->facebook->url = $settings['url'];
        $this->facebook->accestoken_db = $settings['accestoken_db'];
        */
    }

    function setGoogleURL()
    {
        $settings = $this->db->query('SELECT * FROM `settings`');
        $settings = $settings[0];
        /*
        $this->googleurl->apikey = $settings['google_api_key'];
        */
    }
    
    function add_doc($doc, $table_id)
    {
        $sql = 'SELECT MAX(`media`.order) AS `order` FROM `media` WHERE `media`.table_id = ?';
        
        $order = $this->db->query($sql, array(
            $table_id
        ));
        
        $sql = '
          INSERT INTO `media`
          (
            `media`.table_id,
            `media`.filename,
            `media`.order,
            `media`.controller,
            `media`.media_type_id
          )
          VALUES
          (
            :table_id,
            :filename,
            :order,
            :controller,
            :media_type_id
          )
        ';
        
        $this->db->query($sql, array(
            'table_id' => $table_id,
            'filename' => $doc['name'],
            'order' => $order[0]['order'] + 1,
            'controller' => CONTROLLER,
            'media_type_id' => 2
        ));
    }
    
    function getFacebookAccesToken()
    {
        $page_id = $this->facebook->page_id;
        $appId = $this->facebook->appId;
        $secret = $this->facebook->secret;
        $url = $this->facebook->url;
        
        if(@$_GET['code'] == '') {
            // om de Code te krijgen 
            header('Location: https://www.facebook.com/dialog/oauth?client_id=' . $appId . '&redirect_uri=' . $url . '&scope=manage_pages,publish_stream');
        } else {
            $code = $_GET['code'];
            //$code = 'AQD8U7CC7tkMOVmMPcUuePp-1Jy-gVLxwjNawD__QmxGQOIHmE0Cz-z5XtE6l32jOHfNXWe1hW1hJ7kvqhvr6jSwXKS0lhFJffKwO9zYS0PA-_DckobzcA7DXyZrT45nD25I__fF_HyRMSlrbY1abth11yznZNA5bjBMaxossJk1g1MgvQLMmIIKzHNXrxeK02s';
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/oauth/access_token?client_id=' . $appId . '&code=' . $code . '&client_secret=' . $secret . '&redirect_uri=' . $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            parse_str(curl_exec($ch), $return);
            
            $accestoken = $return['access_token'];
            $attachment1 = array(
                'access_token' => $accestoken
            );
            
            //$page print r voor alle pagina's van de gebruiker
            $page = $this->facebook->api('/me/accounts', 'get', $attachment1);
            
            echo '<pre>';
            print_r($page);
            echo '</pre>';
            
            // Accesstoken van de admin opzoeken
            $accestoken2 = $page['data'][0]['access_token'];
            
            //access token van de page waar je wilt posten
            //$accestoken2 = 'AAAFkrjZACZBuABAP21s6hxDZC4HilIInlEzF3MQwzrIAFrvckJ1AZA5LrIZAyvZBx34X1sSZAg983cuhNPyzyTkYXeh5XFvPjUYCzo8fhKvPGxjJCkceAt3';
            
            //$attachment = array(
            //	'access_token' => $accestoken2,
            //	'message'=> "Test 3"
            //);
            
            //$this->facebook->api('/me/feed','POST', $attachment);
        }
        
    }
    
    function fetch_media_ajax($media_id, $language_id)
    {
        $sql = '
          SELECT * FROM `media`
          INNER JOIN `media_content`
            ON `media_content`.media_id = `media`.media_id
          WHERE `media`.media_id = :media_id
            AND `media_content`.language_id = :language_id
        ';

        $data = $this->db->query($sql, array(
            'media_id' => $media_id,
            'language_id' => $language_id
        ));
        
        if(empty($data)) {
            return false;
        }
        
        return $data[0];
    }
    
    function save_media_info($post)
    {
        $sql = '
          UPDATE `media`, `media_content`
          SET
            `media_content`.title 		= :title,
            `media_content`.alt 		= :alt,
            `media_content`.description = :description,
            `media_content`.content 	= :content
          WHERE `media`.media_id = :media_id
          AND `media_content`.media_id = :media_id
          AND `media_content`.language_id = :language_id
        ';
        
        $this->db->query($sql, array(
            'media_id' => $post['media_id'],
            'title' => $post['title'],
            'alt' => $post['alt'],
            'description' => $post['description'],
            'content' => $post['content'],
            'language_id' => $post['language_id']
        ));
    }
    
    function fetch_formular_item($formular_id, $language_id, $formular_item_id = 0)
    {
        if($formular_item_id != 0) {
            $sql = '
              SELECT * FROM `formular_item`, `formular_item_content`
              WHERE `formular_item`.formular_id 					= :formular_id
                AND `formular_item_content`.formular_item_id	= `formular_item`.formular_item_id
                AND `formular_item_content`.language_id			= :language_id
                AND `formular_item`.formular_item_id 			= :formular_item_id
            ';
            
            $data = $this->db->query($sql, array(
                'formular_id' => $formular_id,
                'formular_item_id' => $formular_item_id,
                'language_id' => $language_id
            ));
            
            $sql = '
              SELECT * FROM `formular_subitem`, `formular_subitem_content`
              WHERE `formular_subitem_content`.formular_subitem_id	= `formular_subitem`.formular_subitem_id
                AND `formular_subitem_content`.language_id			= :language_id
                AND `formular_subitem`.formular_item_id 			= :formular_item_id
                ORDER BY `formular_subitem`.formular_subitem_id 
            ';
            
            $data[0]['options'] = $this->db->query($sql, array(
                'formular_item_id' => $formular_item_id,
                'language_id' => $language_id
            ));
            
            if($language_id != $this->config->item('default_language')) {
                $sql = '
                SELECT * FROM `formular_subitem`, `formular_subitem_content`
                WHERE `formular_subitem_content`.formular_subitem_id	= `formular_subitem`.formular_subitem_id
                  AND `formular_subitem_content`.language_id			= :language_id
                  AND `formular_subitem`.formular_item_id 			= :formular_item_id
                  ORDER BY `formular_subitem`.formular_subitem_id 
                ';
                
                $data[0]['default_options'] = $this->db->query($sql, array(
                    'formular_item_id' => $formular_item_id,
                    'language_id' => $this->config->item('default_language')
                ));
            }
        }
        
        if(empty($data)) {
            $data['formular_item_id'] = $formular_item_id;
            $data['formular_id'] = $formular_id;
            $data['language_id'] = $language_id;
            $data['type'] = 1;
            return $data;
        }
        $data[0]['formular_item_id'] = $formular_item_id;
        $data[0]['formular_id'] = $formular_id;
        $data[0]['language_id'] = $language_id;
        return $data[0];
    }
    
    function save_item_info($post)
    {
        if($post['formular_item_id'] == 0) {
            // add
            $sql = 'SELECT MAX(`formular_item`.order) AS `order` FROM `formular_item`';

            $order = $this->db->query($sql);
            if(!$order[0]['order'])
                $order[0]['order'] = 0;
            
            $sql = '
            INSERT INTO `formular_item`
            (
              `formular_item`.formular_id,
              `formular_item`.order,
              `formular_item`.type
            )
            VALUES
            (
              :formular_id,
              :order,
              :type
            )
            ';
            
            $this->db->query($sql, array(
                'formular_id' => $post['formular_id'],
                'order' => $order[0]['order'] + 1,
                'type' => $post['type']
            ));
            
            $formular_item_id = $this->db->last_insert_id;
            
            $sql = '
              INSERT INTO `formular_item_content`
              (
                `formular_item_content`.sub_active,
                `formular_item_content`.formular_item_id,
                `formular_item_content`.language_id,
                `formular_item_content`.title
              )
              VALUES
              (
                :sub_active,
                :formular_item_id,
                :language_id,
                :title
              )
            ';
            
            $this->db->query($sql, array(
                'sub_active' => $post['sub_active'],
                'formular_item_id' => $formular_item_id,
                'language_id' => $post['language_id'],
                'title' => ucfirst($post['title'])
            ));
            
            $languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array(
                $this->config->item('default_language')
            ));
            
            foreach($languages as $language) {
                $sql = '
                INSERT INTO `formular_item_content`
                (
                  formular_item_id,
                  language_id
                )
                VALUES
                (
                  :product_options_id,
                  :language_id
                )';
                
                $this->db->query($sql, array(
                    'product_options_id' => $formular_item_id,
                    'language_id' => $language['language_id']
                ));
            }
            
            foreach($post['options'] as $k => $option) {
                if($option != '') {

                    $sql = '
                      INSERT INTO `formular_subitem`
                      (
                        `formular_subitem`.formular_item_id
                      )
                      VALUES
                      (
                        :formular_item_id
                      )
                    ';
                    
                    $this->db->query($sql, array(
                        'formular_item_id' => $formular_item_id
                    ));
                    
                    $formular_subitem_id = $this->db->last_insert_id;
                    
                    $sql = '
                      INSERT INTO `formular_subitem_content`
                      (
                        `formular_subitem_content`.language_id,
                        `formular_subitem_content`.formular_subitem_id,
                        `formular_subitem_content`.title
                      )
                      VALUES
                      (
                        :language_id,
                        :formular_subitem_id,
                        :title
                      )
                    ';
                    
                    $this->db->query($sql, array(
                        'language_id' => $post['language_id'],
                        'formular_subitem_id' => $formular_subitem_id,
                        'title' => ucfirst($option)
                    ));
                    
                    $languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array(
                        $this->config->item('default_language')
                    ));
                    
                    foreach($languages as $language) {
                        $sql = '
                          INSERT INTO `formular_subitem_content`
                          (
                            `formular_subitem_content`.formular_subitem_id,
                            `formular_subitem_content`.language_id
                          )
                          VALUES
                          (
                            :formular_subitem_id,
                            :language_id
                          )
                        ';
                        
                        $this->db->query($sql, array(
                            'formular_subitem_id' => $formular_subitem_id,
                            'language_id' => $language['language_id']
                        ));
                    }
                }
            }
        } else {
            // edit
            $formular_item_id = $post['formular_item_id'];
            $language_id = $post['language_id'];
            $formular_id = $post['formular_id'];
            
            $sql = '
              UPDATE `formular_item`, `formular_item_content`
                SET
                `formular_item`.type					= :type,
                `formular_item_content`.sub_active 		= :sub_active,
                `formular_item_content`.title 			= :title
                WHERE `formular_item`.formular_item_id 	= :formular_item_id
                AND `formular_item`.formular_item_id 	= `formular_item_content`.formular_item_id
                AND `formular_item_content`.language_id = :language_id
              ';
            
            $this->db->query($sql, array(
                'type' => $post['type'],
                'sub_active' => $post['sub_active'],
                'title' => ucfirst($post['title']),
                'formular_item_id' => $post['formular_item_id'],
                'language_id' => $post['language_id']
            ));
            echo $this->db->error;
            $arr_ids = '';
            
            // edit old options
            foreach($post['edit_options'] as $formular_subitem_id => $option) {
                $sql = '
                  SELECT * FROM `formular_subitem`, `formular_subitem_content`
                  WHERE `formular_subitem_content`.formular_subitem_id	= `formular_subitem`.formular_subitem_id
                    AND `formular_subitem_content`.language_id			= :language_id
                    AND `formular_subitem`.formular_subitem_id 			= :formular_subitem_id
                ';
                
                $current = $this->db->query($sql, array(
                    'formular_subitem_id' => $formular_subitem_id,
                    'language_id' => $language_id
                ));
                echo $this->db->error;
                
                if($current && count($current) == 1) {
                    $sql = '
                    UPDATE `formular_subitem_content`, `formular_subitem`
                    SET `formular_subitem_content`.title 					= :title
                    WHERE `formular_subitem_content`.formular_subitem_id 	= `formular_subitem`.formular_subitem_id
                    AND `formular_subitem`.formular_subitem_id 				= :formular_subitem_id
                    AND `formular_subitem_content`.language_id 				= :language_id
                    ';
                    
                    $this->db->query($sql, array(
                        'title' => ucfirst($option),
                        'formular_subitem_id' => $formular_subitem_id,
                        'language_id' => $language_id
                    ));
                    echo $this->db->error;
                    
                    $arr_ids .= " AND `formular_subitem`.formular_subitem_id <> " . $formular_subitem_id;
                    
                } else {
                    $sql = '
                      INSERT INTO `formular_subitem`
                      (
                        `formular_subitem`.formular_item_id
                      )
                      VALUES
                      (
                        :formular_item_id
                      )
                    ';
                    
                    $this->db->query($sql, array(
                        'formular_item_id' => $formular_item_id
                    ));
                    echo $this->db->error;
                    
                    $formular_subitem_id = $this->db->last_insert_id;
                    
                    $sql = '
                    INSERT INTO `formular_subitem_content`
                    (
                      `formular_subitem_content`.language_id,
                      `formular_subitem_content`.formular_subitem_id,
                      `formular_subitem_content`.title
                    )
                    VALUES
                    (
                      :language_id,
                      :formular_subitem_id,
                      :title
                    )
                  ';
                    
                    $this->db->query($sql, array(
                        'language_id' => $post['language_id'],
                        'formular_subitem_id' => $formular_subitem_id,
                        'title' => ucfirst($option)
                    ));
                    echo $this->db->error;
                    
                    $languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array(
                        $this->config->item('default_language')
                    ));
                    
                    foreach($languages as $language) {
                        $sql = '
                        INSERT INTO `formular_subitem_content`
                        (
                          `formular_subitem_content`.formular_subitem_id,
                          `formular_subitem_content`.language_id
                        )
                        VALUES
                        (
                          :formular_subitem_id,
                          :language_id
                        )
                      ';
                        
                        $this->db->query($sql, array(
                            'formular_subitem_id' => $formular_subitem_id,
                            'language_id' => $language['language_id']
                        ));
                    }
                }
            }
            
            $sql = 'SELECT * FROM `formular_subitem`, `formular_item`
                    WHERE `formular_subitem`.formular_item_id = `formular_item`.formular_item_id
                    AND `formular_item`.formular_item_id = ? ' . $arr_ids;
            $to_delete = $this->db->query($sql, array(
                $formular_item_id
            ));
            
            if(isset($to_delete) && count($to_delete) > 0) {
                foreach($to_delete as $item) {
                    
                    $sql = 'DELETE FROM `formular_subitem_content` WHERE `formular_subitem_content`.formular_subitem_id = ? ';
                    $this->db->query($sql, array(
                        $item['formular_subitem_id']
                    ));
                    
                    $sql = 'DELETE FROM `formular_subitem` WHERE `formular_subitem`.formular_subitem_id = ? ';
                    $this->db->query($sql, array(
                        $item['formular_subitem_id']
                    ));
                }
            }

            // add new options
            foreach($post['options'] as $k => $option) {
                if($option != '') {
                    
                    $sql = '
                      INSERT INTO `formular_subitem`
                      (
                        `formular_subitem`.formular_item_id
                      )
                      VALUES
                      (
                        :formular_item_id
                      )
                    ';
                    
                    $this->db->query($sql, array(
                        'formular_item_id' => $formular_item_id
                    ));
                    
                    $formular_subitem_id = $this->db->last_insert_id;
                    
                    $sql = '
                      INSERT INTO `formular_subitem_content`
                      (
                        `formular_subitem_content`.language_id,
                        `formular_subitem_content`.formular_subitem_id,
                        `formular_subitem_content`.title
                      )
                      VALUES
                      (
                        :language_id,
                        :formular_subitem_id,
                        :title
                      )
                    ';
                    
                    $this->db->query($sql, array(
                        'language_id' => $post['language_id'],
                        'formular_subitem_id' => $formular_subitem_id,
                        'title' => ucfirst($option)
                    ));
                    
                    $languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array(
                        $this->config->item('default_language')
                    ));
                    
                    foreach($languages as $language) {
                        $sql = '
                          INSERT INTO `formular_subitem_content`
                          (
                            `formular_subitem_content`.formular_subitem_id,
                            `formular_subitem_content`.language_id
                          )
                          VALUES
                          (
                            :formular_subitem_id,
                            :language_id
                          )
                        ';
                        
                        $this->db->query($sql, array(
                            'formular_subitem_id' => $formular_subitem_id,
                            'language_id' => $language['language_id']
                        ));
                    }
                }
            }
            
        }
    }
    
}

?>