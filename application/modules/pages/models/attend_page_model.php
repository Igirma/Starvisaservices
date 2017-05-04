<?php 
class attend_page_model extends model
{

	function update_attendance($event_id, $member_id, $attend)
	{			
		
		$this->db->query('DELETE FROM `event_members` WHERE `event_members`.event_id = ? AND `event_members`.member_id = ?', array($event_id, $member_id));
		$sql = '
		INSERT INTO `event_members`
		(
			event_id,
			member_id,
			saved
		)
		VALUES
		(
			:event_id,
			:member_id,
			:saved
		)';
		
		$this->db->query($sql, array(
				'event_id' 	=> $event_id,
				'member_id' => $member_id,
				'saved' 	=> $attend
		));
		
		$attend_page = getSlugOnController('attend_page');
			
		$this->url->redirect(BASE_URL.$attend_page.'/'.$event_id);
		 
	}
	
	function get_message($event_id, $language_id)
	{			
		
		$sql = '
		SELECT *
		FROM `event`
		INNER JOIN `event_content`
		ON `event_content`.event_id = `event`.event_id
		WHERE `event`.event_id = ?
		AND `event_content`.language_id = ?
		';
		
		$r = $this->db->query($sql, array($event_id, $language_id));
		if(isset($r) && $r && count($r) > 0){
			$data = $r[0];

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
		
			$data['media'] = $this->db->query($sql, array($event_id, 'events', $language_id));
			
			 return $data;
		}
	}
	
}
?>