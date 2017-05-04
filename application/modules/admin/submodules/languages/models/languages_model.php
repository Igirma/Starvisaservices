<?php 
class languages_model extends model
{
	function __construct(){}
	
	function fetch_all()
	{
		return $this->db->query('SELECT * FROM `language` ORDER BY `language`.language_id ASC');
	}
	
	function fetch($language)
	{
		$r = $this->db->query('SELECT * FROM `language` WHERE `language`.language_id = ?', array($language));
		return isset($r[0]) ? $r[0] : false;
	}
	
	function add($post)
	{
		$this->db->query('INSERT INTO `language` (name, code) VALUES (:name, :code)', array('name' => ucfirst($post['name']), 'code' => strtolower($post['code'])));

   $language_id = $this->db->last_insert_id;
		# insert new page content rows for the new language
		$r = $this->db->query('SELECT * FROM `page`');
		
		foreach($r as $page)
		{
			$this->db->query('INSERT INTO `mobile_content` (page_id, language_id, sub_active) VALUES(?, ?, 0)', array($page['page_id'], $language_id));
			$this->db->query('INSERT INTO `page_content` (page_id, language_id, sub_active) VALUES(?, ?, 0)', array($page['page_id'], $language_id));
		}
		
		$r = $this->db->query('SELECT * FROM `landingspage`');
		
		foreach($r as $page)
		{
			$this->db->query('INSERT INTO `landingsmobile_content` (landingspage_id, language_id, sub_active) VALUES(?, ?, 0)', array($page['landingspage_id'], $language_id));
			$this->db->query('INSERT INTO `landingspage_content` (landingspage_id, language_id, sub_active) VALUES(?, ?, 0)', array($page['landingspage_id'], $language_id));
		}
		
		// filters update
		$modules = array(
			'filter',
			'product_options'
		);
		
		foreach($modules as $module)
		{
			$r = $this->db->query('SELECT * FROM `' . $module . '`');
			
			foreach($r as $row)
			{
				$this->db->query('INSERT INTO `' . $module . '_heading` (`' . $module . '_heading`.' . $module . '_id, `' . $module . '_heading`.language_id) VALUES(?, ?)', array($row[$module . '_id'], $language_id));
				$item_id = $this->db->last_insert_id;
				$r = $this->db->query('SELECT * FROM `' . $module . '_heading`, `' . $module . '_item` 
					WHERE `' . $module . '_heading`.' . $module . '_heading_id = `' . $module . '_item`.' . $module . '_heading_id
					AND `' . $module . '_heading`.' . $module . '_id = '.$row['' . $module . '_id'].'
					AND `' . $module . '_heading`.language_id = ? ', 
					array($this->config->item('default_language')) );
				
				if(isset($r) && count($r) > 0){
					
					foreach($r as $item){
						$sql = '
						INSERT INTO `' . $module . '_item`
						(
							`' . $module . '_item`.' . $module . '_item_identify,
							`' . $module . '_item`.' . $module . '_heading_id
						)
						VALUES
						(
							:' . $module . '_item_identify,
							:' . $module . '_heading_id
						)
						';
							
						$this->db->query($sql, array(
								'' . $module . '_item_identify'	=> $item['' . $module . '_item_identify'],
								'' . $module . '_heading_id'		=> $item_id
							));
					}
				}
			}
		}
		
		# main table name which have multilingual content
		$modules = array(
			'page',
			'news',
			'project',
			'album',
			'slider',
			'media',
			'category',
			'country',
			'landingspage',
			'order_mails',
			'product',
			'reference',
			'event',
			'newsletter',
			'member',
			'blog',
			'formular',
			'formular_item',
			'formular_subitem',
      'videos',
      'brands',
      'stores'
		);
		
		foreach($modules as $module)
		{
			$r = $this->db->query('SELECT * FROM `' . $module . '`');
			
			foreach($r as $row)
			{
				$old_page_id = $row[$module . '_id'];
				if($module != 'page' && $module != 'landingspage')
					$this->db->query('INSERT INTO `' . $module . '_content` (`' . $module . '_content`.' . $module . '_id, `' . $module . '_content`.language_id) VALUES(?, ?)', array($row[$module . '_id'], $language_id));
				$page_id = $this->db->last_insert_id;
				if($module == 'page') $row['controller']  = 'pages';
				if($module == 'landingspage') $row['controller']  = 'landingspages';
				
			}
		}
	}
	
	function update(array $data)
	{
		$this->db->query('UPDATE `language` SET `language`.code = :code, `language`.name = :name WHERE `language`.language_id = :language_id', array('code' => strtolower($data['code']), 'name' => ucfirst($data['name']), 'language_id' => $data['language_id']));
	}
	
	function delete($language)
	{
		$r = $this->db->query('SELECT * FROM `language` WHERE `language`.language_id = ? AND deletable = 1', array($language));
			
		if(!empty($r))
		{
			$this->db->query('DELETE FROM `language` WHERE `language`.language_id = ?', array($language));
			$this->db->query('DELETE FROM `mobile_content` WHERE `mobile_content`.language_id = ?', array($language));
			$this->db->query('DELETE FROM `page_content` WHERE `page_content`.language_id = ?', array($language));
			
			$modules = array(
				'filter',
				'product_options'
			);
			
			foreach($modules as $module)
			{
				$sql = 'SELECT * FROM `' . $module . '_heading` WHERE `' . $module . '_heading`.language_id = ?';			
				
				$filter_heading_ids = $this->db->query($sql, array($language));
				foreach($filter_heading_ids as $l => $filter_heading_id){
					$filter_ids = $this->db->query('SELECT * FROM `' . $module . '_item` WHERE `' . $module . '_item`.' . $module . '_heading_id = ?', array($filter_heading_id['' . $module . '_heading_id']));	
				
					foreach($filter_ids as $l => $filter_id){
						$this->db->query('DELETE FROM `' . $module . '_item_saved` WHERE `' . $module . '_item_saved`.' . $module . '_item_id = ?', array($filter_id['' . $module . '_item_id']));
					}
					
					$this->db->query('DELETE FROM `' . $module . '_item` WHERE `' . $module . '_item`.' . $module . '_heading_id = ?', array($filter_heading_id['' . $module . '_heading_id']));	
				}
				$this->db->query('DELETE FROM `' . $module . '_heading` WHERE `' . $module . '_heading`.language_id = ?', array($language));	

			}
		
			# main table name which have multilingual content
			$modules = array(
				'news',
				'project',
				'album',
				'slider',
				'media',
				'category',
				'country',
				'landingspage',
				'landingsmobile',
				'order_mails',
				'product',
				'reference',
				'event',
				'newsletter',
				'member',
				'blog',
				'formular',
				'formular_item',
				'formular_subitem',
        'videos',
        'brands',
        'stores'
			);
			
			foreach($modules as $module)
			{
				$this->db->query('DELETE FROM `' . $module . '_content` WHERE `' . $module . '_content`.language_id = ?', array($language));
			}
			$this->db->query('DELETE FROM `user_language` WHERE `user_language`.language_id = ?', array($language));
		}
		
    $this->delete_null();
		//log_msg(__METHOD__ . ': ' . $r[0]['language_id']);
	}
  
	function delete_null($language = 0)
	{
			$this->db->query('DELETE FROM `language` WHERE `language`.language_id = ?', array($language));
			$this->db->query('DELETE FROM `mobile_content` WHERE `mobile_content`.language_id = ?', array($language));
			$this->db->query('DELETE FROM `page_content` WHERE `page_content`.language_id = ?', array($language));
			
			$modules = array(
				'filter',
				'product_options'
			);
			
			foreach($modules as $module)
			{
				$sql = 'SELECT * FROM `' . $module . '_heading` WHERE `' . $module . '_heading`.language_id = ?';
				
				$filter_heading_ids = $this->db->query($sql, array($language));
				foreach($filter_heading_ids as $l => $filter_heading_id){
					$filter_ids = $this->db->query('SELECT * FROM `' . $module . '_item` WHERE `' . $module . '_item`.' . $module . '_heading_id = ?', array($filter_heading_id['' . $module . '_heading_id']));	
				
					foreach($filter_ids as $l => $filter_id){
						$this->db->query('DELETE FROM `' . $module . '_item_saved` WHERE `' . $module . '_item_saved`.' . $module . '_item_id = ?', array($filter_id['' . $module . '_item_id']));
					}
					
					$this->db->query('DELETE FROM `' . $module . '_item` WHERE `' . $module . '_item`.' . $module . '_heading_id = ?', array($filter_heading_id['' . $module . '_heading_id']));	
				}
				$this->db->query('DELETE FROM `' . $module . '_heading` WHERE `' . $module . '_heading`.language_id = ?', array($language));	

			}
		
			# main table name which have multilingual content
			$modules = array(
				'news',
				'project',
				'album',
				'slider',
				'media',
				'category',
				'country',
				'landingspage',
				'landingsmobile',
				'order_mails',
				'product',
				'reference',
				'event',
				'newsletter',
				'member',
				'blog',
				'formular',
				'formular_item',
				'formular_subitem',
        'videos',
        'brands',
        'stores'
			);
			
			foreach($modules as $module)
			{
				$this->db->query('DELETE FROM `' . $module . '_content` WHERE `' . $module . '_content`.language_id = ?', array($language));
			}
			$this->db->query('DELETE FROM `user_language` WHERE `user_language`.language_id = ?', array($language));

	}

}
?>