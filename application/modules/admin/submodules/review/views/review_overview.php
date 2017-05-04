<?php require_once ELEM_DIR . 'admin_header.php'; 

//debug($data);
?>

<div id="container">

	<div id="overview">
	
		<div class="column">
		
			<div class="pie header">
				<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-review"></div>
				<h1><?=$this->lang->line('overview_header');?></h1>
			
			</div>
		
			<form method="post" action="<?=$this->url->current;?>">

				<table>
				
					<thead>
					
						<tr>
						
							<th style="width: 3%;">Nr</th>
							<th class="text_left" style="width: 27%;">
								<div class="spacer"></div>
								<p style="padding-left: 10px;"><?=$this->lang->line('name');?></p>
							</th>
							<th class="text_left" style="width: 20%;">
								<div class="spacer"></div>
								<p style="padding-left: 10px;"><?=$this->lang->line('email');?></p>
							</th>
							<th style="width: 10%">
								<div class="spacer"></div>
								<p><?=$this->lang->line('date');?></p>
							</th>
							<th class="sortable" style="width: 20%">
								<div class="spacer"></div>
								<p><?=$this->lang->line('product');?></p>
							</th>
							
							<th style="width: 10%;">
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
						
						<?php if(!empty($data)): ?>
							<?php foreach($data as $review): ?>
							
							<tr>
							
								<td><?=++$i;?></td>
								
								<td class="text_left">
									
									<?php if(permission(CONTROLLER, 'edit')):?>
										<a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $review['review_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
									<?php endif; ?>
									
									<div class="spacer"></div>
								
									<span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($review['name'] , 100);?></span>

									<?php if(permission(CONTROLLER, 'edit')):?>
										</a>
									<?php endif; ?>
									
								</td>
								
								<td class="text_left">
									<div class="spacer"></div>
									<span style="line-height: 40px; padding-left: 10px;"><a title="<?=$this->lang->line('mailto')?>" href="mailto:<?=$review['email'];?>"><?=$review['email'];?></a></span>
								</td>
								
								<td>
									<div class="spacer"></div>
									
									<p>
										<?=date('d-m-Y', $review['review_date']);?>
									</p>
								</td>
								
								<td>
									<div class="spacer"></div>
									
									<p>
										<?php echo $review['product'];?>
									</p>
								</td>

								<td>
								
									<div class="spacer"></div>
									
									<p>
								
									<?php if(permission(CONTROLLER, 'edit')): ?>
								
										<?php if($review['active'] == 1): ?>
										
										<input type="radio" name="active[<?=$review['review_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
										
										<input type="radio" name="active[<?=$review['review_id'];?>]" value="0"><?=$this->lang->line('no');?>
										
										<?php else: ?>
										
										<input type="radio" name="active[<?=$review['review_id'];?>]" value="1"><?=$this->lang->line('yes');?>
										
										<input type="radio" name="active[<?=$review['review_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
										
										<?php endif; ?>
										
									<?php endif; ?>
									
									</p>
										
								</td>
								
								<td>
									<div class="spacer"></div>
								
									<p><?=(permission('review', 'delete') ? '<a title="' . $this->lang->line('delete') . '" class="delete" href="' . SITE_URL . 'admin/review/delete/' . $review['review_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?></p>
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