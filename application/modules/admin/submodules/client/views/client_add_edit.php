<?php require_once(ELEM_DIR . 'admin_header.php');

if(!empty($_POST))
{
	$data['client'] = $_POST;
}

?>

<?=validation_errors();?>

<div id="container">

	<div id="details">
	
		<form method="post" action="<?=$this->url->current?>">
		
			<div class="menu_float">

				<input class="save" type="submit" name="save" value="<?=$this->lang->line('save_client')?>">

				<input class="save_and_back" type="submit" name="save_and_back" value="<?=$this->lang->line('menu_save_and_back')?>">
				
				<div class="back_button">
					<a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER;?>" title="<?=$this->lang->line('back')?>"><?=$this->lang->line('back_to_overview')?></a>
				</div>

			</div>
			
			<div class="column">
			
				<?php if($this->url->segment(2) == 'edit'): ?>

						<div class="header">
						
							<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-client"></div>
							<h1><?=$this->lang->line('edit_client')?></h1>
						
						</div>

				<?php endif; ?>
				
				<?php if($this->url->segment(2) == 'add'): ?>

						<div class="header">
						
							<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-client"></div>
							<h1><?=$this->lang->line('add_client')?></h1>
						
						</div>

				<?php endif; ?>

			
			</div>
			
			<div class="column">
				
				<div class="subheader">
					<h2><?=$this->lang->line('settings')?></h2>
				</div>
				
				<div class="subcolumn">

					<input type="hidden" name="client_id" value="<?=(isset($data['client']['client_id']) ? $data['client']['client_id'] : '');?>">
					<table>

						<tr class='hidden'>
							<th><?=$this->lang->line('rights')?></th>
							<td>
								<select name="rights_id">
								
								<?php foreach($data['all_rights'] as $right): ?>

									<option <?=(isset($data['client']['rights_id']) && $right['rights_id'] == $data['client']['rights_id'] ? 'selected="selected"' : '')?> value="<?=$right['rights_id']?>"><?=$right['name']?></option>
									
								<?php endforeach; ?>
								
								</select>
							</td>
						</tr>
						<tr>
							<th><?=$this->lang->line('clientname')?></th>
							<td><input type="text" name="clientname" autocomplete="off" value="<?=(isset($data['client']['clientname']) ? $data['client']['clientname'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('email')?></th>
							<td><input type="text" name="email" autocomplete="off" value="<?=(isset($data['client']['email']) ? $data['client']['email'] : '');?>"></td>
						</tr>

						<tr>
							<th><?=$this->lang->line('firstname')?></th>
							<td><input type="text" name="firstname" autocomplete="off" value="<?=(isset($data['client']['firstname']) ? $data['client']['firstname'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('lastname')?></th>
							<td><input type="text" name="lastname" autocomplete="off" value="<?=(isset($data['client']['lastname']) ? $data['client']['lastname'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('company')?></th>
							<td><input type="text" name="company" autocomplete="off" value="<?=(isset($data['client']['company']) ? $data['client']['company'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('phone')?></th>
							<td><input type="text" name="phone" autocomplete="off" value="<?=(isset($data['client']['phone']) ? $data['client']['phone'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('street')?></th>
							<td><input type="text" name="street" autocomplete="off" value="<?=(isset($data['client']['street']) ? $data['client']['street'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('housenumber')?></th>
							<td><input type="text" name="housenumber" autocomplete="off" value="<?=(isset($data['client']['housenumber']) ? $data['client']['housenumber'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('postal')?></th>
							<td><input type="text" name="postal" autocomplete="off" value="<?=(isset($data['client']['postal']) ? $data['client']['postal'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('city')?></th>
							<td><input type="text" name="city" autocomplete="off" value="<?=(isset($data['client']['city']) ? $data['client']['city'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('country')?></th>
							<td><input type="text" name="country" autocomplete="off" value="<?=(isset($data['client']['country']) ? $data['client']['country'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('delivery_address')?></th>
							<td>
							<?php if(isset($data['client']['delivery_address']) && $data['client']['delivery_address'] == 1){ ?>
								<input type="checkbox" name="delivery_address" autocomplete="off" value="1" checked>
							<?php }else{ ?>
								<input type="checkbox" name="delivery_address" autocomplete="off" value="1">
							<?php } ?>
							</td>
						</tr>
						<tr>
							<th><?=$this->lang->line('delivery_street')?></th>
							<td><input type="text" name="delivery_street" autocomplete="off" value="<?=(isset($data['client']['delivery_street']) ? $data['client']['delivery_street'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('delivery_housenumber')?></th>
							<td><input type="text" name="delivery_housenumber" autocomplete="off" value="<?=(isset($data['client']['delivery_housenumber']) ? $data['client']['delivery_housenumber'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('delivery_postal')?></th>
							<td><input type="text" name="delivery_postal" autocomplete="off" value="<?=(isset($data['client']['delivery_postal']) ? $data['client']['delivery_postal'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('delivery_city')?></th>
							<td><input type="text" name="delivery_city" autocomplete="off" value="<?=(isset($data['client']['delivery_city']) ? $data['client']['delivery_city'] : '');?>"></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('delivery_country')?></th>
							<td><input type="text" name="delivery_country" autocomplete="off" value="<?=(isset($data['client']['delivery_country']) ? $data['client']['delivery_country'] : '');?>"></td>
						</tr>
						
						<?php if($this->url->segment(2) == 'edit'): ?>

							<tr>
								<th><?=$this->lang->line('old_password')?></th>
								<td><input style="width: 97%;" type="password" autocomplete="off" name="old_password" value=""></td>
							</tr>
						
						<?php endif; ?>
						
						<tr>
							<th><?=$this->lang->line('password')?></th>
							<td><input style="width: 97%;" type="password" autocomplete="off" name="password" value=""></td>
						</tr>
						<tr>
							<th><?=$this->lang->line('password_check')?></th>
							<td><input style="width: 97%;" type="password" autocomplete="off" name="password_check" value=""></td>
						</tr>
						
					</table>
					
				</div>
				
			</div>
      
      <div class="column">
      
      <a id="anchor_media"></a>
    
        <div class="subheader">
      
          <h2><?=$this->lang->line('images');?></h2>
          
          <div class="options">
          
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

          <?php if(!empty($data['brand']['media']['photos'])): ?>
          
            <?php $x = 0; ?>
            <?php foreach($data['brand']['media']['photos'] as $media): ?>
              <div class="thumb">
              
                <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['brand']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><img src="<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER . '/thumb/' . $media['filename'];?>?<?=rand(0, 10100);?>" alt="<?=$media['alt'];?>"></a>
                <input type="hidden" name="media[][media_id]" value="<?=$media['media_id'];?>">
              
                <a href="<?=SITE_URL . 'admin/' . CONTROLLER . '/delete_media/' . $media['media_id'] . '/' . $media['table_id'] . '/' . $this->url->segment(4);?>" class="delete">
                  <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/delete.png">
                </a>
                
                <div class="set_thumbnail">
                
                  <input type="radio" name="set_thumbnail" value="<?=$media['media_id'];?>" <?=($media['album_thumb'] == 1 ? 'checked="checked"' : '');?>><small> Thumb</small>
                
                </div>
                
                <div class="filename">
                
                  <?=(strlen($media['filename']) > 15 ? substr($media['filename'], 0, 15) . '...' : $media['filename']);?>
                  
                </div>
                
                <div class="order_media">
                
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/left/<?=$media['table_id'];?>/<?=$data['brand']['language_id'];?>/<?=$media['order']?>" title="Move left"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/left.png"></a>
                  <a class="order_media" href="<?=SITE_URL;?>admin/<?=CONTROLLER?>/order_media/right/<?=$media['table_id'];?>/<?=$data['brand']['language_id'];?>/<?=$media['order']?>" title="Move right"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/right.png"></a>
                
                </div>
                
                <div class="edit_image">
                
                  <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit.png">
                  <a href="<?=SITE_URL . LANG_CODE . '/admin/fetch_media_ajax/' . CONTROLLER . '/' . $media['media_id'] . '/' . $data['brand']['language_id'];?>" class="modal_ajax" title="<?=$media['filename'];?>"><?=$this->lang->line('edit_image');?></a>
                
                </div>

              </div>
              
              <?php $x++; ?>
            
            <?php endforeach; ?>
          
          <?php endif; ?>
          
          <div class="clear"></div>
        
          <table>
            
            <tr>
            
              <th>
                <?=$this->lang->line('new_photo');?>
              </th>
              <td>
              
                <div class="file_container">
                  <input type="text" readonly="readonly" class="file_replace_text">
                  <input type="button" class="file_replace" value="<?=$this->lang->line('browse');?>">
                  <input type="submit" name="upload" value="<?=$this->lang->line('upload');?>">
                  <input type="file" accept="image/*" name="photo[]" multiple="multiple" class="file_hack">
                </div>
              
              </td>
            
            </tr>
          
          </table>
        
        </div>
      
      </div>
		
		</form>
	
	</div>

</div>


<?php require_once(ELEM_DIR . 'admin_footer.php'); ?>