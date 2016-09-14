<html>
<head>
   <style>
   </style>

   <script>
		var ctx;
		
		function redraw()
		{
				var yearz=document.getElementById("yearz").value;
				var monthz=document.getElementById("monthz").value;
				var dayz=document.getElementById("dayz").value;
				var viewz=document.getElementById("viewz").value;
				var servz=document.getElementById("servz").value;
				
				ctx.clearRect(0,0,1500,752);
				
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

				// Sort on interval length
				entries.sort(
						function(a, b){
								return b.interval-a.interval
						}
				);
				
				var filter="UNKO";
				
				str="<table>";

				// Filter Service List
				var olist=[];

				var j=0;
				for(i=0;i<entries.length;i++){
						var ar=entries[i].timest.substring(0,4);
						var man=entries[i].timest.substring(5,7);
						var dag=entries[i].timest.substring(8,10);
						var tim=entries[i].timest.substring(11,13);
						var min=entries[i].timest.substring(14,16);						
						var sec=entries[i].timest.substring(17,19);
						var interval=entries[i].interval;
						var xk=parseInt(min)+(parseInt(tim)*60.0);
						var ln=interval*0.1;
						
						if(( ar==yearz && man==monthz && dag==dayz) || filter=="UNK"){
						
								if(olist.indexOf(entries[i].service)==-1){
										olist.push(entries[i].service);
								}
								
								str+="<tr>";
								str+="<td>"+entries[i].id+"</td>";
								str+="<td>"+entries[i].timest+"</td>";
								str+="<td>"+entries[i].service+"</td>";
								str+="<td>"+entries[i].interval+"</td>";

								str+="<td>"+tim+"</td>";
								str+="<td>"+min+"</td>";
								str+="<td>"+sec+"</td>";
								str+="<td>"+xk+"</td>";

								str+="</tr>";
								
								var sst="#4b4";
								if(entries[i].service=="courseedservice.php") sst="#b44";
								if(entries[i].service=="resultedservice.php") sst="#44b";								
								
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
								
								// Advance Forward in sorted view
								j++;
								
						}						

				}
				str+="</table>";
				document.getElementById("content").innerHTML=str;		
				
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
		
		echo "var entries=[";
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
								echo '"timest":"'.$row['timest'].'"';
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
								<td>Sort:&nbsp;<select onchange="redraw()" id="sortz"><option>&#x25B2;</option><option>&#x25BC;</option></select></td>
								<td>Year:&nbsp;<select onchange="redraw()" id="yearz"><option>2015</option><option>2016</option><option>2017</option></select></td>
								<td>Month:&nbsp;<select onchange="redraw()" id="monthz"><option value="01">Jan</option><option value="02">Feb</option><option value="03">Mar</option><option value="04">Apr</option><option value="05">May</option><option value="06">Jun</option><option value="07">Jul</option><option value="08">Aug</option><option value="09">Sep</option><option value="10">Oct</option><option value="11">Nov</option><option value="01">Dec</option></select></td>
								<td>Day:&nbsp;<select onchange="redraw()" id="dayz"><option>01</option><option>02</option><option>03</option><option>04</option><option>05</option><option>06</option><option>07</option><option>08</option><option>09</option><option>10</option><option>11</option><option>12</option><option>13</option><option>14</option><option>15</option><option>16</option><option>17</option><option>18</option><option>19</option><option>20</option><option>21</option><option>22</option><option>23</option><option>24</option><option>25</option><option>26</option><option>27</option><option>28</option><option>29</option><option>30</option><option>31</option></select></td>
								<td>View:&nbsp;<select onchange="redraw()" id="viewz"><option>Daily R</option><option>View R</option></select></td>
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