<?php require_once(ELEM_DIR . 'admin_header.php');

if(!empty($_POST))
{
  $data['setting'] = $_POST;
}

?>

<?=validation_errors();?>

<div id="container">

  <div id="details">
  
    <form method="post" action="<?=$this->url->current?>">
    
      <div class="menu_float">

        <input class="save" type="submit" name="save" value="<?=$this->lang->line('save_settings')?>">

        <input class="save_and_back" type="submit" name="save_and_back" value="<?=$this->lang->line('menu_save_and_back')?>">
        
        <div class="back_button">
          <a href="<?=SITE_URL . LANG_CODE . '/admin/'?>" title="<?=$this->lang->line('back')?>"><?=$this->lang->line('back_to_overview')?></a>
        </div>

      </div>
      
      <div class="column">

        <div class="header">
        
          <div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-settings"></div>
          <h1><?=$this->lang->line('edit_settings')?></h1>
        
        </div>

      </div>
      
      <div class="column">
        
        <div class="subheader">
          <h2><?=$this->lang->line('settings')?></h2>
        </div>
        
        <div class="subcolumn">

          <table>
            
            <tr>
              <th><?=$this->lang->line('admin_mail')?></th>
              <td><input type="text" name="admin_mail" value="<?=(isset($data['setting']['admin_mail']) ? $data['setting']['admin_mail'] : '');?>"></td>
            </tr>
            <tr>
              <th><?=$this->lang->line('company')?></th>
              <td><input type="text" name="company" value="<?=(isset($data['setting']['company']) ? $data['setting']['company'] : '');?>"></td>
            </tr>
            <tr>
              <th><?=$this->lang->line('email')?></th>
              <td><input type="text" name="email" value="<?=(isset($data['setting']['email']) ? $data['setting']['email'] : '');?>"></td>
            </tr>

            <tr>
              <th><?=$this->lang->line('firstname')?></th>
              <td><input type="text" name="firstname" value="<?=(isset($data['setting']['firstname']) ? $data['setting']['firstname'] : '');?>"></td>
            </tr>
            <tr class="hidden">
              <th><?=$this->lang->line('infix')?></th>
              <td><input type="text" name="infix" value="<?=(isset($data['setting']['infix']) ? $data['setting']['infix'] : '');?>"></td>
            </tr>
            <tr>
              <th><?=$this->lang->line('lastname')?></th>
              <td><input type="text" name="lastname" value="<?=(isset($data['setting']['lastname']) ? $data['setting']['lastname'] : '');?>"></td>
            </tr>
            <tr>
              <th><?=$this->lang->line('street')?></th>
              <td><input type="text" name="street" value="<?=(isset($data['setting']['street']) ? $data['setting']['street'] : '');?>"></td>
            </tr>
            <tr>
              <th><?=$this->lang->line('housenumber')?></th>
              <td><input type="text" name="housenumber" value="<?=(isset($data['setting']['housenumber']) ? $data['setting']['housenumber'] : '');?>"></td>
            </tr>
            <tr>
              <th><?=$this->lang->line('postal')?></th>
              <td><input type="text" name="postal" value="<?=(isset($data['setting']['postal']) ? $data['setting']['postal'] : '');?>"></td>
            </tr>
            <tr>
              <th><?=$this->lang->line('city')?></th>
              <td><input type="text" name="city" value="<?=(isset($data['setting']['city']) ? $data['setting']['city'] : '');?>"></td>
            </tr>
            <tr>
              <th><?=$this->lang->line('country')?></th>
              <td><input type="text" name="country" value="<?=(isset($data['setting']['country']) ? $data['setting']['country'] : '');?>"></td>
            </tr>
            <tr>
              <th><?=$this->lang->line('telephone')?></th>
              <td><input type="text" name="telephone" value="<?=(isset($data['setting']['telephone']) ? $data['setting']['telephone'] : '');?>"></td>
            </tr>
            <tr>
              <th>Phone 2</th>
              <td><input type="text" name="telephone_secondary" value="<?=(isset($data['setting']['telephone_secondary']) ? $data['setting']['telephone_secondary'] : '');?>"></td>
            </tr>
            <tr>
              <th><?=$this->lang->line('fax')?></th>
              <td><input type="text" name="fax" value="<?=(isset($data['setting']['fax']) ? $data['setting']['fax'] : '');?>"></td>
            </tr>
            
            <tr>
              <th>Copyright<br><small>(max. 250 caractere)</small></th>
              <td><input type="text" name="kvk" value="<?=(isset($data['setting']['kvk']) ? $data['setting']['kvk'] : '');?>"></td>
            </tr>
            
            <tr class="hidden">
              <th>Opening hours</th>
              <td>

                <div style="width: 100%; clear: both; margin-bottom: 10px; margin-top: 10px;">
                  <div style="width: 10%; display: inline-block; font-weight: bold;">Monday</div>
                  <div style="width: 30px; display: inline-block; text-align: center;">from</div>
                  <input type="text" name="mon_start" class="timepicker" readonly="readonly" value="<?=(isset($data['setting']['mon_start']) ? $data['setting']['mon_start'] : '');?>" style="width: 30%; display: inline-block;">
                  <div style="width: 30px; display: inline-block; text-align: center;">to</div>
                  <input type="text" name="mon_end" class="timepicker" readonly="readonly" value="<?=(isset($data['setting']['mon_end']) ? $data['setting']['mon_end'] : '');?>" style="width: 30%; display: inline-block;">
                </div>
                
                <div style="width: 100%; clear: both; margin-bottom: 10px;">
                  <div style="width: 10%; display: inline-block; font-weight: bold;">Tuesday</div>
                  <div style="width: 30px; display: inline-block; text-align: center;">from</div>
                  <input type="text" name="tue_start" class="timepicker" readonly="readonly" value="<?=(isset($data['setting']['tue_start']) ? $data['setting']['tue_start'] : '');?>" style="width: 30%; display: inline-block;">
                  <div style="width: 30px; display: inline-block; text-align: center;">to</div>
                  <input type="text" name="tue_end" class="timepicker" readonly="readonly" value="<?=(isset($data['setting']['tue_end']) ? $data['setting']['tue_end'] : '');?>" style="width: 30%; display: inline-block;">
                </div>
                
                <div style="width: 100%; clear: both; margin-bottom: 10px;">
                  <div style="width: 10%; display: inline-block; font-weight: bold;">Wednesday</div>
                  <div style="width: 30px; display: inline-block; text-align: center;">from</div>
                  <input type="text" name="wed_start" class="timepicker" readonly="readonly" value="<?=(isset($data['setting']['wed_start']) ? $data['setting']['wed_start'] : '');?>" style="width: 30%; display: inline-block;">
                  <div style="width: 30px; display: inline-block; text-align: center;">to</div>
                  <input type="text" name="wed_end" class="timepicker" readonly="readonly" value="<?=(isset($data['setting']['wed_end']) ? $data['setting']['wed_end'] : '');?>" style="width: 30%; display: inline-block;">
                </div>
                
                <div style="width: 100%; clear: both; margin-bottom: 10px;">
                  <div style="width: 10%; display: inline-block; font-weight: bold;">Thursday</div>
                  <div style="width: 30px; display: inline-block; text-align: center;">from</div>
                  <input type="text" name="thu_start" class="timepicker" readonly="readonly" value="<?=(isset($data['setting']['thu_start']) ? $data['setting']['thu_start'] : '');?>" style="width: 30%; display: inline-block;">
                  <div style="width: 30px; display: inline-block; text-align: center;">to</div>
                  <input type="text" name="thu_end" class="timepicker" readonly="readonly" value="<?=(isset($data['setting']['thu_end']) ? $data['setting']['thu_end'] : '');?>" style="width: 30%; display: inline-block;">
                </div>
                
                <div style="width: 100%; clear: both; margin-bottom: 10px;">
                  <div style="width: 10%; display: inline-block; font-weight: bold;">Friday</div>
                  <div style="width: 30px; display: inline-block; text-align: center;">from</div>
                  <input type="text" name="fri_start" class="timepicker" readonly="readonly" value="<?=(isset($data['setting']['fri_start']) ? $data['setting']['fri_start'] : '');?>" style="width: 30%; display: inline-block;">
                  <div style="width: 30px; display: inline-block; text-align: center;">to</div>
                  <input type="text" name="fri_end" class="timepicker" readonly="readonly" value="<?=(isset($data['setting']['fri_end']) ? $data['setting']['fri_end'] : '');?>" style="width: 30%; display: inline-block;">
                </div>
                
                <div style="width: 100%; clear: both; margin-bottom: 10px;">
                  <div style="width: 10%; display: inline-block; font-weight: bold;">Saturday</div>
                  <div style="width: 30px; display: inline-block; text-align: center;">from</div>
                  <input type="text" name="sat_start" class="timepicker" readonly="readonly" value="<?=(isset($data['setting']['sat_start']) ? $data['setting']['sat_start'] : '');?>" style="width: 30%; display: inline-block;">
                  <div style="width: 30px; display: inline-block; text-align: center;">to</div>
                  <input type="text" name="sat_end" class="timepicker" readonly="readonly" value="<?=(isset($data['setting']['sat_end']) ? $data['setting']['sat_end'] : '');?>" style="width: 30%; display: inline-block;">
                </div>
                
                <div style="width: 100%; clear: both; margin-bottom: 10px;">
                  <div style="width: 10%; display: inline-block; font-weight: bold;">Monday</div>
                  <div style="width: 30px; display: inline-block; text-align: center;">from</div>
                  <input type="text" name="sun_start" class="timepicker" readonly="readonly" value="<?=(isset($data['setting']['sun_start']) ? $data['setting']['sun_start'] : '');?>" style="width: 30%; display: inline-block;">
                  <div style="width: 30px; display: inline-block; text-align: center;">to</div>
                  <input type="text" name="sun_end" class="timepicker" readonly="readonly" value="<?=(isset($data['setting']['sun_end']) ? $data['setting']['sun_end'] : '');?>" style="width: 30%; display: inline-block;">
                </div>
              
              </td>
            </tr>
            
            <tr class="hidden">
              <th>Contact information</th>
              <td style="padding-top: 10px; padding-bottom: 10px;"><textarea name="openingtime" class="editor"><?=(isset($data['setting']['openingtime']) ? $data['setting']['openingtime'] : '');?></textarea></td>
            </tr>
            
            
            <tr class="hidden">
              <th><a href="http://mailchimp.com/" target="_blank">MailChimp</a> API Key</th>
              <td><input type="text" name="mailchimp_api_key" value="<?=(isset($data['setting']['mailchimp_api_key']) ? $data['setting']['mailchimp_api_key'] : '');?>"></td>
            </tr>
            
            <tr class="hidden">
              <th><a href="http://mailchimp.com/" target="_blank">MailChimp</a> List's Unique ID</th>
              <td><input type="text" name="mailchimp_unique_id" value="<?=(isset($data['setting']['mailchimp_unique_id']) ? $data['setting']['mailchimp_unique_id'] : '');?>"></td>
            </tr>
            
            <tr>
              <th>Stripe Secret Key</th>
              <td><input type="text" name="stripe_secret_key" value="<?=(isset($data['setting']['stripe_secret_key']) ? $data['setting']['stripe_secret_key'] : '');?>"></td>
            </tr>
            <tr>
              <th>Stripe Secret Key</th>
              <td><input type="text" name="stripe_publishable_key" value="<?=(isset($data['setting']['stripe_publishable_key']) ? $data['setting']['stripe_publishable_key'] : '');?>"></td>
            </tr>
            
            <tr>
              <th>Facebook Page</th>
              <td><input type="text" name="social_facebook" value="<?=(isset($data['setting']['social_facebook']) ? $data['setting']['social_facebook'] : '');?>"></td>
            </tr>
            <tr>
              <th>Twitter Page</th>
              <td><input type="text" name="social_twitter" value="<?=(isset($data['setting']['social_twitter']) ? $data['setting']['social_twitter'] : '');?>"></td>
            </tr>
            <tr>
              <th>Linkedin Page</th>
              <td><input type="text" name="social_pinterest" value="<?=(isset($data['setting']['social_pinterest']) ? $data['setting']['social_pinterest'] : '');?>"></td>
            </tr>

            <tr>
              <th>GooglePlus Page</th>
              <td><input type="text" name="social_google" value="<?=(isset($data['setting']['social_google']) ? $data['setting']['social_google'] : '');?>"></td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('google_verification')?></th>
              <td><input type="text" name="google_verification" value="<?=(isset($data['setting']['google_verification']) ? $data['setting']['google_verification'] : '');?>"></td>
            </tr>
            <tr class="hidden">
              <th><?=$this->lang->line('google_analytics')?></th>
              <td><input type="text" name="google_analytics" value="<?=(isset($data['setting']['google_analytics']) ? $data['setting']['google_analytics'] : '');?>"></td>
            </tr>
            
            
            
            
            
            
            <tr class="hidden">
              <th><?=$this->lang->line('setOAuthToken')?></th>
              <td><input type="text" name="setOAuthToken" value="<?=(isset($data['setting']['setOAuthToken']) ? $data['setting']['setOAuthToken'] : '');?>"></td>
            </tr>
            <tr class="hidden">
              <th><?=$this->lang->line('setOAuthTokenSecret')?></th>
              <td><input type="text" name="setOAuthTokenSecret" value="<?=(isset($data['setting']['setOAuthTokenSecret']) ? $data['setting']['setOAuthTokenSecret'] : '');?>"></td>
            </tr>
            <tr class="hidden">
              <th><?=$this->lang->line('setConsumerKey')?></th>
              <td><input type="text" name="setConsumerKey" value="<?=(isset($data['setting']['setConsumerKey']) ? $data['setting']['setConsumerKey'] : '');?>"></td>
            </tr>
            <tr class="hidden">
              <th><?=$this->lang->line('setConsumerSecret')?></th>
              <td><input type="text" name="setConsumerSecret" value="<?=(isset($data['setting']['setConsumerSecret']) ? $data['setting']['setConsumerSecret'] : '');?>"></td>
            </tr>
            
            
            
            
            <tr class="hidden">
              <th><?=$this->lang->line('page_id')?></th>
              <td><input type="text" name="page_id" value="<?=(isset($data['setting']['page_id']) ? $data['setting']['page_id'] : '');?>"></td>
            </tr>
            <tr class="hidden">
              <th><?=$this->lang->line('appId')?></th>
              <td><input type="text" name="appId" value="<?=(isset($data['setting']['appId']) ? $data['setting']['appId'] : '');?>"></td>
            </tr>
            <tr class="hidden">
              <th><?=$this->lang->line('secret')?></th>
              <td><input type="text" name="secret" value="<?=(isset($data['setting']['secret']) ? $data['setting']['secret'] : '');?>"></td>
            </tr>
            <tr class="hidden">
              <th><?=$this->lang->line('url')?></th>
              <td><input type="text" style="width: 80%!important;" id="getTokenURL_field" name="url" value="<?=SITE_URL?>nl/admin/getToken"><div class="token_button"><a href="<?=SITE_URL?>nl/admin/getToken">GetToken</a></div></td>
            </tr>
            <tr class="hidden">
              <th><?=$this->lang->line('accestoken_db')?></th>
              <td><input type="text" name="accestoken_db" value="<?=(isset($data['setting']['accestoken_db']) ? $data['setting']['accestoken_db'] : '');?>"></td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('vat')?></th>
              <td><input type="text" name="vat" value="<?=(isset($data['setting']['vat']) ? $data['setting']['vat'] : '');?>"></td>
            </tr>
           
          </table>
          
        </div>
        
      </div>
    
    </form>
  
  </div>

</div>
<?php require_once(ELEM_DIR . 'admin_footer.php'); ?>