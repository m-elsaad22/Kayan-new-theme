<?php 
header("Content-Type: application/json");
$comment = get_comment($Params);
$CommentContent = $comment->comment_content;
$CommentContent = str_replace('<br/>', PHP_EOL, $CommentContent);
$CommentContent = strip_tags($CommentContent);
$CommentContent = $CommentContent;
$CommentContent = str_replace(PHP_EOL, '<br/>', $CommentContent);
$json['content'] = $CommentContent;
echo json_encode($json);