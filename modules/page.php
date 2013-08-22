<?php
function pageOutput($request){
	if(file_exists('content/page/'.$request[1].'.md')){
		require_once('libs/Markdown.php');
		return array('template'=>array(
			'content'=>Markdown::defaultTransform(file_get_contents('content/page/'.$request[1].'.md'))));
	}else{
		giveError(404,'Not Found');
	}
}