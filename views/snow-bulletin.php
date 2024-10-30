<?php
	namespace Webcloud\SnowBulletin;

	$options = get_option("webcloud_snow_bulletin_options", null);
	$apiKey = $options["api_key"];
	$displayAll = $options["mostratutto_default"] === "on" ? "0" : "1";
?>


<?php if (strlen($apiKey) === 24 AND strtolower($apikey) !== $apiKey): ?>
	<script src="https://browser.sentry-cdn.com/5.5.0/bundle.min.js" crossorigin="anonymous"></script>
	<script>
		Sentry.init({ dsn: 'https://c4f7105362834ecf95543ee5c2978ee1@sentry.webcloud.it/10' });
		Sentry.captureMessage('Using an rw key where a read-only key is required: ' + window.location.hostname, 'fatal')
	</script>
<?php else: ?>
	<div id="wc-snowbulletin" data-display-all="<?=$displayAll;?>"></div>
	<script src="https://www.asiago.it/snowbulletinviewer/code/<?=urlencode($apiKey);?>/"></script>
<?php endif ?>
