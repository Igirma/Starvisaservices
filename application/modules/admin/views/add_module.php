<?php require_once(ELEM_DIR . 'admin_header.php');

if(!empty($_POST))
{
	$data = $_POST;
}

?>

<?=validation_errors();?>

<div id="container">

	<div id="details">

		<form method="post" action="<?=$this->url->current;?>">
		
			<div class="menu_float">

				<input class="save" type="submit" name="save" value="Module opslaan">

				<input class="save_and_back" type="submit" name="save_and_back" value="<?=$this->lang->line('menu_save_and_back')?>">
				
				<div class="back_button">
					<a href="<?=SITE_URL . LANG_CODE . '/admin/'?>" title="<?=$this->lang->line('back_to_overview')?>"><?=$this->lang->line('back_to_overview')?></a>
				</div>

			</div>
			
			<div class="column">
				
				<div class="pie subheader">
					
					<h2><?=$this->lang->line('add_module');?></h2>
					
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
							<th><?=$this->lang->line('module_name');?></th>
							<td><input type="text" name="name" value="<?=(isset($data['name']) ? $data['name'] : '');?>" /></td>
						</tr>
						
						<tr>
							<th><?=$this->lang->line('module_dirname');?></th>
							<td><input type="text" name="dirname" value="<?=(isset($data['dirname']) ? $data['dirname'] : '');?>" /></td>
						</tr>

					</table>
				
				</div>
			
			</div>
			
		</form>
	
	</div>

</div>

<?php require_once(ELEM_DIR . 'admin_footer.php'); ?>