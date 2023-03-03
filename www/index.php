<?php
require_once __DIR__.'/_inc/l10n.php';
l10n_setUILang();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{l10n:SITE_TITLE}</title>

	<link rel="shortcut icon" href="_static/favicon.png">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Gudea%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CRoboto%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic&#038;ver=5.5.3" type="text/css" media="all" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8/font/bootstrap-icons.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="_static/kukkuniiaat.css?<?=filemtime(__DIR__.'/_static/kukkuniiaat.css');?>">
	<link rel="alternate" hreflang="da" href="https://kukkuniiaat.gl/?ui=da">
	<link rel="alternate" hreflang="kl" href="https://kukkuniiaat.gl/?ui=kl">
	<link rel="alternate" hreflang="en" href="https://kukkuniiaat.gl/?ui=en">
	<link rel="alternate" hreflang="x-default" href="https://kukkuniiaat.gl/">
	<script src="https://cdn.jsdelivr.net/npm/jquery@3.6/dist/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1/dist/js/bootstrap.bundle.min.js"></script>
	<script src="_static/l10n.js?<?=filemtime(__DIR__.'/_static/l10n.js');?>"></script>
	<script src="_static/kukkuniiaat.js?<?=filemtime(__DIR__.'/_static/kukkuniiaat.js');?>"></script>
</head>
<body class="d-flex flex-column">

<header>
	<div class="container">
	<div class="logo">
		<a href="https://oqaasileriffik.gl/" class="text-decoration-none">
		<h1>{l10n:HDR_TITLE}</h1>
		<h3>{l10n:HDR_SUBTITLE}</h3>
		</a>
	</div>
	</div>

	<div class="menu">
	<div class="container">
		<div class="lang-select">
			<a class="dropdown text-decoration-none fs-5" id="dropLanguages" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false"><i class="bi bi-globe2"></i></a>
			<ul class="dropdown-menu" aria-labelledby="dropLanguages">
				<li><a href="?ui=kl" class="item l10n" data-which="kl" title="Kalaallisut"><tt>KAL</tt> <span>Kalaallisut</span></a></li>
				<li><a href="?ui=da" class="item l10n" data-which="da" title="Dansk"><tt>DAN</tt> <span>Dansk</span></a></li>
				<li><a href="?ui=en" class="item l10n" data-which="en" title="English"><tt>ENG</tt> <span>English</span></a></li>
			</ul>
		</div>
	</div>
	</div>
</header>

<div class="container flex-grow-1">

<div class="row">
<div class="col text-center my-3">
<a href="./" class="text-decoration-none">
<h1 class="title">{l10n:SITE_PRODUCT}</h1>
<h4>{l10n:SITE_SUBTITLE}</h4>
</a>
</div>
</div>

<?php
if (strpos($_SERVER['REQUEST_URI'], '/terms') !== false) {
	require_once __DIR__.'/_pages/terms.php';
}
else if (strpos($_SERVER['REQUEST_URI'], '/help') !== false) {
	require_once __DIR__.'/_pages/help.php';
}
else {
	require_once __DIR__.'/_pages/index.php';
}
?>

</div>

<footer>
	<div class="container footer">
		<section class="row main-footer">
			<div class="col">
				<div class="footer-title">
				<h2>{l10n:FTR_CONTACT}</h2>
				</div>
				<div class="row flex-nowrap mb-2">
					<div class="col-auto pr-0"><i aria-hidden="true" class="bi bi-envelope-fill"></i></div>
					<div class="col nowrap"><a href="mailto:oqaasileriffik@oqaasileriffik.gl" class="text-decoration-none">oqaasileriffik@oqaasileriffik.gl</a></div>
				</div>
				<div class="row flex-nowrap mb-2">
					<div class="col-auto pr-0"><i aria-hidden="true" class="bi bi-telephone-fill"></i></div>
					<div class="col nowrap"><a href="tel:+299384060" class="text-decoration-none">(+299) 38 40 60</a></div>
				</div>
				<div class="row flex-nowrap">
					<div class="col-auto pr-0"><i aria-hidden="true" class="bi bi-geo-alt-fill"></i></div>
					<div class="col"><a href="https://www.google.com/maps?q=Oqaasileriffik,%20Nuuk" class="text-decoration-none">Ceresvej 7-1<br>Postboks 980<br>3900 Nuuk<br>Kalaallit Nunaat</a></div>
				</div>
			</div>

			<div class="col">
				<div class="footer-title">
				<h2>{l10n:FTR_HOURS}</h2>
				</div>
				<div class="row mb-2">
					<div class="col">{l10n:FTR_MON_FRI}</div>
					<div class="col">8:00 - 16:00</div>
				</div>
				<div class="row text-orange">
					<div class="col">{l10n:FTR_SAT_SUN}</div>
					<div class="col">{l10n:FTR_CLOSED}</div>
				</div>
			</div>

			<div class="col-auto">
				<div class="footer-title">
				<h2>{l10n:FTR_NEWS}</h2>
				</div>
				<div class="row mb-4">
					<div class="col">{l10n:FTR_NEWS_TEXT}</div>
				</div>
				<a role="button" class="btn btn-outline-secondary" href="https://groups.google.com/a/oqaasileriffik.gl/forum/#!forum/news/join" target="_blank" rel="noopener">
					<div class="row flex-nowrap">
							<div class="col-auto pr-0"><i aria-hidden="true" class="bi bi-envelope"></i></div>
							<div class="col">{l10n:FTR_NEWS_BUTTON}</div>
					</div>
				</a>
			</div>
		</section>
	</div>
	<div class="footer-line">
	</div>
	<div class="footer copyright text-center">
		<section>
			<div><span class="copyr">©</span> 2022 <span class="sep">|</span> Oqaasileriffik</div>
		</section>
	</div>
</footer>

<script async src="https://www.googletagmanager.com/gtag/js?id=G-6GQ1WRMFZ6"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-6GQ1WRMFZ6');
</script>

<script>
  var _paq = window._paq = window._paq || [];
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//oqaasileriffik.gl/matomo/";
    _paq.push(['setTrackerUrl', u+'matomo.php']);
    _paq.push(['setSiteId', '4']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
  })();
</script>

</body>
</html>
