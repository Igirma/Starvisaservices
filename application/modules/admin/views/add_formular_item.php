<script>
	$(document).ready(function(){
		
		$('.item_add').click(function(){
			$('.option_row:last').after('<tr class="option_row">'+$('.add_row:last').html().replace('options[]','options['+(parseInt($('.nummer-items').val())+1)+']').replace(">1<",'>'+(parseInt($('.nummer-items').val())+1)+'<')+'</tr>');
			$('.nummer-items').val(parseInt($('.nummer-items').val())+1);
			
			$('.product_options_delete').click(function(e){
				e.preventDefault();
				
				var href = $(this).attr('href');
				
				deleteItemInline($(this).parent().parent());
			});
			return false;
		});
		$('.product_options_delete').click(function(e){
			e.preventDefault();
			
			var href = $(this).attr('href');
			
			deleteItemInline($(this).parent().parent());
		});
		
		$('input[name="save_item_info"]').click(function(){
		
			$.ajax({
				url: SITE_URL + 'admin/save_item_info',
				type: 'POST',
				data: $('form#save_item_info').serialize(),
				success: function(){
					$('.save').click()
				}
			});
		});
	});
</script>

<form action="<?=$this->url->current;?>" method="post" id='save_item_info'>

	<div id="edit_<?=$data['formular_id'];?>" class="modal_window">
		
	<input type="hidden" name="formular_id" value="<?=$data['formular_id'];?>">
	<input type="hidden" name="language_id" value="<?=$data['language_id'];?>">
	<?php if(isset($data['formular_item_id'])){ ?>
	<input type="hidden" name="formular_item_id" value="<?=$data['formular_item_id'];?>">
	<?php }else{ ?>
	<input type="hidden" name="formular_item_id" value="0">
	<?php } ?>
	
	<div class="subcolumn">
				
		<table style='width:100%;'>
						
			<tr>
				<th><?=$this->lang->line('item_title');?></th>
				<td><input type="text" name="title" value="<?=(isset($data['title']) ? $data['title'] : '');?>"></td>
			</tr>
						
			<tr>
				<th><?=$this->lang->line('active');?></th>
				<td>
					<?php if(isset($data['sub_active'])): ?>
								
						<?php if($data['sub_active'] == 1): ?>
										
							<input type="radio" name="sub_active" value="1" checked="checked">Ja
							<input type="radio" name="sub_active" value="0">Nee
										
						<?php else: ?>
										
							<input type="radio" name="sub_active" value="1">Ja
							<input type="radio" name="sub_active" value="0" checked="checked">Nee
										
						<?php endif; ?>
										
					<?php else: ?>
								
					<input type="radio" name="sub_active" value="1" checked="checked">Ja
					<input type="radio" name="sub_active" value="0">Nee
								
				<?php endif; ?>
			</td>
		</tr>
		
		<tr>
			<th><?=$this->lang->line('item_type');?></th>
			<td>
				<input type="radio" name="type" value="1" <?php echo (($data['type'] == 1 || !$data['type'])?'checked="checked"':'');?>><?=$this->lang->line('item_type_1');?> &nbsp;&nbsp;
				<input type="radio" name="type" value="2" <?php echo (($data['type'] == 2)?'checked="checked"':'');?>><?=$this->lang->line('item_type_2');?> &nbsp;&nbsp;
				<input type="radio" name="type" value="3" <?php echo (($data['type'] == 3)?'checked="checked"':'');?>><?=$this->lang->line('item_type_3');?> &nbsp;&nbsp;
				<input type="radio" name="type" value="4" <?php echo (($data['type'] == 4)?'checked="checked"':'');?>><?=$this->lang->line('item_type_4');?> &nbsp;&nbsp;
				<input type="radio" name="type" value="5" <?php echo (($data['type'] == 5)?'checked="checked"':'');?>><?=$this->lang->line('item_type_5');?> &nbsp;&nbsp;
			</td> 
		</tr>
						
		<?php 
		if(!empty($data['options'])): 
			$filter_id = 0;
			foreach($data['options'] as $k => $options): 
				?>
				<tr class='option_row'>
					<th><?=$this->lang->line('option');?> <span><?=$filter_id+1;?></span></th>
					<td>
						<?php if(!(isset($options['title']) && $options['title'] != '')) { ?>
						<span class='small_text'>
							<?=(isset($data['default_options'][$k]) && isset($data['default_options'][$k]['title']) ? "(".$data['default_options'][$k]['title'].")" : '');?><br>
						</span>
						<?php } ?>
						<input type="text" name="edit_options[<?=$options['formular_subitem_id'];?>]" class="product_options_input" value="<?=(isset($options['title']) ? $options['title'] : '');?>">
						<span class='spacer_delete'></span>
						<a href='#'  class="product_options_delete" onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>
					</td>
				</tr>
				<?php 
				$filter_id++;
				endforeach; ?>
			<?php else: ?>
				<tr class='option_row'>
					<th><?=$this->lang->line('option');?> <span class='nummer'>1</span></th>
					<td>
						<input type="text" name="options[1]" value="" class="product_options_input">
						<span class='spacer_delete'></span>
						<a href='#'  class="product_options_delete" onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>
					</td>
				</tr>
				
				
			<?php endif; ?>
				<tr class='option_row hidden add_row'>
					<th><?=$this->lang->line('option');?> <span class='nummer'>1</span></th>
					<td>
						<input type="text" name="options[]" value="" class="product_options_input">
						<span class='spacer_delete'></span>
						<a href='#'  class="product_options_delete" onclick="return false"><img src="<?php echo SITE_URL . ELEM_DIR ;?>img_admin/delete.png"></a>
					</td>
				</tr>
		
		<tr>
			<th></th>
			<td>
				<div class="orange_button" id='overview'>
					<input type='hidden' name='nummer_items' value='1' class='nummer-items'>
					<div class="orange_button_left"></div>
					<div class="orange_button_con">
						<img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
						<div class="orange_button_space"></div>
						<a href="#" onclick="return false;" class='font_white item_add'><?=$this->lang->line('add_subitem');?></a>
					</div>
					<div class="orange_button_right"></div>
				</div>
			</td>
		</tr>
		

		<tr>
			<th></th>
			<td>
				<input class="save_item_info" type="button" name="save_item_info" value="<?=$this->lang->line('save');?>" id="<?=$data['formular_id'];?>">
			</td>
		</tr>
								
	</table>
				
</div>
	
</form>