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
        <h1>Destinations overview</h1>
      
      </div>
    
      <form method="post" action="<?=$this->url->current;?>">

        <table>
        
          <thead>
          
            <tr>
              
              <th style="width: 3%;">
                Nr
              </th>
              <th class="text_left" style="width: 25%;">
                <div class="spacer"></div>
                <p style="padding-left: 10px;">Destination country/group</p>
              </th>
              <th class="text_left" style="width: 25%;">
                <div class="spacer"></div>
                <p style="padding-left: 10px;">Nationality country/group</p>
              </th>
              <th style="width: 20%;">
                <div class="spacer"></div>
                <p>Nationality/group status</p>
              </th>
              <th style="width: 17%;">
                <div class="spacer"></div>
                <p>Country/group status</p>
              </th>
              <th style="width: 10%;">
                <div class="spacer"></div>
                <p><?=$this->lang->line('delete');?></p>
              </th>
            
            </tr>
          
          </thead>
        
          <tbody>
		  
				<?php $group_id = 1; ?>
				<?php if(isset($data['groupped_destinations']) && $data['groupped_destinations'] !== false): ?>
					<?php foreach($data['groupped_destinations'] as $group): ?>
						<tr onClick="toggleGroup(this, <?=$group_id;?>)" style="font-weight: bold">
							<td><strong><?=$group_id;?></strong></td>
							<td class="text_left"><div class="spacer"></div><span style="line-height: 40px; padding-left: 10px;"><?=$group[0][0];?></span></td>
							<td class="text_left"><div class="spacer"></div><span style="line-height: 40px; padding-left: 10px;"><?=count($group) - 1;?> Option<?=(count($group) - 1 <= 1 ? '' : 's');?></span></td>
							<td><div class="spacer"></div><span style="line-height: 40px;"><?=$group[0][1];?>/<?=count($group) - 1;?> Active</span></td>
							<td><div class="spacer"></div><span style="line-height: 40px;"><?=$group[0][2];?></span></td>
							<td><div class="spacer"></div></td>
						</tr>
						<?php unset($group[0]); ?>
						<?php $x = 1; ?>
						<?php for($i = 1; $i <= count($group); $i++): ?>
							<tr class="group<?=$group_id?>" style="display: none">
						  
							  <td>
								<?=$x++?>
							  </td>
							  
							  <td class="text_left">
								
								<?php if(permission(CONTROLLER, 'edit')):?>
								  <a title="<?=$this->lang->line('edit')?>" href="<?=SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $data['destinations'][$group[$i]]['id'];?>" style="display:block;">
								<?php endif; ?>
								
								<div class="spacer"></div>
							  
								<span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($data['destinations'][$group[$i]]['name'], 100);?> <span class="datatype">(<?=$data['destinations'][$group[$i]]['group_type'];?>)</span></span>

								<?php if(permission(CONTROLLER, 'edit')):?>
								  </a>
								<?php endif; ?>

							  </td>
				  
							  <td class="text_left">

								<div class="spacer"></div>
								
								<span style="line-height: 40px; padding-left: 10px;"><?=$data['destinations'][$group[$i]]['nationality'];?> <span class="datatype">(<?=$data['destinations'][$group[$i]]['nationality_type'];?>)</span></span>

							  </td>

							  <td>

								<div class="spacer"></div>
								<span style="line-height: 40px;"><?=$data['destinations'][$group[$i]]['nationality_active'];?></span>

							  </td>

							  <td>
								
								<div class="spacer"></div>
								<span style="line-height: 40px; padding-left: 10px; padding-right: 10px;"><?php echo $data['destinations'][$group[$i]]['destination_active']; ?></span>

							  </td>

							  <td>
								<div class="spacer"></div>
								
								<p>
								
								  <?=(permission(CONTROLLER, 'delete') ? '<a title="' . $this->lang->line('delete') . '" class="delete" href="' . SITE_URL . 'admin/' . CONTROLLER . '/delete/' . $data['destinations'][$group[$i]]['id'] . '"><img src="' . SITE_URL . ELEM_DIR . 'img_admin/delete.png"></a>' : '');?>
								  
								</p>
							  </td>
							
							</tr>
							<?php // unset($data['destinations'][$i]); ?>
						<?php endfor; ?>
						</div>
						<?php $group_id ++; ?>
					<?php endforeach; ?>
				<?php endif; ?>
          
          </tbody>
        
        </table>

      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
	function toggleGroup(eventHandler, groupId) {
		if(typeof(groupId) == undefined) {
			return false;
		}
		else {
			if($(eventHandler).css("background-color") == "rgb(225, 225, 225)")
				$(eventHandler).css("background-color", "");
			else
				$(eventHandler).css("background-color", "#E1E1E1");
			$.each($(".group" + groupId), function(k, v) {
				$(".group" + groupId).eq(k).toggle();
			});
			return true;
		}
	}
</script>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>