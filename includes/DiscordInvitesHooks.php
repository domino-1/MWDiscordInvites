<?php

use MediaWiki\MediaWikiServices;


class DiscordInvitesHooks {
	
	// Register any render callbacks with the parser
	public static function onParserFirstCallInit( Parser $parser ) {

		// Create a function hook associating the "discord" magic word with renderDiscord()
		$parser->setFunctionHook( 'discord', [ self::class, 'renderDiscord' ], Parser::SFH_OBJECT_ARGS );
	}

	// Render the output of {{#discord:}}.
	public static function renderDiscord( Parser $parser, PPFrame $frame, array $args ) {
		if ( !isset($args[0]) ) { return ''; } // returns blank string if invite code is blank
		
		$invitecode = $frame->expand( $args[0] );
		
		if ( !(is_string($invitecode) and ctype_alnum($invitecode) and (strlen($invitecode) >= 2) and (strlen($invitecode) <= 256)) ) { 	
				return 	'<strong class="error dcinv-invalid-invite">' . 
						wfMessage( "dcinv-error-invalid-invite" )->inContentLanguage()->escaped() . '</strong>'; 
		}

		# config from extension.json and LocalSettings.php
		$config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'discordinvites' );

		# set up the blocked namespaces and block talk config
		$config_blockednamespaces = $config->get( 'DiscordInvitesBlockedNamespaces' );
		$config_blockOnTalk = $config->get( 'DiscordInvitesBlockTalk' );
		$config_exemptPages = $config->get( 'DiscordInvitesExemptPages' );
		$namespace = $parser->getPage()->getNamespace();
		$pagename = $parser->getPage()->__toString();
																					
		$parser->getOutput()->setEnableOOUI( true );
		OutputPage::setupOOUI();
		$parser->getOutput()->addModules(['ext.DiscordInvites']);
		
		$btnframe = false;
		$btnicon = '';
		$btnflags = '';
		$btndisabled = false;

		# check against the block namespaces and block talk config
		if ( $config_blockednamespaces[$namespace] ) {
			$btndisabled = true;
		} elseif ( $config_blockednamespaces[$namespace] == null && $config_blockOnTalk ) {
			$btndisabled = ($namespace % 2 == 1);
		}
		
		# this must be the last config option to be checked (until checks are moved into a function)
		if ( $config_exemptPages[$pagename] ) {
			$btndisabled = false;
		}
		
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
			'label' => $displaytext . "aba" . $pagename,
			'framed' => $btnframe,
			'flags' => $btnflags,
			'icon' => $btnicon,
			'classes' => [ 'discord-invite', $btnclass ],
			'disabled' => $btndisabled
		] );
		
		$parser->addTrackingCategory( 'dcinv-tracking-category-has-invite' );
		
		$output = $btn;
		return $parser->insertStripItem( $output, $parser->getStripState() );
      
	}
	
}