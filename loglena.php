<html>
<head>
   <style>
   </style>

   <script>
		var ctx;
		function initcanvas()
		{
				str="<table>";
				for(i=0;i<entries.length;i++){
						str+="<tr>";
						str+="<td>"+entries[i].id+"</td>";
						str+="<td>"+entries[i].timest+"</td>";
						str+="<td>"+entries[i].service+"</td>";
						str+="<td>"+entries[i].interval+"</td>";						
						str+="</tr>";
				}
				str+="</table>";
				
				
				
				document.getElementById("content").innerHTML=str;
				
/*
				var canvas = document.getElementById("myCanvas");
				if (canvas.getContext) {
				    ctx = canvas.getContext("2d");
				    ctx.strokeRect(0,0,900,500);
				
				}
*/
		} 
    
<?php
		
		date_default_timezone_set("Europe/Stockholm");
		
		$pdo = new PDO('sqlite:loglena4.db');
		$sql = "SELECT id,uuid,service,datetime(timestamp/1000, 'unixepoch', 'localtime') as timest,timestamp as timez FROM serviceLogEntries LIMIT 4000";
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

				<div id="content">Foo!</div>

	      <canvas id="myCanvas" width="600" height="600" style="display:none;">
</body>
</html>