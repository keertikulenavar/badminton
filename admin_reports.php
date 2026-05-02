<?php
session_start();
include "badmintondb.php";

if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

/* Auto-create cancelled_bookings table (no schema changes needed) */
$conn->query("CREATE TABLE IF NOT EXISTS cancelled_bookings (booking_id INT PRIMARY KEY)");

$today = date('Y-m-d');
$month = date('Y-m');

/* Stats */
$total_users    = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
$total_bookings = $conn->query("SELECT COUNT(*) AS c FROM bookings b LEFT JOIN cancelled_bookings cb ON b.booking_id=cb.booking_id WHERE cb.booking_id IS NULL")->fetch_assoc()['c'];
$total_revenue  = $conn->query("SELECT SUM(amount) AS s FROM payments WHERE payment_status='Paid'")->fetch_assoc()['s'] ?? 0;
$today_bookings = $conn->query("SELECT COUNT(*) AS c FROM bookings b LEFT JOIN cancelled_bookings cb ON b.booking_id=cb.booking_id WHERE b.booking_date='$today' AND cb.booking_id IS NULL")->fetch_assoc()['c'];
$today_revenue  = $conn->query("SELECT SUM(p.amount) AS s FROM payments p JOIN bookings b ON p.booking_id=b.booking_id WHERE b.booking_date='$today' AND p.payment_status='Paid'")->fetch_assoc()['s'] ?? 0;
$month_bookings = $conn->query("SELECT COUNT(*) AS c FROM bookings b LEFT JOIN cancelled_bookings cb ON b.booking_id=cb.booking_id WHERE DATE_FORMAT(b.booking_date,'%Y-%m')='$month' AND cb.booking_id IS NULL")->fetch_assoc()['c'];
$month_revenue  = $conn->query("SELECT SUM(p.amount) AS s FROM payments p JOIN bookings b ON p.booking_id=b.booking_id WHERE DATE_FORMAT(b.booking_date,'%Y-%m')='$month' AND p.payment_status='Paid'")->fetch_assoc()['s'] ?? 0;
$upcoming_c     = $conn->query("SELECT COUNT(*) AS c FROM bookings b LEFT JOIN cancelled_bookings cb ON b.booking_id=cb.booking_id WHERE b.booking_date >= '$today' AND cb.booking_id IS NULL")->fetch_assoc()['c'];
$paid_c         = $conn->query("SELECT COUNT(*) AS c FROM payments WHERE payment_status='Paid'")->fetch_assoc()['c'];
$paid_booking_c = $conn->query("SELECT COUNT(DISTINCT booking_id) AS c FROM payments WHERE payment_status='Paid'")->fetch_assoc()['c'];
$cancelled_c    = $conn->query("SELECT COUNT(*) AS c FROM cancelled_bookings")->fetch_assoc()['c'];

/* Slot popularity */
$slot_result = $conn->query("SELECT booking_time, COUNT(*) AS cnt FROM bookings GROUP BY booking_time ORDER BY cnt DESC");
$slots = []; $max_slot = 1;
if($slot_result) while($r = $slot_result->fetch_assoc()){ $slots[] = $r; if($r['cnt'] > $max_slot) $max_slot = $r['cnt']; }

/* Last 7 days */
$daily = [];
for($d = 6; $d >= 0; $d--){
    $day = date('Y-m-d', strtotime("-$d days"));
    $c   = $conn->query("SELECT COUNT(*) AS c FROM bookings WHERE booking_date='$day'")->fetch_assoc()['c'];
    $rev = $conn->query("SELECT SUM(p.amount) AS s FROM payments p JOIN bookings b ON p.booking_id=b.booking_id WHERE b.booking_date='$day' AND p.payment_status='Paid'")->fetch_assoc()['s'] ?? 0;
    $daily[] = ['date'=>$day,'label'=>date('D',strtotime($day)),'sublabel'=>date('d M',strtotime($day)),'count'=>(int)$c,'revenue'=>(float)$rev];
}
$max_daily = max(array_column($daily,'count')) ?: 1;
$max_rev = max(array_column($daily,'revenue')) ?: 1;

$booking_mix_total = max(((int)$total_bookings + (int)$cancelled_c), 1);
$active_pct = round(((int)$total_bookings / $booking_mix_total) * 100);
$cancelled_pct = 100 - $active_pct;
$payment_total = max((int)$total_bookings, 1);
$paid_pct = min(100, round(((int)$paid_booking_c / $payment_total) * 100));
$awaiting_c = max((int)$total_bookings - (int)$paid_booking_c, 0);

/*
 * FIX 1 — Payments subquery with GROUP BY prevents duplicate rows
 *          when a booking has multiple payment records.
 * FIX 2 — LEFT JOIN cancelled_bookings to show Cancelled status badge.
 */
$recent_result = $conn->query("
    SELECT
        b.booking_id,
        b.user_email,
        b.booking_date,
        b.booking_time,
        p.amount,
        p.txn_id,
        cb.booking_id AS is_cancelled
    FROM bookings b
    LEFT JOIN (
        SELECT booking_id, amount, txn_id
        FROM payments
        GROUP BY booking_id
    ) p ON b.booking_id = p.booking_id
    LEFT JOIN cancelled_bookings cb ON b.booking_id = cb.booking_id
    ORDER BY b.booking_date DESC
    LIMIT 10
");
$recent = [];
if($recent_result) while($r = $recent_result->fetch_assoc()) $recent[] = $r;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reports & Summary - Admin</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body::before {
    content: ""; position: fixed; top: 0; left: 0;
    width: 100%; height: 100%;
    background-image: url("cmb.jpeg");
    background-size: cover; background-position: center;
    filter: blur(8px); z-index: -1;
}
body { font-family: Arial, sans-serif; min-height: 100vh; padding: 36px 16px; }
.container {
    max-width: 1080px; margin: auto;
    background: rgba(255,255,255,0.98);
    border-radius: 20px; overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.28);
}
.page-header {
    background: linear-gradient(135deg,#e67e22,#ca6f1e);
    padding: 26px 32px 22px; color: white;
    display: flex; justify-content: space-between; align-items: center;
}
.page-header-left h2 { font-size: 22px; margin-bottom: 4px; font-weight: 700; }
.page-header-left p  { font-size: 14px; opacity: 0.85; }
.back-header-btn {
    background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.32);
    color: white; padding: 9px 18px; border-radius: 9px;
    text-decoration: none; font-size: 14px; font-weight: bold; transition: 0.2s;
}
.back-header-btn:hover { background: rgba(255,255,255,0.28); }
.body { padding: 28px 32px 40px; }
.section-title {
    font-size: 13px; font-weight: 700; color: #999;
    text-transform: uppercase; letter-spacing: 2px;
    margin: 32px 0 16px;
    display: flex; align-items: center; gap: 12px;
}
.section-title::after { content:''; flex:1; height:1px; background:#e8e8e8; }
.section-title:first-child { margin-top: 0; }

/* Stat cards */
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(155px, 1fr)); gap: 16px; }
.stat-card { border-radius: 16px; padding: 20px 16px; text-align: center; border: 1px solid transparent; transition: transform 0.2s; }
.stat-card:hover { transform: translateY(-3px); }
.stat-card.green   { background: #2e7d32; border-color: #1b5e20; }
.stat-card.blue    { background: #1565c0; border-color: #0d47a1; }
.stat-card.orange  { background: #e65100; border-color: #bf360c; }
.stat-card.teal    { background: #00695c; border-color: #004d40; }
.stat-card.purple  { background: #6a1b9a; border-color: #4a148c; }
.stat-card.red     { background: #c62828; border-color: #b71c1c; }
.stat-card.indigo  { background: #283593; border-color: #1a237e; }
.stat-card.brown   { background: #4e342e; border-color: #3e2723; }
.stat-card.crimson { background: #880e4f; border-color: #6a0030; }
.stat-card .icon { font-size: 28px; margin-bottom: 10px; }
.stat-card .val  { font-size: 26px; font-weight: bold; color: #fff; text-shadow: 0 1px 6px rgba(0,0,0,0.3); }
.stat-card .lbl  { font-size: 12px; color: rgba(255,255,255,0.88); text-transform: uppercase; letter-spacing: 1px; margin-top: 5px; font-weight: 600; }

.month-pill { margin-top: 14px; padding: 16px 22px; background: #fff3e0; border: 1px solid #ffe0b2; border-radius: 14px; display: flex; align-items: center; justify-content: space-between; }
.month-pill .m-label { font-size: 14px; color: #555; font-weight: 600; }
.month-pill .m-val   { font-size: 22px; font-weight: bold; color: #e65100; }

/* Graphs */
.graph-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
.graph-card { border: 1px solid #e5e7eb; border-radius: 16px; background: #fff; padding: 18px; min-height: 250px; box-shadow: 0 8px 22px rgba(0,0,0,0.06); }
.graph-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
.graph-title { font-size: 15px; font-weight: 800; color: #263238; }
.graph-subtitle { font-size: 12px; color: #78909c; margin-top: 4px; }
.graph-chip { font-size: 12px; font-weight: 800; color: #e65100; background: #fff3e0; border: 1px solid #ffe0b2; padding: 5px 9px; border-radius: 999px; white-space: nowrap; }
.column-chart { display: flex; align-items: flex-end; gap: 12px; height: 165px; padding-top: 10px; border-bottom: 1px solid #eceff1; }
.column-item { flex: 1; min-width: 0; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; gap: 6px; }
.column-value { font-size: 12px; font-weight: 800; color: #1565c0; }
.column-bar { width: 100%; max-width: 42px; min-height: 8px; border-radius: 10px 10px 3px 3px; background: linear-gradient(180deg,#42a5f5,#1565c0); box-shadow: inset 0 -8px 12px rgba(0,0,0,0.12); }
.column-label { font-size: 12px; font-weight: 700; color: #546e7a; }
.column-date { font-size: 10px; color: #90a4ae; }
.line-chart { width: 100%; height: 166px; overflow: visible; }
.line-axis { stroke: #dfe6e9; stroke-width: 1; }
.line-fill { fill: rgba(38,198,218,0.20); }
.line-path { fill: none; stroke: #00838f; stroke-width: 4; stroke-linecap: round; stroke-linejoin: round; }
.line-dot { fill: #fff; stroke: #00838f; stroke-width: 3; }
.line-labels { display: flex; justify-content: space-between; margin-top: 6px; color: #546e7a; font-size: 12px; font-weight: 700; }
.donut-layout { display: grid; grid-template-columns: 150px 1fr; gap: 20px; align-items: center; min-height: 166px; }
.donut { width: 148px; height: 148px; border-radius: 50%; background: conic-gradient(#2e7d32 0 <?php echo $active_pct; ?>%, #c62828 <?php echo $active_pct; ?>% 100%); display: grid; place-items: center; box-shadow: inset 0 0 0 16px #f5f7f8; }
.donut-center { width: 86px; height: 86px; border-radius: 50%; background: #fff; display: flex; flex-direction: column; align-items: center; justify-content: center; box-shadow: 0 4px 14px rgba(0,0,0,0.08); }
.donut-number { font-size: 24px; font-weight: 900; color: #263238; line-height: 1; }
.donut-label { font-size: 11px; color: #78909c; margin-top: 4px; }
.legend { display: flex; flex-direction: column; gap: 11px; }
.legend-row { display: flex; align-items: center; justify-content: space-between; gap: 10px; font-size: 13px; color: #455a64; }
.legend-left { display: flex; align-items: center; gap: 8px; font-weight: 700; }
.legend-dot { width: 11px; height: 11px; border-radius: 50%; display: inline-block; }
.legend-dot.green { background: #2e7d32; }
.legend-dot.red { background: #c62828; }
.legend-dot.blue { background: #1565c0; }
.legend-dot.orange { background: #e65100; }
.progress-graph { display: flex; flex-direction: column; gap: 16px; padding-top: 18px; }
.progress-track { height: 36px; background: #eceff1; border-radius: 999px; overflow: hidden; display: flex; }
.progress-paid { width: <?php echo $paid_pct; ?>%; background: linear-gradient(90deg,#26a69a,#00695c); }
.progress-awaiting { flex: 1; background: linear-gradient(90deg,#ffb74d,#e65100); }
.progress-big { font-size: 34px; font-weight: 900; color: #00695c; }
.progress-caption { font-size: 13px; color: #607d8b; font-weight: 700; }

/* Slot popularity */
.slot-grid { display: flex; flex-direction: column; gap: 12px; }
.slot-row  { display: flex; align-items: center; gap: 14px; }
.slot-rank { width: 28px; height: 28px; border-radius: 50%; background: #e65100; color: white; font-size: 12px; font-weight: bold; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.slot-rank.rank-1 { background: #f9a825; }
.slot-rank.rank-2 { background: #90a4ae; }
.slot-rank.rank-3 { background: #a1887f; }
.slot-name     { width: 130px; font-size: 14px; font-weight: bold; color: #333; flex-shrink: 0; }
.slot-bar-wrap { flex: 1; background: #f1f8e9; border-radius: 8px; height: 26px; overflow: hidden; }
.slot-bar-fill { height: 100%; border-radius: 8px; background: linear-gradient(90deg,#43a047,#1b5e20); transition: width 0.8s cubic-bezier(0.4,0,0.2,1); }
.slot-count { width: 40px; text-align: right; font-size: 14px; font-weight: bold; color: #2e7d32; flex-shrink: 0; }
.slot-pct   { width: 42px; text-align: right; font-size: 12px; color: #aaa; flex-shrink: 0; }

/* Table */
table { width: 100%; border-collapse: collapse; font-size: 15px; }
thead tr { background: #f1f8e9; }
th { padding: 12px 14px; text-align: left; font-size: 12px; font-weight: 700; color: #2e7d32; text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 2px solid #c8e6c9; }
tbody tr { border-bottom: 1px solid #f0f0f0; transition: background 0.15s; animation: fadeIn 0.3s ease both; }
tbody tr:hover { background: #f9fbe7; }
tbody tr.row-cancelled { opacity: .8; }
td { padding: 13px 14px; color: #1a1a1a; vertical-align: middle; }
@keyframes fadeIn { from{opacity:0;transform:translateY(4px)} to{opacity:1;transform:translateY(0)} }

.id-badge   { display:inline-block; background:#e8f5e9; color:#27500a; border-radius:6px; padding:3px 10px; font-size:13px; font-weight:bold; }
.time-badge { display:inline-block; background:#e3f2fd; color:#0c447c; border-radius:6px; padding:3px 10px; font-size:13px; font-weight:bold; }
.txn-code   { font-family:monospace; font-size:12px; background:#f5f5f5; padding:3px 8px; border-radius:4px; color:#444; }
.amount-cell { font-weight:bold; color:#16a085; font-size:15px; }
.status-upcoming  { display:inline-flex;align-items:center;gap:5px;background:#e8f5e9;color:#27500a;border-radius:20px;padding:3px 10px;font-size:12px;font-weight:bold; }
.status-past      { display:inline-flex;align-items:center;gap:5px;background:#f5f5f5;color:#777;border-radius:20px;padding:3px 10px;font-size:12px; }
.status-cancelled { display:inline-flex;align-items:center;gap:5px;background:#ffebee;color:#b71c1c;border-radius:20px;padding:3px 10px;font-size:12px;font-weight:bold; }
.dot-g { width:7px;height:7px;border-radius:50%;background:#2e7d32;display:inline-block; }
.dot-x { width:7px;height:7px;border-radius:50%;background:#aaa;display:inline-block; }
.dot-r { width:7px;height:7px;border-radius:50%;background:#b71c1c;display:inline-block; }

.back-btn { display:inline-flex;align-items:center;gap:7px;padding:11px 22px;background:#f5f5f5;color:#555;border-radius:10px;text-decoration:none;font-size:15px;font-weight:bold;margin-top:26px;transition:0.2s; }
.back-btn:hover { background:#e8e8e8; }
@media(max-width:650px){
    .stats-grid { grid-template-columns: repeat(2,1fr); }
    .graph-grid { grid-template-columns: 1fr; }
    .graph-card { min-height: auto; padding: 15px; }
    .graph-head { flex-direction: column; }
    .donut-layout { grid-template-columns: 1fr; justify-items: center; }
    .legend { width: 100%; }
    .body { padding: 18px; }
    th, td { padding: 9px 8px; font-size: 13px; }
}
</style>
</head>
<body>
<div class="container">

    <div class="page-header">
        <div class="page-header-left">
            <h2><i class="fas fa-chart-bar"></i> Reports & Summary</h2>
            <p>Booking statistics and revenue overview</p>
        </div>
        <a href="admin_dashboard.php" class="back-header-btn">← Dashboard</a>
    </div>

    <div class="body">

        <div class="section-title">Overall Summary</div>
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="icon">👥</div>
                <div class="val"><?php echo $total_users; ?></div>
                <div class="lbl">Registered Users</div>
            </div>
            <div class="stat-card green">
                <div class="icon">📋</div>
                <div class="val"><?php echo $total_bookings; ?></div>
                <div class="lbl">Total Bookings</div>
            </div>
            <div class="stat-card orange">
                <div class="icon">💰</div>
                <div class="val">&#8377;<?php echo number_format($total_revenue); ?></div>
                <div class="lbl">Total Revenue</div>
            </div>
            <div class="stat-card teal">
                <div class="icon">📅</div>
                <div class="val"><?php echo $today_bookings; ?></div>
                <div class="lbl">Today's Bookings</div>
            </div>
            <div class="stat-card purple">
                <div class="icon">💵</div>
                <div class="val">&#8377;<?php echo number_format($today_revenue); ?></div>
                <div class="lbl">Today's Revenue</div>
            </div>
            <div class="stat-card red">
                <div class="icon">🗓️</div>
                <div class="val"><?php echo $month_bookings; ?></div>
                <div class="lbl">This Month</div>
            </div>
            <div class="stat-card indigo">
                <div class="icon">🔜</div>
                <div class="val"><?php echo $upcoming_c; ?></div>
                <div class="lbl">Upcoming</div>
            </div>
            <div class="stat-card brown">
                <div class="icon">💳</div>
                <div class="val"><?php echo $paid_c; ?></div>
                <div class="lbl">Paid Payments</div>
            </div>
            <div class="stat-card crimson">
                <div class="icon">✕</div>
                <div class="val"><?php echo $cancelled_c; ?></div>
                <div class="lbl">Cancelled</div>
            </div>
        </div>

        <div class="month-pill">
            <span class="m-label">📆 <?php echo date('F Y'); ?> Revenue</span>
            <span class="m-val">&#8377;<?php echo number_format($month_revenue); ?></span>
        </div>

        <div class="section-title">Visual Reports</div>
        <div class="graph-grid">
            <div class="graph-card">
                <div class="graph-head">
                    <div>
                        <div class="graph-title">Daily Bookings</div>
                        <div class="graph-subtitle">Column chart for the last 7 days</div>
                    </div>
                    <div class="graph-chip"><?php echo array_sum(array_column($daily,'count')); ?> total</div>
                </div>
                <div class="column-chart">
                    <?php foreach($daily as $d):
                        $pct = $max_daily > 0 ? round(($d['count'] / $max_daily) * 125) : 8;
                    ?>
                    <div class="column-item" title="<?php echo $d['sublabel'].': '.$d['count'].' bookings'; ?>">
                        <div class="column-value"><?php echo $d['count']; ?></div>
                        <div class="column-bar" style="height:<?php echo max($pct,8); ?>px;"></div>
                        <div class="column-label"><?php echo $d['label']; ?></div>
                        <div class="column-date"><?php echo $d['sublabel']; ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="graph-card">
                <div class="graph-head">
                    <div>
                        <div class="graph-title">Revenue Trend</div>
                        <div class="graph-subtitle">Line and area graph for paid revenue</div>
                    </div>
                    <div class="graph-chip">&#8377;<?php echo number_format(array_sum(array_column($daily,'revenue'))); ?></div>
                </div>
                <?php
                    $points = [];
                    $area_points = ["24,145"];
                    foreach($daily as $i => $d){
                        $x = 24 + ($i * 76);
                        $y = 145 - (($d['revenue'] / $max_rev) * 112);
                        $points[] = round($x, 1).",".round($y, 1);
                        $area_points[] = round($x, 1).",".round($y, 1);
                    }
                    $area_points[] = "480,145";
                ?>
                <svg class="line-chart" viewBox="0 0 504 166" preserveAspectRatio="none" role="img" aria-label="Revenue line chart">
                    <line class="line-axis" x1="24" y1="145" x2="480" y2="145"></line>
                    <line class="line-axis" x1="24" y1="34" x2="480" y2="34"></line>
                    <polygon class="line-fill" points="<?php echo implode(' ', $area_points); ?>"></polygon>
                    <polyline class="line-path" points="<?php echo implode(' ', $points); ?>"></polyline>
                    <?php foreach($points as $point): list($x,$y) = explode(',', $point); ?>
                    <circle class="line-dot" cx="<?php echo $x; ?>" cy="<?php echo $y; ?>" r="5"></circle>
                    <?php endforeach; ?>
                </svg>
                <div class="line-labels">
                    <?php foreach($daily as $d): ?>
                    <span><?php echo $d['label']; ?></span>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="graph-card">
                <div class="graph-head">
                    <div>
                        <div class="graph-title">Booking Status</div>
                        <div class="graph-subtitle">Donut graph of active and cancelled bookings</div>
                    </div>
                    <div class="graph-chip"><?php echo $booking_mix_total; ?> bookings</div>
                </div>
                <div class="donut-layout">
                    <div class="donut">
                        <div class="donut-center">
                            <div class="donut-number"><?php echo $active_pct; ?>%</div>
                            <div class="donut-label">Active</div>
                        </div>
                    </div>
                    <div class="legend">
                        <div class="legend-row">
                            <span class="legend-left"><span class="legend-dot green"></span>Active</span>
                            <strong><?php echo $total_bookings; ?> (<?php echo $active_pct; ?>%)</strong>
                        </div>
                        <div class="legend-row">
                            <span class="legend-left"><span class="legend-dot red"></span>Cancelled</span>
                            <strong><?php echo $cancelled_c; ?> (<?php echo $cancelled_pct; ?>%)</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="graph-card">
                <div class="graph-head">
                    <div>
                        <div class="graph-title">Payment Completion</div>
                        <div class="graph-subtitle">Stacked progress graph for paid bookings</div>
                    </div>
                    <div class="graph-chip"><?php echo $paid_pct; ?>% paid</div>
                </div>
                <div class="progress-graph">
                    <div>
                        <div class="progress-big"><?php echo $paid_pct; ?>%</div>
                        <div class="progress-caption"><?php echo $paid_booking_c; ?> paid bookings from <?php echo $total_bookings; ?> active bookings</div>
                    </div>
                    <div class="progress-track">
                        <div class="progress-paid"></div>
                        <div class="progress-awaiting"></div>
                    </div>
                    <div class="legend">
                        <div class="legend-row">
                            <span class="legend-left"><span class="legend-dot blue"></span>Paid</span>
                            <strong><?php echo $paid_booking_c; ?></strong>
                        </div>
                        <div class="legend-row">
                            <span class="legend-left"><span class="legend-dot orange"></span>Awaiting / unpaid</span>
                            <strong><?php echo $awaiting_c; ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-title">Slot Popularity</div>
        <?php if(count($slots) > 0): ?>
        <div class="slot-grid">
            <?php foreach($slots as $idx => $s):
                $pct = round(($s['cnt'] / $max_slot) * 100);
                $rankClass = $idx === 0 ? 'rank-1' : ($idx === 1 ? 'rank-2' : ($idx === 2 ? 'rank-3' : ''));
            ?>
            <div class="slot-row">
                <div class="slot-rank <?php echo $rankClass; ?>"><?php echo $idx+1; ?></div>
                <div class="slot-name"><?php echo htmlspecialchars($s['booking_time']); ?></div>
                <div class="slot-bar-wrap">
                    <div class="slot-bar-fill" style="width:<?php echo $pct; ?>%"></div>
                </div>
                <div class="slot-count"><?php echo $s['cnt']; ?></div>
                <div class="slot-pct"><?php echo $pct; ?>%</div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p style="color:#aaa;font-size:15px;">No slot data yet.</p>
        <?php endif; ?>

        <div class="section-title">Recent 10 Bookings</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>User Email</th>
                    <th>Date</th>
                    <th>Time Slot</th>
                    <th>Amount</th>
                    <th>TXN ID</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($recent) > 0): ?>
                <?php foreach($recent as $i => $r):
                    $isCancelled = !empty($r['is_cancelled']);
                    $isUpcoming  = !$isCancelled && $r['booking_date'] >= $today;
                ?>
                <tr class="<?php echo $isCancelled ? 'row-cancelled' : ''; ?>" style="animation-delay:<?php echo $i * 0.04; ?>s">
                    <td><span class="id-badge">B-<?php echo str_pad($i+1, 3, '0', STR_PAD_LEFT); ?></span></td>
                    <td><?php echo htmlspecialchars($r['user_email']); ?></td>
                    <td><strong><?php echo $r['booking_date'] ? date('M d, Y', strtotime($r['booking_date'])) : '&mdash;'; ?></strong></td>
                    <td><span class="time-badge"><?php echo htmlspecialchars($r['booking_time']); ?></span></td>
                    <td class="amount-cell"><?php echo $r['amount'] ? '&#8377;'.number_format($r['amount']) : '<span style="color:#bbb">&mdash;</span>'; ?></td>
                    <td><?php echo $r['txn_id'] ? '<span class="txn-code">'.htmlspecialchars($r['txn_id']).'</span>' : '<span style="color:#bbb">&mdash;</span>'; ?></td>
                    <td>
                        <?php if($isCancelled): ?>
                            <span class="status-cancelled"><span class="dot-r"></span>Cancelled</span>
                        <?php elseif($isUpcoming): ?>
                            <span class="status-upcoming"><span class="dot-g"></span>Upcoming</span>
                        <?php else: ?>
                            <span class="status-past"><span class="dot-x"></span>Completed</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr><td colspan="7" style="text-align:center;color:#aaa;padding:28px;font-size:14px;">No bookings found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="admin_dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

    </div>
</div>
</body>
</html>
