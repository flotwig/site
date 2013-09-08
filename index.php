<?php
// Parse $_GET['request'] to get the request parameters
$homePage='page/main';
$request=strtolower($_GET['request']);
$request=preg_replace('/[^a-z0-9\/\-]/','',$request);
$request=explode('/',$request,127);
$request=array_filter($request);
if(empty($request)){
	$request[0]=$homePage;
}
$request=array_merge($request);
// Load the module specified
if(file_exists('modules/'.$request[0].'.php')){
	require_once('modules/'.$request[0].'.php');
	$output=call_user_func_array($request[0].'Output',array($request));
	if(!$output){
		giveError(500,'Internal Server Error');
	}
}else{
	giveError(404,'Not Found');
}
// Put the output into a template
$template=file_get_contents('template.html');
if(!empty($output['template'])&&is_array($output['template'])){
	foreach($output['template'] as $key=>$value){
		$template=str_replace('{{!'.$key.'}}',$value,$template);
	}
}
echo $template;
// Error function
function giveError($code,$message){
	http_response_code($code);
	$template=file_get_contents('errorTemplate.html');
	$template=str_replace('{{!code}}',(string)$code,$template);
	$template=str_replace('{{!message}}',$message,$template);
	$referredHost=parse_url($_SERVER['HTTP_REFERER'],PHP_URL_HOST);
	if($referredHost==$_SERVER['SERVER_NAME']){
		$template=str_replace('{{!backlink}}',htmlentities($_SERVER['HTTP_REFERER']),$template);
	}else{
		$template=str_replace('{{!backlink}}','javascript:window.history.back();',$template);
	}
	echo $template;
	die();
}