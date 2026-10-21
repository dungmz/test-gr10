<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$availableLangs = ['vi', 'en'];
$requestedLang = $_GET['lang'] ?? null;
if ($requestedLang !== null && in_array($requestedLang, $availableLangs, true)) {
  $_SESSION['lang'] = $requestedLang;
}

$lang = $requestedLang && in_array($requestedLang, $availableLangs, true)
  ? $requestedLang
  : ($_SESSION['lang'] ?? 'vi');
$translations = [];
$translateConfig = __DIR__ . '/translate.php';
if (is_file($translateConfig)) {
  require_once $translateConfig;
}

function load_lang(string $domain): void {
  global $lang, $translations;
  $path = __DIR__ . '/../lang/' . $lang . '/' . $domain . '.php';
  if (is_file($path)) {
    $data = require $path;
    if (is_array($data)) {
      $translations = array_merge($translations, $data);
    }
  }
}

load_lang('common');

function t(string $key, array $vars = []): string {
  global $translations;
  $text = $translations[$key] ?? $key;
  if (!$vars) {
    return $text;
  }

  $replace = [];
  foreach ($vars as $name => $value) {
    $replace['{' . $name . '}'] = $value;
  }

  return strtr($text, $replace);
}

function current_lang(): string {
  global $lang;
  return $lang;
}

function lang_url(string $targetLang): string {
  $uri = $_SERVER['REQUEST_URI'] ?? '';
  $parts = parse_url($uri);
  $path = $parts['path'] ?? '';
  $query = [];

  if (!empty($parts['query'])) {
    parse_str($parts['query'], $query);
  }

  $query['lang'] = $targetLang;
  $qs = http_build_query($query);

  return $qs ? $path . '?' . $qs : $path;
}

function translate_cache_text(
  string $entityType,
  string $entityId,
  string $field,
  string $sourceText,
  string $targetLang = 'en',
  string $sourceLang = 'vi'
): string {
  global $conn;

  if (!$conn || $sourceText === '' || $targetLang === $sourceLang) {
    return $sourceText;
  }

  if (!defined('GOOGLE_TRANSLATE_API_KEY') || GOOGLE_TRANSLATE_API_KEY === '') {
    return $sourceText;
  }

  if (!translations_table_exists()) {
    return $sourceText;
  }

  $sourceHash = hash('sha256', $sourceText);
  try {
    $stmt = $conn->prepare(
      "SELECT translated_text, source_hash FROM translations
       WHERE entity_type=? AND entity_id=? AND field=? AND lang=?
       LIMIT 1"
    );
    if ($stmt) {
      $stmt->bind_param('ssss', $entityType, $entityId, $field, $targetLang);
      $stmt->execute();
      $res = $stmt->get_result();
      if ($res && ($row = $res->fetch_assoc())) {
        if (hash_equals($row['source_hash'] ?? '', $sourceHash)) {
          return $row['translated_text'] ?? $sourceText;
        }
      }
    }
  } catch (mysqli_sql_exception $e) {
    return $sourceText;
  }

  $translated = translate_google_text($sourceText, $targetLang, $sourceLang);
  if ($translated === null || $translated === '') {
    return $sourceText;
  }

  try {
    $upsert = $conn->prepare(
      "INSERT INTO translations (entity_type, entity_id, field, lang, source_text, source_hash, translated_text, updated_at)
       VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
       ON DUPLICATE KEY UPDATE
         source_text=VALUES(source_text),
         source_hash=VALUES(source_hash),
         translated_text=VALUES(translated_text),
         updated_at=NOW()"
    );
    if ($upsert) {
      $upsert->bind_param('sssssss', $entityType, $entityId, $field, $targetLang, $sourceText, $sourceHash, $translated);
      $upsert->execute();
    }
  } catch (mysqli_sql_exception $e) {
    return $sourceText;
  }

  return $translated;
}

function translate_google_text(string $text, string $targetLang, string $sourceLang = 'vi'): ?string {
  if (!defined('GOOGLE_TRANSLATE_API_KEY') || GOOGLE_TRANSLATE_API_KEY === '') {
    return null;
  }

  $endpoint = defined('GOOGLE_TRANSLATE_ENDPOINT')
    ? GOOGLE_TRANSLATE_ENDPOINT
    : 'https://translation.googleapis.com/language/translate/v2';
  $url = $endpoint . '?key=' . urlencode(GOOGLE_TRANSLATE_API_KEY);
  $payload = json_encode([
    'q' => $text,
    'target' => $targetLang,
    'source' => $sourceLang,
    'format' => 'text',
  ]);

  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
  $resp = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($resp === false || $httpCode < 200 || $httpCode >= 300) {
    return null;
  }

  $data = json_decode($resp, true);
  $translated = $data['data']['translations'][0]['translatedText'] ?? null;
  if ($translated === null) {
    return null;
  }

  return html_entity_decode($translated, ENT_QUOTES | ENT_HTML5);
}

function t_data(
  string $entityType,
  string $entityId,
  string $field,
  string $sourceText,
  string $sourceLang = 'vi'
): string {
  $lang = current_lang();
  if ($lang === $sourceLang) {
    return $sourceText;
  }

  return translate_cache_text($entityType, $entityId, $field, $sourceText, $lang, $sourceLang);
}

function translations_table_exists(): bool {
  static $exists = null;
  global $conn;

  if ($exists !== null) {
    return $exists;
  }

  if (!$conn) {
    $exists = false;
    return $exists;
  }

  try {
    $res = $conn->query("SHOW TABLES LIKE 'translations'");
    $exists = $res && $res->num_rows > 0;
  } catch (mysqli_sql_exception $e) {
    $exists = false;
  }

  return $exists;
}
