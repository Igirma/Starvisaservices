<?php

class prices_model extends model
{
    function fetch_all()
    {
        $data = $this->db->query('SELECT * FROM `users_prices` ORDER BY users_price_order ASC');
        if (!isset($data[0])) {
            return false;
        }
        return $data;
    }

    function fetch($users_price_id)
    {
        $data = $this->db->query('SELECT * FROM `users_prices` WHERE users_price_id = ? LIMIT 1', array($users_price_id));
        if (!isset($data[0])) {
            return false;
        }
        return $data[0];
    }

    function add($post)
    {
        $row = $this->db->query('SELECT MAX(`users_price_order`) + 1 AS price_order FROM `users_prices` WHERE users_price_order != 999');
        $this->db->query('INSERT INTO `users_prices` ( users_price_order, users_price_vat, users_price_description, users_price_name ) VALUES ( ?, ?, ?, ? )', array(
            (isset($row[0]['price_order']) ? $row[0]['price_order'] : 0), 
            (isset($post['users_price_vat']) && $post['users_price_vat'] == 1 ? 1 : 0),
            $post['users_price_description'],
            $post['users_price_name']
        ));
        $id = $this->db->last_insert_id;
        return $id;
    }

    function edit($post, $id)
    {
		if($this->get_order($id) == 999)
			return $this->db->query('UPDATE `users_prices` SET users_price_vat = ?, users_price_description = ? WHERE users_price_id = ? LIMIT 1', array(
			(isset($post['users_price_vat']) && $post['users_price_vat'] == 1 ? 1 : 0),
			$post['users_price_description'],
			$id
			));
		else 
			return $this->db->query('UPDATE `users_prices` SET users_price_vat = ?, users_price_description = ?, users_price_name = ? WHERE users_price_id = ? LIMIT 1', array(
            (isset($post['users_price_vat']) && $post['users_price_vat'] == 1 ? 1 : 0),
            $post['users_price_description'],
            $post['users_price_name'],
            $id
			));
    }

    function order($direction, $order, $id)
    {
        switch ($direction) {
            case 'up':
                $from = $this->db->query('SELECT users_price_order, users_price_id FROM `users_prices` WHERE users_price_id = ? LIMIT 1', array($id));
                $to = $this->db->query('SELECT users_price_order, users_price_id FROM `users_prices` WHERE users_price_order < ? AND users_price_order != 999 ORDER BY users_price_order DESC LIMIT 1', array($order));
                if (isset($to[0])) {
                    $this->db->query('UPDATE `users_prices` SET users_price_order = ? WHERE users_price_id = ?', array($to[0]['users_price_order'], $from[0]['users_price_id']));
                    $this->db->query('UPDATE `users_prices` SET users_price_order = ? WHERE users_price_id = ?', array($from[0]['users_price_order'], $to[0]['users_price_id']));
                }
                break;

            case 'down':
                $from = $this->db->query('SELECT users_price_order, users_price_id FROM `users_prices` WHERE users_price_id = ? LIMIT 1', array($id));
                $to = $this->db->query('SELECT users_price_order, users_price_id FROM `users_prices` WHERE users_price_order > ? AND users_price_order != 999 ORDER BY users_price_order ASC LIMIT 1', array($order));
                if (isset($to[0])) {
                    $this->db->query('UPDATE `users_prices` SET users_price_order = ? WHERE users_price_id = ?', array($to[0]['users_price_order'], $from[0]['users_price_id']));
                    $this->db->query('UPDATE `users_prices` SET users_price_order = ? WHERE users_price_id = ?', array($from[0]['users_price_order'], $to[0]['users_price_id']));
                }
                break;
        }
    }

    function delete($id)
    {
        return $this->db->query('DELETE FROM `users_prices` WHERE users_price_id = ?', array($id));
    }
	
	function get_order($id) {
		$data = $this->db->query('SELECT users_price_order FROM `users_prices` WHERE users_price_id = ?', array($id));
		return $data[0]['users_price_order'];
	}
}

?>