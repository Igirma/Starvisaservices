<?php
/**
 * Language Class
 */
class lang 
{

	var $language	= array();
	var $is_loaded	= array();

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function __construct()
	{
		
	}

	// --------------------------------------------------------------------

	/**
	 * Load a language file
	 *
	 * @access	public
	 * @param	mixed	the name of the language file to be loaded. Can be an array
	 * @param	string	the language code (en, etc.)
	 * @return	mixed
	 */
	function load($langfile = '', $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = 'system')
	{
		$alt_path = 'system/';
		
		$langfile = str_replace('.php', '', $langfile);

		if($add_suffix == TRUE)
		{
			$langfile = str_replace('_lang.', '', $langfile).'_lang';
		}

		$langfile .= '.php';

		if(in_array($langfile, $this->is_loaded, true))
		{
			return;
		}

		$config =& get_config();

		if($idiom == '')
		{
			$deft_lang = LANG_CODE;
			
			# TODO: use $config[default_language_code] or static?
			$idiom = ($deft_lang == '') ? 'nl' : $deft_lang;
		}

		# determine where the language file is and load it
		if($alt_path != '' && file_exists($alt_path.'language/'.$idiom.'/'.$langfile))
		{
			include($alt_path.'language/'.$idiom.'/'.$langfile);
		}


		if(!isset($lang))
		{
			# TODO: use $config[default_language_code] or static?
			include($alt_path.'language/nl/'.$langfile);
		}

		if($return == true)
		{
			return $lang;
		}

		$this->is_loaded[] = $langfile;
		$this->language = array_merge($this->language, $lang);
		unset($lang);

		//log_msg('Language file loaded: language/'.$idiom.'/'.$langfile);
		return true;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch a single line of text from the language array
	 *
	 * @access	public
	 * @param	string	$line	the language line
	 * @return	string
	 */
	function line($line = '')
	{
		$line = ($line == '' OR !isset($this->language[$line])) ? false : $this->language[$line];

		if ($line === false)
		{
			log_msg('Could not find the language line "' . $line . '"');
		}

		return $line;
	}

}
?>