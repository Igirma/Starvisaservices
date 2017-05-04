<?php

class type_model extends model
{
    function fetch_all()
    {
        $data = $this->db->query('SELECT * FROM `users_type` ORDER BY users_type_order ASC');
        if (!isset($data[0])) {
            return false;
        }
        /*
        foreach ($data as $key => $item) {
            $data[$key]['entries'] = $this->take_selected($item['users_type_id']);
        }
        */
        return $data;
    }

    function fetch_entries()
    {
        $data = $this->db->query('SELECT * FROM `users_type_entries` ORDER BY user_entry_order ASC');
        if (!isset($data[0])) {
            return false;
        }
        return $data;
    }

    function fetch_countries()
    {
        $data = $this->db->query('SELECT * FROM `users_countries` ORDER BY users_country_name ASC');
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

    function fetch($users_type_id)
    {
        $data = $this->db->query('SELECT * FROM `users_type` WHERE users_type_id = ? LIMIT 1', array($users_type_id));
        if (!isset($data[0])) {
            return false;
        }
        /*
        $data[0]['entries'] = $this->fetch_selected($users_type_id);
        if ($data[0]['entries'] !== false) {
            $data[0]['entries_selected'] = array();
            foreach ($data[0]['entries'] as $user_entry_id => $user_entry_name) {
                array_push($data[0]['entries_selected'], $user_entry_id);
            }
        }
        $data[0]['countries'] = $this->fetch_selected_countries($users_type_id);
        if ($data[0]['countries'] !== false) {
            $data[0]['countries_selected'] = array();
            foreach ($data[0]['countries'] as $users_country_id => $users_country_name) {
                array_push($data[0]['countries_selected'], $users_country_id);
            }
        }
        $data[0]['groups'] = $this->fetch_selected_groups($users_type_id);
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
    function fetch_selected($users_type_id)
    {
        $sql = '
          SELECT 
            `users_type_entries`.user_entry_order, 
            `users_type_entries`.user_entry_name, 
            `users_type_entries`.user_entry_id 
          FROM `users_type_selected` 
          INNER JOIN `users_type_entries` 
            ON `users_type_entries`.user_entry_id = `users_type_selected`.user_entry_id 
          WHERE `users_type_selected`.users_type_id = ? 
          ORDER BY `users_type_entries`.user_entry_order ASC
        ';
        $data = $this->db->query($sql, array($users_type_id));
        if (!isset($data[0])) {
            return false;
        }
        $entries = array();
        foreach ($data as $entry) {
            extract($entry);
            $entries[$user_entry_id] = $user_entry_name;
        }
        return $entries;
    }

    function take_selected($users_type_id)
    {
        $entries = $this->fetch_selected($users_type_id);
        if (!$entries) {
            return false;
        }
        $entry = array();
        foreach ($entries as $key => $name) {
            $entry[] = $name;
        }
        return implode(', ', $entry);
    }

    function fetch_selected_countries($users_type_id)
    {
        $sql = '
          SELECT 
            `users_countries`.users_country_id, 
            `users_countries`.users_country_name, 
            `users_countries`.users_country_active 
          FROM `users_type_countries_selected` 
          INNER JOIN `users_countries` 
            ON `users_countries`.users_country_id = `users_type_countries_selected`.users_country_group_id 
          WHERE `users_type_countries_selected`.users_type_id = ? 
            AND `users_type_countries_selected`.users_country_group_type = ? 
            AND `users_countries`.users_country_active = ? 
          ORDER BY `users_countries`.users_country_name ASC
        ';
        $data = $this->db->query($sql, array($users_type_id, 'country', 1));
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

    function fetch_selected_groups($users_type_id)
    {
        $sql = '
          SELECT 
            `users_countries_groups`.user_country_group_id, 
            `users_countries_groups`.user_country_group_name, 
            `users_countries_groups`.user_country_group_active 
          FROM `users_type_countries_selected` 
          INNER JOIN `users_countries_groups` 
            ON `users_countries_groups`.user_country_group_id = `users_type_countries_selected`.users_country_group_id 
          WHERE `users_type_countries_selected`.users_type_id = ? 
            AND `users_type_countries_selected`.users_country_group_type = ? 
            AND `users_countries_groups`.user_country_group_active = ? 
          ORDER BY `users_countries_groups`.user_country_group_order ASC
        ';
        $data = $this->db->query($sql, array($users_type_id, 'group', 1));
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
        $row = $this->db->query('SELECT MAX(`users_type_order`) + 1 AS type_order FROM `users_type`');
        $this->db->query('INSERT INTO `users_type` ( users_type_order, users_type_name ) VALUES (?, ?)', array(
            (isset($row[0]['type_order']) ? $row[0]['type_order'] : 0),
            $post['users_type_name']
        ));
        $id = $this->db->last_insert_id;
        //$this->attach_entries($id, (!empty($post['entries']) ? $post['entries'] : array()));
        //$this->attach_countries($id, (!empty($post['countries']) ? $post['countries'] : array()), 'country');
        //$this->attach_countries($id, (!empty($post['groups']) ? $post['groups'] : array()), 'group');
        return $id;
    }

    function edit($post, $id)
    {
        $this->db->query('UPDATE `users_type` SET users_type_name = ? WHERE users_type_id = ? LIMIT 1', array($post['users_type_name'], $id));
        //$this->attach_entries($id, (!empty($post['entries']) ? $post['entries'] : array()));
        //$this->attach_countries($id, (!empty($post['countries']) ? $post['countries'] : array()), 'country');
        //$this->attach_countries($id, (!empty($post['groups']) ? $post['groups'] : array()), 'group');
        return true;
    }

    /*
    function attach_entries($users_type_id, $entries)
    {
        $this->db->query('DELETE FROM `users_type_selected` WHERE users_type_id = ?', array($users_type_id));
        if (!isset($entries) || empty($entries) || count($entries) < 1) {
            return false;
        }
        $selected = array();
        foreach ($entries as $user_entry_id) {
            if ((int) $user_entry_id > 0) {
                array_push($selected, $users_type_id, $user_entry_id);
            }
        }
        if (!count($selected)) {
            return false;
        }
        $values = str_repeat('( ?, ? ), ', (count($selected) / 2));
        $values = rtrim($values, ', ');
        return $this->db->query('INSERT INTO `users_type_selected` ( users_type_id, user_entry_id ) VALUES ' . $values, $selected);
    }

    function attach_countries($users_type_id, $countries, $group_type = 'country')
    {
        $this->db->query('DELETE FROM `users_type_countries_selected` WHERE users_type_id = ? AND users_country_group_type = ?', array($users_type_id, $group_type));
        if (!isset($countries) || empty($countries) || count($countries) < 1) {
            return false;
        }
        $selected = array();
        foreach ($countries as $users_country_group_id) {
            if ((int) $users_country_group_id > 0) {
                array_push($selected, $users_type_id, $users_country_group_id, $group_type);
            }
        }
        if (!count($selected)) {
            return false;
        }
        $values = str_repeat('( ?, ?, ? ), ', (count($selected) / 3));
        $values = rtrim($values, ', ');
        return $this->db->query('INSERT INTO `users_type_countries_selected` ( users_type_id, users_country_group_id, users_country_group_type ) VALUES ' . $values, $selected);
    }
    */

    function order($direction, $users_type_order, $users_type_id)
    {
        switch ($direction) {
            case 'up':
                $from = $this->db->query('SELECT users_type_order, users_type_id FROM `users_type` WHERE users_type_id = ?', array($users_type_id));
                $to = $this->db->query('SELECT users_type_order, users_type_id FROM `users_type` WHERE users_type_order < ? ORDER BY users_type_order DESC', array($users_type_order));
                if (isset($to[0])) {
                    $this->db->query('UPDATE `users_type` SET users_type_order = ? WHERE users_type_id = ?', array($to[0]['users_type_order'], $from[0]['users_type_id']));
                    $this->db->query('UPDATE `users_type` SET users_type_order = ? WHERE users_type_id = ?', array($from[0]['users_type_order'], $to[0]['users_type_id']));
                }
                break;

            case 'down':
                $from = $this->db->query('SELECT users_type_order, users_type_id FROM `users_type` WHERE users_type_id = ?', array($users_type_id));
                $to = $this->db->query('SELECT users_type_order, users_type_id FROM `users_type` WHERE users_type_order > ? ORDER BY users_type_order ASC', array($users_type_order));
                if (isset($to[0])) {
                    $this->db->query('UPDATE `users_type` SET users_type_order = ? WHERE users_type_id = ?', array($to[0]['users_type_order'], $from[0]['users_type_id']));
                    $this->db->query('UPDATE `users_type` SET users_type_order = ? WHERE users_type_id = ?', array($from[0]['users_type_order'], $to[0]['users_type_id']));
                }
                break;
        }
    }

    function delete($id)
    {
        $this->db->query('DELETE FROM `users_type_countries_selected` WHERE users_type_id = ?', array($id));
        $this->db->query('DELETE FROM `users_type_selected` WHERE users_type_id = ?', array($id));
        $this->db->query('DELETE FROM `users_type` WHERE users_type_id = ?', array($id));
        return true;
    }
}

?>