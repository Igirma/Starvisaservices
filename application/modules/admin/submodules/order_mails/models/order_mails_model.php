<?php

class order_mails_model extends model
{
	function fetch_all()
	{
		$sql = '
		SELECT * FROM `order_mails`
		INNER JOIN `order_mails_content`
		ON `order_mails`.order_mails_id = `order_mails_content`.order_mails_id
		WHERE `order_mails_content`.language_id = ?
		ORDER BY `order_mails`.order_mails_id ASC
		';
		
		$data['order_mails'] = $this->db->query($sql, array($this->config->item('default_language')));
		return $data;
	}
	
	function fetch($order_mails_id, $language_id)
	{	
		$sql = '
		SELECT *
		FROM `order_mails`,`order_mails_content`
		WHERE `order_mails_content`.order_mails_id = `order_mails`.order_mails_id
		AND `order_mails`.order_mails_id = ?
		AND `order_mails_content`.language_id = ?
		';
		
		$r = $this->db->query($sql, array($order_mails_id, $language_id));
		$data['order_mails'] = $r[0];

		return $data;
	}
	
	function add($post)
	{
		$sql = '
		INSERT INTO `order_mails`
		()
		VALUES
		()';
			
		
		$this->db->query($sql);
		
		$id = $this->db->last_insert_id;

		$sql = '
		INSERT INTO `order_mails_content`
		(
			order_mails_id,
			language_id,
			client_subject,
			client_content,
			client_fromname,
			admin_subject,
			admin_content,
			admin_fromname
		)
		VALUES
		(
			:order_mails_id,
			:language_id,
			:client_subject,
			:client_content,
			:client_fromname,
			:admin_subject,
			:admin_content,
			:admin_fromname
		)';
			
		$this->db->query($sql, array(
				'order_mails_id' 	=> $id,
				'language_id' 	=> $this->config->item('default_language'),
				'client_subject' 		=> $post['order_mails']['client_subject'],
				'client_content' 	=> $post['order_mails']['client_content'],
				'client_fromname' 		=> $post['order_mails']['client_fromname'],
				'admin_subject' 	=> $post['order_mails']['admin_subject'],
				'admin_content' 	=> $post['order_mails']['admin_content'],
				'admin_fromname' 	=> $post['order_mails']['admin_fromname']
		));
		
		$languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array($this->config->item('default_language')));
		
		foreach($languages as $language)
		{				
			$sql = '
			INSERT INTO `order_mails_content`
			(
				order_mails_id,
				language_id
			)
			VALUES
			(
				:order_mails_id,
				:language_id
			)';
			
			$this->db->query($sql, array(
				'order_mails_id' 	=> $id,
				'language_id' 	=> $language['language_id']
			));
		}

		return $id;
	}
	
	function edit($post, $id, $language_id)
	{
		
		$sql = '
		UPDATE `order_mails`, `order_mails_content`
		SET
			`order_mails_content`.client_subject 	= :client_subject,
			`order_mails_content`.client_content 	= :client_content,
			`order_mails_content`.client_fromname 	= :client_fromname,
			`order_mails_content`.admin_subject 	= :admin_subject,
			`order_mails_content`.admin_content 	= :admin_content,
			`order_mails_content`.admin_fromname 	= :admin_fromname
		WHERE `order_mails`.order_mails_id 			= :order_mails_id
		AND `order_mails_content`.order_mails_id 	= :order_mails_id
		AND `order_mails_content`.language_id 	= :language_id
		';

		$this->db->query($sql, array(
			'client_subject' 	=> $post['order_mails']['client_subject'],
			'client_content' 	=> $post['order_mails']['client_content'],
			'client_fromname' 	=> $post['order_mails']['client_fromname'],
			'admin_subject' 	=> $post['order_mails']['admin_subject'],
			'admin_content' 	=> $post['order_mails']['admin_content'],
			'admin_fromname' 	=> $post['order_mails']['admin_fromname'],
			'order_mails_id' 	=> $id,
			'language_id' 	=> $language_id
		));
		
	}
	
	function update_overview($post)
	{
		foreach($post['active'] as $k => $v)
		{
			$this->db->query('UPDATE `order_mails` SET `order_mails`.active = ? WHERE `order_mails`.order_mails_id = ? LIMIT 1', array($v, $k));
		}
		foreach($post['highlight'] as $k => $v)
		{
			$this->db->query('UPDATE `order_mails` SET `order_mails`.highlight = ? WHERE `order_mails`.order_mails_id = ? LIMIT 1', array($v, $k));
		}
	}
	
	function order_status()
	{
		$sql = '
			SELECT *
			FROM `order_status`
		';
		$order_status = $this->db->query($sql);
		if(isset($order_status) && count($order_status) > 0){
			foreach($order_status as $obj){
				$data[$obj['order_status_id']] = $obj['name'];
			}
		}
		return $data;
	}
	
	
	function delete($id)
	{
		$sql = 'SELECT * FROM `order_mails` WHERE `order_mails`.order_mails_id = ?';
		$r = $this->db->query($sql, array($id));

		$this->db->query('DELETE FROM `order_mails` WHERE `order_mails`.order_mails_id = ?', array($id));
		$this->db->query('DELETE FROM `order_mails_content` WHERE `order_mails_content`.order_mails_id = ?', array($id));
		if(haveFilters('order_mails')){
				$sql = '
				SELECT * 
				FROM `filter`
				WHERE `filter`.controller = :controller
				';	
				$data = $this->db->query($sql, array('controller' => 'order_mails'));
				if(!empty($data))
				{
					foreach($data as $filter)
					{
						$sql = '
							SELECT *
								FROM `filter`, `filter_item`, `filter_heading`
								WHERE `filter_heading`.filter_heading_id = `filter_item`.filter_heading_id
								AND `filter_heading`.filter_id = `filter`.filter_id
								AND `filter`.filter_id = :filter_id	
							';
							$subelements = $this->db->query($sql, array('filter_id' => $filter['filter_id']));
							if(isset($subelements) && $subelements && count($subelements) > 0)
								foreach($subelements as $j => $sub_element)
									$this->db->query('DELETE FROM `filter_item_saved` WHERE `filter_item_saved`.table_id = ? AND `filter_item_saved`.filter_item_id = ?', array($id, $sub_element['filter_item_id']));
					}
				}
			}
		
		$sql = '
		SELECT *
		FROM `media`
		WHERE `media`.table_id = ?
		AND `media`.controller = ?
		';
		
		$media_ar = $this->db->query($sql, array($id, CONTROLLER));
		
		foreach($media_ar as $media)
		{
			$this->delete_media($media['media_id']);
		}			

	}

	function fetch_drop_down($controller, $parent_id = 0)
	{
		$return = array();
		
		$sql = '
		SELECT `category`.category_id, `category`.parent_id, `category_content`.title
		FROM `category`, `category_content` 
		WHERE `category`.category_id = `category_content`.category_id 
		AND `category_content`.language_id = :lang 
		AND `category`.parent_id = :parent_id
		AND `category`.controller = :controller
		ORDER BY `category`.order ASC
		';
		
		$data = $this->db->query($sql, array('lang' => $this->config->item('default_language'), 'parent_id' => $parent_id, 'controller' => $controller));
		
		$i = 0;
		
		foreach($data as $category)
		{
			$return[$i] = $category;
		
			$return[$i]['children'] = $this->fetch_drop_down($controller,$category['category_id']);
		
			$i++;
		}
		return $return;
	}
	
	function count_children($category_id = 0)
	{		
		$sql = 'SELECT `category`.category_id FROM `category` WHERE `category`.parent_id = ?';
		$categorys = $this->db->query($sql, array($category_id));
		
		$sub_sub = 0;
		
		foreach($categorys as $category)
		{
			$sql = 'SELECT `category`.category_id FROM `category` WHERE `category`.parent_id = ?';
			$this->db->query($sql, array($category['category_id']));
			if($this->db->num_rows > 0)
			{
				$sub_sub = $sub_sub + $this->db->num_rows;
			}
		}
		
		$count['sub'] = count($categorys);
		$count['sub_sub'] = $sub_sub;
		
		return $count;
	}
	
}

?>