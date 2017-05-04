<?php require_once ELEM_DIR . 'header.php'; ?>
<head>
<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1300488856684499'); // Insert your pixel ID here.
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=1300488856684499&ev=PageView&noscript=1"
/></noscript>
<!-- DO NOT MODIFY -->
<!-- End Facebook Pixel Code -->
</head>
<script>fbq('track', '<EVENT_NAME>');</script>

<?php require_once ELEM_DIR . 'front_menu.php'; ?>


<?php //debug($data['user']); ?>

<header class="full-width selector selector-small background-blue">
  <div style="height: 10px"></div>
  <div class="container">
    <div class="row">
      <div class="col-lg-15 col-md-4 col-sm-4 col-xs-12">
<?php
	if(!isset($_SESSION['preview'])) {
          echo Phery::select_for('ajax_select', $data['users_destination_id'], array(
              'phery-type' => 'json',
              'target' => SITE_URL . $data['slugs']['home'] . '/ajax',
              'encoding' => 'UTF-8',
              'method' => 'POST',
              'name' => 'users_destination_id',
              'selected' => $data['user']['selected']['users_destination_id'],
              'args' => array('show_content' => 1),
              'class' => 'form-control ajax-select users_destination_id',
			  'onChange' => 'resetInvite()'
          )) . PHP_EOL;
	}
	else {
		echo '<span style="color: #FFF"><strong>Preview Mode:<br></strong>' . htmlspecialchars($_SESSION['preview']['destination_name']) . '<br><em><a style="color: #FFF" href="' . SITE_URL . LANG_CODE . '/admin/destinations/exit_preview">Exit Preview</a></em></span>';
	}
?>
      </div>
      <div class="col-lg-15 col-md-4 col-sm-4 col-xs-12">
<?php
          echo Phery::select_for('ajax_select', $data['users_nationality_id'], array(
              'phery-type' => 'json',
              'target' => SITE_URL . $data['slugs']['home'] . '/ajax',
              'encoding' => 'UTF-8',
              'method' => 'POST',
              'name' => 'users_nationality_id',
              'selected' => $data['user']['selected']['users_nationality_id'],
              'args' => array('show_content' => 1),
              'class' => 'form-control ajax-select users_nationality_id'
          )) . PHP_EOL;
?>
      </div>
      <div class="col-lg-15 col-md-4 col-sm-4 col-xs-12">
<?php
          echo Phery::select_for('ajax_select', $data['users_type_id'], array(
              'phery-type' => 'json',
              'target' => SITE_URL . $data['slugs']['home'] . '/ajax',
              'encoding' => 'UTF-8',
              'method' => 'POST',
              'name' => 'users_type_id',
			  'id' => 'users_type_id',
              'args' => array('show_content' => 1),
              'selected' => $data['user']['selected']['users_type_id'],
              'class' => 'form-control ajax-select users_type_id',
			  'onMouseOver' => 'toggleInvite()',
			  'onChange' => 'toggleInvite()',
			  'onClick' => 'toggleInvite()'
          )) . PHP_EOL;
?>
      </div>
	<div class="col-lg-15 col-md-4 col-sm-4 col-xs-12">
		<select id="invitations" name="users_option_id<?php if(!isset($_SESSION['invitation'])) { echo "_disabled"; } ?>" class="form-control ajax-select users_option_id" data-phery-remote="ajax_select" data-phery-target="<?php echo SITE_URL . $data['slugs']['home'] . '/ajax/'; ?>" data-phery-type="json" data-phery-args="{"show_content":1}" data-phery-method="POST" <?php if(!isset($_SESSION['invitation'])) { echo 'style="display: none"'; } ?>>
			<option value="2" <?php if($_SESSION['invitation'] == 2) { echo 'selected="selected"'; } ?>>I need invitation letter!</option>
			<option value="3" <?php if($_SESSION['invitation'] == 3) { echo 'selected="selected"'; } ?>>I have my own invitation letter!</option>
		</select>
	</div>
    <button class="btn btn-primary btn-forward col-lg-15 col-md-4 col-sm-4 col-xs-12" onClick="ajaxProcess()">Go &raquo;</button>
    </div>
  </div>
</header>

<script type="text/javascript">
	function ajaxProcess() {
		if($(".users_type_id").val() != 0 && $(".users_nationality_id").val() != 0 && $(".users_destination_id").val() != 0) {
			$('#users_type_id').change();
			$('#invitations').change();
			window.location = location.href;
		}
		else {
			if($(".users_type_id").val() == 0) {
				$(".users_type_id").fadeOut(500);
				setTimeout(function() { $(".users_type_id").fadeIn(500); }, 500);
			}
			if($(".users_nationality_id").val() == 0) {
				$(".users_nationality_id").fadeOut(500);
				setTimeout(function() { $(".users_nationality_id").fadeIn(500); }, 500);
			}
			if($(".users_destination_id").val() == 0) {
				$(".users_destination_id").fadeOut(500);
				setTimeout(function() { $(".users_destination_id").fadeIn(500); }, 500);
			}
		}
	}
</script>

<section class="full-width steps-content">
  <div class="container">
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
<script>
fbq('track', 'ViewContent', {
users_destination_id: Russia,
});
</script>
        <div id="content" class="block">
        <?php if (strlen($data['costs']) > 0) { ?>
        <?php echo $data['costs']; ?>
        <?php } elseif (isset($_SESSION['preview'])) { ?>
		<br><br>
		<center>
			You are currently viewing this page in <strong>preview mode</strong>.<br>
			To proceed please make the required selections in the menu above.<br>
			<strong>The application has to be completed before it will go live for regular visitors!</strong>
		</center>
		<br><br><br><br>
		<?php } else { ?>
        <br><br><center>No information for your selection.</center><br><br><br><br>
        <?php } ?>
        </div>
      </div>
    </div>
  </div>
</section>


<?php require_once ELEM_DIR . 'front_footer_menu.php'; ?>
<?php require_once ELEM_DIR . 'footer.php'; ?>