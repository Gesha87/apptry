<?php
$plist = simplexml_load_string($_GET['data']);
if ($plist) {
	$i = 0;
	foreach ($plist->dict->key as $key) {
		echo (string)$key . ':' . (string)($plist->dict->string[$i++]).'<br>';
	}
} else {
	echo 'Wrong data!';
}
