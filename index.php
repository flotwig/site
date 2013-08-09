<?php
// Parse $_GET['request'] to get the request parameters
$request=preg_replace('/[^a-zA-Z0-9\/\-]/','',$_GET['request']);
$request=explode('/',$request,127);
$request=array_filter($request);