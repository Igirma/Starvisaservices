<?php require_once ELEM_DIR . 'admin_header.php';
 ?>

<div id="container">

  <div id="overview">
    
    <div class="orange_button">
      <div class="orange_button_left"></div>
      <div class="orange_button_con">
        <img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
        <div class="orange_button_space"></div>
        <a href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER;?>/add"><?=$this->lang->line('add_blog');?></a>
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
        <div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-blog"></div>
        <h1><?=($this->url->segment(2) == 'archive' ? $this->lang->line('overview_archive_header') : $this->lang->line('overview_header'));?></h1>
      
      </div>
      
      <form method="post" action="<?=$this->url->current;?>">

        <table>
        
          <thead>
          
            <tr>
            
              <th style="width: 3%;">
                Nr
              </th>
              <th class="text_left" style="width: 32%;">
                <div class="spacer"></div>
                <p style="padding-left: 10px;"><?=$this->lang->line('content_title');?></p>
              </th>
              <th style="width: 15%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('edit_language');?></p>
              </th>
              <th style="width: 10%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('start_date');?></p>
              </th>
              <th style="width: 10%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('end_date');?></p>
              </th>
              <th style="width: 10%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('active');?></p>
              </th>
              <th style="width: 10%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('delete');?></p>
              </th>
            
            </tr>
          
          </thead>
          
          <tbody>
          
          <?php if(isset($data['blog'])): ?>
          
            <?php $i = 0; ?>
            <?php foreach($data['blog'] as $blog): ?>
            
            <tr>
            
              <td><?=++$i;?></td>
              
              <td class="text_left">
                
                <?php if(permission(CONTROLLER, 'edit')):?>
                  <a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $blog['blog_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
                <?php endif; ?>
                
                <div class="spacer"></div>
              
                <span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($blog['title'], 35);?></span>

                <?php if(permission(CONTROLLER, 'edit')):?>
                  </a>
                <?php endif; ?>

              </td>
              
              <td>
              
                <div class="spacer"></div>
                
                <p>
                        
                <?php foreach($data['languages'] as $language): ?>

                  <?php $sub_active = $this->db->query('SELECT `blog_content`.sub_active FROM `blog_content` WHERE `blog_content`.language_id = ? AND `blog_content`.blog_id = ?', array($language['language_id'], $blog['blog_id'])); ?>
                  
                  <?php if($sub_active[0]['sub_active'] == 1): ?>
                    
                    <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                    
                    <a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/blog/edit/<?=$blog['blog_id']?>/<?=$language['language_id'];?>">
                      <span class="language sprite_<?=$language['code']?>"></span>
                    </a>
                    
                    <?php endif; ?>
                  
                  <?php else: ?>
                  
                  <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                    
                    <a title="<?=$language['name'];?>" class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/blog/edit/<?=$blog['blog_id']?>/<?=$language['language_id'];?>">
                      <span class="language_grey sprite_<?=$language['code']?>"></span>
                    </a>
                    
                    <?php endif; ?>
                  
                  <?php endif; ?>

                <?php endforeach; ?>
                
                </p>
              
              </td>
              
              <td>
              
                <div class="spacer"></div>
                
                <p><?=date('d-m-Y', $blog['start_date']);?></p>
                
              </td>
              
              <td>
              
                <div class="spacer"></div>
              
                <p><?=date('d-m-Y', $blog['end_date']);?></p>
                
              </td>
              
              <td>
                
                <div class="spacer"></div>
                
                <p>
              
                <?php if($blog['active'] == 1): ?>
                
                <input type="radio" name="active[<?=$blog['blog_id'];?>]" checked="checked" value="1">Da
                <input type="radio" name="active[<?=$blog['blog_id'];?>]" value="0">Nu
                
                <?php else: ?>
                
                <input type="radio" name="active[<?=$blog['blog_id'];?>]" value="1">Da
                <input type="radio" name="active[<?=$blog['blog_id'];?>]" checked="checked" value="0">Nu
                
                <?php endif; ?>
                
                
                </p>
                
              </td>
              
              <td>
                <div class="spacer"></div>
              
                <p><?=(permission(CONTROLLER, 'delete') ? '<a title="' . $this->lang->line('delete') . '" class="delete" href="' . SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/delete/' . $blog['blog_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?></p>
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