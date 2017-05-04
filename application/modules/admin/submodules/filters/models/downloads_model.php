<?php

class downloads_model extends model
{
	function fetch_all()
	{
		$sql = '
		SELECT * FROM `downloads`
		INNER JOIN `downloads_content`
		ON `downloads`.downloads_id = `downloads_content`.downloads_id
		WHERE `downloads_content`.language_id = ?
		ORDER BY `downloads`.last_update DESC
		';
		
		$data['downloads'] = $this->db->query($sql, array($this->config->item('default_language')));
		return $data;
	}
	
	function fetch($downloads_id, $language_id)
	{	
		$sql = '
		SELECT *
		FROM `downloads`
		INNER JOIN `downloads_content`
		ON `downloads_content`.downloads_id = `downloads`.downloads_id
		WHERE `downloads`.downloads_id = ?
		AND `downloads_content`.language_id = ?
		';
		
		$r = $this->db->query($sql, array($downloads_id, $language_id));
		$data['downloads'] = $r[0];

		$sql = '
			SELECT *
			FROM `media`
			INNER JOIN `media_content`
			ON `media_content`.media_id = `media`.media_id
			INNER JOIN `media_type`
			ON `media_type`.media_type_id = `media`.media_type_id
			WHERE `media`.table_id = ?
			AND `media_type`.name = "img"
			AND `media`.controller = ?
			AND `media_content`.language_id = ?
			ORDER BY `media`.order ASC
		';
	
		$data['media'] = $this->db->query($sql, array($data['downloads']['downloads_id'], CONTROLLER, $language_id));
		
		$sql = '
			SELECT *
			FROM `media`
			INNER JOIN `media_type`
			ON `media_type`.media_type_id = `media`.media_type_id
			WHERE `media`.table_id = ?
			AND `media_type`.name = "doc"
			AND `media`.controller = ?
			ORDER BY `media`.filename ASC
		';
		
		$data['docs'] = $this->db->query($sql, array($data['downloads']['downloads_id'], CONTROLLER));
	
		return $data;
	}
	
	function add($post)
	{
		$sql = '
		INSERT INTO `downloads`
		(
			category_id,
			date_created,
			last_update
		)
		VALUES
		(
			:category_id,
			:date_created,
			:last_update
		)';
			
		
		$this->db->query($sql, array(
				'category_id' 	=> $post['downloads']['category_id'],
				'date_created' 	=> strtotime($post['downloads']['date_created']),
				'last_update' 	=> strtotime($post['downloads']['date_created'])
		));
		
		$id = $this->db->last_insert_id;

		$sql = '
		INSERT INTO `downloads_content`
		(
			downloads_id,
			language_id,
			title,
			description,
			content,
			sub_active
		)
		VALUES
		(
			:downloads_id,
			:language_id,
			:title,
			:description,
			:content,
			:sub_active
		)';
			
		$this->db->query($sql, array(
				'downloads_id' 		=> $id,
				'language_id' 		=> $this->config->item('default_language'),
				'title' 			=> $post['downloads']['title'],
				'description' 		=> $post['downloads']['description'],
				'content' 			=> $post['downloads']['content'],
				'sub_active' 		=> $post['downloads']['sub_active']
		));
		
		$languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array($this->config->item('default_language')));
		
		foreach($languages as $language)
		{				
			$sql = '
			INSERT INTO `downloads_content`
			(
				downloads_id,
				language_id,
				sub_active
			)
			VALUES
			(
				:downloads_id,
				:language_id,
				:sub_active
			)';
			
			$this->db->query($sql, array(
				'downloads_id' 	=> $id,
				'language_id' 	=> $language['language_id'],
				'sub_active' 	=> 0
			));
		}
		
		return $id;
	}
	
	function edit($post, $id, $language_id)
	{

		$sql = '
		UPDATE `downloads`, `downloads_content`
		SET 
			`downloads`.date_created 				= :date_created,
			`downloads`.category_id 				= :category_id,
			`downloads`.last_update 				= :last_update,
			`downloads_content`.title 				= :title,
			`downloads_content`.description 		= :description,
			`downloads_content`.content 			= :content,
			`downloads_content`.sub_active 			= :sub_active
		WHERE `downloads`.downloads_id 				= :downloads_id
		AND `downloads_content`.downloads_id 		= :downloads_id
		AND `downloads_content`.language_id 		= :language_id
		';
		
		$this->db->query($sql, array(
			'date_created' 		=> strtotime($post['downloads']['date_created']),
			'category_id' 		=> $post['downloads']['category_id'],
			'last_update' 		=> time(),
			'title' 			=> ucfirst($post['downloads']['title']),
			'description' 		=> $post['downloads']['description'],
			'content' 			=> $post['downloads']['content'],
			'sub_active' 		=> $post['downloads']['sub_active'],
			'downloads_id' 		=> $id,
			'language_id' 		=> $language_id
		));

		if(isset($post['media']))
			foreach($post['media'] as $media)
			{
				$sql = '
					UPDATE `media`, `media_content`
					SET
						`media`.album_thumb 		= :album_thumb
					WHERE `media`.media_id = :media_id
					AND `media_content`.media_id = :media_id
					AND `media_content`.language_id = :language_id
				';
				
				$this->db->query($sql, array(
					'album_thumb' 	=> (isset($_POST['set_thumbnail']) && $_POST['set_thumbnail'] == $media['media_id'] ? 1 : 0),
					'media_id'		=> $media['media_id'],
					'language_id'	=> $language_id
				));
			}
	}
	
	function update_overview($post)
	{
		foreach($post['active'] as $k => $v)
		{
			$this->db->query('UPDATE `downloads` SET `downloads`.active = ? WHERE `downloads`.downloads_id = ? LIMIT 1', array($v, $k));
		}
	}
	
	function order_media($direction, $table_id, $language_id, $current_order)
	{
		switch($direction)
		{
			case 'left':
				$from = $this->db->query('SELECT `media`.order, `media`.media_id FROM `media` WHERE `media`.table_id = ? AND `media`.order = ?', array($table_id, $current_order));
				$to = $this->db->query('SELECT `media`.order, `media`.media_id FROM `media` WHERE `media`.order < ? AND `media`.table_id = ? ORDER BY `media`.order DESC', array($current_order, $table_id));
				
				if(!empty($to))
				{
					$this->db->query('UPDATE `media` SET `media`.order = ? WHERE `media`.media_id = ?', array($to[0]['order'], $from[0]['media_id']));
					$this->db->query('UPDATE `media` SET `media`.order = ? WHERE `media`.media_id = ?', array($from[0]['order'], $to[0]['media_id']));
				}
			break;
			
			case 'right':
				$from = $this->db->query('SELECT `media`.order, `media`.media_id FROM `media` WHERE `media`.table_id = ? AND `media`.order = ?', array($table_id, $current_order));
				$to = $this->db->query('SELECT `media`.order, `media`.media_id FROM `media` WHERE `media`.order > ? AND `media`.table_id = ? ORDER BY `media`.order ASC', array($current_order, $table_id));
				
				if(!empty($to))
				{
					$this->db->query('UPDATE `media` SET `media`.order = ? WHERE `media`.media_id = ?', array($to[0]['order'], $from[0]['media_id']));
					$this->db->query('UPDATE `media` SET `media`.order = ? WHERE `media`.media_id = ?', array($from[0]['order'], $to[0]['media_id']));
				}
			break;
		}
	}
	
	function delete($id)
	{
		$sql = 'SELECT * FROM `downloads` WHERE `downloads`.downloads_id = ?';
		$r = $this->db->query($sql, array($id));

		$this->db->query('DELETE FROM `downloads` WHERE `downloads`.downloads_id = ?', array($id));
		$this->db->query('DELETE FROM `downloads_content` WHERE `downloads_content`.downloads_id = ?', array($id));
		
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
	
	function delete_media($media_id)
	{
		$sql = 'SELECT `media`.filename FROM `media` WHERE `media`.media_id = ?';
		$data = $this->db->query($sql, array($media_id));
		
		$filename = $data[0]['filename'];

		$dirs = glob(BASE_PATH . MEDIA_DIR . CONTROLLER . '/*', GLOB_ONLYDIR);
		
        foreach($dirs as $dir)
        {
            if(is_dir($dir))
			{
                if(file_exists($dir.'/'.$filename))
				{
					unlink($dir.'/'.$filename);
                }
            }
        }

		$this->db->query('DELETE FROM `media` WHERE `media`.media_id = ?', array($media_id));
		$this->db->query('DELETE FROM `media_content` WHERE `media_content`.media_id = ?', array($media_id));
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