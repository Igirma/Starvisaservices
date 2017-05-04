<!DOCTYPE html>
<html lang="<?=LANG_CODE;?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />
  <title>Admin</title>
  <link rel="stylesheet" type="text/css" media="screen" href="<?=SITE_URL . ELEM_DIR . 'css/admin.css';?>">
  <link rel="stylesheet" type="text/css" media="screen" href="<?=SITE_URL . ELEM_DIR . 'plugins/noty/css/noty.min.css';?>">
  <link rel="stylesheet" type="text/css" media="screen" href="<?=SITE_URL . ELEM_DIR . 'plugins/fancybox/source/jquery.fancybox.css';?>">
  <link rel="stylesheet" type="text/css" media="screen" href="<?=SITE_URL . ELEM_DIR . 'plugins/qtip/jquery.qtip.min.css';?>">
  <link rel="stylesheet" type="text/css" media="screen" href="<?=SITE_URL . ELEM_DIR . 'plugins/jcrop/css/jquery.jcrop.min.css';?>">
  <link rel="stylesheet" type="text/css" media="screen" href="<?=SITE_URL . ELEM_DIR . 'css/jquery.ui.timepicker.css';?>">

  <script src="<?=SITE_URL . ELEM_DIR . 'js/min.js';?>"></script>
  <script src="<?=SITE_URL . ELEM_DIR . 'plugins/fancybox/source/jquery.fancybox.pack.js';?>"></script>
  <script src="<?=SITE_URL . ELEM_DIR . 'plugins/qtip/jquery.qtip.min.js';?>"></script>
  <script src="<?=SITE_URL . ELEM_DIR . 'plugins/jcrop/js/jquery.jcrop.min.js';?>"></script>
  <script src="<?=SITE_URL . ELEM_DIR . 'plugins/ckeditor/ckeditor.js';?>"></script>
  <script src="<?=SITE_URL . ELEM_DIR . 'plugins/ckeditor/adapters/jquery.js';?>"></script>
  <script type="text/javascript" src="<?=SITE_URL . ELEM_DIR;?>js/colorpicker.js"></script>
  <script type="text/javascript" src="<?=SITE_URL . ELEM_DIR;?>js/eye.js"></script>
  <script type="text/javascript" src="<?=SITE_URL . ELEM_DIR;?>js/utils.js"></script>
  <script type="text/javascript" src="<?=SITE_URL . ELEM_DIR;?>js/phery.min.js"></script>
  <script type="text/javascript" src="<?=SITE_URL . ELEM_DIR;?>js/jquery.ui.timepicker.js"></script>
  <script type="text/javascript" src="<?=SITE_URL . ELEM_DIR;?>js/layout.js?ver=1.0.2"></script>
  <script src="<?=SITE_URL . ELEM_DIR . 'js/init_admin.js';?>"></script>

  <style type="text/css">
    .pie{behavior: url("<?=SITE_URL . ELEM_DIR . 'css/'?>PIE.htc");}
	#menu::-webkit-scrollbar {
		width: 3px;
		background: #000;
	}

	#menu::-webkit-scrollbar-thumb {
		background: #0DAAA7;
	}

	#menu::-webkit-scrollbar-corner {
		background: #000;
	}
  </style>
  
</head>
<body>

<?php echo $this->alert->show(); ?>

<div id="all">

<?php include(ELEM_DIR . 'admin_menu.php'); ?>

<?php include(ELEM_DIR . 'admin_top_menu.php'); ?>