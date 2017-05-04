<?php require_once ELEM_DIR . 'admin_header.php'; ?>
<?php $i = 0; ?>

<div id="container">
	
	<div id="overview">
		
		<div class="orange_button">
			<div class="orange_button_left"></div>
			<div class="orange_button_con">
				<img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
				<div class="orange_button_space"></div>
				<a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER;?>/add">Taal toevoegen</a>
			</div>
			<div class="orange_button_right"></div>
		</div>
		
		<div class="clear"></div>
		
		<div class="column">
		
			<div class="pie header">
				
				<div style="position: absolute; left: 11px; top: 14px; width: 16px; height: 11px; background: url(<?=SITE_URL . ELEM_DIR . 'img_admin/' . CONTROLLER . '.png'?>)" ></div>
				<h1><?=$this->lang->line('overview_languages')?></h1>
			
			</div>

			<div class="sub_culumn">
				
				<div class="pie sub_header">
					
					<table>

						<thead style="text-align: left;">
						
							<tr>
							
								<th style="width: 5%;">Nr</th>
								
								
								<th class="text_left" style="width: 50%;">
									<div class="spacer"></div>
									<p style="padding-left: 10px;"><?=$this->lang->line('language_title');?></p>
								</th>
								
								<th style="width: 5%;">
									<div class="spacer"></div>
									<p>Code</p>
								</th>
								
								<th style="width: 5%;">
									<div class="spacer"></div>
									<p><?=$this->lang->line('delete');?></p>
								</th>
								
							</tr>
							
						</thead>
						
						<tbody>

							<?php foreach($data as $language): ?>

								<tr>
								
									<td><?=++$i;?></td>
									
									<td class="text_left">
										
										<?php if(permission(CONTROLLER, 'edit')):?>
											<a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $language['language_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
										<?php endif; ?>
										
										<div class="spacer"></div>
									
										<span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($language['name'], 30);?></span>

										<?php if(permission(CONTROLLER, 'edit')):?>
											</a>
										<?php endif; ?>
										
									</td>
									
									<td>
										<div class="spacer"></div>
										<p><?=$language['code'];?></p>
									</td>
									
									<td>
										<div class="spacer"></div>
										<p><?=($language['deletable'] == 1 ? '<a class="delete" title="' . $this->lang->line('delete') . '" href="' . SITE_URL . 'admin/languages/delete/' . $language['language_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?></p>
									</td>
									
								</tr>
								
							<?php endforeach; ?>
							
						</tbody>
						
					</table>
					
				</div>
				
			</div>
			
		</div>
	
	</div>
	
</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>