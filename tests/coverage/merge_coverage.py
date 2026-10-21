"""Gộp các tệp độ phủ do prepend.php ghi ra và tính % câu lệnh (line coverage) cho 6 tệp mục tiêu.

Chạy:  COV_DIR=C:/xampp/tmp/ktpm_cov python merge_coverage.py
Quy ước Xdebug: 1 = dòng đã chạy, -1 = dòng thực thi được nhưng chưa chạy, -2 = mã chết (bỏ qua).
"""
import collections, glob, json, os

COV_DIR = os.environ.get("COV_DIR", "C:/xampp/tmp/ktpm_cov")
TARGETS = ["login.php", "modules/faculties/save.php", "modules/programs/save.php",
           "modules/employees/create.php", "modules/programs/update_tuition.php",
           "modules/dangkyhocphan/save_registration.php"]

lines_by_file = collections.defaultdict(dict)
for fn in glob.glob(os.path.join(COV_DIR, "*.json")):
    with open(fn, encoding="utf-8") as f:
        data = json.load(f)
    for path, lines in data.items():
        p = path.replace("\\", "/")
        for t in TARGETS:
            if p.endswith("/" + t):
                for ln, st in lines.items():
                    cur = lines_by_file[t].get(int(ln))
                    if cur is None or st == 1 or (st == -1 and cur != 1):
                        lines_by_file[t][int(ln)] = st

print(f"{'Tệp':48}{'Dòng chạy':>10}{'Tổng dòng':>10}{'%':>8}")
tot_hit = tot_all = 0
for t in TARGETS:
    d = lines_by_file.get(t, {})
    hit = sum(1 for s in d.values() if s == 1)
    allr = sum(1 for s in d.values() if s in (1, -1))
    tot_hit += hit; tot_all += allr
    print(f"{t:48}{hit:>10}{allr:>10}{(100 * hit / allr if allr else 0):>8.1f}")
print(f"{'TỔNG':48}{tot_hit:>10}{tot_all:>10}{(100 * tot_hit / tot_all if tot_all else 0):>8.1f}")
