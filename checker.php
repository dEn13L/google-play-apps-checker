<?php

define('PACKAGES_FILE', 'packages.txt');
define('OUTPUT_FILE', 'result.txt');
define('GOOGLE_PLAY_APP_PREFIX', 'https://play.google.com/store/apps/details?id=');
define('MAX_PERCENTS_BLOCKS_COUNT', 50);

$packages = file(PACKAGES_FILE);
$packagesCount = count($packages);

$fp = fopen(OUTPUT_FILE, 'w');

foreach ($packages as $key => $package) {

	$package = trim($package);

	$appUrl = GOOGLE_PLAY_APP_PREFIX . $package;
	echo 'URL: ' . $appUrl . PHP_EOL;

	$ch = curl_init(); // create cURL handle (ch)
	if (!$ch) {
	    die('Couldn\'t initialize a cURL handle');
	}
	
	$ret = curl_setopt($ch, CURLOPT_URL,            $appUrl);
	$ret = curl_setopt($ch, CURLOPT_HEADER,         0);
	$ret = curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
	$ret = curl_setopt($ch, CURLOPT_TIMEOUT,        30);
	$ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$ret = curl_exec($ch);

	if (empty($ret)) {    
	    die(curl_error($ch));
	    curl_close($ch); // close cURL handler
	} else {
	    $info = curl_getinfo($ch);
	    curl_close($ch); // close cURL handler

	    $responseCode = $info['http_code'];
	    if (empty($responseCode)) {
	        fwrite($fp, 'No HTTP code was returned: ' . $package . PHP_EOL); 
	    } else {
	        echo 'The server responded: ' . $responseCode . PHP_EOL;	       
	        fwrite($fp, $responseCode . ': ' . $package . PHP_EOL);
	    }
	}

	$percentage = '';
	$percent = round(100 * ($key + 1) / $packagesCount, 2);
	
	for ($i = 0; $i < MAX_PERCENTS_BLOCKS_COUNT; $i++) { 
		$m = 100 * $i / MAX_PERCENTS_BLOCKS_COUNT;
		if ($percent > $m) {
			$percentage .= '#';
		} else {
			break;
		}		
	}
	echo PHP_EOL . $percentage . '  ' . $percent . '%' . PHP_EOL . PHP_EOL;
}

fclose($fp);

?>