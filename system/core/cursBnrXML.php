<?php

if (CUR_LANG == 1) {
    setlocale(LC_ALL, 'ro_RO');
}

/*
* Class cursBnrXML v1.0
* Author: Ciuca Valeriu
* E-mail: vali.ciuca@gmail.com
* This class parses BNR's XML and returns the current exchange rate
*
* Requirements: PHP5 
*
* Last update: October 2011, 27     
* More info: www.curs-valutar-bnr.ro
*
*/

class cursBnrXML
{
  /**
   * xml document
   * @var string
   */
   var $xmlDocument = "";
   
  /**
   * exchange date
   * BNR date format is Y-m-d
   * @var string
   */
   var $date = "";
   
  /**
   * exchange date (readable)
   * @var string
   */
   var $currency_date = "";
   
  /**
   * currency
   * @var associative array
   */
   var $currency = array();
   
  /**
   * DB currency
   * @var associative array
   */
   var $currencies = array();
   
  /**
   * db object
   */
   var $db;
   
   /**
   * cursBnrXML class constructor
   *
   * @access        public
   * @param         $url        string
   * @return        void
   */
  function cursBnrXML()
  {
      $this->db =& load_class('db', 'core');
      
      if (!$this->currency_is_outdated()) 
      {
          $this->xmlDocument = file_get_contents('http://www.bnr.ro/nbrfxrates.xml');
          $this->parseXMLDocument();
          $this->update_currency();
      }
      $this->load_today_currency();
  }

  /**
   * Updates currency in the database
   *
   * @access        private
   * @return        string
   */
  private function update_currency() 
  {
      if (empty($this->currency) || count($this->currency) < 1) {
          return false;
      }

      $this->db->query('TRUNCATE `currency`');

      foreach ($this->currency as $currency) 
      {
          $this->db->query('INSERT INTO `currency` (currency_iso, currency_value, currency_lang, currency_date) VALUES (?, ?, ?, ?)', array(
              $currency['name'],
              $currency['value'],
              $this->currency_lang($currency['name']),
              $this->date
          ));
      }
      return true;
  }

  /**
   * Returns currency lang code
   *
   * @access        private
   * @return        string
   */
  private function currency_lang($value) 
  {
      if ($value == 'USD') {
          return 'us';
      }
      if ($value == 'EUR') {
          return 'eu';
      }
      if ($value == 'CAD') {
          return 'ca';
      }
      if ($value == 'GBP') {
          return 'en';
      }
      return '';
  }
  
  /**
   * Checking current date
   *
   * @access        private
   * @return        bool
   */
  private function currency_is_outdated() 
  {
      $data = $this->db->query('SELECT currency_date FROM `currency` ORDER BY currency_id ASC LIMIT 1');
      
      if ($this->db->num_rows == 0) {
          return false;
      }
      
      if (strlen($data[0]['currency_date']) < 1) {
          return false;
      }

      return strtotime($data[0]['currency_date']) >= strtotime('today');
  }
  
  /**
   * Get currency from database
   *
   * @access        public
   * @return        array
   */
  public function load_today_currency() 
  {
      $data = $this->db->query("SELECT * FROM `currency` WHERE currency_lang != '' ORDER BY currency_id ASC");
      
      if ($this->db->num_rows == 0) {
          return false;
      }
      
      foreach ($data as $currency) {
          array_push($this->currencies, array(
              'iso' => $currency['currency_iso'],
              'value' => $currency['currency_value'],
              'lang' => $currency['currency_lang'],
              'date' => $currency['currency_date']
          ));
          if ($this->currency_date == '') {
              $this->currency_date = strftime('%e %B %Y', strtotime($currency['currency_date']));
          }
      }

      return $this->currencies;
  }
   
  /**
   * parseXMLDocument method
   *
   * @access        public
   * @return         void
   */
  function parseXMLDocument()
  {
       $xml = new SimpleXMLElement($this->xmlDocument);
       
       $this->date=$xml->Header->PublishingDate;
       
       foreach($xml->Body->Cube->Rate as $line)    
       {                      
           $this->currency[]=array("name"=>$line["currency"], "value"=>$line, "multiplier"=>$line["multiplier"]);
       }
  }
  
  /**
   * getCurs method
   * 
   * get current exchange rate: example getCurs("USD")
   * 
   * @access        public
   * @return         double
   */
  function getCurs($currency)
  {
      foreach($this->currency as $line)
      {
          if($line["name"]==$currency)
          {
              return $line["value"];
          }
      }
      
      return "Incorrect currency!";
  }
}

?>