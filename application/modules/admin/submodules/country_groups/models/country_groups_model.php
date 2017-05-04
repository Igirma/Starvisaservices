<?php

class country_groups_model extends model
{
    function fetch_all()
    {
        $data = $this->db->query('SELECT * FROM `users_countries_groups` ORDER BY user_country_group_order ASC');
        if (!isset($data[0])) {
            return false;
        }
        return $data;
    }

    function fetch_active_countries()
    {
        $data = $this->db->query('SELECT * FROM `users_countries` WHERE users_country_active = ? ORDER BY users_country_name ASC', array(1));
        if (!isset($data[0])) {
            return false;
        }
        return $data;
    }
    
    function fetch($user_country_group_id)
    {
        $data = $this->db->query('SELECT * FROM `users_countries_groups` WHERE user_country_group_id = ? LIMIT 1', array($user_country_group_id));
        if (!isset($data[0])) {
            return false;
        }
        $data[0]['countries'] = $this->fetch_selected($user_country_group_id);
        if ($data[0]['countries'] !== false) {
            $data[0]['countries_selected'] = array();
            foreach ($data[0]['countries'] as $users_country_id => $users_country_name) {
                array_push($data[0]['countries_selected'], $users_country_id);
            }
        }
        return $data[0];
    }

    function fetch_selected($user_country_group_id)
    {
        $sql = '
          SELECT 
            `users_countries`.users_country_name, 
            `users_countries`.users_country_id 
          FROM `users_countries_groups_selected` 
          INNER JOIN `users_countries` 
            ON `users_countries`.users_country_id = `users_countries_groups_selected`.user_country_id 
          WHERE `users_countries_groups_selected`.user_country_group_id = ? 
            AND `users_countries`.users_country_active = ? 
          ORDER BY `users_countries`.users_country_name ASC
        ';
        $data = $this->db->query($sql, array($user_country_group_id, 1));
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

    function add($post)
    {
        $row = $this->db->query('SELECT MAX(`user_country_group_order`) + 1 AS group_order FROM `users_countries_groups`');
        $this->db->query('INSERT INTO `users_countries_groups` ( user_country_group_order, user_country_group_name ) VALUES ( ?, ? )', array(
            (isset($row[0]['group_order']) ? $row[0]['group_order'] : 0),
            $post['user_country_group_name']
        ));
        $id = $this->db->last_insert_id;
        $this->attach_countries($id, $post['countries']);
        return $id;
    }

    function edit($post, $id)
    {
        $this->db->query('UPDATE `users_countries_groups` SET user_country_group_name = ? WHERE user_country_group_id = ? LIMIT 1', array($post['user_country_group_name'], $id));
        return $this->attach_countries($id, $post['countries']);
    }

    function attach_countries($user_country_group_id, $countries)
    {
        $this->db->query('DELETE FROM `users_countries_groups_selected` WHERE user_country_group_id = ?', array($user_country_group_id));
        if (!isset($countries) || empty($countries) || count($countries) < 1) {
            return false;
        }
        $selected = array();
        foreach ($countries as $user_country_id) {
            if ((int) $user_country_id > 0) {
                array_push($selected, $user_country_group_id, $user_country_id);
            }
        }
        if (!count($selected)) {
            return false;
        }
        $values = str_repeat('( ?, ? ), ', (count($selected) / 2));
        $values = rtrim($values, ', ');
        return $this->db->query('INSERT INTO `users_countries_groups_selected` ( user_country_group_id, user_country_id ) VALUES ' . $values, $selected);
    }

    function update_overview($post)
    {
        if (empty($post['active']) || count($post['active']) < 1) {
            return false;
        }

        $id = array();
        $data = array();

        $sql = 'UPDATE `users_countries_groups` SET user_country_group_active = CASE user_country_group_id';
        foreach($post['active'] as $k => $v)
        {
            $sql .= ' WHEN ? THEN ? ';
            array_push($data, (int) $k, ($v == 1 ? 1 : 0));
            array_push($id, (int) $k);
        }
        $sql .= 'END WHERE user_country_group_id IN (' . implode(', ', $id) . ')';

        return $this->db->query($sql, $data);
    }

    function order($direction, $order, $id)
    {
        switch ($direction) {
            case 'up':
                $from = $this->db->query('SELECT user_country_group_order, user_country_group_id FROM `users_countries_groups` WHERE user_country_group_id = ? LIMIT 1', array($id));
                $to = $this->db->query('SELECT user_country_group_order, user_country_group_id FROM `users_countries_groups` WHERE user_country_group_order < ? ORDER BY user_country_group_order DESC LIMIT 1', array($order));
                if (isset($to[0])) {
                    $this->db->query('UPDATE `users_countries_groups` SET user_country_group_order = ? WHERE user_country_group_id = ?', array($to[0]['user_country_group_order'], $from[0]['user_country_group_id']));
                    $this->db->query('UPDATE `users_countries_groups` SET user_country_group_order = ? WHERE user_country_group_id = ?', array($from[0]['user_country_group_order'], $to[0]['user_country_group_id']));
                }
                break;

            case 'down':
                $from = $this->db->query('SELECT user_country_group_order, user_country_group_id FROM `users_countries_groups` WHERE user_country_group_id = ? LIMIT 1', array($id));
                $to = $this->db->query('SELECT user_country_group_order, user_country_group_id FROM `users_countries_groups` WHERE user_country_group_order > ? ORDER BY user_country_group_order ASC LIMIT 1', array($order));
                if (isset($to[0])) {
                    $this->db->query('UPDATE `users_countries_groups` SET user_country_group_order = ? WHERE user_country_group_id = ?', array($to[0]['user_country_group_order'], $from[0]['user_country_group_id']));
                    $this->db->query('UPDATE `users_countries_groups` SET user_country_group_order = ? WHERE user_country_group_id = ?', array($from[0]['user_country_group_order'], $to[0]['user_country_group_id']));
                }
                break;
        }
    }

    function delete($id)
    {
        $this->db->query('DELETE FROM `users_destinations_selected` WHERE users_destination_id = ? AND users_country_group_type = ?', array($id, 'group'));
        $this->db->query('DELETE FROM `users_type_countries_selected` WHERE users_country_group_id = ? AND users_country_group_type = ?', array($id, 'group'));
        $this->db->query('DELETE FROM `users_documents_selected` WHERE users_country_group_id = ? AND users_country_group_type = ?', array($id, 'group'));
        $this->db->query('DELETE FROM `users_notes_selected` WHERE users_country_group_id = ? AND users_country_group_type = ?', array($id, 'group'));
        $this->db->query('DELETE FROM `users_countries_groups_selected` WHERE user_country_group_id = ?', array($id));
        $this->db->query('DELETE FROM `users_countries_groups` WHERE user_country_group_id = ?', array($id));
        return true;
    }
}

?>