<?php
	$submenus = array();
	$menu_id = 0;
?>
<div class="top-menu">
	<div class="top-menu-container">
		<div class="menu-left">
			<a href="<?php echo SITE_URL; ?>" class="logo">
				<img src="<?php echo SITE_URL . ELEM_DIR . 'img_front/logo.png' ?>" alt="<?php echo $settings['company']; ?>" class="logo-img">
			</a>
		</div>
		<div class="top-menu-toggler" onClick="$('#menu-right').slideToggle()"><img src="<?php echo SITE_URL . ELEM_DIR . 'img_front/navicon.png' ?>" alt="Menu"></div>
<?php if (isset($this->menu->items) && $this->menu->items !== false && count($this->menu->items)) { ?>
	<div class="menu-right" id="menu-right">
		<?php foreach ($this->menu->items as $k => $menu) { ?>
			<a href="<?php echo $menu['menu_url']; ?>"<?php if ($menu['is_active']) echo ' class="active"'; ?> class="menu-parent" id="parentmenu<?php echo $menu_id; ?>" <?php echo $menu['children'] ? "onMouseOver='displaySubMenu(this, {$menu_id})' onMouseOut='hideSubMenu({$menu_id}, 100)'" : ''; ?>>
				<?php echo $menu['menu_title']; ?>
				<?php
					if($menu['children']) {
						$submenus[$menu_id] = $menu['children'];
					}
					$menu_id++;
				?>
			</a>
		<?php } ?>
	</div>
<?php } // any menu? ?>
	<?php
		if(count($submenus) > 0) {
			foreach($submenus as $key => $submenu) {
				echo '<div class="submenu" id="submenu' . $key . '" onMouseOut="hideSubMenu(' . $key . ', 100)">' . PHP_EOL;
				foreach($submenu as $submenu_item) {
					echo '<div class="submenu-item"><a href="' . $submenu_item['menu_slug'] . '">' . $submenu_item['menu_title'] . '</a></div>' . PHP_EOL;
				}
				echo '</div>' . PHP_EOL;
			}
		}
	?>
	</div>
</div>

