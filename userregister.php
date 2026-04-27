<?php
include "badmintondb.php";

$error = "";

if (isset($_POST['userregister'])) {
    $name             = trim($_POST['name']);
    $email            = trim($_POST['email']);
    $contact          = trim($_POST['contact']);
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!preg_match("/^[a-zA-Z ]+$/", $name)) {
        $error = "Username should contain only letters.";
    } elseif (!preg_match("/^[0-9]{10}$/", $contact)) {
        $error = "Phone number must be exactly 10 digits.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Email already registered.";
            $stmt->close();
        } else {
            $stmt->close();
            $stmt2 = $conn->prepare("SELECT user_id FROM users WHERE contact = ?");
            $stmt2->bind_param("s", $contact);
            $stmt2->execute();
            $stmt2->store_result();
            if ($stmt2->num_rows > 0) {
                $error = "Phone number already registered.";
                $stmt2->close();
            } else {
                $stmt2->close();
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt3  = $conn->prepare("INSERT INTO users (name, email, contact, password) VALUES (?, ?, ?, ?)");
                $stmt3->bind_param("ssss", $name, $email, $contact, $hashed);
                if ($stmt3->execute()) {
                    echo "<script>alert('Registration successful! Please sign in.');window.location='user_login.php';</script>";
                    exit();
                } else {
                    $error = "Database error. Please try again.";
                }
                $stmt3->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Create Account</title>
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
    padding: 24px 16px;
}

.card {
    background: rgba(255,255,255,0.98);
    width: 100%; max-width: 440px;
    border-radius: 20px; overflow: hidden;
    animation: slideUp 0.4s ease both;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
}

.card-header {
    background: linear-gradient(135deg,#1b5e20,#2e7d32);
    padding: 26px 28px 22px; color: white; text-align: center;
}
.card-header .avatar {
    width: 58px; height: 58px; border-radius: 50%;
    background: rgba(255,255,255,0.18);
    border: 2px solid rgba(255,255,255,0.35);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 10px; font-size: 26px;
}
.card-header h2 { font-size: 19px; margin-bottom: 3px; }
.card-header p  { font-size: 13px; opacity: 0.8; }

.form-body { padding: 24px 28px 22px; }

.error-box {
    background: #ffebee; border: 1px solid #ffcdd2;
    border-radius: 10px; padding: 11px 14px;
    color: #b71c1c; font-size: 13px; margin-bottom: 16px;
    display: flex; align-items: center; gap: 8px;
}

.field-group { margin-bottom: 15px; }
.field-group label {
    display: flex; align-items: center; gap: 6px;
    font-size: 12px; font-weight: 700; color: #1b5e20;
    text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 6px;
}

.input-wrap { position: relative; }
.input-wrap .prefix {
    position: absolute; left: 13px; top: 50%;
    transform: translateY(-50%); color: #aaa; font-size: 13px;
}
.input-wrap input {
    width: 100%; padding: 11px 42px;
    border: 1.5px solid #c8e6c9; border-radius: 10px;
    font-size: 14px; color: #1a1a1a; background: white;
    outline: none; transition: border 0.2s, box-shadow 0.2s;
}
.input-wrap input:focus {
    border-color: #2e7d32;
    box-shadow: 0 0 0 3px rgba(46,125,50,0.1);
}
.input-wrap .toggle {
    position: absolute; right: 13px; top: 50%;
    transform: translateY(-50%);
    color: #aaa; cursor: pointer; font-size: 13px; transition: color 0.2s;
}
.input-wrap .toggle:hover { color: #2e7d32; }

.btn-register {
    width: 100%; padding: 13px;
    background: linear-gradient(135deg,#2e7d32,#1b5e20);
    color: white; border: none; border-radius: 10px;
    font-size: 15px; font-weight: bold; cursor: pointer;
    transition: 0.2s; margin-top: 6px;
}
.btn-register:hover { background: linear-gradient(135deg,#1b5e20,#134a18); transform: translateY(-1px); }
.btn-register:active { transform: scale(0.98); }

.divider { height: 1px; background: #e8f5e9; margin: 18px 0; }
.footer-center { text-align: center; font-size: 13px; color: #666; }
.footer-center a { color: #2e7d32; text-decoration: none; font-weight: bold; }
.footer-center a:hover { text-decoration: underline; }
</style>
</head>
<body>

<div class="card">
    <div class="card-header">
        <div class="avatar">&#127992;</div>
        <h2>Create Account</h2>
        <p>Register to book your badminton court</p>
    </div>

    <div class="form-body">

        <?php if (!empty($error)): ?>
        <div class="error-box">&#9888;&#65039; <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="" autocomplete="off">

            <div class="field-group">
                <label><i class="fa fa-user"></i> Full Name</label>
                <div class="input-wrap">
                    <i class="fa fa-user prefix"></i>
                    <input type="text" name="name" placeholder="Enter your full name"
                           autocomplete="off"
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                </div>
            </div>

            <div class="field-group">
                <label><i class="fa fa-envelope"></i> Email Address</label>
                <div class="input-wrap">
                    <i class="fa fa-envelope prefix"></i>
                    <input type="email" name="email" placeholder="you@example.com"
                           autocomplete="off"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                </div>
            </div>

            <div class="field-group">
                <label><i class="fa fa-phone"></i> Contact Number</label>
                <div class="input-wrap">
                    <i class="fa fa-phone prefix"></i>
                    <input type="text" name="contact" placeholder="10-digit mobile number"
                           pattern="[6-9]{1}[0-9]{9}" title="Must start with 6-9 and be 10 digits"
                           autocomplete="off"
                           value="<?php echo isset($_POST['contact']) ? htmlspecialchars($_POST['contact']) : ''; ?>" required>
                </div>
            </div>

            <div class="field-group">
                <label><i class="fa fa-lock"></i> Password</label>
                <div class="input-wrap">
                    <i class="fa fa-lock prefix"></i>
                    <input type="password" name="password" id="password"
                           placeholder="Min 8 chars, A-Z, 0-9, symbol"
                           pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}"
                           title="Must contain 8+ chars, uppercase, lowercase, number, symbol"
                           autocomplete="new-password" required>
                    <i class="fa-solid fa-eye-slash toggle" id="togglePassword"></i>
                </div>
            </div>

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

            <button type="submit" name="userregister" class="btn-register">
                Create Account &rarr;
            </button>

        </form>

        <div class="divider"></div>
        <div class="footer-center">
            Already have an account? <a href="user_login.php">Sign In</a>
        </div>

    </div>
</div>

<script>
document.getElementById('togglePassword').addEventListener('click', function () {
    const p = document.getElementById('password');
    p.type = p.type === 'password' ? 'text' : 'password';
    this.classList.toggle('fa-eye'); this.classList.toggle('fa-eye-slash');
});
document.getElementById('toggleConfirm').addEventListener('click', function () {
    const p = document.getElementById('confirm_password');
    p.type = p.type === 'password' ? 'text' : 'password';
    this.classList.toggle('fa-eye'); this.classList.toggle('fa-eye-slash');
});
</script>
</body>
</html>
