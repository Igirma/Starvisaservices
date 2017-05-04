<?php require_once ELEM_DIR . 'admin_header.php'; ?>

<?=validation_errors();?>


<div id="container" style="width: 330px; height: 155px; position: absolute; left: 50%; top: 50%; margin: -78px 0 0 -165px;">

	<div id="overview">
	
		<div class="column" style="min-width: 330px!important; width: 330px;">
		
			<div class="pie header">
				<div style="position: absolute; left: 11px; top: 10px;" class="sprite sprite-news"></div>
				<h1>Wachtwoord wijzigen</h1>
			
			</div>
			
			<form method="post" action="<?=$this->url->current;?>">

				<table>
					
					<tbody>
					
						<tr>
							<td><?=$this->lang->line('password');?>:</td>
							<td><input style="width: 85%;" type="password" name="password"></td>
						</tr>
	
						<tr>
							<td><?=$this->lang->line('password_repeat');?>:</td>
							<td><input style="width: 85%;" type="password" name="password_repeat"></td>
						</tr>
						
						<tr>
							<td>&nbsp;</td>
							<td style="text-align: right; padding-right: 10px;"><input type="submit" name="submit" value="<?=$this->lang->line('submit');?>"</td>
						</tr>
										
					</tbody>
				
				</table>
				
			</form>
		</div>
	</div>
</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>


<?php require_once ELEM_DIR . 'admin_header.php'; ?>

<?=validation_errors();?>

<?php
if($this->url->segment(3) == 'success')
{
	$this->alert->add($this->lang->line('success'), 'success');
}
?>

<?=$this->alert->show();?>

<form action="<?=$this->url->current;?>" method="post">

	<table>
	
		<tr>
			<th><?=$this->lang->line('password');?>:</th>
			<td><input type="password" name="password"></td>
		</tr>
		
		<tr>
			<th><?=$this->lang->line('password_repeat');?>:</th>
			<td><input type="password" name="password_repeat"></td>
		</tr>
		
		<tr>
			<th>&nbsp;</th>
			<td><input type="submit" name="submit" value="<?=$this->lang->line('submit');?>"</td>
		</tr>
		
	</table>
	
</form>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>