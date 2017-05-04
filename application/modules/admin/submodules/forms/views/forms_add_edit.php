<?php require_once ELEM_DIR . 'admin_header.php'; 

$sql = '
UPDATE `form_content`
SET `form_content`.read = 1
WHERE `form_content`.form_id = :form_id
';

$this->db->query($sql, array('form_id' => $this->url->segment(3)));

?>

<div id="container">

	<div id="details">
	
		<form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data">
		
			<div class="menu_float">

				<input class="save" type="submit" name="save" value="<?=$this->lang->line('menu_save');?>">

				<input class="save_and_back" type="submit" name="save_and_back" value="<?=$this->lang->line('menu_save_and_back')?>">
				
				<div class="back_button">
					<a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER;?>" title="<?=$this->lang->line('back_to_overview')?>"><?=$this->lang->line('back_to_overview')?></a>
				</div>

			</div>
			
			<div class="column">
			
				<div class="pie header">
					<div style="position: absolute; left: 11px; top: 11px;" class="sprite sprite-forms"></div>
					<h1><?=$this->lang->line('header_edit');?></h1>
				
				</div>
			
			</div>
			
			<div class="column">
			
				<div class="pie subheader">
				
					<h2><?=$this->lang->line('form_info');?></h2>
					
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
				
					<table>
					
						<?php if($data['formular_id'] != 0){ ?>
							<?=$data['content'];?>
						<?php }else{ ?>

						<?php if (strlen($data['company']) > 0) { ?>
							<tr>
								<th><?=$this->lang->line('company');?></th>
								<td><?=$data['company'];?></td>
							</tr>
						<?php } ?>

							<tr>
								<th><?=$this->lang->line('name');?></th>
								<td><?=$data['gender'] . ' ' . $data['firstname'] . ' ' . ($data['infix'] != '' ? $data['infix'] . ' ' . $data['lastname'] : $data['lastname']);?></td>
							</tr>
							
							<tr>
								<th><?=$this->lang->line('email');?></th>
								<td><a href="mailto:<?=$data['email'];?>"><?=$data['email'];?></td>
							</tr>
							
							<tr>
								<th><?=$this->lang->line('telephone');?></th>
								<td><?=$data['telephone'];?></td>
							</tr>
							
						<?php if (strlen($data['subject']) > 0) { ?>
							<tr>
								<th>Subject</th>
								<td><?=$data['subject'];?></td>
							</tr>
						<?php } ?>
            
						<?php if (strlen($data['address']) > 0) { ?>
							<tr>
								<th>Address</th>
								<td><?=$data['address'];?></td>
							</tr>
						<?php } ?>
              
							<tr>
								<th>Message</th>
								<td style="padding: 10px 0;"><?=$data['content'];?></td>
							</tr>
						<?php } ?>
					</table>
				
				</div>
				
			</div>
			
			<div class="column">
			
				<div class="pie subheader">
				
					<h2><?=$this->lang->line('form_settings');?></h2>
					
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
							<th><?=$this->lang->line('archive');?></th>
							<td>
								<?=$this->lang->line('yes');?> <input type="radio" <?=($data['archive'] == 1 ? 'checked="checked"' : '');?> name="archive" value="1">
								<?=$this->lang->line('no');?> <input type="radio" <?=($data['archive'] == 0 ? 'checked="checked"' : '');?> name="archive" value="0">
							</td>
						</tr>
					
					</table>
				
				</div>
				
			</div>
		
		</form>
		
	</div>

</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>