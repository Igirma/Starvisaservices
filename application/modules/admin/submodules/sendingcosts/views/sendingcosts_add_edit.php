<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
	$data['sendingcosts'] = $_POST['sendingcosts'];
}

//debug($data);

?>
<?=validation_errors();?>

<div id="container">

	<div id="details">
	
		<form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="sendingcosts_edit_add">
		
			<input type="hidden" name="sendingcosts[sendingcosts_id]" value="<?=(isset($data['sendingcosts']['sendingcosts_id']) && $data['sendingcosts']['sendingcosts_id'] != '' ? $data['sendingcosts']['sendingcosts_id'] : '');?>">

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
						<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-sendingcosts"></div>
						<h1><?=$this->lang->line('header_edit');?></h1>
					
					</div>

				<?php endif; ?>
				
				<?php if($this->url->segment(2) == 'edit'): ?>

						<div class="pie header">
							<div class="sprite sprite-sendingcosts" style="position: absolute; left: 11px; top: 12px;"></div>
							<h1><?=$this->lang->line('header_add');?></h1>
						
						</div>

				<?php endif; ?>
			
			</div>
			
			<div class="column">
			
				<div class="subheader">

					<h2><?=$this->lang->line('sendingcosts_info');?></h2>
					
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
				
					<table class='sendingcosts-table'>

						<tr>
							<td><?=$this->lang->line('country');?></td>
							<td><?=$this->lang->line('value');?></td>
							<td><?=$this->lang->line('discount_value');?></td>
							<td><?=$this->lang->line('discount_type');?></td>
						</tr>
						<?php if($this->url->segment(2) == 'edit'){ ?>
							
							<?php foreach($data['sendingcosts'] as $k => $cost){ ?>
								<tr class='option_row'>
									<td>
										<span class='nummer hidden'><?php echo $cost['staffel_id'];?></span>
										<select name='sendingcosts[country_id][<?php echo $cost['staffel_id'];?>]' class='country_select <?php echo (($k > 0)?"hidden":"");?>'>
										<?php 
										if(isset($data['countries']) && $data['countries'] && count($data['countries']) > 0){
											foreach($data['countries'] as $country){
												?>
												<option value='<?php echo $country['country_id'];?>' <?=((isset($cost['country_id']) && $cost['country_id'] == $country['country_id']) ? 'selected' : '');?>><?php echo $country['name'];?></option>
												<?php
											}
										}
										?>
										</select>
									</td>							
									<td>
										<input type="text" name="sendingcosts[discount_top_value][<?php echo $cost['staffel_id'];?>]" value="<?=(isset($cost['discount_top_value']) ? $cost['discount_top_value'] : '');?>">
									</td>
									<td>
										<input type="text" name="sendingcosts[discount_value][<?php echo $cost['staffel_id'];?>]" value="<?=(isset($cost['discount_value']) ? $cost['discount_value'] : '');?>">
									</td>
									<td>
										<?php 
										if(isset($data['discount_type']) && $data['discount_type'] && count($data['discount_type']) > 0){
											foreach($data['discount_type'] as $k => $discount_type){
												?>
												<input type="radio" name="sendingcosts[discount_type_id][<?php echo $cost['staffel_id'];?>]" value="<?php echo $discount_type['discount_type_id'];?>" <?=(((isset($cost['discount_type_id']) && $cost['discount_type_id'] == $discount_type['discount_type_id']) || (!isset($cost['discount_type']) && $k == 0)) ? 'checked' : '');?>>&nbsp;<?php echo $discount_type['discount_description'];?> &nbsp;&nbsp;
												<?php
											}
										}
										?>
										<!--
										<span class='spacer_delete'></span>
										<a href='#'  class="sendingcosts_delete" style='float:right' onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>		
										-->
									</td>
									
								</tr>
							<?php } ?>
						
						<?php }else{ ?>
						<tr class='option_row'>
							<td>
								<span class='nummer hidden'>1</span>
								<select name='sendingcosts[country_id][1]' class='country_select'>
								<?php 
								if(isset($data['countries']) && $data['countries'] && count($data['countries']) > 0){
									foreach($data['countries'] as $country){
										?>
										<option value='<?php echo $country['country_id'];?>' <?=((isset($data['sendingcosts']['country_id']) && $data['sendingcosts']['country_id'] == $country['country_id']) ? 'selected' : '');?>><?php echo $country['name'];?></option>
										<?php
									}
								}
								?>
								</select>
							</td>							
							<td>
								<input type="text" name="sendingcosts[discount_top_value][1]" value="<?=(isset($data['sendingcosts']['discount_top_value']) ? $data['sendingcosts']['discount_top_value'] : '');?>">
							</td>
							<td>
								<input type="text" name="sendingcosts[discount_value][1]" value="<?=(isset($data['sendingcosts']['discount_value']) ? $data['sendingcosts']['discount_value'] : '');?>">
							</td>
							<td>
								<?php 
								if(isset($data['discount_type']) && $data['discount_type'] && count($data['discount_type']) > 0){
									foreach($data['discount_type'] as $k => $discount_type){
										?>
										<input type="radio" name="sendingcosts[discount_type_id][1]" value="<?php echo $discount_type['discount_type_id'];?>" <?=(((isset($data['sendingcosts']['discount_type_id']) && $data['sendingcosts']['discount_type_id'] == $discount_type['discount_type_id']) || (!isset($data['sendingcosts']['discount_type']) && $k == 0)) ? 'checked' : '');?>>&nbsp;<?php echo $discount_type['discount_description'];?> &nbsp;&nbsp;
										<?php
									}
								}
								?>
								<!--
								<span class='spacer_delete'></span>
								<a href='#'  class="sendingcosts_delete" style='float:right' onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>		
								-->
							</td>
							
						</tr>
						<?php } ?>
						<tr class='option_row_add hidden'>
							<td>
								<span class='nummer hidden'>0</span>
								<select name='sendingcosts[country_id][0]' class='country_select hidden'>
								<?php 
								if(isset($data['countries']) && $data['countries'] && count($data['countries']) > 0){
									foreach($data['countries'] as $country){
										?>
										<option value='<?php echo $country['country_id'];?>' ><?php echo $country['name'];?></option>
										<?php
									}
								}
								?>
								</select>
							</td>							
							<td>
								<input type="text" name="sendingcosts[discount_top_value][0]" value="">
							</td>
							<td>
								<input type="text" name="sendingcosts[discount_value][0]" value="">
							</td>
							<td>
								<?php 
								if(isset($data['discount_type']) && $data['discount_type'] && count($data['discount_type']) > 0){
									foreach($data['discount_type'] as $k => $discount_type){
										?>
										<input type="radio" name="sendingcosts[discount_type_id][0]" value="<?php echo $discount_type['discount_type_id'];?>" <?php echo (($k == 0)?'checked':'');?>>&nbsp;<?php echo $discount_type['discount_description'];?> &nbsp;&nbsp;
										<?php
									}
								}
								?>
								<!--
								<span class='spacer_delete'></span>
								<a href='#'  class="sendingcosts_delete" style='float:right' onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>		
								-->
							</td>
							
						</tr>
						
						
						<tr>
							<td colspan='2'>
								<div id="overview" class="orange_button">
									<div class="orange_button_left"></div>
									<div class="orange_button_con">
										<img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png">
										<div class="orange_button_space"></div>
										<a class="font_white sendingcosts_add" onclick="return false;" href="#"><?=$this->lang->line('header_add');?></a>
									</div>
									<div class="orange_button_right"></div>
								</div>
							</td>
						</tr>
					</table>
				
				</div>
				
			</div>

	</div>

</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>