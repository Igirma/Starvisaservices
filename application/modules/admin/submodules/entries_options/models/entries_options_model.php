<?php

class entries_options_model extends model
{
    function fetch_all()
    {
        $data = $this->db->query('SELECT * FROM `users_type_entries_options` ORDER BY entry_option_order ASC');
        if (!isset($data[0])) {
            return false;
        }
        return $data;
    }

    function fetch($entry_option_id)
    {
        $data = $this->db->query('SELECT * FROM `users_type_entries_options` WHERE entry_option_id = ? LIMIT 1', array($entry_option_id));
        if (!isset($data[0])) {
            return false;
        }
        return $data[0];
    }

    function add($post)
    {
        $row = $this->db->query('SELECT MAX(`entry_option_order`) + 1 AS option_order FROM `users_type_entries_options`');
        $this->db->query('INSERT INTO `users_type_entries_options` ( entry_option_order, entry_option_content, entry_option_name ) VALUES ( ?, ?, ? )', array(
            (isset($row[0]['option_order']) ? $row[0]['option_order'] : 0), 
            $post['entry_option_content'],
            $post['entry_option_name']
        ));
        $id = $this->db->last_insert_id;
        return $id;
    }

    function edit($post, $id)
    {
        return $this->db->query('UPDATE `users_type_entries_options` SET entry_option_content = ?, entry_option_name = ? WHERE entry_option_id = ? LIMIT 1', array(
            $post['entry_option_content'], 
            $post['entry_option_name'], 
            $id
        ));
    }

    function order($direction, $order, $id)
    {
        switch ($direction) {
            case 'up':
                $from = $this->db->query('SELECT entry_option_order, entry_option_id FROM `users_type_entries_options` WHERE entry_option_id = ? LIMIT 1', array($id));
                $to = $this->db->query('SELECT entry_option_order, entry_option_id FROM `users_type_entries_options` WHERE entry_option_order < ? ORDER BY entry_option_order DESC LIMIT 1', array($order));
                if (isset($to[0])) {
                    $this->db->query('UPDATE `users_type_entries_options` SET entry_option_order = ? WHERE entry_option_id = ?', array($to[0]['entry_option_order'], $from[0]['entry_option_id']));
                    $this->db->query('UPDATE `users_type_entries_options` SET entry_option_order = ? WHERE entry_option_id = ?', array($from[0]['entry_option_order'], $to[0]['entry_option_id']));
                }
                break;

            case 'down':
                $from = $this->db->query('SELECT entry_option_order, entry_option_id FROM `users_type_entries_options` WHERE entry_option_id = ? LIMIT 1', array($id));
                $to = $this->db->query('SELECT entry_option_order, entry_option_id FROM `users_type_entries_options` WHERE entry_option_order > ? ORDER BY entry_option_order ASC LIMIT 1', array($order));
                if (isset($to[0])) {
                    $this->db->query('UPDATE `users_type_entries_options` SET entry_option_order = ? WHERE entry_option_id = ?', array($to[0]['entry_option_order'], $from[0]['entry_option_id']));
                    $this->db->query('UPDATE `users_type_entries_options` SET entry_option_order = ? WHERE entry_option_id = ?', array($from[0]['entry_option_order'], $to[0]['entry_option_id']));
                }
                break;
        }
    }

    function delete($id)
    {
        return $this->db->query('DELETE FROM `users_type_entries_options` WHERE entry_option_id = ?', array($id));
    }
}

?>