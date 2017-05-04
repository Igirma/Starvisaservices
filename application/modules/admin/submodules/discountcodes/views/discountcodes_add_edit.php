<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
	$data['discountcodes'] = $_POST['discountcodes'];
	$data['discountcodes']['discountcodes_date'] = strtotime($_POST['discountcodes']['discountcodes_date']);
}

?>
<?=validation_errors();?>

<div id="container">

	<div id="details">
	
		<form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="discountcodes_edit_add">
		
			<input type="hidden" name="discountcodes[discountcodes_id]" value="<?=(isset($data['discountcodes']['discountcodes_id']) && $data['discountcodes']['discountcodes_id'] != '' ? $data['discountcodes']['discountcodes_id'] : '');?>">

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
						<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-discountcodes"></div>
						<h1><?=$this->lang->line('header_edit');?></h1>
					
					</div>

				<?php endif; ?>
				
				<?php if($this->url->segment(2) == 'edit'): ?>

						<div class="header">
							<h1><?=$this->lang->line('header_add');?></h1>
							
						</div>

				<?php endif; ?>
			
			</div>
			
			<div class="column">
			
				<div class="subheader">

					<h2><?=$this->lang->line('discountcodes_info');?></h2>
					
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
							<th><?=$this->lang->line('content_title');?></th>
							<td><input type="text" name="discountcodes[title]" value="<?=(isset($data['discountcodes']['title']) ? $data['discountcodes']['title'] : '');?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('discountcodes_date');?></th>
							<td><input class="datepicker" readonly="readonly" type="text" name="discountcodes[discountcodes_date]" value="<?=(isset($data['discountcodes']['discountcodes_date']) ? date('d-m-Y', $data['discountcodes']['discountcodes_date']) : date('d-m-Y', time()));?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('discount_value');?></th>
							<td><input type="text" name="discountcodes[discount_value]" value="<?=(isset($data['discountcodes']['discount_value']) ? $data['discountcodes']['discount_value'] : '');?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('discount_percent');?></th>
							<td><input type="text" name="discountcodes[discount_percent]" value="<?=(isset($data['discountcodes']['discount_percent']) ? $data['discountcodes']['discount_percent'] : '');?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('code_nr');?></th>
							<td><input type="text" name="discountcodes[code_nr]" value="<?=(isset($data['discountcodes']['code_nr']) ? $data['discountcodes']['code_nr'] : '');?>"></td>
						</tr>
						
					</table>
				
				</div>
				
			</div>

			
			<?php if(!empty($data['codes'])): ?>
				<div class="column" id='overview' style='width:auto;'>
				
					<a id="anchor_media"></a>
			
					<div class="subheader">
				
						<h2><?=$this->lang->line('codes');?></h2>
						
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
						
						<table class='normal_format_table'>
							
								<thead>
								
									<tr>
									
										<td style="width: 8%;padding-left:2%;">Nr</td>
										<td class="text_left" style="width: 20%;">
											<p style="padding-left: 10px;"><?=$this->lang->line('code');?></p>
										</td>
										<td style="width: 10%">
											<p><?=$this->lang->line('active');?></p>
										</td>
										<td style="width: 10%;" align='center'>
											<p><?=$this->lang->line('delete');?></p>
										</td>
									</tr>							
								</thead>							
								<tbody>							
									<?php $i = 0; ?>
									<?php 
									foreach($data['codes'] as $code): ?>
										
										<tr>					
											<td style="width: 8%;padding-left:2%;"><?=++$i;?></td>
											<td class="text_left" style="width: 20%;">
												<?=$code['code'];?>
											</td>
											
											<td style="width: 10%">
											
												
												<p>
											
												<?php if(permission(CONTROLLER, 'edit')): ?>
											
													<?php if($code['active'] == 1): ?>
													
													<input style='width:auto !important;' type="radio" name="active[<?=$code['discountcodes_content_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
													
													<input style='width:auto !important;' type="radio" name="active[<?=$code['discountcodes_content_id'];?>]" value="0"><?=$this->lang->line('no');?>
													
													<?php else: ?>
													
													<input style='width:auto !important;' type="radio" name="active[<?=$code['discountcodes_content_id'];?>]" value="1"><?=$this->lang->line('yes');?>
													
													<input style='width:auto !important;' type="radio" name="active[<?=$code['discountcodes_content_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
													
													<?php endif; ?>
													
												<?php endif; ?>
												
												</p>
													
											</td>
											
											<td style="width: 10%" align='center'>
												
												<p><?=(permission('discountcodes', 'delete') ? '<a title="' . $this->lang->line('delete') . '" class="delete" href="' . $this->url->current . '/delete/' . $code['discountcodes_content_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?></p>
											</td>
										
										</tr>
										
									<?php endforeach; ?>
										
								</tbody>
							
							</table>
						
					</div>
				</div>
			<?php endif; ?>
			
		</form>
		
	</div>

</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>