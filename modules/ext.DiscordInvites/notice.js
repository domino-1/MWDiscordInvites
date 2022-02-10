/*
* MediaWiki Extension:DiscordInvites
*
*
*/
    console.log('extension discordinvites js active');

    var invites = $(" .discord-invite[id^=discord-invite-] ").map(function() {
        return this.id.substring(15);
    }).get();

    invites.forEach(elem => {
        try {
            invite = OO.ui.infuse( "#discord-invite-" + elem );
            invite.on( 'click', function() {
                OO.ui.confirm( mw.message( 'dcinv-confirm-message', elem ).text() ).done( function ( confirmed ) {
                    if ( confirmed ) {
                        window.open('https://discord.com/invite/' + elem + '/login', '_blank');
                        console.log( 'User clicked "OK"!' );
                    } else {
                        console.log( 'User clicked "Cancel" or closed the dialog.' );
                    }
                });
            });
        } catch (error) {
            console.warn(error);
        }
    });

        
    console.log(invites);