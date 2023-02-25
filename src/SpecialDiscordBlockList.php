<?php
//namespace MediaWiki\Extension\DiscordInvites;

class SpecialDiscordBlockList extends SpecialPage {
    
    public function __construct() {
		parent::__construct( 'DiscordBlockList', 'editinterface' );
	}

    protected function getGroupName() {
		return 'pagetools';
	}

    public function doesWrites() {
		return false;
	}

	

    public function execute( $par ) {
		$request = $this->getRequest();
		$output = $this->getOutput();
        $user = $this->getUser();

        if ( !$user->isAllowed( 'editinterface' ) ) {
			throw new PermissionsError( 'blockdiscord' );
		}

        // Show a message if the database is in read-only mode
		$this->checkReadOnly();

        // Set the page title and other stuff
		$this->setHeaders();
		$output->setPageTitle( $this->msg( 'dcinv-special-block-title' ) );

		# Get request data from, e.g.
		$param = $request->getText( 'param' );

		# Do stuff
		# ...
		$wikitext = 'Hello world! ' . $par;
		$output->addWikiTextAsInterface( $wikitext );

		/*$dbr = wfGetDB( DB_REPLICA );
		$res = $dbr->select(
			'dcinv_block',
			'*',
			[],
			__METHOD__,
			[
				'LIMIT' => $limit,
				'OFFSET' => $offset,
				'ORDER BY' => 'dcinvblock_timestamp DESC'
			]
		);*/

		$res = [
			[
				"dcinvblock_guild" => 1,
				"dcinvblock_user_text" => "Domino2",
				"dcinvblock_timestamp" => 1,
				"dcinvblock_reason" => "Dummy data"
			], 
			[
				"dcinvblock_guild" => 2,
				"dcinvblock_user_text" => "Domino2",
				"dcinvblock_timestamp" => 1,
				"dcinvblock_reason" => "Dummy data"
			], 
			[
				"dcinvblock_guild" => 3,
				"dcinvblock_user_text" => "Domino2",
				"dcinvblock_timestamp" => 1,
				"dcinvblock_reason" => "Dummy data"
			]
		];

		$this->makeTable( $res );
		//$res->free();
	}


	/**
	 * Output result table.
	 * @param array $data
	 */
	protected function makeTable( $data ) {
		$lang = $this->getLanguage();

		$out = Html::openElement(
			'table',
			[ 'class' => 'mw-discordblocktable wikitable' ]
		) . "\n";
		$out .= Html::openElement( 'thead' ) .
			Html::openElement( 'tr', [ 'class' => 'mw-editcounttable-header' ] ) .
			Html::element( 'th', [], $this->msg( 'discordinvites-special-blocklist-guild' )->text() ) .
			Html::element( 'th', [], $this->msg( 'discordinvites-special-blocklist-user')->text() ) .
			Html::element( 'th', [], $this->msg( 'discordinvites-special-blocklist-timestamp' )->text() ) .
			Html::element( 'th', [], $this->msg( 'discordinvites-special-blocklist-reason' )->text() ) .
			Html::closeElement( 'tr' ) .
			Html::closeElement( 'thead' ) .
			Html::openElement( 'tbody' );

		foreach ( $data as $row ) {
			$out .= Html::openElement( 'tr', [ 'class' => 'mw-discordblocktable-row' ] ) .
				Html::element(
					'td',
					[ 'class' => 'mw-discordblocktable-guild' ],
					$row['dcinvblock_guild']
				) .
				Html::element(
					'td',
					[ 'class' => 'mw-discordblocktable-user' ],
					$row['dcinvblock_user_text']
				) .
				Html::element(
					'td',
					[ 'class' => 'mw-discordblocktable-timestamp' ],
					wfTimestamp( TS_MW, $row['dcinvblock_timestamp'] )
				) .
				Html::element(
					'td',
					[ 'class' => 'mw-discordblocktable-reason' ],
					$row['dcinvblock_reason']
				) .
				Html::closeElement( 'tr' );

		}
		
		/*while ( $row = $data->fetchObject() ) {
			$out .= Html::openElement( 'tr', [ 'class' => 'mw-discordblocktable-row' ] ) .
				Html::element(
					'td',
					[ 'class' => 'mw-discordblocktable-guild' ],
					$data->dcinvblock_guild
				) .
				Html::element(
					'td',
					[ 'class' => 'mw-discordblocktable-user' ],
					$data->dcinvblock_user_text
				) .
				Html::element(
					'td',
					[ 'class' => 'mw-discordblocktable-timestamp' ],
					wfTimestamp( TS_MW, $data->dcinvblock_timestamp )
				) .
				Html::element(
					'td',
					[ 'class' => 'mw-discordblocktable-reason' ],
					$data->dcinvblock_reason
				) .
				Html::closeElement( 'tr' );
		}*/
		
		/*foreach ( $nsData as $ns => $count ) {
			if ( $ns === NS_MAIN ) {
				$nsName = $this->msg( 'blanknamespace' )->text();
			} else {
				$nsName = $converter->convertNamespace( $ns );
				if ( $nsName === '' ) {
					$nsName = "NS$ns";
				}
			}
			$out .= Html::openElement( 'tr', [ 'class' => 'mw-editcounttable-row' ] ) .
				Html::element(
					'td',
					[ 'class' => 'mw-editcounttable-ns' ],
					$nsName
				) .
				Html::element(
					'td',
					[ 'class' => 'mw-editcounttable-count' ],
					$lang->formatNum( $count )
				) .
				Html::element(
					'td',
					[ 'class' => 'mw-editcounttable-percentage' ],
					wfPercent( $count / $data['sum'] * 100 )
				) .
				Html::closeElement( 'tr' );
		} 
			
		// bottom sum row
		$out .= Html::openElement( 'tr', [ 'class' => 'mw-editcounttable-footer' ] ) .
			Html::element( 'th', [], $this->msg( 'editcountneue-result-allnamespaces' )->text() ) .
			Html::element(
				'th',
				[ 'class' => 'mw-editcounttable-count' ],
				$lang->formatNum( $data['sum'] )
			) .
			Html::element(
				'th',
				[ 'class' => 'mw-editcounttable-percentage' ],
				wfPercent( 100 )
			) .
			Html::closeElement( 'tr' );*/

		$out .= Html::closeElement( 'tbody' ) .
			Html::closeElement( 'table' );

		$this->getOutput()->addHTML( $out );
	}
	
}