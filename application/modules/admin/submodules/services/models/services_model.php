<?php

class services_model extends model
{
    function fetch_all()
    {
        $data = $this->db->query('SELECT * FROM `users_services` ORDER BY users_services_order ASC');
        if (!isset($data[0])) {
            return false;
        }
        return $data;
    }

    function fetch($users_services_id)
    {
        $data = $this->db->query('SELECT * FROM `users_services` WHERE users_services_id = ? LIMIT 1', array($users_services_id));
        if (!isset($data[0])) {
            return false;
        }
        return $data[0];
    }

    function add($post)
    {
        $row = $this->db->query('SELECT MAX(`users_services_order`) + 1 AS services_order FROM `users_services`');
        $this->db->query('INSERT INTO `users_services` ( users_services_order, users_services_name ) VALUES ( ?, ? )', array(
            (isset($row[0]['services_order']) ? $row[0]['services_order'] : 0), 
            $post['users_services_name']
        ));
        $id = $this->db->last_insert_id;
        return $id;
    }

    function edit($post, $id)
    {
        return $this->db->query('UPDATE `users_services` SET users_services_name = ? WHERE users_services_id = ? LIMIT 1', array($post['users_services_name'], $id));
    }

    function order($direction, $order, $id)
    {
        switch ($direction) {
            case 'up':
                $from = $this->db->query('SELECT users_services_order, users_services_id FROM `users_services` WHERE users_services_id = ? LIMIT 1', array($id));
                $to = $this->db->query('SELECT users_services_order, users_services_id FROM `users_services` WHERE users_services_order < ? ORDER BY users_services_order DESC LIMIT 1', array($order));
                if (isset($to[0])) {
                    $this->db->query('UPDATE `users_services` SET users_services_order = ? WHERE users_services_id = ?', array($to[0]['users_services_order'], $from[0]['users_services_id']));
                    $this->db->query('UPDATE `users_services` SET users_services_order = ? WHERE users_services_id = ?', array($from[0]['users_services_order'], $to[0]['users_services_id']));
                }
                break;

            case 'down':
                $from = $this->db->query('SELECT users_services_order, users_services_id FROM `users_services` WHERE users_services_id = ? LIMIT 1', array($id));
                $to = $this->db->query('SELECT users_services_order, users_services_id FROM `users_services` WHERE users_services_order > ? ORDER BY users_services_order ASC LIMIT 1', array($order));
                if (isset($to[0])) {
                    $this->db->query('UPDATE `users_services` SET users_services_order = ? WHERE users_services_id = ?', array($to[0]['users_services_order'], $from[0]['users_services_id']));
                    $this->db->query('UPDATE `users_services` SET users_services_order = ? WHERE users_services_id = ?', array($from[0]['users_services_order'], $to[0]['users_services_id']));
                }
                break;
        }
    }

    function delete($id)
    {
        $this->db->query('DELETE FROM `users_services_selected` WHERE users_services_id = ?', array($id));
        $this->db->query('DELETE FROM `users_services` WHERE users_services_id = ?', array($id));
        return true;
    }
}

?>