<?php if (isLoggedIn()) { ?>
<div class="loggedin-menu full-width">
  <div class="container">
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
<?php if (isAdmin()) { ?>
        <a href="<?php echo SITE_URL . getSlugOnController('admin_page'); ?>">Users management</a> | 
<?php } else { ?>
        <a href="<?php echo SITE_URL . getSlugOnController('profile_edit') . '/edit/personal/' . $data['loguser']['profile_id']; ?>"><?php echo (isset($data['loguser']['surename']) && strlen($data['loguser']['surename']) > 0 ? 'Logged in as: <b>' . $data['loguser']['surename'] . ' ' . $data['loguser']['forename'] . '</b>' : 'Application form'); ?></a> | 
<?php } ?>
        <a href="<?php echo SITE_URL . getSlugOnController('profile') . '/logout'; ?>">Log-out</a>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php //debug($this->users->user); ?>