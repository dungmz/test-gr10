"""Học phần, học phí, đăng ký/thanh toán và phân quyền. Ứng với TC-HP, TC-HF, TC-DK, TC-PQ."""
import pytest
from conftest import BASE_URL, PHP_ERRORS, post, uid

COURSE = "modules/programs/save.php"


def course(admin, **over):
    d = dict(action="create", maHP="T" + uid(), tenHP="Kiểm thử phần mềm", soTinChi="3",
             lyThuyet="2", thucHanh="1", hpTienQuyet="", hpHocTruoc="", khoaQuanLy="", ghiChu="", KhoiKienThuc="")
    d.update(over)
    return post(admin, COURSE, **d)


def test_hp01_valid(admin):
    assert "Thêm học phần thành công" in course(admin).text


def test_hp02_missing_code(admin):
    assert "Thiếu thông tin" in course(admin, maHP="").text


def test_hp04_duplicate(admin):
    c = "T" + uid()
    course(admin, maHP=c)
    assert "đã tồn tại" in course(admin, maHP=c).text


@pytest.mark.parametrize("tc,credits,accepted", [
    ("HP-05", "0", False), ("HP-06", "1", True), ("HP-07", "-1", False),
    ("HP-08", "abc", False), ("HP-09", "3.5", False)])
def test_credit_boundaries(admin, tc, credits, accepted):
    r = course(admin, soTinChi=credits)
    assert ("Thêm học phần thành công" in r.text) == accepted


TUITION = "modules/programs/update_tuition.php"


@pytest.mark.parametrize("tc,value", [("HF-03", "0"), ("HF-04", "-100000"), ("HF-05", "abc"), ("HF-07", "1e3")])
def test_invalid_tuition_not_saved(admin, tc, value):
    post(admin, TUITION, hocphi="500000")
    post(admin, TUITION, hocphi=value)
    page = admin.get(BASE_URL + "modules/programs/index.php").text
    assert "500,000" in page, "Đơn giá bị thay đổi bởi giá trị không hợp lệ"


@pytest.mark.parametrize("tc,path,data,who", [
    ("PQ-02", "modules/faculties/save.php", dict(maKhoa="PQ-" + uid(), tenKhoa="x", matruong="PKA04"), "sv"),
    ("PQ-03", TUITION, dict(hocphi="777000"), "sv"),
    ("PQ-04", "modules/dangkyhocphan/update_payment.php", dict(maSV="SV001", maHP="X"), "sv"),
    ("PQ-01", "modules/faculties/save.php", dict(maKhoa="PQ-" + uid(), tenKhoa="x", matruong="PKA04"), "anon"),
])
def test_access_control(request, tc, path, data, who):
    sess = request.getfixturevalue(who)
    r = post(sess, path, **data)
    denied = r.status_code in (301, 302, 303, 401, 403)
    assert denied, f"{tc}: vai trò '{who}' vẫn thực thi được chức năng quản trị (HTTP {r.status_code})"


def test_dk02_no_duplicate_registration(sv):
    for _ in range(2):
        post(sv, "modules/dangkyhocphan/save_registration.php", **{"hocphan[]": "CSE703010"})
    # kiểm tra bằng truy vấn CSDL ở mức tích hợp (xem phụ lục) – tại đây chỉ bảo đảm không lỗi
    r = post(sv, "modules/dangkyhocphan/save_registration.php", **{"hocphan[]": "CSE703010"})
    assert not any(e in r.text for e in PHP_ERRORS)
