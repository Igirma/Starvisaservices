<?php
class settings_model extends model
{
    function fetch_settings()
    {
        $data = $this->db->query('SELECT * FROM `settings`');

        if(!isset($data[0]))
            return false;
        return $data[0];
    }
    
    function edit($post)
    {
        $sql = '
        UPDATE `settings`
        SET
          `settings`.email = :email,
          `settings`.firstname = :firstname,
          `settings`.infix = :infix,
          `settings`.lastname = :lastname,
          `settings`.admin_mail = :admin_mail,
          `settings`.company = :company,
          `settings`.street = :street,
          `settings`.housenumber = :housenumber,
          `settings`.postal = :postal,
          `settings`.city = :city,
          `settings`.country = :country,
          `settings`.telephone = :telephone,
          `settings`.telephone_secondary = :telephone_secondary,
          `settings`.fax = :fax,
          `settings`.kvk = :kvk,
          `settings`.google_verification = :google_verification,
          `settings`.google_analytics = :google_analytics,
          `settings`.setOAuthToken = :setOAuthToken,
          `settings`.setOAuthTokenSecret = :setOAuthTokenSecret,
          `settings`.setConsumerKey = :setConsumerKey,
          `settings`.setConsumerSecret = :setConsumerSecret,
          `settings`.page_id = :page_id,
          `settings`.appId = :appId,
          `settings`.secret = :secret,
          `settings`.url = :url,
          `settings`.vat = :vat,
          `settings`.accestoken_db = :accestoken_db,
          
          `settings`.social_facebook = :social_facebook,
          `settings`.social_twitter = :social_twitter,
          `settings`.social_pinterest = :social_pinterest,
          `settings`.social_google = :social_google,
          
          `settings`.mon_start = :mon_start,
          `settings`.mon_end = :mon_end,
          `settings`.tue_start = :tue_start,
          `settings`.tue_end = :tue_end,
          `settings`.wed_start = :wed_start,
          `settings`.wed_end = :wed_end,
          `settings`.thu_start = :thu_start,
          `settings`.thu_end = :thu_end,
          `settings`.fri_start = :fri_start,
          `settings`.fri_end = :fri_end,
          `settings`.sat_start = :sat_start,
          `settings`.sat_end = :sat_end,
          `settings`.sun_start = :sun_start,
          `settings`.sun_end = :sun_end,
          `settings`.openingtime = :openingtime,
          `settings`.mailchimp_api_key = :mailchimp_api_key,
          `settings`.mailchimp_unique_id = :mailchimp_unique_id,
          `settings`.stripe_secret_key = :stripe_secret_key,
          `settings`.stripe_publishable_key = :stripe_publishable_key
        ';
        
        return $this->db->query($sql, array(
            'email' => $post['email'],
            'firstname' => $post['firstname'],
            'infix' => $post['infix'],
            'lastname' => $post['lastname'],
            'admin_mail' => $post['admin_mail'],
            'company' => $post['company'],
            'street' => $post['street'],
            'housenumber' => $post['housenumber'],
            'postal' => $post['postal'],
            'city' => $post['city'],
            'country' => $post['country'],
            'telephone' => $post['telephone'],
            'telephone_secondary' => $post['telephone_secondary'],
            'fax' => $post['fax'],
            'kvk' => $post['kvk'],
            'google_verification' => $post['google_verification'],
            'google_analytics' => $post['google_analytics'],
            'setOAuthToken' => $post['setOAuthToken'],
            'setOAuthTokenSecret' => $post['setOAuthTokenSecret'],
            'setConsumerKey' => $post['setConsumerKey'],
            'setConsumerSecret' => $post['setConsumerSecret'],
            'page_id' => $post['page_id'],
            'appId' => $post['appId'],
            'secret' => $post['secret'],
            'url' => $post['url'],
            'vat' => $post['vat'],
            'accestoken_db' => $post['accestoken_db'],

            'social_facebook' => $post['social_facebook'],
            'social_twitter' => $post['social_twitter'],
            'social_pinterest' => $post['social_pinterest'],
            'social_google' => $post['social_google'],

            'mon_start' => $post['mon_start'],
            'mon_end' => $post['mon_end'],
            'tue_start' => $post['tue_start'],
            'tue_end' => $post['tue_end'],
            'wed_start' => $post['wed_start'],
            'wed_end' => $post['wed_end'],
            'thu_start' => $post['thu_start'],
            'thu_end' => $post['thu_end'],
            'fri_start' => $post['fri_start'],
            'fri_end' => $post['fri_end'],
            'sat_start' => $post['sat_start'],
            'sat_end' => $post['sat_end'],
            'sun_start' => $post['sun_start'],
            'sun_end' => $post['sun_end'],
            'openingtime' => $post['openingtime'],
            'mailchimp_api_key' => $post['mailchimp_api_key'],
            'mailchimp_unique_id' => $post['mailchimp_unique_id'],
            'stripe_secret_key' => $post['stripe_secret_key'],
            'stripe_publishable_key' => $post['stripe_publishable_key']
        ));
    }
}
?>