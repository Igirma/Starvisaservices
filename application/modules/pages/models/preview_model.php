<?php

class preview_model extends model
{
	function fetch($page_id, $language_id)
	{
		$sql = '
		SELECT * FROM `page`
		INNER JOIN `page_content`
		ON `page_content`.page_id = `page`.page_id
		WHERE `page`.page_id = :page_id
		AND `page_content`.language_id = :language_id
		';
		
		$temp = $this->db->query($sql, array(
			'page_id' => $page_id,
			'language_id' => $language_id
		));
		
		$data = $temp[0];
		return $data;
	}
}

?>