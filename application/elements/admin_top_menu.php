<?php

$menu = fetch_admin_top_menu();

?>

<?php if(isset($_SESSION['login_salt']) && $this->url->segment(1) != 'login'): ?>
	<div id="top_menu" class="pie">
		<div id="top_menu_items">
			<ul>
				
				<div class="top_menu_line_dark"></div>

				<?php if (!empty($menu['items']) && count($menu['items']) > 0) { foreach($menu['items'] as $menu_item): ?>
				
				<li class="pie">
					<a href="<?=SITE_URL . LANG_CODE . '/admin/' . $menu_item['dirname']?>">
						<img alt="<?=$menu_item['dirname'];?>" <?=($menu_item['dirname'] == 'languages' ? 'style="height: 11px; margin-top: 12px;"' : '');?> src="<?=SITE_URL . ELEM_DIR . 'img_admin/' . ($menu_item['dirname'] == 'languages' ? LANG_CODE : $menu_item['dirname']) . '.png'?>"><?=$this->lang->line('module_' . $menu_item['dirname']);?>
					</a>
				</li>
				
				<?php endforeach; } ?>
				
				<div class="top_menu_line_light"></div>
				
			</ul>
			<div class="clear"></div>
		</div>
	</div>

	
<?php endif; ?>