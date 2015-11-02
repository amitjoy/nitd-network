<?php


function he_quiz_paging( $params = array() )
{
	$total = (int)$params['total'];
	$in_page = (int)$params['on_page'];
	$pages_count = (int)$params['pages'];

	$request_uri = $_SERVER['PHP_SELF'];
			
	if( !$total || !$in_page || !$pages_count)
	{
		return '';
	}
	
	if( ($total_pages = ceil($total / $in_page)) <= 1 )
	{
		return '';
	}
	
	$current = @$_GET['page'];
	$current = intval($current) ? $current : 1;
		
	$offset = ceil($pages_count / 2) - 1;
	$offset_inc = ($total_pages - $offset) - $current;
	$offset+= ($offset_inc <= 0) ? abs($offset_inc) + ( ($pages_count%2) ? 0 : 1 ) : 0;
		
	$page = ($current - $offset) > 1 ? ($current - $offset) : 1;
		
	$paging = '';
	
	for ( $counter = 1; $counter <= $pages_count && $page <= $total_pages; $counter++ )
	{
		$active = ($page == $current) ? 'class="active"' : '';
		$url = he_quiz_make_url($request_uri, array( 'page' => $page ));
		$paging .= "<a href='{$url}' {$active}>{$page}</a>";
		$page++;
	}
	
	switch ( $current )
	{
		case 1:
			$paging .= "<a href='" . he_quiz_make_url($request_uri, array( 'page' => $current+1 )) . "'>" . SE_Language::get(690691147) . "</a>";
			$paging .= "<a href='" . he_quiz_make_url($request_uri, array( 'page' => $total_pages)) . "'>" . SE_Language::get(690691148) . "</a>";
			break;
			
		case $total_pages:
			$paging = "<a href='" . he_quiz_make_url($request_uri, array( 'page' => $current-1 )) . "'>" . SE_Language::get(690691149) . "</a>" . $paging;
			$paging = "<a href='" . he_quiz_make_url($request_uri, array( 'page' => 1 )) . "'>" . SE_Language::get(690691150) . "</a>" . $paging;
			break;
			
		default:
			$paging = "<a href='" . he_quiz_make_url($request_uri, array( 'page' => $current-1 )) . "'>" . SE_Language::get(690691149) . "</a>" . $paging;
			$paging = "<a href='" . he_quiz_make_url($request_uri, array( 'page' => 1 )) . "'>" . SE_Language::get(690691150) . "</a>" . $paging;
			
			$paging .= "<a href='" . he_quiz_make_url($request_uri, array( 'page' => $current+1 )) . "'>" . SE_Language::get(690691147) . "</a>";
			$paging .= "<a href='" . he_quiz_make_url($request_uri, array( 'page' => $total_pages )). "'>" . SE_Language::get(690691148) . "</a>";
			break;
	}
	
	$out = '<div class="paging">';
	$out .= '<span>' . SE_Language::get(690691161) . ' </span>';
	$out .= $paging . '</div>';


	return $out;	
}

function he_quiz_make_url( $url=null, $params = null )
{
	$url = $_SERVER['REQUEST_URI'];
	
	$url_info = parse_url($url);
	
	$url = strlen($_url = substr($url, 0, strpos($url, '?'))) ? $_url : $url ;
	
	if (isset($url_info['query'])) {
		parse_str($url_info['query'], $_params);
	}
	else {
		$_params = array();
	}
	
		
	$params = isset($params) ? $params : array();
	
	if(is_string($params) && strlen(trim($params)))
		parse_str($params, $params);
	elseif(!is_array($params))
		$params=array();
	
	$_params = array_merge($_params, $params);
	
	$param_str = '';
	
	foreach ($_params as $key=>$value)
	{
		$param_str .= ( $value ) ? "&$key=$value" : "&$key";
	}
	
	$param_str = ( strlen($param_str)>0 ) ? substr($param_str, 1) : '';		
	$query_str = count($_params) ? '?'.$param_str : '';

	return $url . $query_str;
}

function he_quiz_delete_user( $user_id )
{
	he_quiz::delete_user_info($user_id);
}

function he_quiz_truncate( $string, $length = 80, $etc = '...', $break_words = false, $middle = false )
{
    if ($length == 0)
        return '';

    if (strlen($string) > $length) {
        $length -= strlen($etc);
        if (!$break_words && !$middle) {
            $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length+1));
        }
        if(!$middle) {
            return substr($string, 0, $length).$etc;
        } else {
            return substr($string, 0, $length/2) . $etc . substr($string, -$length/2);
        }
    } else {
        return $string;
    }
}

function he_quiz_list( $params = array() )
{
    $active_tab = ( isset($params['active_tab']) && $params['active_tab'] ) ? $params['active_tab'] : 'popular';
    $count = ( isset($params['count']) && $params['count'] ) ? (int)$params['count'] : 5;

    $list_types = array( 'popular', 'latest', 'commented' );

    $quiz_list_str = '';
    foreach ( $list_types as $list_type )
    {
        $quiz_list = he_quiz::get_index_list($count, $list_type);
        
        $quizzes_str = '';
        foreach ( $quiz_list as $quiz )
	    {
	        $img_size = ( $quiz['size'][0] > $quiz['size'][1] ) ? 'width="60"' : 'height="60"';
	        $quizzes_str .= '<div class="he_quiz_item">
	            <div class="he_quiz_photo">
	            <a href="browse_quiz.php?quiz_id=' . $quiz['quiz_id'] . '">
	                <img border="0" src="' . ( $quiz['photo_url'] ? $quiz['photo_url'] : './images/he_quiz_thumb.jpg' ) .   '" ' . $img_size . '/>
	            </a>
	            </div>
	            <div class="he_quiz_info">
	                <div class="he_quiz_name"><a href="quiz.php?quiz_id=' . $quiz['quiz_id'] . '">' . $quiz['name'] . '</a></div>
	                <div class="he_quiz_description">' . he_quiz_truncate($quiz['description'], 100) . '</div>              
	                
	            </div>
	            <div class="clr"></div>
	        </div>';
	    }
	    
        $quizzes_str = strlen($quizzes_str) ? $quizzes_str : '<center>' . SE_Language::get(690691160) . '</center>';

        $is_active = ( $active_tab == $list_type ) ? 'active_tab' : '';
        $quizzes_str = '<div id="tab_' . $list_type . '" class="he_quiz_list ' . $is_active . '">' . $quizzes_str . '</div>';
        
        $quiz_list_str .= $quizzes_str;
    }
    
    $tabs_str = '<div class="he_quiz_tab" onclick="he_quiz.switch_tab(this, \'tab_commented\')">
                    <label>' . SE_Language::get(690691196) . '</label>
                </div>
                <div class="he_quiz_tab" onclick="he_quiz.switch_tab(this, \'tab_latest\')">
                    <label>' . SE_Language::get(690691158) . '</label>
                </div>
                <div class="he_quiz_tab active_tab" onclick="he_quiz.switch_tab(this, \'tab_popular\')">
                    <label>' . SE_Language::get(690691159) . '</label>
                </div>';
    
    $lang_var = SE_Language::get(690691161);
    
    return <<<OUTPUT
    <script src="./include/js/he_quiz.js" type="text/javascript"></script>
    
    <div class="he_quiz_list_block">
        <div class="he_quiz_block_cap">
            <div class="he_quiz_label">
                <b>$lang_var</b>
            </div>
            $tabs_str
            <div class="clr"></div>
        </div>
        <div class="he_quiz_block_body">
            $quiz_list_str
        </div>
    </div>

OUTPUT;
}

?>