<?php require_once ELEM_DIR . 'admin_header.php'; 

if(!empty($_POST))
{
  $data['document'] = $_POST['document'];
}

?>
<?=validation_errors();?>

<div id="container">

  <div id="details">
  
    <form method="post" action="<?=$this->url->current;?>" enctype="multipart/form-data" id="projects_edit_add">
    
      <input type="hidden" name="document[users_document_id]" value="<?=(isset($data['document']['users_document_id']) && $data['document']['users_document_id'] != '' ? $data['document']['users_document_id'] : '');?>">

      <div class="menu_float">

        <input class="save" type="submit" name="save" value="<?=$this->lang->line('menu_save')?>">

        <input class="save_and_back" type="submit" name="save_and_back" value="<?=$this->lang->line('menu_save_and_back')?>">
        
        <div class="back_button">
          <a href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER;?>" title="<?=$this->lang->line('back_to_overview')?>"><?=$this->lang->line('back_to_overview')?></a>
        </div>

      </div>
      
      <div class="column">
        
        <?php if($this->url->segment(2) == 'add') { ?>

          <div class="header">
            <div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-projects"></div>
            <h1><?=$this->lang->line('header_edit');?></h1>
          
          </div>

        <?php } ?>
        
        <?php if($this->url->segment(2) == 'edit'){ ?>

            <div class="header">
              <input type="hidden" name="document[language][code]" value="en" />
              <div style="position: absolute; left: 11px; top: 15px;" class="language sprite_en"></div>
              <h1><?=$this->lang->line('header_add');?></h1>
              
              <div class="add_edit_languages">
                
                <?php foreach($data['languages'] as $language) { ?>

                    <?php if(permission(CONTROLLER, 'edit', $language['language_id'])) { ?>
                    <a class="no_underline" href="<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/edit/<?=$data['document']['users_document_id']?>/<?=$language['language_id'];?>">
                      <span class="padding_extra_languages language sprite_<?=$language['code']?>"></span>
                    </a>
                    <?php } ?>

                <?php } ?>
                
              </div>
              
            </div>

        <?php } ?>
      
      </div>
      
      <div class="column">
      
        <div class="subheader">

          <h2>Document information</h2>
          
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
              <th>Title</th>
              <td><input type="text" name="document[users_document_title]" value="<?=(isset($data['document']['users_document_title']) ? $data['document']['users_document_title'] : '');?>"></td>
            </tr>
            <tr>
              <th>Subtitle</th>
              <td><input type="text" name="document[users_document_subtitle]" value="<?=(isset($data['document']['users_document_subtitle']) ? $data['document']['users_document_subtitle'] : '');?>"></td>
            </tr>
            <tr>
              <th>Content</th>
              <td style="padding-top: 5px; padding-bottom: 10px;"><textarea class="editor" name="document[users_document_content]" id="targetBox"><?=(isset($data['document']['users_document_content']) ? $data['document']['users_document_content'] : '');?></textarea></td>
            </tr>
            
            <!--
            <tr>
              <th>Countries</th>
              <td>
                <?php if (isset($data['countries']) && $data['countries'] !== false) { ?>
                <?php $countries_exist = (isset($data['document']['countries']) && $data['document']['countries'] !== false); ?>
                <table class="borderless multiple-select-parent countries-list-parent">
                  <tr>
                    <td style="width: 50%;">
                    <select name="countries_list" size="20" class="multiple-select countries_list" multiple>
                    <?php if (!$countries_exist) { ?>
                    <?php foreach ($data['countries'] as $country) { ?>
                      <option value="<?php echo $country['users_country_id']; ?>"><?php echo $country['users_country_name']; ?></option>
                    <?php } ?>
                    <?php } elseif (isset($data['document']['countries_selected'])) { ?>
                    <?php foreach ($data['countries'] as $country) { ?>
                    <?php if (!in_array($country['users_country_id'], $data['document']['countries_selected'])) { ?>
                      <option value="<?php echo $country['users_country_id']; ?>"><?php echo $country['users_country_name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                    <?php } ?>
                    </select>
                    </td>
                    <td style="width: 50%;">
                    <select name="document[countries][]" size="20" class="multiple-select countries_target" multiple>
                    <?php if ($countries_exist) { ?>
                    <?php foreach ($data['document']['countries'] as $users_country_id => $users_country_name) { ?> 
                      <option value="<?php echo $users_country_id; ?>" selected="selected"><?php echo $users_country_name; ?></option>
                    <?php } ?>
                    <?php } ?>
                    </select>
                    </td>
                  </tr>
                </table>
                <?php } ?>
              </td>
            </tr>
            
            <tr>
              <th>Country groups</th>
              <td>
                <?php if (isset($data['groups']) && $data['groups'] !== false) { ?>
                <?php $groups_exist = (isset($data['document']['groups']) && $data['document']['groups'] !== false); ?>
                <table class="borderless multiple-select-parent groups-list-parent">
                  <tr>
                    <td style="width: 50%;">
                    <select name="groups_list" size="20" class="multiple-select groups_list" multiple>
                    <?php if (!$groups_exist) { ?>
                    <?php foreach ($data['groups'] as $group) { ?>
                      <option value="<?php echo $group['user_country_group_id']; ?>"><?php echo $group['user_country_group_name']; ?></option>
                    <?php } ?>
                    <?php } elseif (isset($data['document']['groups_selected'])) { ?>
                    <?php foreach ($data['groups'] as $group) { ?>
                    <?php if (!in_array($group['user_country_group_id'], $data['document']['groups_selected'])) { ?>
                      <option value="<?php echo $group['user_country_group_id']; ?>"><?php echo $group['user_country_group_name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                    <?php } ?>
                    </select>
                    </td>
                    <td style="width: 50%;">
                    <select name="document[groups][]" size="20" class="multiple-select groups_target" multiple>
                    <?php if ($groups_exist) { ?>
                    <?php foreach ($data['document']['groups'] as $user_country_group_id => $user_country_group_name) { ?> 
                      <option value="<?php echo $user_country_group_id; ?>" selected="selected"><?php echo $user_country_group_name; ?></option>
                    <?php } ?>
                    <?php } ?>
                    </select>
                    </td>
                  </tr>
                </table>
                <?php } ?>
              </td>
            </tr>
            -->

          </table>
        
        </div>
        
      </div>
	  
	  <div class="column">
      
        <div class="subheader">
			<?php
				include "../models/documents_model.php";
				$model = new documents_model;
				$data['page']['docs'] = $model->fetch_media();
				if($this->url->segment(4) == "media_delete") {
					// en/admin/documents/edit/[PAGE ID]/media_delete/[PAGE ID]/[FILE ID]
					$model->delete_media($this->url->segment(5));
				}
			?>
          <h2><?=$this->lang->line('documents');?></h2>
          
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
        
          <?php if(!empty($data['page']['docs'])): ?>
          
            <div class="docs">
            
              <ul>

            <?php foreach($data['page']['docs'] as $doc): ?>
            
              <?php $ext = @end(explode('.', $doc['filename'])); ?>
              
                <li style="display: block; width: 95%;">	
                  <a href="<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER;?>/docs/<?=$doc['filename'];?>"><?=str_shorten($doc['filename'], 50);?>
                    <div style="position: absolute; top: 0; left: 0;" class="sprite_ex sprite-<?=$ext;?>"></div>
                  </a>
                  <a class="delete" style="position: absolute; right: 1px; top: 7px;" href="<?=SITE_URL . 'admin/' . CONTROLLER . '/delete_media/' . $doc['media_id'] . '/' . $doc['table_id'] . '/' . $this->url->segment(4);?>">
                    <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/delete.png">
                  </a>
				  <a class="delete" style="position: absolute; right: 47px; top: 7px;" href="javascript:void(0)" onClick="applyLinkToSelection('<?=SITE_URL . ELEM_DIR . 'media/' . CONTROLLER;?>/docs/<?=$doc['filename'];?>')">
                    <img src="<?=SITE_URL . ELEM_DIR;?>img_admin/plus_black.png">
                  </a>
                </li>

            <?php endforeach; ?>

              </ul>						
            
            </div>
            
          <?php endif; ?>
        
          <table>
            
            <tr>
            
              <th>
                <?=$this->lang->line('new_document');?>
              </th>
              <td>
                <div class="file_container">
                  <input type="text" readonly="readonly" class="file_replace_text">
                  <input type="button" class="file_replace" value="<?=$this->lang->line('browse');?>">
                  <input type="submit" name="save" value="<?=$this->lang->line('upload');?>">
                  <input type="file" name="docs[]" multiple="multiple" class="file_hack">
                </div>
              
              </td>
            
            </tr>
          
          </table>
        
        </div>
        
      </div>

</div>
<script type="text/javascript">
	function applyLinkToSelection(linkString) {
		var textComponent = document.getElementById('targetBox');
		var selectedText;
		var appendText;
		if(typeof(document.selection) != 'undefined')
		{
			textComponent.focus();
			var sel = document.selection.createRange();
			selectedText = sel.text;
		}
		
		else if(typeof(textComponent.selectionStart) != 'undefined')
		{
			var startPos = textComponent.selectionStart;
			var endPos = textComponent.selectionEnd;
			selectedText = textComponent.value.substring(startPos, endPos);
			appendText = '<a href="' + linkString + '">' + selectedText + '</a>';  
			textComponent.value = textComponent.value.substring(0, startPos) + appendText + textComponent.value.substring(endPos, textComponent.value.length);
		}
		alert("Selected Text: " + selectedText + " @ Link Add: " + linkString);
	}
</script>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>