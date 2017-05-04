<?php require_once ELEM_DIR . 'admin_header.php'; ?>

<div id="container">

	<div id="overview">
		
		<div class="orange_button">
			<div class="orange_button_left"></div>
			<div class="orange_button_con">
				<img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
				<div class="orange_button_space"></div>
				<a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER?>/add"><?=$this->lang->line('add_discountcodes');?></a>
			</div>
			<div class="orange_button_right"></div>
		</div>
		
		<div class="clear"></div>
		
		<div class="column">

			<div class="pie header">
				<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-discountcodes"></div>
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
								<p style="padding-left: 10px;"><?=$this->lang->line('discountcodes_title');?></p>
							</th>
							<th style="width: 15%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('code_nr');?></p>
							</th>
							<th style="width: 10%;">
								<div class="spacer"></div>
								<p><?=$this->lang->line('nr_codes_active');?></p>
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
					
					<?php if(isset($data['discountcodes'])): ?>
						
						<?php $x = 1; ?>
						
						<?php foreach($data['discountcodes'] as $discountcodes): ?>
						
						
						
						<tr>
							
							<td>
								<?=$x?>
								<?php $x++; ?>
							</td>
							
							<td class="text_left">
								
								<?php if(permission(CONTROLLER, 'edit')):?>
									<a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $discountcodes['discountcodes_id'] ?>" style="display:block;">
								<?php endif; ?>
								
								<div class="spacer"></div>
							
								<span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($discountcodes['title'], 27);?></span>

								<?php if(permission(CONTROLLER, 'edit')):?>
									</a>
								<?php endif; ?>

							</td>

							<td>
							
								<div class="spacer"></div>
								
								<p>
												
									<?php echo $discountcodes['nr_codes'];?>
								
								</p>
							
							</td>
							
							<td>
								<div class="spacer"></div>
								<p>
									<?php echo $discountcodes['nr_codes_active'];?>
								</p>
							</td>
							
							<td>
							
								<div class="spacer"></div>
								
								<p>
							
								<?php if(permission('discountcodes', 'edit')): ?>
								
									<?php if($discountcodes['active'] == 1): ?>
									
									<input type="radio" name="active[<?=$discountcodes['discountcodes_id'];?>]" checked="checked" value="1">Ja
									<input type="radio" name="active[<?=$discountcodes['discountcodes_id'];?>]" value="0">Nee
									
									<?php else: ?>
									
									<input type="radio" name="active[<?=$discountcodes['discountcodes_id'];?>]" value="1">Ja
									<input type="radio" name="active[<?=$discountcodes['discountcodes_id'];?>]" checked="checked" value="0">Nee
									
									<?php endif; ?>
								
								<?php endif; ?>
								
								</p>
							
							</td>
							
							<td>
								<div class="spacer"></div>
								
								<p>
								
									<?=(permission('discountcodes', 'delete') ? '<a title="' . $this->lang->line('delete') . '" class="delete" href="' . SITE_URL . 'admin/discountcodes/delete/' . $discountcodes['discountcodes_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?>
									
								</p>
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