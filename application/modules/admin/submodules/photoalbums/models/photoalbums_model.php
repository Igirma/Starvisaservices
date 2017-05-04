<?php

class photoalbums_model extends model
{
	function fetch_all()
	{
		$data = array();
	
		$sql = '
			SELECT * FROM `album`
			INNER JOIN `album_content`
			ON `album_content`.album_id = `album`.album_id
			WHERE `album_content`.language_id = :language_id
			ORDER BY `album`.order ASC
		';
		
		$data = $this->db->query($sql, array(
			'language_id' => $this->config->item('default_language')
		));
		
		foreach($data as $k => $v)
		{
			$sql = 'SELECT COUNT(`media`.media_id) as `count` FROM `media` WHERE `media`.album_id = :album_id';
			$count = $this->db->query($sql, array('album_id' => $v['album_id']));
			$data[$k]['picture_count'] = $count[0]['count'];
		}

		return $data;
	}
	
	function fetch_dash()
	{
		$sql = '
			SELECT * 
			FROM `album`, `album_content` 
			WHERE `album`.album_id = `album_content`.album_id 
			AND `album_content`.language_id = :lang 
			ORDER BY `album`.last_update DESC, `album`.date_created DESC
			LIMIT 1
		';
		
		$data = $this->db->query($sql, array('lang' => $this->config->item('default_language')));
		
		if(!empty($data))
		{
			$sql = 'SELECT * FROM `media` WHERE `media`.album_id = ? ORDER BY `media`.media_id DESC LIMIT 6';
		
			$data[0]['media'] = $this->db->query($sql, array($data[0]['album_id']));
			
			return $data[0];
		}
		

	}
	
	function fetch($album_id, $language_id)
	{
		$sql = '
			SELECT * FROM `album`
			INNER JOIN `album_content`
			ON `album_content`.album_id = `album`.album_id
			WHERE `album`.album_id = :album_id
			AND `album_content`.language_id = :language_id
		';
		
		$temp = $this->db->query($sql, array(
			'album_id' 		=> $album_id,
			'language_id' 	=> $language_id
		));
		
		$data['photoalbums'] = $temp[0];
		
		$sql = '
			SELECT * FROM `media`
			INNER JOIN `media_content`
			ON `media_content`.media_id = `media`.media_id
			WHERE `media`.album_id = :album_id
			AND `media`.controller = "photoalbums"
			AND `media_content`.language_id = :language_id
			ORDER BY `media`.order ASC
		';
		
		$data['media'] = $this->db->query($sql, array(
			'album_id' 		=> $album_id,
			'language_id' 	=> $language_id
		));
		
		return $data;
	}
	
	function add($post)
	{
		$sql = '
			SELECT MAX(`album`.order) AS `order`
			FROM `album`
		';
		
		$order = $this->db->query($sql);
	
		$sql = '
		INSERT INTO `album`
		(
			`album`.order,
			`album`.date_created,
			`album`.edit_by
		)
		VALUES
		(
			:order,
			:date_created,
			:edit_by
		)';
			
		
		$this->db->query($sql, array(
			'order' 		=> $order[0]['order'] + 1,
			'date_created'	=> time(),
			'edit_by'		=> $_SESSION['username']
		));
		
		$id = $this->db->last_insert_id;
		
		$sql = '
		INSERT INTO `album_content`
		(
			album_id,
			language_id,
			slug,
			title,
			description,
			content,
			meta_title,
			meta_desc,
			meta_keyw,
			sub_active
		)
		VALUES
		(
			:album_id,
			:language_id,
			:slug,
			:title,
			:description,
			:content,
			:meta_title,
			:meta_desc,
			:meta_keyw,
			:sub_active
		)';
			
		$this->db->query($sql, array(
			'album_id' 		=> $id,
			'language_id' 	=> $this->config->item('default_language'),
			'slug' 			=> $this->url->string_to_url($post['photoalbums']['slug']),
			'title' 		=> ucfirst($post['photoalbums']['title']),
			'description' 	=> ucfirst($post['photoalbums']['description']),
			'content' 		=> $post['photoalbums']['content'],
			'meta_title'	=> $post['photoalbums']['meta_title'],
			'meta_desc'		=> $post['photoalbums']['meta_desc'],
			'meta_keyw'		=> $post['photoalbums']['meta_keyw'],
			'sub_active'	=> $post['sub_active']
		));
		
		$languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array($this->config->item('default_language')));
		
		foreach($languages as $language)
		{				
			$sql = '
			INSERT INTO `album_content`
			(
				album_id,
				language_id
			)
			VALUES
			(
				:album_id,
				:language_id
			)';
			
			$this->db->query($sql, array(
				'album_id' 		=> $id,
				'language_id' 	=> $language['language_id']
			));
		}
		
		return $id;
	}
	
	function edit($post, $language_id)
	{
		$sql = 'SELECT `album_content`.slug FROM `album_content` WHERE `album_content`.album_id = ? AND `album_content`.language_id = ?';
		$content = $this->db->query($sql, array($post['photoalbums']['album_id'], $language_id));
		
		$old_slug = $content[0]['slug'];
		
		if($old_slug != $this->url->string_to_url($post['photoalbums']['slug']))
		{
			$sql = '
			UPDATE `album_content`
			SET `album_content`.slug_301 = :slug_301
			WHERE `album_content`.album_id = :album_id
			AND `album_content`.language_id = :language_id
			';
			
			$this->db->query($sql, array(
				'album_id' 		=> $post['photoalbums']['album_id'],
				'language_id' 	=> $language_id,
				'slug_301' 		=> $old_slug
			));
		}
		
		$sql = '
			UPDATE `album`
			SET 
				`album`.last_update = :last_update,
				`album`.edit_by		= :edit_by
			WHERE `album`.album_id = :album_id
		';
		
		$this->db->query($sql, array(
			'last_update' 	=> time(),
			'album_id'		=> $post['photoalbums']['album_id'],
			'edit_by'		=> $_SESSION['username']
		));
		
		$sql = '
			UPDATE `album_content`
			SET
				`album_content`.slug 		= :slug,
				`album_content`.title 		= :title,
				`album_content`.description = :description,
				`album_content`.content 	= :content,
				`album_content`.meta_title 	= :meta_title,
				`album_content`.meta_desc 	= :meta_desc,
				`album_content`.meta_keyw 	= :meta_keyw,
				`album_content`.sub_active 	= :sub_active
			WHERE `album_content`.language_id = :language_id
			AND `album_content`.album_id = :album_id
		';
		
		$this->db->query($sql, array(
			'slug' 			=> $this->url->string_to_url($post['photoalbums']['slug']),
			'title' 		=> ucfirst($post['photoalbums']['title']),
			'description' 	=> ucfirst($post['photoalbums']['description']),
			'content' 		=> $post['photoalbums']['content'],
			'meta_title'	=> $post['photoalbums']['meta_title'],
			'meta_desc'		=> $post['photoalbums']['meta_desc'],
			'meta_keyw'		=> $post['photoalbums']['meta_keyw'],
			'language_id' 	=> $language_id,
			'album_id' 		=> $post['photoalbums']['album_id'],
			'sub_active'	=> $post['sub_active']
		));
		
		if(isset($post['media']))
		{
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
	}
	
	function delete($album_id)
	{
		$sql = '
		SELECT *
		FROM `media`
		WHERE `media`.album_id = ?
		AND `media`.controller = ?
		';
		
		$media_ar = $this->db->query($sql, array($album_id, CONTROLLER));
		
		foreach($media_ar as $media)
		{
			$this->delete_media($media['media_id']);
		}
		
		$sql = '
			DELETE FROM `album`
			WHERE `album`.album_id = :album_id
			LIMIT 1
		';
		
		$this->db->query($sql, array('album_id' => $album_id));
		
		$sql = '
			DELETE FROM `album_content`
			WHERE `album_content`.album_id = :album_id
		';
		
		$this->db->query($sql, array('album_id' => $album_id));
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
		
		$sql = '
			DELETE FROM `media`
			WHERE `media`.media_id = :media_id
			LIMIT 1
		';
		
		$this->db->query($sql, array('media_id' => $media_id));
		
		$sql = '
			DELETE FROM `media_content`
			WHERE `media_content`.media_id = :media_id
			LIMIT 1
		';
		
		$this->db->query($sql, array('media_id' => $media_id));
	}
	
	function order($direction, $current_order, $album_id)
	{
		switch($direction)
		{
			case 'up':
				$from = $this->db->query('SELECT `album`.order, `album`.album_id FROM `album` WHERE `album`.album_id = ?', array($album_id));
				$to = $this->db->query('SELECT `album`.order, `album`.album_id FROM `album` WHERE `album`.order < ? ORDER BY `album`.order DESC', array($current_order));
				
				debug($from);
				debug($to);
				
				if(!empty($to))
				{
					$this->db->query('UPDATE `album` SET `album`.order = ? WHERE `album`.album_id = ?', array($to[0]['order'], $from[0]['album_id']));
					$this->db->query('UPDATE `album` SET `album`.order = ? WHERE `album`.album_id = ?', array($from[0]['order'], $to[0]['album_id']));
				}
			break;
				
			case 'down':
				$from = $this->db->query('SELECT `album`.order, `album`.album_id FROM `album` WHERE `album`.album_id = ?', array($album_id));
				$to = $this->db->query('SELECT `album`.order, `album`.album_id FROM `album` WHERE `album`.order > ? ORDER BY `album`.order ASC', array($current_order));
				
				if(!empty($to))
				{
					$this->db->query('UPDATE `album` SET `album`.order = ? WHERE `album`.album_id = ?', array($to[0]['order'], $from[0]['album_id']));
					$this->db->query('UPDATE `album` SET `album`.order = ? WHERE `album`.album_id = ?', array($from[0]['order'], $to[0]['album_id']));
				}
			break;
		}
	}
	
	function order_media($direction, $album_id, $current_order)
	{
		switch($direction)
		{
			case 'left':
				$from = $this->db->query('SELECT `media`.order, `media`.media_id FROM `media` WHERE `media`.album_id = ? AND `media`.order = ?', array($album_id, $current_order));
				$to = $this->db->query('SELECT `media`.order, `media`.media_id FROM `media` WHERE `media`.order < ? AND `media`.album_id = ? ORDER BY `media`.order DESC', array($current_order, $album_id));
				
				debug($to);
				
				if(!empty($to))
				{
					$this->db->query('UPDATE `media` SET `media`.order = ? WHERE `media`.media_id = ?', array($to[0]['order'], $from[0]['media_id']));
					$this->db->query('UPDATE `media` SET `media`.order = ? WHERE `media`.media_id = ?', array($from[0]['order'], $to[0]['media_id']));
				}
			break;
			
			case 'right':
				$from = $this->db->query('SELECT `media`.order, `media`.media_id FROM `media` WHERE `media`.album_id = ? AND `media`.order = ?', array($album_id, $current_order));
				$to = $this->db->query('SELECT `media`.order, `media`.media_id FROM `media` WHERE `media`.order > ? AND `media`.album_id = ? ORDER BY `media`.order ASC', array($current_order, $album_id));
				
				if(!empty($to))
				{
					$this->db->query('UPDATE `media` SET `media`.order = ? WHERE `media`.media_id = ?', array($to[0]['order'], $from[0]['media_id']));
					$this->db->query('UPDATE `media` SET `media`.order = ? WHERE `media`.media_id = ?', array($from[0]['order'], $to[0]['media_id']));
				}
			break;
		}
	}
	
	function update_overview($post)
	{
		foreach($post['active'] as $k => $v)
		{
			$this->db->query('UPDATE `album` SET `album`.active = ? WHERE `album`.album_id = ? LIMIT 1', array($v, $k));
		}
	}
}

?>