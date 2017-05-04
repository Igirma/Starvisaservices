<?php

class delivery_model extends model
{
    function fetch_all()
    {
        $rows = $this->db->query('SELECT * FROM `users_delivery_methods` ORDER BY delivery_method_id DESC');
        if (!isset($rows[0])) {
            return false;
        }
        return $rows;
    }
    
    function fetch($delivery_method_id)
    {
        $sql = 'SELECT * FROM `users_delivery_methods` WHERE `users_delivery_methods`.delivery_method_id = ? LIMIT 1';
        $row = $this->db->query($sql, array($delivery_method_id));
        if (!isset($row[0])) {
            return false;
        }
        return $row[0];
    }

    function add($post)
    {
        $sql = 'INSERT INTO `users_delivery_methods` ( delivery_method_name, delivery_method_price ) VALUES (?, ?)';
        $this->db->query($sql, array($post['delivery_method_name'], prepare_price($post['delivery_method_price'])));
        return $this->db->last_insert_id;
    }

    function edit($post, $id)
    {
        $sql = 'UPDATE `users_delivery_methods` SET delivery_method_name = ?, delivery_method_price = ? WHERE delivery_method_id = ? LIMIT 1';
        return $this->db->query($sql, array($post['delivery_method_name'], prepare_price($post['delivery_method_price']), $id));
    }

    function delete($id)
    {
        return $this->db->query('DELETE FROM `users_delivery_methods` WHERE delivery_method_id = ?', array($id));
    }
}

?>