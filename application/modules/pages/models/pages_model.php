<?php
class pages_model extends model
{
    function fetch_page($segment, $language, $page_id = 0, $controller = '')
    {
        if ($controller != '') {
            $sql = '
            SELECT * FROM `page`
            LEFT JOIN `page_content`
              ON `page`.page_id = `page_content`.page_id
            WHERE `page_content`.language_id = ?
              AND `page`.controller = ?
              AND `page`.active = ? 
              AND `page_content`.sub_active = ? 
            LIMIT 1
            ';
            $r = $this->db->query($sql, array($language, $controller, 1, 1));
        } elseif ($page_id == 0) {
            $sql = '
            SELECT * FROM `page`
            LEFT JOIN `page_content`
              ON `page`.page_id = `page_content`.page_id
            WHERE `page_content`.language_id = ?
              AND `page_content`.slug = ?
              AND `page`.active = ? 
              AND `page_content`.sub_active = ? 
            ';
            $r = $this->db->query($sql, array($language, $segment, 1, 1));
        } else {
            $sql = '
            SELECT * FROM `page`
            LEFT JOIN `page_content`
              ON `page`.page_id = `page_content`.page_id
            WHERE `page_content`.language_id = ? 
              AND `page`.page_id = ? 
              AND `page`.active = ? 
              AND `page_content`.sub_active = ? 
            ';
            $r = $this->db->query($sql, array($language, $page_id, 1, 1));
        }
        
        if (!isset($r[0])) {
            return false;
        }

        $sql = '
          SELECT * FROM `media`
          LEFT JOIN `media_content` 
            ON `media_content`.media_id = `media`.media_id
          WHERE `media`.controller = ? 
            AND `media_content`.language_id = ? 
            AND `media`.table_id = ? 
            AND `media`.album_id = ? 
          ORDER BY `media`.album_thumb DESC
        ';
        $r[0]['media'] = $this->db->query($sql, array('pages', $language, $r[0]['page_id'], 0));

        $sql = '
          SELECT * FROM `media`
          LEFT JOIN `media_content` 
            ON `media_content`.media_id = `media`.media_id 
          WHERE `media`.controller = ? 
            AND `media_content`.language_id = ? 
            AND `media`.table_id = ? 
            AND `media`.album_id = ? 
          ORDER BY `media`.album_thumb DESC
        ';
        $r[0]['media']['home'] = $this->db->query($sql, array('pages', $language, $r[0]['page_id'], 1));

        $sql = '
          SELECT * FROM `media`
          LEFT JOIN `media_content` 
            ON `media_content`.media_id = `media`.media_id 
          WHERE `media`.controller = ? 
            AND `media_content`.language_id = ? 
            AND `media`.table_id = ? 
            AND `media`.album_id = ? 
          ORDER BY `media`.album_thumb DESC
        ';
        $r[0]['media']['slide'] = $this->db->query($sql, array('pages', $language, $r[0]['page_id'], 2));

        $sql = '
        SELECT *
        FROM `media`
        INNER JOIN `media_type`
          ON `media_type`.media_type_id = `media`.media_type_id
        WHERE `media`.table_id = ? 
          AND `media_type`.name = ? 
          AND `media`.controller = ? 
        ORDER BY `media`.media_id ASC
        ';
        $r[0]['docs'] = $this->db->query($sql, array($r[0]['page_id'], 'doc', 'pages'));

        if (!empty($r) && !$this->db->error) {
            return $r[0];
        }

        $sql = '
          SELECT * FROM `page`
          LEFT JOIN `page_content`
            ON `page`.page_id = `page_content`.page_id
          WHERE `page_content`.language_id = ?
            AND `page_content`.slug_301 = ?
            AND `page`.active = 1
            AND `page_content`.sub_active = 1
        ';
        $r = $this->db->query($sql, array($language, $segment));
        
        if (!empty($r) && !$this->db->error) {
            $sub = subSlug($r[0]['parent_id']);
            if ($sub != '') {
                $this->url->redirect(BASE_URL . $sub . '/' . $r[0]['slug'], 301);
            } else {
                $this->url->redirect(BASE_URL . $r[0]['slug'], 301);
            }
        }
    }

    function fetch_home_pages($language)
    {
      $sql = '
        SELECT * FROM `page` 
        INNER JOIN `page_content` 
          ON `page`.page_id = `page_content`.page_id 
        WHERE `page_content`.language_id = ?
          AND `page`.highlight = 1 
          AND `page`.active = 1 
          AND `page_content`.sub_active = 1 
        ORDER BY RAND() LIMIT 3
      ';
      $data = $this->db->query($sql, array($language));
      
      if (!isset($data[0])) {
          return false;
      }
      foreach ($data as $k => $page)
      {
          $sql = '
            SELECT *
            FROM `media`
            INNER JOIN `media_content`
              ON `media_content`.media_id = `media`.media_id
            WHERE `media`.table_id = ?
              AND `media`.controller = ?
              AND `media_content`.language_id = ?
            ORDER BY `media`.order ASC LIMIT 1
          ';
          $data[$k]['media'] = $this->db->query($sql, array($page['page_id'], 'pages', $language));
      }
      
      return $data;
    }
    
    function fetch_subpages($controller, $language)
    {
      $sql = '
        SELECT * FROM `page` 
        LEFT JOIN `page_content` 
          ON `page`.page_id = `page_content`.page_id
        WHERE `page_content`.language_id = ? 
          AND `page`.controller = ? 
          AND `page`.active = ? 
          AND `page_content`.sub_active = ? 
        LIMIT 1
      ';
      $r = $this->db->query($sql, array($language, $controller, 1, 1));

      if (!isset($r[0])) {
        return false;
      }

      $data = $r[0];

      $sql = '
        SELECT * FROM `page` 
        INNER JOIN `page_content` 
          ON `page`.page_id = `page_content`.page_id 
        WHERE `page_content`.language_id = ? 
          AND `page`.parent_id = ? 
          AND `page`.active = ? 
          AND `page_content`.sub_active = ? 
        ORDER BY `page`.order ASC
      ';
      $data['children'] = $this->db->query($sql, array($language, $data['page_id'], 1, 1));

      if (!empty($data['children']) && !$this->db->error && count($data['children']) > 0)
      {
        foreach ($data['children'] as $k => $item)
        {
            $sql = '
              SELECT `media`.filename FROM `media` 
              INNER JOIN `media_content` 
                ON `media_content`.media_id = `media`.media_id 
              WHERE `media`.table_id = ? 
                AND `media`.controller = ? 
                AND `media_content`.language_id = ? 
                AND `media`.album_id = ? 
              ORDER BY `media`.order ASC 
            ';
            $media = $this->db->query($sql, array($item['page_id'], 'pages', $language, 0));
            if (isset($media[0])) {
                $data['children'][$k]['media'] = $media;
            } else {
                $data['children'][$k]['media'] = false;
            }
            $sql = '
              SELECT * FROM `media` 
              INNER JOIN `media_content`
                ON `media_content`.media_id = `media`.media_id
              WHERE `media`.table_id = ?
                AND `media`.controller = ?
                AND `media_content`.language_id = ?
                AND `media`.album_id = ?
              LIMIT 1
            ';
            $home = $this->db->query($sql, array($item['page_id'], 'pages', $language, 1));
            if (isset($home[0])) {
                $data['children'][$k]['thumbnail'] = $home[0]['filename'];
            } else {
                $data['children'][$k]['thumbnail'] = false;
            }
            $sql = '
              SELECT `media`.filename FROM `media` 
              INNER JOIN `media_type` 
                ON `media_type`.media_type_id = `media`.media_type_id 
              WHERE `media`.table_id = ? 
                AND `media_type`.name = ? 
                AND `media`.controller = ? 
              ORDER BY `media`.media_id ASC LIMIT 1 
            ';
            $docs = $this->db->query($sql, array($item['page_id'], 'doc', 'pages'));
            if (isset($docs[0])) {
                $data['children'][$k]['docs'] = $docs[0]['filename'];
            } else {
                $data['children'][$k]['docs'] = false;
            }
        }
        return $data;
      }
      return false;
    }

    function get_holidays()
    {
      $holidays = $this->db->query('SELECT * FROM `country_holidays` ORDER BY `country_holidays`.holiday_name ASC');
      if (!isset($holidays[0])) {
          return false;
      }
      return $holidays;
    }
    
    function get_team($language) {
        $sql = '
          SELECT * FROM `courses`
          INNER JOIN `courses_content`
            ON `courses`.course_id = `courses_content`.course_id
          WHERE `courses_content`.language_id = ? 
            AND `courses`.active = ? 
            AND `courses_content`.sub_active = ? 
          ORDER BY `courses`.order ASC
        ';
        $team = $this->db->query($sql, array($language, 1, 1));
        if (empty($team) || !count($team)) {
            return false;
        }
        foreach ($team as $k => $r) {
            $sql = '
              SELECT `media`.filename FROM `media`
              INNER JOIN `media_content`
                ON `media_content`.media_id = `media`.media_id
              WHERE `media`.table_id = ?
                AND `media`.controller = ?
                AND `media_content`.language_id = ?
              ORDER BY `media`.order ASC
              LIMIT 1
            ';
            $team[$k]['media'] = $this->db->query($sql, array($r['course_id'], 'courses', $language));
            if (isset($team[$k]['media'][0]['filename'])) {
                $team[$k]['media'] = $team[$k]['media'][0]['filename'];
            } else {
                $team[$k]['media'] = false;
            }
        }
        return $team;
    }

    function fetch_slider($language_id, $page_id = 0)
    {
        if ($page_id == 0) {
            $sql = '
              SELECT * FROM `slider`
                INNER JOIN `slider_content` ON `slider_content`.slider_id = `slider`.slider_id
              WHERE `slider_content`.language_id = ? 
                AND `slider`.active = ? 
                AND `slider_content`.sub_active = ? 
              ORDER BY `slider`.order ASC
            ';
            $slider = $this->db->query($sql, array($language_id, 1, 1));
        } else {
            $sql = '
              SELECT * FROM `slider_selected` 
                INNER JOIN `slider` ON `slider`.slider_id = `slider_selected`.slider_id 
                INNER JOIN `slider_content` ON `slider_content`.slider_id = `slider_selected`.slider_id AND `slider_content`.language_id = ? 
              WHERE `slider_selected`.page_id = ? 
                AND `slider`.active = ? 
                AND `slider_content`.sub_active = ? 
              ORDER BY `slider`.order ASC
            ';
            $slider = $this->db->query($sql, array($language_id, $page_id, 1, 1));
        }

        if (empty($slider) || !count($slider)) {
            return false;
        }

        foreach ($slider as $k => $slide) {
            $sql = '
              SELECT `media`.filename FROM `media`
                INNER JOIN `media_content` ON `media_content`.media_id = `media`.media_id
              WHERE `media`.table_id = ? 
                AND `media`.controller = ? 
                AND `media_content`.language_id = ? 
              ORDER BY `media`.order ASC
            ';
            $slider[$k]['media'] = $this->db->query($sql, array($slide['slider_id'], 'sliders', $language_id));
            if (empty($slider[$k]['media']) || !count($slider[$k]['media'])) {
                unset($slider[$k]);
            }
        }
        return $slider;
    }


    function get_clients($language)
    {
        $sql = '
          SELECT * FROM `brands`
            INNER JOIN `brands_content` ON `brands`.brand_id = `brands_content`.brand_id 
          WHERE `brands_content`.language_id = ?
            AND `brands`.active = ? 
            AND `brands_content`.sub_active = ? 
          ORDER BY `brands`.order ASC
        ';
        $rows = $this->db->query($sql, array($language, 1, 1));

        if(!isset($rows[0]) || empty($rows[0])) {
            return false;
        }

        foreach ($rows as $k => $row) 
        {
            $sql = '
              SELECT `media`.filename FROM `media` 
                INNER JOIN `media_content` ON `media_content`.media_id = `media`.media_id 
              WHERE `media`.table_id = ? 
                AND `media`.controller = ? 
                AND `media_content`.language_id = ? 
                AND `media`.album_id = ? 
              ORDER BY `media`.order ASC 
              LIMIT 1
            ';
            $rows[$k]['media'] = $this->db->query($sql, array($row['brand_id'], 'brands', $language, 0));

            if (!empty($rows[$k]['media']) && isset($rows[$k]['media'][0]['filename'])) {
                $rows[$k]['media'] = $rows[$k]['media'][0]['filename'];
            } else {
                $rows[$k]['media'] = false;
            }
        }
        return $rows;
    }

    function get_portfolio($language)
    {
        $sql = '
          SELECT * FROM `project` 
            INNER JOIN `project_content` ON `project`.project_id = `project_content`.project_id 
          WHERE `project_content`.language_id = ? 
            AND `project`.active = ? 
            AND `project_content`.sub_active = ? 
          ORDER BY `project`.order DESC 
        ';
        $rows = $this->db->query($sql, array($language, 1, 1));

        if (!isset($rows[0]) || empty($rows[0])) 
        {
            return false;
        }

        foreach ($rows as $k => $row) 
        {
            $sql = '
              SELECT `media`.filename FROM `media` 
                INNER JOIN `media_content` ON `media_content`.media_id = `media`.media_id 
              WHERE `media`.table_id = ? 
                AND `media`.controller = ? 
                AND `media_content`.language_id = ? 
                AND `media`.album_id = ? 
              ORDER BY `media`.order ASC 
              LIMIT 1
            ';
            $rows[$k]['logo'] = $this->db->query($sql, array($row['project_id'], 'projects', $language, 2));
            if (!empty($rows[$k]['logo']) && isset($rows[$k]['logo'][0]['filename'])) {
                $rows[$k]['logo'] = $rows[$k]['logo'][0]['filename'];
            } else {
                $rows[$k]['logo'] = false;
            }

            $sql = '
              SELECT `media`.filename FROM `media` 
                INNER JOIN `media_content` ON `media_content`.media_id = `media`.media_id 
              WHERE `media`.table_id = ? 
                AND `media`.controller = ? 
                AND `media_content`.language_id = ? 
                AND `media`.album_id = ? 
              ORDER BY `media`.order ASC 
              LIMIT 1
            ';
            $rows[$k]['cover'] = $this->db->query($sql, array($row['project_id'], 'projects', $language, 1));
            if (!empty($rows[$k]['cover']) && isset($rows[$k]['cover'][0]['filename'])) {
                $rows[$k]['cover'] = $rows[$k]['cover'][0]['filename'];
            } else {
                $rows[$k]['cover'] = false;
            }
        }
        return $rows;
    }
    
    function get_portfolio_item($slug, $language)
    {
        $sql = '
          SELECT * FROM `project` 
            INNER JOIN `project_content` ON `project`.project_id = `project_content`.project_id 
          WHERE `project_content`.language_id = ? 
            AND `project_content`.slug = ? 
            AND `project`.active = ? 
            AND `project_content`.sub_active = ? 
          LIMIT 1
        ';
        $data = $this->db->query($sql, array($language, $slug, 1, 1));
        if (!isset($data[0]) || empty($data[0]))
        {
            return false;
        }

        $sql = '
          SELECT `media`.filename FROM `media` 
            INNER JOIN `media_content` ON `media_content`.media_id = `media`.media_id 
          WHERE `media`.table_id = ? 
            AND `media`.controller = ? 
            AND `media_content`.language_id = ? 
            AND `media`.album_id = ? 
          ORDER BY `media`.order ASC 
          LIMIT 1
        ';
        $data[0]['logo'] = $this->db->query($sql, array($data[0]['project_id'], 'projects', $language, 2));
        if (!empty($data[0]['logo']) && isset($data[0]['logo'][0]['filename'])) {
            $data[0]['logo'] = $data[0]['logo'][0]['filename'];
        } else {
            $data[0]['logo'] = false;
        }

        $sql = '
          SELECT `media`.filename FROM `media` 
            INNER JOIN `media_content` ON `media_content`.media_id = `media`.media_id 
          WHERE `media`.table_id = ? 
            AND `media`.controller = ? 
            AND `media_content`.language_id = ? 
            AND `media`.album_id = ? 
          ORDER BY `media`.order ASC 
          LIMIT 1
        ';
        $data[0]['cover'] = $this->db->query($sql, array($data[0]['project_id'], 'projects', $language, 1));
        if (!empty($data[0]['cover']) && isset($data[0]['cover'][0]['filename'])) {
            $data[0]['cover'] = $data[0]['cover'][0]['filename'];
        } else {
            $data[0]['cover'] = false;
        }

        $sql = '
          SELECT `media`.filename FROM `media` 
            INNER JOIN `media_content` ON `media_content`.media_id = `media`.media_id 
          WHERE `media`.table_id = ? 
            AND `media`.controller = ? 
            AND `media_content`.language_id = ? 
            AND `media`.album_id = ? 
          ORDER BY `media`.order ASC
        ';
        $data[0]['photos_group_1'] = $this->db->query($sql, array($data[0]['project_id'], 'projects', $language, 0));
        if (!empty($data[0]['photos_group_1']) && isset($data[0]['photos_group_1'][0]['filename'])) {
            $data[0]['photos_group_1'] = $data[0]['photos_group_1'];
        } else {
            $data[0]['photos_group_1'] = false;
        }

        $sql = '
          SELECT `media`.filename FROM `media` 
            INNER JOIN `media_content` ON `media_content`.media_id = `media`.media_id 
          WHERE `media`.table_id = ? 
            AND `media`.controller = ? 
            AND `media_content`.language_id = ? 
            AND `media`.album_id = ? 
          ORDER BY `media`.order ASC
        ';
        $data[0]['photos_group_2'] = $this->db->query($sql, array($data[0]['project_id'], 'projects', $language, 3));
        if (!empty($data[0]['photos_group_2']) && isset($data[0]['photos_group_2'][0]['filename'])) {
            $data[0]['photos_group_2'] = $data[0]['photos_group_2'];
        } else {
            $data[0]['photos_group_2'] = false;
        }

        $sql = '
          SELECT `media`.filename FROM `media` 
            INNER JOIN `media_content` ON `media_content`.media_id = `media`.media_id 
          WHERE `media`.table_id = ? 
            AND `media`.controller = ? 
            AND `media_content`.language_id = ? 
            AND `media`.album_id = ? 
          ORDER BY `media`.order ASC
        ';
        $data[0]['photos_group_3'] = $this->db->query($sql, array($data[0]['project_id'], 'projects', $language, 4));
        if (!empty($data[0]['photos_group_3']) && isset($data[0]['photos_group_3'][0]['filename'])) {
            $data[0]['photos_group_3'] = $data[0]['photos_group_3'];
        } else {
            $data[0]['photos_group_3'] = false;
        }

        $r = $this->db->query('SELECT brand_id FROM `brands_selected` WHERE `brands_selected`.table_id = ? AND `brands_selected`.controller = ?', array($table_id, $controller));

        $sql = '
          SELECT 
            `brands`.brand_id,
            `brands_content`.title,
            `brands_content`.description 
          FROM `brands_selected` 
            INNER JOIN `brands` ON `brands`.brand_id = `brands_selected`.brand_id 
            LEFT JOIN `brands_content` ON `brands`.brand_id = `brands_content`.brand_id 
          WHERE `brands_selected`.table_id = ? 
            AND `brands_selected`.controller = ? 
            AND `brands_content`.language_id = ? 
          ORDER BY `brands`.order ASC
        ';
        $data[0]['partners'] = $this->db->query($sql, array($data[0]['project_id'], 'projects', $language));
        if (!empty($data[0]['partners']) && count($data[0]['partners']) > 0) 
        {
            foreach ($data[0]['partners'] as $k => $partner) 
            {
                $sql = '
                  SELECT `media`.filename FROM `media` 
                    INNER JOIN `media_content` ON `media_content`.media_id = `media`.media_id 
                  WHERE `media`.table_id = ? 
                    AND `media`.controller = ? 
                    AND `media_content`.language_id = ? 
                  ORDER BY `media`.order ASC
                ';
                $media = $this->db->query($sql, array($partner['brand_id'], 'brands', $language));
                if (isset($media[0])) {
                    $data[0]['partners'][$k]['media'] = $media[0]['filename'];
                } else {
                    $data[0]['partners'][$k]['media'] = false;
                }
            }
        } else {
            $data[0]['partners'] = false;
        }

        return $data[0];
    }

    function get_blog($language)
    {
        $sql = '
          SELECT * FROM `blog`
          INNER JOIN `blog_content` ON `blog_content`.blog_id = `blog`.blog_id
          WHERE `blog_content`.language_id = ? 
            AND `blog`.active = ? 
            AND `blog_content`.sub_active = ?
          ORDER BY `blog`.date_created DESC 
        ';
        $rows = $this->db->query($sql, array($language, 1, 1));

        if (!isset($rows[0]) || empty($rows[0])) 
        {
            return false;
        }

        foreach ($rows as $k => $row) 
        {
            $sql = '
              SELECT * FROM `reference` 
              INNER JOIN `reference_content` ON `reference`.reference_id = `reference_content`.reference_id
              WHERE `reference`.blog_id = ? 
              AND `reference_content`.language_id = ? 
              ORDER BY `reference`.order ASC
            ';
            $rows[$k]['references'] = $this->db->query($sql, array($row['blog_id'], $language));
            if (!isset($rows[$k]['references'][0])) {
                $rows[$k]['references'] = false;
            } else {
                foreach ($rows[$k]['references'] as $i => $ref) {
                    $sql = '
                      SELECT `media`.filename FROM `media` 
                        INNER JOIN `media_content` ON `media_content`.media_id = `media`.media_id 
                      WHERE `media`.table_id = ? 
                        AND `media`.controller = ? 
                        AND `media_content`.language_id = ? 
                      ORDER BY `media`.order ASC LIMIT 1
                    ';
                    $media = $this->db->query($sql, array($ref['reference_id'], 'references', $language));
                    if (isset($media[0])) {
                        $rows[$k]['references'][$i]['media'] = $media[0]['filename'];
                    } else {
                        $rows[$k]['references'][$i]['media'] = false;
                    }
                }
            }
        }

        return $rows;
    }

    function get_countries($language) 
    {
        $sql = '
          SELECT * FROM `country` 
          INNER JOIN `country_content` 
            ON `country`.country_id = `country_content`.country_id 
          WHERE `country_content`.language_id = ? 
            AND `country`.active = ? 
          ORDER BY `country_content`.name ASC
        ';
        $rows = $this->db->query($sql, array($language, 1));
        if (!isset($rows[0]) || empty($rows[0])){
            return false;
        }
        return $rows;
    }

    function fetch_form($page_id, $language)
    {
        $data = false;
        
        $sql = '
          SELECT * FROM `formular_selected`
          WHERE `formular_selected`.page_id = ?
        ';
        
        $r = $this->db->query($sql, array(
            $page_id
        ));
        
        if (!empty($r) && !$this->db->error) {
            $sql = '
            SELECT * 
            FROM `formular`
            LEFT JOIN `formular_content`
            ON `formular`.formular_id = `formular_content`.formular_id
            WHERE `formular_content`.language_id = ?
            AND `formular_content`.formular_id = ?
            AND `formular`.active = 1
            AND `formular_content`.sub_active = 1
            ';
            
            $r = $this->db->query($sql, array(
                $language,
                $r[0]['formular_id']
            ));
            
            if (!empty($r) && !$this->db->error) {
                $data = $r[0];
                
                $sql = '
                  SELECT * FROM `formular_item`, `formular_item_content`
                  WHERE `formular_item`.formular_id 					= :formular_id
                    AND `formular_item_content`.formular_item_id	= `formular_item`.formular_item_id
                    AND `formular_item_content`.language_id			= :language_id
                    AND `formular_item`.active = 1
                    AND `formular_item_content`.sub_active = 1
                  ORDER BY `formular_item`.order
                ';
                
                $data['items'] = $this->db->query($sql, array(
                    'formular_id' => $data['formular_id'],
                    'language_id' => $language
                ));
                
                if (!empty($data['items']) && !$this->db->error) {
                    foreach ($data['items'] as $k => $item) {
                        $sql = '
                          SELECT * FROM `formular_subitem`, `formular_subitem_content`
                          WHERE `formular_subitem_content`.formular_subitem_id	= `formular_subitem`.formular_subitem_id
                            AND `formular_subitem_content`.language_id			= :language_id
                            AND `formular_subitem`.formular_item_id 			= :formular_item_id
                            ORDER BY `formular_subitem`.formular_subitem_id 
                        ';
                        $data['items'][$k]['subitems'] = $this->db->query($sql, array(
                            'formular_item_id' => $item['formular_item_id'],
                            'language_id' => $language
                        ));
                    }
                }
            }
        }
        
        return $data;
    }
    
    function send_form($post, $page_id, $language)
    {
        
        $formular_data = $this->fetch_form($page_id, $language);
        $page_data = $this->fetch_page('', $language, $page_id);
        
        //testing the data 
        $test = 1;
        //if(!preg_match("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/i", $post['form']['mailadres'])) $test = 0;
        $posted = '';
        foreach ($post['form'] as $item) {
            if (is_array($item))
                $posted .= implode($item);
            else
                $posted .= $item;
        }
        if (preg_match("/bcc:|cc:|multipart|\[url|Content-Type:/i", $posted)) {
            $test = 0;
        }
        if (preg_match_all("/<a|http:/i", $posted, $out) > 3) {
            $test = 0;
        }
        foreach ($post['form'] as $key => $val) {
            if (is_array($val))
                continue;
            if (stristr($val, '\r'))
                $test = 0;
            if (stristr($val, '\n'))
                $test = 0;
            if (stristr($val, '%0A'))
                $test = 0;
            if (stristr($val, '%0D'))
                $test = 0;
            if (stristr($val, '<a'))
                $test = 0;
            if (stristr($val, 'content-type'))
                $test = 0;
            if (stristr($val, 'mime-version'))
                $test = 0;
            if (stristr($val, 'cc:'))
                $test = 0;
        }
        $ok2 = 1;
        if (count($_GET) > 0)
            $ok2 = 0;
        
        if ($_POST && $test == 1 && $ok2 == 1) {
            if ($_POST['submit']) {
                if ($_SERVER['REQUEST_METHOD'] != "POST")
                    $this->url->redirect(SITE_URL);
                $PHPMAILER =& load_class('PHPMailer', 'core');
                
                $content = '';
                $email_content = '';
                $user_email = '';
                foreach ($formular_data['items'] as $item) {
                    $value = '';
                    if ($post['form']['item' . $item['formular_item_id']]) {
                        if (!is_array($post['form']['item' . $item['formular_item_id']]))
                            $value = $post['form']['item' . $item['formular_item_id']];
                        else {
                            foreach ($post['form']['item' . $item['formular_item_id']] as $elem) {
                                if ($value != '')
                                    $value .= ', ';
                                $value .= $elem;
                            }
                        }
                    }
                    
                    if ($item['is_email'] == 1)
                        $user_email = $post['form']['item' . $item['formular_item_id']];
                    $content .= "<tr>
                      <th>" . $item['title'] . "</th>
                      <td>" . $value . "</td>
                    </tr>";
                    $email_content .= "<tr>
                      <td style='color:#484848;font-weight:bold;width:200px;'>" . $item['title'] . "</td>
                      <td>" . $value . "</td>
                    </tr>";
                }
                
                $sql = '
                INSERT INTO `form_content`
                (
                  `form_content`.type,
                  `form_content`.date_added,
                  `form_content`.formular_id,
                  `form_content`.email,
                  `form_content`.content,
                  `form_content`.language_id
                )
                VALUES
                (
                  :type,
                  :date_added,
                  :formular_id,
                  :email,
                  :content,
                  :language_id
                )
              ';
                
                $this->db->query($sql, array(
                    'type' => $page_data['menu_title'],
                    'date_added' => time(),
                    'formular_id' => $formular_data['formular_id'],
                    'email' => $user_email,
                    'content' => $content,
                    'language_id' => CUR_LANG
                ));
                
                
                $message = "<table style='color:#484848;font-size: 12px; font-family: Arial; font-weight:normal;' width='100%'>";
                $message .= $email_content;
                $message .= "<tr><td></td><td></td></tr>";
                $message .= "</table><br>";
                $message .= "<table style='color:#484848;font-size: 12px; font-family: Arial; font-weight:normal;' width='100%'>";
                $message .= "<tr><td width='80%'><a href='" . SITE_URL . "'><img src=\"cid:overnight-logo\" border='0'></td></tr>";
                
                $settings = getSettings();
                
                $message .= "<td width='80%'>" . $settings['street'] . " " . $settings['housenumber'] . " <br>" . $settings['postal'] . " " . $settings['city'] . " " . $settings['country'] . "";
                if ($settings['telephone'] != '')
                    $message .= "<br>Tel: " . $settings['telephone'] . "";
                if ($settings['fax'] != '')
                    $message .= "<br>Fax: " . $settings['fax'] . "";
                if ($settings['email'] != '')
                    $message .= "<br>E: " . $settings['email'] . "";
                $message .= "</td></tr>";
                $message .= "</table>";
                
                // e-mail addresses
                $mail_admin = $settings['admin_mail'];
                $mail_user = $user_email;
                
                // subjects
                $subject_admin = $formular_data['subject_admin'];
                $subject_user = $formular_data['subject_admin'];
                
                // text to apear befor the form informations
                $text_admin = '<div style="color:#484848;font-size: 12px; font-family: Arial; font-weight:normal;">' . $formular_data['content_admin'] . '</div><br>';
                $text_user = '<div style="color:#484848;font-size: 12px; font-family: Arial; font-weight:normal;">' . $formular_data['content_admin'] . '</div><br>';
                
                // messsages
                $message_admin = $text_admin . $message;
                $message_user = $text_user . $message;
                
                // e-mail for user
                if ($formular_data['mail_for_user'] && $user_email != '') {
                    $email_user = new PHPMailer();
                    $email_user->AddAddress($mail_user);
                    $email_user->IsHTML(true);
                    $email_user->From = $mail_admin;
                    $email_user->FromName = $settings['company'];
                    $email_user->AddEmbeddedImage(BASE_PATH . IMG_DIR . "logo.png", "overnight-logo", BASE_PATH . IMG_DIR . "logo.png", "base64", "image/png");
                    $email_user->Subject = $subject_user;
                    $email_user->Body .= $message_user;
                    
                    if ($email_user->Send()) {
                        $msg = "Bedankt, uw bericht is naar ons verzonden. Wij nemen z.s.m. contact met u op.";
                    } else
                        $msg = "E-mail niet verzonden neem contact met ons op";
                }
                
                // e-mail for admin
                if ($formular_data['mail_for_admin']) {
                    if ($mail_user == '')
                        $mail_user = 'Website formular';
                    $email_admin = new PHPMailer();
                    $email_admin->AddAddress($mail_admin);
                    $email_admin->IsHTML(true);
                    $email_admin->From = $mail_user;
                    $email_admin->FromName = $mail_user;
                    $email_admin->AddEmbeddedImage(BASE_PATH . IMG_DIR . "logo.png", "overnight-logo", BASE_PATH . IMG_DIR . "logo.png", "base64", "image/png");
                    $email_admin->Subject = $subject_admin;
                    $email_admin->Body .= $message_admin;
                    
                    if ($email_admin->Send()) {
                        $msg = "Bedankt, uw bericht is naar ons verzonden. Wij nemen z.s.m. contact met u op.";
                    } else
                        $msg = "E-mail niet verzonden neem contact met ons op";
                }
            }
        }
    }
    
}

?>