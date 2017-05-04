<?php

class contact_model extends model
{
  function send_form($post)
  {
    if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$/i", $post['email']))
      return false;

    $dataToSend = array(
        'type' => 'Enquiry',
        'date_added' => time(),
        'firstname' => $post['name'],
        'email' => $post['email'],
        'telephone' => $post['phone'],
        'subject' => 'New enquiry / ' . $post['name'],
        'content' => $post['message'],
        'address' => $post['address'],
        'language_id' => CUR_LANG
    );

    $keys = array();
    $value = array();
    foreach ($dataToSend as $key => $val) {
        array_push($keys, '`form_content`.' . $key);
        array_push($value, $val);
    }

    $values = str_repeat('?, ', count($keys));
    $values = rtrim($values, ', ');

    $this->db->query('INSERT INTO `form_content` (' . implode(', ', $keys) . ') VALUES (' . $values . ')', $value);

    if (isset($this->db->error) && $this->db->error !== false) {
        return false;
    }

    $settings = $this->db->query('SELECT * FROM `settings` LIMIT 1');
    if (isset($this->error) && $this->error !== false || !isset($settings[0])) {
        return false;
    }
    
    $message .= '<p><b>Name:</b><br>' . $post['name'] . '</p>';
    if (strlen($post['phone']) > 0) {
        $message .= '<p><b>Telephone:</b><br>' . $post['phone'] . '</p>';
    }
    if (strlen($post['address']) > 0) {
        $message .= '<p><b>Address:</b><br>' . $post['address'] . '</p>';
    }
    $message .= '<p><b>Enquiry:</b><br>' . $post['message'] . '</p>';

    $message .= '<p>----------<br>';
    $message .= '<b>' . $settings[0]['company'] . '</b><br>';
    $message .= $settings[0]['street'] . ' ' . $settings[0]['housenumber'] . '<br>';
    $message .= $settings[0]['postal'] . ' ' . $settings[0]['city'] . '<br>';
    $message .= $settings[0]['country'] . '<br>';
    
    if (strlen($settings[0]['telephone']) > 0) {
        $message .= '<br><b>Telephone:</b> ' . $settings[0]['telephone'];
    }
    if (strlen($settings[0]['fax']) > 0) {
        $message .= '<br><b>Fax:</b> ' . $settings[0]['fax'];
    }
    if (strlen($settings[0]['email']) > 0) {
        $message .= '<br><b>E-mail:</b> ' . $settings[0]['email'];
    }
    $message .= '</p>';

    if (!sendMail(array(
        'to_name' => $settings[0]['company'],
        'from_name' => $post['name'],
        'to_email' => $settings[0]['admin_mail'],
        'reply_to' => $post['email'],
        'from_email' => $settings[0]['admin_mail'],
        'subject' => 'New enquiry from ' . $_SERVER['SERVER_NAME'],
        'message' => $message
    ))) {
        log_msg('Unsent mail to ' . $settings[0]['email']);
        return false;
    }
    return true;
  }
}

?>