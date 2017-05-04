<?php 
/**************************
  GENERAL SETTINGS
**************************/

/* (www.)$$$$$(.nl) */
define('BASE_HOST', 'www.starvisaservices.co.uk');

/* (www.example.)$$ */
define('BASE_EX', '');

/* (www.example.nl/)$$ */
define('BASE_DIR', '');

/* DEFINES FOR URL USE & DIRECTORY PATHS (AUTO) */
define('SITE_URL', 'http://' . BASE_HOST . '' . BASE_EX . '/' . BASE_DIR . (BASE_DIR != '' ? '/' : ''));
define('SITE_URL_WOSLASH', 'http://' . BASE_HOST . '' . BASE_EX . '/' . BASE_DIR);
define('SITE_URL_WOHTTP', BASE_HOST . '.' . BASE_EX . '/' . BASE_DIR . (BASE_DIR != '' ? '/' : ''));
define('SYS_PATH', 'system/');
define('APP_PATH', 'application/');
define('BASE_PATH', realpath(dirname(__FILE__)) . (BASE_DIR != '' ? '/' : ''));

define('ELEM_DIR', APP_PATH . 'elements/');
define('CACHE_PATH', APP_PATH . 'cache/');
define('MEDIA_DIR', ELEM_DIR . 'media/');
define('IMG_DIR', APP_PATH . 'elements/img_front/');

define('FORGOT_SALT', 'BfRT683C783'); // DO NOT CHANGE THIS!
define('DEV_URL', 'dev.starvisa.local'); // DO NOT CHANGE THIS!

define('PRODUCT_ITEMS_PER_PAGE', 2);

/**************************
  CROP TOOL
**************************/
define('USER_CROP_BASE_W', 1200); // DO NOT CHANGE THIS!
define('USER_CROP_BASE_H', 1200); // DO NOT CHANGE THIS!

define('MIN_WIDTH_UPLOAD', 2);
define('MIN_HEIGHT_UPLOAD', 2);

define('MIN_SLIDER_WIDTH_UPLOAD', 940);
define('MIN_SLIDER_HEIGHT_UPLOAD', 378);

/**************************
  NEWS
**************************/
define('NEWS_DIR_CROP_REPLACE', 'detail');
define('NEWS_CROP_MAX_W', 160);
define('NEWS_CROP_MAX_H', 120);

define('NEWS_IMG_THUMB_W', 160);
define('NEWS_IMG_THUMB_H', 120);
define('NEWS_IMG_DETAIL_W', 1000);
define('NEWS_IMG_DETAIL_H', 300);
define('NEWS_IMG_PAGE_W', 780);
define('NEWS_IMG_PAGE_H', 680);
define('NEWS_IMG_MAX_W', 800);
define('NEWS_IMG_MAX_H', 600);

/**************************
  PHOTOALBUMS
**************************/
define('PHOTOALBUMS_DIR_CROP_REPLACE', 'thumb');
define('PHOTOALBUMS_CROP_MAX_W', 160);
define('PHOTOALBUMS_CROP_MAX_H', 120);

define('PHOTOALBUMS_IMG_THUMB_W', 160);
define('PHOTOALBUMS_IMG_THUMB_H', 120);
define('PHOTOALBUMS_IMG_NORMAL_W', 400);
define('PHOTOALBUMS_IMG_NORMAL_H', 300);
define('PHOTOALBUMS_IMG_MAX_W', 800);
define('PHOTOALBUMS_IMG_MAX_H', 600);

/**************************
  SLIDER
**************************/
define('SLIDERS_DIR_CROP_REPLACE', 'slider');
define('SLIDERS_CROP_MAX_W', 1280);
define('SLIDERS_CROP_MAX_H', 300);

define('SLIDERS_IMG_THUMB_W', 160);
define('SLIDERS_IMG_THUMB_H', 120);
define('SLIDERS_IMG_SLIDER_W', 1280);
define('SLIDERS_IMG_SLIDER_H', 853);
define('SLIDERS_IMG_MAX_W', 2500);
define('SLIDERS_IMG_MAX_H', 2500);

/**************************
  NEWEST
**************************/
define('NEWEST_DIR_CROP_REPLACE', 'min');
define('NEWEST_CROP_MAX_W', 85);
define('NEWEST_CROP_MAX_H', 85);

define('NEWEST_IMG_THUMB_W', 160);
define('NEWEST_IMG_THUMB_H', 120);
define('NEWEST_IMG_MAX_W', 800);
define('NEWEST_IMG_MAX_H', 600);
define('NEWEST_IMG_MIN_W', 85);
define('NEWEST_IMG_MIN_H', 85);

/**************************
  PAGES
**************************/
define('PAGES_DIR_CROP_REPLACE', 'slider');
define('PAGES_CROP_MAX_W', 160);
define('PAGES_CROP_MAX_H', 120);

define('PAGES_IMG_THUMB_W', 160);
define('PAGES_IMG_THUMB_H', 120);

define('PAGES_IMG_DETAIL_W', 200);
define('PAGES_IMG_DETAIL_H', 200);

define('PAGES_IMG_NORMAL_W', 600);
define('PAGES_IMG_NORMAL_H', 400);

define('PAGES_IMG_SLIDER_W', 1280);
define('PAGES_IMG_SLIDER_H', 354);

define('PAGES_IMG_SLIDE_W', 1280);
define('PAGES_IMG_SLIDE_H', 354);

define('PAGES_IMG_MAX_W', 800);
define('PAGES_IMG_MAX_H', 600);
define('PAGES_IMG_ICON_W', 140);
define('PAGES_IMG_ICON_H', 140);



/**************************
  PRODUCTS
**************************/
define('PRODUCTS_DIR_CROP_REPLACE', 'normal');
define('PRODUCTS_CROP_MAX_W', 1170);
define('PRODUCTS_CROP_MAX_H', 480);

define('PRODUCTS_IMG_PAGE_W', 400);
define('PRODUCTS_IMG_PAGE_H', 400);

define('PRODUCTS_IMG_BACKGROUND_W', 1170);
define('PRODUCTS_IMG_BACKGROUND_H', 480);

define('PRODUCTS_IMG_THUMB_W', 160);
define('PRODUCTS_IMG_THUMB_H', 120);

define('PRODUCTS_IMG_NORMAL_W', 600);
define('PRODUCTS_IMG_NORMAL_H', 600);

define('PRODUCTS_IMG_MAX_W', 800);
define('PRODUCTS_IMG_MAX_H', 600);

define('PRODUCTS_IMG_FEATURED_W', 600);
define('PRODUCTS_IMG_FEATURED_H', 400);

define('PRODUCTS_IMG_VCAROUSEL_W', 1800);
define('PRODUCTS_IMG_VCAROUSEL_H', 700);

define('PRODUCTS_IMG_RELATED_W', 768);
define('PRODUCTS_IMG_RELATED_H', 768);

/**************************
  LANDINGSPAGES
**************************/
define('LANDINGSPAGES_DIR_CROP_REPLACE', 'thumb');
define('LANDINGSPAGES_CROP_MAX_W', 160);
define('LANDINGSPAGES_CROP_MAX_H', 120);

define('LANDINGSPAGES_IMG_THUMB_W', 160);
define('LANDINGSPAGES_IMG_THUMB_H', 120);

define('LANDINGSPAGES_IMG_HOME_W', 325);
define('LANDINGSPAGES_IMG_HOME_H', 175);
define('LANDINGSPAGES_IMG_ACTIVI_W', 253);
define('LANDINGSPAGES_IMG_ACTIVI_H', 173);
define('LANDINGSPAGES_IMG_DETAIL_W', 404);
define('LANDINGSPAGES_IMG_DETAIL_H', 243);
define('LANDINGSPAGES_IMG_DETAIL_THUMB_W', 127);
define('LANDINGSPAGES_IMG_DETAIL_THUMB_H', 77);

define('LANDINGSPAGES_IMG_NORMAL_W', 480);
define('LANDINGSPAGES_IMG_NORMAL_H', 320);
define('LANDINGSPAGES_IMG_MAX_W', 800);
define('LANDINGSPAGES_IMG_MAX_H', 600);

/**************************
  CATEGORIES
**************************/

define('CATEGORIES_DIR_CROP_REPLACE', 'page');
define('CATEGORIES_CROP_MAX_W', 224);
define('CATEGORIES_CROP_MAX_H', 223);

define('CATEGORIES_IMG_PAGE_W', 120);
define('CATEGORIES_IMG_PAGE_H', 120);

define('CATEGORIES_IMG_THUMB_W', 160);
define('CATEGORIES_IMG_THUMB_H', 120);
define('CATEGORIES_IMG_NORMAL_W', 120);
define('CATEGORIES_IMG_NORMAL_H', 120);
define('CATEGORIES_IMG_MAX_W', 800);
define('CATEGORIES_IMG_MAX_H', 600);

/**************************
  PROJECTS
**************************/
define('PROJECTS_DIR_CROP_REPLACE', 'normal');
define('PROJECTS_CROP_MAX_W', 1200);
define('PROJECTS_CROP_MAX_H', 500);

define('PROJECTS_IMG_THUMB_W', 160);
define('PROJECTS_IMG_THUMB_H', 120);
define('PROJECTS_IMG_THUMBNAIL_W', 300);
define('PROJECTS_IMG_THUMBNAIL_H', 240);

define('PROJECTS_IMG_SQUARE_W', 300);
define('PROJECTS_IMG_SQUARE_H', 300);

define('PROJECTS_IMG_NORMAL_W', 1000);
define('PROJECTS_IMG_NORMAL_H', 600);
define('PROJECTS_IMG_MAX_W', 1200);
define('PROJECTS_IMG_MAX_H', 500);

/**************************
  BRANDS
**************************/
define('BRANDS_DIR_CROP_REPLACE', 'normal');
define('BRANDS_CROP_MAX_W', 400);
define('BRANDS_CROP_MAX_H', 400);

define('BRANDS_IMG_THUMB_W', 160);
define('BRANDS_IMG_THUMB_H', 120);
define('BRANDS_IMG_NORMAL_W', 400);
define('BRANDS_IMG_NORMAL_H', 400);
define('BRANDS_IMG_PHOTO_W', 200);
define('BRANDS_IMG_PHOTO_H', 200);
define('BRANDS_IMG_MAX_W', 800);
define('BRANDS_IMG_MAX_H', 600);

/**************************
  STORES
**************************/
define('STORES_DIR_CROP_REPLACE', 'normal');
define('STORES_CROP_MAX_W', 160);
define('STORES_CROP_MAX_H', 120);

define('STORES_IMG_THUMB_W', 160);
define('STORES_IMG_THUMB_H', 120);
define('STORES_IMG_NORMAL_W', 780);
define('STORES_IMG_NORMAL_H', 533);
define('STORES_IMG_MAX_W', 800);
define('STORES_IMG_MAX_H', 600);

/**************************
  VIDEOS
**************************/
define('VIDEOS_DIR_CROP_REPLACE', 'normal');
define('VIDEOS_CROP_MAX_W', 160);
define('VIDEOS_CROP_MAX_H', 120);

define('VIDEOS_IMG_THUMB_W', 160);
define('VIDEOS_IMG_THUMB_H', 120);
define('VIDEOS_IMG_NORMAL_W', 300);
define('VIDEOS_IMG_NORMAL_H', 200);
define('VIDEOS_IMG_MAX_W', 800);
define('VIDEOS_IMG_MAX_H', 600);

/**************************
  COURSES
**************************/
define('COURSES_DIR_CROP_REPLACE', 'normal');
define('COURSES_CROP_MAX_W', 160);
define('COURSES_CROP_MAX_H', 120);

define('COURSES_IMG_THUMB_W', 160);
define('COURSES_IMG_THUMB_H', 120);
define('COURSES_IMG_NORMAL_W', 500);
define('COURSES_IMG_NORMAL_H', 500);
define('COURSES_IMG_ICON_W', 200);
define('COURSES_IMG_ICON_H', 200);
define('COURSES_IMG_MAX_W', 800);
define('COURSES_IMG_MAX_H', 600);

/**************************
  REFERENCES
**************************/
define('REFERENCES_DIR_CROP_REPLACE', 'normal');
define('REFERENCES_CROP_MAX_W', 160);
define('REFERENCES_CROP_MAX_H', 120);

define('REFERENCES_IMG_OVERVIEW_W', 177);
define('REFERENCES_IMG_OVERVIEW_H', 108);
define('REFERENCES_IMG_DETAIL_W', 160);
define('REFERENCES_IMG_DETAIL_H', 120);
define('REFERENCES_IMG_DETAIL_THUMB_W', 127);
define('REFERENCES_IMG_DETAIL_THUMB_H', 77);

define('REFERENCES_IMG_THUMB_W', 160);
define('REFERENCES_IMG_THUMB_H', 120);
define('REFERENCES_IMG_NORMAL_W', 140);
define('REFERENCES_IMG_NORMAL_H', 140);
define('REFERENCES_IMG_MAX_W', 800);
define('REFERENCES_IMG_MAX_H', 600);

/**************************
  MEMBERS
**************************/
define('MEMBERS_DIR_CROP_REPLACE', 'thumb');
define('MEMBERS_CROP_MAX_W', 160);
define('MEMBERS_CROP_MAX_H', 120);

define('MEMBERS_IMG_THUMB_W', 160);
define('MEMBERS_IMG_THUMB_H', 120);
define('MEMBERS_IMG_NORMAL_W', 480);
define('MEMBERS_IMG_NORMAL_H', 320);
define('MEMBERS_IMG_MAX_W', 800);
define('MEMBERS_IMG_MAX_H', 600);

/**************************
  BLOG
**************************/
define('BLOG_DIR_CROP_REPLACE', 'page');
define('BLOG_CROP_MAX_W', 611);
define('BLOG_CROP_MAX_H', 375);

define('BLOG_IMG_THUMB_W', 160);
define('BLOG_IMG_THUMB_H', 120);
define('BLOG_IMG_MAX_W', 800);
define('BLOG_IMG_MAX_H', 600);

define('BLOG_IMG_ITEMS_W', 204);
define('BLOG_IMG_ITEMS_H', 135);
define('BLOG_IMG_PAGE_W', 611);
define('BLOG_IMG_PAGE_H', 375);
define('BLOG_IMG_MIN_W', 108);
define('BLOG_IMG_MIN_H', 108);

?>