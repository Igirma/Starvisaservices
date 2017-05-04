<?php require_once ELEM_DIR . 'admin_header.php'; ?>

<div id="container">

  <div id="overview">
    
    <div class="orange_button">
      <div class="orange_button_left"></div>
      <div class="orange_button_con">
        <img src="<?=SITE_URL . ELEM_DIR?>img_admin/plus.png" />
        <div class="orange_button_space"></div>
        <a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER?>/add">Add</a>
      </div>
      <div class="orange_button_right"></div>
    </div>
	
	<div class="orange_button">
      <input id="search" type="text" style="height: 30px" placeholder="Search..." autocomplete="off">
    </div>
    
    <div class="clear"></div>
    
    <div class="column">

      <div class="pie header">
        <div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-projects"></div>
        <h1>Documents overview</h1>
      
      </div>
    
      <form method="post" action="<?=$this->url->current;?>">

        <table id="table">
        
          <thead>

            <tr>

              <th style="width: 3%;">
                Nr
              </th>
              <th class="text_left" style="width: 47%;">
                <div class="spacer"></div>
                <p style="padding-left: 10px;">Title</p>
              </th>
              <th style="width: 10%;">
                <div class="spacer"></div>
                <p>Order</p>
              </th>
              <th style="width: 10%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('active');?></p>
              </th>
              <th style="width: 10%;">
                <div class="spacer"></div>
                <p>Delete</p>
              </th>

            </tr>

          </thead>

          <tbody>
          
          <?php if(isset($data['documents']) && $data['documents'] !== false): ?>
            
            <?php $x = 0; ?>
            <?php $count_items = count($data['documents']); ?>
            
            <?php foreach ($data['documents'] as $document): ?>
            
            
            
            <tr>
              
              <td>
                <?php $x++; ?>
                <?=$x?>
              </td>
              
              <td class="text_left">
                
                <?php if(permission(CONTROLLER, 'edit')):?>
                  <a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $document['users_document_id'] . '/' . $this->config->item('default_language')?>" style="display:block;">
                <?php endif; ?>
                
                <div class="spacer"></div>
              
                <span style="line-height: 40px; padding-left: 10px;">
					<?=str_shorten($document['users_document_title'], 60);?> 
					<?php if($document_subtitle = str_shorten($document['users_document_subtitle'], 60)):?>
						<em>(<?=$document_subtitle?>)</em>
					<?php endif; ?>
				</span>

                <?php if(permission(CONTROLLER, 'edit')):?>
                  </a>
                <?php endif; ?>

              </td>
              
              <td class="order">
              
                <div class="spacer"></div>
                
                <p>
                
                  <div class="up_down">
                  
                    <?php if(permission(CONTROLLER, 'edit')): ?>
                    
                      <?php if($x == 1 && $x != $count_items): ?>

                      <a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$document['users_document_order'];?>/<?=$document['users_document_id'];?>">
                        <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/down.png">
                      </a>
                      
                      <?php endif; ?>
                      
                      <?php if($x != 1 && $x == $count_items): ?>
                      
                      <a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$document['users_document_order'];?>/<?=$document['users_document_id'];?>">
                        <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
                      </a>
                      
                      <?php endif; ?>
                      
                      <?php if($x != 1 && $x != $count_items): ?>
                      
                      <a title="<?=$this->lang->line('up')?>" class="up no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/up/<?=$document['users_document_order'];?>/<?=$document['users_document_id'];?>">
                        <img src="<?=SITE_URL . ELEM_DIR;?>/img_admin/up.png">
                      </a>
                      <a title="<?=$this->lang->line('down')?>" class="down no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/order/down/<?=$document['users_document_order'];?>/<?=$document['users_document_id'];?>">
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
              
                <?php if(permission(CONTROLLER, 'edit')): ?>
                
                  <?php if($document['users_document_active'] == 1): ?>
                  
                  <input type="radio" name="active[<?=$document['users_document_id'];?>]" checked="checked" value="1">Yes
                  <input type="radio" name="active[<?=$document['users_document_id'];?>]" value="0">No

                  <?php else: ?>
                  
                  <input type="radio" name="active[<?=$document['users_document_id'];?>]" value="1">Yes
                  <input type="radio" name="active[<?=$document['users_document_id'];?>]" checked="checked" value="0">No
                  
                  <?php endif; ?>
                
                <?php endif; ?>
                
                </p>
              
              </td>
              
              <td>

                <div class="spacer"></div>
                <p><?=(permission(CONTROLLER, 'delete') ? '<a title="Delete" class="delete" href="' . SITE_URL . 'admin/' . CONTROLLER . '/delete/' . $document['users_document_id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?></p>

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

<script type="text/javascript">
	var $searchRows = $('#table > tbody > tr');
	$('#search').on("keyup", function() {
		var searchTerm = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();
		
		$searchRows.show().filter(function() {
			var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
			return !~text.indexOf(searchTerm);
		}).hide();
	});
</script>
<?php require_once ELEM_DIR . 'admin_footer.php'; ?>