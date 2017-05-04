<?php 
  $settings = getSettings();
  if (!isset($_SESSION['login_salt'])) $_SESSION['login_salt'] = '';
  $meta = getPageHeads(($this->url->segment(0) == '' ? getHomePageSlug(CUR_LANG) : end($this->url->segments)), CUR_LANG); 
  $address = array();
  if (strlen($settings['street']) > 0) {
      $address[] = $settings['street'];
  }
  if (strlen($settings['housenumber']) > 0) {
      $address[] = $settings['housenumber'];
  }
  if (strlen($settings['postal']) > 0) {
      $address[] = $settings['postal'];
  }
  if (strlen($settings['city']) > 0) {
      $address[] = $settings['city'];
  }
  if (strlen($settings['country']) > 0) {
      $address[] = $settings['country'];
  }
?>
<!DOCTYPE html>
<html lang="<?=LANG_CODE;?>" class="controller-<?php echo CONTROLLER; ?>">
<head>
  <base href="<?php echo SITE_URL; ?>">
  <meta charset="UTF-8">
 <title>Star Visa Services Ltd</title>
  <meta name="description" content="Star Visa Services Ltd is a professional visa service company and we have been obtaining business and tourist visas on behalf of different countries' travellers since 2005. 
If you need to travel abroad for business or tourist purposes our company can arrange any type of visa service for all nationalities and destinations that require an entry visa. Star Visa Services Ltd provide professional visa services for both tourist and business visas, no matter where you want to travel to. We can provide you with Chinese visas, Visas for India, Visas for Russia or visas for any other country in the world. ">
  <meta name="keywords" content="<?=(isset($meta[0]['meta_keyw']) ? $meta[0]['meta_keyw'] : '');?>">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,700,300,600,400&subset=latin,latin-ext" rel="stylesheet">
  <link href="<?=SITE_URL . ELEM_DIR . 'css/fonts/fonts.css'?>" rel="stylesheet">
  <link href="<?=SITE_URL . ELEM_DIR . 'css/font-awesome.min.css'?>" rel="stylesheet">
  <link href="<?=SITE_URL . ELEM_DIR . 'css/bootstrap.min.css'?>" rel="stylesheet">
  <link href="<?=SITE_URL . ELEM_DIR . 'css/bootstrap-theme.min.css'?>" rel="stylesheet">
  <link href="<?=SITE_URL . ELEM_DIR . 'css/style.css'?>" rel="stylesheet">
  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
  <![endif]-->
  <script src="<?=SITE_URL . ELEM_DIR . 'js/modernizr.custom.js';?>"></script>
  <script>
    var SITE = '<?php echo SITE_URL; ?>';
    var ADDRESS = '<?php echo implode(' ', $address); ?>';
    var CONTROLLER = '<?php echo CONTROLLER; ?>';
  </script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-92819495-1', 'auto');
  ga('send', 'pageview');

</script>
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

<!--Start of Zendesk Chat Script-->
<script type="text/javascript">
window.$zopim||(function(d,s){var z=$zopim=function(c){
z._.push(c)},$=z.s=
d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');
$.src='https://v2.zopim.com/?4i1DUsZmlihQLrwrCExUtPjRAT8QPRWM';z.t=+new Date;$.
type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');
</script>
<!--End of Zendesk Chat Script-->


</head>
<body class="<?php echo CONTROLLER; ?>">

