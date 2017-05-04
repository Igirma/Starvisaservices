<?php require_once(ELEM_DIR . 'admin_header.php');

//debug($data);

?>

<div id="container">

	<div id="overview">
		
		<div class="column">

			<div class="header">
				<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-info"></div>
				<h1>Help</h1>
			</div>
			
		</div>
		
		<div class="column">
		
			<div class="subcolumn">
			
				<!-- Add modules Help -> Add category_id's based on user permissions  Example: 1-2-34-24-32. -->
				<?=base64_decode(file_get_contents('http://www.orangetalent.nl/faq/1-15-16-17'));?>
			
			</div>
		
		</div>
		
	</div>
	
</div>

<?php require_once(ELEM_DIR . 'admin_footer.php'); ?>