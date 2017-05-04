<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
  $data['events'] = $_POST['events'];
  $data['events']['date'] = strtotime($_POST['events']['date']);
}

?>
<?=validation_errors();?>

<div id="container">

  <div id="details">
  
    <form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="events_edit_add">
    
      <input type="hidden" name="events[event_id]" value="<?=(isset($data['events']['event_id']) && $data['events']['event_id'] != '' ? $data['events']['event_id'] : '');?>">

      <div class="menu_float">

        <input class="save" type="submit" name="save" value="<?=$this->lang->line('menu_save')?>">

        <input class="save_and_back" type="submit" name="save_and_back" value="<?=$this->lang->line('menu_save_and_back')?>">
        
        <div class="back_button">
          <a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER;?>" title="<?=$this->lang->line('back_to_overview')?>"><?=$this->lang->line('back_to_overview')?></a>
        </div>

      </div>
      
      <div class="column">
        
        <?php if($this->url->segment(2) == 'add'): ?>

          <div class="header">
            <div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-events"></div>
            <h1><?=$this->lang->line('header_edit');?></h1>
          
          </div>

        <?php endif; ?>
        
        <?php if($this->url->segment(2) == 'edit'): ?>

            <div class="header">
              <input type="hidden" name="events[language][code]" value="<?=$data['events']['language']['code']?>" />
              <div style="position: absolute; left: 11px; top: 15px;" class="language sprite_<?=$data['events']['language']['code']?>"></div>
              <h1><?=$this->lang->line('header_add');?></h1>
              
              <div class="add_edit_languages">
                
                <?php foreach($data['languages'] as $language): ?>

                    <?php if(permission(CONTROLLER, 'edit', $language['language_id'])): ?>
                  
                    <a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$data['events']['event_id']?>/<?=$language['language_id'];?>">
                      <span class="padding_extra_languages language sprite_<?=$language['code']?>"></span>
                    </a>
                    
                    <?php endif; ?>

                <?php endforeach; ?>
                
              </div>
              
            </div>

        <?php endif; ?>
      
      </div>
      
      <div class="column" style="padding-bottom: 20px;">
      
        <div class="subheader">

          <h2><?=$this->lang->line('events_info');?></h2>
          
          <div class="options">
          
            <ul>
            
              <li>
                <a class="info" title="Info">
                  <div style="position: relative; top: 15px;" class="sprite sprite-info"></div>
                </a>
              </li>
              <li>
                <div class="spacer"></div>
              </li>
              <li>
                <a class="toggle min">
                  <div style="position: relative; top: 15px; left: -1px;" class="sprite sprite-min"></div>
                </a>
              </li>
            
            </ul>
            
          </div>
        
        </div>
        
        <div class="subcolumn">
        
          <table>

            <tr>
              <th><?=$this->lang->line('title');?></th>
              <td><input type="text" name="events[title]" value="<?=(isset($data['events']['title']) ? $data['events']['title'] : '');?>"></td>
            </tr>
          
            <tr>
              <th><?=$this->lang->line('sub_active');?></th>
              <td>
                <?php if(isset($data['events']['sub_active'])): ?>
                
                  <?php if($data['events']['sub_active'] == 1): ?>
                  
                    <input type="radio" name="events[sub_active]" value="1" checked="checked"><?=$this->lang->line('yes')?>
                    <input type="radio" name="events[sub_active]" value="0"><?=$this->lang->line('no')?>
                  
                  <?php else: ?>
                  
                    <input type="radio" name="events[sub_active]" value="1"><?=$this->lang->line('yes')?>
                    <input type="radio" name="events[sub_active]" value="0" checked="checked"><?=$this->lang->line('no')?>
                  
                  <?php endif; ?>
                  
                <?php else: ?>
                
                  <input type="radio" name="events[sub_active]" value="1" checked="checked"><?=$this->lang->line('yes')?>
                  <input type="radio" name="events[sub_active]" value="0"><?=$this->lang->line('no')?>
                
                <?php endif; ?>
              </td>
            </tr>

            <tr>
              <th><?=$this->lang->line('date');?></th>
              <td><input class="datepicker" readonly="readonly" type="text" name="events[date]" value="<?=(isset($data['events']['date']) ? date('d-m-Y', $data['events']['date']) : date('d-m-Y', time()));?>"></td>
            </tr>
            
            <tr class="hidden">
              <th><?=$this->lang->line('time');?></th>
              <td><input type="text" name="events[time]" value="<?=(isset($data['events']['time']) ? $data['events']['time'] : '');?>"></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('description');?></th>
              <td><textarea name="events[description]"><?=(isset($data['events']['description']) ? $data['events']['description'] : '');?></textarea></td>
            </tr>

            <tr>
              <th><?=$this->lang->line('content');?></th>
              <td><textarea class="editor" name="events[content]"><?=(isset($data['events']['content']) ? $data['events']['content'] : '');?></textarea></td>
            </tr>
            
            <tr>
              <th><?=$this->lang->line('slug');?></th>
              <td><input type="text" name="events[slug]" value="<?=(isset($data['events']['slug']) ? $data['events']['slug'] : '');?>"></td>
            </tr>
            
          </table>
        
        </div>

      </div>

    </form>
    
    
  </div>

</div>


<?php require_once ELEM_DIR . 'admin_footer.php'; ?>