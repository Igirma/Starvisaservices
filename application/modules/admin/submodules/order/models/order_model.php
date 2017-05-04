<?php

class order_model extends model
{
	function fetch_all()
	{
		if(!isset($_SESSION['order_type'])) $_SESSION['order_type'] = 'date_created';	
		
		$sql = '
		SELECT * FROM `order`
		INNER JOIN `order_content`
		ON `order`.order_id = `order_content`.order_id
		INNER JOIN `order_client`
		ON `order_content`.order_client_id = `order_client`.order_client_id
		'.((isset($_SESSION['order_search']) && $_SESSION['order_search'] != '')?' 
			WHERE (`order_content`.order_number LIKE "%'.$_SESSION['order_search'].'%"
				OR `order_client`.firstname LIKE "%'.$_SESSION['order_search'].'%"
				OR `order_client`.lastname LIKE "%'.$_SESSION['order_search'].'%"
				OR `order_client`.company LIKE "%'.$_SESSION['order_search'].'%"
			)
			':"").'
		';
		
		if(isset($_SESSION['order_year']) && $_SESSION['order_year'] > 0) $year = $_SESSION['order_year'];
		else $year = date("Y");
		
		
		if(isset($_SESSION['order_quarter']) && $_SESSION['order_quarter'] > 0){
			if($_SESSION['order_quarter'] == 1) 
				$sql .= ' AND `order`.date_created >= '.strtotime('1-1-'.$year).
						' AND `order`.date_created <= '.strtotime('31-3-'.$year);
			if($_SESSION['order_quarter'] == 2) 
				$sql .= ' AND `order`.date_created >= '.strtotime('1-4-'.$year).
						' AND `order`.date_created <= '.strtotime('30-6-'.$year);
			if($_SESSION['order_quarter'] == 3) 
				$sql .= ' AND `order`.date_created >= '.strtotime('1-7-'.$year).
						' AND `order`.date_created <= '.strtotime('30-9-'.$year);
			if($_SESSION['order_quarter'] == 4) 
				$sql .= ' AND `order`.date_created >= '.strtotime('1-10-'.$year).
						' AND `order`.date_created <= '.strtotime('31-12-'.$year);	
		}
		
		if($_SESSION['order_type'] == 'order_number') $sql .= " ORDER BY `order_content`.order_number DESC";
		else if($_SESSION['order_type'] == 'total_price') $sql .= " ORDER BY `order_content`.total_price DESC";
		else if($_SESSION['order_type'] == 'order_status_id') $sql .= " ORDER BY `order_content`.order_status_id ASC";
		else $sql .= " ORDER BY `order`.date_created DESC";
		
		if(isset($_SESSION['order_nr']) && $_SESSION['order_nr'] > 0){
			$sql .= ' LIMIT 0,'.$_SESSION['order_nr'];
		}
		
		$data['order'] = $this->db->query($sql);
		echo $this->db->error;
		if(isset($data['order']) && $data['order'] && count($data['order']) > 0){
			foreach($data['order'] as $k => $item){
				$sql = '
					SELECT *
					FROM `order_client`
					WHERE `order_client`.order_content_id = ?
				';
				$data['order'][$k]['order_client'] = $this->db->query($sql, array($item['order_content_id']));
			}
		}

		$sql = '
			SELECT *
			FROM `order_status`
			WHERE `order_status`.admin_status = 1
		';
		$order_status = $this->db->query($sql);
		if(isset($order_status) && count($order_status) > 0){
			foreach($order_status as $status){
				$data['order_status'][$status['order_status_id']] = $status['name'];
			}
		}
		
		return $data;
	}
	
	function fetch($order_id)
	{	
		$sql = '
		SELECT *
		FROM `order`
		INNER JOIN `order_content`
		ON `order_content`.order_id = `order`.order_id
		WHERE `order`.order_id = ?
		';
		
		$r = $this->db->query($sql, array($order_id));
		$data['order'] = $r[0];

		$sql = '
			SELECT *
			FROM `order_client`
			WHERE `order_client`.order_content_id = ?
		';
	
		$data['order_client'] = $this->db->query($sql, array($data['order']['order_content_id']));
		
		$sql = '
			SELECT *
			FROM `order_products`
			WHERE `order_products`.order_content_id = ?
		';

		$data['order_products'] = $this->db->query($sql, array($data['order']['order_content_id']));
		
		$sql = '
			SELECT *
			FROM `order_status`
			WHERE `order_status`.admin_status = 1
		';
		$data['order_status'] = $this->db->query($sql);
		
		$sql = '
			SELECT *
			FROM `payment_type`
		';
		$payments = $this->db->query($sql);
		if(isset($payments) && count($payments) > 0){
			foreach($payments as $payment){
				$data['payment_type'][$payment['payment_type_id']] = $payment['name'];
			}
		}
		
		return $data;
	}

	function getFirstMonth()
	{	

		$sql = 'SELECT `order`.date_created FROM `order`
				ORDER BY `order`.date_created ASC
				';
		$data = $this->db->query($sql);
		
		if(isset($data) && count($data) > 0) return $data[0]['date_created'];
		return false;
	}

	function add($post)
	{
		$sql = '
		INSERT INTO `order`
		(
			date_created
		)
		VALUES
		(
			:date_created
		)';
			
		
		$this->db->query($sql, array(
				'date_created' 	=> strtotime($post['order']['date_created'])
		));
		
		$order_id = $this->db->last_insert_id;

		$sql = 'SELECT * FROM `client` WHERE `client`.client_id = ?';
		$data = $this->db->query($sql, array($post['order']['client']));

		if(isset($data) && count($data) > 0) $client_info = $data[0];
		else $client_info = array();
		
		$sql = '
			INSERT INTO `order_client`
			(
				`order_client`.username,
				`order_client`.password,
				`order_client`.firstname,
				`order_client`.lastname,
				`order_client`.company,
				`order_client`.email,
				`order_client`.phone,
				`order_client`.street,
				`order_client`.postal,
				`order_client`.housenumber,
				`order_client`.city,
				`order_client`.country,
				`order_client`.delivery_address,
				`order_client`.delivery_street,
				`order_client`.delivery_postal,
				`order_client`.delivery_housenumber,
				`order_client`.delivery_city,
				`order_client`.delivery_country,
				`order_client`.ip	
			)
			VALUES
			(
				:username,
				:password,
				:firstname,
				:lastname,
				:company,
				:email,
				:phone,
				:street,
				:postal,
				:housenumber,
				:city,
				:country,
				:delivery_address,
				:delivery_street,
				:delivery_postal,
				:delivery_housenumber,
				:delivery_city,
				:delivery_country,
				:ip
			)
		';
	
		$this->db->query($sql, array(
			'username' 			=> $client_info['clientname'],
			'password' 			=> $client_info['password'],
			'firstname' 		=> $client_info['firstname'],
			'lastname' 			=> $client_info['lastname'],
			'company' 			=> $client_info['company'],
			'email' 			=> $client_info['email'],
			'phone' 			=> $client_info['phone'],
			'street' 			=> $client_info['street'],
			'postal'			=> $client_info['postal'],
			'housenumber'		=> $client_info['housenumber'],
			'city'				=> $client_info['city'],
			'country'			=> $client_info['country'],
			'delivery_address'	=> $client_info['delivery_address'],
			'delivery_street' 		=> $client_info['delivery_street'],
			'delivery_postal'		=> $client_info['delivery_postal'],
			'delivery_housenumber'	=> $client_info['delivery_housenumber'],
			'delivery_city'			=> $client_info['delivery_city'],
			'delivery_country'		=> $client_info['delivery_country'],
			'ip'				=> $client_info['ip']
		));
		
		$order_client_id = $this->db->last_insert_id;
		
		$sql = '
		INSERT INTO `order_content`
		(
			order_id,
			language_id,
			order_number,
			order_client_id,
			order_status_id,
			vat_costs,
			transport,
			delivery,
			total_price,
			discount_percent,
			discount_price,
			comments,
			payment_type
		)
		VALUES
		(
			:order_id,
			:language_id,
			:order_number,
			:order_client_id,
			:order_status_id,
			:vat_costs,
			:transport,
			:delivery,
			:total_price,
			:discount_percent,
			:discount_price,
			:comments,
			:payment_type
		)';
		
		$this->db->query($sql, array(
				'order_id' 	=> $order_id,
				'language_id' 		=> $client_info['language_id'],
				'order_number' 		=> $post['order']['order_number'],
				'order_client_id' 		=> $order_client_id,
				'order_status_id' 	=> $post['order']['order_status_id'],
				'vat_costs' 		=> $post['order']['vat_costs'],
				'transport' 	=> (($post['order']['delivery'] == 1) ? $post['order']['transport'] : 0),
				'delivery' 		=> $post['order']['delivery'],
				'total_price' 	=> $post['order']['total_price'],
				'discount_percent' 	=> $post['order']['discount_percent'],
				'discount_price' 	=> $post['order']['discount_price'],
				'comments' 	=> $post['order']['comments'],
				'payment_type' 	=> $post['order']['payment_type']
				
		));
		
		$order_content_id = $this->db->last_insert_id;
		
		$sql = '
			UPDATE `order_client`
			SET `order_client`.order_content_id = :order_content_id
			WHERE `order_client`.order_client_id = :order_client_id
		';
	
		$this->db->query($sql, array(
			'order_content_id' 	=> $order_content_id,
			'order_client_id' 	=> $order_client_id
		));
		
		$language_id = $client_info['language_id'];
		$total = 0;
		
		if(isset($post['order']['products']) && count($post['order']['products']) > 0){
			foreach($post['order']['products'] as $k => $product_id){
				$product = $this->getProduct($product_id, $client_info['language_id']);
				
				if(isset($product) && $product && count($product) > 0){
					
					$sql = '
						INSERT INTO `order_products`
						(
							`order_products`.order_content_id,
							`order_products`.language_id,
							`order_products`.articlenumber,
							`order_products`.EAN,
							`order_products`.title,
							`order_products`.description,
							`order_products`.content,
							`order_products`.tags,
							`order_products`.shipping,
							`order_products`.price,
							`order_products`.offer_price,
							`order_products`.has_vat,
							`order_products`.quantity,
							`order_products`.discount_percent,
							`order_products`.discount_price,
							`order_products`.product_options,
							`order_products`.filters
						)
						VALUES
						(
							:order_content_id,
							:language_id,
							:articlenumber,
							:EAN,
							:title,
							:description,
							:content,
							:tags,
							:shipping,
							:price,
							:offer_price,
							:has_vat,
							:quantity,
							:discount_percent,
							:discount_price,
							:product_options,
							:filters				
						)
					';
					
					$product_options = $this->fetch_product_options($language_id, $product['category_id'], $product['product_id']);
					$filters = $this->fetch_filter($language_id, $product['category_id'], $product['product_id']);
					
					$this->db->query($sql, array(
						'order_content_id' 		=> $order_content_id,
						'language_id' 			=> $language_id,
						'articlenumber' 		=> ((isset($product['articlenumber']))?$product['articlenumber']:""),
						'EAN' 					=> ((isset($product['EAN']))?$product['EAN']:""),
						'title' 				=> ((isset($product['title']))?$product['title']:""),
						'description'			=> ((isset($product['description']))?$product['description']:""),
						'content'				=> ((isset($product['content']))?$product['content']:""),
						'tags'					=> ((isset($product['tags']))?$product['tags']:""),
						'shipping'				=> ((isset($product['shipping']))?$product['shipping']:""),
						'price'					=> ((isset($product['price']))?$product['price']:""),
						'offer_price'			=> ((isset($product['offer_price']))?$product['offer_price']:""),
						'has_vat'				=> ((isset($product['has_vat']))?$product['has_vat']:""),
						'quantity'				=> ((isset($post['order']['aantal'][$k]))?$post['order']['aantal'][$k]:"1"),
						'discount_percent'		=> ((isset($product['discount_percent']))?$product['discount_percent']:""),
						'discount_price'		=> ((isset($product['discount_price']))?$product['discount_price']:""),
						'product_options'		=> $product_options,
						'filters'				=> $filters
					));
					
					$order_products_id = $this->db->last_insert_id;
					$price = 0;
					if($product['offer_price'] != 0)
						$price = $product['offer_price'];
					else{
						$price = $product['price'];
						if($product['discount_percent'] > 0) $price = $price - ($price * $product['discount_percent'] / 100);
							else if($product['discount_price'] > 0) $price = $price - $product['discount_price'];
					}
					if($product['has_vat'] == 1) $price = $price + ($price * $post['order']['vat_costs'] / 100);
										
					$total += formatPriceDecimals($price * ((isset($post['order']['aantal'][$k]))?$post['order']['aantal'][$k]:"1"));
					
				}
			}
		}
		$total = formatPriceDecimals($total);		
		if($post['order']['discount_percent'] > 0)
			$total = $total - ($total * $post['order']['discount_percent'] / 100);
		else
			if($post['order']['discount_price'] > 0)
			$total = $total - ($post['order']['discount_price']);
		$total = formatPriceDecimals($total);	
		//if($post['order']['vat_costs'] != 0) $total = $total + formatPriceDecimals($total * $post['order']['vat_costs'] / 100);
		if($post['order']['delivery'] == 1) 
			$total += formatPriceDecimals($post['order']['transport']);	
		$total = formatPriceDecimals($total);		
		
		$sql = '
			UPDATE `order_content`
			SET `order_content`.total_price = :total_price
			WHERE `order_content`.order_content_id = :order_content_id
		';
	
		$this->db->query($sql, array(
			'total_price' 	=> $total,
			'order_content_id' 	=> $order_content_id
		));
		
		return $order_id;
	}
	
	function edit($post, $id, $status = 0)
	{
		if($status != 0) $post['order']['order_status_id'] = $status;
		
		$sql = '
		SELECT *
		FROM `order`
		INNER JOIN `order_content`
		ON `order_content`.order_id = `order`.order_id
		WHERE `order`.order_id = ?
		';
		
		$r = $this->db->query($sql, array($id));
		$data['order'] = $r[0];
		
		$sql = '
			SELECT *
			FROM `order_client`
			WHERE `order_client`.order_content_id = ?
		';
			
		$data['order_client'] = $this->db->query($sql, array($data['order']['order_content_id']));
		
		$sql = '
			SELECT *
			FROM `order_products`
			WHERE `order_products`.order_content_id = ?
		';

		$data['order_products'] = $this->db->query($sql, array($data['order']['order_content_id']));
				
		$sql = '
			SELECT *
			FROM `order_status`
			WHERE `order_status`.admin_status = 1
		';
		$data['order_status'] = $this->db->query($sql);
				
		$sql = '
			SELECT *
			FROM `payment_type`
		';
		
		$payments = $this->db->query($sql);
		
		if(isset($payments) && count($payments) > 0)
		{
			foreach($payments as $payment)
			{
				$data['payment_type'][$payment['payment_type_id']] = $payment['name'];
			}
		}
						
		if($post['order']['order_status_id'] != $data['order']['order_status_id'])
		{
			// send emails
			
			$sql = '
			SELECT *
			FROM `order_mails`
			INNER JOIN `order_mails_content`
			ON `order_mails_content`.order_mails_id = `order_mails`.order_mails_id
			WHERE `order_mails`.order_status_id = ?
			AND `order_mails_content`.language_id = ?
			';
			
			$r = $this->db->query($sql, array($post['order']['order_status_id'], $data['order']['language_id']));
			
			if(isset($r) && count($r) > 0)
			{
				$order_mails = $r[0];
				
				$PHPMAILER =& load_class('PHPMailer', 'core');

				$products = "<table style='color:#484848;font-size: 12px; font-family: Arial; font-weight:normal;'>";
				
				$total = 0;
				
				if(isset($data['order_products']) && count($data['order_products']) > 0)
				{
					$products .= 
					"<tr>
						<td width=30%><b>".$this->lang->line('mail_prodname')."</b></td>	
						<td width=10%><b>".$this->lang->line('mail_code')."</b></td>	
						<td width=20% align=right><b>".$this->lang->line('mail_price')."</b></td>
						<td width=20% align=center><b>".$this->lang->line('mail_quantity')."</b></td>
						<td width=20% align=right><b>".$this->lang->line('mail_pricesubtotal')."</b></td>
					</tr>";	
					
					foreach($data['order_products'] as $k => $product)
					{					
						$price = 0;
						
						$price = $product['price'];

						if($product['offer_price'] != 0)
						{
							$price = $product['offer_price'];
						}
						else{
							if($product['discount_percent'] > 0)
							{
								$price = $price - ($price * $product['discount_percent'] / 100);
							}
							else if($product['discount_price'] > 0)
							{
								$price = $price - $product['discount_price'];
							}
						}
						if($product['has_vat'] == 1) $price = $price + ($price * $data['order']['vat_costs'] / 100);
						
						$products .= 
						"<tr>
							<td>".$product['title'] ."</td>	
							<td>".$product['articlenumber']."</td>	
							<td align=right>&euro; ".formatPrice($price)."</td>
							<td align=center>".$product['quantity']."</td>
							<td align=right>&euro; ".formatPrice($price * $product['quantity'])."</td>
						</tr>";	
						
						$total += $price * $product['quantity'];
					}
					
					$products .= "<tr>
								<td colspan=4 align=right style='padding-right:10px;'><b>".$this->lang->line('products_subtotal')."</b></td>
								<td align=right>&euro; ".formatPrice($total)."</td>
						</tr>";		
					
					if($data['order']['discount_percent'] > 0)
					{
						$products .= "<tr>
										<td colspan=4 align=right style='padding-right:10px;'><b>".$this->lang->line('discount_percent')."</b></td>	
										<td align=right>".$data['order']['discount_percent']."% ".$this->lang->line('discount')."</td>
								</tr>";	
						
						$total = $total - ($total * $data['order']['discount_percent'] / 100);
						
						$products .= "<tr>
									<td colspan=4 align=right style='padding-right:10px;'><b>".$this->lang->line('products_subtotal')."</b></td>
									<td align=right>&euro; ".formatPrice($total)."</td>
							</tr>";
					}
					else if($data['order']['discount_price'] > 0)
					{
						$products .= "<tr>
									<td colspan=4 align=right style='padding-right:10px;'><b>".$this->lang->line('discount_price')."</b></td>	
									<td align=right>&euro; ".formatPrice($data['order']['discount_price'])." ".$this->lang->line('discount')."</td>
							</tr>";
						$total = $total - $data['order']['discount_price'];
						
						$products .= "<tr>
									<td colspan=4 align=right style='padding-right:10px;'><b>".$this->lang->line('products_subtotal')."</b></td>
									<td align=right>&euro; ".formatPrice($total)."</td>
							</tr>";	
					}
						
					//$products .= "<tr>
					//			<td colspan=4 align=right style='padding-right:10px;'><b>".$this->lang->line('vat_costs')."</b></td>
					//			<td align=right>&euro; ".formatPrice($total + ($total * $data['order']['vat_costs'] / 100))."</td>
					//	</tr>";
					
					//$total = $total + ($total * $data['order']['vat_costs'] / 100);
					
					if($data['order']['delivery'] == 1)
						$products .= "<tr>
								<td colspan=4 align=right style='padding-right:10px;'><b>".$this->lang->line('transport')."</b></td>
								<td align=right>&euro; ".formatPrice($data['order']['transport'])."</td>
						</tr>";		
					
					$total = $total + $data['order']['transport'];
					$total = formatPriceDecimals($total);		
		
					$products .= "<tr>
								<td colspan=4 align=right style='padding-right:10px;'><b>".$this->lang->line('products_total')."</b></td>
								<td align=right>&euro; ".formatPrice($total)."</td>
						</tr>";	
				}
						
				$products .= "</table>";
				
				$client = '';
				
				if(isset($data['order_client']) && count($data['order_client']) > 0)
				{
					$client_info = $data['order_client'][0];
					$client .= "<table style='color:#484848;font-size: 12px; font-family: Arial; font-weight:normal;'>";
					$client .= "<tr><td style='color:#484848;font-weight:bold;width:200px;' colspan=2><b style='font-size:13px;'>".$this->lang->line('order_info')."</b></td></tr>";
					$client .= "<tr><td style='color:#484848;font-weight:bold;width:200px;'>".$this->lang->line('company')."</td><td>".$client_info['company']."<br></td></tr>";
					$client .= "<tr><td style='color:#484848;font-weight:bold;width:200px;'>".$this->lang->line('firstname')."</td><td>".$client_info['firstname'].' '.$client_info['lastname']."<br></td></tr>";
					$client .= "<tr><td style='color:#484848;font-weight:bold;width:200px;'>".$this->lang->line('email')."</td><td>".$client_info['email']."<br></td></tr>";
					$client .= "<tr><td style='color:#484848;font-weight:bold;width:200px;'>".$this->lang->line('phone')."</td><td>".$client_info['phone']."<br></td></tr>";
					$client .= "<tr><td style='color:#484848;font-weight:bold;width:200px;' colspan=2><br><b style='font-size:13px;'>".$this->lang->line('address')."</b></td></tr>";
					$client .= "<tr><td style='color:#484848;font-weight:bold;width:200px;'>".$this->lang->line('street')."</td><td>".$client_info['street']."<br></td></tr>";
					$client .= "<tr><td style='color:#484848;font-weight:bold;width:200px;'>".$this->lang->line('housenumber')."</td><td>".$client_info['housenumber']."<br></td></tr>";
					$client .= "<tr><td style='color:#484848;font-weight:bold;width:200px;'>".$this->lang->line('postal')."</td><td>".$client_info['postal']."<br></td></tr>";
					$client .= "<tr><td style='color:#484848;font-weight:bold;width:200px;'>".$this->lang->line('city')."</td><td>".$client_info['city']."<br></td></tr>";
					$client .= "<tr><td style='color:#484848;font-weight:bold;width:200px;'>".$this->lang->line('country')."</td><td>".$client_info['country']."<br></td></tr>";
					
					if($client_info['delivery_address'] == 1){
						$client .= "<tr><td style='color:#484848;font-weight:bold;width:200px;' colspan=2><br><b style='font-size:13px;'>".$this->lang->line('mail_delivery_address')."</b></td></tr>";
						$client .= "<tr><td style='color:#484848;font-weight:bold;width:200px;'>".$this->lang->line('street')."</td><td>".$client_info['delivery_street']."<br></td></tr>";
						$client .= "<tr><td style='color:#484848;font-weight:bold;width:200px;'>".$this->lang->line('housenumber')."</td><td>".$client_info['delivery_housenumber']."<br></td></tr>";
						$client .= "<tr><td style='color:#484848;font-weight:bold;width:200px;'>".$this->lang->line('postal')."</td><td>".$client_info['delivery_postal']."<br></td></tr>";
						$client .= "<tr><td style='color:#484848;font-weight:bold;width:200px;'>".$this->lang->line('city')."</td><td>".$client_info['delivery_city']."<br></td></tr>";
						$client .= "<tr><td style='color:#484848;font-weight:bold;width:200px;'>".$this->lang->line('country')."</td><td>".$client_info['delivery_country']."<br></td></tr>";
					}	
					
					$client .= "<tr><td style='color:#484848;font-weight:bold;width:200px;'><br>".$this->lang->line('order_date')."</td><td><br>".date('d-m-Y H:i:s', ($data['order']['date_created']))."</td></tr>";
					$client .= "<tr><td style='color:#484848;font-weight:bold;width:200px;'>".$this->lang->line('order_code')."</td><td>".$data['order']['order_number']."</td></tr>";
					$client .= "<tr><td style='color:#484848;font-weight:bold;width:200px;'>".$this->lang->line('order_type')."</td><td>".((isset($data['order']['payment_type']) && isset($data['payment_type']) && isset($data['payment_type'][$data['order']['payment_type']])) ? $data['payment_type'][$data['order']['payment_type']] : '')."</td></tr>";
					$client .= "<tr><td style='color:#484848;font-weight:bold;width:200px;'>".$this->lang->line('delivery_type')."</td><td>".((isset($data['order']['delivery']) && $data['order']['delivery'] == 1) ? $this->lang->line('delivery_type_1') : $this->lang->line('delivery_type_0'))."</td></tr>";
					$client .= "</table><br><br>";
		
				}		
			
				$settings = getSettings();
				
				$company_info = '';
				$company_info .= "<br><br><table style='color:#484848;font-size: 12px; font-family: Arial; font-weight:normal;'>";
				$company_info .= "<tr><td width='80%'><a href='".SITE_URL."'><img src=\"cid:overnight-logo\" border='0'></td></tr>";
				$company_info .= "<td width='80%'><b>".$settings['company']."</b><br>
							".$settings['street']." ".$settings['housenumber']." <br>".$settings['postal']." ".$settings['city']." ".$settings['country']."";
				if($settings['telephone'] != '') $company_info .= "<br>T: ".$settings['telephone']."";
				if($settings['fax'] != '') $company_info .= "<br>F: ".$settings['fax']."";
				if($settings['email'] != '') $company_info .= "<br>E: ".$settings['email']."";
				$company_info .= "</td></tr>";
				$company_info .= "</table>";
		
				// e-mail addresses
				$mail_admin = $settings['admin_mail'];
				$mail_user = $client_info['email'];
								
				$subject_user = $order_mails['client_subject']." ".$data['order']['order_number'];
				$text_user = "<table style='color:#484848;font-size:12px; font-family: Arial; font-weight:normal;' width=100%><tr><td>
														Geachte ".$client_info['firstname']." ".$client_info['lastname'].",<br>".
												str_replace("bestelnummer",$this->lang->line('ordernumber').": ".$data['order']['order_number'],($order_mails['client_content']))."</td></tr></table>";
										
				$subject_admin = $order_mails['admin_subject']." ".$data['order']['order_number'];
				$text_admin = "<table style='color:#484848;font-size:12px; font-family: Arial; font-weight:normal;' width=100%><tr><td>".
														str_replace("bestelnummer",$this->lang->line('ordernumber').": ".$data['order']['order_number'],($order_mails['admin_content']))."</td></tr></table>";

				// messsages
				$message_admin = $text_admin . $client . $products . $company_info;
				$message_user = $text_user . $client . $products . $company_info;
				
				// e-mail for user
				$email_user = new PHPMailer();
				$email_user->AddAddress($mail_user);
				$email_user->IsHTML(true);
				$email_user->From = $mail_admin;
				$email_user->FromName = $order_mails['client_fromname'];
				$email_user->AddEmbeddedImage(BASE_PATH.IMG_DIR."logo.png","overnight-logo",BASE_PATH.IMG_DIR."logo.png","base64","image/png");
				$email_user->Subject = $subject_user;
				$email_user->Body .= $message_user;	
								
				if ($email_user->Send()) {
					$msg =  "Bedankt, uw bericht is naar ons verzonden. Wij nemen z.s.m. contact met u op.";
				}
				else $msg =  "E-mail niet verzonden neem contact met ons op";	
								
				// e-mail for admin
				$email_admin = new PHPMailer();
				$email_admin->AddAddress($mail_admin);
				$email_admin->IsHTML(true);
				$email_admin->From = $mail_user;
				$email_admin->FromName = $order_mails['admin_fromname']. ' - ' . $client_info['firstname']." ".$client_info['lastname'];
				$email_admin->AddEmbeddedImage(BASE_PATH.IMG_DIR."logo.png","overnight-logo",BASE_PATH.IMG_DIR."logo.png","base64","image/png");
				$email_admin->Subject = $subject_admin;
				$email_admin->Body .= $message_admin;
								
				if ($email_admin->Send()) {
					$msg =  "Bedankt, uw bericht is naar ons verzonden. Wij nemen z.s.m. contact met u op.";
				}			
				else $msg =  "E-mail niet verzonden neem contact met ons op";	
				
			}
		}
		
		if($ststus == 0){
			$sql = '
			UPDATE `order`, `order_content`
			SET
				`order_content`.comments 		= :comments,
				`order_content`.order_status_id = :order_status_id
			WHERE `order`.order_id 			= :order_id
			AND `order_content`.order_id 	= :order_id
			';
			
			$this->db->query($sql, array(
				'comments' 		=> $post['order']['comments'],
				'order_status_id' 	=> $post['order']['order_status_id'],
				'order_id' 	=> $id
			));
		}
		else{
			$sql = '
			UPDATE `order`, `order_content`
			SET
				`order_content`.order_status_id = :order_status_id
			WHERE `order`.order_id 			= :order_id
			AND `order_content`.order_id 	= :order_id
			';
			
			$this->db->query($sql, array(
				'order_status_id' 	=> $post['order']['order_status_id'],
				'order_id' 	=> $id
			));
		}
		
	}
	
	function delete($id)
	{
		$sql = 'SELECT * FROM `order`,`order_content` WHERE `order`.order_id = `order_content`.order_id AND `order`.order_id = ?';
		$r = $this->db->query($sql, array($id));

		$this->db->query('DELETE FROM `order` WHERE `order`.order_id = ?', array($id));
		$this->db->query('DELETE FROM `order_content` WHERE `order_content`.order_id = ?', array($id));
		
		$this->db->query('DELETE FROM `order_products` WHERE `order_products`.order_content_id = ?', array($r[0]['order_content_id']));
		$this->db->query('DELETE FROM `order_client` WHERE `order_client`.order_content_id = ?', array($r[0]['order_content_id']));
	}
		
	function delete_product($product_id, $order_id)
	{
		$this->db->query('DELETE FROM `order_products` WHERE `order_products`.order_products_id = ?', array($product_id));
		
		$sql = '
		SELECT *
		FROM `order`
		INNER JOIN `order_content`
		ON `order_content`.order_id = `order`.order_id
		WHERE `order`.order_id = ?
		';
		
		$r = $this->db->query($sql, array($order_id));
		$data['order'] = $r[0];
		
		$sql = '
			SELECT *
			FROM `order_products`
			WHERE `order_products`.order_content_id = ?
		';

		$data['order_products'] = $this->db->query($sql, array($data['order']['order_content_id']));
		
		$total = 0;
		
		if(isset($data['order_products']) && count($data['order_products']) > 0)
		{
			foreach($data['order_products'] as $product)
			{
				$price = 0;
				
				$price = $product['price'];
				
				if($product['offer_price'] != 0)
				{
					$price = $product['offer_price'];
				}
				else{
					if($product['discount_percent'] > 0)
					{
						$price = $price - ($price * $product['discount_percent'] / 100);
					}
					else if($product['discount_price'] > 0)
					{
						$price = $price - $product['discount_price'];
					}
				}
				
				if($product['has_vat'] == 1) $price = $price + ($price * $data['order']['vat_costs'] / 100);
					
				$total += formatPriceDecimals($price * $product['quantity']);								
			}
		}
		
		if($data['order']['discount_percent'] > 0)
		{
			$total = $total - ($total * $data['order']['discount_percent'] / 100);
		}
		else if($data['order']['discount_price'] > 0)
		{
			$total = $total - $data['order']['discount_price'];
		}
		if($data['order']['discount_code'] != ''){
			if($data['order']['discount_code_percent'] > 0)
			{
				$total = $total - ($total * $data['order']['discount_code_percent'] / 100);
			}
			else if($data['order']['discount_code_price'] > 0)
			{
				$total = $total - $data['order']['discount_code_price'];
			}
		}
		$total = formatPriceDecimals($total);
		
		if($data['order']['vat_costs'] > 0)
		{
			//$total = $total + ($total * $data['order']['vat_costs'] / 100);
		}
		
		if($data['order']['transport'] > 0 && $data['order']['delivery'] == 1)
		{
			$total = $total + $data['order']['transport'];
		}
		$total = formatPriceDecimals($total);
		
		$sql = '
			UPDATE `order`, `order_content`
			SET
				`order_content`.total_price 	= :total_price
			WHERE `order`.order_id 			= :order_id
			AND `order_content`.order_id 	= :order_id
		';
		
		$this->db->query($sql, array(
			'total_price' 	=> $total,
			'order_id' 	=> $order_id
		));
	}
	
	function update_overview($post)
	{
		foreach($post['active'] as $k => $v)
		{
			$this->db->query('UPDATE `order` SET `order`.active = ? WHERE `order`.order_id = ? LIMIT 1', array($v, $k));
		}
		foreach($post['highlight'] as $k => $v)
		{
			$this->db->query('UPDATE `order` SET `order`.highlight = ? WHERE `order`.order_id = ? LIMIT 1', array($v, $k));
		}
	}

	function getProduct($product_id, $language_id){
		$sql = '
		SELECT *
		FROM `product`
		INNER JOIN `product_content`
		ON `product_content`.product_id = `product`.product_id
		WHERE `product`.product_id = ?
		AND `product_content`.language_id = ?
		';
		
		$r = $this->db->query($sql, array($product_id, $language_id));
		
		if(isset($r) && count($r) > 0){
			$product = $r[0];
			if($product['category_id'] != 0){
				$sql = '
					SELECT *
					FROM `category`
					INNER JOIN `category_content`
					ON `category_content`.category_id = `category`.category_id
					WHERE `category`.category_id = ?
					AND `category_content`.language_id = ?
					ORDER BY `category`.order
				';
				
				$cat_level_1 = $this->db->query($sql, array($product['category_id'], $language_id));
				if(isset($cat_level_1) && count($cat_level_1) > 0){
					if($cat_level_1[0]['parent_id'] != 0){
						$sql = '
							SELECT *
							FROM `category`
							INNER JOIN `category_content`
							ON `category_content`.category_id = `category`.category_id
							WHERE `category`.category_id = ?
							AND `category_content`.language_id = ?
							ORDER BY `category`.order
						';
						
						$cat_level_2 = $this->db->query($sql, array($cat_level_1[0]['parent_id'], $language_id));
						
						if($cat_level_2[0]['parent_id'] != 0){
							$sql = '
								SELECT *
								FROM `category`
								INNER JOIN `category_content`
								ON `category_content`.category_id = `category`.category_id
								WHERE `category`.category_id = ?
								AND `category_content`.language_id = ?
								ORDER BY `category`.order
							';
							
							$cat_level_3 = $this->db->query($sql, array($cat_level_2[0]['parent_id'], $language_id));
						}
					}
				}
			}
			if(isset($cat_level_3) && $cat_level_3 && count($cat_level_3) > 0){
				if($cat_level_3[0]['discount_primary'] == 1){
					$discount_percent = $cat_level_3[0]['discount_percent'];
					$discount_price = $cat_level_3[0]['discount_price'];
				}
				else{
					if($cat_level_2[0]['discount_primary'] == 1){
						$discount_percent = $cat_level_2[0]['discount_percent'];
						$discount_price = $cat_level_2[0]['discount_price'];
					}
					else{
						if($cat_level_1[0]['discount_primary'] == 1){
							$discount_percent = $cat_level_1[0]['discount_percent'];
							$discount_price = $cat_level_1[0]['discount_price'];
						}
					}
				}
			}else{
				if(isset($cat_level_2) && $cat_level_2 && count($cat_level_2) > 0){
					if($cat_level_2[0]['discount_primary'] == 1){
						$discount_percent = $cat_level_2[0]['discount_percent'];
						$discount_price = $cat_level_2[0]['discount_price'];
					}
					else{
						if($cat_level_1[0]['discount_primary'] == 1){
							$discount_percent = $cat_level_1[0]['discount_percent'];
							$discount_price = $cat_level_1[0]['discount_price'];
						}
					}
				}
				else{
					if(isset($cat_level_1) && $cat_level_1 && count($cat_level_1) > 0){
						if($cat_level_1[0]['discount_primary'] == 1){
							$discount_percent = $cat_level_1[0]['discount_percent'];
							$discount_price = $cat_level_1[0]['discount_price'];
						}
					}
				}
			}
			
			if(isset($discount_percent) && isset($discount_price)){
				$product['discount_percent'] = $discount_percent;
				$product['discount_price'] = $discount_price;
			}
			else{
				if($product['discount_percent'] == 0 && $product['discount_price'] == 0){
					if(isset($cat_level_1) && $cat_level_1 && count($cat_level_1) > 0 && ($cat_level_1[0]['discount_percent'] != 0 || $cat_level_1[0]['discount_price'] != 0)){
						$product['discount_percent'] = $cat_level_1[0]['discount_percent'];
						$product['discount_price'] = $cat_level_1[0]['discount_price'];
					}else{
						if(isset($cat_level_2) && $cat_level_2 && count($cat_level_2) > 0 && ($cat_level_2[0]['discount_percent'] != 0 || $cat_level_2[0]['discount_price'] != 0)){
							$product['discount_percent'] = $cat_level_2[0]['discount_percent'];
							$product['discount_price'] = $cat_level_2[0]['discount_price'];
						}else{
							if(isset($cat_level_3) && $cat_level_3 && count($cat_level_3) > 0 && ($cat_level_3[0]['discount_percent'] != 0 || $cat_level_3[0]['discount_price'] != 0)){
								$product['discount_percent'] = $cat_level_3[0]['discount_percent'];
								$product['discount_price'] = $cat_level_3[0]['discount_price'];
							}
						}
					}
				}
			}
			
			return $product;
		}
		else return false;
	}
	
	function fetch_product_options($language_id, $category_id, $prod_id = 0)
	{
		
		$sql = '
			SELECT * 
			FROM `product_options`, `product_options_heading`, `product_options_item_category`
			WHERE `product_options`.product_options_id = `product_options_heading`.product_options_id 
			AND `product_options_heading`.language_id = :lang 
			AND `product_options_item_category`.product_options_item_id = `product_options`.product_options_id
			AND `product_options_item_category`.category_id = :category_id 
			ORDER BY `product_options`.order ASC
			';
			
		$data = $this->db->query($sql, array('lang' => $language_id, 'category_id' => $category_id)); //
		
		$i = 0;
		
		if(count($data) > 0)
			foreach($data as $product_options)
			{
				$i = $product_options['product_options_id'];
				
				$return[$i] = $product_options;
						
				
				$sql = '
				SELECT *
				FROM `product_options`
				WHERE `product_options`.product_options_id = :product_options_id
				';
				
				$type = $this->db->query($sql, array('product_options_id' => $product_options['product_options_id']));
				
				$type = $type[0]['type'];
				$return[$i]['type'] = $type;
				
				$sql = '
				SELECT *, `product_options_item`.title as option_title
				FROM `product_options_item`, `product_options_heading`
				WHERE `product_options_item`.product_options_heading_id 	= `product_options_heading`.product_options_heading_id 
				AND `product_options_heading`.language_id 					= :lang 
				AND `product_options_heading`.product_options_id 			= :product_options_id 
				ORDER BY `product_options_item`.product_options_item_id
				';
				
				$subelements2 = $this->db->query($sql, array('lang' => $language_id, 'product_options_id' => $product_options['product_options_id']));
				
				if($type != 1){
					
					foreach($subelements2 as $sub_element){
						$return[$i]['subelements'][$sub_element['product_options_item_id']] = $sub_element;
						$return[$i]['type'] = $type;
						$sql = '
							SELECT * 
							FROM `product_options_item_saved`
							WHERE `product_options_item_saved`.product_options_item_id = :product_options_item_id
							AND `product_options_item_saved`.value = ""
							AND `product_options_item_saved`.saved = 1
							AND `product_options_item_saved`.table_id = :table_id 
							';
						$selected = $this->db->query($sql, array('product_options_item_id' => $sub_element['product_options_item_id'], 'table_id' => $prod_id));
						
						if($selected && count($selected) >= 1){
							foreach($selected as $option){
								$return[$i]['selected'][] = $option['product_options_item_id'];
							}
						}else{
							if($prod_id != 0){
								unset($return[$i]['subelements'][$sub_element['product_options_item_id']]);
							}
						}
					}
				}else{
					$return[$i]['type'] = $type;
						$sql = '
							SELECT * 
							FROM `product_options_item_saved`,`product_options_heading`
							WHERE `product_options_item_saved`.product_options_item_id = :product_options_item_id
							AND `product_options_item_saved`.saved = 0
							AND `product_options_heading`.language_id = :language_id
							AND `product_options_heading`.product_options_heading_id = `product_options_item_saved`.product_options_heading_id
							AND `product_options_item_saved`.table_id = :table_id 
							LIMIT 1';
						$selected = $this->db->query($sql, array('language_id' => $language_id, 'product_options_item_id' => $product_options['product_options_id'], 'table_id' => $prod_id));
					
					if($selected && count($selected) >= 1) $return[$i]['selected_value'] = $selected[0]['value'];
						else $return[$i]['selected_value'] = '';
				}
				$i++;
			}
		$options = '';
		
		if(isset($return) && count($return) > 0){
			foreach($return as $option){
				if($option['type'] == 1){
					if($option['selected_value'] != ''){
						$options .= "<b>".$option['title']."</b>";
						$options .= $option['selected_value']."<br>";
					}
				}
				else{
					if(isset($option['subelements']) && count($option['subelements']) > 0  && isset($option['selected']) && count($option['selected']) > 0 && isset($option['subelements'][$option['selected'][0]])){
						if($option['type'] != 3){
							if($option['subelements'][$option['selected'][0]]['option_title'] != ''){ 
								$options .= "<b>".$option['title']."</b>";
								$options .= $option['subelements'][$option['selected'][0]]['option_title']."<br>";
							}
						}else{
							$options .= "<b>".$option['title']."</b>";
							foreach($option['selected'] as $k => $obj){
								if($k > 0) $options .= ", ";
								$options .= $option['subelements'][$obj]['option_title'];
							}
							$options .= "<br>";
						}
					}
				}	
			}
		}
		return $options;
	}

	function fetch_filter($language_id, $category_id, $prod_id = 0)
	{
		
		$sql = '
			SELECT * 
			FROM `filter`, `filter_heading`, `filter_item_category`
			WHERE `filter`.filter_id = `filter_heading`.filter_id 
			AND `filter_heading`.language_id = :lang 
			AND `filter_item_category`.filter_item_id = `filter`.filter_id
			AND `filter_item_category`.category_id = :category_id 
			ORDER BY `filter`.order ASC
			';
			
		$data = $this->db->query($sql, array('lang' => $language_id, 'category_id' => $category_id)); //
		$i = 0;
		
		if(count($data) > 0)
			foreach($data as $filter)
			{
				$i = $filter['filter_id'];
				
				$return[$i] = $filter;
						
				$sql = '
				SELECT *, `filter_item`.title as option_title
				FROM `filter_item`, `filter_heading`
				WHERE `filter_item`.filter_heading_id 	= `filter_heading`.filter_heading_id 
				AND `filter_heading`.language_id 					= :lang 
				AND `filter_heading`.filter_id 			= :filter_id 
				ORDER BY `filter_item`.filter_item_id
				';
				
				$subelements2 = $this->db->query($sql, array('lang' => $language_id, 'filter_id' => $filter['filter_id']));
				
				foreach($subelements2 as $sub_element){
					$return[$i]['subelements'][$sub_element['filter_item_id']] = $sub_element;
					$sql = '
							SELECT * 
							FROM `filter_item_saved`
							WHERE `filter_item_saved`.filter_item_id = :filter_item_id
							AND `filter_item_saved`.saved = 1
							AND `filter_item_saved`.table_id = :table_id 
							';
					$selected = $this->db->query($sql, array('filter_item_id' => $sub_element['filter_item_id'], 'table_id' => $prod_id));
						
					if($selected && count($selected) >= 1){
						foreach($selected as $option){
							$return[$i]['selected'][] = $option['filter_item_id'];
						}
					}else{
						if($prod_id != 0){
							unset($return[$i]['subelements'][$sub_element['filter_item_id']]);
						}
					}
				}
				$i++;
			}
		$options = '';
		
		if(isset($return) && count($return) > 0){
			foreach($return as $option){
				if(isset($option['subelements']) && count($option['subelements']) > 0  && isset($option['selected']) && count($option['selected']) > 0 && isset($option['subelements'][$option['selected'][0]])){
					$options .= "<b>".$option['title']."</b>";
					foreach($option['selected'] as $k => $obj){
						if($k > 0) $options .= ", ";
							$options .= $option['subelements'][$obj]['option_title'];
					}
					$options .= "<br>";
				}	
			}
		}
		return $options;
	}

	function getStatusList(){
				
		$sql = '
			SELECT *
			FROM `order_status`
			WHERE `order_status`.admin_status = 1
		';
		$data = $this->db->query($sql);
		
		if(isset($data) && count($data) > 0) return $data;
		else return false;
	}
	
	function getPaymentList(){
			
		$sql = '
			SELECT *
			FROM `payment_type`
		';
		
		$data = $this->db->query($sql);
		
		if(isset($data) && count($data) > 0) return $data;
		else return false;
	}

	function fetch_all_clients()
	{		
		$sql = 'SELECT * FROM `client`';
		$data = $this->db->query($sql);

		if(isset($data) && count($data) > 0) return $data;
		else return false;
	}
	
	function getOrder_number(){
		// 2012(year)10(month)00000001
		$sql = '
			SELECT * 
			FROM `order`, `order_content`
			WHERE `order`.order_id = `order_content`.order_id
			AND `order_content`.order_number LIKE "'.date('Y').date('m').'%"
			ORDER BY `order_content`.order_number DESC
			LIMIT 1
		';
		$r = $this->db->query($sql);
		if(isset($r) && count($r) > 0 && $r){
			$number = '';
			$num = ((string)(intval(substr($r[0]['order_number'],6,14))+1));
			for($i = 1;$i<=(8-strlen($num));$i++){
				$number .= '0';
			}
			$number .= $num;
			return date('Y').date('m').$number;
		}
		else return date('Y').date('m').'00000001';
	}
	
	function getProducts(){
		$sql = '
			SELECT * FROM `product`
			INNER JOIN `product_content`
			ON `product`.product_id = `product_content`.product_id
			WHERE `product_content`.language_id = ?
			ORDER BY `product_content`.title ASC
			';
		
		$data = $this->db->query($sql, array($this->config->item('default_language')));
		
		if(isset($data) && count($data) > 0) return $data;
		else return false;
	}	
	function export(){

		$excelwriter =& load_class('ExcelWriter', 'core');

		$excel=new ExcelWriter(BASE_PATH.SYS_PATH."core/orders.xls");
		
		if($excel==false)	
			echo $excel->error;	

		$sql = '
		SELECT *
		FROM `order`
		INNER JOIN `order_content`
		ON `order_content`.order_id = `order`.order_id
		WHERE `order`.order_id = ?
		';
		
		$r = $this->db->query($sql, array($order_id));
		$data['order'] = $r[0];

		$sql = '
			SELECT *
			FROM `order_client`
			WHERE `order_client`.order_content_id = ?
		';
	
		$data['order_client'] = $this->db->query($sql, array($data['order']['order_content_id']));
		
		$sql = '
			SELECT *
			FROM `order_products`
			WHERE `order_products`.order_content_id = ?
		';

		$data['order_products'] = $this->db->query($sql, array($data['order']['order_content_id']));
		
		$sql = '
			SELECT *
			FROM `order_status`
			WHERE `order_status`.admin_status = 1
		';
		$data['order_status'] = $this->db->query($sql);
		
		$sql = '
			SELECT *
			FROM `payment_type`
		';
		$payments = $this->db->query($sql);
		if(isset($payments) && count($payments) > 0){
			foreach($payments as $payment){
				$data['payment_type'][$payment['payment_type_id']] = $payment['name'];
			}
		}

		$excel->writeRow();
		$excel->writeCol("Nr");
		$excel->writeCol($this->lang->line('order_code'));
		$excel->writeCol($this->lang->line('order_client_id'));
		$excel->writeCol($this->lang->line('status'));
		$excel->writeCol($this->lang->line('total_price'));
		$excel->writeCol($this->lang->line('order_code'));

		if(isset($data['order']) && count($data['order']) > 0)
		{
			foreach($data['order'] as $k => $order){
				$excel->writeRow();
				$excel->writeCol($k);
				$excel->writeCol($order['order_number']);
				$excel->writeCol($order['order_client_id']);
				$excel->writeCol($order['order_number']);
				$excel->writeCol($order['order_number']);
				$excel->writeCol($order['order_number']);
			}
		}
		 
		$excel->close();
		header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
		header ("Cache-Control: no-cache, must-revalidate");
		header ("Pragma: no-cache");
		header ("Content-type: application/x-msexcel");
		header ("Content-Disposition: attachment; filename=orders.xls" );
		header ("Content-Description: PHP/INTERBASE Generated Data" );
		readfile(BASE_PATH.SYS_PATH."core/orders.xls");
	}
}

?>