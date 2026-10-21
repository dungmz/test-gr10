"""Cấu hình dùng chung cho bộ test tự động (pytest + Selenium + requests).

Biến môi trường:
  BASE_URL  địa chỉ gốc của ứng dụng, mặc định http://localhost/KTPM2/KTPM/
"""
import os
import uuid
import pytest
import requests

BASE_URL = os.environ.get("BASE_URL", "http://localhost/KTPM2/KTPM/").rstrip("/") + "/"
ACCOUNTS = {
    "admin":     ("admin@ycp.edu.vn", "123456"),
    "giangvien": ("gv@ycp.edu.vn", "123456"),
    "sinhvien":  ("sv@ycp.edu.vn", "123456"),
}
PHP_ERRORS = ("Fatal error", "Warning:", "Notice:", "Deprecated:",
              "mysqli_sql_exception", "Stack trace")


def uid(n=8):
    return uuid.uuid4().hex[:n].upper()


def login_session(role):
    """Trả về requests.Session đã đăng nhập theo vai trò (giữ cookie PHPSESSID)."""
    s = requests.Session()
    email, pw = ACCOUNTS[role]
    s.post(BASE_URL + "login.php", data={"email": email, "password": pw, "role": role})
    return s


def post(session, path, **data):
    return session.post(BASE_URL + path, data=data, allow_redirects=False, timeout=15)


@pytest.fixture(scope="session")
def admin():
    return login_session("admin")


@pytest.fixture(scope="session")
def sv():
    return login_session("sinhvien")


@pytest.fixture(scope="session")
def gv():
    return login_session("giangvien")


@pytest.fixture(scope="session")
def anon():
    return requests.Session()


@pytest.fixture()
def driver():
    from selenium import webdriver
    from selenium.webdriver.chrome.options import Options
    opts = Options()
    opts.add_argument("--headless=new")
    opts.add_argument("--window-size=1366,900")
    d = webdriver.Chrome(options=opts)
    d.implicitly_wait(2)
    yield d
    d.quit()
