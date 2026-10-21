<?php
session_start();
include "config/i18n.php";
load_lang('login');
include "config/db.php";
?>
<!DOCTYPE html>
<html lang="<?php echo current_lang(); ?>">
<head>
  <meta charset="UTF-8">
  <title><?php echo t('login.title'); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body {
      background: linear-gradient(135deg, #0d6efd, #4dabf7);
      font-family: "Poppins", sans-serif;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-card {
      background-color: #fff;
      border-radius: 20px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      padding: 40px;
      width: 380px;
    }

    .login-card h4 {
      text-align: center;
      color: #0d6efd;
      font-weight: 600;
      margin-bottom: 30px;
    }

    .form-control, .form-select {
      border-radius: 10px;
    }

    .btn-login {
      width: 100%;
      border-radius: 10px;
      font-weight: 500;
      padding: 10px;
    }

    .toggle-password {
      position: absolute;
      right: 15px;
      top: 38px;
      cursor: pointer;
      color: #888;
    }
  </style>
</head>
<body>

<div class="login-card">
  <div class="d-flex justify-content-end mb-2">
    <a class="small text-decoration-none me-2" href="<?= lang_url('vi') ?>"><?= t('common.lang_vi') ?></a>
    <a class="small text-decoration-none" href="<?= lang_url('en') ?>"><?= t('common.lang_en') ?></a>
  </div>

  <h4><i class="bi bi-mortarboard"></i> <?= t('login.title') ?></h4>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label"><?= t('login.email_label') ?></label>
      <input type="email" name="email" class="form-control" placeholder="<?= t('login.email_placeholder') ?>" required>
    </div>

    <div class="mb-3 position-relative">
      <label class="form-label"><?= t('login.password_label') ?></label>
      <input type="password" name="password" id="password" class="form-control" placeholder="<?= t('login.password_placeholder') ?>" required>
      <i class="bi bi-eye-slash toggle-password" id="togglePassword"></i>
    </div>

    <div class="mb-3">
      <label class="form-label"><?= t('login.role_label') ?></label>
      <select name="role" class="form-select" required>
        <option value=""><?= t('login.role_placeholder') ?></option>
        <option value="admin"><?= t('login.role_admin') ?></option>
        <option value="giangvien"><?= t('login.role_lecturer') ?></option>
        <option value="sinhvien"><?= t('login.role_student') ?></option>
      </select>
    </div>

    <button type="submit" class="btn btn-primary btn-login">
      <i class="bi bi-box-arrow-in-right"></i> <?= t('login.submit') ?>
    </button>
  </form>
</div>

<script>
  // Hiện / Ẩn mật khẩu
  const togglePassword = document.getElementById("togglePassword");
  const password = document.getElementById("password");
  togglePassword.addEventListener("click", function() {
    const type = password.getAttribute("type") === "password" ? "text" : "password";
    password.setAttribute("type", type);
    this.classList.toggle("bi-eye");
    this.classList.toggle("bi-eye-slash");
  });
</script>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = trim($_POST['role'] ?? '');

    if ($email !== '' && $password !== '' && $role !== '') {
        switch ($role) {
            case "admin":
                $table = "admin";
                break;
            case "giangvien":
                $table = "giangvien";
                break;
            case "sinhvien":
                $table = "sinhvien";
                break;
            default:
                $table = "";
        }

        if ($table !== "") {
            $stmt = $conn->prepare("SELECT * FROM $table WHERE email = ? AND password = ?");
            $stmt->bind_param("ss", $email, $password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $_SESSION['role'] = $role;
                $_SESSION['user'] = $result->fetch_assoc();


                switch ($role) {
                    case "admin":
                        $redirect = "dashboard_admin.php";
                        break;
                    case "giangvien":
                        $redirect = "dashboard_gv.php";
                        break;
                    case "sinhvien":
                        $redirect = "dashboard_sv.php";
                        break;
                }

               echo "<script>\n" .
                    "Swal.fire({" .
                    "icon: 'success'," .
                    "title: " . json_encode(t('login.success_title')) . "," .
                    "text: " . json_encode(t('login.success_text')) . "," .
                    "confirmButtonText: " . json_encode(t('login.success_confirm')) . "," .
                    "confirmButtonColor: '#0d6efd'" .
                    "}).then(() => { window.location = " . json_encode($redirect) . "; });" .
                    "</script>";

            } else {
                echo "<script>" .
                     "Swal.fire({" .
                     "icon: 'error'," .
                     "title: " . json_encode(t('login.error_invalid_title')) . "," .
                     "text: " . json_encode(t('login.error_invalid_text')) . "," .
                     "confirmButtonText: " . json_encode(t('login.error_invalid_confirm')) .
                     "});" .
                     "</script>";
            }
        } else {
            echo "<script>" .
                 "Swal.fire({" .
                 "icon: 'warning'," .
                 "title: " . json_encode(t('login.error_role_title')) . "," .
                 "text: " . json_encode(t('login.error_role_text')) . "," .
                 "confirmButtonText: " . json_encode(t('login.error_role_confirm')) .
                 "});" .
                 "</script>";
        }
    } else {
        echo "<script>" .
             "Swal.fire({" .
             "icon: 'warning'," .
             "title: " . json_encode(t('login.error_missing_title')) . "," .
             "text: " . json_encode(t('login.error_missing_text')) . "," .
             "confirmButtonText: " . json_encode(t('login.error_missing_confirm')) .
             "});" .
             "</script>";
    }
}
?>

</body>
</html>
