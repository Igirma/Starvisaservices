<?php require_once ELEM_DIR . 'admin_header.php'; 

//debug($data['graph_data']);
?>
<script type="text/javascript">
$(document).ready(function(){
  <?php
  $dates = '';
  if(isset($data['graph_data']) && count($data['graph_data']) > 0 && $data['graph_data']){
	foreach($data['graph_data'] as $day => $value){
		if($dates != '') $dates .= ", ";
		//$dates .= "['".$data['year']." ".$data['month']." ".$day."',".$value.",".(($value > 0)?"'".$value."'":"")."]";
		$dates .= "['".$data['year']." ".$data['month']." ".$day."',".$value.",".(($value > 0)?"'".$value."'":"")."]";
	}
  }
  ?>
  var line1=[<?php echo $dates;?>];
  var plot1 = $.jqplot('chart1', [line1], {
    title:'',
    axes:{
        xaxis:{
          renderer:$.jqplot.DateAxisRenderer,
          tickOptions:{
            formatString:'%b&nbsp;%#d'
          } 
        },
        yaxis:{
           min: 0,
		   tickOptions:{
            formatString:'&euro;%.0f'
            }
        }
    },
    series:[{lineWidth:2, color:'#FE7700'}],
	seriesDefaults: {
      showMarker:false, 
      pointLabels:{ show:false, ypadding:3 }
    }
  });
});
</script>
<div id="container">

	<div id="overview">
	
		<div class="column">
		
			<div class="pie header">
				<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-orange"></div>
				<h1><?=$this->lang->line('overview_header');?></h1>
				<?php
					if(isset($data['drop_down_date']) && $data['drop_down_date']){
						?>
						<select name='month' style='position: absolute; right: 10px; top: 11px;' onchange="window.location='<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/month/'+$(this).val();">
							<?php
							$year = date("Y", $data['drop_down_date']);
							$month = date("n", $data['drop_down_date']);
							$current_month = date('n');
							$current_year = date("Y");
							for($j = $current_year; $j >= $year; $j--){					
								if($j == $year) $current_month = date('n');
								else $current_month = 12;
								if($j == $year) $min_month = $month;
								else $min_month = 1;
								for($i = $current_month; $i >= $min_month; $i--){
									?>
									<option value='<?php echo $j.'-'.$i;?>' <?php echo (($j==$data['year'] && $i==$data['month'])?"selected":"");?> onclick="window.location='<?=SITE_URL . LANG_CODE;?>/admin/<?=CONTROLLER?>/month/'+$(this).val();"><?php echo $this->lang->line('month_'.$i)." ".$j;?></option>
									<?php
								}
							}
							?>
						</select>
						<?php					
				}
				?>				
			</div>

			<div class='graph'>
				<div id="chart1" style="height:300px; width:650px; margin: 0 auto; padding-bottom: 10px;"></div>
			</div>
			
			<table>
				
				<tr>
						
						<td>
							<b><?=$this->lang->line('total');?></b><br>
							<b class='orange_text'>&euro;  <?php echo formatPrice($data['order_total']);?></b>
						</td>
						<td>
							<b><?=$this->lang->line('vat');?></b><br>
							<b class='orange_text'>&euro;  <?php echo formatPrice($data['vat_total']);?></b>
						</td>
						<td>
							<b><?=$this->lang->line('transport');?></b><br>
							<b class='orange_text'>&euro;  <?php echo formatPrice($data['transport_total']);?></b>
						</td>
						
				</tr>
					
			</table>
		
		</div>
		
		<?php if(isset($data['products']) && $data['products'] && count($data['products']) > 0): ?>
		<div class="column">
		
			<div class="pie header">
				<div style="position: absolute; left: 11px; top: 12px;" class="sprite sprite-orange"></div>
				<h1><?=$this->lang->line('overview_products_header');?></h1>				
			</div>
				
				<table>
				
					<thead>
					
						<tr>
						
							<th style="width: 3%;">Nr</th>
							<th class="text_left" style="width: 27%;">
								<div class="spacer"></div>
								<p style="padding-left: 10px;"><?=$this->lang->line('name');?></p>
							</th>
							<th class="text_left" style="width: 20%;">
								<div class="spacer"></div>
								<p style="padding-left: 10px;"><?=$this->lang->line('articlenumber');?></p>
							</th>
							<th class="sortable" style="width: 20%">
								<div class="spacer"></div>
								<p><?=$this->lang->line('quantity');?></p>
							</th>

						</tr>
					
					</thead>
					
					<tbody>
					
						<?php $i = 0; ?>
						
							<?php foreach($data['products'] as $product): ?>
							
							<tr>
							
								<td><?=++$i;?></td>
								
								<td class="text_left">
									
									<div class="spacer"></div>
								
									<span style="line-height: 40px; padding-left: 10px;"><?=str_shorten($product['title'] , 100);?></span>
									
								</td>
								
								<td class="text_left">
									<div class="spacer"></div>
									<span style="line-height: 40px; padding-left: 10px;"><?php echo $product['articlenumber'];?></span>
								</td>

								<td>
									<div class="spacer"></div>
									
									<p>
										<?php echo $product['total_quantity'];?>
									</p>
								</td>
	
							</tr>
							
							<?php endforeach; ?>
							
					</tbody>
				
				</table>
				
		</div>
		<?php endif; ?>
	</div>
</div>

<?php require_once ELEM_DIR . 'admin_footer.php'; ?>