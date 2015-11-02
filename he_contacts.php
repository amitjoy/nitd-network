<?

$page = "he_contacts";
include "header.php";
include "include/class_he_contacts.php";

if( !$user->user_exists ) cheader("login.php");

$start = intval($_REQUEST['start']);
$count = 16;

//it is needed to check db version
$he_contacts = new he_contacts();

$contacts = $user->user_friend_list($start, $count, 1, 1, "se_users.user_id ASC");
$smarty->assign_by_ref('contacts', $contacts);
$contacts_compiled = $contacts ? $smarty->fetch('he_contacts_list.tpl') : '';

$contacts_total = $user->user_friend_total(1, 1);
$more_contacts_existed = ( $contacts_total > $start + $count ) ? 1 : 0;

if( $_REQUEST['is_ajax'] ) {
    he_print_json( array( 'html_code'=> $contacts_compiled, 'more' => $more_contacts_existed, 'start' => $start + $count ) );
}


$smarty->assign('contacts_compiled', $contacts_compiled);
$smarty->assign('last', $start + $count);
$smarty->assign('more_contacts_existed', $more_contacts_existed);
$smarty->assign('message_allowed', intval($_REQUEST['message_allowed']));
$smarty->assign('callback_url', $_REQUEST['callback_url']);

include "footer.php";
?>