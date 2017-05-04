<?php require_once ELEM_DIR . 'admin_header.php'; ?>

<div id="container">

  <div id="overview">
    
    <div class="orange_button">
      <div class="orange_button_left"></div>
      <div class="orange_button_con">
        <img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
        <div class="orange_button_space"></div>
        <a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER?>/add"><?=$this->lang->line('add_reference');?></a>
      </div>
      <div class="orange_button_right"></div>
    </div>
    
    <div class="clear"></div>
    
    <div class="column">

      <div class="pie header">
        <div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-references"></div>
        <h1><?=$this->lang->line('overview_header');?></h1>
      
      </div>
    
      <form method="post" action="<?=$this->url->current;?>">

        <table>
        
          <thead>
          
            <tr>
              
              <th style="width: 3%;">
                Nr
              </th>
              <th class="text_left" style="width: 30%;">
                <div class="spacer"></div>
                <p style="padding-left: 10px;"><?=$this->lang->line('reference_title');?></p>
              </th>
              <th style="width: 15%;" class="hidden">
                <div class="spacer"></div>
                <p><?=$this->lang->line('edit_language');?></p>
              </th>
              <th class="hidden" style="width: 10%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('reference_date');?></p>
              </th>
              <th style="width: 30%;">
                <div class="spacer"></div>
                <p>Blog</p>
              </th>
              <th style="width: 10%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('order');?></p>
              </th>
              <th class="hidden" style="width: 12%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('highlight');?></p>
              </th>
              <th style="width: 12%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('active');?></p>
              </th>
              <th style="width: 15%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('delete');?></p>
              </th>
            
            </tr>
          
          </thead>
          
          <tbody>
          
          <?php if(isset($data['references'])): ?>
            
            <?php $x = 0; ?>
            
            <?php $count_pages = count($data['references']); ?>
            
            <?php foreach($data['references'] as $reference): ?>
            
            
            
            <tr>
              
              <td>
                <?php $x++; ?>
                <?=$x?>
              </td>
              
              <td class="text_left">
                
                <?php if(permission(CONTROLLER, 'edit')):?>
                  <a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $reference['reference_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
                <?php endif; ?>
                
                <div class="spacer"></div>
              
                <span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($reference['title'], 27);?></span>

                <?php if(permission(CONTROLLER, 'edit')):?>
                  </a>
                <?php endif; ?>

              </td>

              <td class="hidden">
              
                <div class="spacer"></div>
                
                <p>
                        
                <?php foreach($data['languages'] as $language): ?>

                  <?php $sub_active = $this->db->query('SELECT `reference_content`.sub_active FROM `reference_content` WHERE `reference_content`.language_id = ? AND `reference_content`.reference_id = ?', array($language['language_id'], $reference['reference_id'])); ?>
                  
                  <?php if($sub_active[0]['sub_active'] == 1): ?>
                    
                    <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                    
                    <a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/references/edit/<?=$reference['reference_id']?>/<?=$language['language_id'];?>">
                      <span class="language sprite_<?=$language['code']?>"></span>
                    </a>
                    
                    <?php endif; ?>
                  
                  <?php else: ?>
                  
                  <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                    
                    <a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER;?>/edit/<?=$reference['reference_id']?>/<?=$language['language_id'];?>">
                      <span class="language_grey sprite_<?=$language['code']?>"></span>
                    </a>
                    
                    <?php endif; ?>
                  
                  <?php endif; ?>

                <?php endforeach; ?>
                
                </p>
              
              </td>
              
              <td class="hidden">
                <div class="spacer"></div>
                <p>
                <?=date('d-m-Y', $reference['reference_date']);?>
                </p>
              </td>
              
              <td>
                <div class="spacer"></div>
                <p>
                <?=$reference['blog_title'];?>
                </p>
              </td>

                <td class="order">
                
                  <div class="spacer"></div>
                  
                  <p>
                  
                    <div class="up_down">
                    
                      <?php if(permission(CONTROLLER, 'edit')): ?>
                      
                        <?php if($x == 1 && $x != $count_pages): ?>

                        <a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$reference['order'];?>/<?=$reference['reference_id'];?>">
                          <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
                        </a>
                        
                        <?php endif; ?>
                        
                        <?php if($x != 1 && $x == $count_pages): ?>
                        
                        <a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$reference['order'];?>/<?=$reference['reference_id'];?>">
                          <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
                        </a>
                        
                        <?php endif; ?>
                        
                        <?php if($x != 1 && $x != $count_pages): ?>
                        
                        <a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$reference['order'];?>/<?=$reference['reference_id'];?>">
                          <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
                        </a>
                        <a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$reference['order'];?>/<?=$reference['reference_id'];?>">
                          <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
                        </a>
                        
                        <?php endif; ?>
                      
                      <?php endif; ?>
                      
                      <div class="clear"></div>
                      
                    </div>
                  
                  </p>
                
                </td>
              
              <td class="hidden">
              
                <div class="spacer"></div>
                
                <p>
              
                <?php if(permission('references', 'edit')): ?>
                
                  <?php if($reference['highlight'] == 1): ?>
                  
                  <input type="radio" name="highlight[<?=$reference['reference_id'];?>]" checked="checked" value="1">Da
                  <input type="radio" name="highlight[<?=$reference['reference_id'];?>]" value="0">Nu
                  
                  <?php else: ?>
                  
                  <input type="radio" name="highlight[<?=$reference['reference_id'];?>]" value="1">Da
                  <input type="radio" name="highlight[<?=$reference['reference_id'];?>]" checked="checked" value="0">Nu
                  
                  <?php endif; ?>
                
                <?php endif; ?>
                
                </p>
              
              </td>
              
              <td>
              
                <div class="spacer"></div>
                
                <p>
              
                <?php if(permission('references', 'edit')): ?>
                
                  <?php if($reference['active'] == 1): ?>
                  
                  <input type="radio" name="active[<?=$reference['reference_id'];?>]" checked="checked" value="1">Da
                  <input type="radio" name="active[<?=$reference['reference_id'];?>]" value="0">Nu
                  
                  <?php else: ?>
                  
                  <input type="radio" name="active[<?=$reference['reference_id'];?>]" value="1">Da
                  <input type="radio" name="active[<?=$reference['reference_id'];?>]" checked="checked" value="0">Nu
                  
                  <?php endif; ?>
                
                <?php endif; ?>
                
                </p>
              
              </td>
              
              <td>
                <div class="spacer"></div>
                
                <p>
                
                  <?=(permission('references', 'delete') ? '<a title="' . $this->lang->line('delete') . '" class="delete" href="' . SITE_URL . 'admin/references/delete/' . $reference['reference_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?>
                  
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