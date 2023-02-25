<?php
//namespace MediaWiki\Extension\DiscordInvites;

class SpecialAddDiscord extends FormSpecialPage {
    
    public function __construct() {
		parent::__construct( 'AddDiscord', 'edit' );
	}

    protected function getGroupName() {
		return 'pagetools';
	}

    public function doesWrites() {
		return true;
	}

	protected function getFormFields() {
		return [
			'invitecode' => [
				'type' => 'text',
				'maxlength' => '64',
				'label-message' => 'dcinv-special-add-invitecode',
				'required' => true
			]
		];
	}

	public function onSubmit( array $data ) { 
		$out = $this->getOutput();
		$config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'discordinvites' );

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://discord.com/api/invites/' . $data['invitecode'],
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: Bot ' . $config->get('DiscordBotToken')
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);

		$out->addWikiTextAsInterface( $response );

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