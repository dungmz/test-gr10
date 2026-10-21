# Huong dan chay du an

## Yeu cau
- XAMPP (Apache, MySQL)
- PHP 7.4+ (khuyen nghi 8.x)
- Trinh duyet web

## Cai dat
1. Sao chep thu muc du an vao `C:\xampp\htdocs\KTPM2\KTPM`.
2. Mo XAMPP Control Panel, bat Apache va MySQL.

## Tao co so du lieu
1. Mo `http://localhost/phpmyadmin`.
2. Tao database ten `ktpm` (co the bo qua neu da co).
3. Import file [database.sql](database.sql) vao database `ktpm`.

## Cau hinh ket noi
- Mo [config/db.php](config/db.php) va dam bao cac thong tin:
  - host: `localhost`
  - user: `root`
  - password: (rong neu mac dinh XAMPP)
	- database: `ktpm`

## Gan API key (neu co)
- Khong cong khai API key len Git hoac noi dung chia se cong cong.
- Luu API key trong bien moi truong hoac file cau hinh rieng va them vao `.gitignore`.

### Cach 1: Dat bien moi truong qua .htaccess (XAMPP)
1. Tao file `.htaccess` trong thu muc goc du an (neu chua co).
2. Them dong sau:
	- `SetEnv APP_API_KEY your_key_here`
3. Trong PHP, doc gia tri:
	- `getenv('APP_API_KEY')`

### Cach 2: Dat bien moi truong trong Apache (httpd.conf hoac vhost)
1. Mo file cau hinh Apache (vd: `C:\xampp\apache\conf\httpd.conf` hoac file vhost).
2. Them dong:
	- `SetEnv APP_API_KEY your_key_here`
3. Restart Apache va doc bang `getenv('APP_API_KEY')`.

### Cach 3: Dung file rieng (vi du .env)
1. Tao file `.env` o thu muc goc du an va them vao `.gitignore`.
2. Them dong:
	- `APP_API_KEY=your_key_here`
3. Doc file `.env` trong PHP (tu viet hoac dung thu vien) va gan vao bien moi truong.

## Chay ung dung
- Truy cap `http://localhost/KTPM2/KTPM/login.php`

## Tai khoan mau
- Admin: `admin@ycp.edu.vn` / `123456`
- Giang vien: `gv@ycp.edu.vn` / `123456`
- Sinh vien: `sv@ycp.edu.vn` / `123456`

## Ghi chu
- Neu bi loi quyen ghi, hay dam bao thu muc du an duoc phep doc/ghi tren Windows.
