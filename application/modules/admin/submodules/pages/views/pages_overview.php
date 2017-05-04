<?php require_once(ELEM_DIR . 'admin_header.php'); ?>

<div id="container">

  <div id="overview">
  
    <div class="orange_button">
      <div class="orange_button_left"></div>
      <div class="orange_button_con">
        <img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
        <div class="orange_button_space"></div>
        <a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER?>/add"><?=$this->lang->line('add_page');?></a>
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
            
              <th style="width: 5%;">Nr</th>
              
              <th class="text_left" style="width: 33%;">
                <div class="spacer"></div>
                <p style="padding-left: 10px;"><?=$this->lang->line('page_name');?></p>
              </th>
              
              <th style="width: 10%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('edit_language');?></p>
              </th>
              
              <th style="width: 7%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('order');?></p>
              </th>
              
              <th style="width: 10%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('active');?></p>
              </th>

              <th style="width: 9%;" class="hidden">
                <div class="spacer"></div>
                <p><?=$this->lang->line('show_on_homepage');?></p>
              </th>
              
              <th style="width: 10%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('show_main_menu');?></p>
              </th>
              
              <th>
                <div class="spacer"></div>
                <p><?=$this->lang->line('show_side_menu');?></p>
              </th>
              
              <th style="width: 9%;" class="hidden">
                <div class="spacer"></div>
                <p><?=$this->lang->line('show_footer_menu');?></p>
              </th>

              <th style="width: 13%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('delete');?></p>
              </th>
              
            </tr>
            
          </thead>
        
          <tbody>
            
            <?php if(isset($data['pages'])): ?>
            
              <?php $i = 0; ?>
              
              <?php $count_pages = count($data['pages']); ?>
              
              <?php foreach($data['pages'] as $page): ?>
              
              <tr>
              
                <td>
                
                  <?=++$i;?>
                
                </td>
                
                <td class="text_left">
                
                  <?php if(permission(CONTROLLER, 'edit')):?>
                    <a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $page['page_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
                  <?php endif; ?>
                  
                  <div class="spacer"></div>
                
                  <span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($page['menu_title'], 35);?></span>

                  <?php if(permission(CONTROLLER, 'edit')):?>
                    </a>
                  <?php endif; ?>
                  
                </td>
                
                <td>
                
                  <div class="spacer"></div>
                  
                  <p>
                
                  <?php foreach($data['languages'] as $language): ?>

                    <?php $sub_active = $this->db->query('SELECT `page_content`.sub_active FROM `page_content` WHERE `page_content`.language_id = ? AND `page_content`.page_id = ?', array($language['language_id'], $page['page_id'])); ?>
                    
                    <?php if($sub_active[0]['sub_active'] == 1): ?>
                    
                      <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                    
                      <a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$page['page_id']?>/<?=$language['language_id'];?>">
                        
                        <span class="language sprite_<?=$language['code']?>"></span>
                      
                      </a>
                      
                      <?php endif; ?>
                    
                    <?php else: ?>
                    
                      <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                    
                      <a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$page['page_id']?>/<?=$language['language_id'];?>">
                        
                        <span class="language_grey sprite_<?=$language['code']?>"></span>
                      
                      </a>
                      
                      <?php endif; ?>
                    
                    <?php endif; ?>

                  <?php endforeach; ?>
                
                  </p>
                
                </td>
                
                <td class="order">
                
                  <div class="spacer"></div>
                  
                  <p>
                  
                    <div class="up_down">
                    
                      <?php if(permission(CONTROLLER, 'edit')): ?>
                      
                        <?php if($i == 1 && $i != $count_pages): ?>

                        <a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$page['order'];?>/<?=$page['parent_id'];?>/<?=$page['page_id'];?>">
                          <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
                        </a>
                        
                        <?php endif; ?>
                        
                        <?php if($i != 1 && $i == $count_pages): ?>
                        
                        <a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$page['order'];?>/<?=$page['parent_id'];?>/<?=$page['page_id'];?>">
                          <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
                        </a>
                        
                        <?php endif; ?>
                        
                        <?php if($i != 1 && $i != $count_pages): ?>
                        
                        <a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$page['order'];?>/<?=$page['parent_id'];?>/<?=$page['page_id'];?>">
                          <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
                        </a>
                        <a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$page['order'];?>/<?=$page['parent_id'];?>/<?=$page['page_id'];?>">
                          <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
                        </a>
                        
                        <?php endif; ?>
                      
                      <?php endif; ?>
                      
                      <div class="clear"></div>
                      
                    </div>
                  
                  </p>
                
                </td>
                
                <td>
                
                  <div class="spacer"></div>
                  
                  <p>
                
                  <?php if($page['controller'] != 'home' && permission(CONTROLLER, 'edit')): ?>
                
                    <?php if($page['active'] == 1): ?>
                    
                    <input type="radio" name="active[<?=$page['page_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
                    
                    <input type="radio" name="active[<?=$page['page_id'];?>]" value="0"><?=$this->lang->line('no');?>
                    
                    <?php else: ?>
                    
                    <input type="radio" name="active[<?=$page['page_id'];?>]" value="1"><?=$this->lang->line('yes');?>
                    
                    <input type="radio" name="active[<?=$page['page_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
                    
                    <?php endif; ?>
                    
                  <?php endif; ?>
                  
                  </p>
                    
                </td>

                <td class="hidden">
                
                  <div class="spacer"></div>
                  
                  <p>
                
                  <?php if(permission(CONTROLLER, 'edit')): ?>
                  
                    <?php if($page['highlight'] == 1): ?>
                    
                    <input type="radio" name="highlight[<?=$page['page_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
                    
                    <input type="radio" name="highlight[<?=$page['page_id'];?>]" value="0"><?=$this->lang->line('no');?>
                    
                    <?php else: ?>
                    
                    <input type="radio" name="highlight[<?=$page['page_id'];?>]" value="1"><?=$this->lang->line('yes');?>
                    
                    <input type="radio" name="highlight[<?=$page['page_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
                    
                    <?php endif; ?>
                    
                  <?php endif; ?>
                  
                  </p>
                  
                </td>

                <td>
                
                  <div class="spacer"></div>
                  
                  <p>
                
                  <?php if(permission(CONTROLLER, 'edit')): ?>
                  
                    <?php if($page['main_menu'] == 1): ?>
                    
                    <input type="radio" name="main_menu[<?=$page['page_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
                    
                    <input type="radio" name="main_menu[<?=$page['page_id'];?>]" value="0"><?=$this->lang->line('no');?>
                    
                    <?php else: ?>
                    
                    <input type="radio" name="main_menu[<?=$page['page_id'];?>]" value="1"><?=$this->lang->line('yes');?>
                    
                    <input type="radio" name="main_menu[<?=$page['page_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
                    
                    <?php endif; ?>
                    
                  <?php endif; ?>
                  
                  </p>
                  
                </td>
                
                <td>
                
                  <div class="spacer"></div>
                  
                  <p>
                
                  <?php if(permission(CONTROLLER, 'edit')): ?>
                  
                    <?php if($page['menu'] == 1): ?>
                    
                    <input type="radio" name="menu[<?=$page['page_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
                    
                    <input type="radio" name="menu[<?=$page['page_id'];?>]" value="0"><?=$this->lang->line('no');?>
                    
                    <?php else: ?>
                    
                    <input type="radio" name="menu[<?=$page['page_id'];?>]" value="1"><?=$this->lang->line('yes');?>
                    
                    <input type="radio" name="menu[<?=$page['page_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
                    
                    <?php endif; ?>
                    
                  <?php endif; ?>
                  
                  </p>
                  
                </td>
                
                <td class="hidden">
                
                  <div class="spacer"></div>
                  
                  <p>
                
                  <?php if(permission(CONTROLLER, 'edit')): ?>
                  
                    <?php if($page['footer'] == 1): ?>
                    
                    <input type="radio" name="footer[<?=$page['page_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
                    
                    <input type="radio" name="footer[<?=$page['page_id'];?>]" value="0"><?=$this->lang->line('no');?>
                    
                    <?php else: ?>
                    
                    <input type="radio" name="footer[<?=$page['page_id'];?>]" value="1"><?=$this->lang->line('yes');?>
                    
                    <input type="radio" name="footer[<?=$page['page_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
                    
                    <?php endif; ?>
                    
                  <?php endif; ?>
                  
                  </p>
                  
                </td>
                
                <td>
                
                  <div class="spacer"></div>
                  
                  <p>
                
                  <?php if($page['deletable'] == 1 && permission(CONTROLLER, 'delete') && empty($page['children'])): ?>
                  
                  <a title="<?=$this->lang->line('delete')?>" class="delete" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/delete/<?=$page['page_id'];?>"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/delete.png"></a>
                  
                  <?php endif; ?>
                  
                  </p>
                  
                </td>
                
              </tr>
              
                <?php if(!empty($page['children'])): ?>
                
                <?php $x = 0; ?>

                      <?php $count_child = count($page['children']); ?>
                      
                      <?php foreach($page['children'] as $child): ?>
                        
                        <tr>
                          
                          <td>
                          
                            <?=$i . '.' . ++$x;?>
                            
                          </td>
                          
                          <td style="width: 25%;" class="text_left">
                          
                            <?php if(permission(CONTROLLER, 'edit')):?>
                              <a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $child['page_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
                            <?php endif; ?>
                            
                            <div class="spacer"></div>
                          
                            <span style="line-height: 40px; padding-left: 30px;"><?=str_shorten($child['menu_title'], 35);?></span>

                            <?php if(permission(CONTROLLER, 'edit')):?>
                              </a>
                            <?php endif; ?>

                          </td>
                          
                          <td>
                          
                            <div class="spacer"></div>
                            
                            <p>
                          
                              <?php foreach($data['languages'] as $language): ?>

                                <?php $sub_active = $this->db->query('SELECT `page_content`.sub_active FROM `page_content` WHERE `page_content`.language_id = ? AND `page_content`.page_id = ?', array($language['language_id'], $child['page_id'])); ?>
                                
                                <?php if($sub_active[0]['sub_active'] == 1): ?>
                                
                                  <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                                
                                  <a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$child['page_id']?>/<?=$language['language_id'];?>">
                                    
                                    <span class="language sprite_<?=$language['code']?>"></span>
                                  
                                  </a>
                                  
                                  <?php endif; ?>
                                
                                <?php else: ?>
                                
                                  <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                                
                                  <a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$child['page_id']?>/<?=$language['language_id'];?>">
                                    
                                    <span class="language_grey sprite_<?=$language['code']?>"></span>
                                  
                                  </a>
                                  
                                  <?php endif; ?>
                                
                                <?php endif; ?>

                              <?php endforeach; ?>
                            
                            <p>
                          
                          </td>
                          
                          <td class="order">
                          
                            <div class="spacer"></div>
                            
                            <p>
                            
                              <div class="up_down">
                              
                                <?php if(permission(CONTROLLER, 'edit')): ?>

                                  <?php if($x == 1 && $x != $count_child): ?>

                                  <a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$child['order'];?>/<?=$child['parent_id'];?>/<?=$child['page_id'];?>">
                                    <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
                                  </a>
                                  
                                  <?php endif; ?>
                                  
                                  <?php if($x != 1 && $x == $count_child): ?>
                                  
                                  <a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$child['order'];?>/<?=$child['parent_id'];?>/<?=$child['page_id'];?>">
                                    <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
                                  </a>
                                  
                                  <?php endif; ?>
                                  
                                  <?php if($x != 1 && $x != $count_child): ?>
                                  
                                  <a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$child['order'];?>/<?=$child['parent_id'];?>/<?=$child['page_id'];?>">
                                    <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
                                  </a>
                                  <a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$child['order'];?>/<?=$child['parent_id'];?>/<?=$child['page_id'];?>">
                                    <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
                                  </a>
                                  
                                  <?php endif; ?>
                                
                                <?php endif; ?>
                                
                              </div>
                              
                            <p>
                          
                          </td>
                          
                          <td>
                          
                            <div class="spacer"></div>
                            
                            <p>
                          
                            <?php if(permission(CONTROLLER, 'edit')): ?>
                            
                              <?php if($child['active'] == 1): ?>
                              
                              <input type="radio" name="active[<?=$child['page_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
                              
                              <input type="radio" name="active[<?=$child['page_id'];?>]" value="0"><?=$this->lang->line('no');?>
                              
                              <?php else: ?>
                              
                              <input type="radio" name="active[<?=$child['page_id'];?>]" value="1"><?=$this->lang->line('yes');?>
                              
                              <input type="radio" name="active[<?=$child['page_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
                              
                              <?php endif; ?>
                            
                            <?php endif; ?>
                            
                            </p>
                            
                          </td>
                          
                          <td class="hidden">
                          
                            <div class="spacer"></div>
                            
                            <p>
                          
                            <?php if(permission(CONTROLLER, 'edit')): ?>
                            
                              <?php if($child['highlight'] == 1): ?>
                              
                              <input type="radio" name="highlight[<?=$child['page_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
                              
                              <input type="radio" name="highlight[<?=$child['page_id'];?>]" value="0"><?=$this->lang->line('no');?>
                              
                              <?php else: ?>
                              
                              <input type="radio" name="highlight[<?=$child['page_id'];?>]" value="1"><?=$this->lang->line('yes');?>
                              
                              <input type="radio" name="highlight[<?=$child['page_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
                              
                              <?php endif; ?>
                            
                            <?php endif; ?>
                            
                            </p>
                            
                          </td>
                          
                          <td>
                          
                            <div class="spacer"></div>
                            
                            <p>
                          
                            <?php if(permission(CONTROLLER, 'edit')): ?>
                          
                              <?php if($child['main_menu'] == 1): ?>
                              
                              <input type="radio" name="main_menu[<?=$child['page_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
                              
                              <input type="radio" name="main_menu[<?=$child['page_id'];?>]" value="0"><?=$this->lang->line('no');?>
                              
                              <?php else: ?>
                              
                              <input type="radio" name="main_menu[<?=$child['page_id'];?>]" value="1"><?=$this->lang->line('yes');?>
                              
                              <input type="radio" name="main_menu[<?=$child['page_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
                              
                              <?php endif; ?>
                            
                            <?php endif; ?>
                            
                            </p>
                            
                          </td>
                          
                          <td>
                          
                            <div class="spacer"></div>
                            
                            <p>
                          
                            <?php if(permission(CONTROLLER, 'edit')): ?>
                          
                              <?php if($child['menu'] == 1): ?>
                              
                              <input type="radio" name="menu[<?=$child['page_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
                              
                              <input type="radio" name="menu[<?=$child['page_id'];?>]" value="0"><?=$this->lang->line('no');?>
                              
                              <?php else: ?>
                              
                              <input type="radio" name="menu[<?=$child['page_id'];?>]" value="1"><?=$this->lang->line('yes');?>
                              
                              <input type="radio" name="menu[<?=$child['page_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
                              
                              <?php endif; ?>
                            
                            <?php endif; ?>
                            
                            </p>
                            
                          </td>
                          
                          <td class="hidden">
                          
                            <div class="spacer"></div>
                            
                            <p>
                          
                            <?php if(permission(CONTROLLER, 'edit')): ?>
                          
                              <?php if($child['footer'] == 1): ?>
                              
                              <input type="radio" name="footer[<?=$child['page_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
                              
                              <input type="radio" name="footer[<?=$child['page_id'];?>]" value="0"><?=$this->lang->line('no');?>
                              
                              <?php else: ?>
                              
                              <input type="radio" name="footer[<?=$child['page_id'];?>]" value="1"><?=$this->lang->line('yes');?>
                              
                              <input type="radio" name="footer[<?=$child['page_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
                              
                              <?php endif; ?>
                            
                            <?php endif; ?>
                            
                            </p>
                            
                          </td>
                          
                          <td>
                          
                            <div class="spacer"></div>
                            
                            <p>
                            
                            <?php if($child['deletable'] == 1 && permission(CONTROLLER, 'delete') && empty($child['children'])): ?>
                            
                            <a title="<?=$this->lang->line('delete')?>" class="delete" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/delete/<?=$child['page_id'];?>"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/delete.png"></a>
                            
                            <?php endif; ?>
                            
                            </p>
                            
                          </td>
                          
                        </tr>
                        
                          <?php if(!empty($child['children'])): ?>
                
                          <?php $y = 0; ?>

                                <?php $count_sub_child = count($child['children']); ?>
                                
                                  <?php foreach($child['children'] as $sub_child): ?>
                                    
                                    <tr>

                                      <td>
                                        
                                        <?=$i . '.' . $x . '.' . ++$y;?>
                                      
                                      </td>
                                      
                                      <td class="text_left">
                                        
                                        <?php if(permission(CONTROLLER, 'edit')):?>
                                          <a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $sub_child['page_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
                                        <?php endif; ?>
                                        
                                        <div class="spacer"></div>
                                      
                                        <span style="line-height: 40px; padding-left: 50px;"><?=str_shorten($sub_child['menu_title'], 35);?></span>

                                        <?php if(permission(CONTROLLER, 'edit')):?>
                                          </a>
                                        <?php endif; ?>

                                      </td>
                                      
                                      <td>
                  
                                      <div class="spacer"></div>
                                      
                                        <p>
                                      
                                          <?php foreach($data['languages'] as $language): ?>

                                            <?php $sub_active = $this->db->query('SELECT `page_content`.sub_active FROM `page_content` WHERE `page_content`.language_id = ? AND `page_content`.page_id = ?', array($language['language_id'], $sub_child['page_id'])); ?>
                                            
                                            <?php if($sub_active[0]['sub_active'] == 1): ?>
                                            
                                              <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                                            
                                              <a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$sub_child['page_id']?>/<?=$language['language_id'];?>">
                                                
                                                <span class="language sprite_<?=$language['code']?>"></span>
                                              
                                              </a>
                                              
                                              <?php endif; ?>
                                            
                                            <?php else: ?>
                                            
                                              <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                                            
                                              <a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$sub_child['page_id']?>/<?=$language['language_id'];?>">
                                                
                                                <span class="language_grey sprite_<?=$language['code']?>"></span>
                                              
                                              </a>
                                              
                                              <?php endif; ?>
                                            
                                            <?php endif; ?>

                                          <?php endforeach; ?>
                                        
                                        <p>
                                      
                                      </td>
                                      
                                      <td class="order">
                                      
                                        <div class="spacer"></div>
                                        
                                        <p>
                                        
                                          <div class="up_down">
                                          
                                            <?php if(permission(CONTROLLER, 'edit')): ?>

                                              <?php if($y == 1 && $y != $count_sub_child): ?>
                                        
                                              <a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$sub_child['order'];?>/<?=$sub_child['parent_id'];?>/<?=$sub_child['page_id'];?>">
                                                <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
                                              </a>
                                              
                                              <?php endif; ?>
                                              
                                              <?php if($y != 1 && $y == $count_sub_child): ?>
                                              
                                              <a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$sub_child['order'];?>/<?=$sub_child['parent_id'];?>/<?=$sub_child['page_id'];?>">
                                                <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
                                              </a>
                                              
                                              <?php endif; ?>
                                              
                                              <?php if($y != 1 && $y != $count_sub_child): ?>
                                              
                                              <a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$sub_child['order'];?>/<?=$sub_child['parent_id'];?>/<?=$sub_child['page_id'];?>">
                                                <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
                                              </a>
                                              <a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$sub_child['order'];?>/<?=$sub_child['parent_id'];?>/<?=$sub_child['page_id'];?>">
                                                <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
                                              </a>
                                              
                                              <?php endif; ?>
                                            
                                            <?php endif; ?>
                                            
                                          </div>

                                        </p>
                                        
                                      </td>
                                      
                                      <td>
                                      
                                        <div class="spacer"></div>
                                        
                                        <p>
                                      
                                        <?php if(permission(CONTROLLER, 'edit')): ?>
                                          
                                          <?php if($sub_child['active'] == 1): ?>
                                          
                                          <input type="radio" name="active[<?=$sub_child['page_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
                                          
                                          <input type="radio" name="active[<?=$sub_child['page_id'];?>]" value="0"><?=$this->lang->line('no');?>
                                          
                                          <?php else: ?>
                                          
                                          <input type="radio" name="active[<?=$sub_child['page_id'];?>]" value="1"><?=$this->lang->line('yes');?>
                                          
                                          <input type="radio" name="active[<?=$sub_child['page_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
                                          
                                          <?php endif; ?>
                                        
                                        <?php endif; ?>
                                        
                                        </p>
                                        
                                      </td>
                                      
                                      <td class="hidden">
                                      
                                        <div class="spacer"></div>
                                        
                                        <p>
                                      
                                        <?php if(permission(CONTROLLER, 'edit')): ?>
                                          
                                          <?php if($sub_child['highlight'] == 1): ?>
                                          
                                          <input type="radio" name="highlight[<?=$sub_child['page_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
                                          
                                          <input type="radio" name="highlight[<?=$sub_child['page_id'];?>]" value="0"><?=$this->lang->line('no');?>
                                          
                                          <?php else: ?>
                                          
                                          <input type="radio" name="highlight[<?=$sub_child['page_id'];?>]" value="1"><?=$this->lang->line('yes');?>
                                          
                                          <input type="radio" name="highlight[<?=$sub_child['page_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
                                          
                                          <?php endif; ?>
                                        
                                        <?php endif; ?>
                                        
                                        </p>
                                        
                                      </td>

                                      <td>
                                      
                                        <div class="spacer"></div>
                                        
                                        <p>
                                      
                                        <?php if(permission(CONTROLLER, 'edit')): ?>
                                        
                                          <?php if($sub_child['main_menu'] == 1): ?>
                                          
                                          <input type="radio" name="main_menu[<?=$sub_child['page_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
                                          
                                          <input type="radio" name="main_menu[<?=$sub_child['page_id'];?>]" value="0"><?=$this->lang->line('no');?>
                                          
                                          <?php else: ?>
                                          
                                          <input type="radio" name="main_menu[<?=$sub_child['page_id'];?>]" value="1"><?=$this->lang->line('yes');?>
                                          
                                          <input type="radio" name="main_menu[<?=$sub_child['page_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
                                          
                                          <?php endif; ?>
                                        
                                        <?php endif; ?>
                                        
                                        </p>
                                        
                                      </td>
                                      
                                      <td class="hidden">
                                      
                                        <div class="spacer"></div>
                                        
                                        <p>
                                      
                                        <?php if(permission(CONTROLLER, 'edit')): ?>
                                        
                                          <?php if($sub_child['menu'] == 1): ?>
                                          
                                          <input type="radio" name="menu[<?=$sub_child['page_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
                                          
                                          <input type="radio" name="menu[<?=$sub_child['page_id'];?>]" value="0"><?=$this->lang->line('no');?>
                                          
                                          <?php else: ?>
                                          
                                          <input type="radio" name="menu[<?=$sub_child['page_id'];?>]" value="1"><?=$this->lang->line('yes');?>
                                          
                                          <input type="radio" name="menu[<?=$sub_child['page_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
                                          
                                          <?php endif; ?>
                                        
                                        <?php endif; ?>
                                        
                                        </p>
                                        
                                      </td>
                                      
                                      <td class="hidden">
                                      
                                        <div class="spacer"></div>
                                        
                                        <p>
                                      
                                        <?php if(permission(CONTROLLER, 'edit')): ?>
                                        
                                          <?php if($sub_child['footer'] == 1): ?>
                                          
                                          <input type="radio" name="footer[<?=$sub_child['page_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes');?>
                                          
                                          <input type="radio" name="footer[<?=$sub_child['page_id'];?>]" value="0"><?=$this->lang->line('no');?>
                                          
                                          <?php else: ?>
                                          
                                          <input type="radio" name="footer[<?=$sub_child['page_id'];?>]" value="1"><?=$this->lang->line('yes');?>
                                          
                                          <input type="radio" name="footer[<?=$sub_child['page_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no');?>
                                          
                                          <?php endif; ?>
                                        
                                        <?php endif; ?>
                                        
                                        </p>
                                        
                                      </td>
                                      
                                      <td>
                                      
                                        <div class="spacer"></div>
                                        
                                        <p>
                                        
                                        <?php if($sub_child['deletable'] == 1 && permission(CONTROLLER, 'delete') && empty($sub_child['children'])): ?>
                                        
                                        <a title="<?=$this->lang->line('delete')?>" class="delete" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/delete/<?=$sub_child['page_id'];?>"><img src="<?=SITE_URL . ELEM_DIR;?>img_admin/delete.png"></a>
                                        
                                        <?php endif; ?>
                                        
                                        </p>
                                        
                                      </td>
                                      
                                    </tr>
                                                
                                  <?php endforeach; ?>

                          <?php endif; ?>
                                    
                        <?php endforeach; ?>

                <?php endif; ?>
              
              <?php endforeach; ?>
            
            <?php endif; ?>
            
          </tbody>
        
        </table>

      </form>
      
    </div>
    
  </div>
  
</div>

<?php require_once(ELEM_DIR . 'admin_footer.php'); ?>								