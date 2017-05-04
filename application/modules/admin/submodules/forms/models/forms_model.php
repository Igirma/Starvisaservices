<?php

class forms_model extends model
{
	function fetch_all($filter = false)
	{	
		$sql = '
			SELECT *
			FROM `form_content`
			WHERE `form_content`.archive = 0
			AND `form_content`.language_id = :language_id
			' . ($filter != false ? 'AND `form_content`.type = "' . $filter . '"' : '') . '
			ORDER BY `form_content`.date_added DESC
		';
		
		$data = $this->db->query($sql, array('language_id' => $this->config->item('default_language')));
		
		return $data;
	}
	
	function fetch($form_id)
	{
		$sql = '
			SELECT *
			FROM `form_content`
			WHERE `form_content`.form_id = :form_id
		';
		
		$data = $this->db->query($sql, array('form_id' => $form_id));
		
		return $data[0];
	}
	
	function fetch_archive($filter = false)
	{
		$sql = '
			SELECT *
			FROM `form_content`
			WHERE `form_content`.archive = 1
			AND `form_content`.language_id = :language_id
			' . ($filter != false ? 'AND `form_content`.type = "' . $filter . '"' : '') . '
			ORDER BY `form_content`.date_added DESC
		';
		
		$data = $this->db->query($sql, array('language_id' => $this->config->item('default_language')));
		
		return $data;
	}
	
	function edit($post, $form_id)
	{
		$sql = '
			UPDATE `form_content`
			SET `form_content`.archive = :archive
			WHERE `form_content`.form_id = :form_id
		';
		
		$this->db->query($sql, array(
			'archive' => $post['archive'],
			'form_id' => $form_id
		));
	}
	
	function delete($form_id)
	{
		$sql = '
		DELETE FROM `form_content`
		WHERE `form_content`.form_id = :form_id
		LIMIT 1
		';
		
		$this->db->query($sql, array('form_id' => $form_id));
	}
}

?>