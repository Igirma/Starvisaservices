<?php

/**
 * Shopping Cart Class
 *
 * @description   Shopping Cart
 * @author        OrangeTalent
 * @link          orangetalent.nl
 */

class shoppingcart
{
    
    var $db;
    var $config;
    var $contents = array();
    var $userData = array();
    var $sess_time_to_update = '';
    var $sess_expiration = '';
    
    public function __construct()
    {
        $this->config =& load_class('config', 'core');
        $this->db =& load_class('db', 'core');
        
        if ($this->sess_time_to_update == '') {
            $this->sess_time_to_update = 60 * 5; // 5 mins
        }
        if ($this->sess_expiration == '') {
            $this->sess_expiration = 60 * 60 * 2; // 2 hours
        }
        
        if (!$this->read_session()) {
            $this->create_session();
        }
        $this->update_session();
    }
    
    /**
     * Save cart data to class array and database
     *
     * @access  private
     * @return  bool
     */
    private function _save_cart()
    {
        // unset these so our total can be calculated correctly below
        unset($this->contents['cart_total']);
        unset($this->contents['cart_amount']);
        
        $total = 0;
        $items = 0;
        foreach ($this->contents as $key => $val) {
            if (!is_array($val) || !isset($val['price']) || !isset($val['qty']))
                continue;
            
            $total += ($val['price'] * $val['qty']);
            $items += $val['qty'];
            
            $this->contents[$key]['subtotal'] = ($this->contents[$key]['price'] * $this->contents[$key]['qty']);
        }
        
        // set the cart total and total items
        $this->contents['cart_amount'] = $items;
        $this->contents['cart_total'] = $total;
        
        return $this->_update_cart_content();
    }
    
    /**
     * Updates cart content
     *
     * @access  private
     * @return  bool
     */
    private function _update_cart_content()
    {
        $contents = count($this->contents) === 0 ? '' : $this->_serialize($this->contents);
        
        return $this->db->query('UPDATE `cart` SET `cart`.cart_content = ? WHERE `cart`.session_id = ?', array(
            $contents,
            $this->userData['session_id']
        ));
    }
    
    /**
     * Cart contents
     *
     * Returns the entire cart array
     *
     * @access  public
     * @return  array || bool
     */
    function content()
    {
        $cart = $this->contents;
        
        // remove these so they don't create a problem when showing the cart table
        unset($cart['cart_total']);
        unset($cart['cart_amount']);
        
        if (is_array($cart) && count($cart) > 0)
            return $cart;
        
        return false;
    }
    
    /**
     * Cart total
     *
     * @access  public
     * @return  integer
     */
    function total()
    {
        return $this->contents['cart_total'];
    }
    
    /**
     * Total items
     *
     * Returns the total item count
     *
     * @access  public
     * @return  integer
     */
    function total_items()
    {
        return $this->contents['cart_amount'];
    }
    
    /**
     * Has options
     *
     * Checks if an item has extra options
     *
     * @access  public
     * @return  array
     */
    function has_options($rowid = '')
    {
        if (!isset($this->contents[$rowid]['options']) || count($this->contents[$rowid]['options']) === 0)
            return false;
        
        return true;
    }
    
    /**
     * Product options
     *
     * Returns the an array of options, for a particular product row ID
     *
     * @access  public
     * @return  array
     */
    function product_options($rowid = '')
    {
        if (!isset($this->contents[$rowid]['options']))
            return array();
        
        return $this->contents[$rowid]['options'];
    }
    
    /**
     * Reads user session data
     *
     * @access  public
     * @return  bool
     */
    function read_session()
    {
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = $this->user_agent();
        
        // is it a current session?
        if (isset($this->userData['last_activity']) && ($this->userData['last_activity'] + $this->sess_expiration) < time()) {
            $this->destroy_cart();
            return false;
        }
        
        // does the IP match?
        if (isset($this->userData['ip_address']) && $this->userData['ip_address'] != $ip_address) {
            $this->destroy_cart();
            return false;
        }
        
        // does the user agent match?
        if (isset($this->userData['user_agent']) && $this->userData['user_agent'] != $user_agent) {
            $this->destroy_cart();
            return false;
        }
        
        $data = $this->db->query('SELECT * FROM `cart` WHERE `cart`.ip_address = ? AND `cart`.user_agent = ?', array(
            $ip_address,
            $user_agent
        ));
        
        if (!isset($data[0])) {
            $this->destroy_cart();
            return false;
        }
        
        if (isset($data[0]['cart_content']) && strlen($data[0]['cart_content']) > 0) {
            $cart_content = $this->_unserialize($data[0]['cart_content']);
            $this->contents = $cart_content;
        } else {
            $this->contents = array(
                'cart_total' => 0,
                'cart_amount' => 0
            );
        }
        
        unset($data[0]['cart_content']);
        $this->userData = $data[0];
        
        return true;
    }
    
    /**
     * Create a session
     *
     * @access  public
     * @return  bool
     */
    function create_session()
    {
        $this->userData = array(
            'session_id' => $this->sessionID(),
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $this->user_agent(),
            'last_activity' => time()
        );
        
        $sql = '
        INSERT INTO `cart`
        (
          session_id,
          ip_address,
          user_agent,
          last_activity
        )
        VALUES
        (
          :session_id,
          :ip_address,
          :user_agent,
          :last_activity
        )
        ';
        return $this->db->query($sql, $this->userData);
    }
    
    /**
     * Update session
     *
     * @access  public
     * @return  bool
     */
    function update_session()
    {
        // update user data every five minutes by default
        if (($this->userData['last_activity'] + $this->sess_time_to_update) >= time())
            return;
        
        // save the old session id so we know which record to update in the database if we need it
        $old_sessid = $this->userData['session_id'];
        
        // update user data
        $this->userData['session_id'] = $this->sessionID();
        $this->userData['last_activity'] = time();
        
        // update user session id and last_activity field in database
        return $this->db->query('UPDATE `cart` SET `cart`.session_id = ?, `cart`.last_activity = ? WHERE `cart`.session_id = ?', array(
            $this->userData['session_id'],
            $this->userData['last_activity'],
            $old_sessid
        ));
    }
    
    /**
     * Delete cart data
     *
     * @access  public
     * @return  bool
     */
    function destroy_cart()
    {
        // delete cart data by session id
        if (isset($this->userData['session_id'])) {
            $this->db->query('DELETE FROM `cart` WHERE `cart`.session_id = ?', array(
                $this->userData['session_id']
            ));
        }
        
        // delete cart data by ip and user agent
        if (isset($this->userData['ip_address'], $this->userData['user_agent'])) {
            $this->db->query('DELETE FROM `cart` WHERE `cart`.ip_address = ? AND `cart`.user_agent = ?', array(
                $this->userData['ip_address'],
                $this->userData['user_agent']
            ));
        }
        
        // reset user data and cart content
        $this->userData = array();
        $this->contents = array(
            'cart_total' => 0,
            'cart_amount' => 0
        );
        
        return true;
    }
    
    /**
     * Insert items into the cart and save them to the cart table
     *
     * @access  public
     * @return  string
     */
    function insert($items = array())
    {
        if (!is_array($items) || count($items) == 0)
            return false;
        
        $save_cart = false;
        
        if (isset($items['id'])) {
            if (($rowid = $this->_insert($items))) {
                $save_cart = true;
            }
        } else {
            foreach ($items as $val) {
                if (is_array($val) && isset($val['id'])) {
                    if ($this->_insert($val)) {
                        $save_cart = true;
                    }
                }
            }
        }
        
        // Save the cart data if the insert was successful
        if ($save_cart) {
            $this->_save_cart();
            return isset($rowid) ? $rowid : true;
        }
        
        return false;
    }
    
    /**
     * Insert
     *
     * @access  private
     * @param   array
     * @return  bool
     */
    private function _insert($items = array())
    {
        if (!is_array($items) || count($items) == 0)
            return false;
        
        // required data to continue
        if (!isset($items['id']) || !isset($items['qty']) or !isset($items['price']) || !isset($items['name']))
            return false;
        
        $items['qty'] = trim(preg_replace('/([^0-9])/i', '', $items['qty']));
        $items['qty'] = trim(preg_replace('/(^[0]+)/i', '', $items['qty']));
        
        if (!is_numeric($items['qty']) || $items['qty'] == 0)
            return false;
        
        // remove anything that isn't a number or decimal point from price
        $items['price'] = trim(preg_replace('/([^0-9\.])/i', '', $items['price']));
        // trim any leading zeros
        $items['price'] = trim(preg_replace('/(^[0]+)/i', '', $items['price']));
        
        // check if it is numeric
        if (!is_numeric($items['price']))
            return false;
        
        /*
        We need to create a unique identifier for the item being inserted into the cart.
        Every time something is added to the cart it is stored in the cart array.
        Each row in the cart array, however, must have a unique index that identifies not only
        a particular product, but makes it possible to store identical products with different options.
        Internally, we need to treat identical submissions, but with different options, as a unique product.
        We convert the options array to a string and MD5 it along with the product ID.
        This becomes the unique "row ID"
        */
        
        if (isset($items['options']) && count($items['options']) > 0) {
            $rowid = md5($items['id'] . implode('', $items['options']));
        } else {
            $rowid = md5($items['id']);
        }
        
        // unset this first, just to make sure index contains only the data from this submission
        unset($this->contents[$rowid]);
        
        // create a new index with the new row ID
        $this->contents[$rowid]['rowid'] = $rowid;
        
        // and add the new items to the cart array
        foreach ($items as $key => $val) {
            $this->contents[$rowid][$key] = $val;
        }
        
        return $rowid;
    }
    
    /**
     * Update the cart
     *
     * @access  public
     * @param   array
     * @return  bool
     */
    function update($items = array())
    {
        // was any cart data passed?
        if (!is_array($items) || count($items) == 0)
            return false;
        
        $save_cart = false;
        if (isset($items['rowid']) && isset($items['qty'])) {
            if ($this->_update($items)) {
                $save_cart = true;
            }
        } else {
            foreach ($items as $val) {
                if (is_array($val) && isset($val['rowid']) && isset($val['qty'])) {
                    if ($this->_update($val)) {
                        $save_cart = true;
                    }
                }
            }
        }
        
        // save the cart data if the update was successful
        if ($save_cart) {
            $this->_save_cart();
            return true;
        }
        
        return false;
    }
    
    /**
     * Update
     *
     * @access  private
     * @param   array
     * @return  bool
     */
    function _update($items = array())
    {
        // without these array indexes there is nothing to do
        if (!isset($items['qty']) || !isset($items['rowid']) || !isset($this->contents[$items['rowid']]))
            return false;
        
        // prepare quantity
        $items['qty'] = preg_replace('/([^0-9])/i', '', $items['qty']);
        
        // is the quantity a number?
        if (!is_numeric($items['qty']))
            return false;
        
        // is the new quantity different than what is already saved in the cart?
        if ($this->contents[$items['rowid']]['qty'] == $items['qty'])
            return false;
        
        // if quantity is zero, remove the item from the cart, otherwise update it
        if ($items['qty'] == 0) {
            unset($this->contents[$items['rowid']]);
        } else {
            $this->contents[$items['rowid']]['qty'] += $items['qty'];
        }
        
        return true;
    }
    
    /**
     * Format number
     *
     * Returns the number with commas and a decimal point
     *
     * @access  public
     * @return  integer
     */
    function format_number($n = '')
    {
        if ($n == '')
            return '';
        
        // remove anything that isn't a number or decimal point
        $n = trim(preg_replace('/([^0-9\.])/i', '', $n));
        
        return number_format($n, 2, '.', ',');
    }
    
    /**
     * Strip slashes
     *
     * Removes slashes contained in a string or in an array
     *
     * @access  private
     * @param   mixed string or array
     * @return  mixed string or array
     */
    private function strip_slashes($str)
    {
        if (is_array($str)) {
            foreach ($str as $key => $val) {
                $str[$key] = $this->strip_slashes($val);
            }
        } else {
            $str = stripslashes($str);
        }
        
        return $str;
    }
    
    /**
     * Serialize an array
     *
     * This function first converts any slashes found in the array to a temporary
     * marker, so when it gets unserialized the slashes will be preserved
     *
     * @access  private
     * @param   array
     * @return  string
     */
    private function _serialize($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                if (is_string($val)) {
                    $data[$key] = str_replace('\\', '{{slash}}', $val);
                }
            }
        } else {
            if (is_string($data)) {
                $data = str_replace('\\', '{{slash}}', $data);
            }
        }
        return @serialize($data);
    }
    
    /**
     * Unserialize
     *
     * This function unserializes a data string, then converts any
     * temporary slash markers back to actual slashes
     *
     * @access  private
     * @param   array
     * @return  string
     */
    private function _unserialize($data)
    {
        $data = @unserialize($this->strip_slashes($data));
        
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                if (is_string($val)) {
                    $data[$key] = str_replace('{{slash}}', '\\', $val);
                }
            }
            return $data;
        }
        
        return (is_string($data)) ? str_replace('{{slash}}', '\\', $data) : $data;
    }
    
    /**
     * Session ID combined with the user's IP
     *
     * @access  private
     * @return  string
     */
    private function sessionID()
    {
        $sessid = '';
        
        while (strlen($sessid) < 32) {
            $sessid .= mt_rand(0, mt_getrandmax());
        }
        $sessid .= $_SERVER['REMOTE_ADDR'];
        
        return md5(uniqid($sessid, true));
    }
    
    /**
     * User agent
     *
     * @access  private
     * @return  string || false
     */
    private function user_agent()
    {
        if (!isset($_SERVER['HTTP_USER_AGENT']))
            return false;
        
        return $_SERVER['HTTP_USER_AGENT'];
    }
}

?>