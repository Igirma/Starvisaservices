<?php

class destinations_model extends model
{
    function fetch_all()
    {
        $sql = '
          SELECT 
			`users_destinations_selected`.users_destination_id AS destination_id, 
            `users_destinations_selected`.users_destinations_selected_id AS id, 
            IF(`users_destinations_selected`.users_country_group_type = "group", 
                IF(`g`.user_country_group_active = 1, "Active", "Inactive"), 
                IF(`d`.users_country_active = 1, "Active", "Inactive")
            ) AS destination_active, 
            IF(`users_destinations_selected`.users_nationality_group_type = "group", 
                IF(`s`.user_nationality_group_active = 1, "Active", "Inactive"), 
                IF(`n`.users_country_active = 1, "Active", "Inactive")
            ) AS nationality_active, 
            IF(`users_destinations_selected`.users_country_group_type = "group", "Group", "Country") AS group_type, 
            IF(`users_destinations_selected`.users_country_group_type = "group", `g`.user_country_group_name, `d`.users_country_name) AS name, 
            IF(`users_destinations_selected`.users_nationality_group_type = "group", "Group", "Nationality") AS nationality_type, 
            IF(`users_destinations_selected`.users_nationality_group_type = "group", `s`.user_nationality_group_name, `n`.users_country_name) AS nationality 
          FROM `users_destinations_selected` 
          LEFT JOIN `users_countries` AS n 
            ON `n`.users_country_id = `users_destinations_selected`.users_nationality_id 
          LEFT JOIN `users_nationality_groups` AS s 
            ON `s`.user_nationality_group_id = `users_destinations_selected`.users_nationality_id 
          LEFT JOIN `users_countries` AS d 
            ON `d`.users_country_id = `users_destinations_selected`.users_destination_id 
          LEFT JOIN `users_countries_groups` AS g 
            ON `g`.user_country_group_id = `users_destinations_selected`.users_destination_id 
          ORDER BY name ASC
        ';
        $data = $this->db->query($sql);
        if (!isset($data[0])) {
            return false;
        }
        return $data;
    }
	
	function fetch_all_group($ungroupped_data) {
		$groupped_data = array();
		for($i = 0; $i < count($ungroupped_data); $i++) {
			if(!isset($groupped_data[$ungroupped_data[$i]['destination_id']])) {
				$groupped_data[$ungroupped_data[$i]['destination_id']][0] = array();
				$groupped_data[$ungroupped_data[$i]['destination_id']][0][0] = $ungroupped_data[$i]['name'];
				$groupped_data[$ungroupped_data[$i]['destination_id']][0][1] = 0;
				$groupped_data[$ungroupped_data[$i]['destination_id']][0][2] = $ungroupped_data[$i]['destination_active'];
			}
			$groupped_data[$ungroupped_data[$i]['destination_id']][] = $i;
			if($ungroupped_data[$i]['nationality_active'] == 'Active') {
				$groupped_data[$ungroupped_data[$i]['destination_id']][0][1] ++;
			}
		}
		return $groupped_data;
	}

    function fetch_countries()
    {
        $data = $this->db->query('SELECT * FROM `users_countries` WHERE users_country_active = ? ORDER BY users_country_name ASC', array(1));
        if (!isset($data[0])) {
            return false;
        }
        return $data;
    }

	function fetch_country_name($country)
    {
        $data = $this->db->query('SELECT * FROM `users_countries` WHERE users_country_id = ? ORDER BY users_country_name ASC', array($country));
        if (!isset($data[0])) {
            return false;
        }
        return $data;
    }
	
    function fetch_groups()
    {
        $data = $this->db->query('SELECT * FROM `users_countries_groups` WHERE user_country_group_active = ? ORDER BY user_country_group_order ASC', array(1));
        if (!isset($data[0])) {
            return false;
        }
        return $data;
    }
	
	function fetch_group()
    {
        $data = $this->db->query('SELECT * FROM `users_countries_groups` WHERE user_country_group_id = ? ORDER BY user_country_group_order ASC', array(1));
        if (!isset($data[0])) {
            return false;
        }
        return $data;
    }

    function fetch_nationalities_groups()
    {
        $data = $this->db->query('SELECT * FROM `users_nationality_groups` WHERE user_nationality_group_active = ? ORDER BY user_nationality_group_order ASC', array(1));
        if (!isset($data[0])) {
            return false;
        }
        return $data;
    }
	
	function fetch_nationality_group($group) {
		$data = $this->db->query('SELECT * FROM `users_nationality_groups` WHERE user_nationality_group_id = ? ORDER BY user_nationality_group_order ASC', array($group));
        if (!isset($data[0])) {
            return false;
        }
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

    function fetch_entries()
    {
        $data = $this->db->query('SELECT * FROM `users_type_entries` ORDER BY user_entry_order ASC');
        if (!isset($data[0])) {
            return false;
        }
        return $data;
    }
    
    function fetch_entries_options()
    {
        $data = $this->db->query('SELECT * FROM `users_type_entries_options` ORDER BY entry_option_order ASC');
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

    function fetch_prices()
    {
        // $data = $this->db->query('SELECT * FROM `users_prices` ORDER BY users_price_order ASC');
        $data = $this->db->query('SELECT * FROM `users_prices` WHERE users_price_order != 999 ORDER BY users_price_order ASC');
        if (!isset($data[0])) {
            return false;
        }
        return $data;
    }

    function fetch_documents()
    {
        $data = $this->db->query('SELECT * FROM `users_documents` ORDER BY users_document_order ASC');
        if (!isset($data[0])) {
            return false;
        }
        return $data;
    }

    function fetch_notes()
    {
        $data = $this->db->query('SELECT * FROM `users_notes` ORDER BY users_notes_order ASC');
        if (!isset($data[0])) {
            return false;
        }
        return $data;
    }

    function fetch($users_destinations_selected_id)
    {
        $data = $this->db->query('SELECT * FROM `users_destinations_selected` WHERE users_destinations_selected_id = ? LIMIT 1', array($users_destinations_selected_id));
        if (!isset($data[0])) {
            return false;
        }
        $data[0]['types'] = $this->fetch_selected_types($data[0]);
        if ($data[0]['types'] !== false) {
            $data[0]['types_selected'] = array();
            foreach ($data[0]['types'] as $type) {
                array_push($data[0]['types_selected'], $type['users_type_id']);
            }
        }
        return $data[0];
    }

    function fetch_item($users_destinations_selected_id)
    {
        $data = $this->db->query('SELECT * FROM `users_destinations_selected` WHERE users_destinations_selected_id = ? LIMIT 1', array($users_destinations_selected_id));
        if (!isset($data[0])) {
            return false;
        }
        return $data[0];
    }

    function destination_exists($post, $id)
    {
        $sql = '
          SELECT 
            IF(`users_destinations_selected`.users_country_group_type = "group", "group", "country") AS group_type, 
            IF(`users_destinations_selected`.users_country_group_type = "group", `g`.user_country_group_name, `d`.users_country_name) AS name, 
            `n`.users_country_name AS nationality 
          FROM `users_destinations_selected` 
          INNER JOIN `users_countries` AS n 
            ON `n`.users_country_id = `users_destinations_selected`.users_nationality_id 
          LEFT JOIN `users_countries` AS d 
            ON `d`.users_country_id = `users_destinations_selected`.users_destination_id 
          LEFT JOIN `users_countries_groups` AS g 
            ON `g`.user_country_group_id = `users_destinations_selected`.users_destination_id 
          WHERE `users_destinations_selected`.users_destination_id = ? 
            AND `users_destinations_selected`.users_nationality_id = ? 
            AND `users_destinations_selected`.users_country_group_type = ? 
            AND `users_destinations_selected`.users_destinations_selected_id != ? 
          LIMIT 1
        ';
        $get = $this->db->query($sql, array(
            $post['users_destination_id'], 
            $post['users_nationality_id'], 
            $post['users_country_group_type'], 
            $id
        ));
        if (!isset($get[0])) {
            return false;
        }
        return $get[0];
    }

    function fetch_selected_documents($array)
    {
        $sql = '
          SELECT 
            `users_documents`.users_document_id, 
            `users_documents`.users_document_title, 
			`users_documents`.users_document_subtitle, 
            `users_documents`.users_document_order, 
            `users_documents_selected`.* 
          FROM `users_documents_selected` 
          INNER JOIN `users_documents` 
            ON `users_documents`.users_document_id = `users_documents_selected`.users_documents_id 
          WHERE `users_documents_selected`.users_type_id = ? 
            AND `users_documents_selected`.users_country_group_id = ? 
            AND `users_documents_selected`.users_country_group_type = ? 
            AND `users_documents_selected`.users_nationality_id = ? 
            AND `users_documents_selected`.users_nationality_group_type = ? 
          ORDER BY `users_documents`.users_document_order ASC
        ';
        $data = $this->db->query($sql, array(
            $array['users_type_id'], 
            $array['users_country_group_id'], 
            $array['users_country_group_type'], 
            $array['users_nationality_id'],
            $array['users_nationality_group_type'] 
        ));
        if (!isset($data[0])) {
            return false;
        }
        $documents = array();
        foreach ($data as $k => $document) {
            $documents[] = $document;
        }
        return $documents;
    }

    function fetch_selected_notes($array)
    {
        $sql = '
          SELECT 
            `users_notes`.users_notes_id, 
            `users_notes`.users_notes_title, 
			`users_notes`.users_notes_subtitle, 
            `users_notes`.users_notes_order, 
            `users_notes_selected`.* 
          FROM `users_notes_selected` 
          INNER JOIN `users_notes` 
            ON `users_notes`.users_notes_id = `users_notes_selected`.users_notes_id 
          WHERE `users_notes_selected`.users_type_id = ? 
            AND `users_notes_selected`.users_country_group_id = ? 
            AND `users_notes_selected`.users_country_group_type = ? 
            AND `users_notes_selected`.users_nationality_id = ? 
            AND `users_notes_selected`.users_nationality_group_type = ? 
          ORDER BY `users_notes`.users_notes_order ASC
        ';
        $data = $this->db->query($sql, array(
            $array['users_type_id'], 
            $array['users_country_group_id'], 
            $array['users_country_group_type'], 
            $array['users_nationality_id'], 
            $array['users_nationality_group_type']
        ));
        if (!isset($data[0])) {
            return false;
        }
        $notes = array();
        foreach ($data as $k => $note) {
            $notes[] = $note;
        }
        return $notes;
    }
    
    function fetch_selected_types($array)
    {
        $sql = '
          SELECT 
            `users_type`.users_type_id, 
            `users_type`.users_type_name, 
            `users_type`.users_type_order, 
            `users_type_countries_selected`.* 
          FROM `users_type_countries_selected` 
          INNER JOIN `users_type` 
            ON `users_type`.users_type_id = `users_type_countries_selected`.users_type_id 
          WHERE `users_type_countries_selected`.users_country_group_id = ? 
            AND `users_type_countries_selected`.users_country_group_type = ? 
            AND `users_type_countries_selected`.users_nationality_id = ? 
            AND `users_type_countries_selected`.users_nationality_group_type = ? 
          ORDER BY `users_type`.users_type_order ASC
        ';
        $data = $this->db->query($sql, array(
            $array['users_destination_id'], 
            $array['users_country_group_type'], 
            $array['users_nationality_id'], 
            $array['users_nationality_group_type']
        ));
        if (!isset($data[0])) {
            return false;
        }
        $types = array();
        foreach ($data as $k => $type) {
            $types[$k] = $type;
            $types[$k]['entries'] = $this->fetch_selected_entries($type);
            if ($types[$k]['entries'] !== false) {
                $types[$k]['entries_selected'] = array();
                foreach ($types[$k]['entries'] as $entry) {
                    array_push($types[$k]['entries_selected'], $entry['user_entry_id']);
                }
            }
            $types[$k]['documents'] = $this->fetch_selected_documents($type);
            if ($types[$k]['documents'] !== false) {
                $types[$k]['documents_selected'] = array();
                foreach ($types[$k]['documents'] as $document) {
                    array_push($types[$k]['documents_selected'], $document['users_document_id']);
                }
            }
            $types[$k]['notes'] = $this->fetch_selected_notes($type);
            if ($types[$k]['notes'] !== false) {
                $types[$k]['notes_selected'] = array();
                foreach ($types[$k]['notes'] as $note) {
                    array_push($types[$k]['notes_selected'], $note['users_notes_id']);
                }
            }
        }
        return $types;
    }

    function fetch_selected_entries($array)
    {
        $sql = '
          SELECT 
            `users_type_entries`.user_entry_order, 
            `users_type_entries`.user_entry_name, 
            `users_type_entries`.user_entry_id, 
            `users_type_selected`.* 
          FROM `users_type_selected` 
          INNER JOIN `users_type_entries` 
            ON `users_type_entries`.user_entry_id = `users_type_selected`.user_entry_id 
          WHERE `users_type_selected`.users_type_id = ? 
            AND `users_type_selected`.users_nationality_id = ? 
            AND `users_type_selected`.users_country_group_type = ? 
            AND `users_type_selected`.users_country_group_id = ? 
            AND `users_type_selected`.users_nationality_group_type = ? 
          ORDER BY `users_type_entries`.user_entry_order ASC
        ';
        $data = $this->db->query($sql, array(
            $array['users_type_id'],
            $array['users_nationality_id'],
            $array['users_country_group_type'],
            $array['users_country_group_id'],
            $array['users_nationality_group_type']
        ));
        if (!isset($data[0])) {
            return false;
        }
        $entries = array();
        foreach ($data as $k => $entry) {
            $entries[$k] = $entry;
            $entries[$k]['services'] = $this->fetch_selected_services($entry);
            if ($entries[$k]['services'] !== false) {
                $entries[$k]['services_selected'] = array();
                foreach ($entries[$k]['services'] as $service) {
                    array_push($entries[$k]['services_selected'], $service['users_services_id']);
                }
            }
        }
        return $entries;
    }

    function fetch_selected_services($array)
    {
        $sql = '
          SELECT 
            `users_services`.users_services_order, 
            `users_services`.users_services_name, 
            `users_services`.users_services_id, 
            `users_services_selected`.* 
          FROM `users_services_selected` 
          INNER JOIN `users_services` 
            ON `users_services`.users_services_id = `users_services_selected`.users_services_id 
          WHERE `users_services_selected`.user_entry_id = ? 
            AND `users_services_selected`.users_type_id = ? 
            AND `users_services_selected`.users_country_group_type = ? 
            AND `users_services_selected`.users_country_group_id = ? 
            AND `users_services_selected`.users_nationality_id = ? 
            AND `users_services_selected`.users_nationality_group_type = ? 
          ORDER BY `users_services`.users_services_order ASC
        ';
        $data = $this->db->query($sql, array(
            $array['user_entry_id'], 
            $array['users_type_id'], 
            $array['users_country_group_type'], 
            $array['users_country_group_id'], 
            $array['users_nationality_id'],
            $array['users_nationality_group_type']
        ));
        if (!isset($data[0])) {
            return false;
        }
        $services = array();
        foreach ($data as $k => $service) {
            $services[$k] = $service;
            $services[$k]['prices'] = $this->fetch_selected_prices($service);
            if ($services[$k]['prices'] !== false) {
                $services[$k]['prices_selected'] = array();
                foreach ($services[$k]['prices'] as $price) {
                    array_push($services[$k]['prices_selected'], $price['users_price_id']);
                }
            }
        }
        return $services;
    }

    function fetch_selected_prices($array)
    {
        $sql = '
          SELECT 
            `users_prices`.users_price_order, 
            `users_prices`.users_price_name, 
            `users_prices`.users_price_id, 
            `users_services_prices`.* 
          FROM `users_services_prices` 
          INNER JOIN `users_prices` 
            ON `users_prices`.users_price_id = `users_services_prices`.users_price_id 
          WHERE `users_services_prices`.users_services_id = ? 
            AND `users_services_prices`.user_entry_id = ? 
            AND `users_services_prices`.users_type_id = ? 
            AND `users_services_prices`.users_country_group_type = ? 
            AND `users_services_prices`.users_country_group_id = ? 
            AND `users_services_prices`.users_nationality_id = ? 
            AND `users_services_prices`.users_nationality_group_type = ? 
          ORDER BY `users_prices`.users_price_order ASC
        ';
        $data = $this->db->query($sql, array(
            $array['users_services_id'], 
            $array['user_entry_id'], 
            $array['users_type_id'], 
            $array['users_country_group_type'], 
            $array['users_country_group_id'], 
            $array['users_nationality_id'], 
            $array['users_nationality_group_type']
        ));
        if (!isset($data[0])) {
            return false;
        }
        $prices = array();
        foreach ($data as $price) {
            $prices[] = $price;
        }
        return $prices;
    }

    function add($post)
    {
        if (!$data = $this->prepare_group_type($post)) {
            return false;
        }
        $sql = '
            INSERT INTO `users_destinations_selected` ( 
                users_destination_id, 
                users_nationality_id, 
                users_country_group_type, 
                users_nationality_group_type 
            ) VALUES ( ?, ?, ?, ? )
        ';
        $this->db->query($sql, array(
            $data['users_destination_id'], 
            $data['users_nationality_id'], 
            $data['users_country_group_type'], 
            $data['users_nationality_group_type']
        ));
        $id = $this->db->last_insert_id;
        if (!$id) {
            return false;
        }
        return $id;
    }

    function edit($post, $id)
    {
        if (!$data = $this->prepare_group_type($post)) {
            return false;
        }
        $this->reset_destination_data($data, $id);
        $sql = '
            UPDATE `users_destinations_selected` SET 
                users_destination_id = ?, 
                users_nationality_id = ?, 
                users_country_group_type = ?, 
                users_nationality_group_type = ? 
            WHERE users_destinations_selected_id = ? 
            LIMIT 1
        ';
        $this->db->query($sql, array(
            $data['users_destination_id'], 
            $data['users_nationality_id'], 
            $data['users_country_group_type'], 
            $data['users_nationality_group_type'], 
            $id
        ));
        return true;
    }
    
    function reset_destination_data($post, $id) 
    {
      if (!$data = $this->fetch_item($id)) {
            return false;
        }
        $reset = false;
        if ($post['users_destination_id'] != $data['users_destination_id']) {
            $reset = true;
        }
        if ($post['users_nationality_id'] != $data['users_nationality_id']) {
            $reset = true;
        }
        if ($post['users_country_group_type'] != $data['users_country_group_type']) {
            $reset = true;
        }
        if ($post['users_nationality_group_type'] != $data['users_nationality_group_type']) {
            $reset = true;
        }
        if (!$reset) {
            return false;
        }
        $where = array(
            $data['users_nationality_group_type'], 
            $data['users_country_group_type'], 
            $data['users_destination_id'], 
            $data['users_nationality_id']
        );
        $append = '
            WHERE users_nationality_group_type = ? 
            AND users_country_group_type = ? 
            AND users_country_group_id = ? 
            AND users_nationality_id = ?
        ';
        $this->db->query('DELETE FROM `users_type_countries_selected` ' . $append, $where);
        $this->db->query('DELETE FROM `users_services_selected` ' . $append, $where);
        $this->db->query('DELETE FROM `users_services_prices` ' . $append, $where);
        $this->db->query('DELETE FROM `users_documents_selected` ' . $append, $where);
        $this->db->query('DELETE FROM `users_notes_selected` ' . $append, $where);
        $this->db->query('DELETE FROM `users_type_selected` ' . $append, $where);
        return true;
    }

    function prepare_group_type($post) 
    {
        $data = array();
        if (isset($post['countries']) && !empty($post['countries']) && (int) $post['countries'] > 0) {
            $data['users_destination_id'] = (int) $post['countries'];
            $data['users_country_group_type'] = 'country';
        } elseif (isset($post['groups']) && !empty($post['groups']) && (int) $post['groups'] > 0) {
            $data['users_destination_id'] = (int) $post['groups'];
            $data['users_country_group_type'] = 'group';
        }
        if (isset($post['nationality']) && !empty($post['nationality']) && (int) $post['nationality'] > 0) {
            $data['users_nationality_id'] = (int) $post['nationality'];
            $data['users_nationality_group_type'] = 'country';
        } elseif (isset($post['nationalities_groups']) && !empty($post['nationalities_groups']) && (int) $post['nationalities_groups'] > 0) {
            $data['users_nationality_id'] = (int) $post['nationalities_groups'];
            $data['users_nationality_group_type'] = 'group';
        }
        if (!isset($data['users_destination_id'])) {
            return false;
        }
        if (!isset($data['users_nationality_id'])) {
            return false;
        }
        $data['type'] = isset($post['type']) ? $post['type'] : '';
        $data['type_name'] = isset($post['type_name']) ? $post['type_name'] : '';
        return $data;
    }

    function insert_update_type($post)
    {
        if (isset($post['type_name']) && strlen($post['type_name']) < 1) {
            return $this->insert_types($post);
        } else {
            return $this->insert_type($post);
        }
    }

    function insert_type($post)
    {
        if (!isset($post['users_nationality_id']) || !isset($post['users_country_group_id']) || !isset($post['users_country_group_type'])) {
            return false;
        }
        if (isset($post['users_type_name']) && strlen($post['users_type_name']) > 0) 
        {
            $row = $this->db->query('SELECT MAX(`users_type_order`) + 1 AS type_order FROM `users_type`');
            $this->db->query('INSERT INTO `users_type` ( users_type_order, users_type_name ) VALUES ( ?, ? )', array(
                (isset($row[0]['type_order']) ? $row[0]['type_order'] : 0),
                trim($post['users_type_name'])
            ));
            $users_type_id = $this->db->last_insert_id;
        } else {
            $users_type_id = $post['users_type_id'];
        }
        $selected = array(
            $post['users_nationality_group_type'], 
            $post['users_country_group_type'], 
            $post['users_country_group_id'], 
            $post['users_nationality_id'], 
            $users_type_id
        );
        $this->db->query('DELETE FROM `users_type_countries_selected` WHERE users_nationality_group_type = ? AND users_country_group_type = ? AND users_country_group_id = ? AND users_nationality_id = ? AND users_type_id = ?', $selected);
        $this->db->query('INSERT INTO `users_type_countries_selected` ( users_nationality_group_type, users_country_group_type, users_country_group_id, users_nationality_id, users_type_id ) VALUES ( ?, ?, ?, ?, ? )', $selected);
        return true;
    }

    function insert_types($post)
    {
        if (!isset($post['users_nationality_id']) || !isset($post['users_destination_id']) || !isset($post['users_country_group_type']) || !isset($post['users_nationality_group_type'])) {
            return false;
        }
        $this->clean_types($post);
        if (!isset($post['type']) || !is_array($post['type']) || !count($post['type'])) {
            return false;
        }
        $selected = array();
        foreach ($post['type'] as $users_type_id) {
            if ((int) $users_type_id > 0) {
                array_push($selected, 
                    $users_type_id, 
                    $post['users_nationality_id'], 
                    $post['users_destination_id'], 
                    $post['users_country_group_type'], 
                    $post['users_nationality_group_type']
                );
            }
        }
        if (!count($selected)) {
            return false;
        }
        $values = str_repeat('( ?, ?, ?, ?, ? ), ', (count($selected) / 5));
        $values = rtrim($values, ', ');
        $sql = 'INSERT INTO `users_type_countries_selected` ( 
            users_type_id, 
            users_nationality_id, 
            users_country_group_id, 
            users_country_group_type, 
            users_nationality_group_type 
        ) VALUES ';
        return $this->db->query($sql . $values, $selected);
    }

    function clean_types($post)
    {
        $inserted = array();
        $selected = array(
            $post['users_nationality_group_type'], 
            $post['users_country_group_type'], 
            $post['users_destination_id'], 
            $post['users_nationality_id']
        );
        if (isset($post['type']) && is_array($post['type']) && count($post['type']) > 0) 
        {
            foreach ($post['type'] as $users_type_id) {
                if ((int) $users_type_id > 0) {
                    array_push($inserted, (int) $users_type_id);
                }
            }
        }
        $prepend = '
            WHERE users_nationality_group_type = ? 
            AND users_country_group_type = ? 
            AND users_country_group_id = ? 
            AND users_nationality_id = ? 
        ';
        $append = '';
        if (count($inserted) > 0) 
        {
            $append = ' AND users_type_id NOT IN (' . implode(', ', $inserted) . ')';
        }
        $this->db->query('DELETE FROM `users_type_countries_selected`' . $prepend, $selected);
        $this->db->query('DELETE FROM `users_services_selected`' . $prepend . $append, $selected);
        $this->db->query('DELETE FROM `users_services_prices`' . $prepend . $append, $selected);
        $this->db->query('DELETE FROM `users_type_selected`' . $prepend . $append, $selected);
        return true;
    }

    function update_entry_option($post)
    {
        if (!isset($post['user_entry_id']) || !isset($post['entry_option_id'])) {
            return false;
        }
        $sql = '
            UPDATE `users_type_selected` SET 
              entry_option_id = ? 
            WHERE users_nationality_group_type = ? 
              AND users_country_group_type = ? 
              AND users_country_group_id = ? 
              AND users_nationality_id = ? 
              AND users_type_id = ? 
              AND user_entry_id = ? 
            LIMIT 1
        ';
        $this->db->query($sql, array(
            $post['entry_option_id'], 
            $post['users_nationality_group_type'], 
            $post['users_country_group_type'], 
            $post['users_country_group_id'], 
            $post['users_nationality_id'], 
            $post['users_type_id'], 
            $post['user_entry_id']
        ));
        return true;
    }

    function insert_entries($post)
    {
        $this->clean_entries($post);
        if (!isset($post['entries']) || !is_array($post['entries']) || !count($post['entries'])) {
            return false;
        }
        $selected = array();
        foreach ($post['entries'] as $user_entry_id) {
            if ((int) $user_entry_id > 0) {
                array_push($selected, 
                    $user_entry_id, 
                    $post['users_type_id'], 
                    $post['users_nationality_id'], 
                    $post['users_country_group_id'], 
                    $post['users_country_group_type'], 
                    $post['users_nationality_group_type']
                );
            }
        }
        if (!count($selected)) {
            return false;
        }
        $values = str_repeat('( ?, ?, ?, ?, ?, ? ), ', (count($selected) / 6));
        $values = rtrim($values, ', ');
        $sql = 'INSERT INTO `users_type_selected` ( 
            user_entry_id, 
            users_type_id, 
            users_nationality_id, 
            users_country_group_id, 
            users_country_group_type, 
            users_nationality_group_type 
        ) VALUES ';
        return $this->db->query($sql . $values, $selected);
    }

    function insert_entry($post)
    {
        $selected = array(
            $post['users_nationality_group_type'],
            $post['users_country_group_type'],
            $post['users_country_group_id'],
            $post['users_nationality_id'],
            $post['users_type_id'],
            $post['user_entry_id']
        );

        $sql = 'DELETE FROM `users_type_selected` 
            WHERE users_nationality_group_type = ? 
            AND users_country_group_type = ? 
            AND users_country_group_id = ? 
            AND users_nationality_id = ? 
            AND users_type_id = ? 
            AND user_entry_id = ?';
        $this->db->query($sql, $selected);

        $sql = 'INSERT INTO `users_type_selected` (
            users_nationality_group_type, 
            users_country_group_type, 
            users_country_group_id, 
            users_nationality_id, 
            users_type_id, 
            user_entry_id
        ) VALUES ( ?, ?, ?, ?, ?, ? )';
        $this->db->query($sql, $selected);
		// error_log($this->db->error);
		// error_log(implode(' ', $post));
        return true;
    }

    function clean_entries($post)
    {
        $inserted = array();
        $selected = array(
            $post['users_nationality_group_type'],
            $post['users_country_group_type'],
            $post['users_country_group_id'], 
            $post['users_nationality_id'], 
            $post['users_type_id']
        );
        if (isset($post['entries']) && is_array($post['entries']) && count($post['entries']) > 0) 
        {
            foreach ($post['entries'] as $user_entry_id) {
                if ((int) $user_entry_id > 0) {
                    array_push($inserted, (int) $user_entry_id);
                }
            }
        }
        $prepend = '
            WHERE users_nationality_group_type = ? 
            AND users_country_group_type = ? 
            AND users_country_group_id = ? 
            AND users_nationality_id = ? 
            AND users_type_id = ?
        ';
        $append = '';
        if (count($inserted) > 0) 
        {
            $append = ' AND user_entry_id NOT IN (' . implode(', ', $inserted) . ')';
        }
        $this->db->query('DELETE FROM `users_services_selected`' . $prepend . $append, $selected);
        $this->db->query('DELETE FROM `users_services_prices`' . $prepend . $append, $selected);
        $this->db->query('DELETE FROM `users_type_selected`' . $prepend, $selected);
        return true;
    }
    
    function insert_services($post)
    {
        $this->update_entry_option($post);
        $this->clean_services($post);
        if (!isset($post['services']) || !is_array($post['services']) || count($post['services']) < 1) {
            return false;
        }
        $selected = array();
        foreach ($post['services'] as $users_services_id) {
            if ((int) $users_services_id > 0) {
                array_push($selected, 
                    $users_services_id, 
                    $post['user_entry_id'], 
                    $post['users_type_id'], 
                    $post['users_nationality_id'], 
                    $post['users_country_group_id'], 
                    $post['users_country_group_type'], 
                    $post['users_nationality_group_type']
                );
            }
        }
        if (!count($selected)) {
            return false;
        }
        $values = str_repeat('( ?, ?, ?, ?, ?, ?, ? ), ', (count($selected) / 7));
        $values = rtrim($values, ', ');
        $sql = 'INSERT INTO `users_services_selected` ( 
            users_services_id, 
            user_entry_id, 
            users_type_id, 
            users_nationality_id, 
            users_country_group_id, 
            users_country_group_type, 
            users_nationality_group_type 
        ) VALUES ';
        return $this->db->query($sql . $values, $selected);
    }

    function insert_service($post)
    {
        $selected = array(
            $post['users_services_id'],
            $post['user_entry_id'],
            $post['users_type_id'], 
            $post['users_nationality_id'], 
            $post['users_country_group_id'], 
            $post['users_country_group_type'], 
            $post['users_nationality_group_type']
        );

        $sql = 'DELETE FROM `users_services_selected` 
            WHERE users_services_id = ? 
            AND user_entry_id = ? 
            AND users_type_id = ? 
            AND users_nationality_id = ? 
            AND users_country_group_id = ? 
            AND users_country_group_type = ? 
            AND users_nationality_group_type = ?';
        $this->db->query($sql, $selected);

        $sql = 'INSERT INTO `users_services_selected` (
            users_services_id, 
            user_entry_id, 
            users_type_id, 
            users_nationality_id, 
            users_country_group_id, 
            users_country_group_type, 
            users_nationality_group_type
        ) VALUES ( ?, ?, ?, ?, ?, ?, ? )';
        $this->db->query($sql, $selected);
        return true;
    }

    function clean_services($post)
    {
        $inserted = array();
        $selected = array(
            $post['users_country_group_type'],
            $post['users_country_group_id'], 
            $post['users_nationality_id'], 
            $post['users_type_id'],
            $post['user_entry_id']
        );
        if (isset($post['services']) && is_array($post['services']) && count($post['services']) > 0) 
        {
            foreach ($post['services'] as $users_services_id) {
                if ((int) $users_services_id > 0) {
                    array_push($inserted, (int) $users_services_id);
                }
            }
        }
        $prepend = '
            WHERE users_country_group_type = ? 
            AND users_country_group_id = ? 
            AND users_nationality_id = ? 
            AND users_type_id = ? 
            AND user_entry_id = ?
        ';
        $append = '';
        if (count($inserted) > 0) 
        {
            $append = ' AND users_services_id NOT IN (' . implode(', ', $inserted) . ')';
        }
        $this->db->query('DELETE FROM `users_services_selected`' . $prepend, $selected);
        $this->db->query('DELETE FROM `users_services_prices`' . $prepend . $append, $selected);
        return true;
    }

    function insert_prices($post)
    {
        $this->clean_prices($post);
        if (!isset($post['prices']) || !is_array($post['prices']) || count($post['prices']) < 1) {
            return false;
        }
        $selected = array();
		$free_visa = false;
        foreach ($post['prices'] as $users_services_id => $users_services_items) 
        {
            if (isset($users_services_items['grand_total'])) {
				if($users_services_items['grand_total'] == 0) {
					$free_visa = true;
				}
				
				unset($users_services_items['grand_total']);
            }
			/*if(isset($users_services_items['users_free_note'])) {
				error_log($users_services_items['users_free_note']);
			}*/
            if (is_array($users_services_items) && count($users_services_items) > 0) 
            {
                foreach ($users_services_items as $users_price_id => $price_item) {
					if(strlen($users_services_items['users_free_note']) == 0) {
						$users_services_items['users_free_note'] = NULL;
					}
					else {
						$users_services_items['users_free_note'] = (string) $users_services_items['users_free_note'];
					}
					
					if($free_visa) {
						$price_item['total'] = 0.00;
						$free_visa = false;
					}
					
                    if (is_numeric($price_item['subtotal']) && is_numeric($price_item['vat']) 
						&& is_numeric($price_item['total']) && is_numeric($users_price_id))  {
                        array_push($selected, 
                            $price_item['subtotal'], 
                            $price_item['vat'], 
                            $price_item['total'], 
                            $users_price_id, 
                            $users_services_id, 
                            $post['user_entry_id'], 
                            $post['users_type_id'], 
                            $post['users_nationality_id'], 
                            $post['users_country_group_id'], 
                            $post['users_country_group_type'], 
                            $post['users_nationality_group_type'],  
							$users_services_items['users_free_note']
                        );
                    }
                }
            }
        }
        if (!count($selected)) {
            return false;
        }
		// error_log(implode(' ', $selected));
        $values = str_repeat('( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ), ', (count($selected) / 12));
        $values = rtrim($values, ', ');
        $sql = 'INSERT INTO `users_services_prices` ( 
            subtotal, 
            vat, 
            total, 
            users_price_id, 
            users_services_id, 
            user_entry_id, 
            users_type_id, 
            users_nationality_id, 
            users_country_group_id, 
            users_country_group_type, 
            users_nationality_group_type, 
			users_free_note
        ) VALUES ';
        $this->db->query($sql . $values, $selected);
		// error_log($this->db->error);
        return true;
    }

    function clean_prices($post)
    {
        $selected = array(
            $post['users_nationality_group_type'],
            $post['users_country_group_type'],
            $post['users_country_group_id'], 
            $post['users_nationality_id'], 
            $post['users_type_id'],
            $post['user_entry_id']
        );
        $sql = '
          DELETE FROM `users_services_prices` 
          WHERE users_nationality_group_type = ? 
            AND users_country_group_type = ? 
            AND users_country_group_id = ? 
            AND users_nationality_id = ? 
            AND users_type_id = ? 
            AND user_entry_id = ?
        ';
        $this->db->query($sql, $selected);
        return true;
    }

    function insert_document($post)
    {
        $selected = array(
            $post['users_documents_id'],
            $post['users_type_id'], 
            $post['users_nationality_id'], 
            $post['users_country_group_id'], 
            $post['users_country_group_type'], 
            $post['users_nationality_group_type']
        );

        $sql = 'DELETE FROM `users_documents_selected` 
            WHERE users_documents_id = ? 
            AND users_type_id = ? 
            AND users_nationality_id = ? 
            AND users_country_group_id = ? 
            AND users_country_group_type = ? 
            AND users_nationality_group_type = ?';
        $this->db->query($sql, $selected);

        $sql = 'INSERT INTO `users_documents_selected` (
            users_documents_id, 
            users_type_id, 
            users_nationality_id, 
            users_country_group_id, 
            users_country_group_type, 
            users_nationality_group_type
        ) VALUES ( ?, ?, ?, ?, ?, ? )';
        $this->db->query($sql, $selected);
        return true;
    }

    function insert_documents($post)
    {
        $this->clean_documents($post);
        if (!isset($post['documents']) || !is_array($post['documents']) || count($post['documents']) < 1) {
            return false;
        }
        $selected = array();
        foreach ($post['documents'] as $users_document_id) {
            if ((int) $users_document_id > 0) {
                array_push($selected, 
                    $users_document_id, 
                    $post['users_type_id'], 
                    $post['users_nationality_id'], 
                    $post['users_country_group_id'], 
                    $post['users_country_group_type'], 
                    $post['users_nationality_group_type']
                );
            }
        }
        if (!count($selected)) {
            return false;
        }
        $values = str_repeat('( ?, ?, ?, ?, ?, ? ), ', (count($selected) / 6));
        $values = rtrim($values, ', ');
        $sql = 'INSERT INTO `users_documents_selected` ( 
            users_documents_id, 
            users_type_id, 
            users_nationality_id, 
            users_country_group_id, 
            users_country_group_type, 
            users_nationality_group_type
        ) VALUES ';
        return $this->db->query($sql . $values, $selected);
    }

    function clean_documents($post)
    {
        $selected = array(
            $post['users_nationality_group_type'],
            $post['users_country_group_type'],
            $post['users_country_group_id'], 
            $post['users_nationality_id'],
            $post['users_type_id']
        );
        $sql = '
          DELETE FROM `users_documents_selected` 
          WHERE users_nationality_group_type = ? 
            AND users_country_group_type = ? 
            AND users_country_group_id = ? 
            AND users_nationality_id = ?
            AND users_type_id = ?
        ';
        return $this->db->query($sql, $selected);
    }

    function insert_note($post)
    {
        $selected = array(
            $post['users_notes_id'],
            $post['users_type_id'], 
            $post['users_nationality_id'], 
            $post['users_country_group_id'], 
            $post['users_country_group_type'], 
            $post['users_nationality_group_type']
        );

        $sql = 'DELETE FROM `users_notes_selected` 
            WHERE users_notes_id = ? 
            AND users_type_id = ? 
            AND users_nationality_id = ? 
            AND users_country_group_id = ? 
            AND users_country_group_type = ? 
            AND users_nationality_group_type = ?';
        $this->db->query($sql, $selected);

        $sql = 'INSERT INTO `users_notes_selected` (
            users_notes_id, 
            users_type_id, 
            users_nationality_id, 
            users_country_group_id, 
            users_country_group_type, 
            users_nationality_group_type
        ) VALUES ( ?, ?, ?, ?, ?, ? )';
        $this->db->query($sql, $selected);
        return true;
    }

    function insert_notes($post)
    {
        $this->clean_notes($post);
        if (!isset($post['notes']) || !is_array($post['notes']) || count($post['notes']) < 1) {
            return false;
        }
        $selected = array();
        foreach ($post['notes'] as $users_notes_id) {
            if ((int) $users_notes_id > 0) {
                array_push($selected, 
                    $users_notes_id, 
                    $post['users_type_id'], 
                    $post['users_nationality_id'], 
                    $post['users_country_group_id'], 
                    $post['users_country_group_type'], 
                    $post['users_nationality_group_type']
                );
            }
        }
        if (!count($selected)) {
            return false;
        }
        $values = str_repeat('( ?, ?, ?, ?, ?, ? ), ', (count($selected) / 6));
        $values = rtrim($values, ', ');
        $sql = 'INSERT INTO `users_notes_selected` (
            users_notes_id, 
            users_type_id, 
            users_nationality_id, 
            users_country_group_id, 
            users_country_group_type, 
            users_nationality_group_type
        ) VALUES ';
        return $this->db->query($sql . $values, $selected);
    }

    function clean_notes($post)
    {
        $selected = array(
            $post['users_nationality_group_type'], 
            $post['users_country_group_type'],
            $post['users_country_group_id'], 
            $post['users_nationality_id'], 
            $post['users_type_id'] 
        );
        $sql = '
          DELETE FROM `users_notes_selected` 
          WHERE users_nationality_group_type = ? 
            AND users_country_group_type = ? 
            AND users_country_group_id = ? 
            AND users_nationality_id = ? 
            AND users_type_id = ?
        ';
        return $this->db->query($sql, $selected);
    }

    function delete_document($post)
    {
        $selected = array(
            $post['users_nationality_group_type'],
            $post['users_country_group_type'],
            $post['users_country_group_id'], 
            $post['users_nationality_id'],
            $post['users_type_id'],
            $post['users_documents_id']
        );
        $sql = '
          DELETE FROM `users_documents_selected` 
          WHERE users_nationality_group_type = ? 
            AND users_country_group_type = ? 
            AND users_country_group_id = ? 
            AND users_nationality_id = ? 
            AND users_type_id = ? 
            AND users_documents_id = ?
        ';
        return $this->db->query($sql, $selected);
    }

    function delete_note($post)
    {
        $selected = array(
            $post['users_nationality_group_type'],
            $post['users_country_group_type'],
            $post['users_country_group_id'], 
            $post['users_nationality_id'], 
            $post['users_type_id'], 
            $post['users_notes_id']
        );
        $sql = '
          DELETE FROM `users_notes_selected` 
          WHERE users_nationality_group_type = ? 
            AND users_country_group_type = ? 
            AND users_country_group_id = ? 
            AND users_nationality_id = ? 
            AND users_type_id = ? 
            AND users_notes_id = ?
        ';
        return $this->db->query($sql, $selected);
    }

    function delete_type($data)
    {
        $selected = array(
            $data['users_nationality_group_type'], 
            $data['users_country_group_type'],
            $data['users_country_group_id'], 
            $data['users_nationality_id'], 
            $data['users_type_id']
        );
        $append = '
            WHERE users_nationality_group_type = ? 
            AND users_country_group_type = ? 
            AND users_country_group_id = ? 
            AND users_nationality_id = ? 
            AND users_type_id = ?
        ';
        $this->db->query('DELETE FROM `users_type_countries_selected` ' . $append, $selected);
        $this->db->query('DELETE FROM `users_documents_selected` ' . $append, $selected);
        $this->db->query('DELETE FROM `users_notes_selected` ' . $append, $selected);
        $this->db->query('DELETE FROM `users_services_selected` ' . $append, $selected);
        $this->db->query('DELETE FROM `users_services_prices` ' . $append, $selected);
        $this->db->query('DELETE FROM `users_type_selected` ' . $append, $selected);
        return true;
    }

    function delete_entry($data)
    {
        $selected = array(
            $data['users_nationality_group_type'],
            $data['users_country_group_type'],
            $data['users_country_group_id'], 
            $data['users_nationality_id'], 
            $data['users_type_id'],
            $data['user_entry_id']
        );
        $append = '
            WHERE users_nationality_group_type = ? 
            AND users_country_group_type = ? 
            AND users_country_group_id = ? 
            AND users_nationality_id = ? 
            AND users_type_id = ? 
            AND user_entry_id = ?
        ';
        $this->db->query('DELETE FROM `users_services_selected`' . $append, $selected);
        $this->db->query('DELETE FROM `users_services_prices`' . $append, $selected);
        $this->db->query('DELETE FROM `users_type_selected`' . $append, $selected);
        return true;
    }

    function delete_service($data)
    {
        $selected = array(
            $data['users_nationality_group_type'],
            $data['users_country_group_type'],
            $data['users_country_group_id'],
            $data['users_nationality_id'],
            $data['users_type_id'],
            $data['user_entry_id'],
            $data['users_services_id']
        );
        $sql = '
            WHERE users_nationality_group_type = ? 
            AND users_country_group_type = ? 
            AND users_country_group_id = ? 
            AND users_nationality_id = ? 
            AND users_type_id = ? 
            AND user_entry_id = ? 
            AND users_services_id = ?
        ';
        $this->db->query('DELETE FROM `users_services_selected`' . $sql, $selected);
        $this->db->query('DELETE FROM `users_services_prices`' . $sql, $selected);
        return true;
    }

    function delete($id)
    {
        if (!$data = $this->fetch_item($id)) {
            return false;
        }
        $where = array(
            $data['users_nationality_group_type'],
            $data['users_country_group_type'],
            $data['users_destination_id'],
            $data['users_nationality_id']
        );
        $append = '
            WHERE users_nationality_group_type = ? 
            AND users_country_group_type = ? 
            AND users_country_group_id = ? 
            AND users_nationality_id = ?
        ';
        $this->db->query('DELETE FROM `users_type_countries_selected` ' . $append, $where);
        $this->db->query('DELETE FROM `users_services_selected` ' . $append, $where);
        $this->db->query('DELETE FROM `users_services_prices` ' . $append, $where);
        $this->db->query('DELETE FROM `users_documents_selected` ' . $append, $where);
        $this->db->query('DELETE FROM `users_notes_selected` ' . $append, $where);
        $this->db->query('DELETE FROM `users_type_selected` ' . $append, $where);
        $this->db->query('DELETE FROM `users_destinations_selected` WHERE users_destinations_selected_id = ?', array($id));
        return true;
    }
}

?>