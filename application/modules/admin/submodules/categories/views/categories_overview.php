<?php require_once ELEM_DIR . 'admin_header.php'; 

?>
<div id="container">

	<div id="overview">
	
		<div  class="orange_button">
		
		<div class="orange_button_left"></div>
		
			<div class="orange_button_con">
			
				<img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
				
				<div class="orange_button_space"></div>
				
				<a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/add/' . $this->url->segment(2);?>"><?=$this->lang->line('add_category');?></a>
				
			</div>
			
			<div class="orange_button_right"></div>
			
		</div>
		
		<?php if($this->url->segment(2) == 'products'): ?>
		
			<div class="orange_button hidden">
			
				<div class="orange_button_left"></div>
				
				<div class="orange_button_con">
				
					<img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
					
					<div class="orange_button_space"></div>
					
					<a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/add_product_options/' . $this->url->segment(2);?>"><?=$this->lang->line('product_options_add');?></a>
				
				</div>
				
				<div class="orange_button_right"></div>
			
			</div>
		
		<?php endif; ?>	
		
		<div class="clear"></div>
		
		<form method="post" action="<?=$this->url->current;?>">

		<div class="column">
		
			<div class="header">
					<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-orange"></div>
					<h1><?=$this->lang->line('overview_header');?></h1>
					<div class="options hidden">
						
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
				
					<table>
					
						<thead>
						
							<tr>
							
								<th style="width: 5%;">Nr</th>
								<th class="text_left" style="width: 45%;">
									<div class="spacer"></div>
									<p style="padding-left: 10px;"><?=$this->lang->line('content_title');?></p>
								</th>
								<th style="width: 15%;">
									<div class="spacer"></div>
									<p><?=$this->lang->line('edit_language');?></p>
								</th>
								
								<th style="width: 10%;">
									<div class="spacer"></div>
									<p><?=$this->lang->line('order');?></p>
								</th>
								
								<th style="width: 15%;">
									<div class="spacer"></div>
									<p><?=$this->lang->line('active');?></p>
								</th>
								<th style="width: 10%;">
									<div class="spacer"></div>
									<p><?=$this->lang->line('delete');?></p>
								</th>
							
							</tr>
						
						</thead>
						
						<tbody>
						
							<?php $i = 0; ?>
							
							<?php if(!empty($data['category'])): ?>
								<?php foreach($data['category'] as $category): ?>
								
								<tr>
								
									<td><?=++$i;?></td>
									<?php $count_categories = count($data['category']); ?>
								
									<td class="text_left">
										
										<?php if(permission(CONTROLLER, 'edit')):?>
											<a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $this->url->segment(2) . '/' . $category['category_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
										<?php endif; ?>
										
										<div class="spacer"></div>
									
										<span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($category['title'], 80);?></span>

										<?php if(permission(CONTROLLER, 'edit')):?>
											</a>
										<?php endif; ?>
										
									</td>
									<td>
									
										<div class="spacer"></div>
										
										<p>
									
										<?php foreach($data['languages'] as $language): ?>

											<?php $sub_active = $this->db->query('SELECT `category_content`.sub_active FROM `category_content` WHERE `category_content`.language_id = ? AND `category_content`.category_id = ?', array($language['language_id'], $category['category_id'])); ?>
											
											<?php if($sub_active[0]['sub_active'] == 1): ?>
											
												<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
											
												<a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$this->url->segment(2);?>/<?=$category['category_id']?>/<?=$language['language_id'];?>">
													
													<span class="language sprite_<?=$language['code']?>"></span>
												
												</a>
												
												<?php endif; ?>
											
											<?php else: ?>
											
												<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
											
												<a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$this->url->segment(2);?>/<?=$category['category_id']?>/<?=$language['language_id'];?>">
													
													<span class="language_grey sprite_<?=$language['code']?>"></span>
												
												</a>
												
												<?php endif; ?>
											
											<?php endif; ?>

										<?php endforeach; ?>
									
										</p>
									
									</td>
									
									<td class="order">
									
										<div class="spacer"></div>
										
										<p>
										
											<div class="up_down">
											
												<?php if(permission(CONTROLLER, 'edit')): ?>
												
													<?php if($i == 1 && $i != $count_categories): ?>

													<a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/<?=$this->url->segment(2);?>/down/<?=$category['order'];?>/<?=$category['parent_id'];?>/<?=$category['category_id'];?>">
														<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
													</a>
													
													<?php endif; ?>
													
													<?php if($i != 1 && $i == $count_categories): ?>
													
													<a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/<?=$this->url->segment(2);?>/up/<?=$category['order'];?>/<?=$category['parent_id'];?>/<?=$category['category_id'];?>">
														<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
													</a>
													
													<?php endif; ?>
													
													<?php if($i != 1 && $i != $count_categories): ?>
													
													<a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/<?=$this->url->segment(2);?>/up/<?=$category['order'];?>/<?=$category['parent_id'];?>/<?=$category['category_id'];?>">
														<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
													</a>
													<a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/<?=$this->url->segment(2);?>/down/<?=$category['order'];?>/<?=$category['parent_id'];?>/<?=$category['category_id'];?>">
														<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
													</a>
													
													<?php endif; ?>
												
												<?php endif; ?>
												
												<div class="clear"></div>
												
											</div>
										
										</p>
									
									</td>
									
									<td>
									
										<div class="spacer"></div>
										
										<p>
									
										<?php if(permission(CONTROLLER, 'edit')): ?>
									
											<?php if($category['active'] == 1): ?>
											
											<input type="radio" name="active[<?=$category['category_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
											
											<input type="radio" name="active[<?=$category['category_id'];?>]" value="0"><?=$this->lang->line('no');?>
											
											<?php else: ?>
											
											<input type="radio" name="active[<?=$category['category_id'];?>]" value="1"><?=$this->lang->line('yes');?>
											
											<input type="radio" name="active[<?=$category['category_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
											
											<?php endif; ?>
											
										<?php endif; ?>
										
										</p>
											
									</td>
									
									<td>
										<div class="spacer"></div>
									
										<p><?=((permission('categories', 'delete') && empty($category['children'])) ? '<a title="' . $this->lang->line('delete') . '" class="delete" href="' . SITE_URL . 'admin/categories/' . $this->url->segment(2) . '/delete/' . $category['category_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?></p>
									</td>
								
								</tr>
								
								<?php if(!empty($category['children'])): ?>
									
									<?php $x = 0; ?>

												<?php $count_child = count($category['children']); ?>
												
												<?php foreach($category['children'] as $child): ?>
													
													<tr>
														
														<td>
														
															<?=$i . '.' . ++$x;?>
															
														</td>
														
														<td style="width: 25%;" class="text_left">
														
															<?php if(permission(CONTROLLER, 'edit')):?>
																<a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/'.$this->url->segment(2). '/' . $child['category_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
															<?php endif; ?>
															
															<div class="spacer"></div>
														
															<span style="line-height: 40px; padding-left: 30px;"><?=str_shorten($child['title'], 35);?></span>

															<?php if(permission(CONTROLLER, 'edit')):?>
																</a>
															<?php endif; ?>

														</td>
														
														<td>
														
															<div class="spacer"></div>
															
															<p>
														
																<?php foreach($data['languages'] as $language): ?>

																	<?php $sub_active = $this->db->query('SELECT `category_content`.sub_active FROM `category_content` WHERE `category_content`.language_id = ? AND `category_content`.category_id = ?', array($language['language_id'], $child['category_id'])); ?>
																	
																	<?php if($sub_active[0]['sub_active'] == 1): ?>
																	
																		<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
																	
																		<a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$this->url->segment(2);?>/<?=$child['category_id']?>/<?=$language['language_id'];?>">
																			
																			<span class="language sprite_<?=$language['code']?>"></span>
																		
																		</a>
																		
																		<?php endif; ?>
																	
																	<?php else: ?>
																	
																		<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
																	
																		<a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$this->url->segment(2);?>/<?=$child['category_id']?>/<?=$language['language_id'];?>">
																			
																			<span class="language_grey sprite_<?=$language['code']?>"></span>
																		
																		</a>
																		
																		<?php endif; ?>
																	
																	<?php endif; ?>

																<?php endforeach; ?>
															
															<p>
														
														</td>
														
														<td class="order">
														
															<div class="spacer"></div>
															
															<p>
															
																<div class="up_down">
																
																	<?php if(permission(CONTROLLER, 'edit')): ?>

																		<?php if($x == 1 && $x != $count_child): ?>

																		<a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/<?=$this->url->segment(2);?>/down/<?=$child['order'];?>/<?=$child['parent_id'];?>/<?=$child['category_id'];?>">
																			<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
																		</a>
																		
																		<?php endif; ?>
																		
																		<?php if($x != 1 && $x == $count_child): ?>
																		
																		<a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/<?=$this->url->segment(2);?>/up/<?=$child['order'];?>/<?=$child['parent_id'];?>/<?=$child['category_id'];?>">
																			<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
																		</a>
																		
																		<?php endif; ?>
																		
																		<?php if($x != 1 && $x != $count_child): ?>
																		
																		<a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/<?=$this->url->segment(2);?>/up/<?=$child['order'];?>/<?=$child['parent_id'];?>/<?=$child['category_id'];?>">
																			<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
																		</a>
																		<a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/<?=$this->url->segment(2);?>/down/<?=$child['order'];?>/<?=$child['parent_id'];?>/<?=$child['category_id'];?>">
																			<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
																		</a>
																		
																		<?php endif; ?>
																	
																	<?php endif; ?>
																	
																</div>
																
															<p>
														
														</td>
														
														<td>
														
															<div class="spacer"></div>
															
															<p>
														
															<?php if(permission(CONTROLLER, 'edit')): ?>
															
																<?php if($child['active'] == 1): ?>
																
																<input type="radio" name="active[<?=$child['category_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
																
																<input type="radio" name="active[<?=$child['category_id'];?>]" value="0"><?=$this->lang->line('no');?>
																
																<?php else: ?>
																
																<input type="radio" name="active[<?=$child['category_id'];?>]" value="1"><?=$this->lang->line('yes');?>
																
																<input type="radio" name="active[<?=$child['category_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
																
																<?php endif; ?>
															
															<?php endif; ?>
															
															</p>
															
														</td>
														
														<td>
														
															<div class="spacer"></div>
															
															<p>
															
															<?php if($child['deletable'] == 1 && permission(CONTROLLER, 'delete') && empty($child['children'])): ?>
															
															<a title="<?=$this->lang->line('delete')?>" class="delete" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/<?=$this->url->segment(2);?>/delete/<?=$child['category_id'];?>"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/delete.png"></a>
															
															<?php endif; ?>
															
															</p>
															
														</td>
														
													</tr>
													
														<?php if(!empty($child['children'])): ?>
									
														<?php $y = 0; ?>

																	<?php $count_sub_child = count($child['children']); ?>
																	
																		<?php foreach($child['children'] as $sub_child): ?>
																			
																			<tr>

																				<td>
																					
																					<?=$i . '.' . $x . '.' . ++$y;?>
																				
																				</td>
																				
																				<td class="text_left">
																					
																					<?php if(permission(CONTROLLER, 'edit')):?>
																						<a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $this->url->segment(2) . '/' . $sub_child['category_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
																					<?php endif; ?>
																					
																					<div class="spacer"></div>
																				
																					<span style="line-height: 40px; padding-left: 50px;"><?=str_shorten($sub_child['title'], 35);?></span>

																					<?php if(permission(CONTROLLER, 'edit')):?>
																						</a>
																					<?php endif; ?>

																				</td>
																				
																				<td>
										
																				<div class="spacer"></div>
																				
																					<p>
																				
																						<?php foreach($data['languages'] as $language): ?>

																							<?php $sub_active = $this->db->query('SELECT `category_content`.sub_active FROM `category_content` WHERE `category_content`.language_id = ? AND `category_content`.category_id = ?', array($language['language_id'], $sub_child['category_id'])); ?>
																							
																							<?php if($sub_active[0]['sub_active'] == 1): ?>
																							
																								<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
																							
																								<a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$this->url->segment(2);?>/<?=$sub_child['category_id']?>/<?=$language['language_id'];?>">
																									
																									<span class="language sprite_<?=$language['code']?>"></span>
																								
																								</a>
																								
																								<?php endif; ?>
																							
																							<?php else: ?>
																							
																								<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
																							
																								<a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$this->url->segment(2);?>/<?=$sub_child['category_id']?>/<?=$language['language_id'];?>">
																									
																									<span class="language_grey sprite_<?=$language['code']?>"></span>
																								
																								</a>
																								
																								<?php endif; ?>
																							
																							<?php endif; ?>

																						<?php endforeach; ?>
																					
																					<p>
																				
																				</td>
																				
																				<td class="order">
																				
																					<div class="spacer"></div>
																					
																					<p>
																					
																						<div class="up_down">
																						
																							<?php if(permission(CONTROLLER, 'edit')): ?>

																								<?php if($y == 1 && $y != $count_sub_child): ?>
																					
																								<a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/<?=$this->url->segment(2);?>/down/<?=$sub_child['order'];?>/<?=$sub_child['parent_id'];?>/<?=$sub_child['category_id'];?>">
																									<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
																								</a>
																								
																								<?php endif; ?>
																								
																								<?php if($y != 1 && $y == $count_sub_child): ?>
																								
																								<a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/<?=$this->url->segment(2);?>/up/<?=$sub_child['order'];?>/<?=$sub_child['parent_id'];?>/<?=$sub_child['category_id'];?>">
																									<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
																								</a>
																								
																								<?php endif; ?>
																								
																								<?php if($y != 1 && $y != $count_sub_child): ?>
																								
																								<a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/<?=$this->url->segment(2);?>/up/<?=$sub_child['order'];?>/<?=$sub_child['parent_id'];?>/<?=$sub_child['category_id'];?>">
																									<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
																								</a>
																								<a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/<?=$this->url->segment(2);?>/down/<?=$sub_child['order'];?>/<?=$sub_child['parent_id'];?>/<?=$sub_child['category_id'];?>">
																									<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
																								</a>
																								
																								<?php endif; ?>
																							
																							<?php endif; ?>
																							
																						</div>

																					</p>
																					
																				</td>
																				
																				<td>
																				
																					<div class="spacer"></div>
																					
																					<p>
																				
																					<?php if(permission(CONTROLLER, 'edit')): ?>
																						
																						<?php if($sub_child['active'] == 1): ?>
																						
																						<input type="radio" name="active[<?=$sub_child['category_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
																						
																						<input type="radio" name="active[<?=$sub_child['category_id'];?>]" value="0"><?=$this->lang->line('no');?>
																						
																						<?php else: ?>
																						
																						<input type="radio" name="active[<?=$sub_child['category_id'];?>]" value="1"><?=$this->lang->line('yes');?>
																						
																						<input type="radio" name="active[<?=$sub_child['category_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
																						
																						<?php endif; ?>
																					
																					<?php endif; ?>
																					
																					</p>
																					
																				</td>
																				
																				<td>
																				
																					<div class="spacer"></div>
																					
																					<p>
																					
																					<?php if($sub_child['deletable'] == 1 && permission(CONTROLLER, 'delete') && empty($sub_child['children'])): ?>
																					
																					<a title="<?=$this->lang->line('delete')?>" class="delete" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/<?=$this->url->segment(2);?>/delete/<?=$sub_child['category_id'];?>"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/delete.png"></a>
																					
																					<?php endif; ?>
																					
																					</p>
																					
																				</td>
																				
																			</tr>
																									
																		<?php endforeach; ?>

														<?php endif; ?>
																			
													<?php endforeach; ?>

									<?php endif; ?>
								
								
								
								<?php endforeach; ?>
							<?php endif; ?>
						
						</tbody>
					
					</table>
					
			</div>
		</div>
		
		<?php
		// options
		?>
		<?php if($this->url->segment(2) == 'products'): ?>
		<div class="column hidden">
		
			<div class="header">
					<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-orange"></div>
					<h1><?=$this->lang->line('category_options');?></h1>
					<div class="options hidden">
						
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
					<input type='hidden' value='yes' name='product_options'>
					<table>
					
						<thead>
						
							<tr>
							
								<th style="width: 5%;">Nr</th>
								<th class="text_left" style="width: 45%;">
									<div class="spacer"></div>
									<p style="padding-left: 10px;"><?=$this->lang->line('content_title');?></p>
								</th>
								<th style="width: 15%;">
									<div class="spacer"></div>
									<p><?=$this->lang->line('edit_language');?></p>
								</th>
								
								<th style="width: 10%;">
									<div class="spacer"></div>
									<p><?=$this->lang->line('order');?></p>
								</th>
								
								<th style="width: 15%;">
									<div class="spacer"></div>
									<p><?=$this->lang->line('active');?></p>
								</th>
								<th style="width: 10%;">
									<div class="spacer"></div>
									<p><?=$this->lang->line('delete');?></p>
								</th>
							
							</tr>
						
						</thead>
						
						<tbody>
						
							<?php $i = 0; ?>
							<?php $count_product_options = 0;
							if(isset($data['product_options']))
								$count_product_options = count($data['product_options']); 
							?>
								
							<?php if(!empty($data['product_options'])): ?>
								<?php foreach($data['product_options'] as $product_options): ?>
								
								<tr <?php echo (($product_options['superadmin'] == 1)?((permission_level() == 2)?"style='background:#ffede1'":"class='hidden'"):"");?>>
						
									<td><?=++$i;?></td>
									
									<td class="text_left">
										
										<?php if(permission(CONTROLLER, 'edit')):?>
											<a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit_product_options/' . $this->url->segment(2) . '/' . $product_options['product_options_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
										<?php endif; ?>
										
										<div class="spacer"></div>
									
										<span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($product_options['title'], 80);?></span>

										<?php if(permission(CONTROLLER, 'edit')):?>
											</a>
										<?php endif; ?>
										
									</td>
									<td>
									
										<div class="spacer"></div>
										
										<p>
									
										<?php foreach($data['languages'] as $language): ?>

											<?php $sub_active = $this->db->query('SELECT `product_options_heading`.sub_active FROM `product_options_heading` WHERE `product_options_heading`.language_id = ? AND `product_options_heading`.product_options_id = ?', array($language['language_id'], $product_options['product_options_id'])); ?>
											
											<?php if($sub_active[0]['sub_active'] == 1): ?>
											
												<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
											
												<a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit_product_options/<?=$this->url->segment(2);?>/<?=$product_options['product_options_id']?>/<?=$language['language_id'];?>">
													
													<span class="language sprite_<?=$language['code']?>"></span>
												
												</a>
												
												<?php endif; ?>
											
											<?php else: ?>
											
												<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
											
												<a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit_product_options/<?=$this->url->segment(2);?>/<?=$product_options['product_options_id']?>/<?=$language['language_id'];?>">
													
													<span class="language_grey sprite_<?=$language['code']?>"></span>
												
												</a>
												
												<?php endif; ?>
											
											<?php endif; ?>

										<?php endforeach; ?>
									
										</p>
									
									</td>
									
									<td class="order">
									
										<div class="spacer"></div>
										
										<p>
										
											<div class="up_down">
											
												<?php if(permission(CONTROLLER, 'edit')): ?>
												
													<?php if($i == 1 && $i != $count_product_options): ?>

													<a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order_product_options/<?=$this->url->segment(2);?>/down/<?=$product_options['order'];?>/<?=$product_options['product_options_id'];?>">
														<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
													</a>
													
													<?php endif; ?>
													
													<?php if($i != 1 && $i == $count_product_options): ?>
													
													<a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order_product_options/<?=$this->url->segment(2);?>/up/<?=$product_options['order'];?>/<?=$product_options['product_options_id'];?>">
														<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
													</a>
													
													<?php endif; ?>
													
													<?php if($i != 1 && $i != $count_product_options): ?>
													
													<a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order_product_options/<?=$this->url->segment(2);?>/up/<?=$product_options['order'];?>/<?=$product_options['product_options_id'];?>">
														<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
													</a>
													<a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order_product_options/<?=$this->url->segment(2);?>/down/<?=$product_options['order'];?>/<?=$product_options['product_options_id'];?>">
														<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
													</a>
													
													<?php endif; ?>
												
												<?php endif; ?>
												
												<div class="clear"></div>
												
											</div>
										
										</p>
									
									</td>
									
									<td>
									
										<div class="spacer"></div>
										
										<p>
									
										<?php if(permission(CONTROLLER, 'edit')): ?>
									
											<?php if($product_options['active'] == 1): ?>
											
											<input type="radio" name="active_product_options[<?=$product_options['product_options_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
											
											<input type="radio" name="active_product_options[<?=$product_options['product_options_id'];?>]" value="0"><?=$this->lang->line('no');?>
											
											<?php else: ?>
											
											<input type="radio" name="active_product_options[<?=$product_options['product_options_id'];?>]" value="1"><?=$this->lang->line('yes');?>
											
											<input type="radio" name="active_product_options[<?=$product_options['product_options_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
											
											<?php endif; ?>
											
										<?php endif; ?>
										
										</p>
											
									</td>
									
									<td>
										<div class="spacer"></div>
									
										<p><?=((permission('categories', 'delete')) ? '<a title="' . $this->lang->line('delete') . '" class="delete" href="' . SITE_URL . 'admin/categories/' . $this->url->segment(2) . '/delete_product_options/' . $product_options['product_options_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?></p>
									</td>
								
								</tr>
								
								<?php endforeach; ?>
							<?php endif; ?>
						
						</tbody>
					
					</table>
					
				
			</div>
		</div>
		<?php endif; ?>
		</form>
	</div>
</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>