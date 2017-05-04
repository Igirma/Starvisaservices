<?php 
class newsletter_model extends model
{	
	function fetch($newsletter_id, $language_id)
	{	
		$sql = '
		SELECT *
		FROM `newsletter`
		INNER JOIN `newsletter_content`
		ON `newsletter_content`.newsletter_id = `newsletter`.newsletter_id
		WHERE `newsletter`.newsletter_id = ?
		AND `newsletter_content`.language_id = ?
		';
		
		$r = $this->db->query($sql, array($newsletter_id, $language_id));
		$data['newsletter'] = $r[0];

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
		
		$data['docs'] = $this->db->query($sql, array($data['newsletter']['newsletter_id'], CONTROLLER));
	
		return $data;
	}
	
	function fetch_dash()
	{
		$sql = '
			SELECT * 
			FROM `newsletter`, `newsletter_content` 
			WHERE `newsletter`.newsletter_id = `newsletter_content`.newsletter_id 
			AND `newsletter_content`.language_id = :lang 
			ORDER BY `newsletter`.date DESC, `newsletter_content`.last_update DESC
			LIMIT 5
		';
		$data = $this->db->query($sql, array('lang' => $this->config->item('default_language')));
		
		return $data;
	}
	
	function fetch_all($archive)
	{
		$sql = '
		SELECT * FROM `newsletter`
		INNER JOIN `newsletter_content`
			ON `newsletter_content`.newsletter_id = `newsletter`.newsletter_id
		WHERE `newsletter_content`.language_id = ?
		ORDER BY `newsletter_content`.date DESC
		';
		
		return $this->db->query($sql, array($this->config->item('default_language')));
	}
	
	function add($post)
	{	
		$sql = '
		INSERT INTO `newsletter`
		(
			event_id
		)
		VALUES
		(
			:event_id
		)';
			
		
		$this->db->query($sql, array(
				'event_id' 	=> $post['newsletter']['event_id']
		));
		echo $this->db->error;
			
		$id = $this->db->last_insert_id;
		
		$sql = '
		INSERT INTO `newsletter_content`
		(
			newsletter_id,
			language_id,
			date,
			title,
			description,
			content_begin,
			content_end,
			sub_active
		)
		VALUES
		(
			:newsletter_id,
			:language_id,
			:date,
			:title,
			:description,
			:content_begin,
			:content_end,
			:sub_active
		)';
			
		$this->db->query($sql, array(
				'newsletter_id' 	=> $id,
				'language_id' 		=> $this->config->item('default_language'),
				'date' 				=> strtotime($post['newsletter']['date']),
				'title' 			=> $post['newsletter']['title'],
				'description' 		=> $post['newsletter']['description'],
				'content_begin' 	=> $post['newsletter']['content_begin'],
				'content_end' 		=> $post['newsletter']['content_end'],
				'sub_active' 		=> $post['newsletter']['sub_active']
		));
		
		$languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array($this->config->item('default_language')));
		
		foreach($languages as $language)
		{				
			$sql = '
			INSERT INTO `newsletter_content`
			(
				newsletter_id,
				language_id,
				sub_active
			)
			VALUES
			(
				:newsletter_id,
				:language_id,
				:sub_active
			)';
			
			$this->db->query($sql, array(
				'newsletter_id' 		=> $id,
				'language_id' 	=> $language['language_id'],
				'sub_active' 	=> 0
			));
		}
		$this->db->query('DELETE FROM `news_newsletter` WHERE `news_newsletter`.newsletter_id = ?', array($id));
		if(isset($post['newsletter']['news']) && count($post['newsletter']['news']) > 0)
		foreach($post['newsletter']['news'] as $news){
			$sql = '
			INSERT INTO `news_newsletter`
			(
				newsletter_id,
				news_id
			)
			VALUES
			(
				:newsletter_id,
				:news_id
			)';
			
			$this->db->query($sql, array(
				'newsletter_id' => $id,
				'news_id' 		=> $news
			));
		}
		return $id;
	}
	
	function edit($post, $id, $language_id)
	{
		
		$sql = '
		UPDATE `newsletter`, `newsletter_content`
		SET
			`newsletter_content`.date 			= :date,
			`newsletter`.event_id 				= :event_id,
			`newsletter_content`.title 			= :title,
			`newsletter_content`.description 	= :description,
			`newsletter_content`.content_begin 	= :content_begin,
			`newsletter_content`.content_end 	= :content_end,
			`newsletter_content`.sub_active 	= :sub_active
		WHERE `newsletter`.newsletter_id 		= :newsletter_id
		AND `newsletter_content`.newsletter_id 	= :newsletter_id
		AND `newsletter_content`.language_id 	= :language_id
		';
		
		$this->db->query($sql, array(
			'date' 			=> strtotime($post['newsletter']['date']),
			'event_id' 		=> $post['newsletter']['event_id'],
			'title' 		=> ucfirst($post['newsletter']['title']),
			'description' 	=> $post['newsletter']['description'],
			'content_begin' => $post['newsletter']['content_begin'],
			'content_end' 	=> $post['newsletter']['content_end'],
			'sub_active' 	=> $post['newsletter']['sub_active'],
			'newsletter_id' => $id,
			'language_id' 	=> $language_id
		));
		
		$this->db->query('DELETE FROM `news_newsletter` WHERE `news_newsletter`.newsletter_id = ?', array($id));
		if(isset($post['newsletter']['news']) && count($post['newsletter']['news']) > 0)
		foreach($post['newsletter']['news'] as $news){
			$sql = '
			INSERT INTO `news_newsletter`
			(
				newsletter_id,
				news_id
			)
			VALUES
			(
				:newsletter_id,
				:news_id
			)';
			
			$this->db->query($sql, array(
				'newsletter_id' => $id,
				'news_id' 		=> $news
			));
		}
	}
	
	function delete($id)
	{
		$this->db->query('DELETE FROM `newsletter` WHERE `newsletter`.newsletter_id = ?', array($id));
		$this->db->query('DELETE FROM `newsletter_content` WHERE `newsletter_content`.newsletter_id = ?', array($id));
		$this->db->query('DELETE FROM `news_newsletter` WHERE `news_newsletter`.newsletter_id = ?', array($id));
		
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
	
	function update_overview($post, $language_id)
	{
		$members = array();
		foreach($post['active'] as $k => $v)
		{
			$this->db->query('UPDATE `newsletter` SET `newsletter`.active = ? WHERE `newsletter`.newsletter_id = ? LIMIT 1', array($v, $k));
		}
		if(isset($post['newsletter_send']) && $post['newsletter_send'] > 0){
			$newsletter = $this->fetch($post['newsletter_send'], $language_id);
			if($newsletter['newsletter']['active'] == 0 || $newsletter['newsletter']['sub_active'] == 0 ) return;
			// send newsletter mails
			if(isset($post['group'][$post['newsletter_send']]))
				$group = $post['group'][$post['newsletter_send']];
			else return;
			if($group == 0){
				// all members
				$sql = '
				SELECT * FROM `member`
				INNER JOIN `member_content`
				ON `member`.member_id = `member_content`.member_id
				WHERE `member_content`.language_id = ?
				AND `member`.active = 1
				AND `member_content`.sub_active = 1
				ORDER BY `member_content`.company_name ASC
				';
				
				$members = $this->db->query($sql, array($this->config->item('default_language')));
						
			}else{
				// group of members
				$sql = '
				SELECT * FROM `member`
				INNER JOIN `member_content`
				ON `member`.member_id = `member_content`.member_id
				INNER JOIN `category_selected`
				ON `category_selected`.table_id = `member`.member_id
				WHERE `member_content`.language_id = ?
				AND `category_selected`.category_id = ?
				AND `member`.active = 1
				AND `member_content`.sub_active = 1
				ORDER BY `member_content`.company_name ASC
				';
				
				$members = $this->db->query($sql, array($this->config->item('default_language'), $group));
				
			}
			$PHPMAILER =& load_class('PHPMailer', 'core');
					
			$settings = getSettings();		
			$attend_page = getSlugOnController('attend_page');
			$message_start = '';
			$message_end = '';	
			$message_start .= "<table style='color:#484848;font-size: 12px; font-family: Arial; font-weight:normal;' width='100%'>";
			if($newsletter['newsletter']['event_id'] != 0){
				$sql = '
				SELECT *
				FROM `event`
				INNER JOIN `event_content`
				ON `event_content`.event_id = `event`.event_id
				WHERE `event`.event_id = ?
				AND `event`.active = 1
				AND `event_content`.sub_active = 1
				AND `event_content`.language_id = ?
				';
						
				$event_content = $this->db->query($sql, array($newsletter['newsletter']['event_id'], $language_id));
				if(isset($event_content) && count($event_content) > 0){
					$message_start .= "<b>".$event_content[0]['title']."</b><br><br>".$event_content[0]['content'];
					
				}
				else return;
			}else{
				$message_start .= "<tr><td>".$newsletter['newsletter']['content_begin'];
				$news_items = $this->fetch_all_news_selected($newsletter['newsletter']['newsletter_id']);
				if(isset($news_items) && count($news_items) > 0){
					foreach($news_items as $news){
						$sql = ' SELECT * FROM `news`
									INNER JOIN `news_content`
										ON `news_content`.news_id = `news`.news_id
									WHERE `news`.news_id = ' . $news . '
										AND `news_content`.language_id = ?
										AND `news`.active = 1
										AND `news_content`.sub_active = 1
									';
							$news_content = $this->db->query($sql, array($this->config->item('default_language')));
							if(isset($news_content) && count($news_content) > 0){
								$message_start .= "<b>".$news_content[0]['title']."</b><br><br>".$news_content[0]['content']."<br>";
							} 
					}
				}
				$message_start .= $newsletter['newsletter']['content_end']."</td></tr>";
			}
			$message_end .= "</table>";
							
			$message_end .= "<br><table style='color:#484848;font-size: 12px; font-family: Arial; font-weight:normal;' width='100%'>";
			$message_end .= "<tr><td colspan=2><a href='".SITE_URL."'><img src=\"cid:overnight-logo\" border='0'></td></tr>";
			$message_end .= "<td width='80%'>".$settings['street']." ".$settings['housenumber']." <br>".$settings['postal']." ".$settings['city']." ".$settings['country']."";
			if($settings['telephone'] != '') $message_end .= "<br>Tel: ".$settings['telephone']."";
			if($settings['fax'] != '') $message_end .= "<br>Fax: ".$settings['fax']."";
			if($settings['admin_mail'] != '') $message_end .= "<br>E: ".$settings['admin_mail']."";
			$message_end .= "</td></tr>";
			$message_end .= "</table>";
					
			if(isset($members) && count($members) > 0){
				$sql = '
					UPDATE `newsletter`
					SET
						`newsletter`.count_sended 			= :count_sended
					WHERE `newsletter`.newsletter_id 		= :newsletter_id
				';	
				$this->db->query($sql, array(
					'count_sended' 	=> $newsletter['newsletter']['count_sended']+1,
					'newsletter_id' => $newsletter['newsletter']['newsletter_id']
				));
			
				foreach($members as $client){
					$client_message = '';
					if($newsletter['newsletter']['event_id'] != 0)
						$client_message .= "
							<a href='".SITE_URL.$attend_page.'/'.$newsletter['newsletter']['event_id'].'/'.$client['member_id']."/1'>".$this->lang->line('yes')."</a> 
							<a href='".SITE_URL.$attend_page.'/'.$newsletter['newsletter']['event_id'].'/'.$client['member_id']."/0'>".$this->lang->line('no')."</a> 
							";								 
					$message = $message_start.$client_message.$message_end;
					// e-mail addresses
					$mail = $client['email'];
							
					// subjects
					$subject = $newsletter['newsletter']['title'];			
					
					// e-mail for admin
					$email = new PHPMailer();
					$email->AddAddress($mail);
					$email->IsHTML(true);
					$email->From = $settings['admin_mail'];
					$email->FromName = $settings['admin_mail'];
					$email->AddEmbeddedImage(BASE_PATH.IMG_DIR."logo.png","overnight-logo",BASE_PATH.IMG_DIR."logo.png","base64","image/png");
					if(isset($newsletter['docs']) && count($newsletter['docs']) > 0)
						$email->AddAttachment(BASE_PATH.MEDIA_DIR."newsletter/docs/".$newsletter['docs'][0]['filename']);
					$email->Subject = $subject;
					$email->Body = $message;
										
					if ($email->Send()) {
						$msg =  "Bedankt, uw bericht is naar ons verzonden. Wij nemen z.s.m. contact met u op.";
					}			
					else 
						$msg =  "E-mail niet verzonden neem contact met ons op";	
				}
			
		}
		
			
			
		}
		return ((count($members) != '')?count($members):'0');
	}
	
	function fetch_all_events()
	{
		$sql = '
		SELECT * FROM `event`
		INNER JOIN `event_content`
		ON `event`.event_id = `event_content`.event_id
		WHERE `event_content`.language_id = ?
		ORDER BY `event_content`.title ASC
		';
		
		$data = $this->db->query($sql, array($this->config->item('default_language')));
		return $data;
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
		
		$this->db->query('DELETE FROM `media` WHERE `media`.media_id = ? LIMIT 1', array($media_id));
		$this->db->query('DELETE FROM `media_content` WHERE `media_content`.media_id = ? LIMIT 1', array($media_id));
	}
	
	function fetch_all_news($newsletter_id = 0, $archive = 0)
	{
		$sql = '
		SELECT
			`news`.news_id,
			`news`.date_created,
			`news`.start_date,
			`news`.end_date,
			`news`.archive,
			`news`.active,
			`news_content`.language_id,
			`news_content`.title
		FROM `news`
		INNER JOIN `news_content`
			ON `news_content`.news_id = `news`.news_id
		WHERE `news`.archive = ' . $archive . '
			AND `news_content`.language_id = ?
		ORDER BY `news`.date_created DESC
		';
		
		return $this->db->query($sql, array($this->config->item('default_language')));
	}
	
	function fetch_all_news_selected($newsletter_id)
	{	
		$r = $this->db->query('SELECT news_id FROM `news_newsletter` WHERE `news_newsletter`.newsletter_id = ?', array($newsletter_id));
		$news_items = array();
		if(isset($r) && count($r) > 0){
			foreach($r as $item){
				$news_items[] = $item['news_id'];
			}
		}
		return $news_items;
	}
	
	function fetch_all_categories()
	{	
		
		$sql = '
		SELECT *
		FROM `category`
		LEFT JOIN `category_content` ON `category`.category_id = `category_content`.category_id
		WHERE `category`.controller = :controller
		AND `category_content`.language_id = :lang
		ORDER BY `category`.order ASC
		';
		
		return $this->db->query($sql, array('controller' => 'members', 'lang' => $this->config->item('default_language')));

	}
	
}
?>