<html>
<head>
   <style>
   </style>

   </script>
</head>
<body onload="initcanvas();">

<?php

function getDomain($url)
{
    $exp = explode('.', $url);
    $count = count($exp);
    $tmp_arr[0] = $exp[$count-2];
    $tmp_arr[1] = $exp[$count-1];
    $final_str = implode('.', $tmp_arr);
    return $final_str;
}

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
			domain VARCHAR(40),	
			timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
		);';
				
		$stmt = $pdo->prepare($sql);
		if(!$stmt->execute()) {
				$error=$stmt->errorInfo();
				echo "Error updating code example: ".$error[2];
		}

		// Set Geo Service Provider
		$servicePr="freegeoip";
			
		echo "<table>";
		$sql='SELECT DISTINCT IP FROM serviceLogEntries WHERE NOT EXISTS(SELECT * FROM ipNumbers WHERE ipnumber=IP);';
		$stmt = $pdo->prepare($sql);
		if(!$stmt->execute()) {
				$error=$stmt->errorInfo();
				echo "Error reading log: ".$error[2];
		} 
		foreach($stmt as $key => $row){
				// Retrieve
				$ip=$row['IP'];
				if(strpos($ip," ")!==false){
						$ip=substr($ip, 0, strpos($ip, " "));
				}
				$host=gethostbyaddr($ip);
				$domain=getDomain($host);

				if($servicePr=="geoplugin"){
						$servj=json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));
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
				}else if($servicePr=="freegeoip"){
						$servj=json_decode(file_get_contents("http://freegeoip.net/json/".$ip));
						$gstatus="UNK";
						$city=$servj->city;
						$region=$servj->region_name;
						$country=$servj->country_name;
						$countrycode=$servj->country_code;
						$continent=$servj->zip_code;
						$latitude=$servj->latitude;
						$longitude=$servj->longitude;
						$regionname=$servj->region_name;
						$regioncode=$servj->region_code;
						$currency=$servj->region_name;
						$zip=$servj->zip_code;				
				}

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
				echo "<td>".$domain."</td>";
				echo "</tr>";

				// Insert into IP Number Log!
				$sql='INSERT INTO ipNumbers(ipnumber,dnsname,city,countrycode,countryname,continent,latitude,longitude,regioncode,regionname,domain) VALUES (:ipnumber,:dnsname,:city,:countrycode,:countryname,:continent,:latitude,:longitude,:regioncode,:regionname,:domain)';
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
				$stmt->bindParam(':domain', $domain);

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