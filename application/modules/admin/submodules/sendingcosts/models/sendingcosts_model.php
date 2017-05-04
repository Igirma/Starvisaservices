<?php

class sendingcosts_model extends model
{
	function fetch_all($language_id)
	{
		$sql = '
		SELECT DISTINCT *, `staffel`.active as staffel_active FROM `staffel`, `country`, `country_content`, `discount_type`
		WHERE `country`.country_id = `country_content`.country_id
		AND `staffel`.country_id = `country`.country_id
		AND `staffel`.discount_type_id = `discount_type`.discount_type_id
		AND `country_content`.language_id = ?
		ORDER BY `country_content`.name ASC, `staffel`.staffel_id
		';
		
		$data['sendingcosts'] = $this->db->query($sql, array($language_id));
		return $data;
	}
	
	function fetch($sendingcosts_id)
	{	
		$sql = '
		SELECT DISTINCT `staffel`.* FROM `staffel`, `country`
		INNER JOIN `country_content`
		ON `country`.country_id = `country_content`.country_id
		WHERE `staffel`.country_id = `country`.country_id
		AND `staffel`.country_id = ?
		ORDER BY `country_content`.name ASC, `staffel`.staffel_id
		';
		
		$r = $this->db->query($sql, array($sendingcosts_id));
		$data['sendingcosts'] = $r;

		return $data;
	}
	
	function add($post)
	{
		
		foreach($post['sendingcosts']['country_id'] as $k => $post_values){
			if($k == 0) continue;
			$sql = '
			SELECT * FROM `staffel`, `country`
			INNER JOIN `country_content`
			ON `country`.country_id = `country_content`.country_id
			WHERE `staffel`.country_id = `country`.country_id
			AND `staffel`.country_id = ?
			AND `staffel`.discount_top_value = ?
			ORDER BY `country_content`.name ASC
			';
			
			$r = $this->db->query($sql, array($post['sendingcosts']['country_id'][1], $post['sendingcosts']['discount_top_value'][$k]));
			if(isset($r) && count($r) > 0){
				$sql = '
				UPDATE `staffel`
				SET `staffel`.country_id		= :country_id,
				`staffel`.discount_type_id		= :discount_type_id,
				`staffel`.discount_value 		= :discount_value,
				`staffel`.discount_top_value 	= :discount_top_value
				WHERE `staffel`.country_id 		= :country_id
				';
				
				$this->db->query($sql, array(
					'country_id' 			=> $post['sendingcosts']['country_id'][1],
					'discount_type_id' 		=> ((isset($post['sendingcosts']['discount_type_id'][$k]))?$post['sendingcosts']['discount_type_id'][$k]:1),
					'discount_value' 		=> $post['sendingcosts']['discount_value'][$k],
					'discount_top_value' 	=> $post['sendingcosts']['discount_top_value'][$k]
				));
			}
			else{
				if($post['sendingcosts']['discount_value'][$k] != '' || $post['sendingcosts']['discount_top_value'][$k] != ''){
					$sql = '
						INSERT INTO `staffel`
						(
							country_id,
							discount_type_id,
							discount_value,
							discount_top_value,
							active
						)
						VALUES
						(
							:country_id,
							:discount_type_id,
							:discount_value,
							:discount_top_value,
							:active
						)';
					
					$this->db->query($sql, array(
						'country_id' 			=> $post['sendingcosts']['country_id'][1],
						'discount_type_id' 		=> ((isset($post['sendingcosts']['discount_type_id'][$k]))?$post['sendingcosts']['discount_type_id'][$k]:1),
						'discount_value' 		=> $post['sendingcosts']['discount_value'][$k],
						'discount_top_value' 	=> $post['sendingcosts']['discount_top_value'][$k],
						'active'				=> 1
					));
				}
			}
		}
		return $post['sendingcosts']['country_id'][1];
	}
	
	function edit($post)
	{
		$coutry_id = 0;
		foreach($post['sendingcosts']['country_id'] as $k => $post_values){
			if($k == 0) continue;
			if($k > 0 && $coutry_id == 0) $coutry_id = $post_values;
			$sql = '
			SELECT * FROM `staffel`, `country`
			INNER JOIN `country_content`
			ON `country`.country_id = `country_content`.country_id
			WHERE `staffel`.country_id = `country`.country_id
			AND `staffel`.staffel_id = ?
			ORDER BY `country_content`.name ASC
			';
			
			$r = $this->db->query($sql, array($k));
			if(isset($r) && count($r) > 0){
				$sql = '
				UPDATE `staffel`
				SET 
				`staffel`.discount_type_id		= :discount_type_id,
				`staffel`.discount_value 		= :discount_value,
				`staffel`.discount_top_value 	= :discount_top_value,
				`staffel`.country_id 			= :country_id
				WHERE `staffel`.staffel_id 		= :staffel_id
				';
				
				$this->db->query($sql, array(
					'staffel_id' 			=> $k,
					'country_id' 			=> $coutry_id,
					'discount_type_id' 		=> ((isset($post['sendingcosts']['discount_type_id'][$k]))?$post['sendingcosts']['discount_type_id'][$k]:1),
					'discount_value' 		=> $post['sendingcosts']['discount_value'][$k],
					'discount_top_value' 	=> $post['sendingcosts']['discount_top_value'][$k]
				));
			
			}
			else{
				if($post['sendingcosts']['discount_value'][$k] != '' || $post['sendingcosts']['discount_top_value'][$k] != ''){
					$sql = '
						INSERT INTO `staffel`
						(
							country_id,
							discount_type_id,
							discount_value,
							discount_top_value,
							active
						)
						VALUES
						(
							:country_id,
							:discount_type_id,
							:discount_value,
							:discount_top_value,
							:active
						)';
					
					$this->db->query($sql, array(
						'country_id' 			=> $coutry_id,
						'discount_type_id' 		=> ((isset($post['sendingcosts']['discount_type_id'][$k]))?$post['sendingcosts']['discount_type_id'][$k]:1),
						'discount_value' 		=> $post['sendingcosts']['discount_value'][$k],
						'discount_top_value' 	=> $post['sendingcosts']['discount_top_value'][$k],
						'active'				=> 1
					));
				}
			}
		}
		return $coutry_id;
	}
	
	function update_overview($post)
	{
		foreach($post['active'] as $k => $v)
		{
			$this->db->query('UPDATE `staffel` SET `staffel`.active = ? WHERE `staffel`.staffel_id = ? LIMIT 1', array($v, $k));
		}
	}
		
	function delete($id)
	{
		$this->db->query('DELETE FROM `staffel` WHERE `staffel`.staffel_id = ?', array($id));
		
	}

	function fetch_countries($language_id, $country_id = 0)
	{
		
		if($country_id == 0){
			$sql = '
			SELECT * FROM `country`
			INNER JOIN `country_content`
			ON `country`.country_id = `country_content`.country_id
			WHERE `country_content`.language_id = :language_id
			AND `country`.country_id NOT IN 
				(SELECT DISTINCT `staffel`.country_id FROM `staffel`, `country`, `country_content`, `discount_type`
				WHERE `country`.country_id = `country_content`.country_id
				AND `staffel`.country_id = `country`.country_id
				AND `staffel`.discount_type_id = `discount_type`.discount_type_id
				AND `country_content`.language_id = :language_id
				ORDER BY `country_content`.name ASC)
			ORDER BY `country_content`.name ASC
			';
			
			$data = $this->db->query($sql, array('language_id' => $language_id));
		}
		else{
			$sql = '
			SELECT * FROM `country`
			INNER JOIN `country_content`
			ON `country`.country_id = `country_content`.country_id
			WHERE `country_content`.language_id = :language_id
			AND (`country`.country_id NOT IN 
				(SELECT DISTINCT `staffel`.country_id FROM `staffel`, `country`, `country_content`, `discount_type`
				WHERE `country`.country_id = `country_content`.country_id
				AND `staffel`.country_id = `country`.country_id
				AND `staffel`.discount_type_id = `discount_type`.discount_type_id
				AND `country_content`.language_id = :language_id
				ORDER BY `country_content`.name ASC)
			OR `country`.country_id = :country_id)
			ORDER BY `country_content`.name ASC
			';
			
			$data = $this->db->query($sql, array('language_id' => $language_id, 'country_id' => $country_id));
			
		}		
		return $data;
	}

	function fetch_discount_type()
	{
		
		$sql = '
		SELECT * FROM `discount_type`
		ORDER BY `discount_type`.discount_type_id ASC
		';
		
		$data = $this->db->query($sql);
		return $data;
	}	
	
}

?>