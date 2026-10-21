"""Kiểm thử đăng nhập: giao diện (Selenium) và máy chủ (requests). Ứng với TC-LG-01..15."""
import pytest
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select, WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from conftest import BASE_URL, ACCOUNTS, PHP_ERRORS, post

# (id, email, mật khẩu, vai trò, kết quả mong đợi, trang đích)
UI_CASES = [
    ("LG-01", *ACCOUNTS["admin"],     "admin",     "success", "dashboard_admin.php"),
    ("LG-02", *ACCOUNTS["giangvien"], "giangvien", "success", "dashboard_gv.php"),
    ("LG-03", *ACCOUNTS["sinhvien"],  "sinhvien",  "success", "dashboard_sv.php"),
    ("LG-04", "admin@ycp.edu.vn", "654321", "admin", "error", None),
    ("LG-05", "none@ycp.edu.vn",  "123456", "admin", "error", None),
    ("LG-06", "admin@ycp.edu.vn", "123456", "giangvien", "error", None),
    ("LG-07", "", "123456", "admin", "blocked", None),
    ("LG-08", "admin@ycp.edu.vn", "", "admin", "blocked", None),
    ("LG-09", "admin@ycp.edu.vn", "123456", "", "blocked", None),
    ("LG-10", "admin", "123456", "admin", "blocked", None),
    ("LG-14", "admin@ycp.edu.vn", " 123456 ", "admin", "success", "dashboard_admin.php"),
    ("LG-15", "ADMIN@YCP.EDU.VN", "123456", "admin", "success", "dashboard_admin.php"),
]


@pytest.mark.parametrize("tc,email,pw,role,expect,target", UI_CASES, ids=[c[0] for c in UI_CASES])
def test_login_ui(driver, tc, email, pw, role, expect, target):
    driver.get(BASE_URL + "login.php")
    driver.find_element(By.NAME, "email").send_keys(email)
    driver.find_element(By.NAME, "password").send_keys(pw)
    if role:
        Select(driver.find_element(By.NAME, "role")).select_by_value(role)
    driver.find_element(By.CSS_SELECTOR, "button[type=submit]").click()

    if expect == "blocked":                       # trình duyệt chặn nhờ thuộc tính required / type=email
        assert driver.find_elements(By.CSS_SELECTOR, ".swal2-popup") == []
        assert "login.php" in driver.current_url
        return
    wait = WebDriverWait(driver, 8)
    wait.until(EC.visibility_of_element_located((By.CSS_SELECTOR, ".swal2-popup")))
    icon = "swal2-icon-success" if expect == "success" else "swal2-icon-error"
    assert driver.find_elements(By.CSS_SELECTOR, "." + icon)
    if target:
        driver.find_element(By.CSS_SELECTOR, ".swal2-confirm").click()
        wait.until(EC.url_contains(target))


def test_lg11_server_missing_email(anon):               # bỏ qua kiểm tra phía trình duyệt
    r = post(anon, "login.php", email="", password="123456", role="admin")
    assert "swal2" in r.text or "Swal" in r.text
    assert "dashboard_admin.php" not in r.text


def test_lg12_invalid_role(anon):
    r = post(anon, "login.php", email="admin@ycp.edu.vn", password="123456", role="superuser")
    assert "dashboard_" not in r.text
    assert not any(e in r.text for e in PHP_ERRORS)


def test_lg13_sql_injection(anon):
    r = post(anon, "login.php", email="' OR '1'='1' -- ", password="x", role="admin")
    assert "dashboard_admin.php" not in r.text
    assert not any(e in r.text for e in PHP_ERRORS)
