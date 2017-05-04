<?php require_once ELEM_DIR . 'admin_header.php';
 
?>

<div id="container">

  <div id="overview">
    
    <div class="orange_button">
      <div class="orange_button_left"></div>
      <div class="orange_button_con">
        <img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
        <div class="orange_button_space"></div>
        <a href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER;?>/add"><?=$this->lang->line('add_news');?></a>
      </div>
      <div class="orange_button_right"></div>
    </div>
    
    <div class="orange_button">
      <div class="orange_button_left"></div>
      <div class="orange_button_con">
        <?=($this->url->segment(2) != 'archive' ? '<a href="' . SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/archive">' . $this->lang->line('overview_archive_header') . '</a>' : '<a href="' . SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '">' . $this->lang->line('overview_header') . '</a>');?>
      </div>
      <div class="orange_button_right"></div>
    </div>
    
    <div class="clear"></div>
    
    <div class="column">

      <div class="pie header">
        <div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-news"></div>
        <h1><?=($this->url->segment(2) == 'archive' ? $this->lang->line('overview_archive_header') : $this->lang->line('overview_header'));?></h1>
      
      </div>
      
      <form method="post" action="<?=$this->url->current;?>">

        <table>
        
          <thead>
          
            <tr>
            
              <th style="width: 3%;">
                Nr
              </th>
              <th class="text_left" style="width: 33%;">
                <div class="spacer"></div>
                <p style="padding-left: 10px;"><?=$this->lang->line('content_title');?></p>
              </th>
              <th style="width: 11%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('edit_language');?></p>
              </th>
              <th style="width: 16%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('start_date');?></p>
              </th>
              <th style="width: 16%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('end_date');?></p>
              </th>
              <th style="width: 10%;" class="hidden">
                <div class="spacer"></div>
                <p><?=$this->lang->line('no_end_date');?></p>
              </th>
              <th style="width: 11%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('active');?></p>
              </th>
              <th style="width: 13%;" class="hidden">
                <div class="spacer"></div>
                <p>Op voorpagina tonen</p>
              </th>
              <th style="width: 12%;" class="hidden">
                <div class="spacer"></div>
                <p>Acties</p>
              </th>
              <th style="width: 12%;" class="hidden">
                <div class="spacer"></div>
                <p>School</p>
              </th>
              <th style="width: 20%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('delete');?></p>
              </th>
            
            </tr>
          
          </thead>
          
          <tbody>
          
          <?php if(isset($data['news'])): ?>
          
            <?php $i = 0; ?>
            <?php foreach($data['news'] as $news): ?>
            
            <tr>
            
              <td><?=++$i;?></td>
              
              <td class="text_left">
                
                <?php if(permission(CONTROLLER, 'edit')):?>
                  <a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $news['news_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
                <?php endif; ?>
                
                <div class="spacer"></div>
              
                <span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($news['title'], 35);?></span>

                <?php if(permission(CONTROLLER, 'edit')):?>
                  </a>
                <?php endif; ?>

              </td>
              
              <td>
              
                <div class="spacer"></div>
                
                <p>
                        
                <?php foreach($data['languages'] as $language): ?>

                  <?php $sub_active = $this->db->query('SELECT `news_content`.sub_active FROM `news_content` WHERE `news_content`.language_id = ? AND `news_content`.news_id = ?', array($language['language_id'], $news['news_id'])); ?>
                  
                  <?php if($sub_active[0]['sub_active'] == 1): ?>
                    
                    <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                    
                    <a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/news/edit/<?=$news['news_id']?>/<?=$language['language_id'];?>">
                      <span class="language sprite_<?=$language['code']?>"></span>
                    </a>
                    
                    <?php endif; ?>
                  
                  <?php else: ?>
                  
                  <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                    
                    <a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/news/edit/<?=$news['news_id']?>/<?=$language['language_id'];?>">
                      <span class="language_grey sprite_<?=$language['code']?>"></span>
                    </a>
                    
                    <?php endif; ?>
                  
                  <?php endif; ?>

                <?php endforeach; ?>
                
                </p>
              
              </td>
              
              <td>
              
                <div class="spacer"></div>
                
                <p><?=date('d-m-Y', $news['start_date']);?></p>
                
              </td>
              
              <td>
              
                <div class="spacer"></div>
              
                <p><?=date('d-m-Y', $news['end_date']);?></p>
                
              </td>
              
              <td class="hidden">
                
                <div class="spacer"></div>
                
                <p>
              
                <?php if($news['no_end_date'] == 1): ?>
                
                <input type="radio" name="no_end_date[<?=$news['news_id'];?>]" value="0"><?=$this->lang->line('yes')?>
                <input type="radio" name="no_end_date[<?=$news['news_id'];?>]" checked="checked" value="1"><?=$this->lang->line('no')?>
                
                <?php else: ?>
                
                <input type="radio" name="no_end_date[<?=$news['news_id'];?>]" checked="checked" value="0"><?=$this->lang->line('yes')?>
                <input type="radio" name="no_end_date[<?=$news['news_id'];?>]" value="1"><?=$this->lang->line('no')?>
                
                <?php endif; ?>
                
                
                </p>
                
              </td>
              
              
              <td>
                
                <div class="spacer"></div>
                
                <p>
              
                <?php if($news['active'] == 1): ?>
                
                <input type="radio" name="active[<?=$news['news_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes')?>
                <input type="radio" name="active[<?=$news['news_id'];?>]" value="0"><?=$this->lang->line('no')?>
                
                <?php else: ?>
                
                <input type="radio" name="active[<?=$news['news_id'];?>]" value="1"><?=$this->lang->line('yes')?>
                <input type="radio" name="active[<?=$news['news_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no')?>
                
                <?php endif; ?>
                
                
                </p>
                
              </td>
              
              <td class="hidden">
                
                <div class="spacer"></div>
                
                <p>
              
                <?php if($news['highlight'] == 1): ?>
                
                <input type="radio" name="highlight[<?=$news['news_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes')?>
                <input type="radio" name="highlight[<?=$news['news_id'];?>]" value="0"><?=$this->lang->line('no')?>
                
                <?php else: ?>
                
                <input type="radio" name="highlight[<?=$news['news_id'];?>]" value="1"><?=$this->lang->line('yes')?>
                <input type="radio" name="highlight[<?=$news['news_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no')?>
                
                <?php endif; ?>
                
                
                </p>
                
              </td>
              
              <td class="hidden">
                
                <div class="spacer"></div>
                
                <p>
              
                <?php if($news['page_news'] == 1): ?>
                
                <input type="radio" name="page_news[<?=$news['news_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes')?>
                <input type="radio" name="page_news[<?=$news['news_id'];?>]" value="0"><?=$this->lang->line('no')?>
                
                <?php else: ?>
                
                <input type="radio" name="page_news[<?=$news['news_id'];?>]" value="1"><?=$this->lang->line('yes')?>
                <input type="radio" name="page_news[<?=$news['news_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no')?>
                
                <?php endif; ?>
                
                </p>
                
              </td>
              
              <td class="hidden">
                
                <div class="spacer"></div>
                
                <p>
              
                <?php if($news['page_school'] == 1): ?>
                
                <input type="radio" name="page_school[<?=$news['news_id'];?>]" checked="checked" value="1"><?=$this->lang->line('yes')?>
                <input type="radio" name="page_school[<?=$news['news_id'];?>]" value="0"><?=$this->lang->line('no')?>
                
                <?php else: ?>
                
                <input type="radio" name="page_school[<?=$news['news_id'];?>]" value="1"><?=$this->lang->line('yes')?>
                <input type="radio" name="page_school[<?=$news['news_id'];?>]" checked="checked" value="0"><?=$this->lang->line('no')?>
                
                <?php endif; ?>
                
                </p>
                
              </td>
              
              <td>
                <div class="spacer"></div>
              
                <p><?=(permission(CONTROLLER, 'delete') ? '<a title="' . $this->lang->line('delete') . '" class="delete" href="' . SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/delete/' . $news['news_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?></p>
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