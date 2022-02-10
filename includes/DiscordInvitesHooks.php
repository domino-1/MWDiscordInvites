<?php
//require 'mojang-api.class.php';

class DiscordInvitesHooks {
	
	// Register any render callbacks with the parser
	public static function onParserFirstCallInit( Parser $parser ) {

		// Create a function hook associating the "discord" magic word with renderDiscord()
		$parser->setFunctionHook( 'discord', [ self::class, 'renderDiscord' ], Parser::SFH_OBJECT_ARGS );
	}

	// Render the output of {{#discord:}}.
	public static function renderDiscord( Parser $parser, PPFrame $frame, array $args ) {
		if ( !isset($args[0]) ) { return 'tada'; } // returns blank string if invite code is blank
		
		$invitecode = $frame->expand( $args[0] );
		
		if ( !(is_string($invitecode) and (strlen($invitecode) >= 2)) ) { 	return 	'<strong class="error dcinv-invalid-invite">' . 
																					wfMessage( "dcinv-error-invalid-invite" )->inContentLanguage()->escaped() . 
																					'</strong>'; }
																					
		$parser->enableOOUI();
		$parser->getOutput()->addModules('ext.DiscordInvites');
		
		$btnframe = false;
		$iconmode = false;
		$btnicon = '';
		$btnflags = '';
		
		if ( isset($args[1]) ) { $displaytext = $frame->expand( $args[1] ); } else { $displaytext = "Join Discord"; }
		if ( isset($args[2]) ) { 
				$mode = $frame->expand( $args[2] ); 
				switch ($mode) {
					case "button":
						$btnframe = true;
						$btnclass = 'discord-invite-button';
						$btnicon = 'discord';
						break;
					case "link":
						$btnclass = 'discord-invite-link';
						$btnflags = 'progressive';
						break;
					case "icon":
						$iconmode = true;
						break;
					default: 	//same as mode: link
						$btnclasses = 'discord-invite-link';
						$btnflags = 'progressive';
						break;
				}
			} else { 	//same as mode: link
				$btnclass = 'discord-invite-link';
				$btnflags = 'progressive';
			}
	  
	  
		$btn = new OOUI\ButtonWidget( [
			'infusable' => true,
			'id' => "discord-invite-$invitecode",
			'label' => $displaytext,
			'framed' => $btnframe,
			'flags' => $btnflags,
			'icon' => $btnicon,
			'classes' => [ 'discord-invite', $btnclass ]/*,
			'target' => '_blank',
			'href' => 'https://discord.com'*/
		] );
		
		$output = $btn;

		return $parser->insertStripItem( $output, $parser->mStripState );
      
	}
	
}