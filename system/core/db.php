<?php 
class db
{
  var $dbh;
  var $error = false;
  var $num_rows;
  var $last_insert_id;

  function __construct()
  {
    $this->config =& load_class('config', 'core');

    //Check if session site is defined
    if(!isset($_SESSION['site']))
    {
      $_SESSION['site'] = 'default_site';
    }

    $this->dbh = new PDO('mysql:host=localhost;dbname=' . $this->config->item('database', $_SESSION['site']), $this->config->item('user', $_SESSION['site']), $this->config->item('pass', $_SESSION['site']));
    $this->dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }

  /**
   * Execute a query, with or without a prepared statement
   * @param string 	$q		The query to execute
   * @param array 	$data	The prepared statement data
   */
  function query($q, $data = array())
  {
    try 
    {
      $this->num_rows = false;
      $this->error = false;
      $this->last_insert_id = false;
      
      $sth = $this->dbh->prepare($q);
      $sth->execute($data);
      
      $this->num_rows = $sth->rowCount();
      $this->last_insert_id = $this->dbh->lastInsertId();
      
      if(strstr($q, 'SELECT') !== false)
      {
        return $sth->fetchAll(PDO::FETCH_ASSOC);
      }
    }
    catch(PDOException $e)
    {
      $this->error = $e->getMessage();
    }
  }
}

?>