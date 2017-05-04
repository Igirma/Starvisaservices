<?php require_once ELEM_DIR . 'header.php'; ?>
<?php require_once ELEM_DIR . 'front_menu.php'; ?>
<?php unset($_SESSION['invitation']); ?>

<header class="full-width selector">
  <div class="container">
    <div class="row">
	  





      <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 left resp-left">
		<div class="resp-spacer"></div>
		<img src="<?php echo SITE_URL . IMG_DIR . 'earth.png'; ?>" alt="<?php echo $settings['company']; ?>" class="img-responsive map resp-img-right">
	  </div>









      <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 left" style="text-align: center; background-color: #FFF; padding-top: 10px; padding-bottom: 20px">
      
        <div class="selector-block color-blue">
          <div class="title background-blue color-white" style="font-weight: 700">VISA SERVICES</div>

<?php
          echo Phery::form_for(SITE_URL . $data['page']['slug'] . '/process', 'configurator', array(
              'id' => 'config',
              'class' => 'configurator',
              'data-type' => 'json',
              'submit' => array('disabled' => false, 'all' => true)
          )) . PHP_EOL;

          echo Phery::select_for('ajax_select', $data['users_destination_id'], array(
              'phery-type' => 'json',
              'target' => SITE_URL . $data['page']['slug'] . '/ajax',
              'encoding' => 'UTF-8',
              'method' => 'POST',
              'name' => 'users_destination_id',
              'class' => 'form-control ajax-select users_destination_id',
			  'onChange' => 'resetInvite()'
          )) . PHP_EOL;

          echo Phery::select_for('ajax_select', array(0 => 'Select Nationality'), array(
              'phery-type' => 'json',
              'target' => SITE_URL . $data['page']['slug'] . '/ajax',
              'encoding' => 'UTF-8',
              'method' => 'POST',
              'name' => 'users_nationality_id',
              'class' => 'form-control ajax-select users_nationality_id'
          )) . PHP_EOL;

          echo Phery::select_for('ajax_select', array(0 => 'Select Type'), array(
              'phery-type' => 'json',
              'target' => SITE_URL . $data['page']['slug'] . '/ajax',
              'encoding' => 'UTF-8',
              'method' => 'POST',
              'name' => 'users_type_id',
              'class' => 'form-control ajax-select users_type_id',
			  'onMouseOver' => 'toggleInvite()',
			  'onChange' => 'toggleInvite()',
			  'onClick' => 'toggleInvite()'
          )) . PHP_EOL;
?>
		<select id="invitations" name="users_option_id" class="form-control ajax-select users_option_id" data-phery-remote="ajax_select" data-phery-target="http://visa.itforlondon.com/home/ajax" data-phery-type="json" data-phery-method="POST" style="display: none">
			<option value="-1">Select Invitation</option>
			<option value="2">I need invitation letter!</option>
			<option value="3">I have my own invitation letter!</option>
		</select>
          <br>
		  <input type="checkbox" name="users_conditions_agree" data-phery-target="<?php echo SITE_URL . $data['slugs']['home'] . '/ajax'; ?>" data-phery-type="json" data-phery-method="POST"> I agree with the <a alink="#069" href="Terms_Conditions.pdf" target="_blank" style="color: #069;text-decoration: underline;">Terms and Conditions</a>
		  <div class="spacer-15px"></div>
          <div class="alert hidden alert-danger messages" role="alert"></div>
          <button type="submit" class="btn btn-primary btn-md" data-loading-text="Please wait..." style="font-size: 16px;">Get Your Visa</button>
<?php 
          echo '</form>' . PHP_EOL;
?>

        </div>
      
      </div>










		<div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 left">
		<div class="resp-spacer"></div>
		<div class="home-contact">
			<span style="font-size:120%;font-weight:700;"><div class="home-contact-title">Telephone</div>
			+44 20 7487 3544</span>
	
			<div class="home-contact-subtitle">Office Hours</div>
			Mon-Fri 9 <a style="font-size: 13px;">AM</a>-6 <a style="font-size: 13px;">PM</a><br />
			
			<div class="home-contact-subtitle">Address</div>
			Star Visa Services Ltd<br />
			Lower Ground Floor<br />
			42 Manchester Street<br />
			London W1U 7LW
		
			<div class="home-contact-subtitle">Email</div>
			<a href="mailto:info@starvisaservices.co.uk">info@starvisaservices.co.uk</a><br /><br/>

			<a target="_blank" href="https://maps.google.com/maps?cid=7569416931143354636&_ga=1.15624240.782239101.1487847304" alt="Star Visa Services on Google Maps"><img src="<?php echo SITE_URL . IMG_DIR . 'google_find.png'; ?>" alt="Find us on Google Maps" style="width: 16px; height: 16px"> Find us on Google Maps</a>
		</div>
	  </div>
















    </div>
  </div>
</header>



<section class="features">
  <div class="features-container">
		<?php for ($i = 1; $i < 6; $i++) { ?>
		<div class="features-item">
			<div class="features-aligner">
				<div class="features-title"><?php echo $data['page']['title_' . $i]; ?></div>
				<?php echo $data['page']['subtitle_' . $i]; ?>
			</div>
		</div>
		<?php } ?>
		<div class="clear"></div>
  </div>
</section>




<section class="full-width about-content">
  <div class="container">
    <div class="row">
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="block background-darkbrown">
<?php if (isset($data['about']) && $data['about'] !== false) { ?>
          <header>
            <h3><?php echo $data['about']['content_title']; ?></h3>
          </header>
          <article><?php echo $data['about']['content_description']; ?></article>
<?php } ?>
        </div>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="block background-darkbrown">
<?php if (isset($data['news']) && $data['news'] !== false) { ?>
          <header>
            <h3><?php echo $data['news']['content_title']; ?></h3>
          </header>
          <div class="full-width news-list">
<?php if (isset($data['news']['news_items']) && $data['news']['news_items'] !== false) { ?>
<?php foreach ($data['news']['news_items'] as $news) { ?>

            <div class="news-item">
              <div class="news-date background-blue color-white"><?php echo date('M', $news['start_date']); ?><br><span><?php echo date('d', $news['start_date']); ?></span></div>
              <div class="news-content">
                <h4><?php echo $news['title']; ?></h4>
                <p><?php echo $news['description']; ?></p>
              </div>
            </div>

<?php } ?>
<?php } else { ?>
            <p class="none">There are no current news items.</p>
<?php } ?>
          </div>
<?php } ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once ELEM_DIR . 'front_footer_menu.php'; ?>
<?php require_once ELEM_DIR . 'footer.php'; ?>