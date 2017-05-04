<script type="text/javascript">
$(document).ready(function(){

	$('#crop_container').find('img').Jcrop({
		boxWidth: 800,
		boxHeight: ($(window).height() - 450),
		allowSelect: false,
		bgOpacity: 0.5,
		bgColor: 'white',
		addClass: 'jcrop-light',
		aspectRatio: MIN_IMG_W / MIN_IMG_H,
		minSize: [ MIN_IMG_W, MIN_IMG_H ],
		//maxSize: [ MAX_IMG_W, MAX_IMG_H ],
		onSelect: setCoords
	},function(){
		api = this;
		api.setSelect([100, 100, 0, 0]);
		api.setOptions({ bgFade: true });
		api.ui.selection.addClass('jcrop-selection');
	  });

	$('input[name="crop_image"]').click(function(){
		coords = api.coords;
		
		filename = $(this).attr('id');
		
		$.ajax({
			type: 'POST',
			data: 'ajax=1&x=' + coords['x'] + '&y=' + coords['x2'] + '&x2=' + coords['y'] + '&y2=' + coords['y2'] + '&h=' + coords['h'] + '&w=' + coords['w'] + '&filename=' + filename,
			url: window.location,
			success: function(){
				noty({animateOpen: {opacity: 'show'}, animateClose: {opacity: 'hide'}, type: 'success', layout: 'center', text: 'Uw afbeelding is bijgesneden', timeout: '3000'});
			}
		});
	});
	
	$('input[name="save_media_info"]').click(function(){
	
		$.ajax({
			url: SITE_URL + 'admin/save_media_info',
			type: 'POST',
			data: $('form#save_media_info').serialize(),
			success: function(){
				$('input[name="upload"]').click()
			}
		});
	});
});

</script>

<div id="crop_container">

	<form action="<?=$this->url->current;?>" method="post" id="save_media_info">

		<div id="edit_<?=$data['media_id'];?>" class="modal_window">
		
			<input type="hidden" name="media_id" value="<?=$data['media_id'];?>">
			<input type="hidden" name="language_id" value="<?=$data['language_id'];?>">
			
			<img src="<?=SITE_URL . ELEM_DIR . 'media/' . $data['controller'] . '/crop_original/' . $data['filename'];?>" alt="<?=$data['alt'];?>"><br>
			
			<input class="crop_image" type="button" name="crop_image" value="<?=$this->lang->line('crop_image');?>" id="<?=$data['filename'];?>">
			<input class="save_media_info" type="button" name="save_media_info" value="<?=$this->lang->line('save');?>" id="<?=$data['media_id'];?>">
			
			<div class="pie header">
				<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-orange"></div>
				<h1><?=$this->lang->line('image_info');?></h1>
			</div>
			
			<table style="width: 100%;">
				<tr>
					<th><?=$this->lang->line('picture_title');?></th>
					<td><input type="text" name="title" value="<?=$data['title'];?>"></td>
				</tr>
				
				<tr>
					<th><?=$this->lang->line('picture_alt');?></th>
					<td><input type="text" name="alt" value="<?=$data['alt'];?>"></td>
				</tr>
				
				<tr>
					<th><?=$this->lang->line('picture_description');?></th>
					<td><input type="text" name="description" value="<?=$data['description'];?>"></td>
				</tr>
				
				<tr>
					<th><?=$this->lang->line('picture_content');?></th>
					<td><input type="text" name="content" value="<?=$data['content'];?>"></td>
				</tr>
			</table>
		
		</div>
		
	</form>

</div>