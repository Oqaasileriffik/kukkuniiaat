<?php

$GLOBALS['-l10n'] = [];

if (file_exists(__DIR__.'/l10n-dan.php')) {
	require_once __DIR__.'/l10n-dan.php';
	require_once __DIR__.'/l10n-kal.php';
	require_once __DIR__.'/l10n-eng.php';
}

if (file_exists(RELROOT.'/inc/l10n-dan-local.php')) {
	require_once RELROOT.'/inc/l10n-dan-local.php';
	require_once RELROOT.'/inc/l10n-kal-local.php';
	require_once RELROOT.'/inc/l10n-eng-local.php';
}

$GLOBALS['-a2-to-a3'] = array(
	'da' => 'dan',
	'kl' => 'kal',
	'en' => 'eng',
	);

$GLOBALS['-l10n-cur'] = $GLOBALS['-l10n']['eng'];
$GLOBALS['-l10n-markers'] = [];

function l10n_get($t) {
	if (!empty($GLOBALS['-l10n-cur'][$t])) {
		return $GLOBALS['-l10n-cur'][$t];
	}
	foreach (['eng', 'kal', 'dan'] as $l) {
		if (!empty($GLOBALS['-l10n'][$l][$t])) {
			return $GLOBALS['-l10n'][$l][$t];
		}
	}
	return $t;
}

function l10n_replace_markers($t) {
	foreach ($GLOBALS['-l10n-markers'] as $k => $v) {
		$t = str_replace($k, $v, $t);
	}
	return $t;
}

function l10n_parseAcceptLanguage($al='') {
	if (empty($al)) {
		$al = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
	}
    $al = strtolower(trim(preg_replace('@\s+@s', '', $al)));
    $list = [];

    $m = [];
    preg_match_all('@(([-a-z]+)([;\s]*q=)?([0-9.]+)?),?@', $al, $m);

    if (!empty($m) && !empty($m[2])) {
        foreach ($m[2] as $k => $v) {
            if (empty($m[4][$k])) {
                $m[4][$k] = 1.0;
            }
            $list[$v] = $m[4][$k];
        }
    }
    if (!empty($list)) {
		foreach ($list as $k => $v) {
			$lang = explode('-', $k);
			if (empty($list[$lang[0]])) {
				$list[$lang[0]] = $v;
			}
		}
        ksort($list);
        arsort($list);
    }

	return $list;
}

function l10n_buffer($s) {
	while (preg_match('~\{l10n:([^{}:]+)\}~', $s, $m)) {
		$r = l10n_get($m[1]);
		$r = l10n_replace_markers($r);
		if (strpos($m[1], 'TXT_') === 0) {
			$r = str_replace("\n", "<br>\n", $r);
		}
		$s = str_replace($m[0], $r, $s);
	}
	return $s;
}

function l10n_setUILang($force=null) {
	$lang = 'en';
	if (!empty($force)) {
		$lang = $force;
	}
	else {
		$langs = l10n_parseAcceptLanguage($_SERVER['HTTP_ACCEPT_LANGUAGE']);
		if (!empty($_COOKIE['uilang'])) {
			$langs[$_COOKIE['uilang']] = 2.0;
		}
		if (!empty($_GET['ui'])) {
			$langs[$_GET['ui']] = 3.0;
		}
		if (!empty($_POST['ui'])) {
			$langs[$_POST['ui']] = 4.0;
		}
		arsort($langs);

		if (!empty($langs)) {
			foreach ($langs as $k => $v) {
				if (array_key_exists($k, $GLOBALS['-a2-to-a3'])) {
					$lang = $k;
					break;
				}
			}
		}
	}

	if ($lang === 'en' || !empty($force)) {
		if (!empty($_COOKIE['uilang'])) {
			setcookie('uilang', '', ['expires' => 0, 'path' => '/', 'secure' => true, 'samesite' => 'None']);
		}
	}
	else {
		setcookie('uilang', $lang, ['expires' => time()+60*60*24*7, 'path' => '/', 'secure' => true, 'samesite' => 'None']);
	}

	$GLOBALS['-l10n-lang2'] = $lang;
	$lang = $GLOBALS['-a2-to-a3'][$lang];
	$GLOBALS['-l10n-lang3'] = $lang;
	$GLOBALS['-l10n-cur'] = $GLOBALS['-l10n'][$lang];

	ob_start('l10n_buffer');
}

function l10n($t, $vs=[]) {
	$t = l10n_get($t);

	if (!is_array($vs) && preg_match('~((?:%[^%\s]+%)|(?:\{[^{}\s]+\}))~', $t, $m)) {
		$id = substr($m[1], 1, -1);
		$vs = [$id => $vs];
	}

	do {
		$did = false;
		preg_match_all('~((?:%[^%\s]+%)|(?:\{[^{}\s]+\}))~', $t, $ms);
		foreach ($ms[0] as $m) {
			$id = substr($m, 1, -1);
			if (strlen($id) && array_key_exists($id, $vs)) {
				$t = str_replace($m, $vs[$id], $t);
				$did = true;
			}
		}
	} while($did);

	$t = l10n_replace_markers($t);
	return $t;
}
