<?php require_once ELEM_DIR . 'admin_header.php';
if(!empty($_POST))
{
	$data = $_POST;
}
 ?>

<?=validation_errors();?>

<div id="container">

	<div id="details">
	
		<form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="news_edit_add">
		
			<input type="hidden" name="news[news_id]" value="<?=(isset($data['news']['news_id']) ? $data['news']['news_id'] : '');?>">
			
			<div class="menu_float">

				<input class="save" type="submit" name="save" value="<?=$this->lang->line('menu_save')?>">

				<input class="save_and_back" type="submit" name="save_and_back" value="<?=$this->lang->line('menu_save_and_back')?>">
				
				<div class="back_button">
					<a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER;?>" title="<?=$this->lang->line('back_to_overview')?>"><?=$this->lang->line('back_to_overview')?></a>
				</div>

			</div>
			
			<div class="column">
			
				<?php if($this->url->segment(2) == 'add'): ?>

						<div class="pie header">
							<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-dashboard"></div>
							<h1><?=$this->lang->line('add_languages')?></h1>
						
						</div>

				<?php endif; ?>
				
				<?php if($this->url->segment(2) == 'edit'): ?>

						<div class="pie header">
							<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-dashboard"></div>
							<h1><?=$this->lang->line('edit_languages')?></h1>
							
						</div>

				<?php endif; ?>
			
			</div>
			
			<div class="column">
			
				<div class="pie subheader">
				
					<h2>Informatie</h2>
					
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
							<th>Titel</th>
							<td><input type="text" name="name" value="<?=(isset($data['name']) ? $data['name'] : '');?>"></td>
						</tr>
						
						<tr>
							<th>Code</th>
							<td><input type="text" name="code" value="<?=(isset($data['code']) ? $data['code'] : '');?>"></td>
						</tr>
					
					</table>
				
				</div>
				
			</div>
			
		</form>
	
	</div>

</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>