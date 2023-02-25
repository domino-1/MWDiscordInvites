<?php

use MediaWiki\MediaWikiServices;
use MediaWiki\DatabaseUpdater;


class DiscordInvitesHooks {

	//database update hook
	public static function onLoadExtensionSchemaUpdates( DatabaseUpdater $updater ) {
        $dbType = $updater->getDB()->getType();
		
		// Register an SQL patch for changing the field
		$updater->addExtensionTable(
			'dcinv_block',
			__DIR__ . '/sql/block_discord.sql'
		);
	}
	
	// Register any render callbacks with the parser
	public static function onParserFirstCallInit( Parser $parser ) {

		// Create a function hook associating the "discord" magic word with renderDiscord()
		$parser->setFunctionHook( 'discord', [ self::class, 'renderDiscord' ], Parser::SFH_OBJECT_ARGS );
	}

	private static function discordApi( string $url, string $token ) {
		/*$data = file_get_contents ('https://discord.com/api/invites/r5xDRcy');
		$json = json_decode($data, TRUE);

		echo ('<pre> print the json ');
		print_r ($json);
		echo ('</pre>');
		return $json;*/
		// $data;
		
		// $curl = curl_init($url);
		// $headers = [
		// 	'Authorization: Bot ' . $token,
		// 	'User-Agent: DiscordBot (https://github.com/domino-1/MWDiscordInvites/, 1.0.0)',
		// 	'Content-Type: application/json'
		// ];
		// curl_setopt_array($curl, [
		// 	CURLOPT_FOLLOWLOCATION => false,
		// 	CURLOPT_MAXREDIRS => 0,
		// 	CURLOPT_RETURNTRANSFER => true,
		// 	CURLOPT_SSL_VERIFYPEER => 1,
		// 	CURLOPT_SSL_VERIFYHOST => 2
		// ]);
		// curl_setopt($curl, CURLOPT_HEADERFUNCTION,
		// 	function($ch, $header) use (&$headers)
		// 	{
		// 		$len = strlen($header);
		// 		$header = explode(':', $header, 2);
		// 		if (count($header) < 2) // ignore invalid headers
		// 		return $len;

		// 		$headers[strtolower(trim($header[0]))][] = trim($header[1]);

		// 		return $len;
		// 	}
		// );
		// $ret = curl_exec($curl);
		// $info = curl_getinfo($curl);
		// $error = curl_error($curl);
		// curl_close($curl);
		// if (empty($error)) {
		// 	if ($info["http_code"] === 200) {
		// 		/*$ratelimits = [$headers["x-ratelimit-limit"][0], $headers["x-ratelimit-remaining"][0], $headers["x-ratelimit-reset"][0], $headers["x-ratelimit-reset-after"][0], $headers["x-ratelimit-bucket"][0]];
		// 		return [false, "Unknown Invite", $ratelimits];*/
		// 		return [false, "Unknown Inivte", $headers];
		// 	} else if ($info["http_code"] === 429) {
		// 		$retrytime = $headers["retry-after"][0]; 
		// 		return [false, "Cloudflare ban\n\nRetry after: " . intval($retrytime / 3600) . " hours " . ($retrytime / 60 % 60) . " minutes and " . ($retrytime % 60) . " seconds" . " (Retry-After:$retrytime)"];
			
		// 	} else {
		// 		//$ratelimits = [$headers["x-ratelimit-limit"][0], $headers["x-ratelimit-remaining"][0], $headers["x-ratelimit-reset"][0], $headers["x-ratelimit-reset-after"][0], $headers["x-ratelimit-bucket"][0]];
		// 		$data = json_decode($ret, true);
		// 	return [true, $data, $headers/*$ratelimits*/];
		// 	}
		// } else {
		// 	return [false, "ERROR: " . $error];
		// }
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://discord.com/api/invites/r5xDRcy',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HEADER => true,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: Bot ' . $token,
				'User-Agent: DiscordBot (https://github.com/domino-1/MWDiscordInvites/, 1.0.0)'
			),
		));

		$response = curl_exec($curl);
		$info = curl_getinfo($curl);
		$error = curl_error($curl);

		curl_close($curl);
		if (empty($error)) {
			if ($info["http_code"] === 200) {
				/*$ratelimits = [$headers["x-ratelimit-limit"][0], $headers["x-ratelimit-remaining"][0], $headers["x-ratelimit-reset"][0], $headers["x-ratelimit-reset-after"][0], $headers["x-ratelimit-bucket"][0]];
				return [false, "Unknown Invite", $ratelimits];*/
				return [false, "Unknown Inivte", $response];
			} else if ($info["http_code"] === 429) {
				$retrytime = $headers["retry-after"][0]; 
				return [false, "Cloudflare ban\n\nRetry after: " . intval($retrytime / 3600) . " hours " . ($retrytime / 60 % 60) . " minutes and " . ($retrytime % 60) . " seconds" . " (Retry-After:$retrytime)"];
			
			} else {
				//$ratelimits = [$headers["x-ratelimit-limit"][0], $headers["x-ratelimit-remaining"][0], $headers["x-ratelimit-reset"][0], $headers["x-ratelimit-reset-after"][0], $headers["x-ratelimit-bucket"][0]];
				$data = json_decode($ret, true);
			return [true, $response/*$ratelimits*/];
			}
		} else {
			return [false, "ERROR: " . $error];
		}

	}

	private static function checkConfig( int $namespace, string $pagename ) {
		$config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'discordinvites' );
		
		# set up the blocked namespaces and block talk config
		$config_blockednamespaces = $config->get( 'DiscordInvitesBlockedNamespaces' );
		$config_blockOnTalk = $config->get( 'DiscordInvitesBlockTalk' );
		$config_exemptPages = $config->get( 'DiscordInvitesExemptPages' );

		# check whether page is exempt from all config, return true if yes
		if ( isset( $config_exemptPages[$pagename] ) ) { return true; }		

		# namespace checks
		if ( isset( $config_blockednamespaces[$namespace] ) && is_bool($config_blockednamespaces[$namespace]) ) {
			return !$config_blockednamespaces[$namespace];
		} 
		elseif ( $config_blockOnTalk && ($namespace % 2 == 1)) {
			return false;
		}

		return true;
	} 

	// Render the output of {{#discord:}}.
	public static function renderDiscord( Parser $parser, PPFrame $frame, array $args ) {
		if ( !isset($args[0]) ) { return ''; } // returns blank string if invite code is blank
		
		$invitecode = $frame->expand( $args[0] );

		$apilink = "https://discord.com/api/v10/invites/" . $invitecode;
		// $apiresult = self::discordApi("$apilink");
		// if (!$apiresult[0]) {
		// 	$out_headers = "";
		// 	/*if (isset($apiresult[2])) {
		// 		$out_headers = "";
		// 		foreach ($apiresult[2] as $header) {
		// 			$out_headers = $out_headers . " " . $header[0] . " : " . $header[1];
		// 		}
		// 	}*/
		// 	return '<strong class="error dcinv-invalid-invite">' . 
		// 	wfMessage( "dcinv-error-api", $apiresult[1], $apiresult[2] )->inContentLanguage()->escaped() 
		// 	. '</strong>';
		// } else { //test branch - remove else ág later
		// 	return '<strong class="error dcinv-invalid-invite">' . 
		// 	wfMessage( "dcinv-error-api", "Works: ", $apiresult[1] )->inContentLanguage()->escaped() 
		// 	. '</strong>';
		// }
		
		// if ( !(is_string($invitecode) and ctype_alnum($invitecode) and (strlen($invitecode) >= 2) and (strlen($invitecode) <= 256)) ) { 	
		// 		return 	'<strong class="error dcinv-invalid-invite">' . 
		// 				wfMessage( "dcinv-error-invalid-invite" )->inContentLanguage()->escaped() . '</strong>'; 
		// }
		$apiresult = [true, "a"];

		$namespace = $parser->getPage()->getNamespace();
		$pagename = $parser->getPage()->__toString();
																					
		$parser->getOutput()->setEnableOOUI( true );
		OutputPage::setupOOUI();
		$parser->getOutput()->addModules(['ext.DiscordInvites']);
		
		$btnframe = false;
		$btnicon = '';
		$btnflags = '';
		$btndisabled = !self::checkConfig($namespace, $pagename);
		
		# checks for button or link mode
		if ( isset($args[2]) ) { 
				$mode = $frame->expand( $args[2] ); 
				switch ($mode) {
					case "button":
						$btnframe = true;
						$btnclass = 'discord-invite-button';
						$btnicon = 'discord';
						$displaytext = (strlen($frame->expand($args[1])) > 0) ? ($frame->expand( $args[1] ) == '-' ? "" : $frame->expand($args[1])) : "Join Discord";
						break;
					case "link":
						$btnclass = 'discord-invite-link';
						$btnflags = 'progressive';
						$displaytext = (strlen($frame->expand($args[1])) > 0) ? $frame->expand( $args[1] ) : ("https://discord.gg/" . $invitecode);
						break;
					default: 	//same as mode: link
						$btnclass = 'discord-invite-link';
						$btnflags = 'progressive';
						$displaytext = (isset($args[1]) and (strlen($frame->expand($args[1])) > 0)) ? $frame->expand( $args[1] ) : ("https://discord.gg/" . $invitecode);
						break;
				}
			} else { 	//same as mode: link
				$btnclass = 'discord-invite-link';
				$btnflags = 'progressive';
				$displaytext = isset($args[1]) ? $frame->expand( $args[1] ) : ("https://discord.gg/" . $invitecode);
			}
	  
		$btnid = $btndisabled ? "discord-invite-blocked" : "discord-invite-unhandled-$invitecode";
		$btn = new OOUI\ButtonWidget( [
			'infusable' => true,
			'id' => $btnid,
			'label' => $displaytext . " - " /*. $apiresult[code]*/,
			'framed' => $btnframe,
			'flags' => $btnflags,
			'icon' => $btnicon,
			'classes' => [ 'discord-invite', $btnclass ],
			'disabled' => $btndisabled
		] );
		
		$parser->addTrackingCategory( 'dcinv-tracking-category-has-invite' );

		$api_ratelimits = "";
		if (isset($apiresult[2])) {
			$api_ratelimits = "Ratelimits: x-ratelimit-limit=" . $apiresult[2][0] . " x-ratelimit-remaining=" . $apiresult[2][1];
		}
		
		$output = json_encode($apiresult[1]) . "\n\n" . $api_ratelimits . "\n\n" . $btn;
		return $parser->insertStripItem( $output, $parser->getStripState() );
      
	}
	
}