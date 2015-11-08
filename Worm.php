	<script type="text/javascript">
		window.onload = function () {
			var chart = new CanvasJS.Chart("chartContainer",
			{

				title:{
					text: "Worms",
					fontSize: 30,
					titleFontColor: "black"
				},
				axisX:{
					title: "Rounds",
					gridColor: "Silver",
					tickColor: "silver",
					titleFontColor: "black"

				},                        
							toolTip:{
							  shared:true
							},
				theme: "theme2",
				axisY: {
					title: "Points",
					gridColor: "Silver",
					tickColor: "silver",
					titleFontColor: "black"
				},
				legend:{
					verticalAlign: "center",
					horizontalAlign: "right"
				},
				data: [
				
				<?php
					$query0 = "select * from player";
					$result0 = mysql_query($query0) or die ("Error in query: $query0. ".mysql_error());
					while($player = mysql_fetch_assoc($result0))
					{
				?>
				
				{        
					type: "line",
					showInLegend: true,
					lineThickness: 2,
					name: "<?php echo $player['player']; ?>",
					markerType: "circle",
					color: "<?php echo $player['color']; ?>",
					dataPoints: [
					
					<?php
						$query = "select * from score where player='".$player['player']."' order by round ";
						$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
						while($score = mysql_fetch_assoc($result))
						{
					?>
					
					{ x: <?php echo $score['round']; ?>, y: <?php echo $score['agg_pts']; ?>, label: "<?php echo $score['round']; ?>" },
					
					<?php
						}
					?>
					
					]
				},
				<?php 
					}
				?>
			
				],
			  legend:{
				cursor:"pointer",
				itemclick:function(e){
				  if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
					e.dataSeries.visible = false;
				  }
				  else{
					e.dataSeries.visible = true;
				  }
				  chart.render();
				}
			  }
			});

	chart.render();
	}
	</script>