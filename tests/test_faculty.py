"""Kiểm thử chức năng Khoa (modules/faculties/save.php). Ứng với TC-KH-01..13."""
import pytest
from conftest import BASE_URL, PHP_ERRORS, post, uid

SAVE = "modules/faculties/save.php"
OK_SCHOOL = "PKA04"          # mã trường có sẵn trong dữ liệu mẫu


def code(n):                  # sinh mã khoa dài đúng n ký tự và không trùng
    return ("K" * n)[: n - 8] + uid(8)


def test_kh01_add_valid(admin):
    r = post(admin, SAVE, maKhoa="T-" + uid(), tenKhoa="Khoa kiểm thử", matruong=OK_SCHOOL)
    assert "Đã thêm Khoa mới" in r.text


def test_kh02_update_existing(admin):
    c = "T-" + uid()
    post(admin, SAVE, maKhoa=c, tenKhoa="Tên cũ", matruong=OK_SCHOOL)
    r = post(admin, SAVE, maKhoa=c, tenKhoa="Tên mới", matruong=OK_SCHOOL)
    assert "Đã cập nhật" in r.text


@pytest.mark.parametrize("tc,data", [
    ("KH-03", dict(maKhoa="",    tenKhoa="Khoa A", matruong=OK_SCHOOL)),
    ("KH-04", dict(maKhoa="T-1", tenKhoa="",       matruong=OK_SCHOOL)),
    ("KH-05", dict(maKhoa="T-2", tenKhoa="Khoa A", matruong="")),
    ("KH-06", dict(maKhoa="   ", tenKhoa="Khoa A", matruong=OK_SCHOOL)),
], ids=lambda x: x if isinstance(x, str) else "")
def test_missing_fields(admin, tc, data):
    r = post(admin, SAVE, **data)
    assert "Thiếu thông tin" in r.text


def test_kh07_code_50_chars(admin):
    r = post(admin, SAVE, maKhoa=code(50), tenKhoa="Biên 50", matruong=OK_SCHOOL)
    assert "Đã thêm Khoa mới" in r.text


@pytest.mark.parametrize("tc,data", [
    ("KH-08", dict(maKhoa=code(51),  tenKhoa="Biên 51", matruong=OK_SCHOOL)),
    ("KH-10", dict(maKhoa="T-" + uid(), tenKhoa="N" * 256, matruong=OK_SCHOOL)),
    ("KH-11", dict(maKhoa="T-" + uid(), tenKhoa="Sai trường", matruong="XXX")),
], ids=lambda x: x if isinstance(x, str) else "")
def test_rejected_without_crash(admin, tc, data):
    r = post(admin, SAVE, **data)
    assert "Đã thêm Khoa mới" not in r.text
    assert not any(e in r.text for e in PHP_ERRORS), "Hệ thống lộ lỗi PHP thay vì thông báo thân thiện"


def test_kh12_html_is_escaped(admin):
    c = "T-" + uid()
    post(admin, SAVE, maKhoa=c, tenKhoa="<b>đậm</b>", matruong=OK_SCHOOL)
    page = admin.get(BASE_URL + "modules/faculties/index.php?q=" + c).text
    assert "<b>đậm</b>" not in page
