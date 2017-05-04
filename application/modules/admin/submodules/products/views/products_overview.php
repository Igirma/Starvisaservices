<?php require_once ELEM_DIR . 'admin_header.php'; ?>

<div id="container">

	<div id="overview">
		
		<div class="orange_button">
			<div class="orange_button_left"></div>
			<div class="orange_button_con">
				<img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
				<div class="orange_button_space"></div>
				<a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER?>/add"><?=$this->lang->line('add_product');?></a>
			</div>
			<div class="orange_button_right"></div>
		</div>
		
			<?php if(($this->config->item('max_category_depth')+1) > 1): ?>
				<select name="product[category_id]" class='prods_categories hidden'>
					<option value="0"><?=$this->lang->line('all')?></option>
										
					<?php foreach($data['drop_down'] as $product): ?>
											
						<option <?=(isset($_SESSION['category_id']) && $_SESSION['category_id'] == $product['category_id'] ? 'selected="selected"' : '');?> value="<?=$product['category_id']?>"><?=$product['title']?></option>
											
						<?php if(($this->config->item('max_category_depth')+1) > 2): ?>
											
							<?php foreach($product['children'] as $child): ?>
														
							<option <?=(isset($_SESSION['category_id']) && $_SESSION['category_id'] == $child['category_id'] ? 'selected="selected"' : '');?> value="<?=$child['category_id']?>">&nbsp;&#8226;&nbsp;&nbsp;<?=$child['title']?></option>
														
													
							<?php endforeach; ?>
													
						<?php endif; ?>
												
					<?php endforeach; ?>

				</select>
			<?php endif ?>
			
			<input type="text" name="search" value="<?php if(isset($_SESSION['prod_search'])) echo $_SESSION['prod_search'];?>" class='prod_search hidden'>
			
		<div class="orange_button hidden">
			<div class="orange_button_left"></div>
			<div class="orange_button_con">
				<a href="#" onclick="return false;" class='prod_search_button'><?=$this->lang->line('search');?></a>
			</div>
			<div class="orange_button_right"></div>
		</div>
		
		
		<div class="clear"></div>
		

<?php if (isset($data['products']) && !empty($data['products']) && count($data['products']) > 0) { ?>
<?php foreach ($data['products'] as $k => $category) { ?>

		<div class="column">

			<div class="pie header">
				<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-products"></div>
				<h1><?=$category['title'];?></h1>
			
			</div>
		
			<form method="post" action="<?=$this->url->current;?>">

				<table>
				
					<thead>
					
						<tr>
							
							<th style="width: 3%;">
								Nr
							</th>
							<th class="text_left" style="width: 48%;">
								<div class="spacer"></div>
								<p style="padding-left: 10px;"><?=$this->lang->line('product_title');?></p>
							</th>
							<th style="width: 12%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('edit_language');?></p>
							</th>
							<th class="hidden" style="width: 10%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('date_created');?></p>
							</th>
							<th style="width: 10%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('order');?></p>
							</th>
							<th style="width: 12%;" class='hidden'>
								<div class="spacer"></div>
								<p><?=$this->lang->line('highlight');?></p>
							</th>
							<th class="hidden" style="width: 12%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('action');?></p>
							</th>
							<th style="width: 12%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('active');?></p>
							</th>
							<th style="width: 15%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('delete');?></p>
							</th>
						
						</tr>
					
					</thead>
					
					<tbody>
					

						<?php $x = 0; ?>
						<?php $count_pages = count($category['products']); ?>
						
            <?php if (isset($category['products']) && !empty($category['products']) && count($category['products']) > 0) { ?>
						<?php foreach($category['products'] as $product) { ?>
						
						
						
						<tr>
							
							<td>
								<?php $x++; ?>
								<?=$x?>
							</td>
							
							<td class="text_left">
								
								<?php if(permission(CONTROLLER, 'edit')):?>
									<a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $product['product_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
								<?php endif; ?>
								
								<div class="spacer"></div>
							
								<span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($product['title'], 27);?></span>

								<?php if(permission(CONTROLLER, 'edit')):?>
									</a>
								<?php endif; ?>

							</td>

							<td>
							
								<div class="spacer"></div>
								
								<p>
												
								<?php foreach($data['languages'] as $language): ?>

									<?php $sub_active = $this->db->query('SELECT `product_content`.sub_active FROM `product_content` WHERE `product_content`.language_id = ? AND `product_content`.product_id = ?', array($language['language_id'], $product['product_id'])); ?>
									
									<?php if($sub_active[0]['sub_active'] == 1): ?>
										
										<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
										
										<a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/products/edit/<?=$product['product_id']?>/<?=$language['language_id'];?>">
											<span class="language sprite_<?=$language['code']?>"></span>
										</a>
										
										<?php endif; ?>
									
									<?php else: ?>
									
									<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
										
										<a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER;?>/edit/<?=$product['product_id']?>/<?=$language['language_id'];?>">
											<span class="language_grey sprite_<?=$language['code']?>"></span>
										</a>
										
										<?php endif; ?>
									
									<?php endif; ?>

								<?php endforeach; ?>
								
								</p>
							
							</td>
							
							<td class="hidden">
								<div class="spacer"></div>
								<p>
								<?=date('d-m-Y', $product['date_created']);?>
								</p>
							</td>

								<td class="order">
								
									<div class="spacer"></div>
									
									<p>
									
										<div class="up_down">
										
											<?php if(permission(CONTROLLER, 'edit')): ?>
											
												<?php if($x == 1 && $x != $count_pages): ?>

												<a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$product['order'];?>/<?=$product['product_id'];?>">
													<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
												</a>
												
												<?php endif; ?>
												
												<?php if($x != 1 && $x == $count_pages): ?>
												
												<a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$product['order'];?>/<?=$product['product_id'];?>">
													<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
												</a>
												
												<?php endif; ?>
												
												<?php if($x != 1 && $x != $count_pages): ?>
												
												<a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$product['order'];?>/<?=$product['product_id'];?>">
													<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
												</a>
												<a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$product['order'];?>/<?=$product['product_id'];?>">
													<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
												</a>
												
												<?php endif; ?>
											
											<?php endif; ?>
											
											<div class="clear"></div>
											
										</div>
									
									</p>
								
								</td>
							
							<td class='hidden'>
							
								<div class="spacer"></div>
								
								<p>
							
								<?php if(permission('products', 'edit')): ?>
								
									<?php if($product['highlight'] == 1): ?>
									
									<input type="radio" name="highlight[<?=$product['product_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
									<input type="radio" name="highlight[<?=$product['product_id'];?>]" value="0"><?=$this->lang->line('no');?>
									
									<?php else: ?>
									
									<input type="radio" name="highlight[<?=$product['product_id'];?>]" value="1"><?=$this->lang->line('yes');?>
									<input type="radio" name="highlight[<?=$product['product_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
									
									<?php endif; ?>
								
								<?php endif; ?>
								
								</p>
							
							</td>

							<td class="hidden">
							
								<div class="spacer"></div>
								
								<p>
							
								<?php if(permission('products', 'edit')): ?>
								
									<?php if($product['action'] == 1): ?>
									
									<input type="radio" name="action[<?=$product['product_id'];?>]" checked="checked" value="1">Da
									<input type="radio" name="action[<?=$product['product_id'];?>]" value="0"><?=$this->lang->line('no');?>
									
									<?php else: ?>
									
									<input type="radio" name="action[<?=$product['product_id'];?>]" value="1"><?=$this->lang->line('yes');?>
									<input type="radio" name="action[<?=$product['product_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
									
									<?php endif; ?>
								
								<?php endif; ?>
								
								</p>
							
							</td>
														
							<td>
							
								<div class="spacer"></div>
								
								<p>
							
								<?php if(permission('products', 'edit')): ?>
								
									<?php if($product['active'] == 1): ?>
									
									<input type="radio" name="active[<?=$product['product_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
									<input type="radio" name="active[<?=$product['product_id'];?>]" value="0"><?=$this->lang->line('no');?>
									
									<?php else: ?>
									
									<input type="radio" name="active[<?=$product['product_id'];?>]" value="1"><?=$this->lang->line('yes');?>
									<input type="radio" name="active[<?=$product['product_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
									
									<?php endif; ?>
								
								<?php endif; ?>
								
								</p>
							
							</td>
							
							<td>
								<div class="spacer"></div>
								
								<p>
								
									<?=(permission('products', 'delete') ? '<a title="' . $this->lang->line('delete') . '" class="delete" href="' . SITE_URL . 'admin/products/delete/' . $product['product_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?>
									
								</p>
							</td>
						
						</tr>
						
						<?php } ?>
					
					<?php } ?>
					
					</tbody>
				
				</table>
				
			</form>
		</div>

<?php } ?>
<?php } ?>


		<?php if(isset($data['out_of_stock']['products']) && count($data['out_of_stock']['products']) > 0): ?>
					
		<div class="column">

			<div class="pie header">
				<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-products"></div>
				<h1><?=$this->lang->line('out_of_stock');?></h1>
			
			</div>
		
			<form method="post" action="<?=$this->url->current;?>">

				<table>
				
					<thead>
					
						<tr>
							
							<th style="width: 3%;">
								Nr
							</th>
							<th class="text_left" style="width: 32%;">
								<div class="spacer"></div>
								<p style="padding-left: 10px;"><?=$this->lang->line('product_title');?></p>
							</th>
							<th style="width: 12%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('edit_language');?></p>
							</th>
							<th style="width: 10%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('date_created');?></p>
							</th>
							<th style="width: 10%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('order');?></p>
							</th>
							<th style="width: 12%;" class='hidden'>
								<div class="spacer"></div>
								<p><?=$this->lang->line('highlight');?></p>
							</th>
							<th style="width: 12%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('action');?></p>
							</th>
							<th style="width: 12%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('active');?></p>
							</th>
							<th style="width: 15%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('delete');?></p>
							</th>
						
						</tr>
					
					</thead>
					
					<tbody>
					
					<?php if(isset($data['out_of_stock']['products'])): ?>
						
						<?php $x = 0; ?>
						<?php $count_pages = count($data['out_of_stock']['products']); ?>
						
						<?php foreach($data['out_of_stock']['products'] as $product): ?>
							
						<tr>
							
							<td>
								<?php $x++; ?>
								<?=$x?>
							</td>
							
							<td class="text_left">
								
								<?php if(permission(CONTROLLER, 'edit')):?>
									<a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $product['product_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
								<?php endif; ?>
								
								<div class="spacer"></div>
							
								<span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($product['title'], 27);?></span>

								<?php if(permission(CONTROLLER, 'edit')):?>
									</a>
								<?php endif; ?>

							</td>

							<td>
							
								<div class="spacer"></div>
								
								<p>
												
								<?php foreach($data['languages'] as $language): ?>

									<?php $sub_active = $this->db->query('SELECT `product_content`.sub_active FROM `product_content` WHERE `product_content`.language_id = ? AND `product_content`.product_id = ?', array($language['language_id'], $product['product_id'])); ?>
									
									<?php if($sub_active[0]['sub_active'] == 1): ?>
										
										<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
										
										<a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/products/edit/<?=$product['product_id']?>/<?=$language['language_id'];?>">
											<span class="language sprite_<?=$language['code']?>"></span>
										</a>
										
										<?php endif; ?>
									
									<?php else: ?>
									
									<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
										
										<a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER;?>/edit/<?=$product['product_id']?>/<?=$language['language_id'];?>">
											<span class="language_grey sprite_<?=$language['code']?>"></span>
										</a>
										
										<?php endif; ?>
									
									<?php endif; ?>

								<?php endforeach; ?>
								
								</p>
							
							</td>
							
							<td>
								<div class="spacer"></div>
								<p>
								<?=date('d-m-Y', $product['date_created']);?>
								</p>
							</td>
							
							<td class="order">
								
									<div class="spacer"></div>
									
									<p>
									
										<div class="up_down">
										
											<?php if(permission(CONTROLLER, 'edit')): ?>
											
												<?php if($x == 1 && $x != $count_pages): ?>

												<a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$product['order'];?>/<?=$product['product_id'];?>">
													<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
												</a>
												
												<?php endif; ?>
												
												<?php if($x != 1 && $x == $count_pages): ?>
												
												<a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$product['order'];?>/<?=$product['product_id'];?>">
													<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
												</a>
												
												<?php endif; ?>
												
												<?php if($x != 1 && $x != $count_pages): ?>
												
												<a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$product['order'];?>/<?=$product['product_id'];?>">
													<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
												</a>
												<a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$product['order'];?>/<?=$product['product_id'];?>">
													<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
												</a>
												
												<?php endif; ?>
											
											<?php endif; ?>
											
											<div class="clear"></div>
											
										</div>
									
									</p>
								
								</td>
							
							<td class='hidden'>
							
								<div class="spacer"></div>
								
								<p>
							
								<?php if(permission('products', 'edit')): ?>
								
									<?php if($product['highlight'] == 1): ?>
									
									<input type="radio" name="highlight[<?=$product['product_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
									<input type="radio" name="highlight[<?=$product['product_id'];?>]" value="0"><?=$this->lang->line('no');?>
									
									<?php else: ?>
									
									<input type="radio" name="highlight[<?=$product['product_id'];?>]" value="1"><?=$this->lang->line('yes');?>
									<input type="radio" name="highlight[<?=$product['product_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
									
									<?php endif; ?>
								
								<?php endif; ?>
								
								</p>
							
							</td>

							<td>
							
								<div class="spacer"></div>
								
								<p>
							
								<?php if(permission('products', 'edit')): ?>
								
									<?php if($product['action'] == 1): ?>
									
									<input type="radio" name="action[<?=$product['product_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
									<input type="radio" name="action[<?=$product['product_id'];?>]" value="0"><?=$this->lang->line('no');?>
									
									<?php else: ?>
									
									<input type="radio" name="action[<?=$product['product_id'];?>]" value="1"><?=$this->lang->line('yes');?>
									<input type="radio" name="action[<?=$product['product_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
									
									<?php endif; ?>
								
								<?php endif; ?>
								
								</p>
							
							</td>
														
							<td>
							
								<div class="spacer"></div>
								
								<p>
							
								<?php if(permission('products', 'edit')): ?>
								
									<?php if($product['active'] == 1): ?>
									
									<input type="radio" name="active[<?=$product['product_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
									<input type="radio" name="active[<?=$product['product_id'];?>]" value="0"><?=$this->lang->line('no');?>
									
									<?php else: ?>
									
									<input type="radio" name="active[<?=$product['product_id'];?>]" value="1"><?=$this->lang->line('yes');?>
									<input type="radio" name="active[<?=$product['product_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
									
									<?php endif; ?>
								
								<?php endif; ?>
								
								</p>
							
							</td>
							
							<td>
								<div class="spacer"></div>
								
								<p>
								
									<?=(permission('products', 'delete') ? '<a title="' . $this->lang->line('delete') . '" class="delete" href="' . SITE_URL . 'admin/products/delete/' . $product['product_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?>
									
								</p>
							</td>
						
						</tr>
						
						<?php endforeach; ?>
					
					<?php endif; ?>
					
					</tbody>
				
				</table>
				
			</form>
		</div>
		<?php endif; ?>
		
	</div>
</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>