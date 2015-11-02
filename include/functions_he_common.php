<?
if( !function_exists('he_pa') ) {

function he_pa( $var, $return = false ) {
    $type = gettype( $var );

    $out = print_r( $var, true );
    $out = htmlspecialchars( $out );
    $out = str_replace('  ', '&nbsp; ', $out );
    if( $type == 'boolean' )
        $content = $var ? 'true' : 'false';
    else
        $content = nl2br( $out );
    $out = '<div style="
        border:2px inset #666;
        background:black;
        font-family:Verdana;
        font-size:11px;
        color:#6F6;
        text-align:left;
        margin:20px;
        padding:16px">
            <span style="color: #F66">('.$type.')</span> '.$content.'</div><br /><br />';

    if( !$return )
        echo $out;
    else
        return $out;
}

function frontend_pa( $params ) {
    he_pa( $params['var'] );
}

$smarty->register_function('he_pa', 'frontend_pa');


function he_print_json( $result ) {
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Pragma: no-cache"); // HTTP/1.0
    header("Content-Type: application/json");
    echo json_encode( $result );
    exit();
}

function he_secure_js( $string )
{
    $search = array("'", '&#039;', '"', "\r", "\n",'</');
    $replace = array('\&#39;','\&#39;', '\&#34;', '\\r', '\\n', '<\/');
	return str_replace( $search, $replace, $string );
}

function frontend_secure_js( $params )
{
	return he_secure_js($params['var']);
}

$smarty->register_function('he_secure_js', 'frontend_secure_js');


function he_paging( $params = array() )
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
		$url = he_make_url($request_uri, array( 'page' => $page ));
		$paging .= "<a href='{$url}' {$active}>{$page}</a>";
		$page++;
	}
	
	switch ( $current )
	{
		case 1:
			$paging .= "<a href='" . he_make_url($request_uri, array( 'page' => $current+1 )) . "'>" . SE_Language::get(680680001) . "</a>";
			$paging .= "<a href='" . he_make_url($request_uri, array( 'page' => $total_pages)) . "'>" . SE_Language::get(680680002) . "</a>";
			break;
			
		case $total_pages:
			$paging = "<a href='" . he_make_url($request_uri, array( 'page' => $current-1 )) . "'>" . SE_Language::get(680680003) . "</a>" . $paging;
			$paging = "<a href='" . he_make_url($request_uri, array( 'page' => 1 )) . "'>" . SE_Language::get(680680004) . "</a>" . $paging;
			break;
			
		default:
			$paging = "<a href='" . he_make_url($request_uri, array( 'page' => $current-1 )) . "'>" . SE_Language::get(680680003) . "</a>" . $paging;
			$paging = "<a href='" . he_make_url($request_uri, array( 'page' => 1 )) . "'>" . SE_Language::get(680680004) . "</a>" . $paging;
			
			$paging .= "<a href='" . he_make_url($request_uri, array( 'page' => $current+1 )) . "'>" . SE_Language::get(680680001) . "</a>";
			$paging .= "<a href='" . he_make_url($request_uri, array( 'page' => $total_pages )). "'>" . SE_Language::get(680680002) . "</a>";
			break;
	}
	
	$out = '<div class="paging">';
	$out .= '<span>' . SE_Language::get(680680005) . ' </span>';
	$out .= $paging . '</div>';


	return $out;	
}

function he_make_url( $url=null, $params = null )
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

$smarty->register_function('he_paging', 'he_paging');


}

?>