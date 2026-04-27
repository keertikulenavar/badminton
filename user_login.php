<?php
session_start();
include "badmintondb.php";

// Already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: user_dashboard.php");
    exit();
}

$error = "";

if (isset($_POST['login'])) {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['email'];
                $_SESSION['email']   = $row['email'];
                $_SESSION['name']    = $row['name'];
                header("Location: user_dashboard.php");
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "No account found with that email address.";
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
<title>User Login</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

body::before {
    content: "";
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background-image: url("cmb.jpeg");
    background-size: cover;
    background-position: center;
    filter: blur(8px);
    z-index: -1;
}

body {
    font-family: Arial, sans-serif;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.card {
    background: rgba(255,255,255,0.98);
    width: 100%;
    max-width: 420px;
    border-radius: 20px;
    overflow: hidden;
    animation: slideUp 0.4s ease both;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
}

.card-header {
    background: linear-gradient(135deg, #1b5e20, #2e7d32);
    padding: 30px 28px 24px;
    color: white;
    text-align: center;
}
.card-header .avatar {
    width: 62px; height: 62px;
    border-radius: 50%;
    background: rgba(255,255,255,0.18);
    border: 2px solid rgba(255,255,255,0.35);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 12px;
    font-size: 28px;
}
.card-header h2 { font-size: 20px; margin-bottom: 3px; }
.card-header p  { font-size: 13px; opacity: 0.8; }

.form-body { padding: 28px; }

.error-box {
    background: #ffebee;
    border: 1px solid #ffcdd2;
    border-radius: 10px;
    padding: 11px 14px;
    color: #b71c1c;
    font-size: 13px;
    margin-bottom: 16px;
    display: flex; align-items: center; gap: 8px;
}

.field-group { margin-bottom: 18px; }
.field-group label {
    display: flex; align-items: center; gap: 6px;
    font-size: 12px; font-weight: 700; color: #1b5e20;
    text-transform: uppercase; letter-spacing: 0.8px;
    margin-bottom: 7px;
}

.input-wrap { position: relative; }
.input-wrap .prefix {
    position: absolute; left: 13px; top: 50%;
    transform: translateY(-50%);
    color: #aaa; font-size: 14px;
}
.input-wrap input {
    width: 100%;
    padding: 12px 42px;
    border: 1.5px solid #c8e6c9;
    border-radius: 10px;
    font-size: 15px; color: #1a1a1a;
    outline: none;
    transition: border 0.2s, box-shadow 0.2s;
}
.input-wrap input:focus {
    border-color: #2e7d32;
    box-shadow: 0 0 0 3px rgba(46,125,50,0.1);
}
.input-wrap .toggle {
    position: absolute; right: 13px; top: 50%;
    transform: translateY(-50%);
    color: #aaa; cursor: pointer; font-size: 14px;
}
.input-wrap .toggle:hover { color: #2e7d32; }

.btn-login {
    width: 100%; padding: 13px;
    background: linear-gradient(135deg, #2e7d32, #1b5e20);
    color: white; border: none; border-radius: 10px;
    font-size: 15px; font-weight: bold; cursor: pointer;
}
.btn-login:hover { transform: translateY(-1px); }

.divider { height: 1px; background: #e8f5e9; margin: 20px 0; }

.footer-links { font-size: 13px; }
.footer-links a { color: #2e7d32; text-decoration: none; font-weight: bold; }

.footer-center { text-align: center; font-size: 13px; color: #666; }
.footer-center a { color: #2e7d32; text-decoration: none; font-weight: bold; }
</style>
</head>
<body>

<div class="card">
    <div class="card-header">
        <div class="avatar">&#127992;</div>
        <h2>Welcome Back</h2>
        <p>Sign in to book your court</p>
    </div>

    <div class="form-body">

        <?php if (!empty($error)): ?>
        <div class="error-box">&#9888;&#65039; <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Autofill disabled -->
        <form method="POST" action="" autocomplete="off">

            <!-- Hidden trick to stop autofill -->
            <input type="text" name="fakeuser" style="display:none">
            <input type="password" name="fakepass" style="display:none">

            <div class="field-group">
                <label><i class="fa fa-envelope"></i> Email Address</label>
                <div class="input-wrap">
                    <i class="fa fa-envelope prefix"></i>
                    <input type="email" name="email" placeholder="you@example.com"
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
                    <i class="fa-solid fa-eye-slash toggle" id="togglePassword"></i>
                </div>
            </div>

            <button type="submit" name="login" class="btn-login">Sign In &rarr;</button>

        </form>

        <div class="divider"></div>

        <div class="footer-links">
            <a href="index.php">&larr; Back to Home</a>
        </div>

        <div class="footer-center">
            Don't have an account? <a href="userregister.php">Sign Up</a>
        </div>

    </div>
</div>

<script>
const togglePassword = document.getElementById('togglePassword');
const passwordInput  = document.getElementById('password');

togglePassword.addEventListener('click', function () {
    const type = passwordInput.type === 'password' ? 'text' : 'password';
    passwordInput.type = type;
    this.classList.toggle('fa-eye');
    this.classList.toggle('fa-eye-slash');
});
</script>

</body>
</html>
