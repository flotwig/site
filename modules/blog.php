<?php
function blogOutput($request){
	$rawBlogs=file_get_contents('content/blog.csv');
	$rawBlogs=explode("\n",$rawBlogs);
	$blogs=array(); // str machine-name => array(int timestamp, str title)
	foreach($rawBlogs as $blog){
		$blog=explode(',',$blog,3);
		$blogs[$blog[1]]=array($blog[0],$blog[2]);
	}
	if (empty($request[1])){
		$content='<table id="blogPosts">';
		foreach($blogs as $machineName=>$blogPost){
			$content.='<tr>
				<th><a href="/blog/'.$machineName.'">'.$blogPost[1].'</a></th>
				<td class="date">'.date('F j Y \a\t g:ia',$blogPost[0]).'</td>
			</tr>';
		}
		$content.='</table>';
		return array('template'=>array(
			'content'=>$content,
			'title'  =>'Blog Posts'
		));
	}elseif(array_key_exists($request[1],$blogs)){
		require_once('libs/Markdown.php');
		return array('template'=>array(
			'title'  =>$blogs[$request[1]][1],
			'content'=>'<h2>'.$blogs[$request[1]][1].'</h2>
			'.Markdown::defaultTransform(file_get_contents('content/blog/'.$request[1].'.md'))));
	}else{
		giveError(404,'Blog Post Not Found');
	}
}