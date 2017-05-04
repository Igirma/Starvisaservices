<?php
error_reporting(1);
/*************************** INFORMATION ***************************/

/******		Version: 1.6			******/
/******		Author: OrangeTalent	******/

/*************************** DEVELOPMENT ***************************/

/*Fixes:

- Form validation slug check optimized
- DEV check updated
- Language set - front-end updated
- Db name auto. in files from config optimized
- Function for page meta settings (getPageHeads) updated
- Pages -> Extern URL auto. http:// fixed
- Preview for pages optimized
- Config file updated and optimized for developer use
- Admin main menu updated

*/

/***************************** MODULES *****************************/

/******		- Pages							******/
/******		- User							******/
/******		- News							******/
/******		- Photo							******/
/******		- Languages						******/
/******		- Form							******/
/******		- Permissions					******/
/******		- Rights						******/
/******		- Module						******/
/******		- Photoalbums					******/

/**NEW**/

/******		- Filters						******/
/******		- Dynamic fields (for products)	******/
/******		- Products						******/
/******		- Webshop						******/
/******		- Countries						******/
/******		- Projects						******/
/******		- Orders						******/
/******		- Reviews						******/
/******		- Categories					******/
/******		- E-mails						******/
/******		- References					******/
/******		- Clients						******/

/*************************** SETTINGS ***************************/

/********** DATABASE **********/
//$config['default_site']['user']     = 'u0250930_default';
$config['default_site']['user']     = 'p415676_starvisa';
//$config['default_site']['pass']     = 'dwtA4N_O';
$config['default_site']['pass']     = 'DGPpKup5*4c9';
//$config['default_site']['database'] = 'u0250930_default';
$config['default_site']['database'] = 'p415676_starvisa';

//$config['siteb']['user'] 					= 'p415676_starvisa';
//$config['siteb']['pass'] 					= 'DGPpKup5*4c9';
//$config['siteb']['database'] 				= 'p415676_starvisa';

/********** CONTROLLER **********/
$config['default_controller'] = 'pages';
$config['default_homepage']   = 'home';

/********** URL **********/
$config['permitted_url_chars']  = 'a-z 0-9~%.:_\-';
$config['enable_query_strings'] = false;

/********** LANGUAGE *************/
$config['default_language']      = 1; // id based on database language.id
$config['default_language_code'] = 'en';

$config['charset'] = 'UTF-8';

/********** PAGES SETTINGS **********/
$config['max_page_depth']                  = 3; // max = 3
$config['max_category_depth']              = 2; // max = 2
$config['mobile_website']                  = 0; // default = 0
$config['landingspages_links_in_ckeditor'] = 0; // default = 0

/********** URL SET_RULES ARRAY -> ALL MAIN TABLE NAMES WICH HAVE A CONTENT TABLE IN DATABASE **********/
$config['db_main_tables'] = array(
    'album',
    'form',
    'media',
    'mobile',
    'news',
    'page',
    'project',
    'product',
    'reference',
    'slider'
);
?>