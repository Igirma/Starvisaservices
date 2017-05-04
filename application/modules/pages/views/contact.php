<?php require_once ELEM_DIR . 'header.php'; ?>
<?php require_once ELEM_DIR . 'front_menu.php'; ?>

<section class="full-width about-content contact-content">
  <div class="container">
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="block background-darkbrown">
          <header>
            <h3><?php echo $data['page']['content_title']; ?></h3>
          </header>

          <div class="alert hidden" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
            <div class="message">Text text</div>
          </div>

<?php
echo Phery::form_for(SITE_URL . $data['page']['slug'] . '/contact_form', 'contact_form', array(
    'class' => 'phery-form contact-form generic-form',
    'id' => 'contact-form',
    'role' => 'form',
    'data-type' => 'json',
    'encoding' => 'UTF-8',
    'submit' => array('disabled' => true, 'all' => true)
)) . PHP_EOL;
?>
        <div class="row">
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
              <label for="name">Your name*</label>
              <input type="text" class="form-control" id="name" name="name" placeholder="Your name (required)" data-rule-required="true" data-msg-required="Please fill in your name">
            </div>
            <div class="form-group">
              <label for="email">Your e-mail*</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Your e-mail (required)" data-rule-required="true" data-rule-email="true" data-msg-required="Please fill in your e-mail" data-msg-email="Invalid e-mail address">
            </div>
            <div class="form-group">
              <label for="phone">Your telephone</label>
              <input type="text" class="form-control" id="phone" name="phone" placeholder="Your telephone">
            </div>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
              <label for="address">Your address</label>
              <textarea class="form-control" id="address" name="address" placeholder="Your address"></textarea>
            </div>
            <div class="form-group">
              <label for="message">Your enquiry*</label>
              <textarea class="form-control" id="message" name="message" placeholder="Your enquiry (required)" data-rule-required="true" data-msg-required="Please fill in your enquiry"></textarea>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
            <button type="submit" class="btn btn-primary" data-loading-text="Sending...">Send</button>
          </div>
        </div>
<?php echo '</form>' . PHP_EOL; ?>


        </div>
      </div>
    </div>
  </div>
</section>

<section class="full-width about-content contact-content">
  <div class="container">
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="block background-darkbrown">
          <article><?php echo $data['page']['content_text']; ?></article>
        </div>
      </div>
    </div>
  </div>
</section>


<?php require_once ELEM_DIR . 'front_footer_menu.php'; ?>
<?php require_once ELEM_DIR . 'footer.php'; ?>