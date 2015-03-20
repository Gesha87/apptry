<?php
$plist = simplexml_load_file('php://input');
$keys = $plist->xpath('/dict/key');
$values = $plist->xpath('/dict/string');
foreach ($keys as $i => $key) {
	echo (string)$key . ':' . (string)($values[$i]);
}