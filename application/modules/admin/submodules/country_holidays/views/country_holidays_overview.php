<?php require_once ELEM_DIR . 'admin_header.php'; ?>

<div id="container">

  <div id="overview">
    
    <div class="orange_button">
      <div class="orange_button_left"></div>
      <div class="orange_button_con">
        <img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
        <div class="orange_button_space"></div>
        <a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER?>/add"><?=$this->lang->line('add_country');?></a>
      </div>
      <div class="orange_button_right"></div>
    </div>
    
    <div class="clear"></div>
    
    <div class="column">

      <div class="pie header">
        <div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-orange"></div>
        <h1><?=$this->lang->line('overview_header');?></h1>
      
      </div>
    
      <form method="post" action="<?=$this->url->current;?>">

        <table>
        
          <thead>
          
            <tr>
              
              <th style="width: 3%;">
                Nr
              </th>
              <th class="text_left" style="width: 38%;">
                <div class="spacer"></div>
                <p style="padding-left: 10px;">Holiday name</p>
              </th>
              <th style="width: 15%;">
                <div class="spacer"></div>
                <p>Country</p>
              </th>
              <th style="width: 12%;">
                <div class="spacer"></div>
                <p>Date (DD/MM)</p>
              </th>
              <th style="width: 15%;">
                <div class="spacer"></div>
                <p>Delete</p>
              </th>
            
            </tr>
          
          </thead>
          
          <tbody>
          
          <?php if(isset($data)): ?>
            
            <?php $x = 1; ?>
            
            <?php foreach($data as $holiday): ?>
              
            <tr>
              
              <td>
                <?=$x?>
                <?php $x++; ?>
              </td>
              
              <td class="text_left">
                
                <?php if(permission(CONTROLLER, 'edit')):?>
                  <a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $holiday['holiday_id'];?>" style="display:block;">
                <?php endif; ?>
                
                <div class="spacer"></div>
              
                <span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($holiday['holiday_name'], 100);?></span>

                <?php if(permission(CONTROLLER, 'edit')):?>
                  </a>
                <?php endif; ?>

              </td>

              <td>
              
                <div class="spacer"></div>
                
                <p><?=str_shorten($holiday['holiday_country'], 100);?></p>

              </td>
  
              <td>
              
                <div class="spacer"></div>
                <p><?=$holiday['holiday_day'] . ' / ' . $holiday['holiday_month'];?></p>

              </td>
              
              <td>
                <div class="spacer"></div>
                
                <p>
                
                  <?=(permission('country_holidays', 'delete') ? '<a title="' . $this->lang->line('delete') . '" class="delete" href="' . SITE_URL . 'admin/country_holidays/delete/' . $holiday['holiday_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?>
                  
                </p>
              </td>
            
            </tr>
            
            <?php endforeach; ?>
          
          <?php endif; ?>
          
          </tbody>
        
        </table>
        
      </form>
    </div>
  </div>
</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>