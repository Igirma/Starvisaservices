<?php require_once(ELEM_DIR . 'admin_header.php');

if(!empty($_POST))
{
	$data = $_POST;
}

?>

<?=validation_errors();?>

<div id="container">

	<div id="overview">
	
		<div class="column">
			
			<div class="pie header">
				
				<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-settings"></div>
				<h1><?=$this->lang->line('overview_rights');?></h1>
				
			</div>
			
			<form method="post" action="<?=$this->url->current;?>">

				<table id="overview">
				
					<thead>
					
						<tr>
						
							<th style="width: 5%;">#ID</th>
							
							<th style="width: 45%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('right_name');?></p>
							</th>
							
							<th style="width: 25%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('count_users');?></p>
							</th>
							
							<th style="width: 25%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('delete');?></p>
							</th>
							
						</tr>
						
					</thead>
				
					<tbody>
						
						<?php foreach($data as $right): ?>
						
						<tr>
							
							<td>
								
								<?=$right['rights_id']?>
								
							</td>
							
							<td>
								<div class="spacer"></div>
								<p><?=$right['name']?></p>
								
							</td>
							
							<td>
								<div class="spacer"></div>
								<p><?=count($right['users'])?></p>
								
							</td>
							
							<td>
								
								<div class="spacer"></div>
								
								<?php if(permission('rights', 'delete') && count($right['users']) < 1): ?>
								
									<p><a title="<?=$this->lang->line('delete')?>" href=" <?=SITE_URL . 'admin/delete_rights/' . $right['rights_id']?> "><span style="position: absolute;" class="sprite-black sprite-black-delete"></span></a></p>
								
								<?php endif; ?>
								
								<?php if(permission('rights', 'delete') && count($right['users']) > 0): ?>
									
									<p><?=$this->lang->line('users_connected');?></p>
									
								<?php endif; ?>
								
							</td>
							
						</tr>
						
						<?php endforeach; ?>
						
					</tbody>
				
				</table>

			</form>
			
		</div>
		
	</div>
	
</div>

<div id="container">

	<div id="details">

		<form method="post" action="<?=$this->url->current;?>">
		
			<div class="menu_float">

				<input class="save" type="submit" name="save" value="<?=$this->lang->line('right_save')?>">

				<input class="save_and_back" type="submit" name="save_and_back" value="<?=$this->lang->line('menu_save_and_back')?>">
				
				<div class="back_button">
					<a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER;?>" title="<?=$this->lang->line('back_to_overview')?>"><?=$this->lang->line('back_to_overview')?></a>
				</div>

			</div>
			
			<div class="column">
				
				<div class="pie subheader">
					
					<h2><?=$this->lang->line('add_right');?></h2>
					
					<div class="options">
					
						<ul>
						
							<li>
								<a class="info" title="Info">
									<div class="sprite sprite-info"></div>
								</a>
							</li>
							<li>
								<div class="spacer"></div>
							</li>
							<li>
								<a class="toggle min">
									<div class="sprite sprite-min"></div>
								</a>
							</li>
						
						</ul>
						
					</div>
					
				</div>
			
				<div class="subcolumn">
				
					<table>
						
						<tr>
							<th><?=$this->lang->line('right_name');?></th>
							<td><input type="text" name="name"></td>
						</tr>

					</table>
				
				</div>
			
			</div>
			
		</form>
	
	</div>

</div>



<?php require_once(ELEM_DIR . 'admin_footer.php'); ?>