
/**
 * @author Eldar
 * @copyright Hire-Experts LLC
 * @version Contacts Module 1.01
 */

var he_contacts = {

    callback_url : '',
    message_allowed : 0,
    contacts : [],
    last : 0,

    link : function( title, callback_url, message_allowed ) {
        javascript:TB_show(title, 'he_contacts.php?callback_url=' + callback_url + '&message_allowed=' + message_allowed + '&TB_iframe=true&height=460&width=580', '', './images/trans.gif');
    },

    init : function( callback_url, message_allowed, last ) {
        this.callback_url = callback_url;
        this.message_allowed = message_allowed;
        this.last = last;
    },

    get_more_contacts : function() {
        var self = this;
        if( self.last == -1 ) return;

        $('he_contacts_loading').setStyle('display', 'block');
        
        new Request.JSON({
            method: 'get',
            url: 'he_contacts.php?is_ajax=1&start=' + this.last,
            onSuccess: function(response) {
                $('he_contacts_loading').setStyle('display', 'none');
                if( response.html_code )
                {
                    var newDiv = document.createElement('div');
                    newDiv.innerHTML = response.html_code;
                    $('he_contacts_list').insertBefore(newDiv, document.getElementById('he_contacts_end_line'));
                }

                self.last = response.start;
                if( !response.more )
                {
                    $('he_contacts_more').set('class', 'more_disabled');
                    self.last = -1;
                }
            }
        }).send();
    },

    choose_contact : function( contact_id ) {
        if( this.contacts.indexOf(contact_id)==-1 ) { //add contact
            $("contact_" + contact_id).addClass("active");
            this.contacts[this.contacts.length] = contact_id;
        }
        else { //remove contact
            $("contact_" + contact_id).removeClass("active");
            this.contacts.splice(this.contacts.indexOf(contact_id), 1);
        }
    },

    send : function() {
        var self = this;
        $('he_contacts_loading').setStyle('display', 'block');
        new Request.JSON({
            method: 'post',
            url: this.callback_url,
            data: { 'contacts_choosed': 1 , 'contacts': self.contacts.toString(), 'emails': $('he_contacts_emails').value },
            onSuccess: function(response) {
                $('he_contacts_loading').setStyle('display', 'none');
                $('he_contacts_message').setStyle('display', 'block');
                $('he_contacts_message').innerHTML = response.message;
                if( response.status ) {
                    setTimeout("parent.TB_remove();", 2500);
                }
                else {
                    setTimeout("$('he_contacts_message').setStyle('display', 'none');", 2500);
                }
            }
        }).send();
    }
}