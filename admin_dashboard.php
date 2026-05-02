<?php
session_start();
include "badmintondb.php";

if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

$admin_name = $_SESSION['admin_name'];

/* ── Stats ── */
$total_users    = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
$total_bookings = $conn->query("SELECT COUNT(*) AS c FROM bookings")->fetch_assoc()['c'];
$total_revenue  = $conn->query("SELECT SUM(amount) AS s FROM payments WHERE payment_status='Paid'")->fetch_assoc()['s'] ?? 0;
$today          = date('Y-m-d');
$today_bookings = $conn->query("SELECT COUNT(*) AS c FROM bookings WHERE booking_date='$today'")->fetch_assoc()['c'];
$upcoming       = $conn->query("SELECT COUNT(*) AS c FROM bookings WHERE booking_date >= '$today'")->fetch_assoc()['c'];
$total_payments = $conn->query("SELECT COUNT(*) AS c FROM payments WHERE payment_status='Paid'")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard – Badminton Court</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

body::before {
    content: "";
    position: fixed; top: 0; left: 0;
    width: 100%; height: 100%;
    background-image: url("indorecourt1.jpg");
    background-size: cover; background-position: center;
    filter: blur(8px); z-index: -1;
}

body { font-family: Arial, sans-serif; min-height: 100vh; color: white; }

/* ── Header ── */
.header {
    background: rgba(0,0,0,0.85);
    padding: 16px 32px;
    display: flex; justify-content: space-between; align-items: center;
    border-bottom: 1px solid rgba(255,255,255,0.12);
    position: sticky; top: 0; z-index: 100;
}
.header-left { display: flex; align-items: center; gap: 14px; }
.header-left h2 { font-size: 21px; letter-spacing: 0.5px; }
.admin-badge-header {
    background: rgba(21,101,192,0.75);
    border: 1px solid rgba(100,160,255,0.35);
    border-radius: 20px; padding: 4px 14px;
    font-size: 12px; letter-spacing: 1px;
    text-transform: uppercase; color: #90caf9;
}
.header-right { display: flex; align-items: center; gap: 16px; font-size: 15px; }
.logout {
    background: #c0392b; padding: 9px 20px;
    color: white; text-decoration: none;
    border-radius: 7px; font-size: 14px; transition: 0.2s;
}
.logout:hover { background: #e74c3c; }

/* ── Content ── */
.content { padding: 30px 30px 56px; max-width: 1200px; margin: auto; }

/* ── Hero ── */
.hero { text-align: center; padding: 28px 0 22px; }
.hero h1 { font-size: 30px; font-weight: bold; color: #fff; margin-bottom: 8px; }
.greeting-badge {
    display: inline-block;
    background: rgba(255,255,255,0.14);
    border: 1px solid rgba(255,255,255,0.22);
    border-radius: 20px; padding: 6px 22px;
    font-size: 14px; color: rgba(255,255,255,0.95);
    font-weight: bold; margin-bottom: 10px;
}

/* ── Section title ── */
.section-title {
    font-size: 14px; text-transform: uppercase;
    letter-spacing: 2px; color: rgba(255,255,255,0.6);
    margin-bottom: 18px; margin-top: 36px;
    display: flex; align-items: center; gap: 12px;
}
.section-title::after { content:''; flex:1; height:1px; background:rgba(255,255,255,0.18); }

/* ── Stat cards ── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    gap: 18px; margin-bottom: 8px;
}
.stat-card {
    backdrop-filter: blur(14px);
    border-radius: 18px; padding: 26px 18px;
    text-align: center;
    transition: transform 0.2s;
    border: 1px solid rgba(255,255,255,0.15);
}
.stat-card:hover { transform: translateY(-5px); }

.stat-card.green  { background: rgba(27,94,32,0.85);   border-color: rgba(76,175,80,0.5); }
.stat-card.blue   { background: rgba(13,71,161,0.85);  border-color: rgba(33,150,243,0.5); }
.stat-card.orange { background: rgba(230,81,0,0.85);   border-color: rgba(255,152,0,0.5); }
.stat-card.teal   { background: rgba(0,96,100,0.85);   border-color: rgba(0,188,212,0.5); }
.stat-card.purple { background: rgba(74,20,140,0.85);  border-color: rgba(156,39,176,0.5); }
.stat-card.red    { background: rgba(183,28,28,0.85);  border-color: rgba(244,67,54,0.5); }

.stat-card .icon { font-size: 32px; margin-bottom: 12px; }
.stat-card .val  { font-size: 32px; font-weight: bold; color: #fff; text-shadow: 0 2px 8px rgba(0,0,0,0.4); }
.stat-card .lbl  { font-size: 13px; color: rgba(255,255,255,0.9); text-transform: uppercase; letter-spacing: 1.2px; margin-top: 6px; font-weight: 600; }

/* ── Nav Cards ── */
.nav-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}
.nav-card {
    border-radius: 20px; overflow: hidden;
    position: relative;
    transition: transform 0.3s, box-shadow 0.3s;
    cursor: pointer;
}
.nav-card:hover { transform: translateY(-10px) scale(1.03); box-shadow: 0 28px 52px rgba(0,0,0,0.5); }
.card-shine {
    position: absolute; top:0; left:-60%;
    width:40%; height:100%;
    background: rgba(255,255,255,0.09);
    transform: skewX(-20deg); transition: left 0.5s; pointer-events: none;
}
.nav-card:hover .card-shine { left: 120%; }

.nav-card.users    { background: linear-gradient(145deg,#1976d2,#0d47a1); }
.nav-card.bookings { background: linear-gradient(145deg,#27ae60,#1e8449); }
.nav-card.payments { background: linear-gradient(145deg,#16a085,#0e6655); }
.nav-card.reports  { background: linear-gradient(145deg,#e67e22,#ca6f1e); }

.nav-card-inner { padding: 30px 28px 26px; }
.nav-card i  { font-size: 44px; margin-bottom: 16px; display: block; opacity: 0.95; }
.nav-card h3 { font-size: 20px; margin-bottom: 10px; font-weight: 700; }
.nav-card p  { font-size: 14px; opacity: 0.88; margin-bottom: 22px; line-height: 1.6; }

.card-btn {
    display: inline-block;
    background: rgba(255,255,255,0.92); color: #1a1a1a;
    padding: 10px 22px; border-radius: 9px;
    font-size: 14px; font-weight: bold;
    text-decoration: none; transition: 0.2s;
}
.card-btn:hover { background: white; transform: scale(1.05); }

@media(max-width:700px){
    .nav-cards { grid-template-columns: 1fr; }
    .stats-grid { grid-template-columns: repeat(2,1fr); }
    .content { padding: 16px 14px 48px; }
}
</style>
</head>
<body>

<div class="header">
    <div class="header-left">
        <h2>🏸 Badminton Court Management</h2>
        <span class="admin-badge-header">Admin Panel</span>
    </div>
    <div class="header-right">
        <span>👤 <strong><?php echo htmlspecialchars($admin_name); ?></strong></span>
        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="content">

    <div class="hero">
        <p class="greeting-badge">⚙️ Admin Control Centre</p>
        <h1>Admin Dashboard</h1>
    </div>

    <div class="section-title">Overview Statistics</div>
    <div class="stats-grid">
        <div class="stat-card green">
            <div class="icon">👥</div>
            <div class="val"><?php echo $total_users; ?></div>
            <div class="lbl">Total Users</div>
        </div>
        <div class="stat-card blue">
            <div class="icon">📋</div>
            <div class="val"><?php echo $total_bookings; ?></div>
            <div class="lbl">Total Bookings</div>
        </div>
        <div class="stat-card orange">
            <div class="icon">💰</div>
            <div class="val">₹<?php echo number_format($total_revenue); ?></div>
            <div class="lbl">Total Revenue</div>
        </div>
        <div class="stat-card teal">
            <div class="icon">📅</div>
            <div class="val"><?php echo $today_bookings; ?></div>
            <div class="lbl">Today's Bookings</div>
        </div>
        <div class="stat-card purple">
            <div class="icon">🔜</div>
            <div class="val"><?php echo $upcoming; ?></div>
            <div class="lbl">Upcoming</div>
        </div>
        <div class="stat-card red">
            <div class="icon">💳</div>
            <div class="val"><?php echo $total_payments; ?></div>
            <div class="lbl">Payments</div>
        </div>
    </div>

    <div class="section-title">Management Modules</div>
    <div class="nav-cards">

        <div class="nav-card users">
            <div class="card-shine"></div>
            <div class="nav-card-inner">
                <i class="fas fa-users"></i>
                <h3>Manage Users</h3>
                <p>View all registered users, their contact details, and account information.</p>
                <a href="admin_users.php" class="card-btn">View Users →</a>
            </div>
        </div>

        <div class="nav-card bookings">
            <div class="card-shine"></div>
            <div class="nav-card-inner">
                <i class="fas fa-calendar-check"></i>
                <h3>Manage Bookings</h3>
                <p>View, filter, and cancel all court bookings across all users and dates.</p>
                <a href="admin_bookings.php" class="card-btn">View Bookings →</a>
            </div>
        </div>

        <div class="nav-card payments">
            <div class="card-shine"></div>
            <div class="nav-card-inner">
                <i class="fas fa-credit-card"></i>
                <h3>Payment Records</h3>
                <p>Track all payment transactions, UTR numbers, and revenue collected.</p>
                <a href="admin_payments.php" class="card-btn">View Payments →</a>
            </div>
        </div>

    </div>

    <!-- Reports card centred below the 3-column row -->
    <div style="display:flex; justify-content:center; margin-top:24px;">
        <div class="nav-card reports" style="max-width:340px; width:100%;">
            <div class="card-shine"></div>
            <div class="nav-card-inner">
                <i class="fas fa-chart-bar"></i>
                <h3>Reports & Summary</h3>
                <p>View daily booking summaries, revenue reports, and slot utilisation stats.</p>
                <a href="admin_reports.php" class="card-btn">View Reports →</a>
            </div>
        </div>
    </div>

</div>
</body>
</html>