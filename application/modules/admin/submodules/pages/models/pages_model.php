<?php

class pages_model extends model
{
    function count_children($page_id)
    {
        $sql = 'SELECT `page`.page_id FROM `page` WHERE `page`.parent_id = ?';
        $pages = $this->db->query($sql, array(
            $page_id
        ));
        
        $sub_sub = 0;
        
        foreach($pages as $page) {
            $sql = 'SELECT `page`.page_id FROM `page` WHERE `page`.parent_id = ?';
            $this->db->query($sql, array(
                $page['page_id']
            ));
            if($this->db->num_rows > 0) {
                $sub_sub = $sub_sub + $this->db->num_rows;
            }
        }
        
        $count['sub'] = count($pages);
        $count['sub_sub'] = $sub_sub;
        
        return $count;
    }
    
    function fetch_dash()
    {
        $sql = '
          SELECT * 
          FROM `page`, `page_content` 
          WHERE `page`.page_id = `page_content`.page_id 
          AND `page_content`.language_id = :lang
          AND `page`.last_update = ""			
          ORDER BY `page`.date_created DESC
          LIMIT 5
        ';
        $data['add'] = $this->db->query($sql, array(
            'lang' => $this->config->item('default_language')
        ));
        
        $sql = '
          SELECT * 
          FROM `page`, `page_content` 
          WHERE `page`.page_id = `page_content`.page_id 
          AND `page_content`.language_id = :lang
          AND `page`.last_update != ""
          ORDER BY `page`.last_update DESC
          LIMIT 5
        ';
        $data['edit'] = $this->db->query($sql, array(
            'lang' => $this->config->item('default_language')
        ));
        
        return $data;
    }
    
    function fetch_drop_down($parent_id = 0)
    {
        $return = array();
        
        $sql = '
        SELECT `page`.page_id, `page`.parent_id, `page_content`.menu_title
        FROM `page`, `page_content` 
        WHERE `page`.page_id = `page_content`.page_id 
        AND `page_content`.language_id = :lang 
        AND `page`.parent_id = :parent_id
        ORDER BY `page`.order ASC
        ';
        $data = $this->db->query($sql, array(
            'lang' => $this->config->item('default_language'),
            'parent_id' => $parent_id
        ));
        
        $i = 0;
        
        foreach($data as $page) {
            $return[$i] = $page;
            
            $return[$i]['children'] = $this->fetch_drop_down($page['page_id']);
            
            $i++;
        }
        return $return;
    }
    
    function fetch_all($parent_id = 0)
    {
        $return = array();
        
        $sql = '
        SELECT * 
        FROM `page`, `page_content` 
        WHERE `page`.page_id = `page_content`.page_id 
        AND `page_content`.language_id = :lang 
        AND `page`.parent_id = :parent_id
        ORDER BY `page`.order ASC
        ';
        $data = $this->db->query($sql, array(
            'lang' => $this->config->item('default_language'),
            'parent_id' => $parent_id
        ));
        
        $i = 0;
        
        foreach($data as $page) {
            $return[$i] = $page;
            
            $return[$i]['children'] = $this->fetch_all($page['page_id']);
            
            $i++;
        }
        return $return;
    }
    
    function fetch($page_id, $language_id)
    {
        $language = $language_id;
        $r = $this->db->query('SELECT * FROM `page` WHERE `page`.page_id = ?', array($page_id));
        if (!isset($r[0])) {
            return false;
        }

        $data['parent_id'] = $r[0]['parent_id'];
        $data['external'] = $r[0]['external'];
        $data['deletable'] = $r[0]['deletable'];
        $data['controller'] = $r[0]['controller'];

        $r = $this->db->query('SELECT * FROM `mobile_content` WHERE `mobile_content`.page_id = ? AND `mobile_content`.language_id = ?', array($page_id, $language));
        $data['form']['mobile_content'] = isset($r[0]) ? $r[0] : array();
        
        $r = $this->db->query('SELECT * FROM `page_content` WHERE `page_content`.page_id = ? AND `page_content`.language_id = ?', array($page_id, $language));
        $data['form']['page_content'] = $r[0];

        $sql = 'SELECT * FROM `language` WHERE `language`.language_id = ?';
        $data['language'] = $this->db->query($sql, array($language));

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
        $data['media']['photos'] = $this->db->query($sql, array($data['form']['page_content']['page_id'], CONTROLLER, $language, 0));

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
        $data['media']['home'] = $this->db->query($sql, array($data['form']['page_content']['page_id'], CONTROLLER, $language, 1));
        
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
        $data['media']['slide'] = $this->db->query($sql, array($data['form']['page_content']['page_id'], CONTROLLER, $language, 2));

        $sql = '
          SELECT * FROM `media`
          INNER JOIN `media_type`
            ON `media_type`.media_type_id = `media`.media_type_id
          WHERE `media`.table_id = ?
            AND `media_type`.name = "doc"
            AND `media`.controller = ?
          ORDER BY `media`.filename ASC
        ';
        $data['docs'] = $this->db->query($sql, array($data['form']['page_content']['page_id'], CONTROLLER));

        return $data;
    }
    
    function add($post)
    {
        $order = $this->db->query('SELECT MAX(`order`) + 1 AS `order` FROM `page` WHERE `page`.parent_id = ?', array(
            $post['parent_id']
        ));
        
        if($order[0]['order'] == '') {
            $order = 0;
        } else {
            $order = $order[0]['order'];
        }
        
        $this->db->query('INSERT INTO page(`parent_id`, `external`, `order`, `date_created`, `edit_by`, `controller`, `deletable`) VALUES(?, ?, ?, ?, ?, ?, ?)', array(
            $post['parent_id'],
            $post['external'],
            $order,
            time(),
            $_SESSION['username'],
            (isset($post['controller']) && strlen($post['controller']) > 0 ? $post['controller'] : 'pages'),
            (isset($post['deletable']) && $post['deletable'] == 0 ? 0 : 1)
        ));
        
        $id = $this->db->last_insert_id;
        
        foreach($post['form'] as $k => $v) {
        
          if ($k == 'page_content') {
            $sql = '
            INSERT INTO ' . $k . ' 
            (
              page_id,
              language_id,
              slug,
              meta_title,
              meta_desc,
              meta_keyw,
              menu_title,
              content_title,
              content_description,
              content_text,
              content_column_left,
              content_column_right,
              overview_title,
              overview_description,
              overview_text,
              video_url_1,
              video_text_1,
              video_url_2,
              video_text_2,
              video_url_3,
              video_text_3,
              video_url_4,
              video_text_4,
              video_url_5,
              video_text_5,
              video_url_6,
              video_text_6,
              title_1,
              subtitle_1,
              title_2,
              subtitle_2,
              title_3,
              subtitle_3,
              title_4,
              subtitle_4,
              title_5,
              subtitle_5,
              title_6,
              subtitle_6,
              footer_title_1,
              footer_title_2,
              footer_text_1,
              footer_text_2,
              content_status_submitted,
              content_status_received,
              content_status_processing,
              content_status_completed,
              content_status_closed,
              ex_name,
              ex_url,
              sub_active
            )
            VALUES
            (
              :page_id,
              :language_id,
              :slug,
              :meta_title,
              :meta_desc,
              :meta_keyw,
              :menu_title,
              :content_title,
              :content_description,
              :content_text,
              :content_column_left,
              :content_column_right,
              :overview_title,
              :overview_description,
              :overview_text,
              :video_url_1,
              :video_text_1,
              :video_url_2,
              :video_text_2,
              :video_url_3,
              :video_text_3,
              :video_url_4,
              :video_text_4,
              :video_url_5,
              :video_text_5,
              :video_url_6,
              :video_text_6,
              :title_1,
              :subtitle_1,
              :title_2,
              :subtitle_2,
              :title_3,
              :subtitle_3,
              :title_4,
              :subtitle_4,
              :title_5,
              :subtitle_5,
              :title_6,
              :subtitle_6,
              :footer_title_1,
              :footer_title_2,
              :footer_text_1,
              :footer_text_2,
              :content_status_submitted,
              :content_status_received,
              :content_status_processing,
              :content_status_completed,
              :content_status_closed,
              :ex_name,
              :ex_url,
              :sub_active
            )';
            $this->db->query($sql, array(
                'page_id' => $id,
                'language_id' => $this->config->item('default_language'),
                'slug' => $this->url->string_to_url($post['form'][$k]['slug']),
                'meta_title' => $post['form'][$k]['meta_title'],
                'meta_desc' => $post['form'][$k]['meta_desc'],
                'meta_keyw' => $post['form'][$k]['meta_keyw'],
                'menu_title' => ucfirst($post['form'][$k]['menu_title']),
                'content_title' => ucfirst($post['form'][$k]['content_title']),
                'content_description' => $post['form'][$k]['content_description'],
                'content_text' => $post['form'][$k]['content_text'],
                'content_column_left' => $post['form'][$k]['content_column_left'],
                'content_column_right' => $post['form'][$k]['content_column_right'],
                'overview_title' => ucfirst($post['form'][$k]['overview_title']),
                'overview_description' => $post['form'][$k]['overview_description'],
                'overview_text' => $post['form'][$k]['overview_text'],
                'video_url_1' => $post['form'][$k]['video_url_1'],
                'video_text_1' => $post['form'][$k]['video_text_1'],
                'video_url_2' => $post['form'][$k]['video_url_2'],
                'video_text_2' => $post['form'][$k]['video_text_2'],
                'video_url_3' => $post['form'][$k]['video_url_3'],
                'video_text_3' => $post['form'][$k]['video_text_3'],
                'video_url_4' => $post['form'][$k]['video_url_4'],
                'video_text_4' => $post['form'][$k]['video_text_4'],
                'video_url_5' => $post['form'][$k]['video_url_5'],
                'video_text_5' => $post['form'][$k]['video_text_5'],
                'video_url_6' => $post['form'][$k]['video_url_6'],
                'video_text_6' => $post['form'][$k]['video_text_6'],
                'title_1' => $post['form'][$k]['title_1'],
                'subtitle_1' => $post['form'][$k]['subtitle_1'],
                'title_2' => $post['form'][$k]['title_2'],
                'subtitle_2' => $post['form'][$k]['subtitle_2'],
                'title_3' => $post['form'][$k]['title_3'],
                'subtitle_3' => $post['form'][$k]['subtitle_3'],
                'title_4' => $post['form'][$k]['title_4'],
                'subtitle_4' => $post['form'][$k]['subtitle_4'],
                'title_5' => $post['form'][$k]['title_5'],
                'subtitle_5' => $post['form'][$k]['subtitle_5'],
                'title_6' => $post['form'][$k]['title_6'],
                'subtitle_6' => $post['form'][$k]['subtitle_6'],
                'footer_title_1' => $post['form'][$k]['footer_title_1'],
                'footer_title_2' => $post['form'][$k]['footer_title_2'],
                'footer_text_1' => $post['form'][$k]['footer_text_1'],
                'footer_text_2' => $post['form'][$k]['footer_text_2'],

                'content_status_submitted' => $post['form'][$k]['content_status_submitted'],
                'content_status_received' => $post['form'][$k]['content_status_received'],
                'content_status_processing' => $post['form'][$k]['content_status_processing'],
                'content_status_completed' => $post['form'][$k]['content_status_completed'],
                'content_status_closed' => $post['form'][$k]['content_status_closed'],

                'ex_name' => ucfirst($post['form'][$k]['ex_name']),
                'ex_url' => $post['form'][$k]['ex_url'],
                'sub_active' => $post['form']['page_content']['sub_active']
            ));
          } else {
            $sql = '
            INSERT INTO ' . $k . ' 
            (
              page_id,
              language_id,
              slug,
              meta_title,
              meta_desc,
              meta_keyw,
              menu_title,
              content_title,
              content_description,
              content_text,
              content_column_left,
              content_column_right,
              overview_title,
              overview_description,
              overview_text,
              footer_title_1,
              footer_title_2,
              footer_text_1,
              footer_text_2,
              ex_name,
              ex_url,
              sub_active
            )
            VALUES
            (
              :page_id,
              :language_id,
              :slug,
              :meta_title,
              :meta_desc,
              :meta_keyw,
              :menu_title,
              :content_title,
              :content_description,
              :content_text,
              :content_column_left,
              :content_column_right,
              :overview_title,
              :overview_description,
              :overview_text,
              :footer_title_1,
              :footer_title_2,
              :footer_text_1,
              :footer_text_2,
              :ex_name,
              :ex_url,
              :sub_active
            )';
            $this->db->query($sql, array(
                'page_id' => $id,
                'language_id' => $this->config->item('default_language'),
                'slug' => $this->url->string_to_url($post['form'][$k]['slug']),
                'meta_title' => $post['form'][$k]['meta_title'],
                'meta_desc' => $post['form'][$k]['meta_desc'],
                'meta_keyw' => $post['form'][$k]['meta_keyw'],
                'menu_title' => ucfirst($post['form'][$k]['menu_title']),
                'content_title' => ucfirst($post['form'][$k]['content_title']),
                'content_description' => $post['form'][$k]['content_description'],
                'content_text' => $post['form'][$k]['content_text'],
                'content_column_left' => $post['form'][$k]['content_column_left'],
                'content_column_right' => $post['form'][$k]['content_column_right'],
                'overview_title' => ucfirst($post['form'][$k]['overview_title']),
                'overview_description' => $post['form'][$k]['overview_description'],
                'overview_text' => $post['form'][$k]['overview_text'],
                'footer_title_1' => $post['form'][$k]['footer_title_1'],
                'footer_title_2' => $post['form'][$k]['footer_title_2'],
                'footer_text_1' => $post['form'][$k]['footer_text_1'],
                'footer_text_2' => $post['form'][$k]['footer_text_2'],
                'ex_name' => ucfirst($post['form'][$k]['ex_name']),
                'ex_url' => $post['form'][$k]['ex_url'],
                'sub_active' => $post['form']['page_content']['sub_active']
            ));
          }
        }
        
        $languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array(
            $this->config->item('default_language')
        ));
        
        foreach($languages as $language) {
            foreach($post['form'] as $k => $v) {
                $sql = '
                INSERT INTO ' . $k . ' 
                (
                  page_id,
                  language_id,
                  sub_active
                )
                VALUES
                (
                  :page_id,
                  :language_id,
                  :sub_active
                )';
                
                $this->db->query($sql, array(
                    'page_id' => $id,
                    'language_id' => $language['language_id'],
                    'sub_active' => 0
                ));
            }
        }
        
        if(haveFilters('pages'))
            $this->addFilters($id, $post['page']['filters'], $this->config->item('default_language'), 'pages');
        
        return $id;
    }
    
    function edit($page_id, $language_id, $post)
    {
        $sql = 'SELECT `page`.parent_id FROM `page` WHERE `page`.page_id = ?';
        $parent = $this->db->query($sql, array(
            $page_id
        ));
        $parent = $parent[0]['parent_id'];
        
        if($post['parent_id'] != $parent) {
            $order = $this->db->query('SELECT MAX(`order`) + 1 AS `order` FROM `page` WHERE `page`.parent_id = ?', array(
                $post['parent_id']
            ));
            $order = $order[0]['order'];
        } else {
            $order = $this->db->query('SELECT `page`.order FROM `page` WHERE `page`.page_id = ?', array(
                $page_id
            ));
            $order = $order[0]['order'];
        }
        
        $sql = '
        UPDATE `page` 
        SET 
          `page`.parent_id = :parent_id, 
          `page`.external = :external, 
          `page`.order = :order, 
          `page`.last_update = :last_update, 
          `page`.edit_by = :edit_by,
          `page`.controller = :controller,
          `page`.deletable = :deletable
        WHERE 
          `page`.page_id = :page_id
        ';
        
        $this->db->query($sql, array(
            'parent_id' => $post['parent_id'],
            'external' => $post['external'],
            'order' => $order,
            'page_id' => $page_id,
            'last_update' => time(),
            'edit_by' => $_SESSION['username'],
            'controller' => (isset($post['controller']) && strlen($post['controller']) > 0 ? $post['controller'] : 'pages'),
            'deletable' => (isset($post['deletable']) && $post['deletable'] == 0 ? 0 : 1)
        ));
        
        foreach($post['form'] as $k => $v) {
            $sql = 'SELECT `' . $k . '`.slug FROM `' . $k . '` WHERE `' . $k . '`.page_id = ? AND `' . $k . '`.language_id = ?';
            $content = $this->db->query($sql, array(
                $page_id,
                $language_id
            ));
            
            $old_slug = @$content[0]['slug'];
            
            if($old_slug != $this->url->string_to_url($post['form'][$k]['slug'])) {
                $sql = '
                UPDATE `' . $k . '`
                SET `' . $k . '`.slug_301 = :slug_301
                WHERE `' . $k . '`.page_id = :page_id
                AND `' . $k . '`.language_id = :language_id
                ';
                
                $this->db->query($sql, array(
                    'page_id' => $page_id,
                    'language_id' => $language_id,
                    'slug_301' => $old_slug
                ));
            }

            if ($k == 'page_content') {
                $sql = '
                UPDATE `' . $k . '`
                SET
                  `' . $k . '`.slug = :slug,
                  `' . $k . '`.meta_title = :meta_title,
                  `' . $k . '`.meta_desc = :meta_desc,
                  `' . $k . '`.meta_keyw = :meta_keyw,
                  `' . $k . '`.menu_title = :menu_title,
                  `' . $k . '`.content_title = :content_title,
                  `' . $k . '`.content_description = :content_description,
                  `' . $k . '`.content_text = :content_text,
                  `' . $k . '`.content_column_left = :content_column_left,
                  `' . $k . '`.content_column_right = :content_column_right,
                  `' . $k . '`.overview_title = :overview_title,
                  `' . $k . '`.overview_description = :overview_description,
                  `' . $k . '`.overview_text = :overview_text,
                  `' . $k . '`.video_url_1 = :video_url_1,
                  `' . $k . '`.video_text_1 = :video_text_1,
                  `' . $k . '`.video_url_2 = :video_url_2,
                  `' . $k . '`.video_text_2 = :video_text_2,
                  `' . $k . '`.video_url_3 = :video_url_3,
                  `' . $k . '`.video_text_3 = :video_text_3,
                  `' . $k . '`.video_url_4 = :video_url_4,
                  `' . $k . '`.video_text_4 = :video_text_4,
                  `' . $k . '`.video_url_5 = :video_url_5,
                  `' . $k . '`.video_text_5 = :video_text_5,
                  `' . $k . '`.video_url_6 = :video_url_6,
                  `' . $k . '`.video_text_6 = :video_text_6,
                  `' . $k . '`.title_1 = :title_1,
                  `' . $k . '`.subtitle_1 = :subtitle_1,
                  `' . $k . '`.title_2 = :title_2,
                  `' . $k . '`.subtitle_2 = :subtitle_2,
                  `' . $k . '`.title_3 = :title_3,
                  `' . $k . '`.subtitle_3 = :subtitle_3,
                  `' . $k . '`.title_4 = :title_4,
                  `' . $k . '`.subtitle_4 = :subtitle_4,
                  `' . $k . '`.title_5 = :title_5,
                  `' . $k . '`.subtitle_5 = :subtitle_5,
                  `' . $k . '`.title_6 = :title_6,
                  `' . $k . '`.subtitle_6 = :subtitle_6,
                  `' . $k . '`.footer_title_1 = :footer_title_1,
                  `' . $k . '`.footer_title_2 = :footer_title_2,
                  `' . $k . '`.footer_text_1 = :footer_text_1,
                  `' . $k . '`.footer_text_2 = :footer_text_2,
                  
                  `' . $k . '`.content_status_submitted = :content_status_submitted,
                  `' . $k . '`.content_status_received = :content_status_received,
                  `' . $k . '`.content_status_processing = :content_status_processing,
                  `' . $k . '`.content_status_completed = :content_status_completed,
                  `' . $k . '`.content_status_closed = :content_status_closed,
                  
                  `' . $k . '`.ex_name = :ex_name,
                  `' . $k . '`.ex_url = :ex_url,
                  `' . $k . '`.sub_active = :sub_active
                WHERE `' . $k . '`.page_id = :page_id
                AND `' . $k . '`.language_id = :language_id
                ';
                $this->db->query($sql, array(
                    'page_id' => $page_id,
                    'language_id' => $language_id,
                    'slug' => $this->url->string_to_url($post['form'][$k]['slug']),
                    'meta_title' => $post['form'][$k]['meta_title'],
                    'meta_desc' => $post['form'][$k]['meta_desc'],
                    'meta_keyw' => $post['form'][$k]['meta_keyw'],
                    'menu_title' => ucfirst($post['form'][$k]['menu_title']),
                    'content_title' => ucfirst($post['form'][$k]['content_title']),
                    'content_description' => $post['form'][$k]['content_description'],
                    'content_text' => $post['form'][$k]['content_text'],
                    'content_column_left' => $post['form'][$k]['content_column_left'],
                    'content_column_right' => $post['form'][$k]['content_column_right'],
                    'overview_title' => ucfirst($post['form'][$k]['overview_title']),
                    'overview_description' => $post['form'][$k]['overview_description'],
                    'overview_text' => $post['form'][$k]['overview_text'],
                    'video_url_1' => $post['form'][$k]['video_url_1'],
                    'video_text_1' => $post['form'][$k]['video_text_1'],
                    'video_url_2' => $post['form'][$k]['video_url_2'],
                    'video_text_2' => $post['form'][$k]['video_text_2'],
                    'video_url_3' => $post['form'][$k]['video_url_3'],
                    'video_text_3' => $post['form'][$k]['video_text_3'],
                    'video_url_4' => $post['form'][$k]['video_url_4'],
                    'video_text_4' => $post['form'][$k]['video_text_4'],
                    'video_url_5' => $post['form'][$k]['video_url_5'],
                    'video_text_5' => $post['form'][$k]['video_text_5'],
                    'video_url_6' => $post['form'][$k]['video_url_6'],
                    'video_text_6' => $post['form'][$k]['video_text_6'],
                    'title_1' => $post['form'][$k]['title_1'],
                    'subtitle_1' => $post['form'][$k]['subtitle_1'],
                    'title_2' => $post['form'][$k]['title_2'],
                    'subtitle_2' => $post['form'][$k]['subtitle_2'],
                    'title_3' => $post['form'][$k]['title_3'],
                    'subtitle_3' => $post['form'][$k]['subtitle_3'],
                    'title_4' => $post['form'][$k]['title_4'],
                    'subtitle_4' => $post['form'][$k]['subtitle_4'],
                    'title_5' => $post['form'][$k]['title_5'],
                    'subtitle_5' => $post['form'][$k]['subtitle_5'],
                    'title_6' => $post['form'][$k]['title_6'],
                    'subtitle_6' => $post['form'][$k]['subtitle_6'],
                    'footer_title_1' => $post['form'][$k]['footer_title_1'],
                    'footer_title_2' => $post['form'][$k]['footer_title_2'],
                    'footer_text_1' => $post['form'][$k]['footer_text_1'],
                    'footer_text_2' => $post['form'][$k]['footer_text_2'],
                    
                    'content_status_submitted' => $post['form'][$k]['content_status_submitted'],
                    'content_status_received' => $post['form'][$k]['content_status_received'],
                    'content_status_processing' => $post['form'][$k]['content_status_processing'],
                    'content_status_completed' => $post['form'][$k]['content_status_completed'],
                    'content_status_closed' => $post['form'][$k]['content_status_closed'],

                    'ex_name' => ucfirst($post['form'][$k]['ex_name']),
                    'ex_url' => $post['form'][$k]['ex_url'],
                    'sub_active' => $post['form']['page_content']['sub_active']
                ));
            } else {
                $sql = '
                UPDATE `' . $k . '`
                SET
                  `' . $k . '`.slug = :slug,
                  `' . $k . '`.meta_title = :meta_title,
                  `' . $k . '`.meta_desc = :meta_desc,
                  `' . $k . '`.meta_keyw = :meta_keyw,
                  `' . $k . '`.menu_title = :menu_title,
                  `' . $k . '`.content_title = :content_title,
                  `' . $k . '`.content_description = :content_description,
                  `' . $k . '`.content_text = :content_text,
                  `' . $k . '`.content_column_left = :content_column_left,
                  `' . $k . '`.content_column_right = :content_column_right,
                  `' . $k . '`.overview_title = :overview_title,
                  `' . $k . '`.overview_description = :overview_description,
                  `' . $k . '`.overview_text = :overview_text,
                  `' . $k . '`.footer_title_1 = :footer_title_1,
                  `' . $k . '`.footer_title_2 = :footer_title_2,
                  `' . $k . '`.footer_text_1 = :footer_text_1,
                  `' . $k . '`.footer_text_2 = :footer_text_2,
                  `' . $k . '`.ex_name = :ex_name,
                  `' . $k . '`.ex_url = :ex_url,
                  `' . $k . '`.sub_active = :sub_active
                WHERE `' . $k . '`.page_id = :page_id
                AND `' . $k . '`.language_id = :language_id
                ';
                $this->db->query($sql, array(
                    'page_id' => $page_id,
                    'language_id' => $language_id,
                    'slug' => $this->url->string_to_url($post['form'][$k]['slug']),
                    'meta_title' => $post['form'][$k]['meta_title'],
                    'meta_desc' => $post['form'][$k]['meta_desc'],
                    'meta_keyw' => $post['form'][$k]['meta_keyw'],
                    'menu_title' => ucfirst($post['form'][$k]['menu_title']),
                    'content_title' => ucfirst($post['form'][$k]['content_title']),
                    'content_description' => $post['form'][$k]['content_description'],
                    'content_text' => $post['form'][$k]['content_text'],
                    'content_column_left' => $post['form'][$k]['content_column_left'],
                    'content_column_right' => $post['form'][$k]['content_column_right'],
                    'overview_title' => ucfirst($post['form'][$k]['overview_title']),
                    'overview_description' => $post['form'][$k]['overview_description'],
                    'overview_text' => $post['form'][$k]['overview_text'],
                    'footer_title_1' => $post['form'][$k]['footer_title_1'],
                    'footer_title_2' => $post['form'][$k]['footer_title_2'],
                    'footer_text_1' => $post['form'][$k]['footer_text_1'],
                    'footer_text_2' => $post['form'][$k]['footer_text_2'],
                    'ex_name' => ucfirst($post['form'][$k]['ex_name']),
                    'ex_url' => $post['form'][$k]['ex_url'],
                    'sub_active' => $post['form']['page_content']['sub_active']
                ));
            }
        }
        
        if(haveFilters('pages'))
            $this->addFilters($page_id, $post['page']['filters'], $this->config->item('default_language'), 'pages');
        
        if (isset($post['media'])) {
          foreach($post['media'] as $media) {
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
    }
    
    function update_overview($post)
    {
        if(isset($post['active'])) {
            foreach($post['active'] as $k => $v) {
                $this->db->query('UPDATE `page` SET `page`.active = ? WHERE `page`.page_id = ?', array($v, $k));
            }
        }
        if(isset($post['highlight'])) {
            foreach($post['highlight'] as $k => $v) {
                $this->db->query('UPDATE `page` SET `page`.highlight = ? WHERE `page`.page_id = ?', array($v, $k));
            }
        }
        if(isset($post['main_menu'])) {
            foreach($post['main_menu'] as $k => $v) {
                $this->db->query('UPDATE `page` SET `page`.main_menu = ? WHERE `page`.page_id = ?', array($v, $k));
            }
        }
        if(isset($post['footer'])) {
            foreach($post['footer'] as $k => $v) {
                $this->db->query('UPDATE `page` SET `page`.footer = ? WHERE `page`.page_id = ?', array($v, $k));
            }
        }
        if(isset($post['menu'])) {
            foreach($post['menu'] as $k => $v) {
                $this->db->query('UPDATE `page` SET `page`.menu = ? WHERE `page`.page_id = ?', array($v, $k));
            }
        }
    }
    
    function order_media($direction, $table_id, $language_id, $current_order)
    {
        //WHERE CONTROLLER = PHOTO TYPE IN DB CHECK TOEVOEGEN
        switch($direction) {
            case 'left':
                $from = $this->db->query('SELECT `media`.order, `media`.media_id FROM `media` WHERE `media`.table_id = ? AND `media`.order = ?', array(
                    $table_id,
                    $current_order
                ));
                $to = $this->db->query('SELECT `media`.order, `media`.media_id FROM `media` WHERE `media`.order < ? AND `media`.table_id = ? ORDER BY `media`.order DESC', array(
                    $current_order,
                    $table_id
                ));
                
                if(!empty($to)) {
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
                
                if(!empty($to)) {
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
        $sql = 'SELECT * FROM `page` WHERE `page`.page_id = ? AND `page`.deletable = 1';
        $r = $this->db->query($sql, array(
            $id
        ));
        
        $sql = 'SELECT * FROM `page` WHERE `page`.parent_id = ?';
        $children = $this->db->query($sql, array(
            $id
        ));
        
        if(!empty($r) && empty($children)) {
            $this->db->query('DELETE FROM `page` WHERE `page`.page_id = ? AND `page`.deletable = 1', array(
                $id
            ));
            $this->db->query('DELETE FROM `page_content` WHERE `page_content`.page_id = ?', array(
                $id
            ));
            $this->db->query('DELETE FROM `mobile_content` WHERE `mobile_content`.page_id = ?', array(
                $id
            ));
            $this->db->query('DELETE FROM `formular_selected` WHERE `formular_selected`.page_id = ?', array(
                $id
            ));
            $this->db->query('DELETE FROM `slider_selected` WHERE `slider_selected`.page_id = ?', array(
                $id
            ));
            $this->db->query('DELETE FROM `videos_selected` WHERE `videos_selected`.page_id = ?', array(
                $id
            ));

            if(haveFilters('pages')) {
                $sql = '
                SELECT * 
                FROM `filter`
                WHERE `filter`.controller = :controller
                ';
                $data = $this->db->query($sql, array(
                    'controller' => 'pages'
                ));
                if(!empty($data)) {
                    foreach($data as $filter) {
                        $sql = '
                        SELECT *
                          FROM `filter`, `filter_item`, `filter_heading`
                          WHERE `filter_heading`.filter_heading_id = `filter_item`.filter_heading_id
                          AND `filter_heading`.filter_id = `filter`.filter_id
                          AND `filter`.filter_id = :filter_id	
                        ';
                        $subelements = $this->db->query($sql, array(
                            'filter_id' => $filter['filter_id']
                        ));
                        if(isset($subelements) && $subelements && count($subelements) > 0)
                            foreach($subelements as $j => $sub_element)
                                $this->db->query('DELETE FROM `filter_item_saved` WHERE `filter_item_saved`.table_id = ? AND `filter_item_saved`.filter_item_id = ?', array(
                                    $id,
                                    $sub_element['filter_item_id']
                                ));
                    }
                }
            }
            
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
            
            foreach($media_ar as $media) {
                $this->delete_media($media['media_id']);
            }
        }
    }
    
    function order($direction, $current_order, $parent_id, $page_id)
    {
        switch($direction) {
            case 'up':
                $from = $this->db->query('SELECT `page`.order, `page`.page_id FROM `page` WHERE `page`.page_id = ?', array(
                    $page_id
                ));
                $to = $this->db->query('SELECT `page`.order, `page`.page_id FROM `page` WHERE `page`.order < ? AND `page`.parent_id = ? ORDER BY `page`.order DESC', array(
                    $current_order,
                    $parent_id
                ));
                
                if(!empty($to)) {
                    $this->db->query('UPDATE `page` SET `page`.order = ? WHERE `page`.page_id = ?', array(
                        $to[0]['order'],
                        $from[0]['page_id']
                    ));
                    $this->db->query('UPDATE `page` SET `page`.order = ? WHERE `page`.page_id = ?', array(
                        $from[0]['order'],
                        $to[0]['page_id']
                    ));
                }
                break;
            
            case 'down':
                $from = $this->db->query('SELECT `page`.order, `page`.page_id FROM `page` WHERE `page`.page_id = ?', array(
                    $page_id
                ));
                $to = $this->db->query('SELECT `page`.order, `page`.page_id FROM `page` WHERE `page`.order > ? AND `page`.parent_id = ? ORDER BY `page`.order ASC', array(
                    $current_order,
                    $parent_id
                ));
                
                if(!empty($to)) {
                    $this->db->query('UPDATE `page` SET `page`.order = ? WHERE `page`.page_id = ?', array(
                        $to[0]['order'],
                        $from[0]['page_id']
                    ));
                    $this->db->query('UPDATE `page` SET `page`.order = ? WHERE `page`.page_id = ?', array(
                        $from[0]['order'],
                        $to[0]['page_id']
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
        
        //$dirs = glob(BASE_PATH . MEDIA_DIR . CONTROLLER . '/*', GLOB_ONLYDIR);
        $dirs = glob(MEDIA_DIR . CONTROLLER . '/*', GLOB_ONLYDIR);
        
        foreach($dirs as $dir) {
            if(is_dir($dir)) {
                if(file_exists($dir . '/' . $filename)) {
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
    
    
    function fetch_category_filters($language_id, $controller, $category_id = 0, $prod_id = 0)
    {
        
        $sql = '
        SELECT * 
        FROM `filter`, `filter_heading`
        WHERE `filter`.filter_id = `filter_heading`.filter_id
        AND `filter_heading`.language_id = :lang
        AND `filter`.controller = :controller
        ';
        
        $data = $this->db->query($sql, array(
            'lang' => $this->config->item('default_language'),
            'controller' => $controller
        ));
        
        $i = 0;
        
        if(!empty($data)) {
            foreach($data as $filter) {
                $return[$i] = $filter;
                $return[$i]['selected'] = array();
                
                $sql = '
                SELECT *, `filter_item`.filter_item_id as filter_item_id_number, `filter_item`.title as filter_item_title		
                  FROM `filter`, `filter_item`, `filter_heading`, `filter_item_category`
                  WHERE `filter_heading`.filter_id = `filter`.filter_id
                  
                  AND `filter_item_category`.filter_item_id = `filter`.filter_id				
                  AND `filter_item`.filter_heading_id = `filter_heading`.filter_heading_id
                  AND `filter_item_category`.category_id = :category_id
                  AND `filter_item_category`.saved = 1
                  AND `filter_heading`.language_id = :language_id
                  AND `filter`.filter_id = :filter_id
                ';
                
                $subelements2 = $this->db->query($sql, array(
                    'category_id' => $category_id,
                    'language_id' => $language_id,
                    'filter_id' => $filter['filter_id']
                ));
                
                if(isset($subelements2) && $subelements2 && count($subelements2) > 0) {
                    foreach($subelements2 as $j => $sub_element) {
                        $return[$i]['subelements'][$sub_element['filter_item_identify']] = $sub_element;
                        
                        $sql = '
                        SELECT *
                        FROM `filter_item_saved`
                        WHERE `filter_item_saved`.filter_item_id = :filter_item_id
                        AND `filter_item_saved`.table_id = :table_id
                        AND `filter_item_saved`.saved = 1
                        ';
                        $selected = $this->db->query($sql, array(
                            'filter_item_id' => $sub_element['filter_item_id_number'],
                            'table_id' => $prod_id
                        ));
                        if($selected && count($selected) >= 1) {
                            foreach($selected as $option) {
                                $return[$i]['selected'][] = $option['filter_item_id'];
                            }
                        }
                        //else $return[$i]['selected'] = array();
                    }
                } else {
                    unset($return[$i]);
                }
                $i++;
            }
        }
        if(isset($return) && $return && count($return) > 0)
            return $return;
        else
            return false;
    }
    
    function fetch_filters($controller, $language_id, $category_id = 0)
    {
        $sql = '
        SELECT * 
        FROM `filter`, `filter_heading`
        WHERE `filter`.filter_id = `filter_heading`.filter_id
        AND `filter_heading`.language_id = :lang
        AND `filter`.controller = :controller
        ';
        
        $data = $this->db->query($sql, array(
            'lang' => $this->config->item('default_language'),
            'controller' => $controller
        ));
        
        $i = 0;
        
        if(!empty($data)) {
            foreach($data as $filter) {
                $return[$i] = $filter;
                $return[$i]['selected'] = array();
                
                $sql = '
                SELECT *, `filter_item`.filter_item_id as filter_item_id_number, `filter_item`.title as filter_item_title		
                  FROM `filter`, `filter_item`, `filter_heading`
                  WHERE `filter_heading`.filter_id = `filter`.filter_id
                  
                  AND `filter_item`.filter_heading_id = `filter_heading`.filter_heading_id
                  AND `filter_heading`.language_id = :language_id
                  AND `filter`.filter_id = :filter_id
                ';
                
                $subelements2 = $this->db->query($sql, array(
                    'language_id' => $language_id,
                    'filter_id' => $filter['filter_id']
                ));
                
                if(isset($subelements2))
                    foreach($subelements2 as $j => $sub_element) {
                        $return[$i]['subelements'][$sub_element['filter_item_identify']] = $sub_element;
                        
                        $sql = '
                        SELECT *
                        FROM `filter_item_saved`
                        WHERE `filter_item_saved`.filter_item_id = :filter_item_id
                        AND `filter_item_saved`.table_id = :table_id
                        AND `filter_item_saved`.saved = 1
                        ';

                        $selected = $this->db->query($sql, array(
                            'filter_item_id' => $sub_element['filter_item_id_number'],
                            'table_id' => $category_id
                        ));
                        if($selected && count($selected) >= 1) {
                            foreach($selected as $option) {
                                $return[$i]['selected'][] = $option['filter_item_id'];
                            }
                        }
                        //else $return[$i]['selected'] = array();
                    }
                $i++;
            }
        }
        if(isset($return) && $return && count($return) > 0)
            return $return;
        else
            return false;
    }
    
    function getFilters($controller, $language_id, $id = 0, $category_id = 0)
    {
        if(haveFilters($controller)) {
            if(!haveCategories($controller))
                return $this->fetch_filters($controller, $language_id, $id);
            else
                return $this->fetch_category_filters($language_id, $controller, $category_id, $id);
        } else
            return false;
    }
    
    function addFilters($id, $posted_filters, $language_id, $controller)
    {
        
        $sql = '
        SELECT * 
        FROM `filter`
        WHERE `filter`.controller = ?
        ';
        $data = $this->db->query($sql, array($controller));
        if(!empty($data)) {
            foreach($data as $filter) {
                $sql = '
                SELECT *
                  FROM `filter`, `filter_item`, `filter_heading`
                  WHERE `filter_heading`.filter_heading_id = `filter_item`.filter_heading_id
                  AND `filter_heading`.filter_id = `filter`.filter_id
                  AND `filter`.filter_id = :filter_id	
                ';
                $subelements = $this->db->query($sql, array(
                    'filter_id' => $filter['filter_id']
                ));
                if(isset($subelements) && $subelements && count($subelements) > 0)
                    foreach($subelements as $j => $sub_element)
                        $this->db->query('DELETE FROM `filter_item_saved` WHERE `filter_item_saved`.table_id=? AND `filter_item_saved`.filter_item_id = ?', array(
                            $id,
                            $sub_element['filter_item_id']
                        ));
            }
        }
        
        if(isset($posted_filters) && $posted_filters && count($posted_filters) > 0)
            foreach($posted_filters as $k => $filters) {
                if(is_array($filters)) {
                    foreach($filters as $l => $filters_subelements) {
                        $sql = 'INSERT INTO `filter_item_saved`
                        (
                          `filter_item_saved`.saved,
                          `filter_item_saved`.table_id,
                          `filter_item_saved`.filter_item_id
                        )
                        VALUES
                        (
                          :saved,
                          :table_id,
                          :filter_item_id
                        )
                        ';
                        $this->db->query($sql, array(
                            'saved' => 1,
                            'table_id' => $id,
                            'filter_item_id' => $filters_subelements
                        ));
                        $arr_filters[] = $filters_subelements;
                        
                        $sql2 = '
                        SELECT `filter_item`.* 
                        FROM `filter`, `filter_item`
                        WHERE `filter_item`.filter_item_id = "' . $filters_subelements . '"
                        AND `filter`.controller = "' . $controller . '"';
                        
                        $elem = $this->db->query($sql2);
                        
                        if(isset($elem) && count($elem) > 0) {
                            $sql2 = '
                            SELECT `filter_item`.* 
                            FROM `filter`, `filter_item`, `filter_heading` 
                            WHERE `filter_item`.filter_heading_id = `filter_heading`.filter_heading_id 
                            AND `filter_item`.filter_item_identify = "' . $elem[0]['filter_item_identify'] . '"
                            AND `filter_item`.filter_item_id <> "' . $filters_subelements . '" 
                            AND `filter`.filter_id = `filter_heading`.filter_id 
                            AND `filter`.controller = "' . $controller . '"';
                            
                            $return = $this->db->query($sql2);
                            
                            foreach($return as $item) {
                                $sql = 'INSERT INTO `filter_item_saved`
                                (
                                  `filter_item_saved`.saved,
                                  `filter_item_saved`.table_id,
                                  `filter_item_saved`.filter_item_id
                                )
                                VALUES
                                (
                                  :saved,
                                  :table_id,
                                  :filter_item_id
                                )
                                ';
                                $this->db->query($sql, array(
                                    'saved' => 1,
                                    'table_id' => $id,
                                    'filter_item_id' => $item['filter_item_id']
                                ));
                                $arr_filters[] = $item['filter_item_id'];
                            }
                        }
                    }
                } else {
                    $sql = 'INSERT INTO `filter_item_saved`
                    (
                      `filter_item_saved`.saved,
                      `filter_item_saved`.table_id,
                      `filter_item_saved`.filter_item_id
                    )
                    VALUES
                    (
                      :saved,
                      :table_id,
                      :filter_item_id
                    )
                    ';
                    $this->db->query($sql, array(
                        'saved' => 1,
                        'table_id' => $id,
                        'filter_item_id' => $filters
                    ));
                    $arr_filters[] = $filters;
                    
                    $sql2 = '
                    SELECT `filter_item`.* 
                    FROM `filter`, `filter_item`
                    WHERE `filter_item`.filter_item_id = "' . $filters . '"
                    AND `filter`.controller = "' . $controller . '"';
                    
                    $elem = $this->db->query($sql2);
                    
                    if(isset($elem) && count($elem) > 0) {
                        $sql2 = '
                        SELECT `filter_item`.* 
                        FROM `filter`, `filter_item`, `filter_heading` 
                        WHERE `filter_item`.filter_heading_id = `filter_heading`.filter_heading_id 
                        AND `filter_item`.filter_item_identify = "' . $elem[0]['filter_item_identify'] . '"
                        AND `filter_item`.filter_item_id <> "' . $filters . '" 
                        AND `filter`.filter_id = `filter_heading`.filter_id 
                        AND `filter`.controller = "' . $controller . '"';
                        
                        $return = $this->db->query($sql2);
                        
                        foreach($return as $item) {
                            $sql = 'INSERT INTO `filter_item_saved`
                            (
                              `filter_item_saved`.saved,
                              `filter_item_saved`.table_id,
                              `filter_item_saved`.filter_item_id
                            )
                            VALUES
                            (
                              :saved,
                              :table_id,
                              :filter_item_id
                            )
                            ';
                            $this->db->query($sql, array(
                                'saved' => 1,
                                'table_id' => $id,
                                'filter_item_id' => $item['filter_item_id']
                            ));
                            $arr_filters[] = $item['filter_item_id'];
                        }
                    }
                }
            }

        $sql2 = '
        SELECT `filter_item`.* 
        FROM `filter`, `filter_item`, `filter_heading` 
        WHERE `filter_item`.filter_heading_id = `filter_heading`.filter_heading_id 
        AND `filter`.filter_id = `filter_heading`.filter_id 
        AND `filter_heading`.language_id = ' . $language_id . '
        AND `filter`.controller = "' . $controller . '"';

        $return = $this->db->query($sql2);
        
        if(isset($return))
            foreach($return as $item) {
                if(!in_array($item['filter_item_id'], $arr_filters)) {
                    
                    $sql = 'INSERT INTO `filter_item_saved`
                    (
                      `filter_item_saved`.saved,
                      `filter_item_saved`.table_id,
                      `filter_item_saved`.filter_item_id
                    )
                    VALUES
                    (
                      :saved,
                      :table_id,
                      :filter_item_id
                    )
                    ';
                    $this->db->query($sql, array(
                        'saved' => 0,
                        'table_id' => $id,
                        'filter_item_id' => $item['filter_item_id']
                    ));
                }
            }
    }


}

?>