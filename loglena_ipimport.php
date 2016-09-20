<html>
<head>
   <style>
   </style>

   </script>
</head>
<body onload="initcanvas();">

<?php

/*
	GeoPlugin Status Codes!
	
		200 - Full data returned
		206 - Only country data
		404 - No data found for the IP at all
*/		

		date_default_timezone_set("Europe/Stockholm");
		
		$pdo = new PDO('sqlite:loglena4.db');

		$sql = '
		CREATE TABLE IF NOT EXISTS ipNumbers (
			id INTEGER PRIMARY KEY,
			ipnumber VARCHAR(20),
			dnsname VARCHAR(64),
			city VARCHAR(40),
			countrycode VARCHAR(4),
			countryname VARCHAR(40),
			continent VARCHAR(4),
			latitude VARCHAR(8),
			longitude VARCHAR(8),
			regioncode VARCHAR(4),
			regionname VARCHAR(40),
			currency VARCHAR(20),		
			timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
		);';
		
		$stmt = $pdo->prepare($sql);
		if(!$stmt->execute()) {
				$error=$stmt->errorInfo();
				echo "Error updating code example: ".$error[2];
		}

		
		echo "<table>";
		$sql='SELECT DISTINCT IP FROM serviceLogEntries WHERE NOT EXISTS(SELECT * FROM ipNumbers WHERE ipnumber=IP);';
		$stmt = $pdo->prepare($sql);
		if(!$stmt->execute()) {
				$error=$stmt->errorInfo();
				echo "Error reading log: ".$error[2];
		} 
		foreach($stmt as $key => $row){
				$servj=json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$row['IP']));

				// Retrieve
				$ip=$row['IP'];
				$host=gethostbyaddr($row['IP']);
				$gstatus=$servj->geoplugin_status;
				$city=$servj->geoplugin_city;
				$region=$servj->geoplugin_region;
				$country=$servj->geoplugin_countryName;
				$countrycode=$servj->geoplugin_countryCode;
				$continent=$servj->geoplugin_continentCode;
				$latitude=$servj->geoplugin_latitude;
				$longitude=$servj->geoplugin_longitude;
				$regionname=$servj->geoplugin_regionName;
				$regioncode=$servj->geoplugin_regionCode;
				$currency=$servj->geoplugin_currencyCode;

				// Print for Debug Purposes
				echo "<tr>";
				echo "<td>".$ip."</td>";
				echo "<td>".$host."</td>";				
				echo "<td>".$gstatus."</td>";
				echo "<td>".$city."</td>";
				echo "<td>".$region."</td>";
				echo "<td>".$country."</td>";
				echo "<td>".$countrycode."</td>";
				echo "<td>".$continent."</td>";
				echo "<td>".$longitude."</td>";
				echo "<td>".$latitude."</td>";
				echo "<td>".$regionname."</td>";
				echo "<td>".$regioncode."</td>";
				echo "<td>".$currency."</td>";
				echo "</tr>";
							
				// Insert into IP Number Log!
				$sql='INSERT INTO ipNumbers(ipnumber,dnsname,city,countrycode,countryname,continent,latitude,longitude,regioncode,regionname,currency) VALUES (:ipnumber,:dnsname,:city,:countrycode,:countryname,:continent,:latitude,:longitude,:regioncode,:regionname,:currency)';
				$stmt = $pdo->prepare($sql);
	
				$stmt->bindParam(':ipnumber', $ip);
				$stmt->bindParam(':dnsname', $host);
				$stmt->bindParam(':city', $city);
				$stmt->bindParam(':countrycode', $countrycode);
				$stmt->bindParam(':countryname', $country);
				$stmt->bindParam(':continent', $continent);
				$stmt->bindParam(':latitude', $latitude);
				$stmt->bindParam(':longitude', $longitude);
				$stmt->bindParam(':regioncode', $regioncode);
				$stmt->bindParam(':regionname', $regionname);
				$stmt->bindParam(':currency', $currency);

				if(!$stmt->execute()) {
						$error=$stmt->errorInfo();
						echo "Error writing log: ".$error[2];
				}
						
				// Keep Keeping On!
 				ob_flush();
        flush();
        sleep(5);				
		}
		
		echo "</table>"

?>

</body>
</html>