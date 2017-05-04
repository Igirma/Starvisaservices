<?php

/**
 * Users class
 *
 * @description   Users system
 */

class users
{
    var $db;
    var $user = false;
    var $ip;
    var $browser;
    var $yesterday;
    
    public function __construct()
    {
        $this->db =& load_class('db', 'core');

        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->browser = $_SERVER['HTTP_USER_AGENT'];
        $this->yesterday = strtotime(date('Y-m-d H:i:s', strtotime('-1 day')));

        $this->clean_temporary_users();
        $this->get_temporary_user();
    }

    function insert_user($arrData)
    {
        $fields['ip'] = $this->ip;
        $fields['browser'] = $this->browser;
        $fields['time'] = time();

        foreach ($arrData as $k => $v) {
            $fields[$k] = $v;
        }

        $keys = array();
        $data = array();
        foreach ($fields as $key => $val) {
            array_push($keys, $key);
            array_push($data, $val);
        }

        $values = str_repeat('?, ', count($fields));
        $values = rtrim($values, ', ');

        return $this->db->query('INSERT INTO `users` (' . implode(', ', $keys) . ') VALUES (' . $values . ')', $data);
    }

    function update_user($data)
    {
        if (!$this->user) {
            return $this->insert_user($data);
        }

        $arrayData = array();
        $arrayFields = array();

        foreach ($data as $key => $val) {
            array_push($arrayData, $val);
            array_push($arrayFields, $key . ' = ?');
        }

        array_push($arrayData, $this->ip, $this->browser);

        return $this->db->query('UPDATE `users` SET ' . implode(', ', $arrayFields) . ' WHERE ip = ? AND browser = ? LIMIT 1', $arrayData);
    }

    function get_temporary_user()
    {
        $data = $this->db->query('SELECT * FROM `users` WHERE ip = ? AND browser = ? LIMIT 1', array($this->ip, $this->browser));
        if (!isset($data[0])) {
            return false;
        }
        $data[0]['users_params'] = get_json($data[0]['users_params']);
        $this->user = $data[0];
        return $this->user;
    }

    function clean_temporary_users()
    {
        return $this->db->query('DELETE FROM `users` WHERE time < ?', array($this->yesterday));
    }

    function saveType($id)
    {
        if ($id == '' || $id == 0) {
            return false;
        }
        if (!$user = $this->get_temporary_user()) {
            return false;
        }
        if (!isset($user['users_params']['selected'])) {
            return false;
        }
        $user['users_params']['selected']['users_type_id'] = $id;
        $this->update_user(array('users_params' => json_encode($user['users_params'])));
        return $this->get_temporary_user();
    }

    function destinations()
    {
        $sql = '
          SELECT 
            `users_countries`.users_country_id AS id, 
            `users_countries`.users_country_name AS name 
          FROM `users_countries` 
          WHERE (`users_countries`.users_country_id IN (
            SELECT `users_destinations_selected`.users_destination_id FROM `users_destinations_selected` 
            WHERE `users_destinations_selected`.users_destination_id = `users_countries`.users_country_id 
            AND `users_destinations_selected`.users_country_group_type = ?
          ) OR `users_countries`.users_country_id IN (
            SELECT `users_countries_groups_selected`.user_country_id 
            FROM `users_countries_groups_selected` 
            WHERE `users_countries_groups_selected`.user_country_id = `users_countries`.users_country_id 
            AND `users_countries_groups_selected`.user_country_group_id IN (
              SELECT `users_destinations_selected`.users_destination_id 
              FROM `users_destinations_selected` 
              INNER JOIN `users_countries_groups` 
                ON `users_countries_groups`.user_country_group_id = `users_destinations_selected`.users_destination_id 
              WHERE `users_destinations_selected`.users_destination_id = `users_countries_groups_selected`.user_country_group_id 
              AND `users_destinations_selected`.users_country_group_type = ? 
              AND `users_countries_groups`.user_country_group_active = ?
            )
          )) AND `users_countries`.users_country_active = ? 
          ORDER BY `users_countries`.users_country_name ASC
        ';
        $data = $this->db->query($sql, array('country', 'group', 1, 1));
        if (!isset($data[0])) {
            return false;
        }
        $rows = array();
        foreach ($data as $row) {
            extract($row);
            $rows[$id] = $name;
        }
        return $rows;
    }

    function nationalities($id, $update = true)
    {
        $sql = '
          SELECT 
            `users_destinations_selected`.users_destinations_selected_id
          FROM `users_destinations_selected` 
          WHERE (
            `users_destinations_selected`.users_country_group_type = ? 
            AND `users_destinations_selected`.users_destination_id = ?
          ) OR (
            `users_destinations_selected`.users_country_group_type = ? 
            AND `users_destinations_selected`.users_destination_id IN (
              SELECT `users_countries_groups_selected`.user_country_group_id 
              FROM `users_countries_groups_selected` 
              WHERE `users_countries_groups_selected`.user_country_id = ?
            )
          )
        ';
        $data = $this->db->query($sql, array('country', $id, 'group', $id));
        if (!isset($data[0])) {
            return false;
        }

        $save = array();
        $save['selected']['users_destination_id'] = $id;

        $dest = array();
        foreach ($data as $d) {
            extract($d);
            array_push($dest, $users_destinations_selected_id);
        }

        $sql = '
          SELECT 
            `users_countries`.users_country_id AS id, 
            `users_countries`.users_country_name AS name 
          FROM `users_countries` 
          WHERE `users_countries`.users_country_id IN (
            SELECT `users_destinations_selected`.users_nationality_id 
            FROM `users_destinations_selected` 
            WHERE `users_destinations_selected`.users_nationality_id = `users_countries`.users_country_id 
            AND `users_destinations_selected`.users_destinations_selected_id = ? 
            AND `users_destinations_selected`.users_nationality_group_type = ? 
            AND `users_destinations_selected`.users_nationality_id != ? 
          ) OR `users_countries`.users_country_id IN (
            SELECT `users_nationality_groups_selected`.user_nationality_id 
            FROM `users_nationality_groups_selected` 
            WHERE `users_nationality_groups_selected`.user_nationality_group_id IN (
              SELECT `users_destinations_selected`.users_nationality_id 
              FROM `users_destinations_selected` 
              WHERE `users_destinations_selected`.users_nationality_id = `users_nationality_groups_selected`.user_nationality_group_id 
              AND `users_destinations_selected`.users_nationality_group_type = ? 
              AND `users_destinations_selected`.users_destinations_selected_id = ?
            )
          ) 
          AND `users_countries`.users_country_active = ?
          AND `users_countries`.users_country_id != ?
          ORDER BY `users_countries`.users_country_name ASC
        ';
        
        $rows = array();
        foreach ($dest as $i) {
            $data = $this->db->query($sql, array($i, 'country', $id, 'group', $i, 1, $id));
            if (isset($data[0])) {
                foreach ($data as $row) {
                    extract($row);
                    $rows[$id] = $name;
                }
            }
        }
        if (!count($rows)) {
            return false;
        }
        if ($update) {
            $this->update_user(array('users_params' => json_encode($save)));
        }
//        asort($rows);
        return $rows;
    }
	
	/**
	function invitations() {
		$invitations = array(
			0 => "Select an Invitation",
			2 => "With Invitation",
			3 => "Without Invitation"
		);
		
		return json_encode($invitations);
	}
	*/

    function get_destination_data($id)
    {
        if (!$user = $this->get_temporary_user()) {
            return false;
        }
        if (!isset($user['users_params']['selected']['users_destination_id'])) {
            return false;
        }
        $did = $user['users_params']['selected']['users_destination_id'];
        $sql = '
          SELECT 
            `users_destinations_selected`.users_nationality_group_type, 
            `users_destinations_selected`.users_country_group_type, 
            `users_destinations_selected`.users_destination_id, 
            `users_destinations_selected`.users_nationality_id, 
            `users_destinations_selected`.users_destinations_selected_id 
          FROM `users_destinations_selected` 
          WHERE ((
            `users_destinations_selected`.users_destination_id IN (
              SELECT `users_countries_groups_selected`.user_country_group_id 
              FROM `users_countries_groups_selected` 
              WHERE `users_countries_groups_selected`.user_country_id = ?
            ) AND `users_destinations_selected`.users_country_group_type = ?
          ) OR (
            `users_destinations_selected`.users_destination_id = ? 
            AND `users_destinations_selected`.users_country_group_type = ?
          )) AND ((
            `users_destinations_selected`.users_nationality_id IN (
              SELECT `users_nationality_groups_selected`.user_nationality_group_id 
              FROM `users_nationality_groups_selected` 
              WHERE `users_nationality_groups_selected`.user_nationality_id = ?
            ) AND `users_destinations_selected`.users_nationality_group_type = ?
          ) OR (
            `users_destinations_selected`.users_nationality_id = ? 
            AND `users_destinations_selected`.users_nationality_group_type = ?
          ))
        ';
        $rows = array();
        $data = $this->db->query($sql, array(
            $did, 'group', $did, 'country', 
            $id, 'group', $id, 'country'
        ));
        if (isset($data[0])) 
        {
            foreach ($data as $row) {
                extract($row);
                $rows[$users_destinations_selected_id] = $row;
            }
        }
        if (!count($rows)) {
            return false;
        }
        $save['params'] = $rows;
        $save['selected']['users_nationality_id'] = $id;
        $save['selected']['users_destination_id'] = $did;
        $this->update_user(array('users_params' => json_encode($save)));
        return $rows;
    }
	
	function get_invitations($user_type, $country_id, $country_type, $nationality_id, $option_id) {
        $sql = '
			SELECT `users_type_selected`.entry_option_id 
			FROM `users_type_selected` 
			WHERE `users_type_selected`.users_country_group_id = ? 
			AND `users_type_selected`.users_country_group_type = ? 
			AND `users_type_selected`.users_nationality_id = ? 
			AND `users_type_selected`.users_type_id = ? 
			AND `users_type_selected`.entry_option_id = ? 
        ';

		$data = $this->db->query($sql, array(
			$country_id,
			$country_type,
			$nationality_id,
			$user_type,
			$option_id
		));
		
		if(count($data) > 0) {
			return true;
		}
		else {
			return false;
		}
	}

    function get_types($id, $opt1 = null, $opt2 = null, $opt3 = null)
    {
        if (!$rows = $this->get_destination_data($id)) {
            return false;
        }
        $types = array();
        $sql = '
          SELECT 
            `users_type`.users_type_id, 
            `users_type`.users_type_name 
          FROM `users_type_countries_selected` 
          INNER JOIN `users_type` 
            ON `users_type`.users_type_id = `users_type_countries_selected`.users_type_id 
          WHERE `users_type_countries_selected`.users_country_group_id = ? 
            AND `users_type_countries_selected`.users_country_group_type = ? 
            AND `users_type_countries_selected`.users_nationality_id = ? 
            AND `users_type_countries_selected`.users_nationality_group_type = ? 
          ORDER BY `users_type`.users_type_order ASC
        ';

        foreach ($rows as $k => $type) 
        {
          $data = $this->db->query($sql, array(
            $type['users_destination_id'], 
            $type['users_country_group_type'], 
            $type['users_nationality_id'], 
            $type['users_nationality_group_type']
          ));
          if (isset($data[0])) {
            foreach ($data as $row) {
              extract($row);
              $types[$users_type_id] = $users_type_name;
            }
          }
        }
		
		if($this->get_invitations($users_type_id, $type['users_destination_id'], $type['users_country_group_type'], $type['users_nationality_id'], 2) && $this->get_invitations($users_type_id, $type['users_destination_id'], $type['users_country_group_type'], $type['users_nationality_id'], 3))
		{
			$types["BOOL_INVITE"] = "TRUE";
		}
		else {
			$types["BOOL_INVITE"] = "FALSE";
		}
		
        if (!count($types)) {
            return false;
        }
        return $types;
    }
	
	function get_glyph_descriptions() {
		$sql = 'SELECT * FROM users_prices WHERE users_price_order = 999 AND users_price_vat = 0';
		$data = $this->db->query($sql);
		
		return $data;
	}

    function format_costs($user, $invitation = 0)
    {
        if (!$data = $this->get_type($user)) {
            return false;
        }
        if (!isset($data['entries'])) {
            return false;
        }
        $html = '<div class="inner list costs">';
        $i = 0;

		$glyphs = $this->get_glyph_descriptions();
		
        foreach ($data['entries'] as $k => $entry) 
        {
			if($entry['id'] == $invitation) {
				$html .= '
				  <div class="row">
				  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				  <header>
				';

				if($entry['id'] > 0) {
					$isContent = strlen($entry['content']) > 0 ? '*' : '';
					$html .= '<h3>Step 1.' . (++$i) . ': Visa Costs / ' . $entry['name'] . $isContent . '</h3>';
					if (strlen($entry['content']) > 0) {
						$html .= '<h5>*' . $entry['content'] . '</h5>';
					}
				} else {
					$html .= '<h3>Step 1: Visa Costs</h3>';
				}

				$html .= '
				  </header>
				  </div>
				  </div>
				';

				$html .= '<table class="table table-striped">';
				$html .= '<thead>';
				$html .= '<tr>';
				$html .= '<th class="text-center">';
				$html .= 'Visa Type ';
				if(strlen($glyphs[0]['users_price_description']) != 0) {
					$html .= '<a href="javascript:;" data-toggle="tooltip" title="' . $glyphs[0]['users_price_description'] . '">';
					$html .= '<i class="glyphicon glyphicon-info-sign"></i></a>';
				}
				$html .= '</th>';
				$html .= '<th class="text-center">';
				$html .= 'Service ';
				if(strlen($glyphs[1]['users_price_description']) != 0) {
					$html .= '<a href="javascript:;" data-toggle="tooltip" title="' . $glyphs[1]['users_price_description'] . '">';
					$html .= '<i class="glyphicon glyphicon-info-sign"></i></a>';
				}
				$html .= '</th>';
				if (count($entry['columns']['header']) > 0) 
				{
					foreach ($entry['columns']['header'] as $column) {
						if (strlen($column['description']) > 0) {
							$html .= '<th class="text-center">' . $column['name'] . ' ';
							$html .= '<a href="javascript:;" data-toggle="tooltip" title="' . $column['description'] . '">';
							$html .= '<i class="glyphicon glyphicon-info-sign"></i></a>';
							$html .= '</th>';
						} else {
							$html .= '<th class="text-center">' . $column['name'] . '</th>';
						}
					}
				}
				$html .= '<th class="text-center">';
				$html .= 'Total';
				if(strlen($glyphs[2]['users_price_description']) != 0) {
					$html .= '<a href="javascript:;" data-toggle="tooltip" title="' . $glyphs[2]['users_price_description'] . '">';
					$html .= '<i class="glyphicon glyphicon-info-sign"></i></a>';
				}
				$html .= '</th>';
				$html .= '</tr>';
				$html .= '</thead>';
				$html .= '<tbody>';

				foreach ($entry['data'] as $service) {
					foreach ($service['entry_services'] as $s) {
						if($s['total'] == 0) {
							$html .= '<tr>';
							$html .= '<td>' . $s['user_entry_name'] . '</td>';
							$html .= '<td>' . $s['users_services_name'] . '</td>';
							foreach ($s['prices'] as $price) {
								$note = $price['note'];
								
								if (!$price['has_vat']) {
									$html .= '<td class="text-center">&pound;' . $price['total'] . '</td>';
								} else {
									if($price['vat'] == 0) {
										$html .= '<td class="text-center">Free</td>';
									}
									else {
										$html .= '<td class="text-center">Free</td>';
									}
								}
							}
							if ($entry['columns']['count'] > count($s['prices'])) 
							{
								$html .= str_repeat('<td class="text-center">Free</td>', ($entry['columns']['count'] - count($s['prices'])));
							}
							if(strlen($note) > 0) {
								$html .= '<td class="text-center">Read More';
								$html .= ' <a href="javascript:;" data-toggle="tooltip" title="' . $note . '">';
								$html .= '<i class="glyphicon glyphicon-info-sign"></i></a>';
								$html .= '</td>';
							}
							else {
								$html .= '<td class="text-center">Free</td>';
							}
							$html .= '</tr>';
						}
						else {
							$html .= '<tr>';
							$html .= '<td>' . $s['user_entry_name'] . '</td>';
							$html .= '<td class="text-center">' . $s['users_services_name'] . '</td>';
							foreach ($s['prices'] as $price) {
								$note = $price['note'];
								
								if (!$price['has_vat']) {
									$html .= '<td class="text-center">&pound;' . $price['total'] . '</td>';
								} else {
									if($price['vat'] == 0) {
										$html .= '<td class="text-center">&pound;' . $price['total'] . '</td>';
									}
									else {
										$html .= '<td class="text-center">&pound;' . $price['subtotal'] . ' (+ &pound;' . $price['vat'] . ' VAT)</td>';
									}
								}
							}
							if ($entry['columns']['count'] > count($s['prices'])) 
							{
								$html .= str_repeat('<td class="text-center">-</td>', ($entry['columns']['count'] - count($s['prices'])));
							}
							$html .= '<td class="text-center">&pound;' . $s['total'];
							if(strlen($note) > 0) {
								$html .= ' <a href="javascript:;" data-toggle="tooltip" title="' . $note . '">';
								$html .= '<i class="glyphicon glyphicon-info-sign"></i></a>';
							}
							$html .= '</td>';
							$html .= '</tr>';
						}
					}
				}

				$html .= '</tbody>';
				$html .= '</table>';

				$html .= '</div>';
				
				$step = 2;
				
				if (($documents = $this->format_documents($user, $step)) !== false) {
					$html .= $documents;
					$step++;
				}
				if (($notes = $this->format_notes($user, $step)) !== false) {
					$html .= $notes;
					$step++;
				}
				if (($article_1 = $this->format_article('payment', $step++)) !== false) {
					$html .= $article_1;
				}
				if (($article_2 = $this->format_article('delivery', $step++)) !== false) {
					$html .= $article_2;
				}
				
				if($step == 6) {
					return strip_spaces($html);
				}
			}
		}
		unset($entry);

		// Retrying and displaying all the data we have as the customer hasn't decided
		// if he has an invitation or not.
		$html = '<div class="inner list costs">';
        $i = 0;

        foreach ($data['entries'] as $k => $entry) 
        {
            $html .= '
              <div class="row">
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <header>
            ';

            if ($entry['id'] > 0) {
                $isContent = strlen($entry['content']) > 0 ? '*' : '';
                $html .= '<h3>Step 1.' . (++$i) . ': Visa Costs / ' . $entry['name'] . $isContent . '</h3>';
                if (strlen($entry['content']) > 0) {
                    $html .= '<h5>*' . $entry['content'] . '</h5>';
                }
            } else {
                $html .= '<h3>Step 1: Visa Costs</h3>';
            }

            $html .= '
              </header>
              </div>
              </div>
            ';

            $html .= '<table class="table table-striped">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th class="text-center">';
			$html .= 'Visa Type ';
			if(strlen($glyphs[0]['users_price_description']) != 0) {
				$html .= '<a href="javascript:;" data-toggle="tooltip" title="' . $glyphs[0]['users_price_description'] . '">';
				$html .= '<i class="glyphicon glyphicon-info-sign"></i></a>';
			}
			$html .= '</th>';
			$html .= '<th class="text-center">';
			$html .= 'Service ';
			if(strlen($glyphs[1]['users_price_description']) != 0) {
				$html .= '<a href="javascript:;" data-toggle="tooltip" title="' . $glyphs[1]['users_price_description'] . '">';
				$html .= '<i class="glyphicon glyphicon-info-sign"></i></a>';
			}
			$html .= '</th>';
            if (count($entry['columns']['header']) > 0) 
            {
                foreach ($entry['columns']['header'] as $column) {
                    if (strlen($column['description']) > 0) {
                        $html .= '<th class="text-center">' . $column['name'] . ' ';
                        $html .= '<a href="javascript:;" data-toggle="tooltip" title="' . $column['description'] . '">';
                        $html .= '<i class="glyphicon glyphicon-info-sign"></i></a>';
                        $html .= '</th>';
                    } else {
                        $html .= '<th class="text-center">' . $column['name'] . '</th>';
                    }
                }
            }
            $html .= '<th class="text-center">';
			$html .= 'Total';
			if(strlen($glyphs[2]['users_price_description']) != 0) {
				$html .= '<a href="javascript:;" data-toggle="tooltip" title="' . $glyphs[2]['users_price_description'] . '">';
				$html .= '<i class="glyphicon glyphicon-info-sign"></i></a>';
			}
			$html .= '</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            foreach ($entry['data'] as $service) {
                foreach ($service['entry_services'] as $s) {
					if($s['total'] == 0) {
						$html .= '<tr>';
						$html .= '<td>' . $s['user_entry_name'] . '</td>';
						$html .= '<td>' . $s['users_services_name'] . '</td>';
						foreach ($s['prices'] as $price) {
							$note = $price['note'];
							
							if (!$price['has_vat']) {
								$html .= '<td class="text-center">Free</td>';
							} else {
								$html .= '<td class="text-center">Free</td>';
							}
						}
						if ($entry['columns']['count'] > count($s['prices'])) 
						{
							$html .= str_repeat('<td class="text-center">Free</td>', ($entry['columns']['count'] - count($s['prices'])));
						}
						if(strlen($note) > 0) {
							$html .= '<td class="text-center">Read More';
							$html .= ' <a href="javascript:;" data-toggle="tooltip" title="' . $note . '">';
							$html .= '<i class="glyphicon glyphicon-info-sign"></i></a>';
							$html .= '</td>';
						}
						else {
							$html .= '<td class="text-center">Free';
							$html .= '</td>';
						}
						$html .= '</tr>';
					}
					else {
						$html .= '<tr>';
						$html .= '<td>' . $s['user_entry_name'] . '</td>';
						$html .= '<td>' . $s['users_services_name'] . '</td>';
						foreach ($s['prices'] as $price) {
							$note = $price['note'];
							
							if (!$price['has_vat']) {
								$html .= '<td class="text-center">&pound;' . $price['total'] . '</td>';
							} else {
								if($price['vat'] > 0) {
									$html .= '<td class="text-center">&pound;' . $price['subtotal'] . ' (+ &pound;' . $price['vat'] . ' VAT)</td>';
								}
								else {
									$html .= '<td class="text-center">&pound;' . $price['subtotal'] . '</td>';
								}
							}
						}
						if ($entry['columns']['count'] > count($s['prices'])) 
						{
							$html .= str_repeat('<td class="text-center">-</td>', ($entry['columns']['count'] - count($s['prices'])));
						}
						$html .= '<td class="text-center">&pound;' . $s['total'];
						if(strlen($note) > 0) {
							$html .= ' <a href="javascript:;" data-toggle="tooltip" title="' . $note . '">';
							$html .= '<i class="glyphicon glyphicon-info-sign"></i></a>';
						}
						$html .= '</td>';
						$html .= '</tr>';
					}
                }
            }

            $html .= '</tbody>';
            $html .= '</table>';
        }
        $html .= '</div>';
        
		$step = 2;
		
        if (($documents = $this->format_documents($user, $step)) !== false) {
            $html .= $documents;
			$step++;
        }
        if (($notes = $this->format_notes($user, $step)) !== false) {
            $html .= $notes;
			$step++;
        }
        if (($article_1 = $this->format_article('payment', $step++)) !== false) {
            $html .= $article_1;
        }
        if (($article_2 = $this->format_article('delivery', $step++)) !== false) {
            $html .= $article_2;
        }
		
		if($step == 6 || isset($_SESSION['preview'])) {
			return strip_spaces($html);
		}
    }

    function get_type($user)
    {
        $sql = '
          SELECT 
            `users_type`.users_type_id, 
            `users_type`.users_type_id AS id, 
            `users_type`.users_type_name AS name, 
            `users_type`.users_type_order, 
            `users_type_countries_selected`.* 
          FROM `users_type_countries_selected` 
          INNER JOIN `users_type` 
            ON `users_type`.users_type_id = `users_type_countries_selected`.users_type_id 
          WHERE `users_type_countries_selected`.users_country_group_id = ? 
            AND `users_type_countries_selected`.users_country_group_type = ? 
            AND `users_type_countries_selected`.users_nationality_id = ? 
            AND `users_type_countries_selected`.users_nationality_group_type = ? 
            AND `users_type_countries_selected`.users_type_id = ? 
          LIMIT 1
        ';
        $data = $this->db->query($sql, array(
            $user['users_country_group_id'], 
            $user['users_country_group_type'], 
            $user['users_nationality_id'], 
            $user['users_nationality_group_type'], 
            $user['users_type_id']
        ));
        if (!isset($data[0])) {
            return false;
        }
        $data[0]['entries'] = $this->fetch_selected_entries($data[0]);
        if (!$data[0]['entries']) {
            return false;
        }
        $data[0]['documents'] = $this->get_documents($data[0]);
        $data[0]['notes'] = $this->get_notes($data[0]);
        return $data[0];
    }

    function format_documents($user, $step = '')
    {
        if (!$data = $this->get_documents($user)) {
            return false;
        }
        $html = '
          <div class="inner list documents">
          <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <header>
            <h3>Step ' . $step . ': Documents required</h3>
          </header>
          </div>
          </div>
          <div class="row">
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 items-list">
        ';

        foreach ($data as $k => $document) {
            $html .= '<div class="item ' . ($k == 0 ? 'active' : '') . '">';
            $html .= '<a href="javascript:;" class="item" data-item-id="' . $document['users_document_id'] . '">';
            $html .= '<span class="title">' . $document['users_document_title'] . '</span>';
            $html .= '<span class="glyphicon glyphicon-forward arrow"></span>';
            $html .= '</a>';
            $html .= '</div>';
        }

        $html .= '
          </div>
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 items-content">
        ';

        foreach ($data as $k => $document) {
            $html .= '<div class="item-content ' . ($k == 0 ? 'active' : 'hidden') . '" id="' . $document['users_document_id'] . '">';
            $html .= $document['users_document_content'];
            $html .= '</div>';
        }

        $html .= '
          </div>
          </div>
          </div>
        ';

        return $html;
    }
    
    function format_notes($user, $step = '')
    {
        if (!$data = $this->get_notes($user)) {
            return false;
        }
        $html = '
          <div class="inner list notes">
          <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <header>
            <h3>Step ' . $step . ': Important notes</h3>
          </header>
          </div>
          </div>
          <div class="row">
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 items-list hidden">
        ';

        foreach ($data as $k => $note) {
            $html .= '<div class="item ' . ($k == 0 ? 'active' : '') . '">';
            $html .= '<a href="javascript:;" class="item" data-item-id="' . $note['users_notes_id'] . '">';
            $html .= '<span class="title">' . $note['users_notes_title'] . '</span>';
            $html .= '<span class="glyphicon glyphicon-forward arrow"></span>';
            $html .= '</a>';
            $html .= '</div>';
        }

        $html .= '
          </div>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 items-content">
        ';

        foreach ($data as $k => $note) {
            $html .= '<div class="item-content ' . ($k == 0 ? 'active' : 'hidden') . '" id="' . $note['users_notes_id'] . '">';
            $html .= '<div class="padding">';
            $html .= $note['users_notes_content'];
            $html .= '</div>';
            $html .= '</div>';
        }

        $html .= '
          </div>
          </div>
          </div>
        ';

        return $html;
    }
    
    function format_article($controller, $step = '')
    {
        if (!$data = $this->get_article($controller)) {
            return false;
        }
        $html = '
          <div class="inner list articles">
          <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <header>
            <h3>Step ' . $step . ': ' . $data['title'] . '</h3>
          </header>
          </div>
          </div>
          <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 items-content">
            <div class="item-content active">' . $data['content'] . '</div>
          </div>
          </div>
          </div>
        ';
        return $html;
    }

    function get_article($controller)
    {
        $sql = '
          SELECT 
            `page_content`.content_title, 
            `page_content`.content_text 
          FROM `page` 
          INNER JOIN `page_content` 
            ON `page`.page_id = `page_content`.page_id 
          WHERE `page_content`.language_id = ? 
            AND `page`.controller = ? 
            AND `page`.active = ? 
            AND `page_content`.sub_active = ? 
          LIMIT 1
        ';
        $data = $this->db->query($sql, array(CUR_LANG, $controller, 1, 1));
        if (!isset($data[0])) {
            return false;
        }
        return array(
            'title' => $data[0]['content_title'],
            'content' => $data[0]['content_text']
        );
    }

    function get_documents($user)
    {
        $sql = '
          SELECT * FROM `users_documents_selected` 
          INNER JOIN `users_documents` 
            ON `users_documents`.users_document_id = `users_documents_selected`.users_documents_id 
          WHERE `users_documents_selected`.users_country_group_id = ? 
            AND `users_documents_selected`.users_country_group_type = ? 
            AND `users_documents_selected`.users_nationality_id = ? 
            AND `users_documents_selected`.users_type_id = ? 
          ORDER BY `users_documents`.users_document_order ASC
        ';
        $data = $this->db->query($sql, array(
            $user['users_country_group_id'], 
            $user['users_country_group_type'], 
            $user['users_nationality_id'], 
            $user['users_type_id']
        ));
        if (!isset($data[0])) {
            return false;
        }
        return $data;
    }

    function get_notes($user)
    {
        $sql = '
          SELECT * FROM `users_notes_selected` 
          INNER JOIN `users_notes` 
            ON `users_notes`.users_notes_id = `users_notes_selected`.users_notes_id 
          WHERE `users_notes_selected`.users_country_group_id = ? 
            AND `users_notes_selected`.users_country_group_type = ? 
            AND `users_notes_selected`.users_nationality_id = ? 
            AND `users_notes_selected`.users_type_id = ? 
          ORDER BY `users_notes`.users_notes_order ASC
        ';
        $data = $this->db->query($sql, array(
            $user['users_country_group_id'], 
            $user['users_country_group_type'], 
            $user['users_nationality_id'], 
            $user['users_type_id']
        ));
        if (!isset($data[0])) {
            return false;
        }
        return $data;
    }

    function fetch_selected_entries($array)
    {
        $sql = '
          SELECT 
            `users_type_entries`.user_entry_order, 
            `users_type_entries`.user_entry_name, 
            `users_type_entries`.user_entry_id, 
            `users_type_selected`.*, 
            `users_type_entries_options`.* 
          FROM `users_type_selected` 
          INNER JOIN `users_type_entries` 
            ON `users_type_entries`.user_entry_id = `users_type_selected`.user_entry_id 
          LEFT JOIN `users_type_entries_options` 
            ON `users_type_entries_options`.entry_option_id = `users_type_selected`.entry_option_id 
          WHERE `users_type_selected`.users_type_id = ? 
            AND `users_type_selected`.users_nationality_id = ? 
            AND `users_type_selected`.users_country_group_type = ? 
            AND `users_type_selected`.users_country_group_id = ? 
            AND `users_type_selected`.users_nationality_group_type = ? 
          ORDER BY `users_type_entries`.user_entry_order ASC
        ';
        $data = $this->db->query($sql, array(
            $array['users_type_id'],
            $array['users_nationality_id'],
            $array['users_country_group_type'],
            $array['users_country_group_id'], 
            $array['users_nationality_group_type']
        ));
        if (!isset($data[0])) {
            return false;
        }
        return $this->filter_entries_options($data);
    }

    private function filter_entries_options($data)
    {
        $options = array();
        foreach ($data as $k => $row) {
            extract($row);
            if ((int) $entry_option_id > 0) {
                $options[$entry_option_id]['id'] = $entry_option_id;
                $options[$entry_option_id]['content'] = $entry_option_content;
                $options[$entry_option_id]['name'] = $entry_option_name;
                $options[$entry_option_id]['data'] = $this->filter_entries_data($data, $entry_option_id);
                $options[$entry_option_id]['columns'] = $this->get_entries_header($options[$entry_option_id]['data']);
            } else {
                $options[0]['id'] = 0;
                $options[0]['content'] = '';
                $options[0]['name'] = 'N/A';
                $options[0]['data'] = $this->filter_entries_data($data, 0);
                $options[0]['columns'] = $this->get_entries_header($options[0]['data']);
            }
        }
        foreach ($options as $k => $option) {
            if (!$option['data']) {
                unset($options[$k]);
            }
        }
        if (count($options) < 1) {
            return false;
        }
        return $options;
    }

    private function get_entries_header($data)
    {
        if (!$data) {
            return false;
        }
        $count = array();
        foreach ($data as $key => $entries) {
            $highest = array();
            foreach ($entries['entry_services'] as $k => $e) {
                $highest[$k] = count($e['header']);
            }
            foreach ($entries['entry_services'] as $k => $entry) {
                if (max($highest) == count($entry['header']))
                {
                  $count['header'] = $entry['header'];
                  $count['count'] = count($entry['header']);
                  break;
                }
            }
        }
        return isset($count) ? $count : false;
    }

    private function filter_entries_data($data, $entry_option_id)
    {
        $entries = array();
        foreach ($data as $k => $entry) {
            if ($entry['entry_option_id'] == $entry_option_id) {
                $entries[$k]['entry_name'] = $entry['user_entry_name'];
                $entries[$k]['entry_services'] = $this->fetch_selected_services($entry);
            }
        }
        if (count($entries) < 1) {
            return false;
        }
        foreach ($entries as $k => $entry) {
            if (!$entry['entry_services']) {
                unset($entries[$k]);
            }
        }
        if (count($entries) < 1) {
            return false;
        }
        return $entries;
    }

    function fetch_selected_services($array)
    {
        $sql = '
          SELECT 
            `users_services`.users_services_order, 
            `users_services`.users_services_name, 
            `users_services`.users_services_id, 
            `users_services_selected`.*, 
            `users_type_entries`.* 
          FROM `users_services_selected` 
          INNER JOIN `users_services` 
            ON `users_services`.users_services_id = `users_services_selected`.users_services_id 
          LEFT JOIN `users_type_entries` 
            ON `users_type_entries`.user_entry_id = `users_services_selected`.user_entry_id 
          WHERE `users_services_selected`.user_entry_id = ? 
            AND `users_services_selected`.users_type_id = ? 
            AND `users_services_selected`.users_country_group_type = ? 
            AND `users_services_selected`.users_country_group_id = ? 
            AND `users_services_selected`.users_nationality_id = ? 
            AND `users_services_selected`.users_nationality_group_type = ? 
          ORDER BY 
            `users_services`.users_services_order ASC,
            `users_type_entries`.user_entry_order ASC
        ';
        $data = $this->db->query($sql, array(
            $array['user_entry_id'], 
            $array['users_type_id'], 
            $array['users_country_group_type'], 
            $array['users_country_group_id'], 
            $array['users_nationality_id'], 
            $array['users_nationality_group_type']
        ));
        if (!isset($data[0])) {
            return false;
        }
        $services = array();
        foreach ($data as $k => $service) {
            $services[$k] = $service;
            $services[$k]['prices'] = $this->fetch_selected_prices($service);
            $services[$k]['total'] = $this->calculate_selected_prices($services[$k]['prices']);
            $services[$k]['header'] = $this->get_prices_headers($services[$k]['prices']);
        }
        $reset = array();
        foreach ($services as $k => $service) {
            if ($service['prices'] !== false) {
                array_push($reset, $service);
            }
        }
        if (count($reset) < 1) {
            return false;
        }
        return $reset;
    }

    function fetch_selected_prices($array)
    {
        $sql = '
          SELECT 
            `users_prices`.users_price_order, 
            `users_prices`.users_price_name, 
            `users_prices`.users_price_description, 
            `users_prices`.users_price_vat, 
            `users_prices`.users_price_id, 
            `users_services_prices`.* 
          FROM `users_services_prices` 
          INNER JOIN `users_prices` 
            ON `users_prices`.users_price_id = `users_services_prices`.users_price_id 
          WHERE `users_services_prices`.users_services_id = ? 
            AND `users_services_prices`.user_entry_id = ? 
            AND `users_services_prices`.users_type_id = ? 
            AND `users_services_prices`.users_country_group_type = ? 
            AND `users_services_prices`.users_country_group_id = ? 
            AND `users_services_prices`.users_nationality_id = ? 
            AND `users_services_prices`.users_nationality_group_type = ? 
          ORDER BY `users_prices`.users_price_order ASC
        ';
        $data = $this->db->query($sql, array(
            $array['users_services_id'], 
            $array['user_entry_id'], 
            $array['users_type_id'], 
            $array['users_country_group_type'], 
            $array['users_country_group_id'], 
            $array['users_nationality_id'], 
            $array['users_nationality_group_type']
        ));
        if (!isset($data[0])) {
            return false;
        }
        $prices = array();
        foreach ($data as $k => $price) {
            $prices[$k]['name'] = $price['users_price_name'];
            $prices[$k]['description'] = $price['users_price_description'];
            $prices[$k]['has_vat'] = $price['users_price_vat'] == 1;
            $prices[$k]['total'] = $price['total'];
			$prices[$k]['note'] = $price['users_free_note'];
            if ($price['users_price_vat'] == 1) {
                $prices[$k]['subtotal'] = $price['subtotal'];
                $prices[$k]['vat'] = $price['vat'];
            }
        }
        return $prices;
    }
    
    function calculate_selected_prices($data)
    {
        $total = 0;
        if (!$data) {
            return formatPriceDecimals($total);
        }
        foreach ($data as $price) {
            $total += $price['total'];
        }
        return formatPriceDecimals($total);
    }
    
    function get_prices_headers($data)
    {
        if (!$data) {
            return false;
        }
        $header = array();
        foreach ($data as $k => $price) {
            $header[$k]['name'] = $price['name'];
            $header[$k]['description'] = $price['description'];
        }
        return $header;
    }
}

?>
