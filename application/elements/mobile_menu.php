<?php if (isset($this->menu->items) && $this->menu->items !== false && count($this->menu->items)) { ?>

<div class="mobile-menu full-width">

<div class="mobile-toggler-wrap full-width">
  <div class="toggler">
    <span class="bar"></span>
    <span class="text">Menu</span>
  </div>
</div>

<div class="mobile-menu-wrap full-width">
<?php foreach ($this->menu->items as $k => $menu) { ?>
<a href="<?php echo $menu['menu_url']; ?>"<?php if ($menu['is_active']) echo ' class="active"'; ?>><?php echo $menu['menu_title']; ?></a>
<?php } ?>
<?php if (isset($this->menu->secondmenu) && $this->menu->secondmenu !== false && count($this->menu->secondmenu)) { ?>
<?php foreach ($this->menu->secondmenu as $k => $menu) { ?>
<a href="<?php echo $menu['menu_url']; ?>"<?php if ($menu['is_active']) echo ' class="active"'; ?>><?php echo $menu['menu_title']; ?></a>
<?php } ?>
<?php } ?>
</div>

</div>

<?php } // any menu? ?>
