<?php require_once(ELEM_DIR . 'admin_header.php');

if(!empty($_POST))
{
	$data['user'] = $_POST;
}

?>

<?=validation_errors();?>

<div id="container">

	<div id="details">
	
		<form method="post" action="<?=$this->url->current?>">
		
			<div class="menu_float">

				<input class="save" type="submit" name="save" value="<?=$this->lang->line('save_user')?>">

				<input class="save_and_back" type="submit" name="save_and_back" value="<?=$this->lang->line('menu_save_and_back')?>">
				
				<div class="back_button">
					<a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER;?>" title="<?=$this->lang->line('back')?>"><?=$this->lang->line('back_to_overview')?></a>
				</div>

			</div>
			
			<div class="column">
			
				<?php if($this->url->segment(2) == 'edit'): ?>

						<div class="header">
						
							<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-users"></div>
							<h1><?=$this->lang->line('edit_user')?></h1>
						
						</div>

				<?php endif; ?>
				
				<?php if($this->url->segment(2) == 'add'): ?>

						<div class="header">
						
							<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-users"></div>
							<h1><?=$this->lang->line('add_user')?></h1>
						
						</div>

				<?php endif; ?>

			
			</div>
			
			<div class="column">
				
				<div class="subheader">
					<h2><?=$this->lang->line('settings')?></h2>
				</div>
				
				<div class="subcolumn">

					<input type="hidden" name="user_id" value="<?=(isset($data['user']['user_id']) ? $data['user']['user_id'] : '');?>">
					<table>

						<tr>
							<th><?=$this->lang->line('rights')?></th>
							<td>
								<select name="rights_id">
								
								<?php foreach($data['all_rights'] as $right): ?>

									<option <?=(isset($data['user']['rights_id']) && $right['rights_id'] == $data['user']['rights_id'] ? 'selected="selected"' : '')?> value="<?=$right['rights_id']?>"><?=$right['name']?></option>
									
								<?php endforeach; ?>
								
								</select>
							</td>
						</tr>
						<tr>
							<th><?=$this->lang->line('username')?></th>
							<td><input type="text" name="username" autocomplete="off" value="<?=(isset($data['user']['username']) ? $data['user']['username'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('email')?></th>
							<td><input type="text" name="email" autocomplete="off" value="<?=(isset($data['user']['email']) ? $data['user']['email'] : '');?>"></td>
						</tr>

						<tr>
							<th><?=$this->lang->line('firstname')?></th>
							<td><input type="text" name="firstname" autocomplete="off" value="<?=(isset($data['user']['firstname']) ? $data['user']['firstname'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('infix')?></th>
							<td><input type="text" name="infix" autocomplete="off" value="<?=(isset($data['user']['infix']) ? $data['user']['infix'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('lastname')?></th>
							<td><input type="text" name="lastname" autocomplete="off" value="<?=(isset($data['user']['lastname']) ? $data['user']['lastname'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('password')?></th>
							<td><input style="width: 97%;" type="password" autocomplete="off" name="password" value=""></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('password_check')?></th>
							<td><input style="width: 97%;" type="password" autocomplete="off" name="password_check" value=""></td>
						</tr>
						
					</table>
					
				</div>
				
			</div>
		
		</form>
	
	</div>

</div>
<?php require_once(ELEM_DIR . 'admin_footer.php'); ?>