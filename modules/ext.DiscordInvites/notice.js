/*
*
*
*
*/
    console.log('extension discordinvites js active');

    var invites = $(" .discord-invite[id^=discord-invite-] ").map(function() {
        return this.id.substring(15);
    }).get();

    invites.forEach(element => {
        try {
            invite = OO.ui.infuse( "#discord-invite-" + element );
            invite.on( 'click', function() {
                alert( 'You clicked the invite' + element);
            });
        } catch (error) {
            console.warn(error);
        }
    });

        
    console.log(invites);