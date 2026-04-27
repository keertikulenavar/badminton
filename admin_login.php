<?php
session_start();
include "badmintondb.php";

if(isset($_SESSION['admin_id'])){
    header("Location: admin_dashboard.php");
    exit();
}

$error = "";

if(isset($_POST['login'])){
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if(!empty($username) && !empty($password)){
        $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows === 1){
            $row = $result->fetch_assoc();
            if(password_verify($password, $row['password'])){
                $_SESSION['admin_id']   = $row['admin_id'];
                $_SESSION['admin_name'] = $row['username'];
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "No admin account found with that username.";
        }
        $stmt->close();
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Login – Badminton Court</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

body::before {
    content: "";
    position: fixed; top: 0; left: 0;
    width: 100%; height: 100%;
    background-image: url("cmb.jpeg");
    background-size: cover; background-position: center;
    filter: blur(8px); z-index: -1;
}

body {
    font-family: Arial, sans-serif;
    min-height: 100vh;
    display: flex; justify-content: center; align-items: center;
    padding: 20px;
}

.card {
    background: rgba(255,255,255,0.98);
    width: 100%; max-width: 420px;
    border-radius: 20px; overflow: hidden;
    animation: slideUp 0.4s ease both;
    box-shadow: 0 20px 60px rgba(0,0,0,0.25);
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
}

.card-header {
    background: linear-gradient(135deg, #0d47a1, #1565c0);
    padding: 30px 28px 24px; color: white; text-align: center;
}
.card-header .avatar {
    width: 64px; height: 64px; border-radius: 50%;
    background: rgba(255,255,255,0.18);
    border: 2px solid rgba(255,255,255,0.35);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 12px; font-size: 28px;
}
.card-header h2 { font-size: 20px; margin-bottom: 3px; }
.card-header p  { font-size: 13px; opacity: 0.8; }

.admin-badge {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    border: 1px solid rgba(255,255,255,0.3);
    border-radius: 20px;
    padding: 3px 14px;
    font-size: 11px;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    margin-top: 8px;
}

.form-body { padding: 28px; }

.error-box {
    background: #ffebee; border: 1px solid #ffcdd2;
    border-radius: 10px; padding: 11px 14px;
    color: #b71c1c; font-size: 13px; margin-bottom: 16px;
    display: flex; align-items: center; gap: 8px;
}

.field-group { margin-bottom: 18px; }
.field-group label {
    display: flex; align-items: center; gap: 6px;
    font-size: 12px; font-weight: 700; color: #0d47a1;
    text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 7px;
}

.input-wrap { position: relative; }
.input-wrap .prefix {
    position: absolute; left: 13px; top: 50%;
    transform: translateY(-50%); color: #aaa; font-size: 14px;
}
.input-wrap input {
    width: 100%; padding: 12px 42px;
    border: 1.5px solid #bbdefb; border-radius: 10px;
    font-size: 15px; color: #1a1a1a;
    outline: none; transition: border 0.2s, box-shadow 0.2s;
}
.input-wrap input:focus {
    border-color: #1565c0;
    box-shadow: 0 0 0 3px rgba(21,101,192,0.1);
}
.input-wrap .toggle {
    position: absolute; right: 13px; top: 50%;
    transform: translateY(-50%);
    color: #aaa; cursor: pointer; font-size: 14px;
}
.input-wrap .toggle:hover { color: #1565c0; }

.btn-login {
    width: 100%; padding: 13px;
    background: linear-gradient(135deg, #1565c0, #0d47a1);
    color: white; border: none; border-radius: 10px;
    font-size: 15px; font-weight: bold; cursor: pointer;
    transition: 0.2s; letter-spacing: 0.5px;
}
.btn-login:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(13,71,161,0.35); }

.divider { height: 1px; background: #e3f2fd; margin: 20px 0; }

.footer-center { text-align: center; font-size: 13px; color: #666; }
.footer-center a { color: #1565c0; text-decoration: none; font-weight: bold; }
.footer-center a:hover { text-decoration: underline; }

.security-note {
    display: flex; align-items: center; justify-content: center;
    gap: 6px; font-size: 12px; color: #888; margin-top: 14px;
}
</style>
</head>
<body>

<div class="card">
    <div class="card-header">
        <div class="avatar"><i class="fas fa-user-shield"></i></div>
        <h2>Admin Portal</h2>
        <p>Badminton Court Management System</p>
        <div class="admin-badge">⚙ Administrator Access</div>
    </div>

    <div class="form-body">

        <?php if(!empty($error)): ?>
        <div class="error-box">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="" autocomplete="off">
            <input type="text" name="fakeuser" style="display:none">
            <input type="password" name="fakepass" style="display:none">

            <div class="field-group">
                <label><i class="fa fa-user-shield"></i> Admin Username</label>
                <div class="input-wrap">
                    <i class="fa fa-user prefix"></i>
                    <input type="text" name="username" placeholder="Enter admin username"
                           autocomplete="off" required>
                </div>
            </div>

            <div class="field-group">
                <label><i class="fa fa-lock"></i> Password</label>
                <div class="input-wrap">
                    <i class="fa fa-lock prefix"></i>
                    <input type="password" name="password" id="password"
                           placeholder="Enter admin password"
                           autocomplete="new-password" required>
                    <i class="fa-solid fa-eye-slash toggle" id="togglePassword"></i>
                </div>
            </div>

            <button type="submit" name="login" class="btn-login">
                <i class="fa fa-sign-in-alt"></i> Sign In to Admin Panel →
            </button>
        </form>

        <div class="security-note">🔒 Restricted access — authorised personnel only</div>

        <div class="divider"></div>
        <div class="footer-center">
            <a href="index.php">← Back to Main Site</a>
        </div>

    </div>
</div>

<script>
document.getElementById('togglePassword').addEventListener('click', function(){
    const p = document.getElementById('password');
    p.type = p.type === 'password' ? 'text' : 'password';
    this.classList.toggle('fa-eye');
    this.classList.toggle('fa-eye-slash');
});
</script>
</body>
</html>