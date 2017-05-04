<?php

class notes_model extends model
{
    function fetch_all()
    {
        $data = $this->db->query('SELECT * FROM `users_notes` ORDER BY users_notes_order ASC');
        if (!isset($data[0])) {
            return false;
        }
        return $data;
    }

    /*
    function fetch_countries()
    {
        $data = $this->db->query('SELECT * FROM `users_countries` WHERE users_country_active = ? ORDER BY users_country_name ASC', array(1));
        if (!isset($data[0])) {
            return false;
        }
        return $data;
    }

    function fetch_country_groups()
    {
        $data = $this->db->query('SELECT * FROM `users_countries_groups` WHERE user_country_group_active = ? ORDER BY user_country_group_order ASC', array(1));
        if (!isset($data[0])) {
            return false;
        }
        return $data;
    }
    */

    function fetch($users_notes_id)
    {
        $data = $this->db->query('SELECT * FROM `users_notes` WHERE users_notes_id = ? LIMIT 1', array($users_notes_id));
        if (!isset($data[0])) {
            return false;
        }
        /*
        $data[0]['countries'] = $this->fetch_selected_countries($users_notes_id);
        if ($data[0]['countries'] !== false) {
            $data[0]['countries_selected'] = array();
            foreach ($data[0]['countries'] as $users_country_id => $users_country_name) {
                array_push($data[0]['countries_selected'], $users_country_id);
            }
        }
        $data[0]['groups'] = $this->fetch_selected_groups($users_notes_id);
        if ($data[0]['groups'] !== false) {
            $data[0]['groups_selected'] = array();
            foreach ($data[0]['groups'] as $user_country_group_id => $user_country_group_name) {
                array_push($data[0]['groups_selected'], $user_country_group_id);
            }
        }
        */
        return $data[0];
    }

    /*
    function fetch_selected_countries($users_notes_id)
    {
        $sql = '
          SELECT 
            `users_countries`.users_country_id, 
            `users_countries`.users_country_name, 
            `users_countries`.users_country_active 
          FROM `users_notes_selected` 
          INNER JOIN `users_countries` 
            ON `users_countries`.users_country_id = `users_notes_selected`.users_country_group_id 
          WHERE `users_notes_selected`.users_notes_id = ? 
            AND `users_notes_selected`.users_country_group_type = ? 
            AND `users_countries`.users_country_active = ? 
          ORDER BY `users_countries`.users_country_name ASC
        ';
        $data = $this->db->query($sql, array($users_notes_id, 'country', 1));
        if (!isset($data[0])) {
            return false;
        }
        $countries = array();
        foreach ($data as $country) {
            extract($country);
            $countries[$users_country_id] = $users_country_name;
        }
        return $countries;
    }
    
    function fetch_selected_groups($users_notes_id)
    {
        $sql = '
          SELECT 
            `users_countries_groups`.user_country_group_id, 
            `users_countries_groups`.user_country_group_name, 
            `users_countries_groups`.user_country_group_active 
          FROM `users_notes_selected` 
          INNER JOIN `users_countries_groups` 
            ON `users_countries_groups`.user_country_group_id = `users_notes_selected`.users_country_group_id 
          WHERE `users_notes_selected`.users_notes_id = ? 
            AND `users_notes_selected`.users_country_group_type = ? 
            AND `users_countries_groups`.user_country_group_active = ? 
          ORDER BY `users_countries_groups`.user_country_group_order ASC
        ';
        $data = $this->db->query($sql, array($users_notes_id, 'group', 1));
        if (!isset($data[0])) {
            return false;
        }
        $groups = array();
        foreach ($data as $group) {
            extract($group);
            $groups[$user_country_group_id] = $user_country_group_name;
        }
        return $groups;
    }
    */

    function add($post)
    {
        $row = $this->db->query('SELECT MAX(`users_notes_order`) + 1 AS notes_order FROM `users_notes`');
        $this->db->query('INSERT INTO `users_notes` ( users_notes_order, users_notes_title, users_notes_subtitle, users_notes_content ) VALUES ( ?, ?, ?, ? )', array(
            (isset($row[0]['notes_order']) ? $row[0]['notes_order'] : 0),
            $post['users_notes_title'],
            $post['users_notes_subtitle'],
            $post['users_notes_content']
        ));
        $id = $this->db->last_insert_id;
        //$this->attach_countries($id, (!empty($post['countries']) ? $post['countries'] : array()), 'country');
        //$this->attach_countries($id, (!empty($post['groups']) ? $post['groups'] : array()), 'group');
        return $id;
    }

    function edit($post, $id)
    {
        $this->db->query('UPDATE `users_notes` SET users_notes_title = ?, users_notes_subtitle = ?, users_notes_content = ? WHERE users_notes_id = ? LIMIT 1', array(
            $post['users_notes_title'],
            $post['users_notes_subtitle'],
            $post['users_notes_content'],
            $id
        ));
        //$this->attach_countries($id, (!empty($post['countries']) ? $post['countries'] : array()), 'country');
        //$this->attach_countries($id, (!empty($post['groups']) ? $post['groups'] : array()), 'group');
        return true;
    }

    /*
    function attach_countries($users_notes_id, $countries, $group_type = 'country')
    {
        $this->db->query('DELETE FROM `users_notes_selected` WHERE users_notes_id = ? AND users_country_group_type = ?', array($users_notes_id, $group_type));
        if (!isset($countries) || empty($countries) || count($countries) < 1) {
            return false;
        }
        $selected = array();
        foreach ($countries as $users_country_group_id) {
            if ((int) $users_country_group_id > 0) {
                array_push($selected, $users_notes_id, $users_country_group_id, $group_type);
            }
        }
        if (!count($selected)) {
            return false;
        }
        $values = str_repeat('( ?, ?, ? ), ', (count($selected) / 3));
        $values = rtrim($values, ', ');
        return $this->db->query('INSERT INTO `users_notes_selected` ( users_notes_id, users_country_group_id, users_country_group_type ) VALUES ' . $values, $selected);
    }
    */

    function update_overview($post)
    {
        if (empty($post['active']) || count($post['active']) < 1) {
            return false;
        }

        $id = array();
        $data = array();

        $sql = 'UPDATE `users_notes` SET users_notes_active = CASE users_notes_id';
        foreach($post['active'] as $k => $v)
        {
            $sql .= ' WHEN ? THEN ? ';
            array_push($data, (int) $k, ($v == 1 ? 1 : 0));
            array_push($id, (int) $k);
        }
        $sql .= 'END WHERE users_notes_id IN (' . implode(', ', $id) . ')';

        return $this->db->query($sql, $data);
    }

    function order($direction, $order, $id)
    {
        switch ($direction) {
            case 'up':
                $from = $this->db->query('SELECT users_notes_order, users_notes_id FROM `users_notes` WHERE users_notes_id = ?', array($id));
                $to = $this->db->query('SELECT users_notes_order, users_notes_id FROM `users_notes` WHERE users_notes_order < ? ORDER BY users_notes_order DESC', array($order));
                if (isset($to[0])) {
                    $this->db->query('UPDATE `users_notes` SET users_notes_order = ? WHERE users_notes_id = ?', array($to[0]['users_notes_order'], $from[0]['users_notes_id']));
                    $this->db->query('UPDATE `users_notes` SET users_notes_order = ? WHERE users_notes_id = ?', array($from[0]['users_notes_order'], $to[0]['users_notes_id']));
                }
                break;

            case 'down':
                $from = $this->db->query('SELECT users_notes_order, users_notes_id FROM `users_notes` WHERE users_notes_id = ?', array($id));
                $to = $this->db->query('SELECT users_notes_order, users_notes_id FROM `users_notes` WHERE users_notes_order > ? ORDER BY users_notes_order ASC', array($order));
                if (isset($to[0])) {
                    $this->db->query('UPDATE `users_notes` SET users_notes_order = ? WHERE users_notes_id = ?', array($to[0]['users_notes_order'], $from[0]['users_notes_id']));
                    $this->db->query('UPDATE `users_notes` SET users_notes_order = ? WHERE users_notes_id = ?', array($from[0]['users_notes_order'], $to[0]['users_notes_id']));
                }
                break;
        }
    }

    function delete($id)
    {
        $this->db->query('DELETE FROM `users_notes_selected` WHERE users_notes_id = ?', array($id));
        $this->db->query('DELETE FROM `users_notes` WHERE users_notes_id = ?', array($id));
        return true;
    }
}

?>