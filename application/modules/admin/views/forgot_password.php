<?php require_once ELEM_DIR . 'admin_header.php'; ?>

<?=validation_errors();?>


<div id="login_container" style="border-top: 5px solid #fe7700;">

	<div id="header" style="background-color: #1e1e1e; border-bottom: 1px solid #616161; text-align: center;">
			
		<img style="margin: 20px;" src="<?=SITE_URL . ELEM_DIR . 'img/logo_login.png';?>">

	</div>

</div>

<div id="container" style="width: 230px; height: 155px; position: absolute; left: 50%; top: 50%; margin: -78px 0 0 -115px;">

	<div id="overview">
	
		<div class="column" style="min-width: 230px!important; width: 230px;">
		
			<div class="pie header">
				<div style="position: absolute; left: 11px; top: 10px;" class="sprite sprite-news"></div>
				<h1><?=$this->lang->line('forgot_password_subject');?></h1>
			
			</div>
			
			<form method="post" action="<?=$this->url->current;?>">

				<table>
					
					<tbody>
					
						<tr>
						
							<td>
								<?=$this->lang->line('username');?>
							</td>
							
						</tr>
						
						<tr>
						
							<td>
								<input style="width: 90%;" id="username" type="text" name="username" value="">
							</td>
						
						</tr>
						
						<tr>
						
							<td>
								<input type="submit" name="sumbit" value="<?=$this->lang->line('submit');?>">
							</td>
						
						</tr>
										
					</tbody>
				
				</table>
				
			</form>
		</div>
	</div>
</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>