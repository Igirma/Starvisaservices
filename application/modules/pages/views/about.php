<?php require_once ELEM_DIR . 'header.php'; ?>
<?php require_once ELEM_DIR . 'front_menu.php'; ?>

<section class="full-width about-content page-content">
  <div class="container">
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="block background-darkbrown">
          <header>
            <h3><?php echo $data['page']['content_title']; ?></h3>
          </header>
          <article><?php echo $data['page']['content_text']; ?></article>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once ELEM_DIR . 'front_footer_menu.php'; ?>
<?php require_once ELEM_DIR . 'footer.php'; ?>