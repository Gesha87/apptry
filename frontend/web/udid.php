<?php
$data = file_get_contents('php://input');
header('HTTP/1.1 301 Moved Permanently');
header('Location: http://apptry.camlyapp.com/result/?data='.rawurldecode($data));