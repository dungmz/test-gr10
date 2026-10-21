"""Sinh test ngẫu nhiên có kiểm soát (seed cố định) cho các biểu mẫu Khoa và Học phần.

Chạy:  python random_fuzz.py --n 300 --seed 2026
Đầu ra: fuzz_report.csv (các ca vi phạm oracle) + tóm tắt trên màn hình.
Oracle:  (O1) phản hồi không chứa lỗi PHP/MySQL thô;
         (O2) dữ liệu số âm/0/không phải số nguyên không được 'thành công';
         (O3) chuỗi vượt giới hạn cột không được 'thành công'.
"""
import argparse, csv, random, string
import requests
from conftest import BASE_URL, ACCOUNTS, PHP_ERRORS

VN = "ĂÂĐÊÔƠƯáàảãạăâđêôơưýỳỷỹỵ"
SPECIAL = ["' OR '1'='1", "\"; DROP TABLE faculties;--", "<script>alert(1)</script>", "%s%d", "\\", "\u0000", "💥", "   "]


def rand_text(rng, max_len):
    kind = rng.choice(["empty", "short", "boundary", "unicode", "special", "long"])
    if kind == "empty":
        return ""
    if kind == "special":
        return rng.choice(SPECIAL)
    if kind == "boundary":
        n = rng.choice([max_len - 1, max_len, max_len + 1])
    elif kind == "long":
        n = max_len * 3
    else:
        n = rng.randint(1, 20)
    alphabet = string.ascii_letters + string.digits + (VN if kind == "unicode" else "")
    return "".join(rng.choice(alphabet) for _ in range(max(n, 0)))


def rand_number(rng):
    return rng.choice([str(rng.randint(-5, 20)), "0", "-1", "3.5", "abc", "", "1e3", str(2**31), str(-2**31 - 1), " 4 "])


def login(role="admin"):
    s = requests.Session()
    e, p = ACCOUNTS[role]
    s.post(BASE_URL + "login.php", data={"email": e, "password": p, "role": role})
    return s


def run(n, seed):
    rng = random.Random(seed)
    s = login()
    rows = []
    for i in range(n):
        if rng.random() < 0.5:                                    # biểu mẫu Khoa
            data = dict(maKhoa=rand_text(rng, 50), tenKhoa=rand_text(rng, 255),
                        matruong=rng.choice(["PKA04", "PKA01", "XXX", ""]))
            path, kind = "modules/faculties/save.php", "khoa"
        else:                                                    # biểu mẫu Học phần
            data = dict(action="create", maHP=rand_text(rng, 50), tenHP=rand_text(rng, 255),
                        soTinChi=rand_number(rng), lyThuyet=rand_number(rng), thucHanh=rand_number(rng),
                        hpTienQuyet="", hpHocTruoc="", khoaQuanLy="", ghiChu="", KhoiKienThuc="")
            path, kind = "modules/programs/save.php", "hocphan"
        r = s.post(BASE_URL + path, data=data, allow_redirects=False, timeout=15)
        txt = r.text
        ok = ("Đã thêm Khoa mới" in txt) or ("Thêm học phần thành công" in txt)
        violations = []
        if any(e in txt for e in PHP_ERRORS) or r.status_code >= 500:
            violations.append("O1:lộ lỗi PHP/MySQL hoặc HTTP 5xx")
        if kind == "hocphan" and ok:
            try:
                if int(data["soTinChi"].strip()) <= 0:
                    violations.append("O2:tín chỉ <= 0 vẫn được lưu")
            except ValueError:
                violations.append("O2:tín chỉ không phải số nguyên vẫn được lưu")
        if ok and (len(data.get("maKhoa", data.get("maHP", ""))) > 50 or len(data.get("tenKhoa", data.get("tenHP", ""))) > 255):
            violations.append("O3:vượt giới hạn cột vẫn được lưu")
        if violations:
            rows.append(dict(stt=i, loai=kind, du_lieu=repr(data), vi_pham="; ".join(violations)))
    return rows


if __name__ == "__main__":
    ap = argparse.ArgumentParser()
    ap.add_argument("--n", type=int, default=300)
    ap.add_argument("--seed", type=int, default=2026)
    a = ap.parse_args()
    bad = run(a.n, a.seed)
    with open("fuzz_report.csv", "w", newline="", encoding="utf-8") as f:
        w = csv.DictWriter(f, fieldnames=["stt", "loai", "du_lieu", "vi_pham"])
        w.writeheader(); w.writerows(bad)
    print(f"Đã chạy {a.n} ca (seed={a.seed}); số ca vi phạm oracle: {len(bad)}")
