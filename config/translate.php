<?php
// Google Cloud Translation API settings.
// Set GOOGLE_TRANSLATE_API_KEY in the environment or in a local .env file.

$envPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';
if (file_exists($envPath)) {
	$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	foreach ($lines as $line) {
		$line = trim($line);
		if ($line === '' || $line[0] === '#') {
			continue;
		}
		$parts = explode('=', $line, 2);
		if (count($parts) !== 2) {
			continue;
		}
		$key = trim($parts[0]);
		$value = trim($parts[1]);
		$value = trim($value, "\"'");
		if ($key === '' || array_key_exists($key, $_ENV)) {
			continue;
		}
		$_ENV[$key] = $value;
		putenv($key . '=' . $value);
	}
}

$apiKey = getenv('GOOGLE_TRANSLATE_API_KEY') ?: '';
define('GOOGLE_TRANSLATE_API_KEY', $apiKey);
define('GOOGLE_TRANSLATE_ENDPOINT', 'https://translation.googleapis.com/language/translate/v2');
