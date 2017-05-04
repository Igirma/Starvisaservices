<footer id="footer" class="footer">
	<div class="footer-container">
		<div class="footer-col">
			<div class="copyright"><?php echo $settings['kvk']; ?></div>
		</div>
		
		<div class="footer-col">
			<div class="social-media">
				<?php if (strlen($settings['social_facebook']) > 0) { ?>
				<a href="<?php echo $settings['social_facebook']; ?>" target="_blank"><i class="fa fa-facebook"></i></a>
				<?php } if (strlen($settings['social_twitter']) > 0) { ?>
				<a href="<?php echo $settings['social_twitter']; ?>" target="_blank"><i class="fa fa-twitter"></i></a>
				<?php } if (strlen($settings['social_google']) > 0) { ?>
				<a href="<?php echo $settings['social_google']; ?>" target="_blank"><i class="fa fa-google-plus"></i></a>
				<?php } if (strlen($settings['social_pinterest']) > 0) { ?>
				<a href="<?php echo $settings['social_pinterest']; ?>" target="_blank"><i class="fa fa-linkedin"></i></a>
				<?php } ?>
			</div>
		</div>
		
		<div class="footer-col">
			<?php if (isset($this->menu->secondmenu) && $this->menu->secondmenu !== false && count($this->menu->secondmenu)) { ?>
			<div class="menu">
				<?php foreach ($this->menu->secondmenu as $k => $menu) { ?>
					<?php if ($k > 0) echo ' / '; ?><a href="<?php echo $menu['menu_url']; ?>"<?php if ($menu['is_active']) echo ' class="active"'; ?>><?php echo $menu['menu_title']; ?></a>
				<?php } ?>
			<?php } // any menu? ?>
			</div>
		</div>
	</div>
</footer>
