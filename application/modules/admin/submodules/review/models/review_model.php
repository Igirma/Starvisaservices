<?php

class review_model extends model
{
	function fetch_all($filter = false)
	{	
		$sql = '
			SELECT *
			FROM `review`, `review_content`
			WHERE `review`.review_id = `review_content`.review_id
			ORDER BY `review`.review_date DESC
		';
		
		$data = $this->db->query($sql, array('language_id' => $this->config->item('default_language')));
		
		if(isset($data) && count($data) > 0){
			foreach($data as $k => $obj){
				$sql = '
				SELECT *
				FROM `product`
				INNER JOIN `product_content`
				ON `product_content`.product_id = `product`.product_id
				WHERE `product`.product_id = ?
				AND `product_content`.language_id = ?
				';
				
				$r = $this->db->query($sql, array($obj['product_id'], $this->config->item('default_language')));
				if(isset($r) && count($r) > 0) $data[$k]['product'] = $r[0]['title'];
				else $data[$k]['product'] = '';
			}
		}
		return $data;
	}
	
	function fetch($review_id)
	{
		$sql = '
			SELECT *
			FROM `review`, `review_content`
			WHERE `review`.review_id = `review_content`.review_id
			AND `review_content`.review_id = :review_id
		';
		
		$data = $this->db->query($sql, array('review_id' => $review_id));
		
		if(isset($data) && count($data) > 0){
			foreach($data as $k => $obj){
				$sql = '
				SELECT *
				FROM `product`
				INNER JOIN `product_content`
				ON `product_content`.product_id = `product`.product_id
				WHERE `product`.product_id = ?
				AND `product_content`.language_id = ?
				';
				
				$r = $this->db->query($sql, array($obj['product_id'], $this->config->item('default_language')));
				if(isset($r) && count($r) > 0) $data[$k]['product'] = $r[0]['title'];
				else $data[$k]['product'] = '';
			}
		}
		return $data[0];
	}
	
	function edit($post, $review_id)
	{
		$sql = '
			UPDATE `review`
			SET `review`.active = :active
			WHERE `review`.review_id = :review_id
		';
		
		$this->db->query($sql, array(
			'active' => $post['active'],
			'review_id' => $review_id
		));
	}
	
	function delete($review_id)
	{
		$sql = '
		DELETE FROM `review_content`
		WHERE `review_content`.review_id = :review_id
		LIMIT 1
		';
		
		$this->db->query($sql, array('review_id' => $review_id));
		$sql = '
		DELETE FROM `review`
		WHERE `review`.review_id = :review_id
		LIMIT 1
		';
		
		$this->db->query($sql, array('review_id' => $review_id));
	}
	
	
	function update_overview($post)
	{
		if(isset($post['active']))
		{
			foreach($post['active'] as $k => $v)
			{
				$this->db->query('UPDATE `review` SET `review`.active = ? WHERE `review`.review_id = ?', array($v, $k));
			}
		}
	}
	
}

?>