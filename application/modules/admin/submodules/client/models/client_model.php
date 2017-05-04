<?php 
class client_model extends model
{


	function fetch_all()
	{		
		$sql = 'SELECT * FROM `client`';
		$data['client'] = $this->db->query($sql);

		return $data;
	}
	
	function update_overview($post)
	{
		foreach($post['active'] as $k => $v)
		{
			$this->db->query('UPDATE `client` SET `client`.active = ? WHERE `client`.client_id = ?', array($v, $k));
		}
	}
	
	function fetch($client_id)
	{
		$sql = 'SELECT * FROM `client` WHERE `client`.client_id = ?';
		$data = $this->db->query($sql, array($client_id));

		return $data[0];
	}
	
	function add($post)
	{
		$sql = '
		INSERT INTO `client`
		(
			clientname,
			email,
			password,
			firstname,
			phone,
			lastname,
			company,
			street,
			housenumber,
			postal,
			city,
			country,
			delivery_address,
			delivery_street,
			delivery_housenumber,
			delivery_postal,
			delivery_city,
			delivery_country			
		)
		VALUES
		(
			:clientname,
			:email,
			:password,
			:firstname,
			:phone,
			:lastname,
			:company,
			:street,
			:housenumber,
			:postal,
			:city,
			:country,
			:delivery_address,
			:delivery_street,
			:delivery_housenumber,
			:delivery_postal,
			:delivery_city,
			:delivery_country
		)
		';
		
		$this->db->query($sql, array(
			'clientname' => strtolower($post['clientname']),
			'email' => $post['email'],
			'password' 			=> sha1($post['password']),
			'firstname'	 		=> ucfirst($post['firstname']),
			'phone'				=> $post['phone'],
			'lastname' 			=> ucfirst($post['lastname']),
			'company' 			=> ucfirst($post['company']),
			'street' 			=> $post['street'],
			'housenumber' 		=> $post['housenumber'],
			'postal' 			=> $post['postal'],
			'city' 				=> $post['city'],
			'country' 			=> $post['country'],
			'delivery_address'	=> ((isset($post['delivery_address']))?1:0),
			'delivery_street' 	=> $post['delivery_street'],
			'delivery_housenumber' => $post['delivery_housenumber'],
			'delivery_postal' 	=> $post['delivery_postal'],
			'delivery_city'	 	=> $post['delivery_city'],
			'delivery_country' 	=> $post['delivery_country']
	
		));
	}
	
	function edit($post)
	{
		debug($post);
		
		$client_id = $post['client_id'];
		$old_password = $post['old_password'];
		$password = $post['password'];
		$password_check = $post['password_check'];
		
		if($old_password != '')
		{
			$sql = '
			UPDATE `client`
			SET
				`client`.password = :password
			WHERE `client`.client_id = :client_id
			';

			$this->db->query($sql, array(
			'password' => sha1($post['password_check']),
			'client_id' => $post['client_id']
			));
		}
		
		$sql = '
		UPDATE `client`
		SET
			`client`.clientname = :clientname,
			`client`.email = :email,
			`client`.firstname = :firstname,
			`client`.phone = :phone,
			`client`.lastname = :lastname,
			`client`.company = :company,
			`client`.street = :street,
			`client`.housenumber = :housenumber,
			`client`.postal = :postal,
			`client`.city = :city,
			`client`.country = :country,
			`client`.delivery_address = :delivery_address,
			`client`.delivery_street = :delivery_street,
			`client`.delivery_housenumber = :delivery_housenumber,
			`client`.delivery_postal = :delivery_postal,
			`client`.delivery_city = :delivery_city,
			`client`.delivery_country = :delivery_country		
		WHERE `client`.client_id = :client_id
		';

		$this->db->query($sql, array(
			'clientname' => strtolower($post['clientname']),
			'email' => $post['email'],
			'firstname' => ucfirst($post['firstname']),
			'phone' => $post['phone'],
			'lastname' => ucfirst($post['lastname']),
			'company' => ucfirst($post['company']),
			'street' => $post['street'],
			'housenumber' => $post['housenumber'],
			'postal' => $post['postal'],
			'city' => $post['city'],
			'country' => $post['country'],
			'delivery_address' => $post['delivery_address'],
			'delivery_street' => $post['delivery_street'],
			'delivery_housenumber' => $post['delivery_housenumber'],
			'delivery_postal' => $post['delivery_postal'],
			'delivery_city' => $post['delivery_city'],
			'delivery_country' => $post['delivery_country'],			
			'client_id' => $post['client_id']
		));
		
	}

	function delete($id)
	{
		$sql = 'DELETE FROM `client` WHERE `client`.client_id = ?';
		$this->db->query($sql, array($id));
	}
	
}
?>