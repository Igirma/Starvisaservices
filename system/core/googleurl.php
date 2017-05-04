<?php

class googleurl
{
    public $longUrl = '';
    public $apikey = '';
    public $jsonData;
    public $shortUrl;
    
    public function makeRequest($link = '')
    {
		$this->longUrl = $link;
		
		if($this->longUrl != '')
		{
			$postData = array('longUrl' => $this->longUrl, 'key' => $this->apikey);
			$this->jsonData = json_encode($postData);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->jsonData);
			
			$return = curl_exec($ch);
			
			$object = json_decode($return);
			
			$this->shortUrl = $object->id;
			
			return $this->shortUrl;
		}
    }
    
}

?>