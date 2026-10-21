<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include "../../config/i18n.php";
load_lang('settings');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $theme = $_POST['theme'] ?? '';
  $lang = $_POST['lang'] ?? '';

  if (in_array($theme, ['light', 'dark'], true)) {
    $_SESSION['theme'] = $theme;
  }
  if (in_array($lang, ['vi', 'en'], true)) {
    $_SESSION['lang'] = $lang;
  }

  $_SESSION['settings_saved'] = true;
  header('Location: index_gv.php');
  exit;
}

$theme = $_SESSION['theme'] ?? 'light';
$lang = current_lang();

include "../../layout/header_gv.php";
include "../../layout/sidebar_gv.php";
?>

<div class="content">
  <h4 class="fw-semibold text-primary mb-3">
    <i class="bi bi-gear"></i> <?php echo t('settings.title'); ?>
  </h4>

  <?php if (!empty($_SESSION['settings_saved'])): ?>
    <?php unset($_SESSION['settings_saved']); ?>
    <div class="alert alert-success"><?php echo t('settings.saved'); ?></div>
  <?php endif; ?>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <form method="POST">
        <div class="mb-3">
          <label class="form-label"><?php echo t('settings.theme_label'); ?></label>
          <select class="form-select" name="theme">
            <option value="light" <?php echo $theme === 'light' ? 'selected' : ''; ?>>
              <?php echo t('settings.theme_light'); ?>
            </option>
            <option value="dark" <?php echo $theme === 'dark' ? 'selected' : ''; ?>>
              <?php echo t('settings.theme_dark'); ?>
            </option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label"><?php echo t('settings.language_label'); ?></label>
          <select class="form-select" name="lang">
            <option value="vi" <?php echo $lang === 'vi' ? 'selected' : ''; ?>>
              <?php echo t('common.lang_vi'); ?>
            </option>
            <option value="en" <?php echo $lang === 'en' ? 'selected' : ''; ?>>
              <?php echo t('common.lang_en'); ?>
            </option>
          </select>
        </div>

        <button type="submit" class="btn btn-primary">
          <i class="bi bi-save"></i> <?php echo t('settings.save_button'); ?>
        </button>
      </form>
    </div>
  </div>
</div>

<?php include "../../layout/footer.php"; ?>
