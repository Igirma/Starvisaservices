<?php
/**
 *
 * Language Detector
 * Version: 1.4.2
 * Date: 2014-02-06
 * Copyright (c) 2012-2013 Peter Kahl. All rights reserved.
 * Use of this source code is governed by a GNU General Public License
 * that can be found in the LICENSE file.
 *
 * https://github.com/peterkahl/language-detector
 *
 */
class language_detector
{
    public $language_default;
    protected $lang_lang_map;
    protected $country_code;
    protected $use_cookie;
    protected $cookie_name;
    protected $cookie_ssl;
    protected $anon_proxy;
    protected $cookie_host;
    protected $cookie_expire_date;
    protected $cookie_http_only;
    protected $locale;
    //------------------------------------------------------------------
    public function __construct($params = '')
    {
        if (!is_array($params))
            die('Language Detector error: Variable params must be array.');
        if (!isset($params['LANGUAGE_DEFAULT']))
            $this->language_default = 'en_GB';
        else
            $this->language_default = $params['LANGUAGE_DEFAULT'];
        // Language codes in accept language header:
        // https://www.iana.org/assignments/language-subtag-registry/language-subtag-registry
        if (!isset($params['LANG_LANG_MAP']))
            $this->lang_lang_map = array(
                'en*' => 'en_GB',
                'zh-cn' => 'zh_CN',
                'zh-sg' => 'zh_CN',
                'zh-hans*' => 'zh_CN',
                'zh-hk' => 'zh_HK',
                'zh-mo' => 'zh_HK',
                'zh-tw' => 'zh_HK',
                'zh-hant*' => 'zh_HK',
                'zh-yue*' => 'zh_HK',
                'zh' => 'zh_CN'
            );
        else
            $this->lang_lang_map = $params['LANG_LANG_MAP'];
        if (!isset($params['COUNTRY_CODE']))
            $this->country_code = '';
        else
            $this->country_code = strtolower($params['COUNTRY_CODE']);
        if (!isset($params['USE_COOKIE']) || $params['USE_COOKIE'] == '')
            $this->use_cookie = false;
        else
            $this->use_cookie = $params['USE_COOKIE'];
        if (!isset($params['LOCALE']) || $params['LOCALE'] == '')
            $this->locale = false;
        else
            $this->locale = $params['LOCALE'];
        if (!isset($params['COOKIE_NAME']) || $params['COOKIE_NAME'] == '')
            $this->cookie_name = 'CMLANG';
        else
            $this->cookie_name = $params['COOKIE_NAME'];
        if (!isset($params['COOKIE_SSL']) || $params['COOKIE_SSL'] == '')
            $this->cookie_ssl = false;
        else
            $this->cookie_ssl = $params['COOKIE_SSL'];
        if (!isset($params['ANON_PROXY']) || $params['ANON_PROXY'] == '')
            $this->anon_proxy = 'no';
        else
            $this->anon_proxy = 'yes';
        $this->cookie_host        = $this->resetexplode(':', $_SERVER['HTTP_HOST']);
        $this->cookie_expire_date = time() + intval(20 * 365.25 * 86400); // very long time
        $this->cookie_http_only   = true;
    }
    //------------------------------------------------------------------
    public function detect_language()
    {
        // cookie method first
        if ($this->use_cookie) {
            if (isset($_COOKIE[$this->cookie_name]) && in_array($_COOKIE[$this->cookie_name], $this->lang_lang_map)) {
                $output[0] = $_COOKIE[$this->cookie_name];
                $output[1] = 'cookie';
                // refresh cookie
                setcookie($this->cookie_name, $output[0], $this->cookie_expire_date, "/", $this->cookie_host, $this->cookie_ssl, $this->cookie_http_only);
                return $output;
            }
        }
        // parse Accept-Language header
        $parsed = $this->parse_accept_language();
        if ($parsed !== false) {
            $temp = $this->locale_to_language($parsed);
            if ($temp !== false) {
                $output[0] = $temp;
                $output[1] = 'accept-language';
                if ($this->use_cookie)
                    setcookie($this->cookie_name, $output[0], $this->cookie_expire_date, "/", $this->cookie_ssl, $this->cookie_http_only);
                return $output;
            }
        }
        // language from locale
        if ($this->locale !== false) {
            $temp = $this->locale_to_language($this->locale);
            if ($temp !== false) {
                $output[0] = $temp;
                $output[1] = 'locale';
                if ($this->use_cookie)
                    setcookie($this->cookie_name, $output[0], $this->cookie_expire_date, "/", $this->cookie_ssl, $this->cookie_http_only);
                return $output;
            }
        }
        // language from country
        if ($this->country_code !== '' && $this->anon_proxy == 'no') {
            $parsed = $this->country_to_locale();
            if ($parsed !== false) {
                $temp = $this->locale_to_language($parsed);
                if ($temp !== false) {
                    $output[0] = $temp;
                    $output[1] = 'country';
                    if ($this->use_cookie)
                        setcookie($this->cookie_name, $output[0], $this->cookie_expire_date, "/", $this->cookie_ssl, $this->cookie_http_only);
                    return $output;
                }
            }
        }
        // let's use default
        $output[0] = $this->language_default;
        $output[1] = 'default';
        if ($this->use_cookie)
            setcookie($this->cookie_name, $output[0], $this->cookie_expire_date, "/", $this->cookie_ssl, $this->cookie_http_only);
        return $output;
    }
    //------------------------------------------------------------------
    private function parse_accept_language()
    {
        if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]) && $_SERVER["HTTP_ACCEPT_LANGUAGE"] != '') {
            // en-GB,en;q=0.8,en-US;q=0.6
            $accept = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $lang   = explode(',', $accept); // array
            foreach ($lang as $key => $val) {
                $lang[$key] = $this->resetexplode(';', $val);
            }
            return $lang; // array('en-gb','en','en-us')
        }
        return false;
    }
    //------------------------------------------------------------------
    private function locale_to_language($loc)
    {
        if (!is_array($loc)) {
            $loc = explode(',', $loc);
        }
        foreach ($loc as $key => $val) {
            $loc[$key] = str_replace('_', '-', strtolower($val));
        }
        foreach ($loc as $acode) {
            foreach ($this->lang_lang_map as $lmcode => $target) {
                if (strpos($lmcode, '*') !== false) {
                    $lmcode = rtrim($lmcode, '*');
                    $len    = strlen($lmcode);
                    if ($lmcode == substr($acode, 0, $len))
                        return $target;
                }
                if (str_replace('_', '-', strtolower($target)) == $acode)
                    return $target;
                if ($lmcode == $acode)
                    return $target;
            }
        }
        return false;
    }
    //------------------------------------------------------------------
    private function resetexplode($glue, $str)
    {
        $str = explode($glue, $str);
        $str = reset($str);
        return $str;
    }
    //------------------------------------------------------------------
    private function country_to_locale()
    {
        // http://wiki.openstreetmap.org/wiki/Nominatim/Country_Codes
        $arr  = array(
            'ad' => 'ca',
            'ae' => 'ar',
            'af' => 'fa,ps',
            'ag' => 'en',
            'ai' => 'en',
            'al' => 'sq',
            'am' => 'hy',
            'an' => 'nl,en',
            'ao' => 'pt',
            'aq' => 'en',
            'ar' => 'es',
            'as' => 'en,sm',
            'at' => 'de',
            'au' => 'en',
            'aw' => 'nl,pap',
            'ax' => 'sv',
            'az' => 'az',
            'ba' => 'bs,hr,sr',
            'bb' => 'en',
            'bd' => 'bn',
            'be' => 'nl,fr,de',
            'bf' => 'fr',
            'bg' => 'bg',
            'bh' => 'ar',
            'bi' => 'fr',
            'bj' => 'fr',
            'bl' => 'fr',
            'bm' => 'en',
            'bn' => 'ms',
            'bo' => 'es,qu,ay',
            'br' => 'pt',
            'bs' => 'en',
            'bt' => 'dz',
            'bv' => 'no',
            'bw' => 'en,tn',
            'by' => 'be,ru',
            'bz' => 'en',
            'ca' => 'en,fr',
            'cc' => 'en',
            'cd' => 'fr',
            'cf' => 'fr',
            'cg' => 'fr',
            'ch' => 'de,fr,it,rm',
            'ci' => 'fr',
            'ck' => 'en,rar',
            'cl' => 'es',
            'cm' => 'fr,en',
            'cn' => 'zh',
            'co' => 'es',
            'cr' => 'es',
            'cu' => 'es',
            'cv' => 'pt',
            'cx' => 'en',
            'cy' => 'el,tr',
            'cz' => 'cs',
            'de' => 'de',
            'dj' => 'fr,ar,so',
            'dk' => 'da',
            'dm' => 'en',
            'do' => 'es',
            'dz' => 'ar',
            'ec' => 'es',
            'ee' => 'et',
            'eg' => 'ar',
            'eh' => 'ar,es,fr',
            'er' => 'ti,ar,en',
            'es' => 'ast,ca,es,eu,gl',
            'et' => 'am,om',
            'fi' => 'fi,sv,se',
            'fj' => 'en',
            'fk' => 'en',
            'fm' => 'en',
            'fo' => 'fo',
            'fr' => 'fr',
            'ga' => 'fr',
            'gb' => 'en,ga,cy,gd,kw',
            'gd' => 'en',
            'ge' => 'ka',
            'gf' => 'fr',
            'gg' => 'en',
            'gh' => 'en',
            'gi' => 'en',
            'gl' => 'kl,da',
            'gm' => 'en',
            'gn' => 'fr',
            'gp' => 'fr',
            'gq' => 'es,fr,pt',
            'gr' => 'el',
            'gs' => 'en',
            'gt' => 'es',
            'gu' => 'en,ch',
            'gw' => 'pt',
            'gy' => 'en',
            'hk' => 'zh,en',
            'hm' => 'en',
            'hn' => 'es',
            'hr' => 'hr',
            'ht' => 'fr,ht',
            'hu' => 'hu',
            'id' => 'id',
            'ie' => 'en,ga',
            'il' => 'he',
            'im' => 'en',
            'in' => 'hi,en',
            'io' => 'en',
            'iq' => 'ar,ku',
            'ir' => 'fa',
            'is' => 'is',
            'it' => 'it,de,fr',
            'je' => 'en',
            'jm' => 'en',
            'jo' => 'ar',
            'jp' => 'ja',
            'ke' => 'sw,en',
            'kg' => 'ky,ru',
            'kh' => 'km',
            'ki' => 'en',
            'km' => 'ar,fr',
            'kn' => 'en',
            'kp' => 'ko',
            'kr' => 'ko,en',
            'kw' => 'ar',
            'ky' => 'en',
            'kz' => 'kk,ru',
            'la' => 'lo',
            'lb' => 'ar,fr',
            'lc' => 'en',
            'li' => 'de',
            'lk' => 'si,ta',
            'lr' => 'en',
            'ls' => 'en,st',
            'lt' => 'lt',
            'lu' => 'lb,fr,de',
            'lv' => 'lv',
            'ly' => 'ar',
            'ma' => 'ar',
            'mc' => 'fr',
            'md' => 'ru,uk,ro',
            'me' => 'srp,sq,bs,hr,sr',
            'mf' => 'fr',
            'mg' => 'mg,fr',
            'mh' => 'en,mh',
            'mk' => 'mk',
            'ml' => 'fr',
            'mm' => 'my',
            'mn' => 'mn',
            'mo' => 'zh,pt',
            'mp' => 'ch',
            'mq' => 'fr',
            'mr' => 'ar,fr',
            'ms' => 'en',
            'mt' => 'mt,en',
            'mu' => 'mfe,fr,en',
            'mv' => 'dv',
            'mw' => 'en,ny',
            'mx' => 'es',
            'my' => 'ms',
            'mz' => 'pt',
            'na' => 'en,sf,de',
            'nc' => 'fr',
            'ne' => 'fr',
            'nf' => 'en,pih',
            'ng' => 'en',
            'ni' => 'es',
            'nl' => 'nl',
            'no' => 'nb,nn,no,se',
            'np' => 'ne',
            'nr' => 'na,en',
            'nu' => 'niu,en',
            'nz' => 'mi,en',
            'om' => 'ar',
            'pa' => 'es',
            'pe' => 'es',
            'pf' => 'fr',
            'pg' => 'en,tpi,ho',
            'ph' => 'en,tl',
            'pk' => 'en,ur',
            'pl' => 'pl',
            'pm' => 'fr',
            'pn' => 'en,pih',
            'pr' => 'es,en',
            'ps' => 'ar,he',
            'pt' => 'pt',
            'pw' => 'en,pau,ja,sov,tox',
            'py' => 'es,gn',
            'qa' => 'ar',
            're' => 'fr',
            'ro' => 'ro',
            'rs' => 'sr',
            'ru' => 'ru',
            'rw' => 'rw,fr,en',
            'sa' => 'ar',
            'sb' => 'en',
            'sc' => 'fr,en,crs',
            'sd' => 'ar,en',
            'se' => 'sv',
            'sg' => 'en,ms,zh,ta',
            'sh' => 'en',
            'si' => 'sl',
            'sj' => 'no',
            'sk' => 'sk',
            'sl' => 'en',
            'sm' => 'it',
            'sn' => 'fr',
            'so' => 'so,ar',
            'sr' => 'nl',
            'st' => 'pt',
            'ss' => 'en',
            'sv' => 'es',
            'sy' => 'ar',
            'sz' => 'en,ss',
            'tc' => 'en',
            'td' => 'fr,ar',
            'tf' => 'fr',
            'tg' => 'fr',
            'th' => 'th',
            'tj' => 'tg,ru',
            'tk' => 'tkl,en,sm',
            'tl' => 'pt,tet',
            'tm' => 'tk',
            'tn' => 'ar',
            'to' => 'en',
            'tr' => 'tr',
            'tt' => 'en',
            'tv' => 'en',
            'tw' => 'zh',
            'tz' => 'sw,en',
            'ua' => 'uk',
            'ug' => 'en,sw',
            'um' => 'en',
            'us' => 'en',
            'uy' => 'es',
            'uz' => 'uz,kaa',
            'va' => 'it',
            'vc' => 'en',
            've' => 'es',
            'vg' => 'en',
            'vi' => 'en',
            'vn' => 'vi',
            'vu' => 'bi,en,fr',
            'wf' => 'fr',
            'ws' => 'sm,en',
            'ye' => 'ar',
            'yt' => 'fr',
            'za' => 'zu,xh,af,st,tn,en',
            'zm' => 'en',
            'zw' => 'en,sn,nd'
        );
        $code = strtolower($this->country_code);
        if ($code == 'uk')
            $code = 'gb';
        if (array_key_exists($code, $arr)) {
            if (strpos($arr[$code], ',') !== false) {
                $new = explode(',', $arr[$code]);
                $n   = 0;
                $loc = array();
                foreach ($new as $key => $val) {
                    $loc[$n] = $val . '_' . strtoupper($code);
                    $n++;
                }
                return $loc;
            } else {
                return $arr[$code] . '_' . strtoupper($code);
            }
        }
        return false;
    }
}