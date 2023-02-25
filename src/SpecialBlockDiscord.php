<?php
//namespace MediaWiki\Extension\DiscordInvites;

class SpecialBlockDiscord extends FormSpecialPage {
    
    public function __construct() {
		parent::__construct( 'BlockDiscord', 'editinterface' );
	}

    protected function getGroupName() {
		return 'pagetools';
	}

    public function doesWrites() {
		return true;
	}

	protected function getFormFields() {
		return [
			'guildid' => [
				'type' => 'text',
				'maxlength' => '22',
				'label-message' => 'dcinv-special-block-guildid',
				'required' => true
			]
		];
	}

	public function onSubmit( array $data ) { 
		$out = $this->getOutput();
		$out->addWikiTextAsInterface( "Hello world!" . $data['guildid'] );

	}

    /*public function execute( $par ) {
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
	}*/
	
}