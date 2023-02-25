/*
* MediaWiki Extension:DiscordInvites
*
*
*/
    if( typeof deactivateDiscordInvites !== 'undefined' ) { throw "DiscordInvites were deactivated using on-site javascript."; }

    console.log('extension discordinvites js active');

    var invites = $(" .discord-invite[id^=discord-invite-unhandled-] ").map(function() {
        return this.id.substring(25);
    }).get();

    invites.forEach(elem => {
        try {
            invite = OO.ui.infuse( "#discord-invite-unhandled-" + elem );
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
            invite.setElementId( "discord-invite-" + elem );
        } catch (error) {
            console.warn(error);
        }
    });

        
    console.log(invites);