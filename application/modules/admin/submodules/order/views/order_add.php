<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
	$data['order'] = $_POST['order'];
	$data['order']['date_created'] = strtotime($_POST['order']['date_created']);
}

?>
<?=validation_errors();?>

<div id="container">

	<div id="details">
	
		<form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="order_edit_add">
		
			<input type="hidden" name="order[order_id]" value="<?=(isset($data['order']['order_id']) && $data['order']['order_id'] != '' ? $data['order']['order_id'] : '');?>">

			<div class="menu_float">

				<input class="save" type="submit" name="save" value="<?=$this->lang->line('menu_save')?>">

				<input class="save_and_back" type="submit" name="save_and_back" value="<?=$this->lang->line('menu_save_and_back')?>">

				<div class="back_button">
					<a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER;?>" title="<?=$this->lang->line('back_to_overview')?>"><?=$this->lang->line('back_to_overview')?></a>
				</div>

			</div>
			
			<div class="column">
				
				<?php if($this->url->segment(2) == 'edit'): ?>

					<div class="header">
						<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-order"></div>
						<h1><?=$this->lang->line('header_edit');?></h1>
					
					</div>

				<?php endif; ?>
			
			</div>
			
			<div class="column">
			
				<div class="subheader">

					<h2><?=$this->lang->line('order_info');?></h2>
					
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
							<th><?=$this->lang->line('order_title');?></th>
							<td><input type="text" name="order[order_number]" value="<?=(isset($data['order']['order_number']) ? $data['order']['order_number'] : $data['order_number']);?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('status');?></th>
							<td>
								<?php
								if(isset($data['order_status']) && count($data['order_status']) > 0){
									foreach($data['order_status'] as $k => $order_status){
										?>
										<input type='radio' value='<?php echo $order_status['order_status_id'];?>' name='order[order_status_id]' <?php echo (((isset($data['order']['order_status_id']) && $data['order']['order_status_id'] == $order_status['order_status_id'] || (!isset($data['order']['order_status_id']) && $k == 1)))?"checked":"");?>><?php echo $order_status['name'];?>&nbsp;&nbsp;
										<?php
									}
								}
								?>
							</td>
						</tr>
						<tr>
							<th><?=$this->lang->line('date_created');?></th>
							<td><input readonly="readonly" type="text" name="order[date_created]" value="<?=(isset($data['order']['date_created']) ? date('d-m-Y H:i:s', ($data['order']['date_created'])) : date('d-m-Y H:i:s', time()));?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('payment_type');?></th>
							<td>
								<?php
								if(isset($data['payment']) && count($data['payment']) > 0){
									foreach($data['payment'] as $k => $payment){
										?>
										<input type='radio' value='<?php echo $payment['payment_type_id'];?>' name='order[payment_type]' <?php echo (((isset($data['order']['payment_type']) && $data['order']['payment_type'] == $payment['payment_type_id']) || (!isset($data['order']['payment_type']) && $k == 0))?"checked":"");?>><?php echo $payment['name'];?>&nbsp;&nbsp;
										<?php
									}
								}
								?>
							</td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('delivery_type');?></th>
							<td>
								<input type='radio' value='0' name='order[delivery]' <?php echo (((isset($data['order']['delivery']) && $data['order']['delivery'] == 0))?"checked":"");?>><?php echo $this->lang->line('delivery_type_0');?>&nbsp;&nbsp;
								<input type='radio' value='1' name='order[delivery]' <?php echo (((isset($data['order']['delivery']) && $data['order']['delivery'] == 1) || (!isset($data['order']['deliverydelivery'])))?"checked":"");?>><?php echo $this->lang->line('delivery_type_1');?>&nbsp;&nbsp;		
							</td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('products');?></th>
							<td>
								<table>
									<tr class='option_row'>
										<th>
											<select name='order[products][]'>
												<?php
												if(isset($data['products']) && $data['products'] && count($data['products']) > 0){
													foreach($data['products'] as $product){
														?>
														<option value='<?php echo $product['product_id'];?>'><?php echo $product['title'];?></option>
														<?php
													}
												}
												?>
												
											</select>
										</th>
										<td>
											<input type="text" name="order[aantal][]" value="1" class="product_options_input">
											<span class='spacer_delete'></span>
											<a href='#'  class="order_product_add_delete" onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>
										</td>
									</tr>									
									<tr>
										<th colspan=2>
											<div id="overview" class="orange_button">
												<div class="orange_button_left"></div>
												<div class="orange_button_con">
													<img src="http://www.beeldbelovend.nl/newadmin/application/elements/img_admin/plus.png">
													<div class="orange_button_space"></div>
													<a class="font_white order_product_add" onclick="return false;" href="#"><?=$this->lang->line('add_product');?></a>
												</div>
												<div class="orange_button_right"></div>
											</div>
										</th>
										
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<th><?=$this->lang->line('discount_percent');?></th>
							<td><input type="text" name="order[discount_percent]" value="<?=(isset($data['order']['discount_percent']) ? $data['order']['discount_percent'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('discount_price');?></th>
							<td><input type="text" name="order[discount_price]" value="<?=(isset($data['order']['discount_price']) ? $data['order']['discount_price'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('vat_costs');?></th>
							<td><input type="text" name="order[vat_costs]" value="<?=(isset($data['order']['vat_costs']) ? $data['order']['vat_costs'] : $data['vat_costs']);?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('transport');?></th>
							<td><input type="text" name="order[transport]" value="<?=(isset($data['order']['transport']) ? $data['order']['transport'] : '');?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('total_price');?></th>
							<td><input type="text" name="order[total_price]" value="<?=(isset($data['order']['total_price']) ? $data['order']['total_price'] : '');?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('client');?></th>
							<td>
								<select name='order[client]'>
									<?php
									if(isset($data['client']) && count($data['client']) > 0){
										foreach($data['client'] as $client){
											?>
											<option value='<?php echo $client['client_id'];?>' <?php echo ((isset($data['order']['client']) && $data['order']['client'] == $client['client_id'])?"selected":"");?>><?php echo $client['firstname'].' '.$client['lastname'].', '.$client['company'];?></option>
											<?php
										}
									}
									?>
								</select>
							</td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('comments');?></th>
							<td><textarea name="order[comments]" class="editor"><?=(isset($data['order']['comments']) ? $data['order']['comments'] : '');?></textarea></td>
						</tr>
						
					</table>
				
				</div>
				
			</div>

			<div class="column hidden">
			
				<div class="subheader">

					<h2><?=$this->lang->line('documents');?></h2>
					
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
				
					<?php if(!empty($data['docs'])): ?>
					
						<div class="docs">
						
							<ul>

						<?php foreach($data['docs'] as $doc): ?>
						
							<?php $ext = end(explode('.', $doc['filename'])); ?>
							
								<li>	
									<a href="<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER;?>/docs/<?=$doc['filename'];?>"><?=str_shorten($doc['filename'], 15);?>
										<div style="position: absolute; top: 0; left: 0;" class="sprite_ex sprite-<?=$ext;?>"></div>
									</a>
									<a class="delete" style="display: none; position: absolute; right: 3px; top: 7px;" href="<?=SITE_URL . 'admin/' . CONTROLLER . '/delete_media/' . $doc['media_id'] . '/' . $doc['table_id'] . '/' . $this->url->segment(4);?>">
										<img src="<?=SITE_URL . ELEM_DIR;?>img_admin/delete.png">
									</a>
								</li>

						<?php endforeach; ?>

							</ul>						
						
						</div>
						
					<?php endif; ?>
				
					<table>
						
						<tr>
						
							<th>
								<?=$this->lang->line('new_document');?>
							</th>
							<td>
							
								<div class="file_container">
									<input type="text" readonly="readonly" class="file_replace_text">
									<input type="button" class="file_replace" value="<?=$this->lang->line('browse');?>">
									<input type="submit" name="save" value="<?=$this->lang->line('upload');?>">
									<input type="file" name="docs[]" multiple="multiple" class="file_hack">
								</div>
							
							</td>
						
						</tr>
					
					</table>
				
				</div>
				
			</div>
			
		</form>		

	</div>

</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>