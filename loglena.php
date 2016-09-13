<html>
<head>
   <style>
   </style>

   <script>
		var ctx;
		
		function initcanvas()
		{
				// Setup canvas
				var canvas = document.getElementById("myCanvas");
				if (canvas.getContext) {
				    ctx = canvas.getContext("2d");
				    ctx.strokeStyle="#000";
						ctx.beginPath();
				    ctx.moveTo(0,550);
				    ctx.lineTo(1500,550);
				    ctx.stroke();
				    
				    // Hour is 60px and each minute is 1px
						ctx.font="20px Arial";
				    for(i=0;i<25;i++){
								var klocka="";
								if(i<10) klocka+="0";
								
								if(i<24){
										klocka+=i+":00";
										ctx.fillStyle="#aaa";
										ctx.fillText(klocka,(i*60)+5,575);								
								}

						    ctx.strokeStyle="#AAA";
								ctx.beginPath();
						    ctx.moveTo(i*60,00);
						    ctx.lineTo(i*60,555);
						    ctx.stroke();
				    }
				    				
				}

				// Sort on interval length
				entries.sort(
						function(a, b){
								return a.interval-b.interval
						}
				);
				
				var filter="UNKO";
				
				str="<table>";
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
						
						if((ar=="2016" && man=="09"&&dag=="09") || filter=="UNK"){
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
								ctx.moveTo(xk,550);
								ctx.lineTo(xk,550-ln);
								ctx.stroke();
						}						

				}
				str+="</table>";
				
				document.getElementById("content").innerHTML=str;
		} 
    
<?php
		
		date_default_timezone_set("Europe/Stockholm");
		
		$pdo = new PDO('sqlite:loglena4.db');
		$sql = "SELECT id,uuid,service,datetime(timestamp/1000, 'unixepoch', 'localtime') as timest,timestamp as timez FROM serviceLogEntries LIMIT 30000 OFFSET 90000";
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

	      <canvas id="myCanvas" width="2200" height="600" style="display:block;border:1px solid red;">
	      </canvas>

				<div id="content" style="border:1px solid green;display:none;" >
					Foo!
				</div>

</body>
</html>