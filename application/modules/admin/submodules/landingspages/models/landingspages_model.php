<?php 
class landingspages_model extends model
{	
	function count_children($landingspage_id)
	{		
		$sql = 'SELECT `landingspage`.landingspage_id FROM `landingspage` WHERE `landingspage`.parent_id = ?';
		$landingspages = $this->db->query($sql, array($landingspage_id));
		
		$sub_sub = 0;
		
		foreach($landingspages as $landingspage)
		{
			$sql = 'SELECT `landingspage`.landingspage_id FROM `landingspage` WHERE `landingspage`.parent_id = ?';
			$this->db->query($sql, array($landingspage['landingspage_id']));
			if($this->db->num_rows > 0)
			{
				$sub_sub = $sub_sub + $this->db->num_rows;
			}
		}
		
		$count['sub'] = count($landingspages);
		$count['sub_sub'] = $sub_sub;
		
		return $count;
	}
	
	function fetch_dash()
	{
		$sql = '
			SELECT * 
			FROM `landingspage`, `landingspage_content` 
			WHERE `landingspage`.landingspage_id = `landingspage_content`.landingspage_id 
			AND `landingspage_content`.language_id = :lang
			AND `landingspage`.last_update = ""			
			ORDER BY `landingspage`.date_created DESC
			LIMIT 5
		';
		$data['add'] = $this->db->query($sql, array('lang' => $this->config->item('default_language')));

		$sql = '
			SELECT * 
			FROM `landingspage`, `landingspage_content` 
			WHERE `landingspage`.landingspage_id = `landingspage_content`.landingspage_id 
			AND `landingspage_content`.language_id = :lang
			AND `landingspage`.last_update != ""
			ORDER BY `landingspage`.last_update DESC
			LIMIT 5
		';
		$data['edit'] = $this->db->query($sql, array('lang' => $this->config->item('default_language')));

		return $data;
	}

	function fetch_drop_down($parent_id = 0)
	{
		$return = array();
		
		$sql = '
		SELECT `landingspage`.landingspage_id, `landingspage`.parent_id, `landingspage_content`.menu_title
		FROM `landingspage`, `landingspage_content` 
		WHERE `landingspage`.landingspage_id = `landingspage_content`.landingspage_id 
		AND `landingspage_content`.language_id = :lang 
		AND `landingspage`.parent_id = :parent_id
		ORDER BY `landingspage`.order ASC
		';
		$data = $this->db->query($sql, array('lang' => $this->config->item('default_language'), 'parent_id' => $parent_id));
		
		$i = 0;
		
		foreach($data as $landingspage)
		{
			$return[$i] = $landingspage;
		
			$return[$i]['children'] = $this->fetch_drop_down($landingspage['landingspage_id']);
		
			$i++;
		}
		return $return;
	}
	
	function fetch_all($parent_id = 0)
	{
		$return = array();
		
		$sql = '
		SELECT * 
		FROM `landingspage`, `landingspage_content` 
		WHERE `landingspage`.landingspage_id = `landingspage_content`.landingspage_id 
		AND `landingspage_content`.language_id = :lang 
		AND `landingspage`.parent_id = :parent_id
		ORDER BY `landingspage`.order ASC
		';
		$data = $this->db->query($sql, array('lang' => $this->config->item('default_language'), 'parent_id' => $parent_id));
		
		$i = 0;
		
		if(isset($data) && $data && count($data) > 0)
		foreach($data as $landingspage)
		{
			$return[$i] = $landingspage;
		
			$return[$i]['children'] = $this->fetch_all($landingspage['landingspage_id']);
		
			$i++;
		}
		return $return;
	}
	
	function fetch($landingspage_id, $language_id)
	{
		$r = $this->db->query('SELECT * FROM `landingspage` WHERE `landingspage`.landingspage_id = ?', array($landingspage_id));
		$data['parent_id'] = $r[0]['parent_id'];
		$data['category_id'] = $r[0]['category_id'];
		$data['external'] = $r[0]['external'];
		
		if($this->config->item('mobile_website')  == 1){
			$r = $this->db->query('SELECT * FROM `landingsmobile_content` WHERE `landingsmobile_content`.landingspage_id = ? AND `landingsmobile_content`.language_id = ?', array($landingspage_id, $language_id));
			$data['form']['landingsmobile_content'] = $r[0];
		}
		
		$r = $this->db->query('SELECT * FROM `landingspage_content` WHERE `landingspage_content`.landingspage_id = ? AND `landingspage_content`.language_id = ?', array($landingspage_id, $language_id));
		$data['form']['landingspage_content'] = $r[0];
		
		$sql = '
		SELECT *
		FROM `language`
		WHERE `language`.language_id = ?
		';
		
		$data['language'] = $this->db->query($sql, array($language_id));
		
		$sql = '
		SELECT *
		FROM `media`
		INNER JOIN `media_content`
			ON `media_content`.media_id = `media`.media_id
		WHERE `media`.table_id = ?
			AND `media`.controller = ?
			AND `media_content`.language_id = ?
		ORDER BY `media`.order ASC
		';

		$data['media'] = $this->db->query($sql, array($data['form']['landingspage_content']['landingspage_id'], CONTROLLER, $language_id));
		
		return $data;
	}
	
	function collectForDuplicate($landingspage_id, $image_settings)
	{
		//Auto settings:
		$order = $this->db->query('SELECT MAX(`order`) + 1 AS `order` FROM `landingspage`');

		if($order[0]['order'] == '')
		{
			$order = 0;
		}
		else
		{
			$order = $order[0]['order'];
		}
		
		//Collect page data
		$sql = '
			SELECT * FROM `landingspage`
			WHERE `landingspage`.landingspage_id = ?
		';
		$landingspage = $this->db->query($sql, array($landingspage_id));
		$landingspage = $landingspage[0];
		
		// Insert page data	
		$sql = '
			INSERT INTO landingspage
			(
			`parent_id`,
			`controller`,
			`external`,
			`main_menu`,
			`menu`,
			`footer`,
			`mobile`,
			`deletable`,
			`order`,
			`active`,
			`adwords_code`,
			`date_created`,
			`last_update`,
			`edit_by`
			)
			VALUES
			(
			:parent_id,
			:controller,
			:external,
			:main_menu,
			:menu,
			:footer,
			:mobile,
			:deletable,
			:order,
			:active,
			:adwords_code,
			:date_created,
			:last_update,
			:edit_by
			)
		';
		$this->db->query($sql, array(
			'parent_id' => 0,
			'controller' => $landingspage['controller'],
			'external' => $landingspage['external'],
			'main_menu' => $landingspage['main_menu'],
			'menu' => $landingspage['menu'],
			'footer' => $landingspage['footer'],
			'mobile' => $landingspage['mobile'],
			'deletable' => $landingspage['deletable'],
			'order' => $order,
			'active' => $landingspage['active'],
			'adwords_code' => $landingspage['adwords_code'],
			'date_created' => time(),
			'last_update' => time(),
			'edit_by' => $_SESSION['username']
		));
		
		$id = $this->db->last_insert_id;
		$new_landingspage_id = $id;
		
		//Collect page content
		$sql = '
			SELECT * FROM `landingspage_content`
			WHERE `landingspage_content`.landingspage_id = ?
		';
		$landingspage_content = $this->db->query($sql, array($landingspage_id));
		
		//Insert page content
		foreach($landingspage_content as $content)
		{
			$sql = 'INSERT INTO `landingspage_content`
			(
				landingspage_id,
				language_id,
				slug,
				slug_301,
				meta_title,
				meta_desc,
				meta_keyw,
				menu_title,
				content_title,
				content_description,
				content_text,
				overview_title,
				overview_description,
				overview_text,
				ex_name,
				ex_url,
				sub_active
			)
			VALUES
			(
				:landingspage_id,
				:language_id,
				:slug,
				:slug_301,
				:meta_title,
				:meta_desc,
				:meta_keyw,
				:menu_title,
				:content_title,
				:content_description,
				:content_text,
				:overview_title,
				:overview_description,
				:overview_text,
				:ex_name,
				:ex_url,
				:sub_active
			)';
			
			if($content['slug'] != '')
			{
				$newSlug = generateLandingsPageSlug($content['slug'], 'landingspage_content');
			}
			else
			{
				$newSlug = '';
			}
			
			$this->db->query($sql, array(
					'landingspage_id' 		=> $id,
					'language_id' 			=> $content['language_id'],
					'slug' 					=> $newSlug,
					'slug_301' 				=> '',
					'meta_title' 			=> $content['meta_title'],
					'meta_desc' 			=> $content['meta_desc'],
					'meta_keyw' 			=> $content['meta_keyw'],
					'menu_title' 			=> $content['menu_title'],
					'content_title' 		=> $content['content_title'],
					'content_description' 	=> $content['content_description'],
					'content_text' 			=> $content['content_text'],
					'overview_title' 		=> $content['overview_title'],
					'overview_description' 	=> $content['overview_description'],
					'overview_text' 		=> $content['overview_text'],
					'ex_name' 				=> $content['ex_name'],
					'ex_url' 				=> $content['ex_url'],
					'sub_active' 			=> $content['sub_active']
			));
			
		}
		
		//Collect mobile content
		$sql = '
			SELECT * FROM `landingsmobile_content`
			WHERE `landingsmobile_content`.landingspage_id = ?
		';
		$landingsmobile = $this->db->query($sql, array($landingspage_id));
		
		foreach($landingsmobile as $mobile)
		{
			$sql = 'INSERT INTO `landingsmobile_content`
			(
				landingspage_id,
				language_id,
				slug,
				slug_301,
				meta_title,
				meta_desc,
				meta_keyw,
				menu_title,
				content_title,
				content_description,
				content_text,
				overview_title,
				overview_description,
				overview_text,
				ex_name,
				ex_url,
				sub_active
			)
			VALUES
			(
				:landingspage_id,
				:language_id,
				:slug,
				:slug_301,
				:meta_title,
				:meta_desc,
				:meta_keyw,
				:menu_title,
				:content_title,
				:content_description,
				:content_text,
				:overview_title,
				:overview_description,
				:overview_text,
				:ex_name,
				:ex_url,
				:sub_active
			)';
			
			if($mobile['slug'] != '')
			{
				$newSlug = generateLandingsPageSlug($mobile['slug'], 'landingsmobile_content');
			}
			else
			{
				$newSlug = '';
			}
			
			$this->db->query($sql, array(
					'landingspage_id' 		=> $id,
					'language_id' 			=> $mobile['language_id'],
					'slug' 					=> $newSlug,
					'slug_301' 				=> '',
					'meta_title' 			=> $mobile['meta_title'],
					'meta_desc' 			=> $mobile['meta_desc'],
					'meta_keyw' 			=> $mobile['meta_keyw'],
					'menu_title' 			=> $mobile['menu_title'],
					'content_title' 		=> $mobile['content_title'],
					'content_description' 	=> $mobile['content_description'],
					'content_text' 			=> $mobile['content_text'],
					'overview_title' 		=> $mobile['overview_title'],
					'overview_description' 	=> $mobile['overview_description'],
					'overview_text' 		=> $mobile['overview_text'],
					'ex_name' 				=> $mobile['ex_name'],
					'ex_url' 				=> $mobile['ex_url'],
					'sub_active' 			=> $mobile['sub_active']
			));
		}
		

		//Collect media items
		$sql = '
			SELECT * FROM `media`
			WHERE `media`.controller = ?
			AND `media`.table_id = ?
		';
		$media = $this->db->query($sql, array(CONTROLLER, $landingspage_id));
		
		//Collect media content
		$sql = '
			SELECT * FROM `media`, `media_content`
			WHERE `media`.controller = ?
			AND `media`.media_id = `media_content`.media_id
			AND `media`.table_id = ?
			AND `media_content`.language_id = ?
		';
		$media = $this->db->query($sql, array(CONTROLLER, $landingspage_id, $this->config->item('default_language')));
		
		if(isset($media) && count($media) > 0)
		{
			foreach($media as $obj)
			{
				$i = 1;
				while(file_exists(BASE_PATH.MEDIA_DIR.'landingspages/crop_original/'.$i.'_'.$obj['filename']))
				{
					$i++;
				}	
				
				copy ( BASE_PATH.MEDIA_DIR.'landingspages/crop_original/'.$obj['filename'] , BASE_PATH.MEDIA_DIR.'landingspages/crop_original/'.$i.'_'.$obj['filename'] );
				
				foreach($image_settings as $setting){
					$folder = $setting['sub_folder'];
					copy ( BASE_PATH.MEDIA_DIR.'landingspages/'.$folder.'/'.$obj['filename'] , BASE_PATH.MEDIA_DIR.'landingspages/'.$folder.'/'.$i.'_'.$obj['filename'] );
				}
				
				if($obj['album_id'] == 0)
				{
					$sql = '
						SELECT MAX(`media`.order) AS `order`
						FROM `media`
						WHERE `media`.table_id = ?
					';
					
					$order = $this->db->query($sql, array($id));
				}
				else
				{
					$sql = '
						SELECT MAX(`media`.order) AS `order`
						FROM `media`
						WHERE `media`.album_id = ?
					';
					
					$order = $this->db->query($sql, array($id));
				}
				
				//TODO exclude default language and insert default language with set options, than loop through languages
				$sql = 'SELECT `language`.language_id FROM `language`';
				
				$language = $this->db->query($sql);
				
             	$sql = '
            	INSERT INTO `media`
            	(
            		`media`.table_id,
					`media`.album_id,
            		`media`.media_type_id,
            		`media`.filename,
            		`media`.order,
            		`media`.controller,
					`media`.album_thumb
            	)
            	VALUES
            	(
            		:table_id,
					:album_id,
            		:media_type_id,
            		:filename,
            		:order,
            		:controller,
					:album_thumb
            	)
            	';
            	
            	$this->db->query($sql, array(
            		'table_id' 		=> $new_landingspage_id,
					'album_id'		=> $obj['album_id'],
            		'media_type_id' => 1,
            		'filename' 		=> $i.'_'.$obj['filename'],
            		'order' 		=> $order[0]['order'] + 1,
            		'controller' 	=> $obj['controller'],
					'album_thumb'	=> $obj['album_thumb']
            	));
            	
            	$id = $this->db->last_insert_id;
				
				foreach($language as $lang)
				{
					$sql = '
					INSERT INTO `media_content`
					(
						`media_content`.media_id,
						`media_content`.language_id,
						`media_content`.title
					)
					VALUES
					(
						:media_id,
						:language_id,
						:title
					)
					';
            	 
					$this->db->query($sql, array(
            			'media_id' 		=> $id,
            			'language_id' 	=> $lang['language_id'],
            			'title' 		=> $obj['album_thumb']
					)); 
				}
			}
		}
	
	
		// categories
		$r = $this->db->query('SELECT category_id FROM `category_selected` WHERE `category_selected`.table_id = ? AND `category_selected`.controller = ?', array($landingspage_id, CONTROLLER));
		
		if(isset($r) && $r && count($r) > 0){
			foreach($r as $item){
				
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
					'table_id' 		=> $new_landingspage_id,
					'controller' 	=> CONTROLLER,
					'category_id' 	=> $item['category_id']
				));
				
			}
		}
		
		
		// filters
		if(isset($landingspage['category_id'])) {
			
			$sql = 'SELECT * FROM `filter_item_saved`
						WHERE `filter_item_saved`.table_id = ?';
				$filters = $this->db->query($sql, array($landingspage_id));
				
				if(isset($filters) && count($filters) > 0){
					foreach($filters as $filter){
						$sql = 'INSERT INTO `filter_item_saved`
								(
									`filter_item_saved`.saved,
									`filter_item_saved`.table_id,
									`filter_item_saved`.filter_item_id
								)
								VALUES
								(
									:saved,
									:table_id,
									:filter_item_id
								)
								';
								
						$this->db->query($sql, array(
									'saved' 					=> $filter['saved'],
									'table_id' 					=> $new_landingspage_id,
									'filter_item_id' 			=> $filter['filter_item_id']
								));	
					}
				}				
		}
	}
	
	function add($post)
	{
		//Auto settings:
		$order = $this->db->query('SELECT MAX(`order`) + 1 AS `order` FROM `landingspage`');

		if($order[0]['order'] == '')
		{
			$order = 0;
		}
		else
		{
			$order = $order[0]['order'];
		}

		$this->db->query('INSERT INTO landingspage(`parent_id`, `external`, `order`, `date_created`, `edit_by`) VALUES(?, ?, ?, ?, ?)', array($post['parent_id'], $post['external'], $order, time(), $_SESSION['username']));
		
		$id = $this->db->last_insert_id;
		
		foreach($post['form'] as $k => $v)
		{
			$sql = '
			INSERT INTO '.$k.' 
			(
				landingspage_id,
				language_id,
				slug,
				meta_title,
				meta_desc,
				meta_keyw,
				menu_title,
				content_title,
				content_description,
				content_text,
				overview_title,
				overview_description,
				overview_text,
				ex_name,
				ex_url,
				sub_active
			)
			VALUES
			(
				:landingspage_id,
				:language_id,
				:slug,
				:meta_title,
				:meta_desc,
				:meta_keyw,
				:menu_title,
				:content_title,
				:content_description,
				:content_text,
				:overview_title,
				:overview_description,
				:overview_text,
				:ex_name,
				:ex_url,
				:sub_active
			)';
		
			$this->db->query($sql, array(
					'landingspage_id' 		=> $id,
					'language_id' 			=> $this->config->item('default_language'),
					'slug' 					=> $this->url->string_to_url($post['form'][$k]['slug']),
					'meta_title' 			=> $post['form'][$k]['meta_title'],
					'meta_desc' 			=> $post['form'][$k]['meta_desc'],
					'meta_keyw' 			=> $post['form'][$k]['meta_keyw'],
					'menu_title' 			=> ucfirst($post['form'][$k]['menu_title']),
					'content_title' 		=> ucfirst($post['form'][$k]['content_title']),
					'content_description' 	=> $post['form'][$k]['content_description'],
					'content_text' 			=> $post['form'][$k]['content_text'],
					'overview_title' 		=> ucfirst($post['form'][$k]['overview_title']),
					'overview_description' 	=> $post['form'][$k]['overview_description'],
					'overview_text' 		=> $post['form'][$k]['overview_text'],
					'ex_name' 				=> ucfirst($post['form'][$k]['ex_name']),
					'ex_url' 				=> $post['form'][$k]['ex_url'],
					'sub_active' 			=> $post['form']['landingspage_content']['sub_active']
			));
		}
		
		$languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array($this->config->item('default_language')));
		
		foreach($languages as $language)
		{
			foreach($post['form'] as $k => $v)
			{				
				$sql = '
				INSERT INTO '.$k.' 
				(
					landingspage_id,
					language_id,
					sub_active
				)
				VALUES
				(
					:landingspage_id,
					:language_id,
					:sub_active
				)';
				
				$this->db->query($sql, array(
				'landingspage_id' => $id,
				'language_id' => $language['language_id'],
				'sub_active' => 0
				));
			}
		}

		if(haveFilters('landingspages'))
		{
			$this->addFilters($id, $post['landingspage']['filters'], $this->config->item('default_language'), 'landingspages');
		}
		$this->members_model->attach_caregories($post['landingspage']['category_id'], $id, CONTROLLER);		
		
		return $id;
	}
	
	function edit($landingspage_id, $language_id, $post)
	{
		$sql = '
			UPDATE `landingspage` 
			SET 
				`landingspage`.parent_id 	= :parent_id,
				`landingspage`.external 	= :external,
				`landingspage`.last_update 	= :last_update, 
				`landingspage`.edit_by 		= :edit_by
			WHERE 
				`landingspage`.landingspage_id = :landingspage_id';
				
		$this->db->query($sql, array(
			'parent_id' 		=> $post['parent_id'],
			'external' 			=> $post['external'],
			'landingspage_id' 	=> $landingspage_id,
			'last_update' 		=> time(),
			'edit_by' 			=> $_SESSION['username']
		));
		
		foreach($post['form'] as $k => $v)
		{
			$sql = 'SELECT `' . $k . '`.slug FROM `' . $k . '` WHERE `' . $k . '`.landingspage_id = ? AND `' . $k . '`.language_id = ?';
			$content = $this->db->query($sql, array($landingspage_id, $language_id));
			
			$old_slug = $content[0]['slug'];
			
			if($old_slug != $this->url->string_to_url($post['form'][$k]['slug']))
			{
				$sql = '
				UPDATE `'.$k.'`
				SET `'.$k.'`.slug_301 = :slug_301
				WHERE `'.$k.'`.landingspage_id = :landingspage_id
				AND `'.$k.'`.language_id = :language_id
				';
				
				$this->db->query($sql, array(
					'landingspage_id' => $landingspage_id,
					'language_id' => $language_id,
					'slug_301' => $old_slug
				));
			}
			
			$sql = '
			UPDATE `'.$k.'`
			SET
				`'.$k.'`.slug = :slug,
				`'.$k.'`.meta_title = :meta_title,
				`'.$k.'`.meta_desc = :meta_desc,
				`'.$k.'`.meta_keyw = :meta_keyw,
				`'.$k.'`.menu_title = :menu_title,
				`'.$k.'`.content_title = :content_title,
				`'.$k.'`.content_description = :content_description,
				`'.$k.'`.content_text = :content_text,
				`'.$k.'`.overview_title = :overview_title,
				`'.$k.'`.overview_description = :overview_description,
				`'.$k.'`.overview_text = :overview_text,
				`'.$k.'`.ex_name = :ex_name,
				`'.$k.'`.ex_url = :ex_url,
				`'.$k.'`.sub_active = :sub_active
			WHERE `'.$k.'`.landingspage_id = :landingspage_id
			AND `'.$k.'`.language_id = :language_id
			';

			$this->db->query($sql, array(
			'landingspage_id' 		=> $landingspage_id,
			'language_id' 			=> $language_id,
			'slug' 					=> $this->url->string_to_url($post['form'][$k]['slug']),
			'meta_title' 			=> $post['form'][$k]['meta_title'],
			'meta_desc' 			=> $post['form'][$k]['meta_desc'],
			'meta_keyw' 			=> $post['form'][$k]['meta_keyw'],
			'menu_title' 			=> ucfirst($post['form'][$k]['menu_title']),
			'content_title' 		=> ucfirst($post['form'][$k]['content_title']),
			'content_description' 	=> $post['form'][$k]['content_description'],
			'content_text' 			=> $post['form'][$k]['content_text'],
			'overview_title' 		=> ucfirst($post['form'][$k]['overview_title']),
			'overview_description' 	=> $post['form'][$k]['overview_description'],
			'overview_text' 		=> $post['form'][$k]['overview_text'],
			'ex_name' 				=> ucfirst($post['form'][$k]['ex_name']),
			'ex_url' 				=> $post['form'][$k]['ex_url'],
			'sub_active' 			=> $post['form']['landingspage_content']['sub_active']
			));
		}
		
		if(haveFilters('landingspages'))
		{
			$this->addFilters($landingspage_id, $post['landingspage']['filters'], $this->config->item('default_language'), 'landingspages');
		}
		
		foreach($post['media'] as $media)
		{
			$sql = '
				UPDATE `media`, `media_content`
				SET
					`media`.album_thumb = :album_thumb
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
		$this->members_model->attach_caregories($post['landingspage']['category_id'], $landingspage_id, CONTROLLER);		
		
	}
	
	function update_overview($post)
	{
		if(isset($post['active']))
		{
			foreach($post['active'] as $k => $v)
			{
				$this->db->query('UPDATE `landingspage` SET `landingspage`.active = ? WHERE `landingspage`.landingspage_id = ?', array($v, $k));
			}
		}
		if(isset($post['main_menu']))
		{
			foreach($post['main_menu'] as $k => $v)
			{
				$this->db->query('UPDATE `landingspage` SET `landingspage`.main_menu = ? WHERE `landingspage`.landingspage_id = ?', array($v, $k));
			}
		}
	}
	
	function order_media($direction, $table_id, $language_id, $current_order)
	{
		//WHERE CONTROLLER = PHOTO TYPE IN DB CHECK TOEVOEGEN
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
		$sql = 'SELECT * FROM `landingspage` WHERE `landingspage`.landingspage_id = ? AND `landingspage`.deletable = 1';
		$r = $this->db->query($sql, array($id));
		
		$sql = 'SELECT * FROM `landingspage` WHERE `landingspage`.parent_id = ?';
		$children = $this->db->query($sql, array($id));

		if(!empty($r) && empty($children))
		{
			$this->db->query('DELETE FROM `landingspage` WHERE `landingspage`.landingspage_id = ? AND `landingspage`.deletable = 1', array($id));
			$this->db->query('DELETE FROM `landingspage_content` WHERE `landingspage_content`.landingspage_id = ?', array($id));
			$this->db->query('DELETE FROM `landingsmobile_content` WHERE `landingsmobile_content`.landingspage_id = ?', array($id));
			$this->members_model->delete_categories($id, CONTROLLER);
			
			if(haveFilters('landingspages')){
				$sql = '
				SELECT * 
				FROM `filter`
				WHERE `filter`.controller = :controller
				';	
				$data = $this->db->query($sql, array('controller' => 'landingspages'));
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
	}
	
	function order($direction, $current_order, $parent_id, $landingspage_id)
	{
		switch($direction)
		{
			case 'up':
				$from = $this->db->query('SELECT `landingspage`.order, `landingspage`.landingspage_id FROM `landingspage` WHERE `landingspage`.landingspage_id = ?', array($landingspage_id));
				$to = $this->db->query('SELECT `landingspage`.order, `landingspage`.landingspage_id FROM `landingspage` WHERE `landingspage`.order < ? AND `landingspage`.parent_id = ? ORDER BY `landingspage`.order DESC', array($current_order, $parent_id));
				
				if(!empty($to))
				{
					$this->db->query('UPDATE `landingspage` SET `landingspage`.order = ? WHERE `landingspage`.landingspage_id = ?', array($to[0]['order'], $from[0]['landingspage_id']));
					$this->db->query('UPDATE `landingspage` SET `landingspage`.order = ? WHERE `landingspage`.landingspage_id = ?', array($from[0]['order'], $to[0]['landingspage_id']));
				}
			break;
				
			case 'down':
				$from = $this->db->query('SELECT `landingspage`.order, `landingspage`.landingspage_id FROM `landingspage` WHERE `landingspage`.landingspage_id = ?', array($landingspage_id));
				$to = $this->db->query('SELECT `landingspage`.order, `landingspage`.landingspage_id FROM `landingspage` WHERE `landingspage`.order > ? AND `landingspage`.parent_id = ? ORDER BY `landingspage`.order ASC', array($current_order, $parent_id));
				
				if(!empty($to))
				{
					$this->db->query('UPDATE `landingspage` SET `landingspage`.order = ? WHERE `landingspage`.landingspage_id = ?', array($to[0]['order'], $from[0]['landingspage_id']));
					$this->db->query('UPDATE `landingspage` SET `landingspage`.order = ? WHERE `landingspage`.landingspage_id = ?', array($from[0]['order'], $to[0]['landingspage_id']));
				}
			break;
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
	
	function duplicate_media($media_id)
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
	
	
	function fetch_category_filters($language_id, $controller, $prod_id = 0)
	{
		
		$sql = '
		SELECT * 
		FROM `filter`, `filter_heading`
		WHERE `filter`.filter_id = `filter_heading`.filter_id
		AND `filter_heading`.language_id = :lang
		AND `filter`.controller = :controller
		';
		
		$data = $this->db->query($sql, array('lang' => $this->config->item('default_language'),'controller' => $controller));
		
		$i = 0;
		
		$extra_sql = '';
		$extra_sql .= " AND  `filter_item_category`.category_id IN (
												SELECT category_id FROM `category_selected` WHERE `category_selected`.table_id = ".$prod_id." AND `category_selected`.controller = '".$controller."' 
												) ";
						
		if(!empty($data))
		{
			foreach($data as $filter)
			{
				$return[$i] = $filter;
				$return[$i]['selected'] = array();
				
				$sql = '
				SELECT *, `filter_item`.filter_item_id as filter_item_id_number, `filter_item`.title as filter_item_title		
					FROM `filter`, `filter_item`, `filter_heading`, `filter_item_category`
					WHERE `filter_heading`.filter_id = `filter`.filter_id
					
					AND `filter_item_category`.filter_item_id = `filter`.filter_id				
					AND `filter_item`.filter_heading_id = `filter_heading`.filter_heading_id
					'.$extra_sql.'
					AND `filter_item_category`.saved = 1
					AND `filter_heading`.language_id = :language_id
					AND `filter`.filter_id = :filter_id
					
				';
				
				$subelements2 = $this->db->query($sql, array('language_id' => $language_id, 'filter_id' => $filter['filter_id']));
				//debug($subelements2);
				
				if(isset($subelements2) && $subelements2 && count($subelements2) > 0){
					foreach($subelements2 as $j => $sub_element){
						$return[$i]['subelements'][$sub_element['filter_item_identify']] = $sub_element;

						$sql = '
						SELECT *
						FROM `filter_item_saved`
						WHERE `filter_item_saved`.filter_item_id = :filter_item_id
						AND `filter_item_saved`.table_id = :table_id
						AND `filter_item_saved`.saved = 1
						';
						
						$selected = $this->db->query($sql, array('filter_item_id' => $sub_element['filter_item_id_number'], 'table_id' => $prod_id));
						if($selected && count($selected) >= 1){
							foreach($selected as $option){
								$return[$i]['selected'][] = $option['filter_item_id'];
							}
						}
						//else $return[$i]['selected'] = array();
					}	
				}
				else{
					unset($return[$i]);
				}
				$i++;
			}
		}
		if(isset($return) && $return && count($return) > 0) return $return;
		else return false;
	}
	
	function fetch_filters($controller, $language_id, $prod_id = 0)
	{
		
		$sql = '
		SELECT * 
		FROM `filter`, `filter_heading`
		WHERE `filter`.filter_id = `filter_heading`.filter_id
		AND `filter_heading`.language_id = :lang
		AND `filter`.controller = :controller
		';
		
		$data = $this->db->query($sql, array('lang' => $this->config->item('default_language'),'controller' => $controller));
		
		$i = 0;
		
		$extra_sql = '';
		$extra_sql .= " AND  `filter_item_saved`.category_id IN (
												SELECT category_id FROM `category_selected` WHERE `category_selected`.table_id = ".$prod_id." AND `category_selected`.controller = '".$controller."' 
												) ";
						
		if(!empty($data))
		{
			foreach($data as $filter)
			{
				$return[$i] = $filter;
				$return[$i]['selected'] = array();
				
				$sql = '
				SELECT *, `filter_item`.filter_item_id as filter_item_id_number, `filter_item`.title as filter_item_title		
					FROM `filter`, `filter_item`, `filter_heading`
					WHERE `filter_heading`.filter_id = `filter`.filter_id
					
					AND `filter_item`.filter_heading_id = `filter_heading`.filter_heading_id
					AND `filter_heading`.language_id = :language_id
					AND `filter`.filter_id = :filter_id
					
				';
				
				$subelements2 = $this->db->query($sql, array('language_id' => $language_id, 'filter_id' => $filter['filter_id']));
				//debug($subelements2);
				
				if(isset($subelements2))
					foreach($subelements2 as $j => $sub_element){
						$return[$i]['subelements'][$sub_element['filter_item_identify']] = $sub_element;

						$sql = '
						SELECT *
						FROM `filter_item_saved`
						WHERE `filter_item_saved`.filter_item_id = :filter_item_id
						'.$extra_sql.'
						AND `filter_item_saved`.saved = 1
						';
						
						$selected = $this->db->query($sql, array('filter_item_id' => $sub_element['filter_item_id_number']));
						if($selected && count($selected) >= 1){
							foreach($selected as $option){
								$return[$i]['selected'][] = $option['filter_item_id'];
							}
						}
						//else $return[$i]['selected'] = array();
					}
				$i++;
			}
		}
		if(isset($return) && $return && count($return) > 0) return $return;
		else return false;
	}
	
	function getFilters($controller, $language_id, $id = 0, $category_id = 0){
		if(haveFilters($controller)){
			if(!haveCategories($controller)) return $this->fetch_filters($controller, $language_id, $id);
			else return $this->fetch_category_filters($language_id, $controller, $id);
		}
		else return false;	
	}
	
	function addFilters($id, $posted_filters, $language_id, $controller){
		
		$sql = '
					SELECT * 
					FROM `filter`
					WHERE `filter`.controller = :controller
					';	
			$data = $this->db->query($sql, array('controller' => $controller));
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
							$this->db->query('DELETE FROM `filter_item_saved` WHERE `filter_item_saved`.table_id=? AND `filter_item_saved`.filter_item_id = ?', array($id, $sub_element['filter_item_id']));		
				}
			}	
		
		if(isset($posted_filters) && $posted_filters && count($posted_filters) > 0)
				foreach($posted_filters as $k => $filters)
				{
					if(is_array($filters)){
						foreach($filters as $l => $filters_subelements){
							$sql = 'INSERT INTO `filter_item_saved`
							(
								`filter_item_saved`.saved,
								`filter_item_saved`.table_id,
								`filter_item_saved`.filter_item_id
							)
							VALUES
							(
								:saved,
								:table_id,
								:filter_item_id
							)
							';
							$this->db->query($sql, array(
								'saved' 					=> 1,
								'table_id' 					=> $id,
								'filter_item_id' 			=> $filters_subelements
							));
							$arr_filters[] = $filters_subelements;
							
							$sql2 = '
							SELECT `filter_item`.* 
							FROM `filter`, `filter_item`
							WHERE `filter_item`.filter_item_id = "'.$filters_subelements.'"
							AND `filter`.controller = "'.$controller.'"';
							
							$elem = $this->db->query($sql2);
							
							if(isset($elem) && count($elem) > 0){
								$sql2 = '
										SELECT `filter_item`.* 
										FROM `filter`, `filter_item`, `filter_heading` 
										WHERE `filter_item`.filter_heading_id = `filter_heading`.filter_heading_id 
										AND `filter_item`.filter_item_identify = "'.$elem[0]['filter_item_identify'].'"
										AND `filter_item`.filter_item_id <> "'.$filters_subelements.'" 
										AND `filter`.filter_id = `filter_heading`.filter_id 
										AND `filter`.controller = "'.$controller.'"';
										
								$return = $this->db->query($sql2);
										
								foreach($return as $item){
									$sql = 'INSERT INTO `filter_item_saved`
										(
											`filter_item_saved`.saved,
											`filter_item_saved`.table_id,
											`filter_item_saved`.filter_item_id
										)
										VALUES
										(
											:saved,
											:table_id,
											:filter_item_id
										)
										';
										$this->db->query($sql, array(
											'saved' 					=> 1,
											'table_id' 					=> $id,
											'filter_item_id' 			=> $item['filter_item_id']
										));
									$arr_filters[] = $item['filter_item_id'];
								}
							}		
						}
					}else{
						$sql = 'INSERT INTO `filter_item_saved`
						(
							`filter_item_saved`.saved,
							`filter_item_saved`.table_id,
							`filter_item_saved`.filter_item_id
						)
						VALUES
						(
							:saved,
							:table_id,
							:filter_item_id
						)
						';
						$this->db->query($sql, array(
							'saved' 					=> 1,
							'table_id' 					=> $id,
							'filter_item_id' 			=> $filters
						));
						$arr_filters[] = $filters;
						
						$sql2 = '
							SELECT `filter_item`.* 
							FROM `filter`, `filter_item`
							WHERE `filter_item`.filter_item_id = "'.$filters.'"
							AND `filter`.controller = "'.$controller.'"';
							
							$elem = $this->db->query($sql2);
							
							if(isset($elem) && count($elem) > 0){
								$sql2 = '
										SELECT `filter_item`.* 
										FROM `filter`, `filter_item`, `filter_heading` 
										WHERE `filter_item`.filter_heading_id = `filter_heading`.filter_heading_id 
										AND `filter_item`.filter_item_identify = "'.$elem[0]['filter_item_identify'].'"
										AND `filter_item`.filter_item_id <> "'.$filters.'" 
										AND `filter`.filter_id = `filter_heading`.filter_id 
										AND `filter`.controller = "'.$controller.'"';
										
								$return = $this->db->query($sql2);
										
								foreach($return as $item){
									$sql = 'INSERT INTO `filter_item_saved`
										(
											`filter_item_saved`.saved,
											`filter_item_saved`.table_id,
											`filter_item_saved`.filter_item_id
										)
										VALUES
										(
											:saved,
											:table_id,
											:filter_item_id
										)
										';
										$this->db->query($sql, array(
											'saved' 					=> 1,
											'table_id' 					=> $id,
											'filter_item_id' 			=> $item['filter_item_id']
										));
									$arr_filters[] = $item['filter_item_id'];
								}
							}		
					}
				}
			
			$sql2 = '
			SELECT `filter_item`.* 
			FROM `filter`, `filter_item`, `filter_heading` 
			WHERE `filter_item`.filter_heading_id = `filter_heading`.filter_heading_id 
			AND `filter`.filter_id = `filter_heading`.filter_id 
			AND `filter_heading`.language_id = '.$language_id.'
			AND `filter`.controller = "'.$controller.'"';
			
			$return = $this->db->query($sql2);
			
			if(isset($return))
			foreach($return as $item){
				if(!in_array($item['filter_item_id'], $arr_filters)){
					
					$sql = 'INSERT INTO `filter_item_saved`
					(
						`filter_item_saved`.saved,
						`filter_item_saved`.table_id,
						`filter_item_saved`.filter_item_id
					)
					VALUES
					(
						:saved,
						:table_id,
						:filter_item_id
					)
					';
					$this->db->query($sql, array(
						'saved' 					=> 0,
						'table_id' 					=> $id,
						'filter_item_id' 			=> $item['filter_item_id']
					));
				}
			}
		
	}

}
?>