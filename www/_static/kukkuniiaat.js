'use strict';

let g_lang = 'en';
let g_root = false;

function escHTML(t) {
	return t.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&apos;');
}

function ls_get(key, def) {
	let v = null;
	try {
		v = window.localStorage.getItem(key);
	}
	catch (e) {
	}
	if (v === null) {
		v = def;
	}
	else {
		v = JSON.parse(v);
	}
	return v;
}

function ls_set(key, val) {
	try {
		window.localStorage.setItem(key, JSON.stringify(val));
	}
	catch (e) {
	}
}

function l10n_detectLanguage() {
	g_lang = ls_get('lang', navigator.language).replace(/^([^-_]+).*$/, '$1');
	if (/\/(da|en|kl)$/i.test(location.pathname)) {
		g_lang = location.pathname.slice(-2);
	}
	if (!l10n.s.hasOwnProperty(g_lang)) {
		g_lang = 'en';
	}
	return g_lang;
}

function l10n_translate(s, g) {
	s = '' + s; // Coerce to string

	if (s === 'EMPTY') {
		return '';
	}

	let t = '';

	// If the string doesn't exist in the locale, fall back
	if (!l10n.s[g_lang].hasOwnProperty(s)) {
		// Try English
		if (l10n.s.hasOwnProperty('en') && l10n.s.en.hasOwnProperty(s)) {
			t = l10n.s.en[s];
		}
		// ...then Danish
		else if (l10n.s.hasOwnProperty('da') && l10n.s.da.hasOwnProperty(s)) {
			t = l10n.s.da[s];
		}
		// ...give up and return as-is
		else {
			t = s;
		}
	}
	else {
		t = l10n.s[g_lang][s];
	}

	let did = false;
	do {
		did = false;
		let rx = /\{([A-Z0-9_]+)\}/g;
		let ms = [];
		let m = null;
		while ((m = rx.exec(t)) !== null) {
			ms.push(m[1]);
		}
		for (let i=0 ; i<ms.length ; ++i) {
			let nt = l10n_translate(ms[i]);
			if (nt !== ms[i]) {
				t = t.replace('{'+ms[i]+'}', nt);
				did = true;
			}
		}

		rx = /%([a-zA-Z0-9]+)%/;
		m = null;
		while ((m = rx.exec(t)) !== null) {
			let rpl = '\ue001'+m[1]+'\ue001';
			if (typeof g === 'object' && g.hasOwnProperty(m[1])) {
				rpl = g[m[1]];
			}
			t = t.replace(m[0], rpl);
			did = true;
		}
	} while (did);

	t = t.replace(/\ue001/g, '%');
	return t;
};

function _l10n_world_helper() {
	let e = $(this);
	let k = e.attr('data-l10n');
	let v = l10n_translate(k);

	if (k === v) {
		return;
	}

	if (/^TXT_/.test(k)) {
		v = '<p>'+v.replace(/\n+<ul>/g, '</p><ul>').replace(/\n+<\/ul>/g, '</ul>').replace(/<\/ul>\n+/g, '</ul><p>').replace(/\n+<li>/g, '<li>').replace(/\n\n+/g, '</p><p>').replace(/\n/g, '<br>')+'</p>';
	}
	e.html(v);
	if (/^TXT_/.test(k)) {
		l10n_world(e);
	}
}

function l10n_world(node) {
	if (!node) {
		node = document;
	}
	$(node).find('[data-l10n]').each(_l10n_world_helper);
	$(node).find('[data-l10n-alt]').each(function() {
		let e = $(this);
		let k = e.attr('data-l10n-alt');
		let v = l10n_translate(k);
		e.attr('alt', v);
	});
	$(node).find('[data-l10n-href]').each(function() {
		let e = $(this);
		let k = e.attr('data-l10n-href');
		let v = l10n_translate(k);
		e.attr('href', v);
	});
	if (node === document) {
		$('html').attr('lang', g_lang);
	}
}

function init() {
	/*
	$('a.l10n').click(function() {
		g_lang = $(this).attr('data-which');
		ls_set('lang', g_lang);
		if (!g_root) {
			window.location.search = '?uilang='+g_lang;
		}
		else {
			l10n_world();
		}
		return false;
	});
	l10n_detectLanguage();
	l10n_world();
	//*/
}

window.addEventListener('load', init);
