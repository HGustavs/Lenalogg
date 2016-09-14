<html>
<head>
   <style>
   </style>

   <script>
		var ctx;
		
		var colorz=[];
		
		colorz["courseedservice.php"]="#b36";
		colorz["filerecrive.php"]="#b63";
		colorz["resultedservice.php"]="#36b";
		colorz["sectionedservice.php"]="#3b6";
		colorz["showDuggaservice.php"]="#6b3";
		colorz["duggaedservice.php"]="#6b3";
				
		function redraw()
		{
				var sortz=document.getElementById("sortz").value;

				var yearz=document.getElementById("yearz").value;
				var monthz=document.getElementById("monthz").value;
				var dayz=document.getElementById("dayz").value;
				var viewz=document.getElementById("viewz").value;
				var servz=document.getElementById("servz").value;

				
				if(servz==""){
						servz="ALL";
				}
							
				// Always Clear Screen
				ctx.clearRect(0,0,2200,768);
				
				var filter="UNKO";
				
				// Prepare grid lines if applicable
				if(viewz=="Daily R"||viewz=="View R"||viewz=="Down R"){
							// Clock Diagram Guide Lines				
				    ctx.strokeStyle="#000";
						ctx.beginPath();
				    ctx.moveTo(0,550);
				    ctx.lineTo(1500,550);
				    ctx.moveTo(0,752);
				    ctx.lineTo(1500,752);
				    ctx.stroke();
				    
				    // Hour is 60px and each minute is 1px
						ctx.font="20px Arial";
				    for(i=0;i<25;i++){
								var klocka="";
								if(i<10) klocka+="0";
		
								// Draw hour border and background								
								if(i<24){
										klocka+=i+":00";
										ctx.fillStyle="#aaa";
										ctx.fillText(klocka,(i*60)+5,575);								
		
										if(i%2==0){
												ctx.fillStyle="#f8f8f8";
												ctx.fillRect(i*60,0,60,548);								
										}
		
								}
		
						    ctx.strokeStyle="#AAA";
								ctx.beginPath();
						    ctx.moveTo(i*60,00);
						    ctx.lineTo(i*60,555);
						    ctx.stroke();
				    }											
				}

				// Show the relevant view
				if(viewz=="Daily R" || viewz=="View R" || viewz=="Down R"){
						document.getElementById("myCanvas").style.display="block";
						document.getElementById("content").style.display="none";
				}else if(viewz=="Tab R"){
						document.getElementById("myCanvas").style.display="none";
						document.getElementById("content").style.display="block";						
				}
						
				// Produce Content!
				if(viewz=="Daily R" || viewz=="Tab R"){

						// Prefiltering
						entries=[];
						for(var i=0;i<entriez.length;i++){
								if(( entriez[i].ar==yearz && entriez[i].man==monthz && entriez[i].dag==dayz)){
										entries.push(entriez[i]);
								}
						}
						
						if(sortz=="U"){
								// Sort on interval length
								entries.sort(
										function(a, b){
												return b.interval-a.interval
										}
								);
						}else if(sortz=="D"){
								// Sort on interval length
								entries.sort(
										function(a, b){
												return a.interval-b.interval
										}
								);						
						}else if(sortz=="T"){
								// Sort on timestamp
								entries.sort(
										function(a, b){
												if(a.timest < b.timest) return -1;
												if(a.timest > b.timest) return 1;
												return 0;
										}
								);						
						}

						if(viewz=="Tab R"){
								str="<table>";						
						}else if (viewz=="Daily R"){

						}

						var j=0;
						for(i=0;i<entries.length;i++){
								var interval=entries[i].interval;
								var xk=parseInt(entries[i].min)+(parseInt(entries[i].tim)*60.0);
								var ln=interval*0.1;
																
								if(viewz=="Tab R"){
										str+="<tr>";
										str+="<td>"+entries[i].id+"</td>";
										str+="<td>"+entries[i].timest+"</td>";
										str+="<td>"+entries[i].service+"</td>";
										str+="<td>"+entries[i].interval+"</td>";
		
										str+="<td>"+entries[i].tim+"</td>";
										str+="<td>"+entries[i].min+"</td>";
										str+="<td>"+entries[i].sec+"</td>";
										str+="<td>"+xk+"</td>";
		
										str+="</tr>";
								}else{
										sst=colorz[entries[i].service];
										ctx.strokeStyle=sst;								

										ctx.beginPath();
										
										// Time Based Plot
										ctx.moveTo(xk,550);
										ctx.lineTo(xk,550-ln);
										
										// Sorted Plot
										if(ln>150) ln=150;
										ctx.moveTo(j,750);
										ctx.lineTo(j,750-ln);
		
										ctx.stroke();
								}
																		
								// Advance Forward in sorted view
								j++;
						}

						if(viewz=="Tab R"){
								str+="</table>";
								document.getElementById("content").innerHTML=str;		
						}
				}else if(viewz=="View R"){

						// Prefiltering
						entries=[];
						for(var i=0;i<entriez.length;i++){
								if(( entriez[i].ar==yearz && entriez[i].man==monthz && entriez[i].dag==dayz)){
										entries.push(entriez[i]);
								}
						}
						
						// Remember Prev Array!
						var servcoord=[];
					
						// No sorting in this view!
						for(i=0;i<entries.length;i++){
								var interval=entries[i].interval;
								var xk=parseInt(entries[i].min)+(parseInt(entries[i].tim)*60.0);
								var ln=interval*0.1;
								if(servz=="ALL"||servz==entries[i].service){
										ctx.beginPath();
										
										sst=colorz[entries[i].service];
										ctx.strokeStyle=sst;								
		
										// Draw line
										if(servcoord[entries[i].service] != undefined){
												// Was previously found - draw between old and new
												ctx.moveTo(servcoord[entries[i].service].xk,550-servcoord[entries[i].service].yk);
												ctx.lineTo(xk,550-ln);
										}else{
												// Was not previously found - draw between 0 and new
												ctx.moveTo(0,550-ln);
												ctx.lineTo(xk,550-ln);
										}								
										
										// Declare object for next time
										servcoord[entries[i].service]={"xk":xk,"yk":ln};
		
										ctx.stroke();
								}
						}											
				}else if(viewz=="Down R"){

						// Prefiltering
						entries=[];
						for(var i=0;i<entriez.length;i++){
								if(( entriez[i].ar==yearz && entriez[i].man==monthz && entriez[i].dag==dayz)){
										entries.push(entriez[i]);
								}
						}
						
						// No sorting in this view!
						var lxk=0;

						ctx.globalAlpha = 0.2;

						for(i=0;i<entries.length;i++){
								var interval=entries[i].interval;
								var xk=parseInt(entries[i].min)+(parseInt(entries[i].tim)*60.0);
								var ln=interval*0.1;

								if(lxk>0&&(xk-lxk)>60&&entries[i].tim>=2&&entries[i].tim<=7){
										ctx.fillStyle="#00F";
										ctx.fillRect(lxk,0,xk-lxk,546);								
								}else if(lxk>0&&(xk-lxk)>60){
										ctx.fillStyle="#F00";
										ctx.fillRect(lxk,0,xk-lxk,546);
								}else if(lxk>0&&(xk-lxk)>15){
										ctx.fillStyle="#0F0";
										ctx.fillRect(lxk,0,xk-lxk,546);
								}
								
								lxk=xk;
						}
						ctx.globalAlpha = 1.0;
						
						// ctx.globalAlpha = 0.5
				}

				//Update olist (iterating over entries)
				var olist=[];
				for(i=0;i<entries.length;i++){
						if(olist.indexOf(entries[i].service)==-1){
								olist.push(entries[i].service);
						}				
				}
								
				str="";
				if(servz=="ALL"){
						str+="<option selected='selected'>ALL</option>";
				}else{
						str+="<option>ALL</option>";
				}
				for(i=0;i<olist.length;i++){
						if(servz==olist[i]){
								str+="<option selected='selected'>"+olist[i]+"</option>";
						}else{
								str+="<option>"+olist[i]+"</option>";
						}
				}
				document.getElementById("servz").innerHTML=str;								
		
		}
		
		function initcanvas()
		{
				// Setup canvas
				var canvas = document.getElementById("myCanvas");
				if (canvas.getContext) {
				    ctx = canvas.getContext("2d");
				}
				
				redraw();

		} 
    
<?php
		
		date_default_timezone_set("Europe/Stockholm");
		
		$pdo = new PDO('sqlite:loglena4.db');
		$sql = "SELECT id,uuid,service,datetime(timestamp/1000, 'unixepoch', 'localtime') as timest,timestamp as timez FROM serviceLogEntries LIMIT 30000 OFFSET 80000";
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
		
		echo "var entriez=[";
		$i=0;
		foreach($stmt as $key => $row){
				if(isset($oldrow)){
						if($row['uuid']==$oldrow['uuid']){
								if($i>0) echo ",";
								$i++;
								echo "{";
								
								$datetime1 = $row['timez'];
								$datetime2 = $oldrow['timez'];
								$interval = $datetime1-$datetime2;
								
								if($oldrow['uuid']!=$row['uuid']) $interval=-1;

								echo '"id":"'.$row['id'].'",';
								echo '"uuid":"'.$row['uuid'].'",';
								echo '"service":"'.$row['service'].'",';
								echo '"interval":"'.$interval.'",';

								echo '"interval":"'.$interval.'",';
								echo '"interval":"'.$interval.'",';
								echo '"interval":"'.$interval.'",';
								echo '"interval":"'.$interval.'",';

								$timest=$row['timest'];

								echo '"ar":"'.substr($timest,0,4).'",';
								echo '"man":"'.substr($timest,5,2).'",';
								echo '"dag":"'.substr($timest,8,2).'",';
								echo '"tim":"'.substr($timest,11,2).'",';
								echo '"min":"'.substr($timest,14,2).'",';
								echo '"sec":"'.substr($timest,17,2).'",';

								echo '"timest":"'.$timest.'"';
								echo "}";
						}
				}
				$oldrow=$row;
		}
		echo "];";

?>    
        
   </script>
</head>
<body onload="initcanvas();">

				<div class="topmenu">
						<table>
								<td>Sort:&nbsp;<select onchange="redraw()" id="sortz"><option value="U">&#x25B2;</option><option value="D">&#x25BC;</option><option value="T">&hearts;</option></select></td>
								<td>Year:&nbsp;<select onchange="redraw()" id="yearz"><option>2015</option><option>2016</option><option>2017</option></select></td>
								<td>Month:&nbsp;<select onchange="redraw()" id="monthz"><option value="01">Jan</option><option value="02">Feb</option><option value="03">Mar</option><option value="04">Apr</option><option value="05">May</option><option value="06">Jun</option><option value="07">Jul</option><option value="08">Aug</option><option value="09">Sep</option><option value="10">Oct</option><option value="11">Nov</option><option value="01">Dec</option></select></td>
								<td>Day:&nbsp;<select onchange="redraw()" id="dayz"><option>01</option><option>02</option><option>03</option><option>04</option><option>05</option><option>06</option><option>07</option><option>08</option><option>09</option><option>10</option><option>11</option><option>12</option><option>13</option><option>14</option><option>15</option><option>16</option><option>17</option><option>18</option><option>19</option><option>20</option><option>21</option><option>22</option><option>23</option><option>24</option><option>25</option><option>26</option><option>27</option><option>28</option><option>29</option><option>30</option><option>31</option></select></td>
								<td>View:&nbsp;<select onchange="redraw()" id="viewz"><option>Daily R</option><option>View R</option><option>Tab R</option><option>Down R</option></select></td>
								<td>Service:&nbsp;<select onchange="redraw()" id="servz"></select></td>
						</table>
				</div>
	      
	      <canvas id="myCanvas" width="2200" height="1200" style="display:block;box-shadow:2px 2px 4px #444;">
	      </canvas>

				<div id="content" style="border:1px solid green;display:none;" >
					Foo!
				</div>

</body>
</html>