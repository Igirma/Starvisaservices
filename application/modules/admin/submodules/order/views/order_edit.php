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
							<td><input type="text" name="order[order_number]" value="<?=(isset($data['order']['order_number']) ? $data['order']['order_number'] : '');?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('status');?></th>
							<td>
								<?php
								if(isset($data['order_status']) && count($data['order_status']) > 0){
									foreach($data['order_status'] as $order_status){
										?>
										<input type='radio' value='<?php echo $order_status['order_status_id'];?>' name='order[order_status_id]' <?php echo ((isset($data['order']['order_status_id']) && $data['order']['order_status_id'] == $order_status['order_status_id'])?"checked":"");?>><?php echo $order_status['name'];?>&nbsp;&nbsp;
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
							<td><input disabled type="text" name="order[payment_type]" value="<?=((isset($data['order']['payment_type']) && isset($data['payment_type']) && isset($data['payment_type'][$data['order']['payment_type']])) ? $data['payment_type'][$data['order']['payment_type']] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('delivery_type');?></th>
							<td><input disabled type="text" name="order[delivery]" value="<?=((isset($data['order']['delivery']))? $this->lang->line('delivery_type_'.$data['order']['delivery']) : '');?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('products');?></th>
							<td>
								<div class='white_block'>
								<?php
								if(isset($data['order_products']) && count($data['order_products']) > 0){
									?>
									<div>
										<table>
											<tr style="border-top:0 !important;">
												<td style="width:5% !important;"><b><?=$this->lang->line('ord');?></b></td>
												<td style="width:20% !important;"><b><?=$this->lang->line('product');?></b></td>
												<td style="width:15% !important;"><b><?=$this->lang->line('mail_code');?></b></td>	
												<td style="width:20% !important;"><b><?=$this->lang->line('price');?></b></td>
												<td style="width:15% !important;"><b><?=$this->lang->line('quantity');?></b></td>
												<td style="width:20% !important;"><b><?=$this->lang->line('total-price');?></b></td>
												<td style="width:5% !important;"><b></b></td>
											</tr>
											<?php
											foreach($data['order_products'] as $k => $product){
												$price = 0;
												if($product['offer_price'] != 0){
													$price = $product['offer_price'];
												}else{
													$price = $product['price'];
													if($product['discount_percent'] > 0) $price = $product['price'] - ($product['price'] * $product['discount_percent'] / 100);
														else if($product['discount_price'] > 0) $price = $product['price'] - $product['discount_price'];
												}
												if($product['has_vat'] == 1) $price = $price + ($price * $data['order']['vat_costs'] / 100);
						
												?>
												<tr>
													<td style="width:5% !important;"><?=$k+1;?></td>
													<td style="width:20% !important;"><?=$product['title'];?></td>
													<td style="width:15% !important;"><?=$product['articlenumber'];?></td>
													<td style="width:20% !important;">&euro; <?=formatPrice($price);?></td>
													<td style="width:15% !important;"><?=$product['quantity'];?></td>
													<td style="width:20% !important;">&euro; <?=formatPrice($price * $product['quantity']);?></td>
													<td style="width:5% !important;">
														<?=(permission('order', 'delete') ? '<a title="' . $this->lang->line('delete') . '" class="delete" href="' . SITE_URL . 'admin/order/delete_product/' . $product['order_products_id'] . '/' . $data['order']['order_id'] .'"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?>
													</td>											
												</tr>
												<?php
											}
											?>
											</table>
										</div>
									<?php
								}
								?>
								</div>
							</td>
						</tr>
						<tr>
							<th><?=$this->lang->line('discount_percent');?></th>
							<td><input readonly type="text" name="order[discount_percent]" value="<?=(isset($data['order']['discount_percent']) ? $data['order']['discount_percent'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('discount_price');?></th>
							<td><input readonly type="text" name="order[discount_price]" value="<?=(isset($data['order']['discount_price']) ? $data['order']['discount_price'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('discount_code');?></th>
							<td><input readonly type="text" name="order[discount_code]" value="<?=(isset($data['order']['discount_code']) ? $data['order']['discount_code'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('discount_code_percent');?></th>
							<td><input readonly type="text" name="order[discount_code_percent]" value="<?=(isset($data['order']['discount_code_percent']) ? $data['order']['discount_code_percent'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('discount_code_price');?></th>
							<td><input readonly type="text" name="order[discount_code_price]" value="<?=(isset($data['order']['discount_code_price']) ? $data['order']['discount_code_price'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('vat_costs');?></th>
							<td><input readonly type="text" name="order[vat_costs]" value="<?=(isset($data['order']['vat_costs']) ? $data['order']['vat_costs'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('transport');?></th>
							<td><input readonly type="text" name="order[transport]" value="<?=(isset($data['order']['transport']) ? $data['order']['transport'] : '');?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('total_price');?></th>
							<td><input readonly type="text" name="order[total_price]" value="<?=(isset($data['order']['total_price']) ? $data['order']['total_price'] : '');?>"></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('client');?></th>
							<td>
								<div class='white_block'>
								<?php
								if(isset($data['order_client']) && count($data['order_client']) > 0){
									foreach($data['order_client'] as $k => $client){
									?>
										<b><?php echo $client['firstname']. " ".$client['lastname']. ", ".$client['company'];?></b><br>
										<a href='mailto:<?php echo $client['email'];?>'><?php echo $client['email'];?></a><br>
										<?php echo $client['phone'];?><br><br>
										<?php echo $client['street']. " ".$client['housenumber'];?><br>
										<?php echo $client['postal']. " ".$client['city'];?><br>
										<?php echo $client['country'];?><br>
										<?php if($client['delivery_address'] == 1){ ?>
											<br><?=$this->lang->line('delivery_address');?><br>
											<?php echo $client['delivery_street']. " ".$client['delivery_housenumber'];?><br>
											<?php echo $client['delivery_postal']. " ".$client['delivery_city'];?><br>
											<?php echo $client['delivery_country'];?><br>											
										<?php } ?>
									<?php
									}
								}
								?>
								</div>
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