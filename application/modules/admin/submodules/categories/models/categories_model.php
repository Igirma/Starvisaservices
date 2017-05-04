<?php
class categories_model extends model
{

	function fetch_all($controller,$parent_id = 0)
	{
		$return = array();
		
		$sql = '
		SELECT *
		FROM `category`
		LEFT JOIN `category_content` ON `category`.category_id = `category_content`.category_id
		WHERE `category`.controller = :controller
		AND `category_content`.language_id = :lang
		AND `category`.parent_id = :parent_id
		ORDER BY `category`.order ASC
		';
		$data = $this->db->query($sql, array('controller' => $controller, 'lang' => $this->config->item('default_language'), 'parent_id' => $parent_id));
		
		$i = 0;
		
		foreach($data as $category)
		{
			$return[$i] = $category;
		
			$return[$i]['children'] = $this->fetch_all($controller, $category['category_id']);
		
			$i++;
		}
		
		return $return;
	}
	
	function fetch($category_id, $language_id)
	{
		$sql = '
			SELECT *
			FROM `category`
			INNER JOIN `category_content`
			ON `category_content`.category_id = `category`.category_id
			WHERE `category`.category_id = ?
			AND `category_content`.language_id = ?
			ORDER BY `category`.order
		';
		
		$r = $this->db->query($sql, array($category_id, $language_id));
		$data['category'] = $r[0];
		
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
	
		$data['media'] = $this->db->query($sql, array($data['category']['category_id'], CONTROLLER, $language_id));
	
		return $data;
	}
	
	function fetch_products_categories($controller)
	{
		$sql = '
			SELECT * FROM `category` 
			INNER JOIN `category_content` 
				ON `category_content`.category_id = `category`.category_id
			WHERE `category_content`.language_id = :language_id
			AND `category`.controller = "products"
			ORDER BY `category_content`.title ASC
		';
		
		$r = $this->db->query($sql, array('language_id' => $this->config->item('default_language')));
		$data = $r;
		
		foreach($data as $k => $v)
		{
			$data[$k]['selected'] = 0;
		}
		
		return $data;
	}
	
	function fetch_products($controller)
	{
		$sql = '
			SELECT * FROM `product` 
			INNER JOIN `product_content` 
				ON `product_content`.product_id = `product`.product_id
			WHERE `product_content`.language_id = :language_id
			ORDER BY `product_content`.title ASC
		';
		
		$r = $this->db->query($sql, array('language_id' => $this->config->item('default_language')));
		$data = $r;
		
		foreach($data as $k => $v)
		{
			$data[$k]['selected'] = 0;
		}
		
		return $data;
	}	
	
	function add($post, $controller)
	{
		$sql = '
			SELECT MAX(`category`.order) AS `order`
			FROM `category`
		';
					
		$order = $this->db->query($sql);
	
		$sql = '
		INSERT INTO `category`
		(
			`category`.parent_id,
			`category`.controller,
			`category`.order
		)
		VALUES
		(
			:parent_id,
			:controller,
			:order
		)
		';
		
		$this->db->query($sql, array(
			'parent_id' 	=> $post['category']['parent_id'],
			'controller' 	=> $controller,
			'order'			=> $order[0]['order'] + 1
		));
		
		$category_id = $this->db->last_insert_id;
		
		if($controller=='products')
		{
			$sql = '
				INSERT INTO `category_content`
				(
					`category_content`.category_id,
					`category_content`.language_id,
					`category_content`.title,
					`category_content`.description,
					`category_content`.content,
          `category_content`.option_1,
          `category_content`.option_2,
          `category_content`.option_3,
          `category_content`.value_1,
          `category_content`.value_2,
          `category_content`.value_3,
					`category_content`.discount_percent,
					`category_content`.discount_price,
					`category_content`.discount_primary,
					`category_content`.slug,
					`category_content`.meta_title,
					`category_content`.meta_keyw,
					`category_content`.meta_desc,
					`category_content`.sub_active
				)
				VALUES
				(
					:category_id,
					:language_id,
					:title,
					:description,
					:content,
          :option_1,
          :option_2,
          :option_3,
          :value_1,
          :value_2,
          :value_3,
					:discount_percent,
					:discount_price,
					:discount_primary,
					:slug,
					:meta_title,
					:meta_keyw,
					:meta_desc,
					:sub_active
				)
			';
			
			$this->db->query($sql, array(
				'category_id' 	=> $category_id,
				'language_id' 	=> $this->config->item('default_language'),
				'title' 		=> $post['category']['title'],
				'description' 	=> $post['category']['description'],
				'content' 		=> $post['category']['content'],
        'option_1' 		=> $post['category']['option_1'],
        'option_2' 		=> $post['category']['option_2'],
        'option_3' 		=> $post['category']['option_3'],
        'value_1' 		=> $post['category']['value_1'],
        'value_2' 		=> $post['category']['value_2'],
        'value_3' 		=> $post['category']['value_3'],
				'discount_percent' 	=> $post['category']['discount_percent'],
				'discount_price' 	=> $post['category']['discount_price'],
				'discount_primary' 	=> ((isset($post['category']['discount_primary']))?1:0),
				'slug' 			=> $post['category']['slug'],
				'meta_title' 	=> $post['category']['meta_title'],
				'meta_keyw' 	=> $post['category']['meta_keyw'],
				'meta_desc' 	=> $post['category']['meta_desc'],
				'sub_active'	=> 1
			));
		}
		else
		{
			$sql = '
			INSERT INTO `category_content`
			(
				`category_content`.category_id,
				`category_content`.language_id,
				`category_content`.title,
				`category_content`.description,
				`category_content`.content,
				`category_content`.slug,
				`category_content`.meta_title,
				`category_content`.meta_keyw,
				`category_content`.meta_desc,
				`category_content`.sub_active
			)
			VALUES
			(
				:category_id,
				:language_id,
				:title,
				:description,
				:content,
				:slug,
				:meta_title,
				:meta_keyw,
				:meta_desc,
				:sub_active
			)
			';
			
			$this->db->query($sql, array(
				'category_id' 	=> $category_id,
				'language_id' 	=> $this->config->item('default_language'),
				'title' 		=> $post['category']['title'],
				'description' 	=> $post['category']['description'],
				'content' 		=> $post['category']['content'],
				'slug' 			=> $post['category']['slug'],
				'meta_title' 	=> $post['category']['meta_title'],
				'meta_keyw' 	=> $post['category']['meta_keyw'],
				'meta_desc' 	=> $post['category']['meta_desc'],
				'sub_active'	=> 1
			));
		}
		
		$languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array($this->config->item('default_language')));
		
		foreach($languages as $language)
		{				
			$sql = '
			INSERT INTO `category_content`
			(
				category_id,
				language_id,
				sub_active
			)
			VALUES
			(
				:category_id,
				:language_id,
				:sub_active
			)';
			
			$this->db->query($sql, array(
				'category_id' 	=> $category_id,
				'language_id' 	=> $language['language_id'],
				'sub_active' 	=> 0
			));
		}
		
		$sql = 'DELETE FROM `product_options_item_category` WHERE `product_options_item_category`.category_id=?';
		$this->db->query($sql, array($category_id));
		
		if(isset($post['category']['products_options']) && $post['category']['products_options'] && count($post['category']['products_options']) > 0)
		{
			foreach($post['category']['products_options'] as $k => $products_options)
			{
				$sql = 'INSERT INTO `product_options_item_category`
						(
							 `product_options_item_category`.category_id,
							 `product_options_item_category`.product_options_item_id
						)
						 VALUES
						 (
							 :category_id,
							 :product_options_item_id
						 )
				 ';
				 $this->db->query($sql, array(
					 'category_id' 				=> $category_id,
					 'product_options_item_id' 	=> $products_options
				 ));
			}
		}
		
		
		if($controller=='products')
		{ 
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
							$this->db->query('DELETE FROM `filter_item_category` WHERE `filter_item_category`.category_id=? AND `filter_item_category`.filter_item_id = ?', array($category_id, $sub_element['filter_item_id']));		
				}
			}
		}
		else{
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
							$this->db->query('DELETE FROM `filter_item_category` WHERE `filter_item_category`.category_id=? AND `filter_item_category`.filter_item_id = ?', array($category_id, $sub_element['filter_id']));		
				}
			}
		}	
		
		$this->db->query($sql, array($category_id));
		if(isset($post['category']['filters']) && $post['category']['filters'] && count($post['category']['filters']) > 0)
			foreach($post['category']['filters'] as $k => $filters)
			{
				$sql = 'INSERT INTO `filter_item_category`
				(
					`filter_item_category`.saved,
					`filter_item_category`.category_id,
					`filter_item_category`.filter_item_id
				)
				VALUES
				(
					:saved,
					:category_id,
					:filter_item_id
				)
				';
				$this->db->query($sql, array(
					'saved' 				=> 1,
					'category_id' 			=> $category_id,
					'filter_item_id' 		=> $filters
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
						$sql = 'INSERT INTO `filter_item_category`
									(
										`filter_item_category`.saved,
										`filter_item_category`.category_id,
										`filter_item_category`.filter_item_id
									)
									VALUES
									(
										:saved,
										:category_id,
										:filter_item_id
									)
									';
						$this->db->query($sql, array(
										'saved' 				=> 1,
										'category_id' 			=> $category_id,
										'filter_item_id' 		=> $item['filter_item_id']
									));
						$arr_filters[] = $item['filter_item_id'];
					}
				}		
			}
		
		$controller = $this->url->segment(3);
		if($controller == 'products'){
			$sql2 = '
			SELECT `filter_item`.* 
			FROM `filter`, `filter_item`, `filter_heading` 
			WHERE `filter_item`.filter_heading_id = `filter_heading`.filter_heading_id 
			AND `filter`.filter_id = `filter_heading`.filter_id 
			AND `filter`.controller = "'.$controller.'"';
			//AND `filter_heading`.language_id = '.$language_id.'
			
			$return = $this->db->query($sql2);
			
			foreach($return as $item){
				if(!in_array($item['filter_item_id'], $arr_filters)){
					$sql = 'INSERT INTO `filter_item_category`
					(
						`filter_item_category`.saved,
						`filter_item_category`.category_id,
						`filter_item_category`.filter_item_id
					)
					VALUES
					(
						:saved,
						:category_id,
						:filter_item_id
					)
					';
					$this->db->query($sql, array(
						'saved' 				=> 0,
						'category_id' 			=> $category_id,
						'filter_item_id' 		=> $item['filter_item_id']
					));
				}
			}

		}
		else{
			$sql2 = '
			SELECT `filter`.* 
			FROM `filter`, `filter_heading` 
			WHERE `filter`.filter_id = `filter_heading`.filter_id 
			AND `filter_heading`.language_id = '.$this->config->item('default_language').'
			AND `filter`.controller = "'.$controller.'"';
			
			$return = $this->db->query($sql2);
			
			foreach($return as $item){
				if(!in_array($item['filter_id'], $arr_filters)){
					$sql = 'INSERT INTO `filter_item_category`
					(
						`filter_item_category`.saved,
						`filter_item_category`.category_id,
						`filter_item_category`.filter_item_id
					)
					VALUES
					(
						:saved,
						:category_id,
						:filter_item_id
					)
					';
					$this->db->query($sql, array(
						'saved' 				=> 0,
						'category_id' 			=> $category_id,
						'filter_item_id' 		=> $item['filter_id']
					));
				}
			}
		
		}
		
		return $category_id;
	}
	
	function edit($post, $id, $language_id, $controller)
	{
		$sql = 'SELECT `category`.parent_id FROM `category` WHERE `category`.category_id = ?';
		$parent = $this->db->query($sql, array($id));
		$parent = $parent[0]['parent_id'];
		
		if($post['category']['parent_id'] != $parent)
		{
			$order = $this->db->query('SELECT MAX(`order`) + 1 AS `order` FROM `category` WHERE `category`.parent_id = ?', array($post['category']['parent_id']));
			$order = $order[0]['order'];
		}
		else
		{
			$order = $this->db->query('SELECT `category`.order FROM `category` WHERE `category`.category_id = ?', array($id));
			$order = $order[0]['order'];
		}

		
		$sql = 'SELECT `category_content`.slug FROM `category_content` WHERE `category_content`.category_id = ? AND `category_content`.language_id = ?';
		$content = $this->db->query($sql, array($id, $language_id));
		
		$old_slug = $content[0]['slug'];
		
		if($old_slug != $this->url->string_to_url($post['category']['slug']))
		{
			$sql = '
			UPDATE `category_content`
			SET `category_content`.slug_301 = :slug_301
			WHERE `category_content`.category_id = :category_id
			AND `category_content`.language_id = :language_id
			';
			
			$this->db->query($sql, array(
				'category_id' => $id,
				'language_id' => $language_id,
				'slug_301' => $old_slug
			));
		}

		if($controller=='products')
		{ 	 	 	
			$sql = '
			UPDATE `category`, `category_content`
			SET
				`category`.order 				= :order,
				`category`.parent_id 			= :parent_id,
				`category_content`.title 		= :title,
				`category_content`.description 	= :description,
				`category_content`.content 		= :content,
        `category_content`.option_1 		= :option_1,
        `category_content`.option_2 		= :option_2,
        `category_content`.option_3 		= :option_3,
        `category_content`.value_1 		= :value_1,
        `category_content`.value_2 		= :value_2,
        `category_content`.value_3 		= :value_3,
				`category_content`.meta_title 	= :meta_title,
				`category_content`.meta_desc 	= :meta_desc,
				`category_content`.meta_keyw 	= :meta_keyw,
				`category_content`.sub_active 	= :sub_active,
				`category_content`.discount_percent 	= :discount_percent,
				`category_content`.discount_price 	= :discount_price,
				`category_content`.discount_primary = :discount_primary,
				`category_content`.slug 		= :slug
			WHERE `category`.category_id 		= :category_id
			AND `category_content`.category_id 	= :category_id
			AND `category_content`.language_id 	= :language_id
			';
			
			$this->db->query($sql, array(
				'order' 		=> $order,
				'parent_id' 	=> $post['category']['parent_id'],
				'title' 		=> ucfirst($post['category']['title']),
				'description' 	=> $post['category']['description'],
				'content' 		=> $post['category']['content'],
        'option_1' 		=> $post['category']['option_1'],
        'option_2' 		=> $post['category']['option_2'],
        'option_3' 		=> $post['category']['option_3'],
        'value_1' 		=> $post['category']['value_1'],
        'value_2' 		=> $post['category']['value_2'],
        'value_3' 		=> $post['category']['value_3'],
				'meta_title' 	=> $post['category']['meta_title'],
				'meta_desc' 	=> $post['category']['meta_desc'],
				'meta_keyw' 	=> $post['category']['meta_keyw'],
				'sub_active' 	=> $post['category']['sub_active'],
				'discount_percent' 	=> $post['category']['discount_percent'],
				'discount_price' 	=> $post['category']['discount_price'],
				'discount_primary' 	=> ((isset($post['category']['discount_primary']))?1:0),
				'slug' 			=> $this->url->string_to_url($post['category']['slug']),
				'category_id' 	=> $id,
				'language_id' 	=> $language_id
			));		

		}else{
			$sql = '
			UPDATE `category`, `category_content`
			SET
				`category`.order 				= :order,
				`category`.parent_id 			= :parent_id,
				`category_content`.title 		= :title,
				`category_content`.description 	= :description,
				`category_content`.content 		= :content,
				`category_content`.meta_title 	= :meta_title,
				`category_content`.meta_desc 	= :meta_desc,
				`category_content`.meta_keyw 	= :meta_keyw,
				`category_content`.sub_active 	= :sub_active,
				`category_content`.slug 		= :slug
			WHERE `category`.category_id 		= :category_id
			AND `category_content`.category_id 	= :category_id
			AND `category_content`.language_id 	= :language_id
			';
			
			$this->db->query($sql, array(
				'order' 		=> $order,
				'parent_id' 	=> $post['category']['parent_id'],
				'title' 		=> ucfirst($post['category']['title']),
				'description' 	=> $post['category']['description'],
				'content' 		=> $post['category']['content'],
				'meta_title' 	=> $post['category']['meta_title'],
				'meta_desc' 	=> $post['category']['meta_desc'],
				'meta_keyw' 	=> $post['category']['meta_keyw'],
				'sub_active' 	=> $post['category']['sub_active'],
				'slug' 			=> $this->url->string_to_url($post['category']['slug']),
				'category_id' 	=> $id,
				'language_id' 	=> $language_id
			));
		}
		
		$sql = 'DELETE FROM `product_options_item_category` WHERE `product_options_item_category`.category_id=?';
		$this->db->query($sql, array($id));
		if(isset($post['category']['products_options']) && $post['category']['products_options'] && count($post['category']['products_options']) > 0)
			foreach($post['category']['products_options'] as $k => $products_options)
			{
				$sql = 'INSERT INTO `product_options_item_category`
				(
					`product_options_item_category`.category_id,
					`product_options_item_category`.product_options_item_id
				)
				VALUES
				(
					:category_id,
					:product_options_item_id
				)
				';
				$this->db->query($sql, array(
					'category_id' 				=> $id,
					'product_options_item_id' 	=> $products_options
				));
			}
		
		if($controller=='products')
		{ 
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
							$this->db->query('DELETE FROM `filter_item_category` WHERE `filter_item_category`.category_id=? AND `filter_item_category`.filter_item_id = ?', array($id, $sub_element['filter_item_id']));		
				}
			}
		}
		else{
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
							$this->db->query('DELETE FROM `filter_item_category` WHERE `filter_item_category`.category_id=? AND `filter_item_category`.filter_item_id = ?', array($id, $sub_element['filter_id']));		
				}
			}
		}	
		
		$this->db->query($sql, array($id));
		if(isset($post['category']['filters']) && $post['category']['filters'] && count($post['category']['filters']) > 0)
			foreach($post['category']['filters'] as $k => $filters)
			{
				$sql = 'INSERT INTO `filter_item_category`
				(
					`filter_item_category`.saved,
					`filter_item_category`.category_id,
					`filter_item_category`.filter_item_id
				)
				VALUES
				(
					:saved,
					:category_id,
					:filter_item_id
				)
				';
				$this->db->query($sql, array(
					'saved' 				=> 1,
					'category_id' 			=> $id,
					'filter_item_id' 		=> $filters
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
						$sql = 'INSERT INTO `filter_item_category`
									(
										`filter_item_category`.saved,
										`filter_item_category`.category_id,
										`filter_item_category`.filter_item_id
									)
									VALUES
									(
										:saved,
										:category_id,
										:filter_item_id
									)
									';
						$this->db->query($sql, array(
										'saved' 				=> 1,
										'category_id' 			=> $id,
										'filter_item_id' 		=> $item['filter_item_id']
									));
						$arr_filters[] = $item['filter_item_id'];
					}
				}		
			}
		
		$controller = $this->url->segment(3);
		if($controller == 'products'){
			$sql2 = '
			SELECT `filter_item`.* 
			FROM `filter`, `filter_item`, `filter_heading` 
			WHERE `filter_item`.filter_heading_id = `filter_heading`.filter_heading_id 
			AND `filter`.filter_id = `filter_heading`.filter_id 
			AND `filter`.controller = "'.$controller.'"';
			//AND `filter_heading`.language_id = '.$language_id.'
			
			$return = $this->db->query($sql2);
			
			foreach($return as $item){
				if(!in_array($item['filter_item_id'], $arr_filters)){
					$sql = 'INSERT INTO `filter_item_category`
					(
						`filter_item_category`.saved,
						`filter_item_category`.category_id,
						`filter_item_category`.filter_item_id
					)
					VALUES
					(
						:saved,
						:category_id,
						:filter_item_id
					)
					';
					$this->db->query($sql, array(
						'saved' 				=> 0,
						'category_id' 			=> $id,
						'filter_item_id' 		=> $item['filter_item_id']
					));
				}
			}

		}
		else{
			$sql2 = '
			SELECT `filter`.* 
			FROM `filter`, `filter_heading` 
			WHERE `filter`.filter_id = `filter_heading`.filter_id 
			AND `filter_heading`.language_id = '.$language_id.'
			AND `filter`.controller = "'.$controller.'"';
			
			$return = $this->db->query($sql2);
			
			foreach($return as $item){
				if(!in_array($item['filter_id'], $arr_filters)){
					$sql = 'INSERT INTO `filter_item_category`
					(
						`filter_item_category`.saved,
						`filter_item_category`.category_id,
						`filter_item_category`.filter_item_id
					)
					VALUES
					(
						:saved,
						:category_id,
						:filter_item_id
					)
					';
					$this->db->query($sql, array(
						'saved' 				=> 0,
						'category_id' 			=> $id,
						'filter_item_id' 		=> $item['filter_id']
					));
				}
			}
		
		}
		
		if(isset($post['media']) && count($post['media']) > 0)
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
	}
	
	function delete($category_id)
	{
		$sql = 'SELECT * FROM `category` WHERE `category`.category_id = ?';
		$r = $this->db->query($sql, array($category_id));

		$this->db->query('DELETE FROM `category` WHERE `category`.category_id = ?', array($category_id));
		$this->db->query('DELETE FROM `category_content` WHERE `category_content`.category_id = ?', array($category_id));
		$this->db->query('DELETE FROM `filter_item_category` WHERE `filter_item_category`.category_id = ?', array($category_id));
		$this->db->query('DELETE FROM `product_options_item_category` WHERE `product_options_item_category`.category_id = ?', array($category_id));
		$this->db->query('DELETE FROM `category_selected` WHERE `category_selected`.category_id = ?', array($category_id));
		
		$sql = '
		SELECT *
		FROM `media`
		WHERE `media`.table_id = ?
		AND `media`.controller = ?
		';
		
		$media_ar = $this->db->query($sql, array($category_id, CONTROLLER));
		
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
	
	function order($subcontroller, $direction, $current_order, $parent_id, $category_id)
	{
		switch($direction)
		{
			case 'up':
				$from = $this->db->query('SELECT `category`.order, `category`.category_id FROM `category` WHERE `category`.category_id = ?', array($category_id));
				$to = $this->db->query('SELECT `category`.order, `category`.category_id FROM `category` WHERE `category`.order < ? AND `category`.parent_id = ? AND `category`.controller = ? ORDER BY `category`.order DESC', array($current_order, $parent_id, $subcontroller));
				
				if(!empty($to))
				{
					$this->db->query('UPDATE `category` SET `category`.order = ? WHERE `category`.category_id = ?', array($to[0]['order'], $from[0]['category_id']));
					$this->db->query('UPDATE `category` SET `category`.order = ? WHERE `category`.category_id = ?', array($from[0]['order'], $to[0]['category_id']));
				}
			break;
				
			case 'down':
				$from = $this->db->query('SELECT `category`.order, `category`.category_id FROM `category` WHERE `category`.category_id = ?', array($category_id));
				$to = $this->db->query('SELECT `category`.order, `category`.category_id FROM `category` WHERE `category`.order > ? AND `category`.parent_id = ? AND `category`.controller = ? ORDER BY `category`.order ASC', array($current_order, $parent_id, $subcontroller));
				
				if(!empty($to))
				{
					$this->db->query('UPDATE `category` SET `category`.order = ? WHERE `category`.category_id = ?', array($to[0]['order'], $from[0]['category_id']));
					$this->db->query('UPDATE `category` SET `category`.order = ? WHERE `category`.category_id = ?', array($from[0]['order'], $to[0]['category_id']));
				}
			break;
		}
	}
	
	function update_overview($post)
	{
		if(isset($post['active']))
		{
			foreach($post['active'] as $k => $v)
			{
				$this->db->query('UPDATE `category` SET `category`.active = ? WHERE `category`.category_id = ?', array($v, $k));
			}
		}
		if(isset($post['main_menu']))
		{
			foreach($post['main_menu'] as $k => $v)
			{
				$this->db->query('UPDATE `category` SET `category`.main_menu = ? WHERE `category`.category_id = ?', array($v, $k));
			}
		}
		if(isset($post['active_product_options']))
		{
			foreach($post['active_product_options'] as $k => $v)
			{
				$this->db->query('UPDATE `product_options` SET `product_options`.active = ? WHERE `product_options`.product_options_id = ?', array($v, $k));
			}
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
	
	
	function fetch_category_product_options($category_id = 0)
	{
		$return = array();
		$parent_id = 0;
		$sql = '
		SELECT * 
		FROM `product_options`, `product_options_heading` 
		WHERE `product_options`.product_options_id = `product_options_heading`.product_options_id 
		AND `product_options_heading`.language_id = :lang 
		ORDER BY `product_options`.order ASC
		';
		$data = $this->db->query($sql, array('lang' => $this->config->item('default_language')));
		
		$i = 0;
		
		foreach($data as $product_options)
		{
			$return[$i] = $product_options;
		
			$sql = '
				SELECT * 
				FROM `product_options_item_category`
				WHERE `product_options_item_category`.product_options_item_id = :product_options_item_id
				AND `product_options_item_category`.	category_id = :category_id 
				';
			unset($data);
			$data = $this->db->query($sql, array('product_options_item_id' => $product_options['product_options_id'], 'category_id' => $category_id));
			if($data && count($data) == 1) $return[$i]['selected'] = 1;
			else $return[$i]['selected'] = 0;
		
			$i++;
		}
		return $return;
	}
	
	function fetch_category_filters($controller, $language_id, $category_id = 0)
	{
		$return = array();
		$parent_id = 0;
		$sql = '
		SELECT  DISTINCT *, `filter_heading`.title as filer_title
		FROM `filter`, `filter_heading`, `filter_item` 
		WHERE `filter`.filter_id = `filter_heading`.filter_id 
		AND `filter_heading`.language_id = :lang 
		AND `filter_heading`.filter_heading_id = `filter_item`.filter_heading_id 
		AND `filter`.controller = :controller
		ORDER BY `filter`.order ASC, `filter_item`.filter_item_id 
		';
		
		$data = $this->db->query($sql, array('lang' => $language_id, 'controller' => $controller));
		
		$i = 0;
		
		foreach($data as $filter)
		{
			$return[$i] = $filter;
			
			$sql = '
				SELECT * 
				FROM `filter_item_category`
				WHERE `filter_item_category`.filter_item_id = :filter_item_id
				AND `filter_item_category`.	category_id = :category_id 
				';
			unset($data);
			
			if($controller == 'products')
				$data = $this->db->query($sql, array('filter_item_id' => $filter['filter_item_id'], 'category_id' => $category_id));
			else
				$data = $this->db->query($sql, array('filter_item_id' => $filter['filter_id'], 'category_id' => $category_id));
			
			if($data && count($data) == 1) $return[$i]['selected'] = $data[0]['saved'];
			else $return[$i]['selected'] = 0;
		
			$i++;
		}
		return $return;
	}
		
	function fetch_all_product_options($language_id)
	{
		$return = array();
		
		$sql = '
		SELECT * 
		FROM `product_options`, `product_options_heading` 
		WHERE `product_options`.product_options_id = `product_options_heading`.product_options_id 
		AND `product_options_heading`.language_id = :lang 
		ORDER BY `product_options`.order ASC
		';
		$return = $this->db->query($sql, array('lang' => $language_id));
		
		return $return;
	}
	
	function fetch_product_options($product_options_id, $language_id)
	{	
		$sql = '
			SELECT *
			FROM `product_options`
			INNER JOIN `product_options_heading`
			ON `product_options_heading`.product_options_id = `product_options`.product_options_id
			WHERE `product_options`.product_options_id = ?
			AND `product_options_heading`.language_id = ?
			ORDER BY `product_options`.order
		';
		
		$r = $this->db->query($sql, array($product_options_id, $language_id));
		$data['product_options'] = $r[0];
		
		$sql = '
			SELECT *
			FROM `product_options_heading`
			INNER JOIN `product_options_item`
			ON `product_options_item`.product_options_heading_id = `product_options_heading`.product_options_heading_id
			WHERE `product_options_heading`.language_id = ?
			AND `product_options_heading`.product_options_heading_id = ?
			ORDER BY `product_options_item`.product_options_item_id
		';
	
		$data['product_options']['options'] = $this->db->query($sql, array($language_id, $data['product_options']['product_options_heading_id']));
	
		return $data;
	}
	
	function add_product_options($post)
	{
		$sql = '
			SELECT MAX(`product_options`.order) AS `order`
			FROM `product_options`
		';
					
		$order = $this->db->query($sql);
		if(!$order[0]['order']) $order[0]['order'] = 0;
		
		$sql = '
		INSERT INTO `product_options`
		(
			`product_options`.order,
			`product_options`.type,
			`product_options`.superadmin
		)
		VALUES
		(
			:order,
			:type,
			:superadmin
		)
		';
		
		$this->db->query($sql, array(
			'order'			=> $order[0]['order'] + 1,
			'type'			=> $post['product_options']['type'],
			'superadmin'	=> ((isset($post['product_options']['superadmin']))?1:0)
		));
		
		$product_options_id = $this->db->last_insert_id;
		
		$sql = '
			INSERT INTO `product_options_heading`
			(
				`product_options_heading`.sub_active,
				`product_options_heading`.product_options_id,
				`product_options_heading`.language_id,
				`product_options_heading`.title
			)
			VALUES
			(
				:sub_active,
				:product_options_id,
				:language_id,
				:title
			)
		';
		
		$this->db->query($sql, array(
			'sub_active' 			=> $post['product_options']['sub_active'],
			'product_options_id' 	=> $product_options_id,
			'language_id' 			=> $this->config->item('default_language'),
			'title' 				=> ucfirst($post['product_options']['title'])
		));
		$product_options_heading_ids[] = $this->db->last_insert_id;
		
		$languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array($this->config->item('default_language')));
		
		foreach($languages as $language)
		{				
			$sql = '
			INSERT INTO `product_options_heading`
			(
				product_options_id,
				language_id
			)
			VALUES
			(
				:product_options_id,
				:language_id
			)';
			
			$this->db->query($sql, array(
				'product_options_id' 	=> $product_options_id,
				'language_id' 	=> $language['language_id']
			));
			$product_options_heading_ids[] = $this->db->last_insert_id;
		}
		$arr_ids = '';
		foreach($post['product_options']['options'] as $k => $option){
				if($option != ''){
					$arr_ids .= " AND `product_options`.product_options_id<>".$k;

					foreach($product_options_heading_ids as $l => $product_options_heading_id){
						if($l == 0){
							$sql = '
							INSERT INTO `product_options_item`
							(
								`product_options_item`.product_options_heading_id,
								`product_options_item`.title
							)
							VALUES
							(
								:product_options_heading_id,
								:title
							)
							';
							
							$this->db->query($sql, array(
								'product_options_heading_id'		=> $product_options_heading_id,
								'title'					=> ucfirst($option)
							));
							$product_options_item = $this->db->last_insert_id;
							$sql = '
								UPDATE `product_options_item`
								SET
									`product_options_item`.product_options_item_identify 		= :product_options_item_identify
								WHERE `product_options_item`.product_options_item_id 			= :product_options_id
								';
								
								$this->db->query($sql, array(
									'product_options_item_identify' 	=> $product_options_item,
									'product_options_id' 			=> $product_options_item
								));
							
						}else{
							$sql = '
							INSERT INTO `product_options_item`
							(
								`product_options_item`.product_options_heading_id,
								`product_options_item`.product_options_item_identify
							)
							VALUES
							(
								:product_options_heading_id,
								:product_options_item_identify
							)
							';
							
							$this->db->query($sql, array(
								'product_options_heading_id'		=> $product_options_heading_id,
								'product_options_item_identify'	=> $product_options_item
							));
							
						}					
				}
			}
		}
		return $product_options_id;
	}
	
	function edit_product_options($post, $id, $language_id)
	{
		
		$sql = '
		UPDATE `product_options`, `product_options_heading`
		SET
			`product_options`.type					= :type,
			`product_options`.superadmin			= :superadmin,
			`product_options_heading`.sub_active 	= :sub_active,
			`product_options_heading`.title 		= :title
		WHERE `product_options`.product_options_id 	= :product_options_id
		AND `product_options`.product_options_id 	= `product_options_heading`.product_options_id
		AND `product_options_heading`.language_id 	= :language_id
		';
		
		$this->db->query($sql, array(
			'type' 	=> $post['product_options']['type'],
			'superadmin'	=> ((isset($post['product_options']['superadmin']))?1:0),
			'sub_active' 	=> $post['product_options']['sub_active'],
			'title' 		=> ucfirst($post['product_options']['title']),
			'product_options_id' 	=> $id,
			'language_id' 	=> $language_id
		));
		$arr_ids = '';
		
		foreach($post['product_options']['options'] as $k => $option){
			if($option != ''){
				$sql = 'SELECT `product_options_item`.product_options_item_id FROM `product_options`,`product_options_item`, `product_options_heading`
					WHERE `product_options_heading`.product_options_heading_id = `product_options_item`.product_options_heading_id
					AND `product_options_heading`.product_options_id = `product_options`.product_options_id
					AND `product_options_heading`.language_id = ?
					AND `product_options`.product_options_id = ?
					AND `product_options_item`.product_options_item_id = ?';
				
				$current = $this->db->query($sql, array($language_id, $id, $k));
				print_r($current);
				
				if($current && count($current) == 1){
					
					$sql = '
					UPDATE `product_options_heading`, `product_options_item`
					SET
						`product_options_item`.title 					= :title
					WHERE `product_options_heading`.product_options_heading_id 	= `product_options_item`.product_options_heading_id
					AND `product_options_item`.product_options_item_id 			= :product_options_item_id
					AND `product_options_heading`.language_id 			= :language_id
					';
					
					$this->db->query($sql, array(
						'title' 			=> ucfirst($option),
						'product_options_item_id'	=> $k,
						'language_id' 		=> $language_id
					));
					$arr_ids .= " AND `product_options_item`.product_options_item_identify <> ".$k;
				}else{	
					if($this->config->item('default_language') == $language_id){
						$sql = '
							SELECT *
							FROM `product_options_heading` WHERE `product_options_heading`.product_options_id = ?
							ORDER BY `product_options_heading`.language_id
						';			
						$product_options_heading_ids = $this->db->query($sql, array($id));
						
						foreach($product_options_heading_ids as $l => $product_options_heading_id){
							
							if($l == 0){
								$sql = '
								INSERT INTO `product_options_item`
								(
									`product_options_item`.product_options_heading_id,
									`product_options_item`.title
								)
								VALUES
								(
									:product_options_heading_id,
									:title
								)
								';
								
								$this->db->query($sql, array(
									'product_options_heading_id'		=> $product_options_heading_id['product_options_heading_id'],
									'title'					=> ucfirst($option)
								));
								$product_options_item = $this->db->last_insert_id;
								$arr_ids .= " AND `product_options_item`.product_options_item_identify <> ".$product_options_item;
								$sql = '
									UPDATE `product_options_item`
									SET
										`product_options_item`.product_options_item_identify 		= :product_options_item_identify
									WHERE `product_options_item`.product_options_item_id 			= :product_options_id
									';
									
									$this->db->query($sql, array(
										'product_options_item_identify' 	=> $product_options_item,
										'product_options_id' 			=> $product_options_item
									));
								
							}else{
								$sql = '
								INSERT INTO `product_options_item`
								(
									`product_options_item`.product_options_heading_id,
									`product_options_item`.product_options_item_identify
								)
								VALUES
								(
									:product_options_heading_id,
									:product_options_item_identify
								)
								';
								
								$this->db->query($sql, array(
									'product_options_heading_id'		=> $product_options_heading_id['product_options_heading_id'],
									'product_options_item_identify'	=> $product_options_item
								));
								
							}	
						}
					}
				}
			}
		}						
		
		if($this->config->item('default_language') == $language_id){
			$sql = 'SELECT * FROM `product_options_heading`,`product_options` WHERE `product_options_heading`.product_options_id = ? 
					AND `product_options`.product_options_id 		= `product_options_heading`.product_options_id
					';			
			$product_options_heading_ids = $this->db->query($sql, array($id));
			$heading_sql = '';
			foreach($product_options_heading_ids as $l => $product_options_heading_id){
				if($heading_sql != '') $heading_sql .=  " OR ";
				$heading_sql .= "`product_options_heading`.product_options_heading_id = ".$product_options_heading_id['product_options_heading_id'];
			}	
			$heading_sql = "(".$heading_sql.")";
			$sql = 'SELECT `product_options_item`.product_options_item_identify FROM `product_options_item`, `product_options_heading`
						WHERE `product_options_heading`.product_options_heading_id=`product_options_item`.product_options_heading_id AND '.$heading_sql.' '.$arr_ids;
			
			$old_options = $this->db->query($sql);
			if($old_options && count($old_options)> 0){
				foreach($old_options as $option){
					$this->delete_product_suboptions($option['product_options_item_identify']);
				}
			}	
		}
	}
	
	function delete_product_options($product_options_id)
	{
		$this->db->query('DELETE FROM `product_options` WHERE `product_options`.product_options_id = ?', array($product_options_id));
		$this->delete_product_headings($product_options_id);
		$this->db->query('DELETE FROM `product_options_heading` WHERE `product_options_heading`.product_options_id = ?', array($product_options_id));			
		
	}
	function delete_product_suboptions($product_options_item_identify)
	{
		$sql = 'SELECT * FROM `product_options_item` WHERE `product_options_item`.product_options_item_identify = ?';			
		$filter_heading_ids = $this->db->query($sql, array($filter_item_identify));
		
		foreach($filter_heading_ids as $l => $filter_heading_id){
			$this->db->query('DELETE FROM `product_options_item_saved` WHERE `product_options_item_saved`.product_options_item_id 	 = ?', array($id));
		}
		$this->db->query('DELETE FROM `product_options_item` WHERE `product_options_item`.product_options_item_identify = ?', array($product_options_item_identify));	
	}
	function delete_product_headings($product_options_id)
	{
		$sql = 'SELECT * FROM `product_options_heading` WHERE `product_options_heading`.product_options_id = ?';			
		$product_options_heading_ids = $this->db->query($sql, array($product_options_id));
		foreach($product_options_heading_ids as $l => $product_options_heading_id){
			$this->db->query('DELETE FROM `product_options_item` WHERE `product_options_item`.product_options_heading_id = ?', array($product_options_heading_id['product_options_heading_id']));	
		}
	}
	
	function order_product_options($direction, $current_order, $product_options_id)
	{
		switch($direction)
		{
			case 'up':
				$from = $this->db->query('SELECT `product_options`.order, `product_options`.product_options_id FROM `product_options` WHERE `product_options`.product_options_id = ?', array($product_options_id));
				$to = $this->db->query('SELECT `product_options`.order, `product_options`.product_options_id FROM `product_options` WHERE `product_options`.order < ?  ORDER BY `product_options`.order DESC', array($current_order));
				
				if(!empty($to))
				{
					$this->db->query('UPDATE `product_options` SET `product_options`.order = ? WHERE `product_options`.product_options_id = ?', array($to[0]['order'], $from[0]['product_options_id']));
					$this->db->query('UPDATE `product_options` SET `product_options`.order = ? WHERE `product_options`.product_options_id = ?', array($from[0]['order'], $to[0]['product_options_id']));
				}
			break;
				
			case 'down':
				$from = $this->db->query('SELECT `product_options`.order, `product_options`.product_options_id FROM `product_options` WHERE `product_options`.product_options_id = ?', array($product_options_id));
				$to = $this->db->query('SELECT `product_options`.order, `product_options`.product_options_id FROM `product_options` WHERE `product_options`.order > ? ORDER BY `product_options`.order ASC', array($current_order));
				
				if(!empty($to))
				{
					$this->db->query('UPDATE `product_options` SET `product_options`.order = ? WHERE `product_options`.product_options_id = ?', array($to[0]['order'], $from[0]['product_options_id']));
					$this->db->query('UPDATE `product_options` SET `product_options`.order = ? WHERE `product_options`.product_options_id = ?', array($from[0]['order'], $to[0]['product_options_id']));
				}
			break;
		}
	}
	
}
?>