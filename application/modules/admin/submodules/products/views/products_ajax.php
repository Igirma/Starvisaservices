<?php
	session_start();
	if(isset($_POST['cat'])){
		$_SESSION['category_id'] = $_POST['cat'];
	}
	if(isset($_POST['search'])){
		$_SESSION['prod_search'] = $_POST['search'];
	}

?>