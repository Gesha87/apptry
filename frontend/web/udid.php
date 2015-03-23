<?php
$data = file_get_contents('php://input');
header('Location: http://apptry.camlyapp.com/result.php?data='.rawurldecode($data));