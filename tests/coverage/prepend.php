<?php
// Cấu hình php.ini (XAMPP):  zend_extension=xdebug ; xdebug.mode=coverage ; auto_prepend_file="<đường dẫn>/tests/coverage/prepend.php"
// Mỗi request PHP sẽ ghi một tệp JSON độ phủ vào COV_DIR; merge_coverage.py gộp lại sau khi chạy bộ test.
const COV_DIR = 'C:/xampp/tmp/ktpm_cov';          // chỉnh cho khớp với COV_DIR của merge_coverage.py
if (function_exists('xdebug_start_code_coverage')) {
    xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
    register_shutdown_function(function () {
        $cov = xdebug_get_code_coverage();
        xdebug_stop_code_coverage();
        if (!is_dir(COV_DIR)) { @mkdir(COV_DIR, 0777, true); }
        file_put_contents(COV_DIR . '/cov_' . uniqid('', true) . '.json', json_encode($cov));
    });
}
