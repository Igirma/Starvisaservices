<?php

class webshop_model extends model
{
	function getSales($year, $month)
	{	
		$date_from = strtotime($year.'-'.$month.'-01');
		$date_until = strtotime(date("Y-m-d", $date_from) . " +1 month");
		
		$sql = 'SELECT `order`.date_created, `order_content`.total_price as price, `order_content`.* FROM `order`, `order_content`
				WHERE `order`.order_id = `order_content`.order_id
				AND `order`.date_created >= ?
				AND `order`.date_created <= ?			
				AND (`order_content`.order_status_id = 2 OR `order_content`.order_status_id = 3 OR `order_content`.order_status_id = 4 OR 
					 `order_content`.order_status_id = 5 OR `order_content`.order_status_id = 6)			
				GROUP BY `order`.date_created
				ORDER BY `order`.date_created ASC
				';
		$data = $this->db->query($sql, array($date_from, $date_until));
		
		$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		for($i = 1; $i <= $days_in_month; $i++){
			$most_sold[$i] = 0;
		}
		
		$big_total = 0;
		$current = '';
		$transport_total = 0;
		if(isset($data) && count($data) > 0){
			$day = 1;
			$total = 0;
			foreach($data as $date){
				if($day != date("j", $date['date_created'])){
					$most_sold[$day] = $total;
					$total = 0;
				}
				if($current != $date['date_created'])
					$big_total += $date['total_price'];
				$current = $date['date_created'];
				if($date['delivery'] == 1) $transport_total += $date['transport'];
				$total += $date['total_price'];
				$day = date("j", $date['date_created']);
			}
			$most_sold[$day] = $total;
			
		}
		
		return array('graph_data' => $most_sold, 'order_total' => $big_total, 'transport_total' => $transport_total);
	}

	function getVatTotal($year, $month)
	{	
		$date_from = strtotime($year.'-'.$month.'-01');
		$date_until = strtotime(date("Y-m-d", $date_from) . " +1 month");
		
		$sql = 'SELECT `order_products`.*, `order_content`.vat_costs FROM `order_products`, `order`, `order_content`
				WHERE `order`.order_id = `order_content`.order_id
				AND `order_content`.order_content_id = `order_products`.order_content_id
				AND `order`.date_created >= ?
				AND `order`.date_created <= ?			
				ORDER BY `order`.date_created ASC
				';
		$data = $this->db->query($sql, array($date_from, $date_until));
		
		$total_vat = 0;
		if(isset($data) && count($data) > 0){
			foreach($data as $product){
				$price = 0;
				if($product['offer_price'] != 0){
					$price = $product['offer_price'];
				}else{
					$price = $product['price'];
					if($product['discount_percent'] > 0) $price = $product['price'] - ($product['price'] * $product['discount_percent'] / 100);
					else if($product['discount_price'] > 0) $price = $product['price'] - $product['discount_price'];
				}
				if($product['has_vat'] == 1) 
					$total_vat += $price * $product['quantity'] * $product['vat_costs'] / 100;
			}
		}

		return $total_vat;
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

	function get_products($year, $month)
	{	
		
		$date_from = strtotime($year.'-'.$month.'-01');
		$date_until = strtotime(date("Y-m-d", $date_from) . " +1 month");
		
		$sql = 'SELECT `order_products`.*, SUM(`order_products`.quantity) as total_quantity FROM `order_products`, `order`, `order_content`
				WHERE `order`.order_id = `order_content`.order_id
				AND `order_content`.order_content_id = `order_products`.order_content_id
				AND `order`.date_created >= ?
				AND `order`.date_created <= ?			
				GROUP BY `order_products`.articlenumber
				ORDER BY SUM(`order_products`.quantity) DESC
				LIMIT 20
				';
		$most_sold = $this->db->query($sql, array($date_from, $date_until));
		
		if(isset($most_sold) && count($most_sold) > 0) return $most_sold;
		return false;
	}

}
?>