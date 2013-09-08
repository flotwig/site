<?php
function pageOutput($request){
	if(file_exists('content/page/'.$request[1].'.md')){
		require_once('libs/Markdown.php');
		$content=file_get_contents('content/page/'.$request[1].'.md');
		$title=explode("\n",$content);
		$title=$title[0];
		return array('template'=>array(
			'title'  =>$title;
			'content'=>Markdown::defaultTransform($content)));
	}else{
		giveError(404,'Not Found');
	}
}