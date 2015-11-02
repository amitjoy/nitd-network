<?

$page = 'quiz_suggest_to_friends';
include('header.php');

if( !$user->user_exists ) exit;

$quiz = he_quiz::general_info(intval($_REQUEST['quiz_id']));

if( $_POST['contacts_choosed'] && $quiz )
{
    $subject = htmlspecialchars_decode(SE_Language::_get(690691168), ENT_QUOTES);
    $message = nl2br(htmlspecialchars_decode(SE_Language::_get(690691169), ENT_QUOTES));
    
    $mass_mailing = new he_mass_mailing();
    $campaign_id = $mass_mailing->create_campaign($subject, $message, -1, 1);
    if( $campaign_id ) {
        $users = explode(',', $_POST['contacts']);
        $replace = array($user->user_info['user_displayname'], $quiz['name'], $url->url_base.'browse_quiz_results.php?quiz_id='.$quiz['id']);
        foreach( $users as $user_id ) {
            if( intval($user_id) )
                $mass_mailing->add_message_into_queue_for_user($campaign_id, $user_id, $replace);
        }

        $emails = explode(',', $_POST['emails']);
        foreach( $emails as $email ) {
            $email = trim($email);
            if( $email && is_email_address($email) )
                $mass_mailing->add_message_into_queue_for_email($campaign_id, $email, '', $replace);
        }
        $result = array( 'message' => SE_Language::_get(690691164), 'status' => true );
    }
    else {
        $result = array( 'message' => SE_Language::_get(690691165), 'status' => false );
    }
    
    he_print_json($result);
}

?>