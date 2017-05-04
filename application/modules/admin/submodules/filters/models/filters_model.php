<?php
class filters_model extends model
{

	function fetch_all($controller,$parent_id = 0)
	{
		$return = array();
		
		$sql = '
		SELECT * 
		FROM `filter`, `filter_heading` 
		WHERE `filter`.filter_id = `filter_heading`.filter_id 
		AND `filter`.controller = :controller
		AND `filter_heading`.language_id = :lang 
		ORDER BY `filter`.order ASC
		';
		
		$return = $this->db->query($sql, array('lang' => $this->config->item('default_language'), 'controller' => $controller));
		
		return $return;
	}
	
	function fetch($filter_id, $language_id)
	{	
		$sql = '
			SELECT *
			FROM `filter`
			INNER JOIN `filter_heading`
			ON `filter_heading`.filter_id = `filter`.filter_id
			WHERE `filter`.filter_id = ?
			AND `filter_heading`.language_id = ?
			ORDER BY `filter`.order
		';
		
		$r = $this->db->query($sql, array($filter_id, $language_id));
		$data['filters'] = $r[0];
		
		$sql = '
			SELECT *
			FROM `filter_heading`
			INNER JOIN `filter_item`
			ON `filter_item`.filter_heading_id = `filter_heading`.filter_heading_id
			WHERE `filter_heading`.language_id = ?
			AND `filter_heading`.filter_heading_id = ?
			ORDER BY `filter_item`.filter_item_id
		';
	
		$data['filters']['options'] = $this->db->query($sql, array($language_id, $data['filters']['filter_heading_id']));
	
		return $data;
	}
	
	function update_overview($post)
	{
		foreach($post['active'] as $k => $v)
		{
			$this->db->query('UPDATE `filter` SET `filter`.active = ? WHERE `filter`.filter_id = ? LIMIT 1', array($v, $k));
		}
	}
	function add($post, $controller)
	{
		$sql = '
			SELECT MAX(`filter`.order) AS `order`
			FROM `filter` WHERE `filter`.controller = ?
		';
					
		$order = $this->db->query($sql, array($controller));
		if(!$order[0]['order']) $order[0]['order'] = 0;
		
		$sql = '
		INSERT INTO `filter`
		(
			`filter`.order,
			`filter`.controller
		)
		VALUES
		(
			:order,
			:controller
		)
		';
		
		$this->db->query($sql, array(
			'order'			=> $order[0]['order'] + 1,
			'controller'	=> $controller
		));
		
		$filter_id = $this->db->last_insert_id;
		
		$sql = '
			INSERT INTO `filter_heading`
			(
				`filter_heading`.sub_active,
				`filter_heading`.filter_id,
				`filter_heading`.language_id,
				`filter_heading`.title
			)
			VALUES
			(
				:sub_active,
				:filter_id,
				:language_id,
				:title
			)
		';
		
		$this->db->query($sql, array(
			'sub_active' 	=> $post['filter']['sub_active'],
			'filter_id' 	=> $filter_id,
			'language_id' 	=> $this->config->item('default_language'),
			'title' 		=> ucfirst($post['filter']['title'])
		));
		$filter_heading_ids[] = $this->db->last_insert_id;
		
		$languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array($this->config->item('default_language')));
		
		foreach($languages as $language)
		{				
			$sql = '
			INSERT INTO `filter_heading`
			(
				filter_id,
				language_id
			)
			VALUES
			(
				:filter_id,
				:language_id
			)';
			
			$this->db->query($sql, array(
				'filter_id' 	=> $filter_id,
				'language_id' 	=> $language['language_id']
			));
			$filter_heading_ids[] = $this->db->last_insert_id;
		}
		$arr_ids = '';
		foreach($post['filter']['options'] as $k => $option){
				if($option != ''){
					$arr_ids .= " AND `filter`.filter_id<>".$k;

					foreach($filter_heading_ids as $l => $filter_heading_id){
						if($l == 0){
							$sql = '
							INSERT INTO `filter_item`
							(
								`filter_item`.filter_heading_id,
								`filter_item`.title
							)
							VALUES
							(
								:filter_heading_id,
								:title
							)
							';
							
							$this->db->query($sql, array(
								'filter_heading_id'		=> $filter_heading_id,
								'title'					=> ucfirst($option)
							));
							$filter_item = $this->db->last_insert_id;
							$sql = '
								UPDATE `filter_item`
								SET
									`filter_item`.filter_item_identify 		= :filter_item_identify
								WHERE `filter_item`.filter_item_id 			= :filter_id
								';
								
								$this->db->query($sql, array(
									'filter_item_identify' 	=> $filter_item,
									'filter_id' 			=> $filter_item
								));
							
						}else{
							$sql = '
							INSERT INTO `filter_item`
							(
								`filter_item`.filter_heading_id,
								`filter_item`.filter_item_identify
							)
							VALUES
							(
								:filter_heading_id,
								:filter_item_identify
							)
							';
							
							$this->db->query($sql, array(
								'filter_heading_id'		=> $filter_heading_id,
								'filter_item_identify'	=> $filter_item
							));
							
						}					
				}
			}
		}
		return $filter_id;
	}
	
	function edit($post, $id, $language_id, $controller)
	{
		
		$sql = '
		UPDATE `filter`, `filter_heading`
		SET
			`filter_heading`.sub_active = :sub_active,
			`filter_heading`.title 		= :title
		WHERE `filter`.filter_id 		= :filter_id
		AND `filter`.filter_id 		= `filter_heading`.filter_id
		AND `filter_heading`.language_id 	= :language_id
		';
		
		$this->db->query($sql, array(
			'sub_active' 	=> $post['filter']['sub_active'],
			'title' 		=> ucfirst($post['filter']['title']),
			'filter_id' 	=> $id,
			'language_id' 	=> $language_id
		));
		$arr_ids = '';
		foreach($post['filter']['options'] as $k => $option){
			if($option != ''){
				
				$sql = 'SELECT `filter_item`.filter_item_id FROM `filter`,`filter_item`, `filter_heading`
					WHERE `filter_heading`.filter_heading_id = `filter_item`.filter_heading_id
					AND `filter_heading`.filter_id = `filter`.filter_id
					AND `filter_heading`.language_id = ?
					AND `filter`.filter_id = ?
					AND `filter_item`.filter_item_id = ?';
				
				$current = $this->db->query($sql, array($language_id, $id, $k));
				
				if($current && count($current) == 1){
					
					$sql = '
					UPDATE `filter_heading`, `filter_item`
					SET
						`filter_item`.title 					= :title
					WHERE `filter_heading`.filter_heading_id 	= `filter_item`.filter_heading_id
					AND `filter_item`.filter_item_id 			= :filter_item_id
					AND `filter_heading`.language_id 			= :language_id
					';
					
					$this->db->query($sql, array(
						'title' 			=> ucfirst($option),
						'filter_item_id'	=> $k,
						'language_id' 		=> $language_id
					));
					$arr_ids .= " AND `filter_item`.filter_item_identify <> ".$k;
				}else{	
					if($this->config->item('default_language') == $language_id){
						$sql = '
							SELECT *
							FROM `filter_heading` WHERE `filter_heading`.filter_id = ?
							ORDER BY `filter_heading`.language_id
						';			
						$filter_heading_ids = $this->db->query($sql, array($id));
						
						foreach($filter_heading_ids as $l => $filter_heading_id){
							
							if($l == 0){
								$sql = '
								INSERT INTO `filter_item`
								(
									`filter_item`.filter_heading_id,
									`filter_item`.title
								)
								VALUES
								(
									:filter_heading_id,
									:title
								)
								';
								
								$this->db->query($sql, array(
									'filter_heading_id'		=> $filter_heading_id['filter_heading_id'],
									'title'					=> ucfirst($option)
								));
								$filter_item = $this->db->last_insert_id;
								$arr_ids .= " AND `filter_item`.filter_item_identify <> ".$filter_item;
								$sql = '
									UPDATE `filter_item`
									SET
										`filter_item`.filter_item_identify 		= :filter_item_identify
									WHERE `filter_item`.filter_item_id 			= :filter_id
									';
									
									$this->db->query($sql, array(
										'filter_item_identify' 	=> $filter_item,
										'filter_id' 			=> $filter_item
									));
								
							}else{
								$sql = '
								INSERT INTO `filter_item`
								(
									`filter_item`.filter_heading_id,
									`filter_item`.filter_item_identify
								)
								VALUES
								(
									:filter_heading_id,
									:filter_item_identify
								)
								';
								
								$this->db->query($sql, array(
									'filter_heading_id'		=> $filter_heading_id['filter_heading_id'],
									'filter_item_identify'	=> $filter_item
								));
								
							}	
						}
					}
				}
			}
		}						
		
		if($this->config->item('default_language') == $language_id){
			$sql = 'SELECT * FROM `filter_heading`,`filter` WHERE `filter_heading`.filter_id = ? 
					AND `filter`.filter_id 		= `filter_heading`.filter_id
					';			
			$filter_heading_ids = $this->db->query($sql, array($id));
			$heading_sql = '';
			foreach($filter_heading_ids as $l => $filter_heading_id){
				if($heading_sql != '') $heading_sql .=  " OR ";
				$heading_sql .= "`filter_heading`.filter_heading_id = ".$filter_heading_id['filter_heading_id'];
			}	
			$heading_sql = "(".$heading_sql.")";
			$sql = 'SELECT `filter_item`.filter_item_identify FROM `filter_item`, `filter_heading`
						WHERE `filter_heading`.filter_heading_id=`filter_item`.filter_heading_id AND '.$heading_sql.' '.$arr_ids;
			
			$old_options = $this->db->query($sql);
			if($old_options && count($old_options)> 0){
				foreach($old_options as $option){
					$this->delete_product_suboptions($option['filter_item_identify']);
				}
			}	
		}
	}
	
	function delete($filter_id)
	{
		$this->db->query('DELETE FROM `filter` WHERE `filter`.filter_id = ?', array($filter_id));
		$this->delete_product_headings($filter_id);
		$this->db->query('DELETE FROM `filter_heading` WHERE `filter_heading`.filter_id = ?', array($filter_id));			
		
	}
	function delete_product_suboptions($filter_item_identify)
	{
		$sql = 'SELECT * FROM `filter_item` WHERE `filter_item`.filter_item_identify = ?';			
		$filter_heading_ids = $this->db->query($sql, array($filter_item_identify));
		
		foreach($filter_heading_ids as $l => $filter_heading_id){
			$this->db->query('DELETE FROM `filter_item_saved` WHERE `filter_item_saved`.filter_item_id = ?', array($id));
		}
		$this->db->query('DELETE FROM `filter_item` WHERE `filter_item`.filter_item_identify = ?', array($filter_item_identify));		
		
	}
	function delete_product_headings($filter_id)
	{
		$sql = 'SELECT * FROM `filter_heading` WHERE `filter_heading`.filter_id = ?';			
		$filter_heading_ids = $this->db->query($sql, array($filter_id));
		foreach($filter_heading_ids as $l => $filter_heading_id){
			$this->db->query('DELETE FROM `filter_item` WHERE `filter_item`.filter_heading_id = ?', array($filter_heading_id['filter_heading_id']));	
		}
	}
	function order($direction, $current_order, $controller, $filter_id)
	{
		switch($direction)
		{
			case 'up':
				$from = $this->db->query('SELECT `filter`.order, `filter`.filter_id FROM `filter` WHERE `filter`.filter_id = ?', array($filter_id));
				$to = $this->db->query('SELECT `filter`.order, `filter`.filter_id FROM `filter` WHERE `filter`.order < ? AND `filter`.controller = ? ORDER BY `filter`.order DESC', array($current_order, $controller));
				
				if(!empty($to))
				{
					$this->db->query('UPDATE `filter` SET `filter`.order = ? WHERE `filter`.filter_id = ?', array($to[0]['order'], $from[0]['filter_id']));
					$this->db->query('UPDATE `filter` SET `filter`.order = ? WHERE `filter`.filter_id = ?', array($from[0]['order'], $to[0]['filter_id']));
				}
			break;
				
			case 'down':
				$from = $this->db->query('SELECT `filter`.order, `filter`.filter_id FROM `filter` WHERE `filter`.filter_id = ?', array($filter_id));
				$to = $this->db->query('SELECT `filter`.order, `filter`.filter_id FROM `filter` WHERE `filter`.order > ? AND `filter`.controller = ? ORDER BY `filter`.order ASC', array($current_order, $controller));
				
				if(!empty($to))
				{
					$this->db->query('UPDATE `filter` SET `filter`.order = ? WHERE `filter`.filter_id = ?', array($to[0]['order'], $from[0]['filter_id']));
					$this->db->query('UPDATE `filter` SET `filter`.order = ? WHERE `filter`.filter_id = ?', array($from[0]['order'], $to[0]['filter_id']));
				}
			break;
		}
	}
	
	
}
?>