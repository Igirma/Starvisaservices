<?php

require_once(ELEM_DIR . 'admin_header.php');

?>

<div id="container">

	<div id="overview">
		
		<div class="orange_button">
			<div class="orange_button_left"></div>
			<div class="orange_button_con">
				<img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
				<div class="orange_button_space"></div>
				<a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER?>/add"><?=$this->lang->line('add_user');?></a>
			</div>
			<div class="orange_button_right"></div>
		</div>
		
		<div class="clear"></div>
		
		<div class="column">
		
			<div class="pie header">
				
				<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-users"></div>
				<h1><?=$this->lang->line('overview_header');?></h1>
			
			</div>
		
			<form method="post" action="<?=$this->url->current;?>">

				<table id="overview">
				
					<thead>
					
						<tr>
						
							<th style="width: 3%;">Nr</th>
							
							<th style="width: 28%;" class="text_left">
								<div class="spacer"></div>
								<p style="padding-left: 10px;"><?=$this->lang->line('user_name');?></p>
							</th>
							
							<th style="width: 15%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('language');?></p>
							</th>
							
							<th style="width: 15%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('rank');?></p>
							</th>
							
							<th style="width: 15%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('email');?></p>
							</th>
							
							<th style="width: 12%;">
								<div class="spacer"></div>
								<p>Actief</p>
							</th>
							
							<th style="width: 12%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('delete');?></p>
							</th>
							
						</tr>
						
					</thead>

					<tbody class="">
						
						<?php $i = 0; ?>
						
						<?php foreach($data['users'] as $user): ?>
						
						<tr>
						
							<td>
								<?=++$i;?>
							</td>
							
							<td class="text_left">
								
								<?php if(permission(CONTROLLER, 'edit')):?>
									<a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $user['user_id']?>" style="display:block;">
								<?php endif; ?>
								
								<div class="spacer"></div>
							
								<span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($user['firstname'] . ' ' . $user['infix'] . ' ' . $user['lastname'], 50);?></span>

								<?php if(permission(CONTROLLER, 'edit')):?>
									</a>
								<?php endif; ?>

							</td>
							
							<td>
							
								<div class="spacer"></div>
							
								<p>
								
									<?php foreach($data['languages'] as $language): ?>
											
											<?php
											$check = false;
											
											foreach($user['languages'] as $lang)
											{
												if($lang['language_id'] == $language['language_id'])
												{
													$check = true;
												}
											}
											?>

											<a title="<?=$language['name']?>" class="no_underline language<?=($check == true ? '' : '_grey')?> sprite_<?=$language['code']?>" href="<?=SITE_URL . 'admin/users/permission/' . $user['user_id'] . '/' . $language['language_id'] . '/' . ($check == true ? 'off' : 'on')?>"></a>

									<?php endforeach; ?>
								
								</p>
							
							</td>
							
							<td>
								<div class="spacer"></div>
								<p><?=$user['rights']['name']?></p>
							</td>
							
							<td>
								<div class="spacer"></div>
								<p><a title="<?=$this->lang->line('mailto')?>" href="mailto:<?=$user['email']?>"><?=$user['email']?></a></p>
							</td>
							
							<td>
								
								<div class="spacer"></div>
								
								<?php if($user['active'] == 1): ?>
								
								<p><input type="radio" name="active[<?=$user['user_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
								
								<input type="radio" name="active[<?=$user['user_id'];?>]" value="0"><?=$this->lang->line('no');?></p>
								
								<?php else: ?>
								
								<p><input type="radio" name="active[<?=$user['user_id'];?>]" value="1"><?=$this->lang->line('yes');?>
								
								<input type="radio" name="active[<?=$user['user_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?></p>
								
								<?php endif; ?>
								
							</td>
							
							<td>
							
							<?php if(permission('users', 'delete')): ?>
									
									<div class="spacer"></div>
									
									<?php if($user['deletable'] == 1): ?>
									
									<p><a title="<?=$this->lang->line('delete')?>" class="delete" href="<?=SITE_URL;?>admin/users/delete/<?=$user['user_id'];?>"><span style="position: absolute;" class="sprite-black sprite-black-delete"></span></a></p>
									
									<?php endif; ?>

							<?php endif; ?>
							
							</td>
							
						</tr>
						
						<?php endforeach; ?>
						
					</tbody>
				
				</table>

			</form>
			
		</div>
		
	</div>
	
<div>

<?php require_once(ELEM_DIR . 'admin_footer.php'); ?>