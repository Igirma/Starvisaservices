<?php

class country_model extends model
{
    function fetch_all()
    {
        $data = $this->db->query('SELECT * FROM `users_countries` ORDER BY users_country_name ASC');
        if (!isset($data[0])) {
            return false;
        }
        foreach ($data as $key => $item) {
            $data[$key]['groups'] = $this->take_selected($item['users_country_id']);
            $data[$key]['nationalities'] = $this->take_selected_nationalities($item['users_country_id']);
        }
        return $data;
    }

    function fetch_active_groups()
    {
        $data = $this->db->query('SELECT * FROM `users_countries_groups` WHERE user_country_group_active = ? ORDER BY user_country_group_order ASC', array(1));
        if (!isset($data[0])) {
            return false;
        }
        return $data;
    }
    
    function fetch_active_nationalities_groups()
    {
        $data = $this->db->query('SELECT * FROM `users_nationality_groups` WHERE user_nationality_group_active = ? ORDER BY user_nationality_group_order ASC', array(1));
        if (!isset($data[0])) {
            return false;
        }
        return $data;
    }

    function resetCountries() 
    {
        $data = $this->db->query('SELECT * FROM `country_content` ORDER BY name ASC');
        if (!isset($data[0])) {
            return false;
        }
        $this->db->query('TRUNCATE `users_countries`');
        foreach ($data as $country) 
        {
          $this->db->query('INSERT INTO `users_countries` ( users_country_name, users_country_code ) VALUES ( ?, ? )', array($country['name'], strtoupper($country['country_code'])));
        }
    }

    function fetch($users_country_id)
    {
        $data = $this->db->query('SELECT * FROM `users_countries` WHERE users_country_id = ? LIMIT 1', array($users_country_id));
        if (!isset($data[0])) {
            return false;
        }
        $data[0]['groups'] = $this->fetch_selected($users_country_id);
        if ($data[0]['groups'] !== false) {
            $data[0]['groups_selected'] = array();
            foreach ($data[0]['groups'] as $user_country_group_id => $user_country_group_name) {
                array_push($data[0]['groups_selected'], $user_country_group_id);
            }
        }
        $data[0]['nationalities'] = $this->fetch_selected_nationalities($users_country_id);
        if ($data[0]['nationalities'] !== false) {
            $data[0]['nationalities_selected'] = array();
            foreach ($data[0]['nationalities'] as $user_nationality_group_id => $user_nationality_group_name) {
                array_push($data[0]['nationalities_selected'], $user_nationality_group_id);
            }
        }
        return $data[0];
    }

    function fetch_selected($user_country_id)
    {
        $sql = '
          SELECT 
            `users_countries_groups`.user_country_group_name, 
            `users_countries_groups`.user_country_group_id 
          FROM `users_countries_groups_selected` 
          INNER JOIN `users_countries_groups` 
            ON `users_countries_groups`.user_country_group_id = `users_countries_groups_selected`.user_country_group_id 
          WHERE `users_countries_groups_selected`.user_country_id = ? 
            AND `users_countries_groups`.user_country_group_active = ? 
          ORDER BY `users_countries_groups`.user_country_group_name ASC
        ';
        $data = $this->db->query($sql, array($user_country_id, 1));
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
    
    function fetch_selected_nationalities($user_country_id)
    {
        $sql = '
          SELECT 
            `users_nationality_groups`.user_nationality_group_name, 
            `users_nationality_groups`.user_nationality_group_id 
          FROM `users_nationality_groups_selected` 
          INNER JOIN `users_nationality_groups` 
            ON `users_nationality_groups`.user_nationality_group_id = `users_nationality_groups_selected`.user_nationality_group_id 
          WHERE `users_nationality_groups_selected`.user_nationality_id = ? 
            AND `users_nationality_groups`.user_nationality_group_active = ? 
          ORDER BY `users_nationality_groups`.user_nationality_group_name ASC
        ';
        $data = $this->db->query($sql, array($user_country_id, 1));
        if (!isset($data[0])) {
            return false;
        }
        $groups = array();
        foreach ($data as $group) {
            extract($group);
            $groups[$user_nationality_group_id] = $user_nationality_group_name;
        }
        return $groups;
    }
    
    function take_selected($user_country_id)
    {
        $groups = $this->fetch_selected($user_country_id);
        if (!$groups) {
            return false;
        }
        $group = array();
        foreach ($groups as $key => $name) {
            $group[] = $name;
        }
        return implode(', ', $group);
    }
    
    function take_selected_nationalities($user_country_id)
    {
        $groups = $this->fetch_selected_nationalities($user_country_id);
        if (!$groups) {
            return false;
        }
        $group = array();
        foreach ($groups as $key => $name) {
            $group[] = $name;
        }
        return implode(', ', $group);
    }

    function add($post)
    {
        $this->db->query('INSERT INTO `users_countries` ( users_country_name, users_country_code ) VALUES (?, ?)', array($post['users_country_name'], $post['users_country_code']));
        $id = $this->db->last_insert_id;
        $this->attach_groups($id, $post['groups']);
        $this->attach_nationalities($id, $post['nationalities']);
        return $id;
    }

    function edit($post, $id)
    {
        $this->db->query('UPDATE `users_countries` SET users_country_name = ?, users_country_code = ? WHERE users_country_id = ? LIMIT 1', array($post['users_country_name'], $post['users_country_code'], $id));
        $this->attach_groups($id, $post['groups']);
        $this->attach_nationalities($id, $post['nationalities']);
        return true;
    }

    function attach_groups($user_country_id, $groups)
    {
        $this->db->query('DELETE FROM `users_countries_groups_selected` WHERE user_country_id = ?', array($user_country_id));
        if (!isset($groups) || empty($groups) || count($groups) < 1) {
            return false;
        }
        $selected = array();
        foreach ($groups as $user_country_group_id) {
            if ((int) $user_country_group_id > 0) {
                array_push($selected, $user_country_id, $user_country_group_id);
            }
        }
        if (!count($selected)) {
            return false;
        }
        $values = str_repeat('( ?, ? ), ', (count($selected) / 2));
        $values = rtrim($values, ', ');
        return $this->db->query('INSERT INTO `users_countries_groups_selected` ( user_country_id, user_country_group_id ) VALUES ' . $values, $selected);
    }
    
    function attach_nationalities($user_nationality_id, $groups)
    {
        $this->db->query('DELETE FROM `users_nationality_groups_selected` WHERE user_nationality_id = ?', array($user_nationality_id));
        if (!isset($groups) || empty($groups) || count($groups) < 1) {
            return false;
        }
        $selected = array();
        foreach ($groups as $user_nationality_group_id) {
            if ((int) $user_nationality_group_id > 0) {
                array_push($selected, $user_nationality_id, $user_nationality_group_id);
            }
        }
        if (!count($selected)) {
            return false;
        }
        $values = str_repeat('( ?, ? ), ', (count($selected) / 2));
        $values = rtrim($values, ', ');
        return $this->db->query('INSERT INTO `users_nationality_groups_selected` ( user_nationality_id, user_nationality_group_id ) VALUES ' . $values, $selected);
    }

    function update_overview($post)
    {
        if (empty($post['active']) || count($post['active']) < 1) {
            return false;
        }

        $id = array();
        $data = array();

        $sql = 'UPDATE `users_countries` SET users_country_active = CASE users_country_id';
        foreach($post['active'] as $k => $v)
        {
            $sql .= ' WHEN ? THEN ? ';
            array_push($data, (int) $k, ($v == 1 ? 1 : 0));
            array_push($id, (int) $k);
        }
        $sql .= 'END WHERE users_country_id IN (' . implode(', ', $id) . ')';

        return $this->db->query($sql, $data);
    }

    function delete($id)
    {
        $this->db->query('DELETE FROM `users_destinations_selected` WHERE users_destination_id = ? AND users_country_group_type = ?', array($id, 'country'));
        $this->db->query('DELETE FROM `users_type_countries_selected` WHERE users_country_group_id = ? AND users_country_group_type = ?', array($id, 'country'));
        $this->db->query('DELETE FROM `users_documents_selected` WHERE users_country_group_id = ? AND users_country_group_type = ?', array($id, 'country'));
        $this->db->query('DELETE FROM `users_notes_selected` WHERE users_country_group_id = ? AND users_country_group_type = ?', array($id, 'country'));
        $this->db->query('DELETE FROM `users_nationality_groups_selected` WHERE user_nationality_id = ?', array($id));
        $this->db->query('DELETE FROM `users_countries_groups_selected` WHERE user_country_id = ?', array($id));
        $this->db->query('DELETE FROM `users_countries` WHERE users_country_id = ?', array($id));
        return true;
    }
}

?>