<?php

class formular_model extends model
{
    function fetch_all()
    {
        $sql = '
        SELECT * FROM `formular`
        INNER JOIN `formular_content`
        ON `formular`.formular_id = `formular_content`.formular_id
        WHERE `formular_content`.language_id = ?
        ORDER BY `formular`.date_created DESC
        ';
        
        $data['formular'] = $this->db->query($sql, array(
            $this->config->item('default_language')
        ));
        return $data;
    }
    
    function fetch($formular_id, $language_id)
    {
        $sql = '
        SELECT *
        FROM `formular`
        INNER JOIN `formular_content`
        ON `formular_content`.formular_id = `formular`.formular_id
        WHERE `formular`.formular_id = ?
        AND `formular_content`.language_id = ?
        ';
        
        $r = $this->db->query($sql, array(
            $formular_id,
            $language_id
        ));
        
        if (!isset($r[0])) 
            return false;
        
        $data['formular'] = $r[0];
        
        return $data;
    }
    
    function add($post)
    {
        $sql = '
        INSERT INTO `formular`
        (
          date_created,
          mail_for_admin,
          mail_for_user
        )
        VALUES
        (
          :date_created,
          :mail_for_admin,
          :mail_for_user
        )';

        $this->db->query($sql, array(
            'date_created' => strtotime($post['formular']['date_created']),
            'mail_for_admin' => ((isset($post['formular']['mail_for_admin'])) ? 1 : 0),
            'mail_for_user' => ((isset($post['formular']['mail_for_user'])) ? 1 : 0)
        ));
        
        $id = $this->db->last_insert_id;
        
        $sql = '
        INSERT INTO `formular_content`
        (
          formular_id,
          language_id,
          title,
          subject_admin,
          subject_user,
          content_admin,
          content_user,
          sub_active
        )
        VALUES
        (
          :formular_id,
          :language_id,
          :title,
          :subject_admin,
          :subject_user,
          :content_admin,
          :content_user,
          :sub_active
        )';
        
        $this->db->query($sql, array(
            'formular_id' => $id,
            'language_id' => $this->config->item('default_language'),
            'title' => $post['formular']['title'],
            'subject_admin' => $post['formular']['subject_admin'],
            'subject_user' => $post['formular']['subject_user'],
            'content_admin' => $post['formular']['content_admin'],
            'content_user' => $post['formular']['content_user'],
            'sub_active' => $post['formular']['sub_active']
        ));

        $languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array(
            $this->config->item('default_language')
        ));
        
        if (empty($languages) || !count($languages))
            return $id;
        
        foreach ($languages as $language) {
            $sql = '
            INSERT INTO `formular_content`
            (
              formular_id,
              language_id,
              sub_active
            )
            VALUES
            (
              :formular_id,
              :language_id,
              :sub_active
            )';

            $this->db->query($sql, array(
                'formular_id' => $id,
                'language_id' => $language['language_id'],
                'sub_active' => 0
            ));
        }

        $this->db->query('DELETE FROM `formular_selected` WHERE `formular_selected`.formular_id = ?', array($id));

        if (isset($post['formular']['page_id']) && count($post['formular']['page_id']) > 0)
        {
            foreach ($post['formular']['page_id'] as $page_id)
            {
                $sql = '
                INSERT INTO `formular_selected`
                (
                  formular_id,
                  page_id
                )
                VALUES
                (
                  :formular_id,
                  :page_id
                )';

                $this->db->query($sql, array(
                    'formular_id' => $id,
                    'page_id' => $page_id
                ));
            }
        }
        return $id;
    }
    
    function edit($post, $id, $language_id)
    {
        $sql = '
        UPDATE `formular`, `formular_content`
        SET
          `formular`.date_created = :date_created,
          `formular_content`.title = :title,
          `formular_content`.subject_admin = :subject_admin,
          `formular_content`.subject_user = :subject_user,
          `formular_content`.content_admin = :content_admin,
          `formular_content`.content_user = :content_user,
          `formular`.mail_for_admin = :mail_for_admin,
          `formular`.mail_for_user = :mail_for_user
        WHERE `formular`.formular_id = :formular_id
        AND `formular_content`.formular_id = :formular_id
        AND `formular_content`.language_id = :language_id
        ';

        $this->db->query($sql, array(
            'date_created' => strtotime($post['formular']['date_created']),
            'title' => ucfirst($post['formular']['title']),
            'subject_admin' => $post['formular']['subject_admin'],
            'subject_user' => $post['formular']['subject_user'],
            'content_admin' => $post['formular']['content_admin'],
            'content_user' => $post['formular']['content_user'],
            'mail_for_admin' => ((isset($post['formular']['mail_for_admin'])) ? 1 : 0),
            'mail_for_user' => ((isset($post['formular']['mail_for_user'])) ? 1 : 0),
            'formular_id' => $id,
            'language_id' => $language_id
        ));

        $this->db->query('DELETE FROM `formular_selected` WHERE `formular_selected`.formular_id = ?', array($id));

        if (isset($post['formular']['page_id']) && count($post['formular']['page_id']) > 0)
        {
            foreach ($post['formular']['page_id'] as $page_id)
            {
                $sql = '
                INSERT INTO `formular_selected`
                (
                  formular_id,
                  page_id
                )
                VALUES
                (
                  :formular_id,
                  :page_id
                )';

                $this->db->query($sql, array(
                    'formular_id' => $id,
                    'page_id' => $page_id
                ));
            }
        }

        foreach ($post['active_items'] as $k => $v) {
            $this->db->query('UPDATE `formular_item` SET `formular_item`.active = ? WHERE `formular_item`.formular_item_id = ? LIMIT 1', array($v, $k));
        }
        foreach ($post['mandatory_items'] as $k => $v) {
            $this->db->query('UPDATE `formular_item` SET `formular_item`.mandatory = ? WHERE `formular_item`.formular_item_id = ? LIMIT 1', array($v, $k));
        }
        foreach ($post['is_email'] as $k => $v) {
            $this->db->query('UPDATE `formular_item` SET `formular_item`.is_email = ? WHERE `formular_item`.formular_item_id = ? LIMIT 1', array($v, $k));
        }

        // delete item
        if (isset($post['delete_item']) && count($post['delete_item']) > 0)
        {
            foreach ($post['delete_item'] as $id => $v)
            {
            
                $delete_item = $this->db->query('SELECT * FROM `formular_item` WHERE `formular_item`.formular_item_id = ?', array($id));
                if (isset($delete_item) && count($delete_item) > 0)
                {
                    foreach ($delete_item as $item)
                    {
                        $this->db->query('DELETE FROM `formular_item_content` WHERE `formular_item_content`.formular_item_id = ?', array($id));
                        $this->db->query('DELETE FROM `formular_item` WHERE `formular_item`.formular_item_id = ?', array($id));
                        $delete_subitem = $this->db->query('SELECT * FROM `formular_subitem` WHERE `formular_subitem`.formular_item_id = ?', array($id));
                        if (isset($delete_subitem) && count($delete_subitem) > 0)
                        {
                            foreach ($delete_subitem as $subitem)
                            {
                                $this->db->query('DELETE FROM `formular_subitem_content` WHERE `formular_subitem_content`.formular_subitem_id = ?', array($subitem['formular_subitem_id']));
                                $this->db->query('DELETE FROM `formular_subitem` WHERE `formular_subitem`.formular_subitem_id = ?', array($subitem['formular_subitem_id']));
                            }
                        }
                    }
                }
            }
        }
        // order item
        if (isset($post['order_down']) && count($post['order_down']) > 0)
        {
            foreach ($post['order_down'] as $id => $v) {
                $current_item = $this->db->query('SELECT * FROM `formular_item` WHERE `formular_item`.formular_item_id = ? LIMIT 1', array($id));
                $current_order = $current_item[0]['order'];
                
                $from = $this->db->query('SELECT `formular_item`.order, `formular_item`.formular_item_id FROM `formular_item` WHERE `formular_item`.formular_item_id = ?', array(
                    $id
                ));
                $to = $this->db->query('SELECT `formular_item`.order, `formular_item`.formular_item_id FROM `formular_item` WHERE `formular_item`.order > ? ORDER BY `formular_item`.order ASC', array(
                    $current_order
                ));
                
                if (!empty($to)) {
                    $this->db->query('UPDATE `formular_item` SET `formular_item`.order = ? WHERE `formular_item`.formular_item_id = ?', array(
                        $to[0]['order'],
                        $from[0]['formular_item_id']
                    ));
                    $this->db->query('UPDATE `formular_item` SET `formular_item`.order = ? WHERE `formular_item`.formular_item_id = ?', array(
                        $from[0]['order'],
                        $to[0]['formular_item_id']
                    ));
                }
            }
        }
        if (isset($post['order_up']) && count($post['order_up']) > 0)
        {
            foreach ($post['order_up'] as $id => $v) {
                $current_item = $this->db->query('SELECT * FROM `formular_item` WHERE `formular_item`.formular_item_id = ? LIMIT 1', array($id));
                $current_order = $current_item[0]['order'];
                
                $from = $this->db->query('SELECT `formular_item`.order, `formular_item`.formular_item_id FROM `formular_item` WHERE `formular_item`.formular_item_id = ?', array(
                    $id
                ));
                $to = $this->db->query('SELECT `formular_item`.order, `formular_item`.formular_item_id FROM `formular_item` WHERE `formular_item`.order < ? ORDER BY `formular_item`.order DESC', array(
                    $current_order
                ));
                
                if (!empty($to)) {
                    $this->db->query('UPDATE `formular_item` SET `formular_item`.order = ? WHERE `formular_item`.formular_item_id = ?', array(
                        $to[0]['order'],
                        $from[0]['formular_item_id']
                    ));
                    $this->db->query('UPDATE `formular_item` SET `formular_item`.order = ? WHERE `formular_item`.formular_item_id = ?', array(
                        $from[0]['order'],
                        $to[0]['formular_item_id']
                    ));
                }
            }
        }
    }

    function update_overview($post)
    {
        foreach ($post['active'] as $k => $v) {
            $this->db->query('UPDATE `formular` SET `formular`.active = ? WHERE `formular`.formular_id = ? LIMIT 1', array($v, $k));
        }
        foreach ($post['highlight'] as $k => $v) {
            $this->db->query('UPDATE `formular` SET `formular`.highlight = ? WHERE `formular`.formular_id = ? LIMIT 1', array($v, $k));
        }
        return true;
    }

    function delete($id)
    {
        $this->db->query('DELETE FROM `formular` WHERE `formular`.formular_id = ?', array($id));
        $this->db->query('DELETE FROM `formular_content` WHERE `formular_content`.formular_id = ?', array($id));
        $this->db->query('DELETE FROM `formular_selected` WHERE `formular_selected`.formular_id = ?', array($id));

        $delete_item = $this->db->query('SELECT * FROM `formular_item` WHERE `formular_item`.formular_id = ?', array($id));
        if (isset($delete_item) && count($delete_item) > 0) 
        {
            foreach ($delete_item as $item) {
                $this->db->query('DELETE FROM `formular_item_content` WHERE `formular_item_content`.formular_item_id = ?', array($item['formular_item_id']));
                $this->db->query('DELETE FROM `formular_item` WHERE `formular_item`.formular_item_id = ?', array($item['formular_item_id']));
                
                $delete_subitem = $this->db->query('SELECT * FROM `formular_subitem` WHERE `formular_subitem`.formular_item_id = ?', array($item['formular_item_id']));
                if (isset($delete_subitem) && count($delete_subitem) > 0)
                {
                    foreach ($delete_subitem as $subitem) {
                        $this->db->query('DELETE FROM `formular_subitem_content` WHERE `formular_subitem_content`.formular_subitem_id = ?', array($subitem['formular_subitem_id']));
                        $this->db->query('DELETE FROM `formular_subitem` WHERE `formular_subitem`.formular_subitem_id = ?', array($subitem['formular_subitem_id']));
                    }
                }
            }
        }
        return true;
    }
    
    function fetch_all_pages_selected($formular_id)
    {
        $r = $this->db->query('SELECT page_id FROM `formular_selected` WHERE `formular_selected`.formular_id = ?', array($formular_id));
        if (isset($r) && count($r) > 0) {
            $items = array();
            foreach ($r as $item) {
                $items[] = $item['page_id'];
            }
            return $items;
        }
        return false;
    }

    function fetch_items($formular_id, $language_id)
    {
        $return = array();
        
        $sql = '
        SELECT * 
        FROM `formular_item`, `formular_item_content` 
        WHERE `formular_item`.formular_item_id = `formular_item_content`.formular_item_id 
        AND `formular_item_content`.language_id = :lang 
        AND `formular_item`.formular_id = :formular_id 
        ORDER BY `formular_item`.order ASC
        ';
        $return['items'] = $this->db->query($sql, array(
            'lang' => $language_id,
            'formular_id' => $formular_id
        ));
        if ($language_id != $this->config->item('default_language'))
        {
            $sql = '
            SELECT * 
            FROM `formular_item`, `formular_item_content` 
            WHERE `formular_item`.formular_item_id = `formular_item_content`.formular_item_id 
            AND `formular_item_content`.language_id = :lang 
            AND `formular_item`.formular_id = :formular_id 
            ORDER BY `formular_item`.order ASC
            ';
            $return['default'] = $this->db->query($sql, array(
                'lang' => $this->config->item('default_language'),
                'formular_id' => $formular_id
            ));
        }
        return $return;
    }
}

?>