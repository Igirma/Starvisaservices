<?php

function debug($x)
{
    echo '<div class="draggable" style="position: absolute; top: 0; right: 0; z-index: 9999; background-color: white; border: 2px solid red; margin: 10px; padding: 10px; width: 800px;">';
    if(in_array(gettype($x), array(
        'array',
        'object'
    ))) {
        echo '<pre>';
        print_r($x);
        echo '</pre>';
        echo '</div>';
        return;
    }
    
    echo '<pre>';
    var_dump($x);
    echo '</pre>';
    echo '</div>';
}

function showOrderArrow($data = '', $field = '') {
    if (!isset($data['order']) || !isset($data['field'])) {
        return '';
    }
    if ($data['field'] != $field) {
        return '';
    }
    if ($data['order'] == 'DESC') {
        $ord = 'glyphicon glyphicon-chevron-up';
    } else {
        $ord = 'glyphicon glyphicon-chevron-down';
    }
    return ' <i class="order-arr ' . $ord . '"></i>';
}

function dateHolidaysDiff($days, $holidays)
{
    $total = 0;
    $diff = array();
    $current = strtotime(date('Y-m-d'));
    $last = strtotime(date('Y-m-d', strtotime('+' . $days . ' days')));

    if ($holidays !== false) 
    {
        foreach ($holidays as $day) {
            if ($day['holiday_month'] < 10) {
                $day['holiday_month'] = '0' . $day['holiday_month'];
            }
            if ($day['holiday_day'] < 10) {
                $day['holiday_day'] = '0' . $day['holiday_day'];
            }
            $diff[] = date('Y') . '-' . $day['holiday_month'] . '-' . $day['holiday_day'];
        }
    }

    $dates = array();
    while ($current <= $last) {
        $day = date('Y-m-d', $current);
        $weekday = date('w', $current);
        if (in_array($day, $diff)) {
            $total += 1;
            $dates[] = $day;
        }
        elseif ($weekday == 0 || $weekday == 6) {
            $total += 1;
            $dates[] = $day;
        }
        $current = strtotime('+1 days', $current);
    }
    return $total;
}

function getVAT($price, $percent = 20) 
{
    return $price * ($percent / 100);
}

function prepare_price($price) 
{
    return trim(preg_replace('/([^0-9\.])/i', '', $price));
}

function percent($num_amount, $num_total = 5) {
  $count1 = $num_amount / $num_total;
  $count2 = $count1 * 100;
  $count = number_format($count2, 0);
  return $count;
}

/*
function sendMail($data) 
{
    $headers = 'From: ' . $data['from_name'] . ' <' . $data['from_email'] . '>' . "\r\n";
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    return @mail($data['to_email'], $data['subject'], $data['message'], $headers);
}
*/

function sendMail($data) 
{
  $headers  = 'MIME-Version: 1.0' . "\r\n";
  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
  $headers .= 'To: ' . $data['to_name'] . ' <' . $data['to_email'] . '>' . "\r\n";
  $headers .= 'From: ' . $data['from_name'] . ' <' . $data['from_email'] . '>' . "\r\n";
  $headers .= 'Reply-To: ' . $data['from_name'] . ' <' . $data['reply_to'] . '>' . "\r\n";
  $headers .= 'Subject: ' . $data['subject'] . "\r\n";
  $headers .= 'X-Mailer: PHP/' . phpversion();
  return @mail($data['to_email'], $data['subject'], $data['message'], $headers);
}

function get_date_array($string) {
    if ($string == '' || $string == '0000-00-00') {
        return false;
    }
    $data['year'] = date('Y', strtotime($string));
    $data['month'] = date('m', strtotime($string));
    $data['day'] = date('d', strtotime($string));
    return $data;
}

function set_empty($str, $field) {
    if (!isset($str[$field]) || (isset($str[$field]) && strlen($str[$field]) < 1)) {
        return ' empty';
    }
    return '';
}

function set_unfilled($str, $field) {
    if (!isset($str[$field]) || (isset($str[$field]) && strlen($str[$field]) < 1)) {
        return ' input-required';
    }
    return '';
}

function set_value($str, $field) {
    if (isset($str[$field]) && strlen($str[$field]) > 0) {
        return htmlentities($str[$field]);
    }
    return '';
}

function prepend_select($arrayData, $first = '')
{
    $data = array();
    $data[0] = $first;
    if (isset($arrayData) && $arrayData !== false) {
        foreach ($arrayData as $key => $name) {
            $data[$key] = $name;
        }
    }
    return $data;
}

function safe_file_name($input)
{
    $input = trim($input);
    $input = strip_tags($input);
    $input = remove_accents($input);
    $input = strtolower($input);
    $input = preg_replace("/[^a-zA-Z0-9_\s]/", "", $input);
    $input = str_replace(" ", "_", strip_spaces($input));
    return $input;
}

function strip_spaces($input)
{
    return preg_replace("/\s\s+/", " ", strip_breaklines($input));
}

function strip_breaklines($input)
{
    $input = preg_replace("/\r/", "", $input);
    $input = preg_replace("/\n/", "", $input);
    return $input;
}

function extractFile($source, $first = true)
{
    if (!isset($source) || count($source) < 1) {
        return false;
    }
    $files = array();
    foreach ($source as $k => $l) {
        foreach ($l as $i => $v) {
            if (!array_key_exists($i, $files))
                $files[$i] = array();
            $files[$i][$k] = $v;
        }
    }
    if (!isset($files[0])) {
        return false;
    }
    if ($first) {
        return $files[0];
    }
    return $files;
}

function sizeFormat($size)
{
    if ($size < 1024) {
        return $size . " B";
    } elseif ($size < (1024 * 1024)) {
        $size = round($size / 1024, 1);
        return $size . " KB";
    } elseif ($size < (1024 * 1024 * 1024)) {
        $size = round($size / (1024 * 1024), 1);
        return $size . " MB";
    } else {
        $size = round($size / (1024 * 1024 * 1024), 1);
        return $size . " GB";
    }
}

function get_json($data)
{
    if (json_decode($data, true) != null && is_array(json_decode($data, true)))
        return json_decode($data, true);
    return false;
}

function result($text = null, $type = 'success', $data = null) {
  $d = array();
  $d['data'] = $text;
  if ($type == 'restricted') {
      $d['code'] = 530;
  } elseif ($type == 'error') {
      $d['code'] = 500;
  } else {
      $d['code'] = 200;
  }
  if ($data != null) {
      $d['result'] = $data;
  }
  header('Content-Type: application/json');
  return json_encode($d);
}

function rmdirr($dirname)
{
    if (!file_exists($dirname)) {
        return false;
    }
    if (is_file($dirname) || is_link($dirname)) {
        return @unlink($dirname);
    }
    $stack = array(
        $dirname
    );
    while ($entry = array_pop($stack)) {
        if (is_link($entry)) {
            @unlink($entry);
            continue;
        }
        if (@rmdir($entry)) {
            continue;
        }
        $stack[] = $entry;
        $dh = opendir($entry);
        while (false !== $child = readdir($dh)) {
            if ($child === '.' || $child === '..') {
                continue;
            }
            $child = $entry . DIRECTORY_SEPARATOR . $child;
            if (is_dir($child) && !is_link($child)) {
                $stack[] = $child;
            } else {
                @unlink($child);
            }
        }
        closedir($dh);
    }
    return true;
}

function youtube_id($url = '')
{
    if ($url === '') {
        return FALSE;
    }
    if (!_isValidURL($url)) {
        return FALSE;
    }
    preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $url, $matches);
    if (!$matches) {
        return FALSE;
    }
    if (!_isValidID($matches[0])) {
        return FALSE;
    } else {
        return $matches[0];
    }
}

function vimeo_id($url = '')
{
    if ($url === '') {
        return FALSE;
    }
    if (_isValidURL($url)) {
        sscanf(parse_url($url, PHP_URL_PATH), '/%d', $vimeo_id);
    } else {
        $vimeo_id = $url;
    }
    return (_isValidID($vimeo_id, TRUE)) ? $vimeo_id : FALSE;
}

function youtube_fullvideo($url_id = '')
{
    if ($url_id == '') {
        return FALSE;
    }
    if (_isValidID($url_id)) {
        $id = $url_id;
    } else {
        $id = youtube_id($url_id);
    }
    return 'http://www.youtube.com/v/' . $id;
}

function vimeo_fullvideo($url_id = '')
{
    if ($url_id == '') {
        return FALSE;
    }
    if (_isValidID($url_id)) {
        $id = $url_id;
    } else {
        $id = vimeo_id($url_id);
    }
    return ($id) ? 'http://vimeo.com/' . $id : FALSE;
}

function youtube_thumbs($url_id = '', $thumb = '')
{
    if ($url_id === '') {
        return FALSE;
    }
    if (_isValidID($url_id)) {
        $id = $url_id;
    } else {
        $id = youtube_id($url_id);
    }
    $result = array(
        '0' => 'http://img.youtube.com/vi/' . $id . '/0.jpg',
        '1' => 'http://img.youtube.com/vi/' . $id . '/1.jpg',
        '2' => 'http://img.youtube.com/vi/' . $id . '/2.jpg',
        '3' => 'http://img.youtube.com/vi/' . $id . '/3.jpg'
    );
    if ($thumb == '') {
        return $result;
    } else {
        return $result[$thumb];
    }
}

function vimeo_thumbs($url_id = '', $thumb = '')
{
    if ($url_id == '') {
        return FALSE;
    }
    if (!_isValidURL($url_id)) {
        $id = $url_id;
    } else {
        $id = vimeo_id($url_id);
    }
    $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$id.php"));
    $result = array(
        '0' => $hash[0]['thumbnail_small'],
        '1' => $hash[0]['thumbnail_medium'],
        '2' => $hash[0]['thumbnail_large']
    );
    if ($thumb == '') {
        return $result;
    } else {
        return $result[$thumb];
    }
}

function youtube_embed($url_id = '', $width = '', $height = '', $old_embed = FALSE, $hd = FALSE, $https = FALSE, $suggested = FALSE)
{
    if ($url_id == '') {
        return FALSE;
    }
    if (_isValidID($url_id)) {
        $id = $url_id;
    } else {
        $id = youtube_id($url_id);
    }
    if ($old_embed) {
        if ($hd) {
            $embed = '<object width="1280" height="720">';
        } else {
            $embed = '<object width="' . $width . '" height="' . $height . '">';
        }
        $embed .= '<param name="movie" value="';
        if ($https) {
            $embed .= 'https';
        } else {
            $embed .= 'http';
        }
        $embed .= '://www.youtube-nocookie.com/v/' . $id . '?version=3&amp;hl=en_US&amp;';
        if ($suggested) {
            $embed .= 'rel=0&amp;';
        }
        if ($hd) {
            $embed .= 'hd=1';
        }
        $embed .= '"></param>';
        $embed .= '<param name="allowFullScreen" value="true"></param>';
        $embed .= '<param name="allowscriptaccess" value="always"></param>';
        $embed .= '<embed src="';
        if ($https) {
            $embed .= 'https';
        } else {
            $embed .= 'http';
        }
        $embed .= '://www.youtube-nocookie.com/v/' . $id . '?version=3&amp;hl=en_US';
        if ($hd) {
            $embed .= '&amp;hd=1';
        }
        $embed .= '" type="application/x-shockwave-flash" ';
        if ($hd) {
            $embed .= 'width="1280" height="720" ';
        } else {
            $embed .= 'width="' . $width . '" height="' . $height . '" ';
        }
        $embed .= 'allowscriptaccess="always" allowfullscreen="true"></embed>';
        $embed .= '</object>';
    } else {
        $embed = '<iframe ';
        if ($hd) {
            $embed .= 'width="1280" height="720" ';
        } else {
            $embed .= 'width="' . $width . '" height="' . $height . '" ';
        }
        $embed .= 'src="';
        if ($https) {
            $embed .= 'https';
        } else {
            $embed .= 'http';
        }
        $embed .= '://www.youtube-nocookie.com/embed/' . $id;
        if ($suggested OR $hd) {
            $embed .= '?';
        }
        if ($suggested) {
            $embed .= 'rel=0&amp;';
        }
        if ($hd) {
            $embed .= 'hd=1';
        }
        $embed .= '" frameborder="0" allowfullscreen></iframe>';
    }
    return $embed;
}

function vimeo_embed($url_id = '', $width = '', $height = '', $color = '', $title = FALSE, $autoplay = FALSE)
{
    if ($url_id == '') {
        return FALSE;
    }
    if (!_isValidURL($url_id)) {
        $id = $url_id;
    } else {
        $id = vimeo_id($url_id);
    }
    $embed = '<iframe src="http://player.vimeo.com/video/' . $id . '?byline=0&amp;portrait=0&amp;';
    if ($color != '') {
        $embed .= 'color=' . $color . '&amp;';
    }
    if ($autoplay) {
        $embed .= 'autoplay=1';
    }
    $embed .= '" width="' . $width . '" height="' . $height . '" frameborder="0" webkitAllowFullScreen allowFullScreen></iframe>';
    return $embed;
}


function _isValidURL($url = '')
{
    return preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/i', $url);
}

function _isValidID($id = '', $vimeo = FALSE)
{
    if ($vimeo)
        $headers = @get_headers('http://vimeo.com/' . $id);
    else
        $headers = @get_headers('http://gdata.youtube.com/feeds/api/videos/' . $id);
    if (!strpos($headers[0], '200')) {
        return FALSE;
    } else {
        return TRUE;
    }
}

function strip_slashes($str)
{
  if (is_array($str))
  {
    foreach ($str as $key => $val)
    {
      $str[$key] = strip_slashes($val);
    }
  }
  else
  {
    $str = stripslashes(reduce_double_slashes($str));
  }
  return $str;
}

function reduce_double_slashes($str)
{
  return preg_replace("#(^|[^:])//+#", "\\1/", $str);
}

function isLoggedIn()
{
  //$CI =& get_instance();
  //return $CI->load->users->loggedIn;
  return false;
}

function isAdmin()
{
  return false;
  /*
  $CI =& get_instance();
  if (!isset($CI->load->users->loggedUser['level'])) {
      return false;
  }
  return $CI->load->users->loggedUser['level'] == 'admin';
  */
}

function breadcrumb()
{
  global $CI;
  $breadcrumb = '<li><a href="' . SITE_URL . '"><i class="glyphicon glyphicon-home"></i></a></li>';
  $CI =& get_instance();
  $total = count($CI->url->segments);
  if ($total == 0) {
      return $breadcrumb;
  }
  for ($i = 1; $i <= $total; $i++) {
      $sql = '
        SELECT 
          `page`.page_id,
          `page_content`.content_title,
          `page_content`.menu_title
        FROM `page`
        INNER JOIN `page_content`
          ON `page`.page_id = `page_content`.page_id
        WHERE `page_content`.language_id = ?
          AND `page_content`.slug = ?
          AND `page`.active = 1
          AND `page_content`.sub_active = 1
        LIMIT 1
      ';
      $r = $CI->db->query($sql, array(CUR_LANG, $CI->url->segment($i - 1)));
      if (isset($r[0]['page_id'])) {
          if (end($CI->url->segments) == $CI->url->segment($i - 1)) {
              $breadcrumb .= '<li>' . ((isset($r[0]['menu_title']) && $r[0]['menu_title'] != '') ? $r[0]['menu_title'] : $r[0]['content_title']) . '</li>';
          } else {
              $breadcrumb .= '<li><a href="' . SITE_URL . subSlug($r[0]['page_id']) . '">' . ((isset($r[0]['menu_title']) && $r[0]['menu_title'] != '') ? $r[0]['menu_title'] : $r[0]['content_title']) . '</a></li>';
          }
      }
  }
  return '<ol class="breadcrumb">' . $breadcrumb . '</ol>';
}

function convertYoutube($link)
{
    if(stristr($link, 'http') == true) {
        if(preg_match('%(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $link, $match)) {
            return $match[1];
        } else {
            return '';
        }
    } else {
        return $link;
    }
}

function previewCheck($fields)
{
    $CI =& get_instance();
    
    $query = '';
    if(isset($_SESSION['login_salt'])) {
        if(end($CI->url->segments) != $_SESSION['login_salt']) {
            foreach($fields as $field) {
                $query .= 'AND ' . $field . ' = 1 ';
            }
        }
    }
    
    return $query;
}

function getContentTables()
{
    $CI =& get_instance();
    
    $tables = $CI->db->dbh->query('show tables from ' . $CI->config->item('database', 'default_site') . ' like "%content%"');
    
    $row = $tables->fetchAll(PDO::FETCH_ASSOC);
    
    $content_tables = array();
    
    foreach($row as $key => $value) {
        foreach($value as $k => $v) {
            $content_tables[] = $v;
        }
    }
    
    return $content_tables;
}

function get_languages()
{
  $CI =& get_instance();
  return $CI->db->query('SELECT * FROM `language` ORDER BY `language`.language_id ASC');
}

function openingtime()
{
    $settings = getSettings();
    $i = date('w'); // weekday
    $x = date('H:i:s'); // current time

    $ranges = array(
        array(
            'from' => $settings['sun_start'],
            'to' => $settings['sun_end'],
            'weekday' => 'Zondag'
        ),
        array(
            'from' => $settings['mon_start'],
            'to' => $settings['mon_end'],
            'weekday' => 'Maandag'
        ),
        array(
            'from' => $settings['tue_start'],
            'to' => $settings['tue_end'],
            'weekday' => 'Dinsdag'
        ),
        array(
            'from' => $settings['wed_start'],
            'to' => $settings['wed_end'],
            'weekday' => 'Woensdag'
        ),
        array(
            'from' => $settings['thu_start'],
            'to' => $settings['thu_end'],
            'weekday' => 'Donderdag'
        ),
        array(
            'from' => $settings['fri_start'],
            'to' => $settings['fri_end'],
            'weekday' => 'Vrijdag'
        ),
        array(
            'from' => $settings['sat_start'],
            'to' => $settings['sat_end'],
            'weekday' => 'Zaterdag'
        )
    );

    // current opening time
    $today = $ranges[$i];

    // if too early or too later
    if (strtotime($x) < strtotime($today['from']) || strtotime($x) > strtotime($today['to'])) {
        return false;
    }

    // hours + minutes left
    $reminder = strtotime($today['to']) - strtotime($x);
    $hours = floor($reminder / (60 * 60));
    $reminder = $reminder % (60 * 60);
    $minutes = floor($reminder / 60);

    if ($minutes > 0) {
        return array(
            'hours' => $hours,
            'minutes' => $minutes,
            'weekday' => $today['weekday']
        );
    }

    return false;
}

function permission_level()
{
    $CI =& get_instance();
    
    $sql = '
    SELECT `user`.rights_id FROM `user`
    WHERE
      `user`.user_id = :user_id
    ';
    
    $rights = $CI->db->query($sql, array(
        'user_id' => $_SESSION['user_id']
    ));
    
    if($CI->db->num_rows > 0) {
        return $rights[0]['rights_id'];
    } else {
        return false;
    }
    
}

function isSuperadmin() {
    return (isset($_SESSION['permission']) && $_SESSION['permission'] == 'Superadmin');
}

function permission_overall($module)
{
    if($module != '') {
        $CI =& get_instance();
        
        $sql = '
        SELECT `module`.dirname
        FROM `module`
        WHERE `module`.dirname = :dirname
        ';
        
        $CI->db->query($sql, array(
            'dirname' => $module
        ));
        
        if($CI->db->num_rows > 0) {
            $sql = '
            SELECT * FROM
            `module`, `user`, `rights_module`
            WHERE
              `module`.dirname = :module
            AND
              `rights_module`.rights_id = `user`.rights_id
            AND
              `rights_module`.module_id = `module`.module_id
            AND
              `rights_module`.permission = 1
            AND
              `user`.user_id = :user_id
            AND `module`.active = 1
            ';
            
            $CI->db->query($sql, array(
                'module' => $module,
                'user_id' => $_SESSION['user_id']
            ));
            
            if($CI->db->num_rows > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    } else {
        return true;
    }
    
}

function empty_dir($directory, $empty = FALSE)
{
    if(substr($directory, -1) == '/') {
        $directory = substr($directory, 0, -1);
    }
    if(!file_exists($directory) || !is_dir($directory)) {
        return FALSE;
    } elseif(is_readable($directory)) {
        $handle = opendir($directory);
        while(FALSE !== ($item = readdir($handle))) {
            if($item != '.' && $item != '..') {
                $path = $directory . '/' . $item;
                if(is_dir($path)) {
                    empty_dir($path);
                } else {
                    unlink($path);
                }
            }
        }
        closedir($handle);
        if($empty == FALSE) {
            if(!rmdir($directory)) {
                return FALSE;
            }
        }
    }
    return TRUE;
}

function generateLandingsPageSlug($currentslug, $table)
{
    $CI =& get_instance();
    
    $is_landingspage = 0;
    
    if(strstr($currentslug, '_copy_') == true) {
        $is_landingspage = 1;
    }
    
    if($is_landingspage == 1) {
        $slug_elements = explode('_copy_', $currentslug);
        $count = count($slug_elements) - 1;
        unset($slug_elements[$count]);
        $currentslug = implode('_copy_', $slug_elements);
    }
    
    for($x = 1; $x > -1; $x++) {
        $CI->db->query('SELECT * FROM `' . $table . '` WHERE `' . $table . '`.slug = ?', array(
            $currentslug . '_copy_' . $x
        ));
        if($CI->db->num_rows < 1) {
            return $currentslug . '_copy_' . $x;
        }
    }
}

function log_msg($msg)
{
    $handle = fopen('log.txt', 'a');
    $time = date('d-m-Y @ H:i:s');
    fwrite($handle, 'DATETIME: ' . $time . ' IP: ' . $_SERVER['REMOTE_ADDR'] . ' MESSAGE: ' . $msg . "\n");
    fclose($handle);
}

function getSettings($field = '')
{
    $CI =& get_instance();
    
    if($field == '') {
        $settings = $CI->db->query('SELECT * FROM `settings`');
        return $settings[0];
    } else {
        $settings = $CI->db->query('SELECT ' . $field . ' FROM `settings`');
        return $settings[0][$field];
    }
}


function is_loaded($class = '')
{
    static $_is_loaded = array();
    
    if($class != '') {
        $_is_loaded[strtolower($class)] = $class;
    }
    return $_is_loaded;
}

/**
 * Load a class based on name and directory and instantiate it
 * @param string 	$class	 	the class to load
 * @param string 	$directory 	the directory to look in (within the SYS_PATH)
 */
function &load_class($class, $directory)
{
    static $_classes = array();
    
    if(isset($_classes[$class])) {
        return $_classes[$class];
    }
    
    if(file_exists(SYS_PATH . $directory . '/' . $class . '.php')) {
        if(class_exists($class) === false) {
            require(SYS_PATH . $directory . '/' . $class . '.php');
        }
    } else {
        die('Class "' . $class . '" not found');
    }
    
    is_loaded($class);
    
    $_classes[$class] = new $class();
    return $_classes[$class];
}

/**
 * Cleans a given URL from non displayable characters
 * @param 	string 	$str
 * @param 	string 	$url_encoded
 * @return 	string 	$str
 */
function remove_invisible_characters($str, $url_encoded = TRUE)
{
    $non_displayables = array();
    
    // every control character except newline (dec 10)
    // carriage return (dec 13), and horizontal tab (dec 09)
    
    if($url_encoded) {
        $non_displayables[] = '/%0[0-8bcef]/'; // url encoded 00-08, 11, 12, 14, 15
        $non_displayables[] = '/%1[0-9a-f]/'; // url encoded 16-31
    }
    
    $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S'; // 00-08, 11, 12, 14-31, 127
    
    do {
        $str = preg_replace($non_displayables, '', $str, -1, $count);
    } while($count);
    
    return $str;
}

function &get_config($module = null, $replace = array())
{
    static $_config;
    
    if(isset($_config)) {
        return $_config[0];
    }
    
    // Is the config file in the environment folder?
    if(!defined('ENVIRONMENT') OR !file_exists($file_path = APP_PATH . 'config/' . ENVIRONMENT . '/config' . '.php')) {
        $file_path = SYS_PATH . 'config/config.php';
    }
    
    // Fetch the config file
    if(!file_exists($file_path)) {
        exit('The configuration file does not exist.');
    }
    
    require($file_path);
    
    // Does the $config array exist in the file?
    if(!isset($config) OR !is_array($config)) {
        exit('Your config file does not appear to be formatted correctly.');
    }
    
    // Are any values being dynamically replaced?
    if(count($replace) > 0) {
        foreach($replace as $key => $val) {
            if(isset($config[$key])) {
                $config[$key] = $val;
            }
        }
    }
    
    return $_config[0] =& $config;
}

function decode_utf8($string)
{
    $search["\xC4\x82"] = "A";
    $search["\xC4\x83"] = "a";
    $search["\xC3\x82"] = "A";
    $search["\xC3\xA2"] = "a";
    $search["\xC3\x8E"] = "I";
    $search["\xC3\xAE"] = "i";
    $search["\xC8\x98"] = "S";
    $search["\xC8\x99"] = "s";
    $search["\xC8\x9A"] = "T";
    $search["\xC8\x9B"] = "t";
    $search["\xC5\x9E"] = "S";
    $search["\xC5\x9F"] = "s";
    $search["\xC5\xA2"] = "T";
    $search["\xC5\xA3"] = "t";
    $search["&#259;"]   = "a";
    $search["&#258;"]   = "A";
    $search["&#226;"]   = "a";
    $search["&#194;"]   = "A";
    $search["&#238;"]   = "i";
    $search["&#206;"]   = "I";
    $search["&#351;"]   = "s";
    $search["&#350;"]   = "S";
    $search["&#355;"]   = "t";
    $search["&#354;"]   = "T";
    $search[","]        = "";
    $search["- "]       = "";
    $search["."]        = "";
    $search["?"]        = "";
    $search["!"]        = "";
    $search["["]        = "";
    $search["]"]        = "";
    $search["("]        = "";
    $search[")"]        = "";
    $search["|"]        = "";
    $search["#"]        = "";
    $search["&nbsp;"]   = " ";
    foreach ($search as $char => $replace) {
        $string = str_replace($char, $replace, $string);
    }
    return $string;
}

function seems_utf8($str)
{
    $length = strlen($str);
    for ($i = 0; $i < $length; $i++) {
        $c = ord($str[$i]);
        if ($c < 0x80)
            $n = 0;
        elseif (($c & 0xE0) == 0xC0)
            $n = 1;
        elseif (($c & 0xF0) == 0xE0)
            $n = 2;
        elseif (($c & 0xF8) == 0xF0)
            $n = 3;
        elseif (($c & 0xFC) == 0xF8)
            $n = 4;
        elseif (($c & 0xFE) == 0xFC)
            $n = 5;
        else
            return false;
        for ($j = 0; $j < $n; $j++) {
            if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
                return false;
        }
    }
    return true;
}

function remove_accents($string)
{
    if (!preg_match('/[\x80-\xff]/', $string))
        return $string;
    if (seems_utf8($string)) {
        $chars  = array(
            chr(195) . chr(128) => 'A',
            chr(195) . chr(129) => 'A',
            chr(195) . chr(130) => 'A',
            chr(195) . chr(131) => 'A',
            chr(195) . chr(132) => 'A',
            chr(195) . chr(133) => 'A',
            chr(195) . chr(135) => 'C',
            chr(195) . chr(136) => 'E',
            chr(195) . chr(137) => 'E',
            chr(195) . chr(138) => 'E',
            chr(195) . chr(139) => 'E',
            chr(195) . chr(140) => 'I',
            chr(195) . chr(141) => 'I',
            chr(195) . chr(142) => 'I',
            chr(195) . chr(143) => 'I',
            chr(195) . chr(145) => 'N',
            chr(195) . chr(146) => 'O',
            chr(195) . chr(147) => 'O',
            chr(195) . chr(148) => 'O',
            chr(195) . chr(149) => 'O',
            chr(195) . chr(150) => 'O',
            chr(195) . chr(153) => 'U',
            chr(195) . chr(154) => 'U',
            chr(195) . chr(155) => 'U',
            chr(195) . chr(156) => 'U',
            chr(195) . chr(157) => 'Y',
            chr(195) . chr(159) => 's',
            chr(195) . chr(160) => 'a',
            chr(195) . chr(161) => 'a',
            chr(195) . chr(162) => 'a',
            chr(195) . chr(163) => 'a',
            chr(195) . chr(164) => 'a',
            chr(195) . chr(165) => 'a',
            chr(195) . chr(167) => 'c',
            chr(195) . chr(168) => 'e',
            chr(195) . chr(169) => 'e',
            chr(195) . chr(170) => 'e',
            chr(195) . chr(171) => 'e',
            chr(195) . chr(172) => 'i',
            chr(195) . chr(173) => 'i',
            chr(195) . chr(174) => 'i',
            chr(195) . chr(175) => 'i',
            chr(195) . chr(177) => 'n',
            chr(195) . chr(178) => 'o',
            chr(195) . chr(179) => 'o',
            chr(195) . chr(180) => 'o',
            chr(195) . chr(181) => 'o',
            chr(195) . chr(182) => 'o',
            chr(195) . chr(182) => 'o',
            chr(195) . chr(185) => 'u',
            chr(195) . chr(186) => 'u',
            chr(195) . chr(187) => 'u',
            chr(195) . chr(188) => 'u',
            chr(195) . chr(189) => 'y',
            chr(195) . chr(191) => 'y',
            chr(196) . chr(128) => 'A',
            chr(196) . chr(129) => 'a',
            chr(196) . chr(130) => 'A',
            chr(196) . chr(131) => 'a',
            chr(196) . chr(132) => 'A',
            chr(196) . chr(133) => 'a',
            chr(196) . chr(134) => 'C',
            chr(196) . chr(135) => 'c',
            chr(196) . chr(136) => 'C',
            chr(196) . chr(137) => 'c',
            chr(196) . chr(138) => 'C',
            chr(196) . chr(139) => 'c',
            chr(196) . chr(140) => 'C',
            chr(196) . chr(141) => 'c',
            chr(196) . chr(142) => 'D',
            chr(196) . chr(143) => 'd',
            chr(196) . chr(144) => 'D',
            chr(196) . chr(145) => 'd',
            chr(196) . chr(146) => 'E',
            chr(196) . chr(147) => 'e',
            chr(196) . chr(148) => 'E',
            chr(196) . chr(149) => 'e',
            chr(196) . chr(150) => 'E',
            chr(196) . chr(151) => 'e',
            chr(196) . chr(152) => 'E',
            chr(196) . chr(153) => 'e',
            chr(196) . chr(154) => 'E',
            chr(196) . chr(155) => 'e',
            chr(196) . chr(156) => 'G',
            chr(196) . chr(157) => 'g',
            chr(196) . chr(158) => 'G',
            chr(196) . chr(159) => 'g',
            chr(196) . chr(160) => 'G',
            chr(196) . chr(161) => 'g',
            chr(196) . chr(162) => 'G',
            chr(196) . chr(163) => 'g',
            chr(196) . chr(164) => 'H',
            chr(196) . chr(165) => 'h',
            chr(196) . chr(166) => 'H',
            chr(196) . chr(167) => 'h',
            chr(196) . chr(168) => 'I',
            chr(196) . chr(169) => 'i',
            chr(196) . chr(170) => 'I',
            chr(196) . chr(171) => 'i',
            chr(196) . chr(172) => 'I',
            chr(196) . chr(173) => 'i',
            chr(196) . chr(174) => 'I',
            chr(196) . chr(175) => 'i',
            chr(196) . chr(176) => 'I',
            chr(196) . chr(177) => 'i',
            chr(196) . chr(178) => 'IJ',
            chr(196) . chr(179) => 'ij',
            chr(196) . chr(180) => 'J',
            chr(196) . chr(181) => 'j',
            chr(196) . chr(182) => 'K',
            chr(196) . chr(183) => 'k',
            chr(196) . chr(184) => 'k',
            chr(196) . chr(185) => 'L',
            chr(196) . chr(186) => 'l',
            chr(196) . chr(187) => 'L',
            chr(196) . chr(188) => 'l',
            chr(196) . chr(189) => 'L',
            chr(196) . chr(190) => 'l',
            chr(196) . chr(191) => 'L',
            chr(197) . chr(128) => 'l',
            chr(197) . chr(129) => 'L',
            chr(197) . chr(130) => 'l',
            chr(197) . chr(131) => 'N',
            chr(197) . chr(132) => 'n',
            chr(197) . chr(133) => 'N',
            chr(197) . chr(134) => 'n',
            chr(197) . chr(135) => 'N',
            chr(197) . chr(136) => 'n',
            chr(197) . chr(137) => 'N',
            chr(197) . chr(138) => 'n',
            chr(197) . chr(139) => 'N',
            chr(197) . chr(140) => 'O',
            chr(197) . chr(141) => 'o',
            chr(197) . chr(142) => 'O',
            chr(197) . chr(143) => 'o',
            chr(197) . chr(144) => 'O',
            chr(197) . chr(145) => 'o',
            chr(197) . chr(146) => 'OE',
            chr(197) . chr(147) => 'oe',
            chr(197) . chr(148) => 'R',
            chr(197) . chr(149) => 'r',
            chr(197) . chr(150) => 'R',
            chr(197) . chr(151) => 'r',
            chr(197) . chr(152) => 'R',
            chr(197) . chr(153) => 'r',
            chr(197) . chr(154) => 'S',
            chr(197) . chr(155) => 's',
            chr(197) . chr(156) => 'S',
            chr(197) . chr(157) => 's',
            chr(197) . chr(158) => 'S',
            chr(197) . chr(159) => 's',
            chr(197) . chr(160) => 'S',
            chr(197) . chr(161) => 's',
            chr(197) . chr(162) => 'T',
            chr(197) . chr(163) => 't',
            chr(197) . chr(164) => 'T',
            chr(197) . chr(165) => 't',
            chr(197) . chr(166) => 'T',
            chr(197) . chr(167) => 't',
            chr(197) . chr(168) => 'U',
            chr(197) . chr(169) => 'u',
            chr(197) . chr(170) => 'U',
            chr(197) . chr(171) => 'u',
            chr(197) . chr(172) => 'U',
            chr(197) . chr(173) => 'u',
            chr(197) . chr(174) => 'U',
            chr(197) . chr(175) => 'u',
            chr(197) . chr(176) => 'U',
            chr(197) . chr(177) => 'u',
            chr(197) . chr(178) => 'U',
            chr(197) . chr(179) => 'u',
            chr(197) . chr(180) => 'W',
            chr(197) . chr(181) => 'w',
            chr(197) . chr(182) => 'Y',
            chr(197) . chr(183) => 'y',
            chr(197) . chr(184) => 'Y',
            chr(197) . chr(185) => 'Z',
            chr(197) . chr(186) => 'z',
            chr(197) . chr(187) => 'Z',
            chr(197) . chr(188) => 'z',
            chr(197) . chr(189) => 'Z',
            chr(197) . chr(190) => 'z',
            chr(197) . chr(191) => 's',
            chr(226) . chr(130) . chr(172) => 'E',
            chr(194) . chr(163) => ''
        );
        $string = strtr($string, $chars);
    } else {
        $chars['in']  = chr(128) . chr(131) . chr(138) . chr(142) . chr(154) . chr(158) . chr(159) . chr(162) . chr(165) . chr(181) . chr(192) . chr(193) . chr(194) . chr(195) . chr(196) . chr(197) . chr(199) . chr(200) . chr(201) . chr(202) . chr(203) . chr(204) . chr(205) . chr(206) . chr(207) . chr(209) . chr(210) . chr(211) . chr(212) . chr(213) . chr(214) . chr(216) . chr(217) . chr(218) . chr(219) . chr(220) . chr(221) . chr(224) . chr(225) . chr(226) . chr(227) . chr(228) . chr(229) . chr(231) . chr(232) . chr(233) . chr(234) . chr(235) . chr(236) . chr(237) . chr(238) . chr(239) . chr(241) . chr(242) . chr(243) . chr(244) . chr(245) . chr(246) . chr(248) . chr(249) . chr(250) . chr(251) . chr(252) . chr(253) . chr(255);
        $chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";
        $string       = strtr($string, $chars['in'], $chars['out']);
        $double_chars['in']  = array(
            chr(140),
            chr(156),
            chr(198),
            chr(208),
            chr(222),
            chr(223),
            chr(230),
            chr(240),
            chr(254)
        );
        $double_chars['out'] = array(
            'OE',
            'oe',
            'AE',
            'DH',
            'TH',
            'ss',
            'ae',
            'dh',
            'th'
        );
        $string = str_replace($double_chars['in'], $double_chars['out'], $string);
    }
    return $string;
}

function &get_instance()
{
    return controller::get_instance();
}

function &_get_validation_object()
{
    $CI =& get_instance();
    
    return $CI->form;
}

function validation_errors($prefix = '', $suffix = '', $front = 0)
{
    if(FALSE === ($OBJ =& _get_validation_object())) {
        return '';
    }
    
    if($front == 1) {
        return $OBJ->_error_array;
    } else {
        return $OBJ->error_string($prefix, $suffix);
    }
}

function prepare_tree($data)
{
    $arr = array(
        'items' => array(),
        'parents' => array()
    );
    
    foreach($data as $x) {
        $arr['items'][$x['page_id']] = $x;
        $arr['parents'][$x['parent_id']][] = $x['page_id'];
    }
    
    return $arr;
}

function build_sitemap($parent_id, $data)
{
    // USE PREPARE_TREE() FIRST!
    
    $html = '';
    
    if(isset($data['parents'][$parent_id])) {
        $html = '<ul>';
        foreach($data['parents'][$parent_id] as $item_id) {
            $html .= '<li>' . $data['items'][$item_id]['title'];
            
            // find childitems recursively
            $html .= build_tree($item_id, $data);
            
            $html .= '<div style="margin-left: 100px; border: 1px solid red;">Hello</div></li>';
        }
        $html .= '</ul>';
    }
    
    return $html;
}

function cron()
{
    $CI =& get_instance();
    $CI->db->query('UPDATE `news` SET `news`.archive = 1, `news`.active = 0 WHERE `news`.end_date < ? AND `news`.no_end_date = 0', array(
        time()
    ));
    $CI->db->query('UPDATE `news` SET `news`.archive = 0 WHERE `news`.end_date > ?', array(
        time()
    ));
    $CI->db->query('UPDATE `blog` SET `blog`.archive = 1, `blog`.active = 0 WHERE `blog`.end_date < ?', array(
        time()
    ));
    $CI->db->query('UPDATE `blog` SET `blog`.archive = 0 WHERE `blog`.end_date > ?', array(
        time()
    ));
}

function rearrange_files($arr)
{
    if(!empty($arr['name'][0])) {
        foreach($arr as $key => $all) {
            foreach($all as $i => $val) {
                $new[$i][$key] = $val;
            }
        }
        return $new;
    }
    
    return false;
}

function permission($dirname, $permission, $language = false)
{
    $CI =& get_instance();
    
    if(!$language) {
        $language = $CI->config->item('default_language');
    }
    
    $sql = '
    SELECT
      `user`.user_id,
      `user`.rights_id,
      `user_language`.language_id,
      `rights`.rights_id,
      `module`.module_id,
      `module`.dirname,
      `rights_module`.rights_id,
      `rights_module`.rights_module_id,
      `rights_module`.module_id,
      `permission`.rights_module_id,
      `permission`.add,
      `permission`.edit,
      `permission`.delete
    FROM `user`
    INNER JOIN `rights`
      ON `rights`.rights_id = `user`.rights_id
      INNER JOIN `user_language`
      ON `user_language`.user_id = `user`.user_id
      INNER JOIN `rights_module`
      ON `rights_module`.rights_id = `rights`.rights_id
      INNER JOIN `module`
      ON `module`.module_id = `rights_module`.module_id
      INNER JOIN `permission`
      ON `permission`.rights_module_id = `rights_module`.rights_module_id
    WHERE `user`.user_id = :user_id
    AND `user`.login_salt = :login_salt
    AND `rights_module`.module_id = `module`.module_id
    AND `module`.dirname = :dirname
    AND `user_language`.language_id = :language_id
    ';
    
    $data = $CI->db->query($sql, array(
        'language_id' => $language,
        'user_id' => $_SESSION['user_id'],
        'login_salt' => $_SESSION['login_salt'],
        'dirname' => $dirname
    ));
    
    if(!empty($data)) {
        if($data[0][$permission] == 1) {
            return true;
        }
    }
    
    return false;
}

function fetch_admin_top_menu()
{
    $CI =& get_instance();
    
    $sql = '
      SELECT *
      FROM `module`
      WHERE `module`.menu = 1
    ';
    
    $modules = $CI->db->query($sql);
    
    $menu = array();
    
    $menu['logged_in'] = ($CI->login_model->check_login() ? '1' : '0');
    
    if($CI->login_model->check_login()) {
        foreach($modules as $module) {
            $sql = '
              SELECT * 
              FROM `module`, `user`, `rights_module`
              WHERE `module`.dirname = :module
              AND `rights_module`.rights_id = `user`.rights_id
              AND `rights_module`.module_id = `module`.module_id
              AND `rights_module`.permission = 1
              AND `user`.user_id = :user_id
              AND `module`.active = 1
              ORDER BY `module`.order ASC
            ';
            
            $CI->db->query($sql, array(
                'module' => $module['dirname'],
                'user_id' => $_SESSION['user_id']
            ));
            
            if($CI->db->num_rows > 0) {
                $menu['items'][] = $module;
            }
        }
    }
    
    return $menu;
}

function str_shorten($str, $len)
{
    return (strlen($str) > $len ? substr($str, 0, $len) . '...' : $str);
}

function gen_docname($name)
{
    for($x = 0; $x > -1; $x++) {
        if($x == 0) {
            if(!doc_in_dir($name)) {
                return $name;
            }
        } else {
            if(!doc_in_dir($x . '_' . $name)) {
                $name = $x . '_' . $name;
                return $name;
            }
        }
    }
}

function doc_in_dir($file)
{
    if(file_exists(MEDIA_DIR . CONTROLLER . '/docs/' . $file)) {
        return true;
    } else {
        return false;
    }
}

function add_doc($doc)
{
    $doc['name'] = gen_docname($doc['name']);
    
    if(!is_dir(MEDIA_DIR . CONTROLLER . '/docs')) {
        mkdir(MEDIA_DIR . CONTROLLER . '/docs', 0777);
    }
    
    move_uploaded_file($doc['tmp_name'], MEDIA_DIR . CONTROLLER . '/docs/' . $doc['name']);
}

function xml_to_array(SimpleXMLElement $xml)
{
    $array = (array) $xml;
    
    foreach(array_slice($array, 0) as $key => $value) {
        if($value instanceof SimpleXMLElement) {
            $array[$key] = empty($value) ? NULL : xml_to_array($value);
        }
    }
    return $array;
}

function getMainMenu()
{
    $CI =& get_instance();
    
    $sql = '
    SELECT * FROM `page`, `page_content`
    WHERE `page`.page_id = `page_content`.page_id
    AND `page`.active = 1
    AND `page`.parent_id = 0
    AND `page`.main_menu = 1
    AND `page_content`.sub_active = 1
    AND `page_content`.language_id = ? 
    ORDER BY `page`.order ASC
    ';
    
    $menu = $CI->db->query($sql, array(CUR_LANG));
    return $menu;
}

function getSubMenu($id)
{
    $CI =& get_instance();
    
    $sql = '
    SELECT * FROM `page`, `page_content`
    WHERE `page`.page_id = `page_content`.page_id
    AND `page`.active = 1
    AND `page`.parent_id = ? 
    AND `page`.main_menu = 1
    AND `page_content`.sub_active = 1
    AND `page_content`.language_id = ? 
    ORDER BY `page`.order ASC
    ';
    
    $menu = $CI->db->query($sql, array($id, CUR_LANG));
    
    if (!empty($menu) && count($menu) > 0)
    {
        foreach ($menu as $k => $item)
        {
            $sql = '
            SELECT *
            FROM `media`
            INNER JOIN `media_content`
              ON `media_content`.media_id = `media`.media_id
            WHERE `media`.table_id = ?
              AND `media`.controller = ?
              AND `media_content`.language_id = ?
              AND `media`.album_id = ? 
            ORDER BY `media`.order ASC LIMIT 1
            ';
            $media = $CI->db->query($sql, array($item['page_id'], 'pages', CUR_LANG, 1));
            if (isset($media[0])) {
                $menu[$k]['media'] = $media[0];
            } else {
                $menu[$k]['media'] = false;
            }
        }
    }

    return $menu;
}

function haveChildren($id)
{
    $CI =& get_instance();
    
    $sql = '
    SELECT * FROM `page`, `page_content`
    WHERE `page`.parent_id = ?
    AND `page`.active = 1
    AND `page`.main_menu = 1
    AND `page_content`.sub_active = 1
    AND `page_content`.language_id = ' . CUR_LANG . '
    ';
    
    $CI->db->query($sql, array(
        $id
    ));
    if($CI->db->num_rows > 0) {
        return 1;
    } else {
        return 0;
    }
}

function haveFilters($controller)
{
    $CI =& get_instance();
    
    $sql = '
    SELECT  DISTINCT *, `filter_heading`.title as filer_title
    FROM `filter`, `filter_heading`, `filter_item` 
    WHERE `filter`.filter_id = `filter_heading`.filter_id 
    AND `filter_heading`.language_id = :lang 
    AND `filter_heading`.filter_heading_id = `filter_item`.filter_heading_id 
    AND `filter`.controller = :controller
    ORDER BY `filter`.order ASC, `filter_item`.filter_item_id 
    ';
    
    $CI->db->query($sql, array(
        'lang' => $CI->config->item('default_language'),
        'controller' => $controller
    ));
    if($CI->db->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}
function haveCategories($controller)
{
    $CI =& get_instance();
    
    $sql = '
    SELECT *
    FROM `category`
    LEFT JOIN `category_content` ON `category`.category_id = `category_content`.category_id
    WHERE `category`.controller = :controller
    AND `category_content`.language_id = :lang
    AND `category`.parent_id = :parent_id
    ORDER BY `category`.order ASC
    ';
    
    $CI->db->query($sql, array(
        'controller' => $controller,
        'lang' => $CI->config->item('default_language'),
        'parent_id' => 0
    ));
    
    if($CI->db->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}

function getHomePageSlug($language_id)
{
    $CI =& get_instance();
    
    $sql = '
    SELECT `page_content`.slug
    FROM `page`, `page_content`
    WHERE `page`.page_id = `page_content`.page_id
    AND `page`.controller = :controller
    AND `page_content`.language_id = :language_id
    ';
    
    $all = $CI->db->query($sql, array(
        'controller' => $CI->config->item('default_homepage'),
        'language_id' => $language_id
    ));
    if(isset($all) && $all && count($all) > 0)
        return $all[0]['slug'];
}

function subSlug($page_id)
{
    $CI =& get_instance();
    $sub_slug = '';

    $sql = '
      SELECT `page`.page_id, `page`.parent_id, `page_content`.slug FROM `page` 
      INNER JOIN `page_content` ON `page_content`.page_id = `page`.page_id 
      WHERE `page`.page_id = ? AND `page_content`.language_id = ? 
    ';
    $sub = $CI->db->query($sql, array($page_id, CUR_LANG));
    $sub_slug .= $sub[0]['slug'];

    $sql = '
      SELECT `page`.page_id, `page`.parent_id, `page_content`.slug FROM `page` 
      INNER JOIN `page_content` ON `page_content`.page_id = `page`.page_id 
      WHERE `page`.page_id = ? AND `page_content`.language_id = ? 
    ';
    $sub = $CI->db->query($sql, array($sub[0]['parent_id'], CUR_LANG));

    if ($CI->db->num_rows > 0) 
    {
        $sub_slug = $sub[0]['slug'] . '/' . $sub_slug;
        $sql = '
          SELECT `page`.page_id, `page`.parent_id, `page_content`.slug FROM `page` 
          INNER JOIN `page_content` ON `page_content`.page_id = `page`.page_id 
          WHERE `page`.page_id = ? AND `page_content`.language_id = ? 
        ';
        $sub = $CI->db->query($sql, array($sub[0]['parent_id'], CUR_LANG));
        if ($CI->db->num_rows > 0) 
        {
            $sub_slug = $sub[0]['slug'] . '/' . $sub_slug;
        }
    }
    return $sub_slug;
}

function getPage($controller)
{
    $CI =& get_instance();
    $sql = '
      SELECT 
        `page`.page_id, 
        `page_content`.content_title 
      FROM `page` 
      INNER JOIN `page_content` ON `page_content`.page_id = `page`.page_id 
      WHERE `page_content`.language_id = ? 
        AND `page`.controller = ? 
      LIMIT 1
    ';
    $page = $CI->db->query($sql, array(CUR_LANG, $controller));
    if (!isset($page[0])) {
        return false;
    }
    return array(
      'title' => $page[0]['content_title'],
      'id' => $page[0]['page_id']
    );
}

function landingspageSubSlug($landingspage_id)
{
    $CI =& get_instance();
    $sub_slug = '';
    
    $sql = '
    SELECT * FROM `landingspage`
    LEFT JOIN `landingspage_content` ON `landingspage_content`.landingspage_id = `landingspage`.landingspage_id
    WHERE `landingspage`.landingspage_id = ?
    AND `landingspage_content`.language_id = ' . CUR_LANG . '
    ';
    
    $sub = $CI->db->query($sql, array(
        $landingspage_id
    ));
    $sub_slug .= $sub[0]['slug'];
    
    $sql = '
    SELECT * FROM `landingspage`
    LEFT JOIN `landingspage_content` ON `landingspage_content`.landingspage_id = `landingspage`.landingspage_id
    WHERE `landingspage`.landingspage_id = ?
    AND `landingspage_content`.language_id = ' . CUR_LANG . '
    ';
    
    $sub = $CI->db->query($sql, array(
        $sub[0]['parent_id']
    ));
    
    if($CI->db->num_rows > 0) {
        
        $sub_slug = $sub[0]['slug'] . '/' . $sub_slug;
        
        $sql = '
        SELECT * FROM `landingspage`
        LEFT JOIN `landingspage_content` ON `landingspage_content`.landingspage_id = `landingspage`.landingspage_id
        WHERE `landingspage`.landingspage_id = ?
        AND `landingspage_content`.language_id = ' . CUR_LANG . '
        ';
        
        $sub = $CI->db->query($sql, array(
            $sub[0]['parent_id']
        ));
        
        if($CI->db->num_rows > 0) {
            $sub_slug = $sub[0]['slug'] . '/' . $sub_slug;
        }
    }
    return $sub_slug;
}

function findSettings()
{
    $CI =& get_instance();
    $settings = $CI->db->query('SELECT * FROM `settings`');
    return $settings;
}

function getSlugOnController($controller)
{
    $CI =& get_instance();
    
    $sql = 'SELECT * FROM `page`, `page_content` WHERE `page`.page_id = `page_content`.page_id AND `page`.controller = ?';
    $slug = $CI->db->query($sql, array(
        $controller
    ));
    if(isset($slug) && $slug && count($slug) > 0) {
        $slug = subSlug($slug[0]['page_id']);
        return $slug;
    }
}

function getPageHeads($slug, $language_id)
{
    $CI =& get_instance();
    
    $tables = $CI->db->dbh->query('show tables from `' . $CI->config->item('database', 'default_site') . '` like "%content%"');
    
    $row = $tables->fetchAll(PDO::FETCH_ASSOC);
    
    $content_tables = array();
    
    foreach($row as $key => $value) {
        foreach($value as $k => $v) {
            $content_tables[] = $v;
        }
    }
    
    foreach($content_tables as $key => $value) {
        $select_field = 'slug';
        
        $CI->db->query('SELECT `' . $value . '`.' . $select_field . ' FROM `' . $value . '` WHERE `' . $value . '`.language_id = ?', array(
            $language_id
        ));
        
        if($CI->db->num_rows > 0) {
            if($value != 'mobile_content') {
                $arr = $CI->db->query('SELECT * FROM `' . $value . '` WHERE `' . $value . '`.' . $select_field . ' = ? AND `' . $value . '`.language_id = ?', array(
                    $slug,
                    $language_id
                ));
                if($CI->db->num_rows > 0) {
                    return $arr;
                }
            }
        }
    }
}

function submitReview($post)
{
    
    $CI =& get_instance();
    
    $test = 1;
    if(!preg_match("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/i", $post['form']['email']))
        $test = 0;
    if(strlen($post['form']['name']) > 300 || strlen($post['form']['email']) > 300 || strlen($post['form']['content']) > 4300)
        $test = 0;
    if(preg_match("/bcc:|cc:|multipart|\[url|Content-Type:/i", implode($post['form'])))
        $test = 0;
    if(preg_match_all("/<a|http:/i", implode($post['form']), $out) > 3)
        $test = 0;
    foreach($post['form'] as $key => $val) {
        if(stristr($val, '\r'))
            $test = 0;
        if(stristr($val, '\n'))
            $test = 0;
        if(stristr($val, '%0A'))
            $test = 0;
        if(stristr($val, '%0D'))
            $test = 0;
        if(stristr($val, '<a'))
            $test = 0;
        if(stristr($val, 'content-type'))
            $test = 0;
        if(stristr($val, 'mime-version'))
            $test = 0;
        if(stristr($val, 'cc:'))
            $test = 0;
    }
    if(count($_GET) > 0)
        $test = 0;
    
    if($post && $test == 1) {
        if($post['comment_submit']) {
            if($_SERVER['REQUEST_METHOD'] != "POST")
                $this->url->redirect(SITE_URL);
            
            $sql = '
            INSERT INTO `review`
            (
              `review`.review_date,
              `review`.language_id,
              `review`.product_id
            )
            VALUES
            (
              :review_date,
              :language_id,
              :product_id
            )
          ';
            $CI->db->query($sql, array(
                'review_date' => time(),
                'language_id' => CUR_LANG,
                'product_id' => $post['form']['product_id']
            ));
            $review_id = $CI->db->last_insert_id;
            
            $sql = '
              INSERT INTO `review_content`
              (
                `review_content`.review_id,
                `review_content`.name,
                `review_content`.email,
                content
              )
              VALUES
              (
                :review_id,
                :name,
                :email,
                :content
              )
            ';
            $CI->db->query($sql, array(
                'review_id' => $review_id,
                'name' => $post['form']['name'],
                'email' => $post['form']['email'],
                'content' => $post['form']['content']
            ));
            
        }
    }
    
}

function submitBlogReview($post)
{
    
    $CI =& get_instance();
    
    $test = 1;
    if(!preg_match("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/i", $post['form']['email']))
        $test = 0;
    if(strlen($post['form']['name']) > 300 || strlen($post['form']['email']) > 300 || strlen($post['form']['content']) > 4300)
        $test = 0;
    if(preg_match("/bcc:|cc:|multipart|\[url|Content-Type:/i", implode($post['form'])))
        $test = 0;
    if(preg_match_all("/<a|http:/i", implode($post['form']), $out) > 3)
        $test = 0;
    foreach($post['form'] as $key => $val) {
        if(stristr($val, '\r'))
            $test = 0;
        if(stristr($val, '\n'))
            $test = 0;
        if(stristr($val, '%0A'))
            $test = 0;
        if(stristr($val, '%0D'))
            $test = 0;
        if(stristr($val, '<a'))
            $test = 0;
        if(stristr($val, 'content-type'))
            $test = 0;
        if(stristr($val, 'mime-version'))
            $test = 0;
        if(stristr($val, 'cc:'))
            $test = 0;
    }
    if(count($_GET) > 0)
        $test = 0;
    
    if($post && $test == 1) {
        if($post['comment_submit']) {
            if($_SERVER['REQUEST_METHOD'] != "POST")
                $this->url->redirect(SITE_URL);
            
            $sql = '
              INSERT INTO `comment`
              (
                `comment`.comment_date,
                `comment`.language_id,
                `comment`.blog_id
              )
              VALUES
              (
                :comment_date,
                :language_id,
                :blog_id
              )
            ';
            $CI->db->query($sql, array(
                'comment_date' => time(),
                'language_id' => CUR_LANG,
                'blog_id' => $post['form']['blog_id']
            ));
            $comment_id = $CI->db->last_insert_id;
            
            $sql = '
              INSERT INTO `comment_content`
              (
                `comment_content`.comment_id,
                `comment_content`.name,
                `comment_content`.email,
                content
              )
              VALUES
              (
                :comment_id,
                :name,
                :email,
                :content
              )
            ';
            $CI->db->query($sql, array(
                'comment_id' => $comment_id,
                'name' => $post['form']['name'],
                'email' => $post['form']['email'],
                'content' => $post['form']['content']
            ));
            
        }
    }
    
}

function submitBlogReviewReact($post)
{
    
    $CI =& get_instance();
    
    $test = 1;
    if(!preg_match("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/i", $post['form']['react_email']))
        $test = 0;
    if(strlen($post['form']['react_name']) > 300 || strlen($post['form']['react_email']) > 300 || strlen($post['form']['react_content']) > 4300)
        $test = 0;
    if(preg_match("/bcc:|cc:|multipart|\[url|Content-Type:/i", implode($post['form'])))
        $test = 0;
    if(preg_match_all("/<a|http:/i", implode($post['form']), $out) > 3)
        $test = 0;
    foreach($post['form'] as $key => $val) {
        if(stristr($val, '\r'))
            $test = 0;
        if(stristr($val, '\n'))
            $test = 0;
        if(stristr($val, '%0A'))
            $test = 0;
        if(stristr($val, '%0D'))
            $test = 0;
        if(stristr($val, '<a'))
            $test = 0;
        if(stristr($val, 'content-type'))
            $test = 0;
        if(stristr($val, 'mime-version'))
            $test = 0;
        if(stristr($val, 'cc:'))
            $test = 0;
    }
    if(count($_GET) > 0)
        $test = 0;
    
    if($post && $test == 1) {
        if($post['react_submit']) {
            if($_SERVER['REQUEST_METHOD'] != "POST")
                $this->url->redirect(SITE_URL);
            
            $sql = '
              INSERT INTO `comment`
              (
                `comment`.comment_date,
                `comment`.language_id,
                `comment`.react_id
              )
              VALUES
              (
                :comment_date,
                :language_id,
                :react_id
              )
            ';
            $CI->db->query($sql, array(
                'comment_date' => time(),
                'language_id' => CUR_LANG,
                'react_id' => $post['form']['comment_id']
            ));
            echo $CI->db->error;
            
            $comment_id = $CI->db->last_insert_id;
            
            $sql = '
              INSERT INTO `comment_content`
              (
                `comment_content`.comment_id,
                `comment_content`.name,
                `comment_content`.email,
                content
              )
              VALUES
              (
                :comment_id,
                :name,
                :email,
                :content
              )
            ';
            $CI->db->query($sql, array(
                'comment_id' => $comment_id,
                'name' => $post['form']['react_name'],
                'email' => $post['form']['react_email'],
                'content' => $post['form']['react_content']
            ));
            
        }
    }
    
}

function getCategoryMainMenu()
{
    $CI =& get_instance();
    
    $sql = '
      SELECT * 
      FROM `category`, `category_content` 
      WHERE `category`.controller = "products"
      AND `category`.category_id = `category_content`.category_id 
      AND `category`.active = 1 
      AND `category`.parent_id = 0
      AND `category_content`.language_id = ' . CUR_LANG . ' 
      AND `category_content`.sub_active = 1
      ORDER BY `category`.order ASC
    ';
    
    $menu = $CI->db->query($sql);
    if(isset($menu) && count($menu) > 0) {
        foreach($menu as $k => $menu_item) {
            $sql = '
              SELECT * 
              FROM `category`, `category_content` 
              WHERE `category`.controller = "products"
              AND `category`.category_id = `category_content`.category_id 
              AND `category`.active = 1 
              AND `category_content`.language_id = ' . CUR_LANG . ' 
              AND `category_content`.sub_active = 1
              AND `category`.parent_id = ? 
              ORDER BY `category`.order ASC
            ';
            
            $r = $CI->db->query($sql, array(
                $menu_item['category_id']
            ));
            if(isset($r) && count($r) > 0) {
                $menu[$k]['subitems'] = $r;
            }
        }
    }
    return $menu;
}

function formatPrice($price)
{
    return number_format(floatval($price), 2, ',', '.');
}

function formatPriceDecimals($price)
{
    return number_format(floatval($price), 2, '.', '');
}

function getProductPrice($price, $discount_percent, $discount_price, $has_vat = 0, $offer = 0, $offer_price = 0)
{
    if($offer == 0) {
        if($discount_percent > 0)
            $price = $price - ($price * $discount_percent / 100);
        else if($discount_price > 0)
            $price = $price - $discount_price;
    } else
        $price = $offer_price;
    if($has_vat == 1) {
        $vat_value = getSettings();
        $vat = $vat_value['vat'];
        $price = $price + ($price * $vat / 100);
    }
    return $price;
}

function getTwitterFeed()
{
    $CI =& get_instance();
    
    $twitter_name = getSettings();

    $datee = date('Y-m-d h:i:s');
    $r = $CI->db->query("SELECT * FROM twitter LIMIT 1");
    if(strtotime(date("Y-m-d h:i:s", strtotime($r[0]['updatee'])) . " +1 hour") < strtotime($datee)) {
        
        $oauth_hash = '';
        
        $oauth_hash .= 'oauth_consumer_key=' . $twitter_name['setConsumerKey'] . '&';
        
        $oauth_hash .= 'oauth_nonce=' . time() . '&';
        
        $oauth_hash .= 'oauth_signature_method=HMAC-SHA1&';
        
        $oauth_hash .= 'oauth_timestamp=' . time() . '&';
        
        $oauth_hash .= 'oauth_token=' . $twitter_name['setOAuthToken'] . '&';
        
        $oauth_hash .= 'oauth_version=1.0';
        
        $base = '';
        
        $base .= 'GET';
        
        $base .= '&';
        
        $base .= rawurlencode('https://api.twitter.com/1.1/statuses/user_timeline.json');
        
        $base .= '&';
        
        $base .= rawurlencode($oauth_hash);
        
        $key = '';
        
        $key .= rawurlencode('' . $twitter_name['setConsumerSecret'] . '');
        
        $key .= '&';
        
        $key .= rawurlencode('' . $twitter_name['setOAuthTokenSecret'] . '');
        
        $signature = base64_encode(hash_hmac('sha1', $base, $key, true));
        
        $signature = rawurlencode($signature);
        
        
        $oauth_header = '';
        
        $oauth_header .= 'oauth_consumer_key="' . $twitter_name['setConsumerKey'] . '", ';
        
        $oauth_header .= 'oauth_nonce="' . time() . '", ';
        
        $oauth_header .= 'oauth_signature="' . $signature . '", ';
        
        $oauth_header .= 'oauth_signature_method="HMAC-SHA1", ';
        
        $oauth_header .= 'oauth_timestamp="' . time() . '", ';
        
        $oauth_header .= 'oauth_token="' . $twitter_name['setOAuthToken'] . '", ';
        
        $oauth_header .= 'oauth_version="1.0", ';
        
        $curl_header = array(
            "Authorization: Oauth {$oauth_header}",
            'Expect:'
        );
        
        $curl_request = curl_init();
        
        curl_setopt($curl_request, CURLOPT_HTTPHEADER, $curl_header);
        
        curl_setopt($curl_request, CURLOPT_HEADER, false);
        
        curl_setopt($curl_request, CURLOPT_URL, 'https://api.twitter.com/1.1/statuses/user_timeline.json');
        
        curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
        
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
        
        $json = curl_exec($curl_request);
        //debug(json_decode($json));
        //echo $json;
        curl_close($curl_request);
        
        $r = $CI->db->query("UPDATE twitter SET text='" . addslashes($json) . "', updatee='" . $datee . "'");
    }
    $r = $CI->db->query("SELECT * FROM twitter LIMIT 1");
    return json_decode($r[0]['text']);
    
}


function countries_list()
{
    return array(
        array(
            'ro' => 'Afganistan',
            'en' => 'Afghanistan'
        ),
        array(
            'ro' => 'Africa de Sud',
            'en' => 'South Africa'
        ),
        array(
            'ro' => 'Albania',
            'en' => 'Albania'
        ),
        array(
            'ro' => 'Algeria',
            'en' => 'Algeria'
        ),
        array(
            'ro' => 'Andorra',
            'en' => 'Andorra'
        ),
        array(
            'ro' => 'Marea Britanie',
            'en' => 'United Kingdom'
        ),
        array(
            'ro' => 'Angola',
            'en' => 'Angola'
        ),
        array(
            'ro' => 'Anguilla',
            'en' => 'Anguilla'
        ),
        array(
            'ro' => 'Antarctica',
            'en' => 'Antarctica'
        ),
        array(
            'ro' => 'Antigua &#351;i Barbuda',
            'en' => 'Antigua and Barbuda'
        ),
        array(
            'ro' => 'Antilele Olandeze',
            'en' => 'Netherlands Antilles'
        ),
        array(
            'ro' => 'Arabia Saudit&#259;',
            'en' => 'Saudi Arabia'
        ),
        array(
            'ro' => 'Argentina',
            'en' => 'Argentina'
        ),
        array(
            'ro' => 'Armenia',
            'en' => 'Armenia'
        ),
        array(
            'ro' => 'Aruba',
            'en' => 'Aruba'
        ),
        array(
            'ro' => 'Australia',
            'en' => 'Australia'
        ),
        array(
            'ro' => 'Austria',
            'en' => 'Austria'
        ),
        array(
            'ro' => 'Azerbaijan',
            'en' => 'Azerbaijan'
        ),
        array(
            'ro' => 'Bahamas',
            'en' => 'Bahamas'
        ),
        array(
            'ro' => 'Bahrain',
            'en' => 'Bahrain'
        ),
        array(
            'ro' => 'Bangladesh',
            'en' => 'Bangladesh'
        ),
        array(
            'ro' => 'Barbados',
            'en' => 'Barbados'
        ),
        array(
            'ro' => 'Belarus',
            'en' => 'Belarus'
        ),
        array(
            'ro' => 'Belgia',
            'en' => 'Belgium'
        ),
        array(
            'ro' => 'Belize',
            'en' => 'Belize'
        ),
        array(
            'ro' => 'Benin',
            'en' => 'Benin'
        ),
        array(
            'ro' => 'Bermude',
            'en' => 'Bermuda'
        ),
        array(
            'ro' => 'Bhutan',
            'en' => 'Bhutan'
        ),
        array(
            'ro' => 'Bolivia',
            'en' => 'Bolivia'
        ),
        array(
            'ro' => 'Bosnia &#351;i Her&#355;egovina',
            'en' => 'Bosnia and Herzegovina'
        ),
        array(
            'ro' => 'Botswana',
            'en' => 'Botswana'
        ),
        array(
            'ro' => 'Brazilia',
            'en' => 'Brazil'
        ),
        array(
            'ro' => 'Brunei',
            'en' => 'Brunei'
        ),
        array(
            'ro' => 'Bulgaria',
            'en' => 'Bulgaria'
        ),
        array(
            'ro' => 'Burkina Faso',
            'en' => 'Burkina Faso'
        ),
        array(
            'ro' => 'Burundi',
            'en' => 'Burundi'
        ),
        array(
            'ro' => 'Cambogia',
            'en' => 'Cambodia'
        ),
        array(
            'ro' => 'Camerun',
            'en' => 'Cameroon'
        ),
        array(
            'ro' => 'Canada',
            'en' => 'Canada'
        ),
        array(
            'ro' => 'Capul Verde',
            'en' => 'Cape Verde'
        ),
        array(
            'ro' => 'Chile',
            'en' => 'Chile'
        ),
        array(
            'ro' => 'China',
            'en' => 'China'
        ),
        array(
            'ro' => 'Christmas Island',
            'en' => 'Christmas Island'
        ),
        array(
            'ro' => 'Ciad',
            'en' => 'Chad'
        ),
        array(
            'ro' => 'Cipru',
            'en' => 'Cyprus'
        ),
        array(
            'ro' => 'Coasta de Filde&#351;',
            'en' => 'Cote D\'Ivoire'
        ),
        array(
            'ro' => 'Columbia',
            'en' => 'Colombia'
        ),
        array(
            'ro' => 'Comoros',
            'en' => 'Comoros'
        ),
        array(
            'ro' => 'Congo',
            'en' => 'Congo'
        ),
        array(
            'ro' => 'Corea',
            'en' => 'Korea'
        ),
        array(
            'ro' => 'Costa Rica',
            'en' => 'Costa Rica'
        ),
        array(
            'ro' => 'Croa&#355;ia',
            'en' => 'Croatia'
        ),
        array(
            'ro' => 'Cuba',
            'en' => 'Cuba'
        ),
        array(
            'ro' => 'Danemarca',
            'en' => 'Denmark'
        ),
        array(
            'ro' => 'Djibouti',
            'en' => 'Djibouti'
        ),
        array(
            'ro' => 'Dominica',
            'en' => 'Dominica'
        ),
        array(
            'ro' => 'Ecuador',
            'en' => 'Ecuador'
        ),
        array(
            'ro' => 'Egipt',
            'en' => 'Egypt'
        ),
        array(
            'ro' => 'El Salvador',
            'en' => 'El Salvador'
        ),
        array(
            'ro' => 'Elve&#355;ia',
            'en' => 'Switzerland'
        ),
        array(
            'ro' => 'Emiratele Arabe Unite',
            'en' => 'United Arab Emirates'
        ),
        array(
            'ro' => 'Eritreea',
            'en' => 'Eritrea'
        ),
        array(
            'ro' => 'Estonia',
            'en' => 'Estonia'
        ),
        array(
            'ro' => 'Etiopia',
            'en' => 'Ethiopia'
        ),
        array(
            'ro' => 'Fiji',
            'en' => 'Fiji'
        ),
        array(
            'ro' => 'Filipine',
            'en' => 'Philippines'
        ),
        array(
            'ro' => 'Finlanda',
            'en' => 'Finland'
        ),
        array(
            'ro' => 'Fran&#355;a',
            'en' => 'France'
        ),
        array(
            'ro' => 'Gabon',
            'en' => 'Gabon'
        ),
        array(
            'ro' => 'Gambia',
            'en' => 'Gambia'
        ),
        array(
            'ro' => 'Georgia',
            'en' => 'Georgia'
        ),
        array(
            'ro' => 'Germania',
            'en' => 'Germany'
        ),
        array(
            'ro' => 'Ghana',
            'en' => 'Ghana'
        ),
        array(
            'ro' => 'Gibraltar',
            'en' => 'Gibraltar'
        ),
        array(
            'ro' => 'Grecia',
            'en' => 'Greece'
        ),
        array(
            'ro' => 'Grenada',
            'en' => 'Grenada'
        ),
        array(
            'ro' => 'Groenlanda',
            'en' => 'Greenland'
        ),
        array(
            'ro' => 'Guadalupe',
            'en' => 'Guadeloupe'
        ),
        array(
            'ro' => 'Guam',
            'en' => 'Guam'
        ),
        array(
            'ro' => 'Guatemala',
            'en' => 'Guatemala'
        ),
        array(
            'ro' => 'Guernsey',
            'en' => 'Guernsey'
        ),
        array(
            'ro' => 'Guinea-Bissau',
            'en' => 'Guinea-Bissau'
        ),
        array(
            'ro' => 'Guineea',
            'en' => 'Guinea'
        ),
        array(
            'ro' => 'Guineea Ecuatorial&#259;',
            'en' => 'Equatorial Guinea'
        ),
        array(
            'ro' => 'Guyana',
            'en' => 'Guyana'
        ),
        array(
            'ro' => 'Guyana Francez&#259;',
            'en' => 'French Guiana'
        ),
        array(
            'ro' => 'Haiti',
            'en' => 'Haiti'
        ),
        array(
            'ro' => 'Honduras',
            'en' => 'Honduras'
        ),
        array(
            'ro' => 'Hong Kong',
            'en' => 'Hong Kong'
        ),
        array(
            'ro' => 'India',
            'en' => 'India'
        ),
        array(
            'ro' => 'Indonezia',
            'en' => 'Indonesia'
        ),
        array(
            'ro' => 'Insula Man',
            'en' => 'Isle of Man'
        ),
        array(
            'ro' => 'Insula Norfolk',
            'en' => 'Norfolk Island'
        ),
        array(
            'ro' => 'Insulele Cayman',
            'en' => 'Cayman Islands'
        ),
        array(
            'ro' => 'Insulele Cook',
            'en' => 'Cook Islands'
        ),
        array(
            'ro' => 'Insulele Falkland',
            'en' => 'Falkland Islands'
        ),
        array(
            'ro' => 'Insulele Feroe',
            'en' => 'Faeroe Islands'
        ),
        array(
            'ro' => 'Insulele Maldive',
            'en' => 'Maldives'
        ),
        array(
            'ro' => 'Insulele Mariane de Nord',
            'en' => 'Northern Mariana Islands'
        ),
        array(
            'ro' => 'Insulele Marshall',
            'en' => 'Marshall Islands'
        ),
        array(
            'ro' => 'Insulele Reunion',
            'en' => 'Reunion Island'
        ),
        array(
            'ro' => 'Insulele Solomon',
            'en' => 'Solomon Islands'
        ),
        array(
            'ro' => 'Insulele Turks & Caicos',
            'en' => 'Turks & Caicos Islands'
        ),
        array(
            'ro' => 'Insulele Virgine (Britanice)',
            'en' => 'Virgin Islands (British)'
        ),
        array(
            'ro' => 'Insulele Virgine (S.U.A.)',
            'en' => 'Virgin Islands (U.S.)'
        ),
        array(
            'ro' => 'Insulele Wallis & Futuna',
            'en' => 'Wallis & Futuna Islands'
        ),
        array(
            'ro' => 'Iordania',
            'en' => 'Jordan'
        ),
        array(
            'ro' => 'Irak',
            'en' => 'Iraq'
        ),
        array(
            'ro' => 'Iran',
            'en' => 'Iran'
        ),
        array(
            'ro' => 'Irlanda',
            'en' => 'Ireland'
        ),
        array(
            'ro' => 'Irlanda de Nord',
            'en' => 'Northern Ireland'
        ),
        array(
            'ro' => 'Islanda',
            'en' => 'Iceland'
        ),
        array(
            'ro' => 'Israel',
            'en' => 'Israel'
        ),
        array(
            'ro' => 'Italia',
            'en' => 'Italy'
        ),
        array(
            'ro' => 'Jamaica',
            'en' => 'Jamaica'
        ),
        array(
            'ro' => 'Japonia',
            'en' => 'Japan'
        ),
        array(
            'ro' => 'Jersey',
            'en' => 'Jersey'
        ),
        array(
            'ro' => 'Kazahstan',
            'en' => 'Kazakhstan'
        ),
        array(
            'ro' => 'Kenya',
            'en' => 'Kenya'
        ),
        array(
            'ro' => 'Kirghizstan',
            'en' => 'Kyrgyzstan'
        ),
        array(
            'ro' => 'Kiribati',
            'en' => 'Kiribati'
        ),
        array(
            'ro' => 'Kosovo',
            'en' => 'Kosovo'
        ),
        array(
            'ro' => 'Kuweit',
            'en' => 'Kuwait'
        ),
        array(
            'ro' => 'Laos',
            'en' => 'Laos'
        ),
        array(
            'ro' => 'Lesotho',
            'en' => 'Lesotho'
        ),
        array(
            'ro' => 'Letonia',
            'en' => 'Latvia'
        ),
        array(
            'ro' => 'Liban',
            'en' => 'Lebanon'
        ),
        array(
            'ro' => 'Liberia',
            'en' => 'Liberia'
        ),
        array(
            'ro' => 'Libia',
            'en' => 'Libya'
        ),
        array(
            'ro' => 'Liechtenstein',
            'en' => 'Liechtenstein'
        ),
        array(
            'ro' => 'Lituania',
            'en' => 'Lithuania'
        ),
        array(
            'ro' => 'Luxemburg',
            'en' => 'Luxembourg'
        ),
        array(
            'ro' => 'Macao',
            'en' => 'Macau'
        ),
        array(
            'ro' => 'Macedonia',
            'en' => 'Macedonia'
        ),
        array(
            'ro' => 'Madagascar',
            'en' => 'Madagascar'
        ),
        array(
            'ro' => 'Malaezia',
            'en' => 'Malaysia'
        ),
        array(
            'ro' => 'Malawi',
            'en' => 'Malawi'
        ),
        array(
            'ro' => 'Mali',
            'en' => 'Mali'
        ),
        array(
            'ro' => 'Malta',
            'en' => 'Malta'
        ),
        array(
            'ro' => 'Maroc',
            'en' => 'Morocco'
        ),
        array(
            'ro' => 'Martinica',
            'en' => 'Martinique'
        ),
        array(
            'ro' => 'Mauritania',
            'en' => 'Mauritania'
        ),
        array(
            'ro' => 'Mauritius',
            'en' => 'Mauritius'
        ),
        array(
            'ro' => 'Mayotte',
            'en' => 'Mayotte'
        ),
        array(
            'ro' => 'Mexic',
            'en' => 'Mexico'
        ),
        array(
            'ro' => 'Micronezia',
            'en' => 'Micronesia'
        ),
        array(
            'ro' => 'Monaco',
            'en' => 'Monaco'
        ),
        array(
            'ro' => 'Mongolia',
            'en' => 'Mongolia'
        ),
        array(
            'ro' => 'Montserrat',
            'en' => 'Montserrat'
        ),
        array(
            'ro' => 'Mozambic',
            'en' => 'Mozambique'
        ),
        array(
            'ro' => 'Muntenegru',
            'en' => 'Montenegro'
        ),
        array(
            'ro' => 'Myanmar',
            'en' => 'Myanmar'
        ),
        array(
            'ro' => 'Namibia',
            'en' => 'Namibia'
        ),
        array(
            'ro' => 'Nauru',
            'en' => 'Nauru'
        ),
        array(
            'ro' => 'Nepal',
            'en' => 'Nepal'
        ),
        array(
            'ro' => 'Nicaragua',
            'en' => 'Nicaragua'
        ),
        array(
            'ro' => 'Niger',
            'en' => 'Niger'
        ),
        array(
            'ro' => 'Nigeria',
            'en' => 'Nigeria'
        ),
        array(
            'ro' => 'Niue',
            'en' => 'Niue'
        ),
        array(
            'ro' => 'Norvegia',
            'en' => 'Norway'
        ),
        array(
            'ro' => 'Noua Caledonie',
            'en' => 'New Caledonia'
        ),
        array(
            'ro' => 'Noua Zeeland&#259;',
            'en' => 'New Zealand'
        ),
        array(
            'ro' => 'Olanda',
            'en' => 'Netherlands'
        ),
        array(
            'ro' => 'Oman',
            'en' => 'Oman'
        ),
        array(
            'ro' => 'Pakistan',
            'en' => 'Pakistan'
        ),
        array(
            'ro' => 'Palau',
            'en' => 'Palau'
        ),
        array(
            'ro' => 'Panama',
            'en' => 'Panama'
        ),
        array(
            'ro' => 'Papua Noua Guinee',
            'en' => 'Papua New Guinea'
        ),
        array(
            'ro' => 'Paraguay',
            'en' => 'Paraguay'
        ),
        array(
            'ro' => 'Peru',
            'en' => 'Peru'
        ),
        array(
            'ro' => 'Polinezia Francez&#259;',
            'en' => 'French Polynesia'
        ),
        array(
            'ro' => 'Polonia',
            'en' => 'Poland'
        ),
        array(
            'ro' => 'Portugalia',
            'en' => 'Portugal'
        ),
        array(
            'ro' => 'Puerto Rico',
            'en' => 'Puerto Rico'
        ),
        array(
            'ro' => 'Qatar',
            'en' => 'Qatar'
        ),
        array(
            'ro' => 'Republica Arab&#259; Sirian&#259;',
            'en' => 'Syrian Arab Republic'
        ),
        array(
            'ro' => 'Republica Ceh&#259;',
            'en' => 'Czech Republic'
        ),
        array(
            'ro' => 'Republica Centrafrican&#259;',
            'en' => 'Central African Republic'
        ),
        array(
            'ro' => 'Republica Dominican&#259;',
            'en' => 'Dominican Republic'
        ),
        array(
            'ro' => 'Republica Moldova',
            'en' => 'Moldova'
        ),
        array(
            'ro' => 'Rom&#226;nia',
            'en' => 'Romania'
        ),
        array(
            'ro' => 'Rusia',
            'en' => 'Russia'
        ),
        array(
            'ro' => 'Rwanda',
            'en' => 'Rwanda'
        ),
        array(
            'ro' => 'Sahara de Vest',
            'en' => 'Western Sahara'
        ),
        array(
            'ro' => 'Samoa',
            'en' => 'Samoa'
        ),
        array(
            'ro' => 'Samoa American&#259;',
            'en' => 'American Samoa'
        ),
        array(
            'ro' => 'San Marino',
            'en' => 'San Marino'
        ),
        array(
            'ro' => 'Sao Tome &#351;i Principe',
            'en' => 'Sao Tome and Principe'
        ),
        array(
            'ro' => 'Sco&#355;ia',
            'en' => 'Scotland'
        ),
        array(
            'ro' => 'Senegal',
            'en' => 'Senegal'
        ),
        array(
            'ro' => 'Serbia',
            'en' => 'Serbia'
        ),
        array(
            'ro' => 'Seychelles',
            'en' => 'Seychelles'
        ),
        array(
            'ro' => 'Sf. Elena',
            'en' => 'St. Helena'
        ),
        array(
            'ro' => 'Sierra Leone',
            'en' => 'Sierra Leone'
        ),
        array(
            'ro' => 'Singapore',
            'en' => 'Singapore'
        ),
        array(
            'ro' => 'Slovacia',
            'en' => 'Slovakia'
        ),
        array(
            'ro' => 'Slovenia',
            'en' => 'Slovenia'
        ),
        array(
            'ro' => 'Somalia',
            'en' => 'Somalia'
        ),
        array(
            'ro' => 'Spania',
            'en' => 'Spain'
        ),
        array(
            'ro' => 'Sri Lanka',
            'en' => 'Sri Lanka'
        ),
        array(
            'ro' => 'St. Kitts &#351;i Nevis',
            'en' => 'St. Kitts and Nevis'
        ),
        array(
            'ro' => 'St. Lucia',
            'en' => 'St. Lucia'
        ),
        array(
            'ro' => 'St. Pierre &#351;i Miquelon',
            'en' => 'St. Pierre and Miquelon'
        ),
        array(
            'ro' => 'St. Vincent / Grenada',
            'en' => 'St. Vincent / Grenadines'
        ),
        array(
            'ro' => 'Statele Unite',
            'en' => 'United States'
        ),
        array(
            'ro' => 'Statul Vatican',
            'en' => 'Vatican City State'
        ),
        array(
            'ro' => 'Sudan',
            'en' => 'Sudan'
        ),
        array(
            'ro' => 'Suedia',
            'en' => 'Sweden'
        ),
        array(
            'ro' => 'Surinam',
            'en' => 'Suriname'
        ),
        array(
            'ro' => 'Swaziland',
            'en' => 'Swaziland'
        ),
        array(
            'ro' => 'Tadjikistan',
            'en' => 'Tajikistan'
        ),
        array(
            'ro' => 'Taiwan',
            'en' => 'Taiwan'
        ),
        array(
            'ro' => 'Tanzania',
            'en' => 'Tanzania'
        ),
        array(
            'ro' => '&#354;ara Galilor',
            'en' => 'Wales'
        ),
        array(
            'ro' => 'Thailanda',
            'en' => 'Thailand'
        ),
        array(
            'ro' => 'Timorul de Est',
            'en' => 'East Timor'
        ),
        array(
            'ro' => 'Togo',
            'en' => 'Togo'
        ),
        array(
            'ro' => 'Tokelau',
            'en' => 'Tokelau'
        ),
        array(
            'ro' => 'Tonga',
            'en' => 'Tonga'
        ),
        array(
            'ro' => 'Trinidad Tobago',
            'en' => 'Trinidad and Tobago'
        ),
        array(
            'ro' => 'Tunisia',
            'en' => 'Tunisia'
        ),
        array(
            'ro' => 'Turcia',
            'en' => 'Turkey'
        ),
        array(
            'ro' => 'Turkmenistan',
            'en' => 'Turkmenistan'
        ),
        array(
            'ro' => 'Tuvalu',
            'en' => 'Tuvalu'
        ),
        array(
            'ro' => 'Ucraina',
            'en' => 'Ukraine'
        ),
        array(
            'ro' => 'Uganda',
            'en' => 'Uganda'
        ),
        array(
            'ro' => 'Ungaria',
            'en' => 'Hungary'
        ),
        array(
            'ro' => 'Uruguay',
            'en' => 'Uruguay'
        ),
        array(
            'ro' => 'Uzbekistan',
            'en' => 'Uzbekistan'
        ),
        array(
            'ro' => 'Vanuatu',
            'en' => 'Vanuatu'
        ),
        array(
            'ro' => 'Venezuela',
            'en' => 'Venezuela'
        ),
        array(
            'ro' => 'Vietnam',
            'en' => 'Vietnam'
        ),
        array(
            'ro' => 'Yemen',
            'en' => 'Yemen'
        ),
        array(
            'ro' => 'Zambia',
            'en' => 'Zambia'
        ),
        array(
            'ro' => 'Zimbabwe',
            'en' => 'Zimbabwe'
        )
    );
}

?>