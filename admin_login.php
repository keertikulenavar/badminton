<?php
session_start();
include "badmintondb.php";

// If already logged in as admin, go straight to dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {

        $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password']) || $password === $row['password']) {
                $_SESSION['admin_id']   = $row['admin_id'];
                $_SESSION['admin_name'] = $row['username'];
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $error = "Incorrect password!";
            }
        } else {
            $error = "Admin not found!";
        }

        $stmt->close();

    } else {
        $error = "Please fill all fields!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: Arial, sans-serif;
    background-image: url("cmb.jpeg");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

body::before {
    content: "";
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 0;
}

.card {
    position: relative;
    z-index: 1;
    background: rgba(255, 255, 255, 0.98);
    width: 100%;
    max-width: 400px;
    border-radius: 20px;
    overflow: hidden;
    animation: slideUp 0.4s ease both;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
}

.card-header {
    background: linear-gradient(135deg, #0d47a1, #1565c0);
    padding: 30px 28px 24px;
    color: white;
    text-align: center;
}

.admin-avatar {
    width: 62px; height: 62px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.18);
    border: 2px solid rgba(255, 255, 255, 0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    font-size: 28px;
}

.card-header h2 { font-size: 20px; letter-spacing: 0.5px; margin-bottom: 3px; }
.card-header p  { font-size: 13px; opacity: 0.8; }

.form-body { padding: 28px 28px 24px; }

.error-box {
    background: #ffebee;
    border: 1px solid #ffcdd2;
    border-radius: 10px;
    padding: 11px 14px;
    color: #b71c1c;
    font-size: 13px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.field-group { margin-bottom: 18px; }
.field-group label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 700;
    color: #1565c0;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 7px;
}

.input-wrap { position: relative; }
.input-wrap .prefix {
    position: absolute;
    left: 13px; top: 50%;
    transform: translateY(-50%);
    color: #aaa;
    font-size: 14px;
}
.input-wrap input {
    width: 100%;
    padding: 12px 42px;
    border: 1.5px solid #bbdefb;
    border-radius: 10px;
    font-size: 15px;
    color: #1a1a1a;
    outline: none;
    transition: border 0.2s, box-shadow 0.2s;
}
.input-wrap input:focus {
    border-color: #1565c0;
    box-shadow: 0 0 0 3px rgba(21, 101, 192, 0.1);
}
.eye-icon {
    position: absolute;
    right: 13px; top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #888;
    font-size: 16px;
    transition: color 0.2s;
}
.eye-icon:hover { color: #1565c0; }

.btn-login {
    width: 100%;
    padding: 13px;
    background: linear-gradient(135deg, #1565c0, #0d47a1);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    letter-spacing: 0.5px;
    transition: 0.2s;
    margin-top: 4px;
}
.btn-login:hover {
    background: linear-gradient(135deg, #0d47a1, #09357a);
    transform: translateY(-1px);
}
.btn-login:active { transform: scale(0.98); }

.divider { height: 1px; background: #e3f2fd; margin: 20px 0; }

.back-link {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    color: #1565c0;
    text-decoration: none;
    font-size: 14px;
    font-weight: bold;
    padding: 8px;
    border-radius: 8px;
    transition: 0.2s;
}
.back-link:hover { background: #e3f2fd; }

.security-note {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    font-size: 11px;
    color: #aaa;
    margin-top: 14px;
}
</style>
</head>
<body>

<div class="card">
    <div class="card-header">
        <div class="admin-avatar">🛡️</div>
        <h2>Admin Portal</h2>
        <p>Badminton Court Management System</p>
    </div>

    <div class="form-body">

        <?php if (!empty($error)): ?>
        <div class="error-box">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="" autocomplete="off">

            <div class="field-group">
                <label><i class="fa fa-user"></i> Admin Username</label>
                <div class="input-wrap">
                    <i class="fa fa-user prefix"></i>
                    <input type="text" name="username"
                           placeholder="Enter your username"
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                           autocomplete="off" required>
                </div>
            </div>

            <div class="field-group">
                <label><i class="fa fa-lock"></i> Password</label>
                <div class="input-wrap">
                    <i class="fa fa-lock prefix"></i>
                    <input type="password" name="password" id="password"
                           placeholder="Enter your password"
                           autocomplete="new-password" required>
                    <i class="fa-solid fa-eye-slash eye-icon" id="togglePassword"></i>
                </div>
            </div>

            <button type="submit" name="login" class="btn-login">
                Login to Dashboard →
            </button>

        </form>

        <div class="divider"></div>
        <a href="index.php" class="back-link">← Back to Home</a>
        <div class="security-note">🔒 This portal is for authorised administrators only</div>

    </div>
</div>

<script>
const togglePassword = document.getElementById('togglePassword');
const passwordInput  = document.getElementById('password');
togglePassword.addEventListener('click', function () {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    this.classList.toggle('fa-eye');
    this.classList.toggle('fa-eye-slash');
});
</script>

</body>
</html>