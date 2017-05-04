<?php require_once ELEM_DIR . 'admin_header.php'; ?>

<style>

input[type="text"], input[type="password"]
{
  width: 330px;
  height: 28px;
}

#login_container
{
  color: #363636;
}

#login_container a
{
  color: #ff6c00;
}

#login_container a:hover
{
  text-decoration: underline;
}

</style>

<div id="login_container" style="border-top: 5px solid #088475;">

  <div id="header" style="background-color: #1e1e1e; border-bottom: 1px solid #616161; text-align: center;">
      
    <img style="margin: 20px;" src="<?=SITE_URL . ELEM_DIR . 'img_admin/logo_login.png';?>">

  </div>
  
  <div id="login_form" style="height: 308px; background-color: #e7e7e7;">
  
    <div style="margin: 0 auto; width: 330px;">
    
      <form action="<?php echo SITE_URL . 'en/admin/login'; ?>" method="post">
      
        <table>
        
          <tr>
            <td style="padding: 10px 0;">Username</td>
          </tr>
          
          <tr>
            <td><input type="text" autofocus="autofocus" name="username" value="<?=(isset($_POST['username']) ? $_POST['username'] : '');?>"></td>
          </tr>
          
          <tr>
            <td style="padding: 10px 0;">Password</td>
          </tr>
          
          <tr>
            <td>
              <input type="password" name="password">
            </td>
          </tr>
          
          <tr class="hidden">
            <td style="padding: 10px 0;">Selecta&#355;i limba</td>
          </tr>
          
          <tr class="hidden">
            <td>
              <div style="position: relative;">
              
                <div style="float: left; padding-left: 15px; width: 18px; height: 28px; line-height: 30px; background-color: #FFFFFF; border: 1px solid #D1D1D1; border-right: none;">
                  <div class="language sprite_ro"></div>
                </div>
                <input name="language" readonly="readonly" class="lang_select" style="cursor: pointer; width: 297px; border-left: none;" type="text" value="Rom&#226;n&#259;">
                <div class="lang_container" style="position: absolute; top: 28px; left: 0; z-index: 999; display: none; width: 327px; padding: 10px 0 10px 11px; background-color: #FFFFFF; border: 1px solid #D1D1D1;">
                  <div id="en" class="lang" style="cursor: pointer;">
                    <div class="language sprite_en"></div><span style="margin-left: 10px;">English</span>
                  </div>
                  <div id="ro" class="lang" style="cursor: pointer;">
                    <div class="language sprite_ro"></div><span style="margin-left: 10px;">Romanian</span>
                  </div>
                </div>
              
              </div>
            </td>
          </tr>
          
          <tr>
            <td style="padding: 30px 0 0 0;">
              <a style="position: relative; top: 5px; display: none;" href="<?=SITE_URL;?>admin/forgot_password" title="Reset password">Reset password</a>
              <input style="float: right;" type="submit" name="login" value="Login">
            </td>
          </tr>
        
        </table>

      </form>
    
    </div>
  
  </div>

</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>