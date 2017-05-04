<?php require_once ELEM_DIR . 'admin_header.php'; 

$sql = '
UPDATE `review_content`
SET `review_content`.read = 1
WHERE `review_content`.review_id = :review_id
';

$this->db->query($sql, array('review_id' => $this->url->segment(3)));

?>

<div id="container">

	<div id="details">
	
		<form method="post" action="<?=$this->url->current;?>" enctype="multipart/review-data">
		
			<div class="menu_float">

				<input class="save" type="submit" name="save" value="<?=$this->lang->line('menu_save');?>">

				<input class="save_and_back" type="submit" name="save_and_back" value="<?=$this->lang->line('menu_save_and_back')?>">
				
				<div class="back_button">
					<a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER;?>" title="<?=$this->lang->line('back_to_overview')?>"><?=$this->lang->line('back_to_overview')?></a>
				</div>

			</div>
			
			<div class="column">
			
				<div class="pie header">
					<div style="position: absolute; left: 11px; top: 11px;" class="sprite sprite-review"></div>
					<h1><?=$this->lang->line('header_edit');?></h1>
				
				</div>
			
			</div>
			
			<div class="column">
			
				<div class="pie subheader">
				
					<h2><?=$this->lang->line('review_info');?></h2>
					
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
					
						<tr>
							<th><?=$this->lang->line('product');?></th>
							<td><?=$data['product'];?></td>
						</tr>
					
						<tr>
							<th><?=$this->lang->line('name');?></th>
							<td><?=$data['name'];?></td>
						</tr>
							
						<tr>
							<th><?=$this->lang->line('email');?></th>
							<td><a href="mailto:<?=$data['email'];?>"><?=$data['email'];?></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('content');?></th>
							<td style="padding: 10px 0;"><?=$data['content'];?></td>
						</tr>
					
					</table>
				
				</div>
				
			</div>
			
			<div class="column">
			
				<div class="pie subheader">
				
					<h2><?=$this->lang->line('review_settings');?></h2>
					
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
							<th><?=$this->lang->line('active');?></th>
							<td>
								<?=$this->lang->line('yes');?> <input type="radio" <?=($data['active'] == 1 ? 'checked="checked"' : '');?> name="active" value="1">
								<?=$this->lang->line('no');?> <input type="radio" <?=($data['active'] == 0 ? 'checked="checked"' : '');?> name="active" value="0">
							</td>
						</tr>
					
					</table>
				
				</div>
				
			</div>
		
		</form>
		
	</div>

</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>