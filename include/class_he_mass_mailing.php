<?

if( !class_exists('he_mass_mailing') ) {

class he_mass_mailing
{
    var $cron_period;

    /**
     * Constructor
     * @return object
     */
    function he_mass_mailing()
    {
        $this->cron_period = 60;
        $this->check_db_structure();
    }

    /**
     * Creates campaign and return its ID. Admin is -1;
     * @param $subject
     * @param $message
     * @param $sender
     * @return int OR false
     */
    function create_campaign($subject, $message, $sender, $is_email = true)
    {
        if( !strlen($message) || !intval($sender) ) return false;

        he_database::query(he_database::placeholder("INSERT INTO se_he_mass_mailing_campaign SET subject='?', message='?', sender=?, sent_time=UNIX_TIMESTAMP(), is_email=?", $subject, $message, intval($sender), intval($is_email)));

        return he_database::insert_id();
    }

	/**
     * Returns campaign
     * @param $id
     * @return array
     */
    function get_campaign($id)
    {
        return he_database::fetch_row(he_database::placeholder("SELECT * FROM se_he_mass_mailing_campaign WHERE id=?", intval($id)));
    }

    /**
     * Returns campaingns
     * @param $from
     * @param $count
     * @return array of array
     */
    function get_campaigns($from, $count)
    {
        return he_database::fetch_array(he_database::placeholder("SELECT * FROM se_he_mass_mailing_campaign ORDER BY sent_time DESC LIMIT ?, ?", intval($from), intval($count)));
    }

    /**
     * Adds a message into queue for user
     * @param $campaign_id
     * @param $recipient
     * @param array $replace
     * @return int OR false
     */
    function add_message_into_queue_for_user($campaign_id, $recipient, $replace = array())
    {
        $replace_str = ( $replace && is_array($replace) ) ? serialize($replace) : '';
        
        he_database::query(he_database::placeholder("INSERT INTO se_he_mass_mailing_queue SET campaign_id=?, recipient=?, `replace`='?'", intval($campaign_id), intval($recipient), $replace_str));
        return he_database::insert_id();
    }

    /**
     * Adds a message into queue for email. Recipient's full name is optional
     * @param $campaign_id
     * @param string $email
     * @param string $full_name
     * @param array $replace
     * @return int OR false
     */
    function add_message_into_queue_for_email($campaign_id, $email, $full_name='', $replace = array())
    {
        $replace_str = ( $replace && is_array($replace) ) ? serialize($replace) : '';

        he_database::query(he_database::placeholder("INSERT INTO se_he_mass_mailing_queue SET campaign_id=?, email='?', full_name='?', `replace`='?'", intval($campaign_id), $email, $full_name, $replace_str));
        return he_database::insert_id();
    }

    /**
     * Deletes message from queue
     * @param $id
     * @return boolean
     */
    function delete_message_from_queue($id)
    {
        he_database::query(he_database::placeholder("DELETE FROM se_he_mass_mailing_queue WHERE id=?", intval($id)));
        return ( he_database::affected_rows() );
    }

    /**
     * Sends messages from queue
     * @param $count
     * @return void
     */
    function send_messages_from_queue($count)
    {
        global $setting;
        $count = intval($count) ? intval($count) : 10;
        $campaigns = array();
        $senders = array();

        $messages = he_database::fetch_array("SELECT * FROM se_he_mass_mailing_queue ORDER BY id LIMIT $count");
        foreach ($messages as $message)
        {
            if( !$this->delete_message_from_queue($message['id']) ) continue;

            //get campaign
            if( !$campaigns[$message['campaign_id']] )
                $campaigns[$message['campaign_id']] = $this->get_campaign($message['campaign_id']);
            $campaign = $campaigns[$message['campaign_id']];

            //get sender if it is not email and sender is not admin
            if( !$campaign['is_email'] && $campaign['sender'] ) {
                if( !$senders[$campaign['sender']] )
                    $senders[$campaign['sender']] = new se_user(array($campaign['sender'], ''));
                
                $sender = $senders[$campaign['sender']];
                if( !$sender->user_exists ) continue;
            }

            //get recipient
            $recipient = null;
            if( $message['recipient'] ) {
                $recipient = new se_user(array($message['recipient'], ''));
                if( !$recipient->user_exists ) continue;
            }
            else {
                if( !$message['email'] ) continue;
            }

            //set subject and message
            $subject = $message['replace'] ? vsprintf($campaign['subject'], unserialize($message['replace'])) : $campaign['subject'];
            $body = $message['replace'] ? vsprintf($campaign['message'], unserialize($message['replace'])) : $campaign['message'];

            //send email/message
            if( $campaign['is_email'] )
            {
                $mail = new PHPMailer();

                $mail->From = $setting['setting_email_fromemail'];
                $mail->FromName = $setting['setting_email_fromname'];
                $mail->Subject = $subject;
                $mail->AltBody = $body;//'To view the message, please use an HTML compatible email viewer.';
                $mail->MsgHTML($body);
                if( $recipient )
                    $mail->AddAddress($recipient->user_info['user_email'], $recipient->user_displayname);
                else
                    $mail->AddAddress($message['email'], $message['full_name']);

                $mail->Send();
            }
            else {
                if( !$recipient->user_exists || !$sender->user_exists ) continue;
                $sender->user_message_send($recipient->user_info['user_username'], $subject, $body);
            }
        }
    }

    /**
     * Should be called by cron or in header file
     * @return void
     */
    function cron()
    {
        global $setting;

        if( $setting['he_mass_mailing_last_execute'] > time()-$this->cron_period ) return;

        $setting['he_mass_mailing_last_execute'] = time()+5;
        he_database::query("UPDATE se_settings SET he_mass_mailing_last_execute={$setting['he_mass_mailing_last_execute']}");

        $this->send_messages_from_queue($setting['he_mass_mailing_limit_per_execute']);
    }

    /**
     * Checks if module has db structure and if it is needed upgrade or not
     *
     * @return void;
     */
    function check_db_structure()
    {
        global $settings;

        $file_version = $this->get_version();

        //check if db structure is ready
        if( !$settings['he_mass_mailing_version'] )
        {
            if( !he_database::fetch_row("SHOW COLUMNS FROM se_settings LIKE 'he_mass_mailing_version'") )
            {
                he_database::query("ALTER TABLE `se_settings` ADD `he_mass_mailing_version` int(10) NOT NULL DEFAULT $file_version");
                he_database::query("ALTER TABLE `se_settings` ADD `he_mass_mailing_last_execute` int(10) NOT NULL");
                he_database::query("ALTER TABLE `se_settings` ADD `he_mass_mailing_limit_per_execute` int(10) NOT NULL DEFAULT 30");
                he_database::query("CREATE TABLE `se_he_mass_mailing_campaign` (`id` int(11) NOT NULL AUTO_INCREMENT,`subject` varchar(255) NOT NULL,`message` text NOT NULL,`sender` int(10) NOT NULL,`sent_time` int(10) unsigned NOT NULL,`is_email` tinyint(1) NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
                he_database::query("CREATE TABLE `se_he_mass_mailing_queue` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT,`campaign_id` int(11) NOT NULL,`recipient` int(10) NOT NULL,`email` varchar(64) NOT NULL,`full_name` varchar(128) NOT NULL,`replace` text NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
                $settings['he_mass_mailing_version'] = $file_version;
            }
        }

        //check db and file version
        if( $file_version > $settings['he_mass_mailing_version'] )
        {
            //db version is older than file so we have to upgrade db version
            switch( $settings['he_mass_mailing_version'] )
            {
                case 101:
                break;
            }
        }
    }

    /**
     * Returns module version
     * @return int
     */
    function get_version()
    {
        return 101;
    }
}

}

?>