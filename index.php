<?php
// Parse $_GET['request'] to get the request parameters
$request=preg_replace('/[^a-zA-Z0-9\/\-]/','',$_GET['request']);
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
		// TODO: handle 500
	}
}else{
	// TODO: handle 404
	die();
}