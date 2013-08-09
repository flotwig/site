<?php
// Parse $_GET['request'] to get the request parameters
$request=strtolower($_GET['request']);
$request=preg_replace('/[^a-z0-9\/\-]/','',$request);
$request=explode('/',$request,127);
$request=array_filter($request);
if(empty($request)){
	$request[0]='blog';
}
// Load the module specified
if(file_exists('./modules/'.$request[0].'.php')){
	require_once('./modules/'.$request[0].'.php');
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
// Error function
function giveError($code,$message){
	$template=file_get_contents('errorTemplate.html');
	$template=str_replace('{{!code}}',(string)$code,$template);
	$template=str_replace('{{!message}}',$message,$template);
	echo $template;
	die();
}