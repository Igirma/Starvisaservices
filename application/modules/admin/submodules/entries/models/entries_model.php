<?php

class entries_model extends model
{
    function fetch_all()
    {
        $data = $this->db->query('SELECT * FROM `users_type_entries` ORDER BY user_entry_order ASC');
        if (!isset($data[0])) {
            return false;
        }
        /*
        foreach ($data as $key => $item) {
            $data[$key]['entry_types'] = $this->take_selected($item['user_entry_id']);
        }
        */
        return $data;
    }

    function fetch_types()
    {
        $data = $this->db->query('SELECT * FROM `users_type` ORDER BY users_type_order ASC');
        if (!isset($data[0])) {
            return false;
        }
        return $data;
    }
    
    function fetch_services()
    {
        $data = $this->db->query('SELECT * FROM `users_services` ORDER BY users_services_order ASC');
        if (!isset($data[0])) {
            return false;
        }
        return $data;
    }

    function fetch($user_entry_id)
    {
        $data = $this->db->query('SELECT * FROM `users_type_entries` WHERE user_entry_id = ? LIMIT 1', array($user_entry_id));
        if (!isset($data[0])) {
            return false;
        }
        /*
        $data[0]['types'] = $this->fetch_selected($user_entry_id);
        if ($data[0]['types'] !== false) {
            $data[0]['types_selected'] = array();
            foreach ($data[0]['types'] as $users_type_id => $users_type_name) {
                array_push($data[0]['types_selected'], $users_type_id);
            }
        }
        $data[0]['services'] = $this->fetch_services_selected($user_entry_id);
        if ($data[0]['services'] !== false) {
            $data[0]['services_selected'] = array();
            foreach ($data[0]['services'] as $users_services_id => $users_services_name) {
                array_push($data[0]['services_selected'], $users_services_id);
            }
        }
        */
        return $data[0];
    }

    /*
    function fetch_selected($user_entry_id)
    {
        $sql = '
          SELECT 
            `users_type`.users_type_order, 
            `users_type`.users_type_name, 
            `users_type`.users_type_id 
          FROM `users_type_selected` 
          INNER JOIN `users_type` 
            ON `users_type`.users_type_id = `users_type_selected`.users_type_id 
          WHERE `users_type_selected`.user_entry_id = ? 
          ORDER BY `users_type`.users_type_order ASC
        ';
        $data = $this->db->query($sql, array($user_entry_id));
        if (!isset($data[0])) {
            return false;
        }
        $types = array();
        foreach ($data as $type) {
            extract($type);
            $types[$users_type_id] = $users_type_name;
        }
        return $types;
    }
    
    function take_selected($user_entry_id)
    {
        $types = $this->fetch_selected($user_entry_id);
        if (!$types) {
            return false;
        }
        $type = array();
        foreach ($types as $key => $name) {
            $type[] = $name;
        }
        return implode(', ', $type);
    }
    
    function fetch_services_selected($users_entry_id)
    {
        $sql = '
          SELECT 
            `users_services`.users_services_order, 
            `users_services`.users_services_name, 
            `users_services`.users_services_id 
          FROM `users_services_selected` 
          INNER JOIN `users_services` 
            ON `users_services`.users_services_id = `users_services_selected`.users_services_id 
          WHERE `users_services_selected`.users_entry_id = ? 
          ORDER BY `users_services`.users_services_order ASC
        ';
        $data = $this->db->query($sql, array($users_entry_id));
        if (!isset($data[0])) {
            return false;
        }
        $services = array();
        foreach ($data as $service) {
            extract($service);
            $services[$users_services_id] = $users_services_name;
        }
        return $services;
    }
    
    function take_services_selected($users_entry_id)
    {
        $services = $this->fetch_services_selected($users_entry_id);
        if (!$services) {
            return false;
        }
        $service = array();
        foreach ($services as $key => $name) {
            $service[] = $name;
        }
        return implode(', ', $service);
    }
    */

    function add($post)
    {
        $row = $this->db->query('SELECT MAX(`user_entry_order`) + 1 AS entry_order FROM `users_type_entries`');
        $this->db->query('INSERT INTO `users_type_entries` ( user_entry_order, user_entry_name ) VALUES ( ?, ? )', array(
            (isset($row[0]['entry_order']) ? $row[0]['entry_order'] : 0), 
            $post['user_entry_name']
        ));
        $id = $this->db->last_insert_id;
        //$this->attach_services($id, (isset($post['services']) ? $post['services'] : ''));
        //$this->attach_types($id, (isset($post['types']) ? $post['types'] : ''));
        return $id;
    }

    function edit($post, $id)
    {
        $this->db->query('UPDATE `users_type_entries` SET user_entry_name = ? WHERE user_entry_id = ? LIMIT 1', array($post['user_entry_name'], $id));
        //$this->attach_services($id, (isset($post['services']) ? $post['services'] : ''));
        //$this->attach_types($id, (isset($post['types']) ? $post['types'] : ''));
        return true;
    }

    /*
    function attach_types($user_entry_id, $types)
    {
        $this->db->query('DELETE FROM `users_type_selected` WHERE user_entry_id = ?', array($user_entry_id));
        if (!isset($types) || empty($types) || count($types) < 1) {
            return false;
        }
        $selected = array();
        foreach ($types as $users_type_id) {
            if ((int) $users_type_id > 0) {
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
    
    function attach_services($users_entry_id, $services)
    {
        $this->db->query('DELETE FROM `users_services_selected` WHERE users_entry_id = ?', array($users_entry_id));
        if (!isset($services) || empty($services) || count($services) < 1) {
            return false;
        }
        $selected = array();
        foreach ($services as $users_services_id) {
            if ((int) $users_services_id > 0) {
                array_push($selected, $users_services_id, $users_entry_id);
            }
        }
        if (!count($selected)) {
            return false;
        }
        $values = str_repeat('( ?, ? ), ', (count($selected) / 2));
        $values = rtrim($values, ', ');
        return $this->db->query('INSERT INTO `users_services_selected` ( users_services_id, users_entry_id ) VALUES ' . $values, $selected);
    }
    */

    function order($direction, $order, $id)
    {
        switch ($direction) {
            case 'up':
                $from = $this->db->query('SELECT user_entry_order, user_entry_id FROM `users_type_entries` WHERE user_entry_id = ? LIMIT 1', array($id));
                $to = $this->db->query('SELECT user_entry_order, user_entry_id FROM `users_type_entries` WHERE user_entry_order < ? ORDER BY user_entry_order DESC LIMIT 1', array($order));
                if (isset($to[0])) {
                    $this->db->query('UPDATE `users_type_entries` SET user_entry_order = ? WHERE user_entry_id = ?', array($to[0]['user_entry_order'], $from[0]['user_entry_id']));
                    $this->db->query('UPDATE `users_type_entries` SET user_entry_order = ? WHERE user_entry_id = ?', array($from[0]['user_entry_order'], $to[0]['user_entry_id']));
                }
                break;

            case 'down':
                $from = $this->db->query('SELECT user_entry_order, user_entry_id FROM `users_type_entries` WHERE user_entry_id = ? LIMIT 1', array($id));
                $to = $this->db->query('SELECT user_entry_order, user_entry_id FROM `users_type_entries` WHERE user_entry_order > ? ORDER BY user_entry_order ASC LIMIT 1', array($order));
                if (isset($to[0])) {
                    $this->db->query('UPDATE `users_type_entries` SET user_entry_order = ? WHERE user_entry_id = ?', array($to[0]['user_entry_order'], $from[0]['user_entry_id']));
                    $this->db->query('UPDATE `users_type_entries` SET user_entry_order = ? WHERE user_entry_id = ?', array($from[0]['user_entry_order'], $to[0]['user_entry_id']));
                }
                break;
        }
    }

    function delete($id)
    {
        $this->db->query('DELETE FROM `users_services_selected` WHERE users_entry_id = ?', array($id));
        $this->db->query('DELETE FROM `users_type_selected` WHERE user_entry_id = ?', array($id));
        $this->db->query('DELETE FROM `users_type_entries` WHERE user_entry_id = ?', array($id));
        return true;
    }
}

?>