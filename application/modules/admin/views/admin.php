<?php require_once ELEM_DIR . 'admin_header.php'; ?>

<div id="container">

  <div id="overview">
  
    <div class="column" style="border: none;">
      
      <?php if(permission_overall('pages')): ?>
      <div class="dash_item">
      
        <div class="pie subheader">
          <div style="position: absolute; top: 16px; left: 12px;" class="sprite sprite-pages"></div>
          <h2><?=$this->lang->line('last_edit_pages');?></h2>
          
          <div class="options">
          
            <ul>
            
              <li>
                <a class="info">
                  <div class="sprite sprite-info"></div>
                </a>
              </li>
              <li>
                <div class="spacer"></div>
              </li>
              <li>
                <a class="toggle_dash min">
                  <div class="sprite sprite-min"></div>
                </a>
              </li>
            
            </ul>
            
          </div>

        </div>
        
        <div class="subcolumn">
        
          <table>
        
            <thead>
            
              <tr>
              
                <th class="text_left" style="width: 35%;"><p style="padding-left: 10px;"><?=$this->lang->line('page_title');?></p></th>
                <th class="text_left" style="width: 20%;">
                  <div class="spacer"></div>
                  <p style="padding-left: 10px;"><?=$this->lang->line('when');?></p>
                </th>
                <th class="text_left" style="width: 20%;">
                  <div class="spacer"></div>
                  <p style="padding-left: 10px;"><?=$this->lang->line('by_user');?></p>
                </th>
              
              </tr>
            
            </thead>
            
            <tbody>

            <?php if(isset($data['pages'])): ?>
            
              <?php $i = 0; ?>
              
              <?php foreach($data['pages']['add'] as $page): ?>
              
                <?++$i;?>
              
                <tr>
                  
                  <td class="text_left">
                    
                    <?php if(permission('pages', 'edit')):?>
                      <a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/pages/edit/' . $page['page_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
                    <?php endif; ?>
                  
                    <span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($page['menu_title'], 30);?></span>

                    <?php if(permission('pages', 'edit')):?>
                      </a>
                    <?php endif; ?>
                    
                  </td>
                  
                  <td class="text_left">
                  
                    <div style="margin-top: 15px;" class="spacer"></div>
                    
                    <p style="padding-left: 10px;">
                    
                      <?php if($page['last_update'] != ''): ?>
                      
                        <div style="float: left; padding: 7px 10px 0 10px;"><img alt="edit" src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit_lg.png"></div>
                        <i><?=date('d-m-Y', $page['last_update']);?></i><br>
                        <small><i><?=date('H:i', $page['last_update']);?></i></small>

                      <?php else: ?>
                      
                        <div style="float: left; padding: 7px 10px 0 10px;"><img alt="add" src="<?=SITE_URL . ELEM_DIR;?>img_admin/plus_lg.png"></div>
                        <i><?=date('d-m-Y', $page['date_created']);?></i><br>
                        <small><i><?=date('H:i', $page['date_created']);?></i></small>
                      
                      <?php endif; ?>
                    
                    </p>
                    
                  </td>
                  
                  <td class="text_left">
                  
                    <div class="spacer"></div>
                    
                    <p style="padding-left: 10px;">
                      <i><?=ucfirst($page['edit_by']);?></i>
                    </p>
                  
                  </td>
                
                </tr>
              
              <?php endforeach; ?>
              
              <?php foreach($data['pages']['edit'] as $page): ?>
              
                <?++$i;?>
              
                <tr>
                  
                  <td class="text_left">
                    
                    <?php if(permission('pages', 'edit')):?>
                      <a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/pages/edit/' . $page['page_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
                    <?php endif; ?>
                  
                    <span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($page['menu_title'], 30);?></span>

                    <?php if(permission('pages', 'edit')):?>
                      </a>
                    <?php endif; ?>
                    
                  </td>
                  
                  <td class="text_left">
                  
                    <div style="margin-top: 15px;" class="spacer"></div>
                    
                    <p style="padding-left: 10px;">
                    
                      <?php if($page['last_update'] != ''): ?>
                      
                        <div style="float: left; padding: 7px 10px 0 10px;"><img alt="edit" src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit_lg.png"></div>
                        <i><?=date('d-m-Y', $page['last_update']);?></i><br>
                        <small><i><?=date('H:i', $page['last_update']);?></i></small>

                      <?php else: ?>
                      
                        <div style="float: left; padding: 7px 10px 0 10px;"><img alt="add" src="<?=SITE_URL . ELEM_DIR;?>img_admin/plus_lg.png"></div>
                        <i><?=date('d-m-Y', $page['date_created']);?></i><br>
                        <small><i><?=date('H:i', $page['date_created']);?></i></small>
                      
                      <?php endif; ?>
                    
                    </p>
                    
                  </td>
                  
                  <td class="text_left">
                  
                    <div class="spacer"></div>
                    
                    <p style="padding-left: 10px;">
                      <i><?=ucfirst($page['edit_by']);?></i>
                    </p>
                  
                  </td>
                
                </tr>
              
              <?php endforeach; ?>
              
            <?php endif; ?>
            
            </tbody>
          
          </table>
          
          <div class="menu" style="padding-left: 10px;">
          
            <?=(permission('pages', 'add') ? '<img alt="Add" style="position: relative; top: 3px;" src="' . SITE_URL . ELEM_DIR . 'img_admin/plus_black.png"><a href="' . SITE_URL . LANG_CODE . '/admin/pages/add" style="padding-right: 30px;">' . $this->lang->line('add_page') . '</a>' : '');?>
            <img alt="Open" style="position: relative; top: 3px;" src="<?=SITE_URL . ELEM_DIR;?>img_admin/zoom_black.png"><a href="<?=SITE_URL . LANG_CODE;?>/admin/pages">View all pages</a>
          
          </div>
        
        </div>
      
      </div>
      <?php endif; ?>
      
      <?php if(permission_overall('news')): ?>
      <div class="dash_item" style="float: right;">
      
        <div class="pie subheader">
          <div style="position: absolute; top: 16px; left: 12px;" class="sprite sprite-news"></div>
          <h2><?=$this->lang->line('last_edit_news');?></h2>
          
          <div class="options">
          
            <ul>
            
              <li>
                <a class="info" title="<?=$this->lang->line('last_edit_news');?>">
                  <div class="sprite sprite-info"></div>
                </a>
              </li>
              <li>
                <div class="spacer"></div>
              </li>
              <li>
                <a class="toggle_dash min">
                  <div class="sprite sprite-min"></div>
                </a>
              </li>
            
            </ul>
            
          </div>

        </div>
        
        <div class="subcolumn">
        
          <table>
        
            <thead>
            
              <tr>
              
                <th class="text_left" style="width: 35%;"><p style="padding-left: 10px;"><?=$this->lang->line('news_title');?></p></th>
                <th class="text_left" style="width: 20%;">
                  <div class="spacer"></div>
                  <p style="padding-left: 10px;"><?=$this->lang->line('when');?></p>
                </th>
                <th class="text_left" style="width: 20%;">
                  <div class="spacer"></div>
                  <p style="padding-left: 10px;"><?=$this->lang->line('by_user');?></p>
                </th>
              
              </tr>
            
            </thead>
            
            <tbody>
            
            <?php if(!empty($data['pages'])): ?>
            
              <?php $i = 0; ?>
              
              <?php foreach($data['news'] as $news): ?>
              
                <?++$i;?>
              
                <tr>
                  
                  <td class="text_left">
                    
                    <?php if(permission('news', 'edit')):?>
                      <a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/news/edit/' . $news['news_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
                    <?php endif; ?>
                  
                    <span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($news['title'], 30);?></span>

                    <?php if(permission('news', 'edit')):?>
                      </a>
                    <?php endif; ?>
                    
                  </td>
                  
                  <td class="text_left">
                  
                    <div style="margin-top: 15px;" class="spacer"></div>
                    
                    <p style="padding-left: 10px;">
                    
                      <?php if($news['last_update'] != ''): ?>
                      
                        <div style="float: left; padding: 7px 10px 0 10px;"><img alt="edit" src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit_lg.png"></div>
                        <i><?=date('d-m-Y', $news['last_update']);?></i><br>
                        <small><i><?=date('H:i', $news['last_update']);?></i></small>

                      <?php else: ?>
                      
                        <div style="float: left; padding: 7px 10px 0 10px;"><img alt="add" src="<?=SITE_URL . ELEM_DIR;?>img_admin/plus_lg.png"></div>
                        <i><?=date('d-m-Y', $news['date_created']);?></i><br>
                        <small><i><?=date('H:i', $news['date_created']);?></i></small>
                      
                      <?php endif; ?>
                    
                    </p>
                    
                  </td>
                  
                  <td class="text_left">
                  
                    <div class="spacer"></div>
                    
                    <p style="padding-left: 10px;">
                      <i><?=ucfirst($news['edit_by']);?></i>
                    </p>
                  
                  </td>
                
                </tr>
              
              <?php endforeach; ?>
              
            <?php endif; ?>
            
            </tbody>
          
          </table>
          
          <div class="menu" style="padding-left: 10px;">
          
            <?=(permission('news', 'add') ? '<img alt="add" style="position: relative; top: 3px;" src="' . SITE_URL . ELEM_DIR . 'img_admin/plus_black.png"><a href="' . SITE_URL . LANG_CODE . '/admin/news/add" style="padding-right: 30px;">Add news</a>' : '');?>
            <img alt="Open" style="position: relative; top: 3px;" src="<?=SITE_URL . ELEM_DIR;?>img_admin/zoom_black.png"><a href="<?=SITE_URL . LANG_CODE;?>/admin/news">View all news</a>
          
          </div>
        
        </div>
      
      </div>
      <?php endif; ?>
      
      <?php if(permission_overall('references')): ?>
      <div class="dash_item" style="float: right;">
      
        <div class="pie subheader">
          <div style="position: absolute; top: 16px; left: 12px;" class="sprite sprite-references"></div>
          <h2><?=$this->lang->line('last_edit_references');?></h2>
          
          <div class="options">
          
            <ul>
            
              <li>
                <a class="info" title="<?=$this->lang->line('last_edit_references');?>">
                  <div class="sprite sprite-info"></div>
                </a>
              </li>
              <li>
                <div class="spacer"></div>
              </li>
              <li>
                <a class="toggle_dash min">
                  <div class="sprite sprite-min"></div>
                </a>
              </li>
            
            </ul>
            
          </div>

        </div>
        
        <div class="subcolumn">
        
          <table>
        
            <thead>
            
              <tr>
              
                <th class="text_left" style="width: 35%;"><p style="padding-left: 10px;"><?=$this->lang->line('reference_title');?></p></th>
                <th class="text_left" style="width: 20%;">
                  <div class="spacer"></div>
                  <p style="padding-left: 10px;"><?=$this->lang->line('when');?></p>
                </th>
                <th class="text_left" style="width: 20%;">
                  <div class="spacer"></div>
                  <p style="padding-left: 10px;"><?=$this->lang->line('by_user');?></p>
                </th>
              
              </tr>
            
            </thead>
            
            <tbody>
            
            <?php if(isset($data['references'])): ?>
            
              <?php $i = 0; ?>
              
              <?php foreach($data['references']['add'] as $reference): ?>
              
                <?++$i;?>
              
                <tr>
                  
                  <td class="text_left">
                    
                    <?php if(permission('references', 'edit')):?>
                      <a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/references/edit/' . $reference['reference_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
                    <?php endif; ?>
                  
                    <span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($reference['title'], 30);?></span>

                    <?php if(permission('references', 'edit')):?>
                      </a>
                    <?php endif; ?>
                    
                  </td>
                  
                  <td class="text_left">
                  
                    <div style="margin-top: 15px;" class="spacer"></div>
                    
                    <p style="padding-left: 10px;">
                    
                      <?php if($reference['last_update'] != ''): ?>
                      
                        <div style="float: left; padding: 7px 10px 0 10px;"><img alt="edit" src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit_lg.png"></div>
                        <i><?=date('d-m-Y', $reference['last_update']);?></i><br>
                        <small><i><?=date('H:i', $reference['last_update']);?></i></small>

                      <?php else: ?>
                      
                        <div style="float: left; padding: 7px 10px 0 10px;"><img alt="add" src="<?=SITE_URL . ELEM_DIR;?>img_admin/plus_lg.png"></div>
                        <i><?=date('d-m-Y', $reference['date_created']);?></i><br>
                        <small><i><?=date('H:i', $reference['date_created']);?></i></small>
                      
                      <?php endif; ?>
                    
                    </p>
                    
                  </td>
                  
                  <td class="text_left">
                  
                    <div class="spacer"></div>
                    
                    <p style="padding-left: 10px;">
                      <i><?=ucfirst($reference['edit_by']);?></i>
                    </p>
                  
                  </td>
                
                </tr>
              
              <?php endforeach; ?>
              
              <?php foreach($data['references']['edit'] as $reference): ?>
              
                <?++$i;?>
              
                <tr>
                  
                  <td class="text_left">
                    
                    <?php if(permission('references', 'edit')):?>
                      <a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/references/edit/' . $reference['reference_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
                    <?php endif; ?>
                  
                    <span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($reference['title'], 30);?></span>

                    <?php if(permission('references', 'edit')):?>
                      </a>
                    <?php endif; ?>
                    
                  </td>
                  
                  <td class="text_left">
                  
                    <div style="margin-top: 15px;" class="spacer"></div>
                    
                    <p style="padding-left: 10px;">
                    
                      <?php if($reference['last_update'] != ''): ?>
                      
                        <div style="float: left; padding: 7px 10px 0 10px;"><img alt="edit" src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit_lg.png"></div>
                        <i><?=date('d-m-Y', $reference['last_update']);?></i><br>
                        <small><i><?=date('H:i', $reference['last_update']);?></i></small>

                      <?php else: ?>
                      
                        <div style="float: left; padding: 7px 10px 0 10px;"><img alt="add" src="<?=SITE_URL . ELEM_DIR;?>img_admin/plus_lg.png"></div>
                        <i><?=date('d-m-Y', $reference['date_created']);?></i><br>
                        <small><i><?=date('H:i', $reference['date_created']);?></i></small>
                      
                      <?php endif; ?>
                    
                    </p>
                    
                  </td>
                  
                  <td class="text_left">
                  
                    <div class="spacer"></div>
                    
                    <p style="padding-left: 10px;">
                      <i><?=ucfirst($reference['edit_by']);?></i>
                    </p>
                  
                  </td>
                
                </tr>
              
              <?php endforeach; ?>
              
            <?php endif; ?>
            
            </tbody>
          
          </table>
          
          <div class="menu" style="padding-left: 10px;">
          
            <?=(permission('references', 'add') ? '<img alt="Add" style="position: relative; top: 3px;" src="' . SITE_URL . ELEM_DIR . 'img_admin/plus_black.png"><a href="' . SITE_URL . LANG_CODE . '/admin/references/add" style="padding-right: 30px;">' . $this->lang->line('add_reference') . '</a>' : '');?>
            <img alt="Open" style="position: relative; top: 3px;" src="<?=SITE_URL . ELEM_DIR;?>img_admin/zoom_black.png"><a href="<?=SITE_URL . LANG_CODE;?>/admin/references">View all references</a>
          
          </div>
        
        </div>
      
      </div>
      <?php endif; ?>
      
      <?php if(permission_overall('photoalbums')): ?>
      <div class="dash_item">
      
        <div class="pie subheader">
          <div style="position: absolute; top: 15px; left: 12px;" class="sprite sprite-photoalbums"></div>
          <h2><?=$this->lang->line('last_edit_photoalbum');?></h2>
          
          <div class="options">
          
            <ul>
            
              <li>
                <a class="info">
                  <div class="sprite sprite-info"></div>
                </a>
              </li>
              <li>
                <div class="spacer"></div>
              </li>
              <li>
                <a class="toggle_dash min">
                  <div class="sprite sprite-min"></div>
                </a>
              </li>
            
            </ul>
            
          </div>

        </div>
        
        <div style="background-color: #f1f1f1;" class="subcolumn">
        
        <?php if(isset($data['photoalbums'])): ?>
        
          <div style="background-color: #FC6B00; color: #F9F9F9; height: 40px; line-height: 40px; padding-left: 10px;">
            <b><?=$data['photoalbums']['title'];?></b>
          </div>
        
          <?php if(!empty($data['photoalbums']['media'])): ?>
        
            <?php foreach($data['photoalbums']['media'] as $media): ?>
            
              <div class="thumb">
                <img src="<?=SITE_URL . ELEM_DIR;?>media/photoalbums/thumb/<?=$media['filename'];?>">
              </div>
            
            <?php endforeach; ?>
        
          <?php endif; ?>
          
          <div class="clear"></div>
        
          <div style="margin-top: 10px; padding: 5px 0 5px 10px; height: 40px; line-height: 40px; background-color: #eaeaea;">
            <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/edit_lg.png" alt="Edit">
            <i style="color: #939393;">
              editat la 
              <span style="color: #1e1e1e;">
                <?=($data['photoalbums']['last_update'] != '' ? date('d-m-Y', $data['photoalbums']['last_update']) : date('d-m-Y', $data['photoalbums']['date_created']));?>, <?=($data['photoalbums']['last_update'] != '' ? date('H:i', $data['photoalbums']['last_update']) : date('H:i', $data['photoalbums']['date_created']));?>;
              </span>
              de 
              <span style="color: #1e1e1e;">
                <?=ucfirst($data['photoalbums']['edit_by']);?>
              </span>
            </i>
          </div>
          
          <div class="menu" style="padding-left: 10px;">
          
            <?=(permission('photoalbums', 'add') ? '<img alt="Add" style="position: relative; top: 3px;" src="' . SITE_URL . ELEM_DIR . 'img_admin/plus_black.png"><a href="' . SITE_URL . LANG_CODE . '/admin/photoalbums/add" style="padding-right: 30px;">Add new photo album</a>' : '');?>
            <img alt="Open" style="position: relative; top: 3px;" src="<?=SITE_URL . ELEM_DIR;?>img_admin/zoom_black.png"><a href="<?=SITE_URL . LANG_CODE;?>/admin/photoalbums">See all photo albums</a>
          
          </div>
        
        <?php endif; ?>
        
        </div>
      
      </div>
      <?php endif; ?>
      
    </div>
  
  </div>
  
</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>