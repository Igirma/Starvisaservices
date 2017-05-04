<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
	$data['order_mails'] = $_POST['order_mails'];
}

//debug($data);

?>
<?=validation_errors();?>

<div id="container">

	<div id="details">
	
		<form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="order_mails_edit_add">
		
			<input type="hidden" name="order_mails[order_mails_id]" value="<?=(isset($data['order_mails']['order_mails_id']) && $data['order_mails']['order_mails_id'] != '' ? $data['order_mails']['order_mails_id'] : '');?>">

			<div class="menu_float">

				<input class="save" type="submit" name="save" value="<?=$this->lang->line('menu_save')?>">

				<input class="save_and_back" type="submit" name="save_and_back" value="<?=$this->lang->line('menu_save_and_back')?>">
				
				<div class="back_button">
					<a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER;?>" title="<?=$this->lang->line('back_to_overview')?>"><?=$this->lang->line('back_to_overview')?></a>
				</div>

			</div>
			
			<div class="column">
				
				<?php if($this->url->segment(2) == 'add'): ?>

					<div class="header">
						<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-forms"></div>
						<h1><?=$this->lang->line('header_edit');?></h1>
					
					</div>

				<?php endif; ?>
				
				<?php if($this->url->segment(2) == 'edit'): ?>

						<div class="header">
							<input type="hidden" name="order_mails[language][code]" value="<?=$data['order_mails']['language']['code']?>" />
							<div style="position: absolute; left: 11px; top: 15px;" class="language sprite_<?=$data['order_mails']['language']['code']?>"></div>
							<h1><?=$this->lang->line('header_add');?></h1>
							
							<div class="add_edit_languages">
								
								<?php foreach($data['languages'] as $language): ?>

										<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
									
										<a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$data['order_mails']['order_mails_id']?>/<?=$language['language_id'];?>">
											<span class="padding_extra_languages language sprite_<?=$language['code']?>"></span>
										</a>
										
										<?php endif; ?>

								<?php endforeach; ?>
								
							</div>
							
						</div>

				<?php endif; ?>
			
			</div>
			
			<div class="column">
			
				<div class="subheader">

					<h2><?=$this->lang->line('order_mails_info');?></h2>
					
					<div class="options">
					
						<ul>
						
							<li>
								<a class="info" title="Info">
									<div style="position: relative; top: 15px;" class="sprite sprite-info"></div>
								</a>
							</li>
							<li>
								<div class="spacer"></div>
							</li>
							<li>
								<a class="toggle min">
									<div style="position: relative; top: 15px; left: -1px;" class="sprite sprite-min"></div>
								</a>
							</li>
						
						</ul>
						
					</div>
				
				</div>
				
				<div class="subcolumn">
				
					<table>

						<tr>
							<th><?=$this->lang->line('order_status');?></th>
							<td>
								<p>
									<b>
										<input type='hidden' name='order_mails[order_status_id]' value ='<?php echo $data['order_mails']['order_status_id'];?>'>
										<?php echo ((isset($data['order_status']) && isset($data['order_status'][$data['order_mails']['order_status_id']]))?$data['order_status'][$data['order_mails']['order_status_id']]:"");?>
									</b>
								</p>
							</td>
						</tr>

						<tr>
							<th><?=$this->lang->line('client_fromname');?></th>
							<td><input type="text" name="order_mails[client_fromname]" value="<?=(isset($data['order_mails']['client_fromname']) ? $data['order_mails']['client_fromname'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('client_subject');?></th>
							<td><input type="text" name="order_mails[client_subject]" value="<?=(isset($data['order_mails']['client_subject']) ? $data['order_mails']['client_subject'] : '');?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('client_content');?></th>
							<td><textarea name="order_mails[client_content]" class="editor"><?=(isset($data['order_mails']['client_content']) ? $data['order_mails']['client_content'] : '');?></textarea></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('admin_fromname');?></th>
							<td><input type="text" name="order_mails[admin_fromname]" value="<?=(isset($data['order_mails']['admin_fromname']) ? $data['order_mails']['admin_fromname'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('admin_subject');?></th>
							<td><input type="text" name="order_mails[admin_subject]" value="<?=(isset($data['order_mails']['admin_subject']) ? $data['order_mails']['admin_subject'] : '');?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('admin_content');?></th>
							<td><textarea name="order_mails[admin_content]" class="editor"><?=(isset($data['order_mails']['admin_content']) ? $data['order_mails']['admin_content'] : '');?></textarea></td>
						</tr>
						
					
					</table>
				
				</div>
				
			</div>

	</div>

</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>