<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>

<title>Badminton Court Management</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: 'Poppins', sans-serif;
  background: url("court.jpeg") center/cover no-repeat fixed;
  color: white;
  min-height: 100vh;
}

/* dark overlay over the background */
body::before {
  content: "";
  position: fixed; inset: 0;
  z-index: 0;
}

/* ── NAVBAR ── */
.navbar {
  position: relative; z-index: 10;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 18px 40px;
  background: rgba(0, 0, 0, 0.45);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid rgba(255,255,255,0.1);
}

.navbar h2 {
  font-size: 20px;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.nav-buttons {
  display: flex;
  gap: 10px;
}

.nav-buttons a {
  padding: 9px 20px;
  text-decoration: none;
  color: white;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  transition: opacity 0.2s, transform 0.2s;
}

.nav-buttons a:hover {
  opacity: 0.85;
  transform: translateY(-1px);
}

.userregister { background: #27ae60; }
.user         { background: #2980b9; }
.admin        { background: #1f4ab8; }

/* ── HERO ── */
.center {
  position: relative; z-index: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  min-height: calc(100vh - 65px);
  padding: 40px 20px;
}

.center h2 {
  font-size: 18px;
  font-weight: 300;
  letter-spacing: 4px;
  text-transform: uppercase;
  color: rgb(255, 251, 251);
  margin-bottom: 14px;
}

.center h1 {
  font-size: clamp(36px, 6vw, 64px);
  font-weight: 700;
  color: #ffd369;
  line-height: 1.15;
  text-shadow: 0 4px 20px rgba(0,0,0,0.5);
  margin-bottom: 18px;
}

.center p {
  font-size: 16px;
  color: rgb(255, 253, 253);
  max-width: 440px;
  line-height: 1.7;
  margin-bottom: 40px;
}

.center a {
  text-decoration: none;
}

.center button {
  padding: 14px 44px;
  font-size: 16px;
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
  background: #27ae60;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  letter-spacing: 0.5px;
  transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
  box-shadow: 0 4px 20px rgba(39,174,96,0.4);
}

.center button:hover {
  background: #219150;
  transform: translateY(-2px);
  box-shadow: 0 8px 28px rgba(39,174,96,0.5);
}

</style>
</head>

<body>

<div class="navbar">
  <h2>🏸 Badminton Court Management</h2>
  <div class="nav-buttons">
    <a href="userregister.php" class="userregister">User Register</a>
    <a href="user_login.php"   class="user">User Login</a>
    <a href="admin_login.php"  class="admin">Admin Login</a>
  </div>
</div>

<div class="center">
  <h2>Welcome to</h2>
  <h1>Badminton Court Management System</h1>
  <p>Easily manage &amp; book your badminton courts online</p>
  <a href="userregister.php">
    <button>Get Started Now</button>
  </a>
</div>

</body>
</html>
