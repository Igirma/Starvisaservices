<?php require_once ELEM_DIR . 'admin_header.php'; 

//debug($data);
?>

<div id="container">

	<div id="overview">
	
		<div class="orange_button">
			<div class="orange_button_left"></div>
			<div class="orange_button_con">
				<?=($this->url->segment(2) != 'archive' ? '<a href="' . SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/archive">' . $this->lang->line('overview_archive_header') . '</a>' : '<a href="' . SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '">' . $this->lang->line('overview_header') . '</a>');?>
			</div>
			<div class="orange_button_right"></div>
		</div>
		
		<div class="clear"></div>
	
		<div class="column">
		
			<div class="pie header">
				<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-forms"></div>
				<h1><?=($this->url->segment(2) == 'archive' ? $this->lang->line('overview_archive_header') : $this->lang->line('overview_header'));?></h1>
			
			</div>
		
			<form method="post" action="<?=$this->url->current;?>">

				<table>
				
					<thead>
					
						<tr>
						
							<th style="width: 3%;">Nr</th>
							<th class="text_left" style="width: 37%;">
								<div class="spacer"></div>
								<p style="padding-left: 10px;"><?=$this->lang->line('name');?></p>
							</th>
							<th class="text_left" style="width: 25%;">
								<div class="spacer"></div>
								<p style="padding-left: 10px;"><?=$this->lang->line('email');?></p>
							</th>
							<th style="width: 15%">
								<div class="spacer"></div>
								<p><?=$this->lang->line('date');?></p>
							</th>
							<th class="sortable" style="width: 10%">
								<div class="spacer"></div>
								<p>Type</p>
							</th>
							<th style="width: 10%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('delete');?></p>
							</th>
						
						</tr>
					
					</thead>
					
					<tbody>
					
						<?php $i = 0; ?>
						
						<?php if(!empty($data)): ?>
							<?php foreach($data as $form): ?>
							
							<tr <?=($form['read'] == 0 ? 'style="font-weight: 700;"' : 'style="font-weight: 300;"')?>>
							
								<td><?=++$i;?></td>
								
								<td class="text_left">
									
									<?php if(permission(CONTROLLER, 'edit')):?>
										<a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $form['form_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
									<?php endif; ?>
									
									<div class="spacer"></div>
								
									<span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($form['firstname'] . $form['infix'] . $form['lastname'], 80);?></span>

									<?php if(permission(CONTROLLER, 'edit')):?>
										</a>
									<?php endif; ?>
									
								</td>
								
								<td class="text_left">
									<div class="spacer"></div>
									<span style="line-height: 40px; padding-left: 10px;"><a title="<?=$this->lang->line('mailto')?>" href="mailto:<?=$form['email'];?>"><?=$form['email'];?></a></span>
								</td>
								
								<td>
									<div class="spacer"></div>
									
									<p>
										<?=date('d-m-Y', $form['date_added']);?>
									</p>
								</td>
								
								<td>
									<div class="spacer"></div>
									
									<p>
										<?=ucfirst($form['type']);?>
									</p>
								</td>

								<td>
									<div class="spacer"></div>
								
									<p><?=(permission('forms', 'delete') ? '<a title="' . $this->lang->line('delete') . '" class="delete" href="' . SITE_URL . 'admin/forms/delete/' . $form['form_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?></p>
								</td>
							
							</tr>
							
							<?php endforeach; ?>
						<?php endif; ?>
					
					</tbody>
				
				</table>
				
			</form>
		</div>
	</div>
</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>