<?php
	session_start();
	if(isset($_POST['search'])){
		$_SESSION['order_search'] = $_POST['search'];
	}
	if(isset($_POST['filter'])){
		$_SESSION['order_quarter'] = $_POST['filter'];
		$_SESSION['order_nr'] = $_POST['nr'];
		$_SESSION['order_year'] = $_POST['year'];
	}

?>