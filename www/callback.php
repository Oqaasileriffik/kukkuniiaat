<?php

header('HTTP/1.1 400 Bad Request');
$a = !empty($_REQUEST['a']) ? $_REQUEST['a'] : '';

$origin = '*';
if (!empty($_SERVER['HTTP_ORIGIN'])) {
	$origin = trim($_SERVER['HTTP_ORIGIN']);
}
header('Access-Control-Allow-Origin: '.$origin);
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	header('HTTP/1.1 200 Options');
	header('Access-Control-Allow-Headers: HMAC, *');
	die();
}

function json_encode_num($v, $o=0) {
	return json_encode($v, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | $o);
}

$start = microtime(true);
$acl = null;
$rv = [
	'e' => [],
	'a' => $a,
	];

while ($a === 'grammar') {
	if (empty(trim($_REQUEST['t']))) {
		$rv['e'][] = 'Malformed check request - no text passed';
		break;
	}

	if (preg_match_all('~<(STYLE:\w+:\w+)>~u', $_REQUEST['t'], $ms, PREG_SET_ORDER)) {
		foreach ($ms as $m) {
			// Shuffle whitespace from inside the style to outside the style
			$_REQUEST['t'] = preg_replace('~\Q'.$m[0].'\E(\s+)~us', '\1'.$m[0], $_REQUEST['t']);
			$_REQUEST['t'] = preg_replace('~(\s+)</\Q'.$m[1].'\E>~us', '</'.$m[1].'>\1', $_REQUEST['t']);
			// Remove now-empty styles
			$_REQUEST['t'] = preg_replace('~\Q'.$m[0].'\E</\Q'.$m[1].'\E>~us', '', $_REQUEST['t']);
			// Remove styles that are fully inside words
			$_REQUEST['t'] = preg_replace('~([\pL\pN\pM])\Q'.$m[0].'\E(.+)</\Q'.$m[1].'\E>([\pL\pN\pM])~us', '\1\2\3', $_REQUEST['t']);
		}
	}

	$nonce = mt_rand();
	$nonced = preg_replace('~<(/?s\d+)>~', '<$1-'.$nonce.'>', $_REQUEST['t']);

	$port = 12400;
	for ($try=0 ; $try < 3 ; ++$try) {
		//header('X-Kukkuniiaat-Port: '.$port, false);
		$s = fsockopen('localhost', $port, $errno, $errstr, 1);
		if ($s === false) {
			header('X-Kukkuniiaat-Error: '.$errno, false);
			continue;
		}
		//header('X-Kukkuniiaat-10-Connect: '.(microtime(true) - $start), false);
		if (fwrite($s, $nonced."\n<END-OF-INPUT>\n") === false) {
			header('X-Kukkuniiaat-Error: '.$port, false);
			continue;
		}
		//header('X-Kukkuniiaat-20-Write: '.(microtime(true) - $start), false);
		$output = stream_get_contents($s);
		//header('X-Kukkuniiaat-30-Read: '.(microtime(true) - $start), false);
		$output = trim($output);
		if (!preg_match('~<s\d+-'.$nonce.'>\n~', $output)) {
			$output = '';
		}

		if (!empty($output)) {
			$rv['c'] = $output;
			break;
		}
	}

	// Hack to show alternatives
	$rv['c'] = preg_replace('~ <R:([^>]+)>~', ' <R:$1> <AFR:$1>', $rv['c']);

	$rv['c'] = preg_replace('~<(/?s\d+)-\d+>~', '<$1>', $rv['c']);

	break;
}

if (empty($rv['e'])) {
	header('HTTP/1.1 200 Ok');
	unset($rv['e']);
}

if (!empty($_GET['callback'])) {
	header('Content-Type: application/javascript; charset=UTF-8');
	echo $_GET['callback'].'('.json_encode_num($rv).');';
}
else {
	header('Content-Type: application/json; charset=UTF-8');
	echo json_encode_num($rv);
}

flush();
if (function_exists('fastcgi_finish_request')) {
	fastcgi_finish_request();
}

if (!empty($rv['e'])) {
	exit(0);
}
