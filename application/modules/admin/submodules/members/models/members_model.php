<?php

class members_model extends model
{
	function fetch_all()
	{
		$sql = '
		SELECT * FROM `member`
		INNER JOIN `member_content`
		ON `member`.member_id = `member_content`.member_id
		WHERE `member_content`.language_id = ?
		ORDER BY `member_content`.company_name ASC
		';
		
		$data['members'] = $this->db->query($sql, array($this->config->item('default_language')));
		return $data;
	}
	
	function fetch($member_id, $language_id)
	{	
		$sql = '
		SELECT *
		FROM `member`
		INNER JOIN `member_content`
		ON `member_content`.member_id = `member`.member_id
		WHERE `member`.member_id = ?
		AND `member_content`.language_id = ?
		';
		
		$r = $this->db->query($sql, array($member_id, $language_id));
		$data['members'] = $r[0];

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
	
		$data['media'] = $this->db->query($sql, array($data['members']['member_id'], CONTROLLER, $language_id));
		
		return $data;
	}
	
	function add($post)
	{
		$sql = '
		INSERT INTO `member`
		(
			date_birth
		)
		VALUES
		(
			:date_birth
		)';
			
		
		$this->db->query($sql, array(
				'date_birth' 	=> strtotime($post['members']['date_birth'])
		));
		
		$id = $this->db->last_insert_id;

		$sql = '
		INSERT INTO `member_content`
		(
			member_id,
			language_id,
			company_name,
			firstname,
			lastname,
			email,
			telephone,
			mobile,
			website,
			street,
			postal,
			city,
			description,
			sub_active
		)
		VALUES
		(
			:member_id,
			:language_id,
			:company_name,
			:firstname,
			:lastname,
			:email,
			:telephone,
			:mobile,
			:website,
			:street,
			:postal,
			:city,
			:description,
			:sub_active
		)';
			
			
		$this->db->query($sql, array(
				'member_id' 	=> $id,
				'language_id' 	=> $this->config->item('default_language'),
				'company_name' 	=> $post['members']['company_name'],
				'firstname' 	=> $post['members']['firstname'],
				'lastname' 		=> $post['members']['lastname'],
				'email' 		=> $post['members']['email'],
				'telephone' 	=> $post['members']['telephone'],
				'mobile' 		=> $post['members']['mobile'],
				'website' 		=> $post['members']['website'],
				'street' 		=> $post['members']['street'],
				'postal' 		=> $post['members']['postal'],
				'city' 			=> $post['members']['city'],
				'description' 	=> $post['members']['description'],
				'sub_active' 	=> $post['members']['sub_active']
		));
		
		$languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array($this->config->item('default_language')));
		
		foreach($languages as $language)
		{				
			$sql = '
			INSERT INTO `member_content`
			(
				member_id,
				language_id,
				sub_active
			)
			VALUES
			(
				:member_id,
				:language_id,
				:sub_active
			)';
			
			$this->db->query($sql, array(
				'member_id' 	=> $id,
				'language_id' 	=> $language['language_id'],
				'sub_active' 	=> 0
			));
		}
		
		$this->attach_caregories($post['members']['category_id'], $id, CONTROLLER);
		
		return $id;
	}
	
	function edit($post, $id, $language_id)
	{
		
		$sql = '
		UPDATE `member`, `member_content`
		SET
			`member`.date_birth 			= :date_birth,
			`member_content`.company_name 	= :company_name,
			`member_content`.firstname 		= :firstname,
			`member_content`.lastname 		= :lastname,
			`member_content`.email 			= :email,
			`member_content`.telephone 		= :telephone,
			`member_content`.mobile 		= :mobile,
			`member_content`.website 		= :website,
			`member_content`.street 		= :street,
			`member_content`.postal 		= :postal,
			`member_content`.city 			= :city,
			`member_content`.description 	= :description,
			`member_content`.sub_active 	= :sub_active
		WHERE `member`.member_id 			= :member_id
		AND `member_content`.member_id 		= :member_id
		AND `member_content`.language_id 	= :language_id
		';
		
		$this->db->query($sql, array(
			'date_birth' 		=> strtotime($post['members']['date_birth']),
			'company_name' 		=> $post['members']['company_name'],
			'firstname' 		=> $post['members']['firstname'],
			'lastname' 			=> $post['members']['lastname'],
			'email' 			=> $post['members']['email'],
			'telephone' 		=> $post['members']['telephone'],
			'mobile' 			=> $post['members']['mobile'],
			'website' 			=> $post['members']['website'],
			'street' 			=> $post['members']['street'],
			'postal' 			=> $post['members']['postal'],
			'city' 				=> $post['members']['city'],
			'description' 		=> $post['members']['description'],
			'sub_active' 		=> $post['members']['sub_active'],
			'member_id' 		=> $id,
			'language_id' 		=> $language_id
		));
		echo $this->db->error;
		
		if(isset($post['media']) && $post['media'] && count($post['media']) > 0)
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
		
		$this->attach_caregories($post['members']['category_id'], $id, CONTROLLER);
		
	}
	
	function update_overview($post)
	{
		foreach($post['active'] as $k => $v)
		{
			$this->db->query('UPDATE `member` SET `member`.active = ? WHERE `member`.member_id = ? LIMIT 1', array($v, $k));
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
		$sql = 'SELECT * FROM `member` WHERE `member`.member_id = ?';
		$r = $this->db->query($sql, array($id));

		$this->db->query('DELETE FROM `member` WHERE `member`.member_id = ?', array($id));
		$this->db->query('DELETE FROM `member_content` WHERE `member_content`.member_id = ?', array($id));
		$this->db->query('DELETE FROM `event_members` WHERE `event_members`.member_id = ?', array($id));
		$this->delete_categories($id, CONTROLLER);
		
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
	
	function fetch_all_category_selected($table_id, $controller)
	{	
		$r = $this->db->query('SELECT category_id FROM `category_selected` WHERE `category_selected`.table_id = ? AND `category_selected`.controller = ?', array($table_id, $controller));
		
		$items = array();
		if(isset($r) && $r && count($r) > 0){
			foreach($r as $item){
				$items[] = $item['category_id'];
			}
		}
		return $items;
	}
		
	function attach_caregories($post, $id, $controller){
		
		$this->db->query('DELETE FROM `category_selected` WHERE `category_selected`.table_id = ? AND controller = ?', array($id, $controller));
		if(isset($post) && count($post) > 0) {
      foreach($post as $category_id){
        $sql = '
        INSERT INTO `category_selected`
        (
          table_id,
          controller,
          category_id
        )
        VALUES
        (
          :table_id,
          :controller,
          :category_id
        )';
        
        $this->db->query($sql, array(
          'table_id' 		=> $id,
          'controller' 	=> $controller,
          'category_id' 	=> $category_id
        ));
      }
    }
		
	}
		
	function delete_categories($id, $controller){
		$this->db->query('DELETE FROM `category_selected` WHERE `category_selected`.table_id = ? AND controller = ?', array($id, $controller));
	}
	
}

?>