<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/i18n.php';

if (!isset($conn)) {
  echo "Database connection not available.";
  exit;
}

set_time_limit(0);

$targetLang = 'en';
$sourceLang = 'vi';

$targets = [
  ['entity' => 'schools', 'table' => 'schools', 'id' => 'matruong', 'fields' => ['name']],
  ['entity' => 'faculties', 'table' => 'faculties', 'id' => 'maKhoa', 'fields' => ['tenKhoa']],
  ['entity' => 'majors', 'table' => 'majors', 'id' => 'maNganh', 'fields' => ['tenNganh']],
  ['entity' => 'employees', 'table' => 'employees', 'id' => 'stt', 'fields' => ['tenNV', 'chucVu']],
  ['entity' => 'phongban', 'table' => 'phongban', 'id' => 'maPB', 'fields' => ['tenPB', 'diaChi']],
  ['entity' => 'courses', 'table' => 'courses', 'id' => 'maHP', 'fields' => ['tenHP', 'ghiChu', 'KhoiKienThuc']],
];

$total = 0;
$translatedCount = 0;

foreach ($targets as $t) {
  $cols = array_merge([$t['id']], $t['fields']);
  $sql = 'SELECT ' . implode(',', $cols) . ' FROM ' . $t['table'];
  $res = $conn->query($sql);
  if (!$res) {
    echo "Skip {$t['table']}: query failed.\n";
    continue;
  }

  while ($row = $res->fetch_assoc()) {
    $entityId = (string)$row[$t['id']];
    foreach ($t['fields'] as $field) {
      $total++;
      $value = (string)($row[$field] ?? '');
      if ($value === '') {
        continue;
      }
      $translated = translate_cache_text($t['entity'], $entityId, $field, $value, $targetLang, $sourceLang);
      if ($translated !== $value) {
        $translatedCount++;
      }
    }
  }
}

echo "Backfill complete. Fields processed: {$total}. Translated: {$translatedCount}.\n";
