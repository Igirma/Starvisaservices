<?php

class discountcodes_model extends model
{
	function fetch_all()
	{
		$sql = '
		SELECT * FROM `discountcodes`
		WHERE `discountcodes`.site = :site		
		ORDER BY `discountcodes`.discountcodes_date DESC
		';
		
		$data['discountcodes'] = $this->db->query($sql, array('site' => $_SESSION['site_name']));
		
		if(isset($data['discountcodes']) && $data['discountcodes'] && count($data['discountcodes']) > 0){
			foreach($data['discountcodes'] as $k => $item){
				
				$sql = '
					SELECT *
					FROM `discountcodes_content`
					WHERE `discountcodes_content`.discountcodes_id = ?
					ORDER BY `discountcodes_content`.discountcodes_content_id ASC
				';
			
				$r = $this->db->query($sql, array($item['discountcodes_id']));
				$data['discountcodes'][$k]['nr_codes'] = count($r);
				
				$sql = '
					SELECT *
					FROM `discountcodes_content`
					WHERE `discountcodes_content`.discountcodes_id = ?
					AND `discountcodes_content`.active = 1
					ORDER BY `discountcodes_content`.discountcodes_content_id ASC
				';
			
				$r = $this->db->query($sql, array($item['discountcodes_id']));
				$data['discountcodes'][$k]['nr_codes_active'] = count($r);
				
			}
		}
		return $data;
	}
	
	function fetch($discountcodes_id)
	{	
		$sql = '
		SELECT *
		FROM `discountcodes`
		WHERE `discountcodes`.discountcodes_id = ?
		';
		
		$r = $this->db->query($sql, array($discountcodes_id));
		$data['discountcodes'] = $r[0];

		$sql = '
			SELECT *
			FROM `discountcodes_content`
			WHERE `discountcodes_content`.discountcodes_id = ?
			ORDER BY `discountcodes_content`.discountcodes_content_id ASC
		';
	
		$data['codes'] = $this->db->query($sql, array($data['discountcodes']['discountcodes_id']));
		
		return $data;
	}
	
	function add($post)
	{
		$sql = '
		INSERT INTO `discountcodes`
		(
			discountcodes_date,
			title,
			discount_value,
			discount_percent,
			site
		)
		VALUES
		(
			:discountcodes_date,
			:title,
			:discount_value,
			:discount_percent,
			:site
		)';
			
		
		$this->db->query($sql, array(
				'discountcodes_date' 	=> strtotime($post['discountcodes']['discountcodes_date']),
				'title' 				=> $post['discountcodes']['title'],
				'discount_value' 		=> $post['discountcodes']['discount_value'],
				'discount_percent' 		=> $post['discountcodes']['discount_percent'],
				'site' 					=> $_SESSION['site_name']
		));
		
		$id = $this->db->last_insert_id;

		$i = 0; 
		while ($i < $post['discountcodes']['code_nr']) {
			$code_val = substr(md5(rand()), 0, 10);
			$in_system = $this->db->query("SELECT * FROM `` WHERE `discountcodes_content`.code = '".$code_val."'");
		
			if(!in_array($code_val, $code) && (!isset($in_system) || !$in_system)){
				$code[$i] = $code_val;
				$i++;
			}
		}
		
		for($k = 0; $k < $post['discountcodes']['code_nr']; $k++){
			$sql = '
			INSERT INTO `discountcodes_content`
			(
				discountcodes_id,
				code
			)
			VALUES
			(
				:discountcodes_id,
				:code
			)';
				
			$this->db->query($sql, array(
				'discountcodes_id' 	=> $id,
				'code' 				=> $code[$k]
			));
		}
		
		return $id;
	}
	
	function edit($post, $id, $language_id)
	{
		
		$sql = '
		UPDATE `discountcodes`, `discountcodes_content`
		SET
			`discountcodes`.discountcodes_date 			= :discountcodes_date,
			`discountcodes`.title 				= :title,
			`discountcodes`.discount_value 		= :discount_value,
			`discountcodes`.discount_percent 	= :discount_percent
		WHERE `discountcodes`.discountcodes_id 			= :discountcodes_id
		AND `discountcodes_content`.discountcodes_id 	= :discountcodes_id
		';
		
		$this->db->query($sql, array(
			'discountcodes_date' 	=> strtotime($post['discountcodes']['discountcodes_date']),
			'title' 				=> ucfirst($post['discountcodes']['title']),
			'discount_value' 		=> $post['discountcodes']['discount_value'],
			'discount_percent' 		=> $post['discountcodes']['discount_percent'],
			'discountcodes_id' 	=> $id
		));
		echo $this->db->error;
	
		$i = 0; 
		while ($i < $post['discountcodes']['code_nr']) {
			$code_val = substr(md5(rand()), 0, 10);
			$in_system = $this->db->query("SELECT * FROM `` WHERE `discountcodes_content`.code = '".$code_val."'");
		
			if(!in_array($code_val, $code) && (!isset($in_system) || !$in_system)){
				$code[$i] = $code_val;
				$i++;
			}
		}
		
		for($k = 0; $k < $post['discountcodes']['code_nr']; $k++){
			$sql = '
			INSERT INTO `discountcodes_content`
			(
				discountcodes_id,
				code
			)
			VALUES
			(
				:discountcodes_id,
				:code
			)';
				
			$this->db->query($sql, array(
				'discountcodes_id' 	=> $id,
				'code' 				=> $code[$k]
			));
		}
		
		
		foreach($post['active'] as $k => $v)
		{
			$this->db->query('UPDATE `discountcodes_content` SET `discountcodes_content`.active = ? WHERE `discountcodes_content`.discountcodes_content_id = ? LIMIT 1', array($v, $k));
		}
		
	}
	
	function update_overview($post)
	{
		foreach($post['active'] as $k => $v)
		{
			$this->db->query('UPDATE `discountcodes` SET `discountcodes`.active = ? WHERE `discountcodes`.discountcodes_id = ? LIMIT 1', array($v, $k));
		}
	}
	
	function delete($id)
	{
		$sql = 'SELECT * FROM `discountcodes` WHERE `discountcodes`.discountcodes_id = ?';
		$r = $this->db->query($sql, array($id));

		$this->db->query('DELETE FROM `discountcodes` WHERE `discountcodes`.discountcodes_id = ?', array($id));
		$this->db->query('DELETE FROM `discountcodes_content` WHERE `discountcodes_content`.discountcodes_id = ?', array($id));
		
	}

	function delete_code($code_id){
		$this->db->query('DELETE FROM `discountcodes_content` WHERE `discountcodes_content`.discountcodes_content_id = ? LIMIT 1', array($code_id));
	}
		
}

?>