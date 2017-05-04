<?php require_once ELEM_DIR . 'admin_header.php'; ?>
<?php
//debug($data);
?>
<div id="container">

	<div id="overview">
		
		<div class="orange_button">
			<div class="orange_button_left"></div>
			<div class="orange_button_con">
				<img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
				<div class="orange_button_space"></div>
				<a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER?>/add"><?=$this->lang->line('add_order');?></a>
			</div>
			<div class="orange_button_right"></div>
		</div>
		
		<div class="orange_button hidden">
			<div class="orange_button_left"></div>
			<div class="orange_button_con">
				<img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
				<div class="orange_button_space"></div>
				<a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER?>/import"><?=$this->lang->line('import');?></a>
			</div>
			<div class="orange_button_right"></div>
		</div>
		
		<div class="orange_button hidden">
			<div class="orange_button_left"></div>
			<div class="orange_button_con">
				<img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
				<div class="orange_button_space"></div>
				<a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER?>/export"><?=$this->lang->line('export');?></a>
			</div>
			<div class="orange_button_right"></div>
		</div>
			
		<input type="text" name="search" value="<?php if(isset($_SESSION['order_search'])) echo $_SESSION['order_search'];?>" class='order_search'>
			
		<div class="orange_button">
			<div class="orange_button_left"></div>
			<div class="orange_button_con">
				<a href="#" onclick="return false;" class='order_search_button'><?=$this->lang->line('search');?></a>
			</div>
			<div class="orange_button_right"></div>
		</div>
		<div class="clear"></div>
		<select name="" class='prods_nr'>
			<option value=''>Alle</option>
			<option value='50' <?php echo ((isset($_SESSION['order_nr']) && $_SESSION['order_nr'] == 50)?"selected":"");?>>50</option>
			<option value='100' <?php echo ((isset($_SESSION['order_nr']) && $_SESSION['order_nr'] == 100)?"selected":"");?>>100</option>
			<option value='150' <?php echo ((isset($_SESSION['order_nr']) && $_SESSION['order_nr'] == 150)?"selected":"");?>>150</option>
			<option value='200' <?php echo ((isset($_SESSION['order_nr']) && $_SESSION['order_nr'] == 200)?"selected":"");?>>200</option>
		</select>
		<select name="" class='prods_quarter'>
			<option value=''>Alle</option>
			<option value='1' <?php echo ((isset($_SESSION['order_quarter']) && $_SESSION['order_quarter'] == 1)?"selected":"");?>>1e <?=$this->lang->line('quarter');?></option>
			<option value='2' <?php echo ((isset($_SESSION['order_quarter']) && $_SESSION['order_quarter'] == 2)?"selected":"");?>>2e <?=$this->lang->line('quarter');?></option>
			<option value='3' <?php echo ((isset($_SESSION['order_quarter']) && $_SESSION['order_quarter'] == 3)?"selected":"");?>>3e <?=$this->lang->line('quarter');?></option>
			<option value='4' <?php echo ((isset($_SESSION['order_quarter']) && $_SESSION['order_quarter'] == 4)?"selected":"");?>>4e <?=$this->lang->line('quarter');?></option>
		</select>
		<select name="" class='prods_year'>
			<option value=''>Alle</option>
			<?php
				$year = date("Y", $data['first_month']);
				for($i = date("Y"); $i >= $year; $i--){
					?>
					<option value='<?php echo $i;?>' <?php echo ((isset($_SESSION['order_year']) && $_SESSION['order_year'] == $i)?"selected":"");?>><?=$i;?></option>
					<?php
				}
			?>
		</select>
		<div class="orange_button">
			<div class="orange_button_left"></div>
			<div class="orange_button_con">
				<a href="#" onclick="return false;" class='filter_button'><?=$this->lang->line('filter');?></a>
			</div>
			<div class="orange_button_right"></div>
		</div>
		<div class="clear"></div>
		
		<div class="column">

			<div class="pie header">
				<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-order"></div>
				<h1><?=$this->lang->line('overview_header');?></h1>
			
			</div>
		
			<form method="post" action="<?=$this->url->current;?>">

				<table>
				
					<thead>
					
						<tr>
							
							<th style="width: 3%;">
								Nr
							</th>
							<th class="text_left" style="width: 38%;">
								<div class="spacer"></div>
								<p style="padding-left: 10px;"><?=$this->lang->line('client');?></p>
							</th>
							<th class="text_left" style="width: 14%;">
								<div class="spacer"></div>
								<p style="padding-left: 10px;"><a style='color:#ffffff !important; text-decoration:underline' href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/change_order/'?>order_number"><?=$this->lang->line('order_title');?></a></p>
							</th>
							<th style="width: 10%;">
								<div class="spacer"></div>
								<p><a style='color:#ffffff !important; text-decoration:underline' href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/change_order/' ?>order_date"><?=$this->lang->line('order_date');?></a></p>
							</th>
							<th style="width: 12%;">
								<div class="spacer"></div>
								<p><a style='color:#ffffff !important; text-decoration:underline' href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/change_order/' ?>order_status_id"><?=$this->lang->line('status');?></a></p>
							</th>
							<th style="width: 12%;">
								<div class="spacer"></div>
								<p><a style='color:#ffffff !important; text-decoration:underline' href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/change_order/'?>total_price"><?=$this->lang->line('total_price');?></a></p>
							</th>
							<th style="width: 15%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('delete');?></p>
							</th>
						
						</tr>
					
					</thead>
					
					<tbody>
					
					<?php if(isset($data['order'])): ?>
						
						<?php $x = 1;
						$total = 0;
						?>
						
						<?php foreach($data['order'] as $order): ?>
						
						
						
						<tr>
							
							<td>
								<?=$x?>
								<?php $x++; ?>
							</td>
							
							<td class="text_left">
								
								<?php if(permission(CONTROLLER, 'edit')):?>
									<a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $order['order_id']?>" style="display:block;">
								<?php endif; ?>
								
								<div class="spacer"></div>
							
								<span style="line-height: 40px; padding-left: 10px;"><?=$order['order_client'][0]['firstname']. " ".$order['order_client'][0]['lastname']. ", ".$order['order_client'][0]['company'];?></span>

								<?php if(permission(CONTROLLER, 'edit')):?>
									</a>
								<?php endif; ?>

							</td>

							<td class="text_left">
								
								<div class="spacer"></div>
							
								<span style="line-height: 40px; padding-left: 10px;"><?=$order['order_number'];?></span>

							</td>

							<td>
								<div class="spacer"></div>
								<p>
								<?=date('d-m-Y H:i', ($order['date_created']));?>
								</p>
							</td>

							<td>
							
								<div class="spacer"></div>
								
								<p>
									<select class='status'>
										<?php 
										foreach($data['order_status'] as $id => $item){
											?>
											<option value='<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/change_status/' . $order['order_id'].'/'.$id?>' <?php echo (((isset($data['order_status']) && isset($data['order_status'][$order['order_status_id']])) && $order['order_status_id'] == $id)?"selected":"");?>><?php echo $item;?></option>
											<?php
										}
										?>
									</select>
								</p>
							
							</td>
							
							<td>
							
								<div class="spacer"></div>
								
								<p>
								&euro; <?=formatPrice($order['total_price']);?>	
								</p>
								
							</td>
							
							<td>
								<div class="spacer"></div>
								
								<p>
								
									<?=(permission('order', 'delete') ? '<a title="' . $this->lang->line('delete') . '" class="delete" href="' . SITE_URL . 'admin/order/delete/' . $order['order_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?>
									
								</p>
							</td>
						
						</tr>
						
						<?php 
						if($order['order_status_id'] == 2 || $order['order_status_id'] == 3 || $order['order_status_id'] == 4 || $order['order_status_id'] == 5 || $order['order_status_id'] == 6) 
							$total += $order['total_price'];
					
					endforeach; ?>
					
						<tr>
							
							<td>
								<p>
								&nbsp;
								</p>
							</td>
							
							<td class="text_left">
								<p>
								&nbsp;
								</p>
							</td>

							<td>
								<p>
								&nbsp;
								</p>
							</td>

							<td>
								<p>
								&nbsp;
								</p>
							</td>

							<td>
							
								<div class="spacer"></div>
								
								<p>
								<b><?php echo $this->lang->line('total');?></b>
								</p>
								
							</td>
							
							<td>
								<div class="spacer"></div>
								
								<p>
								<b>
									&euro; <?=formatPrice($total);?>	
								</b>
								</p>
							</td>
	
							<td>
								<div class="spacer"></div>
								<p>
								&nbsp;
								</p>
							</td>
												
						</tr>
						
					<?php endif; ?>
					
					</tbody>
				
				</table>
				
			</form>
		</div>
	</div>
</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>