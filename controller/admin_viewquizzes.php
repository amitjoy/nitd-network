<?php

$page = "admin_viewquizzes";
include "admin_header.php";

if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "id"; }
if(isset($_POST['st'])) { $st = $_POST['st']; } elseif(isset($_GET['st'])) { $st = $_GET['st']; }
if(isset($_POST['id'])) { $id = $_POST['id']; } elseif(isset($_GET['id'])) { $id = $_GET['id']; }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; }

if ( $task == 'delete' )
{
	he_quiz::delete_quiz($id);
}
elseif( $task == 'dodelete' )
{
	$deleted_quizzes = isset($_POST['delete']) ? $_POST['delete'] : array();

	foreach ( $deleted_quizzes as $quiz_id )
	{
		he_quiz::delete_quiz($quiz_id);
	}
}

if ( $st == 1 )
{
	he_quiz::approve_quizz($id);
}
elseif ( $st == 0 )
{
	he_quiz::disapprove_quizz($id);
}

$total_quizzes = he_quiz::count_quizzes();

$quizzes_per_page = 20;
$page_vars = make_page($total_quizzes, $quizzes_per_page, $p);
$page_array = Array();

for( $x = 0; $x <= $page_vars[2]-1; $x++ )
{
	if( $x+1 == $page_vars[1] )
	{
		$link = "1";
	}
	else
	{
		$link = "0";
	}
  
	$page_array[$x] = Array( 'page' => $x+1, 'link' => $link );
}

$rows = he_quiz::get_quizzes($page_vars[0], $quizzes_per_page);

$smarty->assign('quizzes',$rows);
$smarty->assign('total_quizzes',$total_quizzes);
$smarty->assign('pages', $page_array);

include "admin_footer.php";

?>