<?php
session_start();
include "badmintondb.php";

// Only allow access if already logged in as admin (optional security layer)
// Uncomment below to restrict registration to existing admins only:
// if(!isset($_SESSION['admin_id'])){
//     header("Location: admin_login.php");
//     exit();
// }

$error   = "";
$success = "";

if(isset($_POST['register'])){
    $username        = trim($_POST['username']);
    $email           = trim($_POST['email']);
    $password        = trim($_POST['password']);
    $confirm         = trim($_POST['confirm_password']);
    // ── Basic validation ────────────────────────────────────────────────────
    if(empty($username) || empty($email) || empty($password) || empty($confirm)){
        $error = "Please fill in all fields.";

    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "Please enter a valid email address.";

    } elseif(strlen($password) < 8){
        $error = "Password must be at least 8 characters long.";

    } elseif($password !== $confirm){
        $error = "Passwords do not match.";

    } else {
        // ── Check if username or email already exists ─────────────────────
        $stmt = $conn->prepare("SELECT admin_id FROM admin WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows > 0){
            $error = "An admin with that username or email already exists.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt->close();

            $ins = $conn->prepare("INSERT INTO admin (username, email, password) VALUES (?, ?, ?)");
            $ins->bind_param("sss", $username, $email, $hashed);

            if($ins->execute()){
                $success = "Admin account created successfully! You can now log in.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
            $ins->close();
        }

        if($stmt) $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Registration – Badminton Court</title>
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
    width: 100%; max-width: 460px;
    border-radius: 20px; overflow: hidden;
    animation: slideUp 0.4s ease both;
    box-shadow: 0 20px 60px rgba(0,0,0,0.25);
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Header ─────────────────────────────────────────── */
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

/* ── Form body ──────────────────────────────────────── */
.form-body { padding: 28px; }

/* ── Alerts ─────────────────────────────────────────── */
.error-box {
    background: #ffebee; border: 1px solid #ffcdd2;
    border-radius: 10px; padding: 11px 14px;
    color: #b71c1c; font-size: 13px; margin-bottom: 16px;
    display: flex; align-items: center; gap: 8px;
}
.success-box {
    background: #e8f5e9; border: 1px solid #c8e6c9;
    border-radius: 10px; padding: 11px 14px;
    color: #1b5e20; font-size: 13px; margin-bottom: 16px;
    display: flex; align-items: center; gap: 8px;
}

/* ── Fields ─────────────────────────────────────────── */
.field-group { margin-bottom: 16px; }
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

/* ── Password strength bar ──────────────────────────── */
.strength-wrap { margin-top: 7px; }
.strength-bar {
    height: 4px; border-radius: 4px;
    background: #e0e0e0; overflow: hidden;
}
.strength-bar-fill {
    height: 100%; width: 0%;
    border-radius: 4px;
    transition: width 0.3s, background 0.3s;
}
.strength-label {
    font-size: 11px; margin-top: 3px; color: #888;
}

/* ── Submit button ───────────────────────────────────── */
.btn-register {
    width: 100%; padding: 13px;
    background: linear-gradient(135deg, #1565c0, #0d47a1);
    color: white; border: none; border-radius: 10px;
    font-size: 15px; font-weight: bold; cursor: pointer;
    transition: 0.2s; letter-spacing: 0.5px;
    margin-top: 4px;
}
.btn-register:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(13,71,161,0.35); }

/* ── Footer ─────────────────────────────────────────── */
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
        <div class="avatar"><i class="fas fa-user-plus"></i></div>
        <h2>Admin Registration</h2>
        <p>Badminton Court Management System</p>
        <div class="admin-badge">⚙ Create Administrator Account</div>
    </div>

    <div class="form-body">

        <?php if(!empty($error)): ?>
        <div class="error-box">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if(!empty($success)): ?>
        <div class="success-box">✅ <?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if(empty($success)): ?>
        <form method="POST" action="" autocomplete="off">
            <input type="text"     name="fakeuser" style="display:none">
            <input type="password" name="fakepass" style="display:none">

            <!-- Username -->
            <div class="field-group">
                <label><i class="fa fa-user-shield"></i> Admin Username</label>
                <div class="input-wrap">
                    <i class="fa fa-user prefix"></i>
                    <input type="text" name="username"
                           placeholder="Choose a username"
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                           autocomplete="off" required>
                </div>
            </div>

            <!-- Email -->
            <div class="field-group">
                <label><i class="fa fa-envelope"></i> Email Address</label>
                <div class="input-wrap">
                    <i class="fa fa-envelope prefix"></i>
                    <input type="email" name="email"
                           placeholder="Enter email address"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                           autocomplete="off" required>
                </div>
            </div>

            <!-- Password -->
            <div class="field-group">
                <label><i class="fa fa-lock"></i> Password</label>
                <div class="input-wrap">
                    <i class="fa fa-lock prefix"></i>
                    <input type="password" name="password" id="password"
                           placeholder="Minimum 8 characters"
                           autocomplete="new-password"
                           oninput="checkStrength(this.value)"
                           required>
                    <i class="fa-solid fa-eye-slash toggle" id="togglePassword"></i>
                </div>
                <div class="strength-wrap">
                    <div class="strength-bar">
                        <div class="strength-bar-fill" id="strengthFill"></div>
                    </div>
                    <div class="strength-label" id="strengthLabel"></div>
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="field-group">
                <label><i class="fa fa-lock"></i> Confirm Password</label>
                <div class="input-wrap">
                    <i class="fa fa-lock prefix"></i>
                    <input type="password" name="confirm_password" id="confirm_password"
                           placeholder="Re-enter your password"
                           autocomplete="new-password" required>
                    <i class="fa-solid fa-eye-slash toggle" id="toggleConfirm"></i>
                </div>
            </div>

            <button type="submit" name="register" class="btn-register">
                <i class="fa fa-user-plus"></i> Create Admin Account →
            </button>
        </form>
        <?php else: ?>
            <div style="text-align:center; padding: 10px 0 6px;">
                <a href="admin_login.php" style="color:#1565c0; font-weight:bold; font-size:15px; text-decoration:none;">
                    <i class="fa fa-sign-in-alt"></i> Go to Login
                </a>
            </div>
        <?php endif; ?>

        <div class="security-note">🔒 Restricted access — authorised personnel only</div>

        <div class="divider"></div>
        <div class="footer-center">
            Already have an account? <a href="admin_login.php">Sign In →</a>
        </div>
        <div class="footer-center" style="margin-top:10px;">
            <a href="index.php">← Back to Main Site</a>
        </div>

    </div>
</div>

<script>
// ── Password visibility toggles ──────────────────────────────────────────
function makeToggle(toggleId, inputId) {
    document.getElementById(toggleId).addEventListener('click', function(){
        const p = document.getElementById(inputId);
        p.type = p.type === 'password' ? 'text' : 'password';
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });
}
makeToggle('togglePassword', 'password');
makeToggle('toggleConfirm',  'confirm_password');

// ── Password strength meter ──────────────────────────────────────────────
function checkStrength(val) {
    const fill  = document.getElementById('strengthFill');
    const label = document.getElementById('strengthLabel');

    let score = 0;
    if(val.length >= 8)                    score++;
    if(val.length >= 12)                   score++;
    if(/[A-Z]/.test(val))                  score++;
    if(/[0-9]/.test(val))                  score++;
    if(/[^A-Za-z0-9]/.test(val))          score++;

    const levels = [
        { pct: '0%',   color: '#e0e0e0', text: '' },
        { pct: '25%',  color: '#ef5350', text: 'Weak' },
        { pct: '50%',  color: '#ffa726', text: 'Fair' },
        { pct: '75%',  color: '#66bb6a', text: 'Good' },
        { pct: '100%', color: '#43a047', text: 'Strong' },
    ];

    const lvl = val.length === 0 ? levels[0] : levels[Math.min(score, 4)];
    fill.style.width      = lvl.pct;
    fill.style.background = lvl.color;
    label.textContent     = lvl.text;
    label.style.color     = lvl.color;
}
</script>
</body>
</html>