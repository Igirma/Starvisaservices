<?php require_once(ELEM_DIR . 'admin_header.php'); ?>

<div id="container">

  <div id="overview">
    
    <div class="orange_button">
      <div class="orange_button_left"></div>
      <div class="orange_button_con">
        <img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
        <div class="orange_button_space"></div>
        <a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER?>/add">Toevoegen</a>
      </div>
      <div class="orange_button_right"></div>
    </div>
    
    <div class="clear"></div>
    
    <div class="column">

      <div class="pie header">
        <div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-pages"></div>
        <h1><?=$this->lang->line('overview_header');?></h1>
      
      </div>
    
      <form method="post" action="<?=$this->url->current;?>">

        <table>
          <thead>
            <tr>
              <th width="5%">Nr</th>
              <th class="text_left">
                <div class="spacer"></div>
                <p style="padding-left: 10px;">Naam</p>
              </th>
              <th class="text_left" width="15%">
                <div class="spacer"></div>
                <p style="padding-left:10px;">Taal</p>
              </th>
              <th width="5%" style="padding-right:10px;">
                <div class="spacer"></div>
                <p style="padding-left:10px;">Verwijderen</p>
              </th>
            </tr>
          </thead>
          <tbody>
            <?php
            
            for ($i = 1; $i <= 10; $i++) {
            ?>
            <tr>
              <td><?php echo $i; ?>.
              </td>
              <td class="text_left" style="padding-right:20px;">
                <a href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/1/<?=$language['language_id'];?>" style="display:block;">
                <div class="spacer"></div>
                <span style="line-height: 40px; padding-left: 10px;">Tag</span>
                </a>
              </td>
              <td class="text_left" >
                <div class="spacer"></div>
                <p style="padding-left:10px;">
              <?php foreach($data['languages'] as $language): ?>

                <a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/1/<?=$language['language_id'];?>">
                  
                  <span class="language_grey sprite_<?=$language['code']?>"></span>
                
                </a>
                

              <?php endforeach; ?></p>
              </td>
              <td >
                <div class="spacer"></div>
                <p><a title="" class="delete" href="#"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/delete.png"></a></p>
              </td>
            </tr>
            <?php
            }
            ?>
          </tbody>
        </table>
        
      </form>
      
    </div>
    
  </div>
  
</div>

<?php require_once(ELEM_DIR . 'admin_footer.php'); ?>