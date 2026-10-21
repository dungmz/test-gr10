# Bộ mã kiểm thử – Hệ thống Quản lý chương trình đào tạo

## Cài đặt
pip install -r requirements.txt   (cần Google Chrome cho Selenium)

## Chạy
set BASE_URL=http://localhost/KTPM2/KTPM/        (Windows; Linux/macOS dùng export)
pytest . -v --junitxml=ket_qua.xml                (toàn bộ test tự động)
python random_fuzz.py --n 300 --seed 2026         (sinh test ngẫu nhiên -> fuzz_report.csv)
python coverage/merge_coverage.py                 (đo độ bao phủ sau khi bật Xdebug + coverage/prepend.php)

## Tệp
conftest.py                              cấu hình chung, đăng nhập theo vai trò, trình duyệt headless
test_login.py                            LG-01..LG-15 (Selenium + HTTP)
test_faculty.py                          KH-01..KH-08, KH-10..KH-12
test_course_tuition_registration.py      HP, HF, PQ, DK-02
random_fuzz.py                           sinh test ngẫu nhiên có oracle O1-O3
coverage/prepend.php, merge_coverage.py  đo độ bao phủ bằng Xdebug

Lưu ý: nhập lại database.sql trước mỗi lượt chạy để dữ liệu ban đầu giống nhau.
