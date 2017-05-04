<script src="<?=SITE_URL . ELEM_DIR . 'js/jquery-1.10.2.min.js';?>"></script>
<script src="<?=SITE_URL . ELEM_DIR . 'js/jquery.easing-1.3.js';?>"></script>
<script src="<?=SITE_URL . ELEM_DIR . 'js/bootstrap.min.js';?>"></script>
<script src="<?=SITE_URL . ELEM_DIR . 'js/bootstrap-select.js';?>"></script>
<script src="<?=SITE_URL . ELEM_DIR . 'js/jquery.plugin.min.js';?>"></script>
<script src="<?=SITE_URL . ELEM_DIR . 'js/jquery.datepick.min.js';?>"></script>
<script src="<?=SITE_URL . ELEM_DIR . 'js/owl.carousel.min.js';?>"></script>
<script src="<?=SITE_URL . ELEM_DIR . 'js/phery.min.js';?>"></script>
<script src="<?=SITE_URL . ELEM_DIR . 'js/jquery.validate.min.js';?>"></script>
<script src="<?=SITE_URL . ELEM_DIR . 'js/fileinput.min.js';?>"></script>
<?php if (isset($data['payment']) && $data['payment'] || CONTROLLER == 'profile_edit') { ?>
<script src="https://js.stripe.com/v1/"></script>
<script src="<?=SITE_URL . ELEM_DIR . 'js/bootstrap-formhelpers-min.js';?>"></script>
<script src="<?=SITE_URL . ELEM_DIR . 'js/bootstrapValidator-min.js';?>"></script>
<?php } ?>
<script src="<?=SITE_URL . ELEM_DIR . 'js/script.js';?>"></script>
<script type="text/javascript">
	$(window).resize(function() {
		if($('.top-menu').width() > 900)
			$('#menu-right').removeAttr("style");
	});
</script>
<script type="text/javascript">
	$(document).ready(function() {
		$("select").eq(1).click(function() { bringToTop(220); });
	});

	function bringToTop(valueString) {
		var selectElement = $("select").eq(1);
		var ukElement = selectElement.find('option[value="' + valueString + '"]');
		selectElement.find('option').eq(1).before(ukElement); 
	}
</script>
<script type="text/javascript">
	var currentOpened = -1;
	
	function displaySubMenu(parentMenu, parentId) {
		if($(window).width() < 1000) {
			return false;
		}
		if(currentOpened != -1) {
			hideSubMenu(currentOpened);
		}
		
		var parentPosition = $(parentMenu).offset();
		parentPosition.top = parentPosition.top + $(parentMenu).height() + 3;
		$("#submenu" + parentId).attr("style", "left: " + parentPosition.left + "px; top: " + parentPosition.top + "px");
		$("#submenu" + parentId).show();
		
		currentOpened = parentId;
	}
	
	function hideSubMenu(parentId, delayedHide) {
		delayedHide = typeof(delayedHide) == 'undefined' ? 0 : delayedHide;
		if(delayedHide > 50) {
			setTimeout(function() { if(!$("#submenu" + parentId).is(":hover") && !$("#parentmenu" + parentId).is(":hover")) { $("#submenu" + parentId).hide(); currentOpened = -1; } }, delayedHide);
		}
		else {
			$("#submenu" + parentId).hide();
			currentOpened = -1;
		}
	}
</script>
<script type="text/javascript">
	var displayNow = false;

	function toggleInvite() {
		var triggerElem = $(".users_type_id option[value='BOOL_INVITE']");
		if(triggerElem.eq(-1).text() == "TRUE") {
			triggerElem.eq(-1).remove();
			
			if(displayNow == true) {
				return true;
			}
			
			$("#invitations").slideDown(500);
			displayNow = true;
			
			$("#invitations").attr("name", "users_option_id");
		}
		else {
			triggerElem.eq(-1).remove();
			
			if(displayNow == true) {
				return true;
			}
			
			$("#invitations").slideUp(500);
			displayNow = false;
			
			$("#invitations").attr("name", "users_option_id_disabled");
		}
	}
	
	function resetInvite() {
		displayNow = false;
		
		$("#invitations").slideUp(500);
		$("#invitations").attr("name", "users_option_id");
	}
</script>
</body>
</html>