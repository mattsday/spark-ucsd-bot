<?php

	# Room to send responses:
	$roomId = 'Y2lzY29zcGFyazovL3VzL1JPT00vNThkMWMzNDAtYmFlNC0xMWU1LTg5NzEtM2Y3ZmFhNDU4MjA1';

	# Token:
	$token = 'Bearer xxx';

	# ID of bot
	$me = 'id';

	# Hostname of private server:
	$private_server = 'http://blah/';


	function send_message($body, $token) {
		$url = 'https://api.ciscospark.com/v1/messages';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [ "Authorization: ".$token,'Content-Type: application/json']);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		$response = json_decode(curl_exec($ch));
		print $url;
		print_r($body);
		print_r($ch);
		print_r($response);
	}
	$f = fopen('iddb', 'r');
	$id = chop(fread($f, filesize('iddb')));
	fclose($f);
	$f = fopen('iddb', 'w');
	fwrite($f, ($id + 1));
	fclose($f);


	header('Content-Type: text/plain');
	// dump request to file:
	$dump = print_r($_REQUEST, true);
	
	file_put_contents('log', $dump, FILE_APPEND);

	// Get latest message:
	$url = 'https://api.ciscospark.com/v1/messages';
	# Private chat:
	

	# Query string:
	$query_string = 'roomId='.$roomId.'&max=1';

	$url .= '?'.$query_string;


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	# Set headers
	curl_setopt($ch, CURLOPT_HTTPHEADER, [ "Authorization: ".$token,]);
	$content = json_decode(curl_exec($ch))->{'items'}[0];
	//build me a debian server with 3 cores and 4gb ram
	print_r($content);
	$message = $content->{'text'};
	#$message = 'build me a debian server with 3 cores and 4gb ram';
	#print $message;
	if (preg_match('/^pour me a (beer|pint)/i', $message)) {
		$body['roomId'] = $roomId;
		$img[0] = 'http://sparkle-bot.fragilegeek.com/pint.jpg';
		$body['files'] = $img;
		send_message(json_encode($body), $token);
		exit();
	}
	if ((preg_match('/^(make|build|create)( me)? a/i', $message)) && ($content->{'personId'} != $me)) {
		if (preg_match('/^make me a (bacon|cheese)?\s?(sandwich|butty)/i', $message, $nsandwich)) {
			$type = 'cheese';
			$img_url[0] = 'http://sparkle-bot.fragilegeek.com/cheese-sandwich.jpg';
			if (count($nsandwich) > 0) {
				$type = $nsandwich[1];
			}
			if (strtolower($type) == 'bacon') {
				$img_url[0] = 'http://sparkle-bot.fragilegeek.com/bacon-sandwich.jpg';
			}
			$body['roomId'] = $roomId;
			$body['files'] = $img_url;
			send_message(json_encode($body), $token);
			exit();
		}
		$os_type = 'debian';
		if (preg_match('/(debian|centos)/i', $message, $nos_type)) {
			$os_type = strtolower($nos_type[0]);
		}
		$cores = 1; $ram = 1; 
		if (preg_match('/ (\d) cores?/i', $message, $ncores)) {
			$cores = $ncores[1];
		}
		if (preg_match('/(\d\d?)gb ram/i', $message, $nram)) {
			$ram = $nram[1];
			if ($ram > 16) {
		                $body['roomId'] = $roomId;
			        $body['text'] = 'You have requested '.$ram.'gb RAM which is more than the currently allowed maximum (16gb). Please try again.';
			        send_message(json_encode($body), $token);
				exit();
			}
		}
		$name = $os_type;
		if (preg_match('/(called|call it) (\S+)/i', $message, $nname)) {
			print_r($nname);
			$name = $nname[2];
		}
		$message = 'Received request for '.$os_type.' server called '.$name.' with '.$cores.' cores and '.$ram.'gb ram'."\n\n";
		$qs = 'c='.$cores.'&r='.$ram.'&t='.$os_type.'&n='.$name.'&id='.$id.'&pid='.$content->{'personId'}; 
		$message .= 'If this is correct, please confirm by going here: http://'.$private_server.'/req?'.$qs;
		$body['roomId'] = $roomId;
		$body['text'] = $message;
		send_message(json_encode($body), $token);
	}
	else if ((preg_match('/rollback (SR)?(\d{1,4})/i', $message, $match) && ($content->{'personId'} != $me))) {
		$message = "Rolling back service request ID: ".$match[2].". To confirm, please go here:\nhttp://".$private_server."/rollback?sr=".$match[2].'&id='.$id;
		$body['text'] = $message;
		$body['roomId'] = $roomId;
		send_message(json_encode($body), $token);
	}

?>
