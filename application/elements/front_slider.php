<?php if (isset($data['slider']) && $data['slider'] !== false && count($data['slider']) > 0) { ?>
<style>
<?php foreach ($data['slider'] as $k => $slider) { ?>
.carousel .item.item-<?php echo $k; ?> {
  background-image: url(<?php echo SITE_URL . MEDIA_DIR . 'sliders/slider/' . str_replace(' ', '%20', $slider['media'][0]['filename']); ?>);
}
<?php } ?>
</style>

<div class="carousel-background hidden-print">
<div class="carousel-wrap">
  <div id="header-carousel" class="carousel slide carousel-fade" data-ride="carousel">
    <div class="carousel-inner">
<?php foreach ($data['slider'] as $k => $slider) { ?>
      <div class="item item-<?php echo $k; if ($k == 0) echo ' active'; ?>" data-index="<?php echo $k; ?>">
        <div class="caption-t">
          <div class="caption-c">
            <div class="container">
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

<?php if (strlen($slider['description']) > 0) { ?>
            <div class="carousel-caption">
              <div class="carousel-title"><span><?php echo $slider['title']; ?></span></div>
<?php if (strlen($slider['description']) > 0) { ?>
              <div class="carousel-description"><span><?php echo $slider['description']; ?></span></div>
<?php } ?>
<?php if (strlen($slider['button_text']) > 0 && (strlen($slider['button_url']) > 0 || (int) $slider['button_page_id'] > 0)) { ?>
              <div class="carousel-button">
<?php if ((int) $slider['button_page_id'] > 0) { ?>
                <a href="<?php echo SITE_URL . (LANG_CODE != 'en' ? LANG_CODE . '/' : '') . subSlug($slider['button_page_id']); ?>"><?php echo $slider['button_text']; ?></a>
<?php } else { ?>
                <a href="<?php echo $slider['button_url']; ?>"><?php echo $slider['button_text']; ?></a>
<?php } ?>
              </div>
<?php } ?>
            </div>
<?php } ?>

                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="overlay"></div>
      </div>
<?php } ?>
    </div>
<?php if (count($data['slider']) > 1) { ?>
    <a class="left carousel-control" href="#header-carousel" data-slide="prev">
      <span class="tbl">
        <span class="cell">
          <i class="entypo icon">&#59237;</i>
        </span>
      </span>
    </a>
    <a class="right carousel-control" href="#header-carousel" data-slide="next">
      <span class="tbl">
        <span class="cell">
          <i class="entypo icon">&#59238;</i>
        </span>
      </span>
    </a>
<?php } ?>
<?php if (count($data['slider']) > 1) { ?>
    <div class="carousel-indicators-wrap hidden">
    <ol class="carousel-indicators">
<?php for ($i = 0; $i <= (count($data['slider']) - 1); $i++) { ?>
      <li data-target="#header-carousel" data-slide-to="<?php echo $i; ?>"<?php if ($i == 0) echo ' class="active"'; ?>></li>
<?php } ?>
    </ol>
    </div>
<?php } ?>

  </div>
</div>
</div>
<?php } ?>