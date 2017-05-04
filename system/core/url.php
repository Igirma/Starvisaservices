<?php 
class url
{
  var $url_string;
  var $current;
  var $segments = array();
  var $protocol;
  var $db;
  var $form;
  
  function __construct()
  {
    $this->config =& load_class('config', 'core');
    $this->db =& load_class('db', 'core');
  }
  
  /**
   * Fetch the url and set it based on uri protocol
   */
  function _fetch_url_string()
  {	
    $path = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');
    if(trim($path, '/') != '' && $path != '/' . @self)
    {
      $this->_set_url_string($path);
      return;
    }
    
    # No PATH_INFO?... What about REQUEST_URI?
    $path =  (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : @getenv('REQUEST_URI');
    if(trim($path, '/') != '')
    {
      $this->protocol = 'REQUEST_URI';
      $this->_set_url_string($path);
      return;
    }
    
    # No PATH_INFO?... What about QUERY_STRING?
    $path =  (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : @getenv('QUERY_STRING');
    if(trim($path, '/') != '')
    {
      $this->_set_url_string($path);
      return;
    }
    
    $this->url_string = '';
    return;
  }
  
  function _set_url_string($str)
  {
    $str = remove_invisible_characters($str, false);
    $this->url_string = ($str == '/') ? '' : rtrim($str, '/');
    $this->current = SITE_URL_WOSLASH . $this->url_string;
    define('CURRENT_URL', $this->current);
  }
  
  /**
   * Filter the given URL from bad characters
   * @param 	string 	$str
   */
  function _filter_url($str)
  {
    if ($str != '' && $this->config->item('permitted_uri_chars') != '' && $this->config->item('enable_query_strings') == false)
    {
      if(!preg_match("|^[".str_replace(array('\\-', '\-'), '-', preg_quote($this->config->item('permitted_url_chars'), '-'))."]+$|i", $str))
      {
        die('The URL you submitted has disallowed characters.');
      }
    }
  
    # Convert programatic characters to entities
    $bad	= array('$',		'(',		')',		'%28',		'%29');
    $good	= array('&#36;',	'&#40;',	'&#41;',	'&#40;',	'&#41;');
  
    return str_replace($bad, $good, $str);
  }
  
  /**
   * Explode the URL based on URI protocol
   */
  function _explode_segments()
  {
    foreach (explode("/", preg_replace("|/*(.+?)/*$|", "\\1", $this->url_string)) as $val)
    {
      # Filter segments for security
      $val = trim($this->_filter_url($val));
  
      if ($val != '')
      {
        $this->segments[] = $val;
      }
    }
    
    # set languages based on first url part
    
    $lang = false;

    if(isset($this->segments[0]) && ($this->segments[0] != '' || !empty($this->segments[0])))
    {
      $lang = $this->db->query('SELECT * FROM language WHERE code = "' . $this->segments[0] . '"');
    }

    
    # define CONST based on language data
    if($lang != false)
    {
      define('LANG_CODE', $lang[0]['code']);
      define('CUR_LANG', $lang[0]['language_id']);

      define('BASE_URL', SITE_URL . LANG_CODE . '/');

      # remove the language code from the url segments array
      array_shift($this->segments);
    }
    else
    {
      define('LANG_CODE', $this->config->item('default_language_code'));
      define('CUR_LANG', $this->config->item('default_language'));
      
      define('BASE_URL', SITE_URL);
    }
    
    # remove first url segment when REQUEST_URI is used
    # the first url segment will be a directory
    # TODO: check for errors on domains other than beeldbelovend
    if(BASE_HOST == 'arramedia' && $this->protocol == 'REQUEST_URI')
    {
      array_shift($this->segments);
      //array_shift($this->segments);
    }
  }
  
  /**
   * Return a part of the url as a string
   * @param 	int 	$n	The part of the url to return starting from 0
   * @return 	string		Part of the url
   */
  function segment($n)
  {
    return (!isset($this->segments[$n])) ? false : $this->segments[$n];
  }

  function redirect($url = '', $http_response_code = 303, $method = 'location')
  {
    switch($method)
    {
      case 'refresh'	: header('Refresh:0;url=' . $url);
        break;
      default			: header('Location: ' . $url, TRUE, $http_response_code);
        break;
    }
    exit;
  }
  
  /**
   * Convert a string to a url and strip bad characters
   * @param string $str
   * @param string $separator
   * @param bool $lowercase
   */
  function string_to_url($str, $separator = 'dash')
  {
    if ($separator == 'dash')
    {
      $search		= '_';
      $replace	= '-';
    }
    else
    {
      $search		= '-';
      $replace	= '_';
    }

    $str = remove_accents($str);
  
    $trans = array(
        '&\#\d+?;'				=> '',
        '&\S+?;'				=> '',
        '\s+'					=> $replace,
        '[^a-z0-9\-\._]'		=> '',
        $replace.'+'			=> $replace,
        $replace.'$'			=> $replace,
        '^'.$replace			=> $replace,
        '\.+$'					=> ''
    );
  
    $str = strip_tags($str);
  
    foreach ($trans as $key => $val)
    {
      $str = preg_replace("#".$key."#i", $val, $str);
    }
  
    return strtolower(trim(stripslashes($str)));
  }
}
?>