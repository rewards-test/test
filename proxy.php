<?php 
	$url = "http://hiring.rewardgateway.net/list";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERPWD, "hard:hard");
	$result = curl_exec ($ch);
	curl_close ($ch);
	echo $result;
?>