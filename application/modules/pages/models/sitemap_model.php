<?php 
class sitemap_model extends model
{
	function __construct()
	{
		
	}
	
	function fetch_sitemap($language)
	{			
		$sql = '
			SELECT * 
			FROM `page`
			LEFT JOIN `page_content`
			ON `page`.page_id = `page_content`.page_id
			WHERE `page_content`.language_id = ?
			AND `page`.active = 1
			AND `page`.	parent_id = 0
			AND `page_content`.sub_active = 1
			AND controller <> "sitemap"
			AND controller <> "cart_page_2"
			
			ORDER BY `page`.order
		';
		$sitemap['pages'] = $this->db->query($sql, array($language));
		foreach($sitemap['pages'] as $k => $menu_id){
				$sql = '
					SELECT * 
					FROM `page`
					LEFT JOIN `page_content`
					ON `page`.page_id = `page_content`.page_id
					WHERE `page_content`.language_id = ?
					AND `page`.active = 1
					AND `page`.	parent_id = ?
					AND `page_content`.sub_active = 1
					AND controller <> "sitemap"
					ORDER BY `page`.order
				';
				$r = $this->db->query($sql, array($language, $menu_id['page_id']));
				if(!empty($r) && !$this->db->error)
				{
					$sitemap['pages'][$k]['subitems'] = $r;
				}
				else 
					$sitemap['pages'][$k]['subitems'] = '';
		}
		$sql = '
			SELECT * 
			FROM `landingspage`
			LEFT JOIN `landingspage_content`
			ON `landingspage`.landingspage_id = `landingspage_content`.landingspage_id
			WHERE `landingspage_content`.language_id = ?
			AND `landingspage`.active = 1
			AND `landingspage_content`.sub_active = 1
			ORDER BY `landingspage`.order
		';
		
		$sitemap['landingspages'] = $this->db->query($sql, array($language));
		
		return $sitemap;
	}
	
	
}
?>