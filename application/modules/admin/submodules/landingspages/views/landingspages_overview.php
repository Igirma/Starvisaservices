<?php require_once(ELEM_DIR . 'admin_header.php');

?>

<div id="container">

	<div id="overview">
	
		<div class="orange_button">
			<div class="orange_button_left"></div>
			<div class="orange_button_con">
				<img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
				<div class="orange_button_space"></div>
				<a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER?>/add"><?=$this->lang->line('add_page');?></a>
			</div>
			<div class="orange_button_right"></div>
		</div>
		
		<div class="clear"></div>
		
		<div class="column">

			<div class="pie header">
				<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-<?=CONTROLLER?>"></div>
				<h1><?=$this->lang->line('overview_header');?></h1>
			
			</div>
		
			<form method="post" action="<?=$this->url->current;?>">

				<table>
				
					<thead>
					
						<tr>
						
							<th style="width: 5%;">Nr</th>
							
							<th class="text_left" style="width: 30%;">
								<div class="spacer"></div>
								<p style="padding-left: 10px;"><?=$this->lang->line('page_name');?></p>
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
							
							<th style="width: 15%;">
								<div class="spacer"></div>
								<p>Dup</p>
							</th>
							
							<th style="width: 10%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('delete');?></p>
							</th>
							
						</tr>
						
					</thead>
				
					<tbody>
						
						<?php if(isset($data['landingspages'])): ?>
						
							<?php $i = 0; ?>
							
							<?php $count_pages = count($data['landingspages']); ?>
							
							<?php foreach($data['landingspages'] as $landingspage): ?>
							
							<tr>
							
								<td>
								
									<?=++$i;?>
								
								</td>
								
								<td class="text_left">
								
									<?php if(permission(CONTROLLER, 'edit')):?>
										<a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $landingspage['landingspage_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
									<?php endif; ?>
									
									<div class="spacer"></div>
								
									<span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($landingspage['menu_title'], 35);?></span>

									<?php if(permission(CONTROLLER, 'edit')):?>
										</a>
									<?php endif; ?>
									
								</td>
								
								<td>
								
									<div class="spacer"></div>
									
									<p>
								
									<?php foreach($data['languages'] as $language): ?>

										<?php $sub_active = $this->db->query('SELECT `landingspage_content`.sub_active FROM `landingspage_content` WHERE `landingspage_content`.language_id = ? AND `landingspage_content`.landingspage_id = ?', array($language['language_id'], $landingspage['landingspage_id'])); ?>
										
										<?php if($sub_active[0]['sub_active'] == 1): ?>
										
											<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
										
											<a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$landingspage['landingspage_id']?>/<?=$language['language_id'];?>">
												
												<span class="language sprite_<?=$language['code']?>"></span>
											
											</a>
											
											<?php endif; ?>
										
										<?php else: ?>
										
											<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
										
											<a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$landingspage['landingspage_id']?>/<?=$language['language_id'];?>">
												
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
											
												<?php if($i == 1 && $i != $count_pages): ?>

												<a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$landingspage['order'];?>/<?=$landingspage['parent_id'];?>/<?=$landingspage['landingspage_id'];?>">
													<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
												</a>
												
												<?php endif; ?>
												
												<?php if($i != 1 && $i == $count_pages): ?>
												
												<a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$landingspage['order'];?>/<?=$landingspage['parent_id'];?>/<?=$landingspage['landingspage_id'];?>">
													<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
												</a>
												
												<?php endif; ?>
												
												<?php if($i != 1 && $i != $count_pages): ?>
												
												<a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$landingspage['order'];?>/<?=$landingspage['parent_id'];?>/<?=$landingspage['landingspage_id'];?>">
													<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
												</a>
												<a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$landingspage['order'];?>/<?=$landingspage['parent_id'];?>/<?=$landingspage['landingspage_id'];?>">
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
								
									<?php if($landingspage['controller'] != 'home' && permission(CONTROLLER, 'edit')): ?>
								
										<?php if($landingspage['active'] == 1): ?>
										
										<input type="radio" name="active[<?=$landingspage['landingspage_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
										
										<input type="radio" name="active[<?=$landingspage['landingspage_id'];?>]" value="0"><?=$this->lang->line('no');?>
										
										<?php else: ?>
										
										<input type="radio" name="active[<?=$landingspage['landingspage_id'];?>]" value="1"><?=$this->lang->line('yes');?>
										
										<input type="radio" name="active[<?=$landingspage['landingspage_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
										
										<?php endif; ?>
										
									<?php endif; ?>
									
									</p>
										
								</td>
								
								<td>
								
									<div class="spacer"></div>
									
									<p>
								
									<?php if(permission(CONTROLLER, 'add')): ?>
									
										<a href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/duplicate/<?=$landingspage['landingspage_id'];?>">Dupliceren</a>
										
									<?php endif; ?>
									
									</p>
									
								</td>
								
								<td>
								
									<div class="spacer"></div>
									
									<p>
								
									<?php if($landingspage['deletable'] == 1 && permission(CONTROLLER, 'delete') && empty($landingspage['children'])): ?>
									
									<a title="<?=$this->lang->line('delete')?>" class="delete" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/delete/<?=$landingspage['landingspage_id'];?>"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/delete.png"></a>
									
									<?php endif; ?>
									
									</p>
									
								</td>
								
							</tr>
							
								<?php if(!empty($landingspage['children'])): ?>
								
								<?php $x = 0; ?>

											<?php $count_child = count($landingspage['children']); ?>
											
											<?php foreach($landingspage['children'] as $child): ?>
												
												<tr>
													
													<td>
													
														<?=$i . '.' . ++$x;?>
														
													</td>
													
													<td style="width: 25%;" class="text_left">
													
														<?php if(permission(CONTROLLER, 'edit')):?>
															<a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $child['landingspage_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
														<?php endif; ?>
														
														<div class="spacer"></div>
													
														<span style="line-height: 40px; padding-left: 30px;"><?=str_shorten($child['menu_title'], 35);?></span>

														<?php if(permission(CONTROLLER, 'edit')):?>
															</a>
														<?php endif; ?>

													</td>
													
													<td>
													
														<div class="spacer"></div>
														
														<p>
													
															<?php foreach($data['languages'] as $language): ?>

																<?php $sub_active = $this->db->query('SELECT `landingspage_content`.sub_active FROM `landingspage_content` WHERE `landingspage_content`.language_id = ? AND `landingspage_content`.landingspage_id = ?', array($language['language_id'], $child['landingspage_id'])); ?>
																
																<?php if($sub_active[0]['sub_active'] == 1): ?>
																
																	<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
																
																	<a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$child['landingspage_id']?>/<?=$language['language_id'];?>">
																		
																		<span class="language sprite_<?=$language['code']?>"></span>
																	
																	</a>
																	
																	<?php endif; ?>
																
																<?php else: ?>
																
																	<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
																
																	<a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$child['landingspage_id']?>/<?=$language['language_id'];?>">
																		
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

																	<a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$child['order'];?>/<?=$child['parent_id'];?>/<?=$child['landingspage_id'];?>">
																		<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
																	</a>
																	
																	<?php endif; ?>
																	
																	<?php if($x != 1 && $x == $count_child): ?>
																	
																	<a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$child['order'];?>/<?=$child['parent_id'];?>/<?=$child['landingspage_id'];?>">
																		<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
																	</a>
																	
																	<?php endif; ?>
																	
																	<?php if($x != 1 && $x != $count_child): ?>
																	
																	<a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$child['order'];?>/<?=$child['parent_id'];?>/<?=$child['landingspage_id'];?>">
																		<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
																	</a>
																	<a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$child['order'];?>/<?=$child['parent_id'];?>/<?=$child['landingspage_id'];?>">
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
															
															<input type="radio" name="active[<?=$child['landingspage_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
															
															<input type="radio" name="active[<?=$child['landingspage_id'];?>]" value="0"><?=$this->lang->line('no');?>
															
															<?php else: ?>
															
															<input type="radio" name="active[<?=$child['landingspage_id'];?>]" value="1"><?=$this->lang->line('yes');?>
															
															<input type="radio" name="active[<?=$child['landingspage_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
															
															<?php endif; ?>
														
														<?php endif; ?>
														
														</p>
														
													</td>
													
													<td>
													
														<div class="spacer"></div>
														
														<p>
													
														<?php if(permission(CONTROLLER, 'add')): ?>
														
															<a href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/duplicate/<?=$child['landingspage_id'];?>">Dupliceren</a>
															
														<?php endif; ?>
														
														</p>
														
													</td>
													
													<td>
													
														<div class="spacer"></div>
														
														<p>
														
														<?php if($child['deletable'] == 1 && permission(CONTROLLER, 'delete') && empty($child['children'])): ?>
														
														<a title="<?=$this->lang->line('delete')?>" class="delete" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/delete/<?=$child['landingspage_id'];?>"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/delete.png"></a>
														
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
																					<a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $sub_child['landingspage_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
																				<?php endif; ?>
																				
																				<div class="spacer"></div>
																			
																				<span style="line-height: 40px; padding-left: 50px;"><?=str_shorten($sub_child['menu_title'], 35);?></span>

																				<?php if(permission(CONTROLLER, 'edit')):?>
																					</a>
																				<?php endif; ?>

																			</td>
																			
																			<td>
									
																			<div class="spacer"></div>
																			
																				<p>
																			
																					<?php foreach($data['languages'] as $language): ?>

																						<?php $sub_active = $this->db->query('SELECT `landingspage_content`.sub_active FROM `landingspage_content` WHERE `landingspage_content`.language_id = ? AND `landingspage_content`.landingspage_id = ?', array($language['language_id'], $sub_child['landingspage_id'])); ?>
																						
																						<?php if($sub_active[0]['sub_active'] == 1): ?>
																						
																							<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
																						
																							<a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$sub_child['landingspage_id']?>/<?=$language['language_id'];?>">
																								
																								<span class="language sprite_<?=$language['code']?>"></span>
																							
																							</a>
																							
																							<?php endif; ?>
																						
																						<?php else: ?>
																						
																							<?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
																						
																							<a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$sub_child['landingspage_id']?>/<?=$language['language_id'];?>">
																								
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
																				
																							<a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$sub_child['order'];?>/<?=$sub_child['parent_id'];?>/<?=$sub_child['landingspage_id'];?>">
																								<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
																							</a>
																							
																							<?php endif; ?>
																							
																							<?php if($y != 1 && $y == $count_sub_child): ?>
																							
																							<a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$sub_child['order'];?>/<?=$sub_child['parent_id'];?>/<?=$sub_child['landingspage_id'];?>">
																								<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
																							</a>
																							
																							<?php endif; ?>
																							
																							<?php if($y != 1 && $y != $count_sub_child): ?>
																							
																							<a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$sub_child['order'];?>/<?=$sub_child['parent_id'];?>/<?=$sub_child['landingspage_id'];?>">
																								<img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
																							</a>
																							<a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$sub_child['order'];?>/<?=$sub_child['parent_id'];?>/<?=$sub_child['landingspage_id'];?>">
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
																					
																					<input type="radio" name="active[<?=$sub_child['landingspage_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
																					
																					<input type="radio" name="active[<?=$sub_child['landingspage_id'];?>]" value="0"><?=$this->lang->line('no');?>
																					
																					<?php else: ?>
																					
																					<input type="radio" name="active[<?=$sub_child['landingspage_id'];?>]" value="1"><?=$this->lang->line('yes');?>
																					
																					<input type="radio" name="active[<?=$sub_child['landingspage_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
																					
																					<?php endif; ?>
																				
																				<?php endif; ?>
																				
																				</p>
																				
																			</td>
																			
																			<td>
								
																				<div class="spacer"></div>
																				
																				<p>
																			
																				<?php if(permission(CONTROLLER, 'add')): ?>
																				
																					<a href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/duplicate/<?=$landingspage['landingspage_id'];?>">Dupliceren</a>
																					
																				<?php endif; ?>
																				
																				</p>
																				
																			</td>
																			
																			<td>
																			
																				<div class="spacer"></div>
																				
																				<p>
																				
																				<?php if($sub_child['deletable'] == 1 && permission(CONTROLLER, 'delete') && empty($sub_child['children'])): ?>
																				
																				<a title="<?=$this->lang->line('delete')?>" class="delete" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/delete/<?=$sub_child['landingspage_id'];?>"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/delete.png"></a>
																				
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

			</form>
			
		</div>
		
	</div>
	
</div>

<?php require_once(ELEM_DIR . 'admin_footer.php'); ?>								