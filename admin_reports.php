<?php
session_start();
include "badmintondb.php";

if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

$today = date('Y-m-d');
$month = date('Y-m');

/* ── Summary Stats ── */
$total_users    = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
$total_bookings = $conn->query("SELECT COUNT(*) AS c FROM bookings")->fetch_assoc()['c'];
$total_revenue  = $conn->query("SELECT SUM(amount) AS s FROM payments WHERE payment_status='Paid'")->fetch_assoc()['s'] ?? 0;
$today_bookings = $conn->query("SELECT COUNT(*) AS c FROM bookings WHERE booking_date='$today'")->fetch_assoc()['c'];
$today_revenue  = $conn->query("SELECT SUM(p.amount) AS s FROM payments p JOIN bookings b ON p.booking_id=b.booking_id WHERE b.booking_date='$today' AND p.payment_status='Paid'")->fetch_assoc()['s'] ?? 0;
$month_bookings = $conn->query("SELECT COUNT(*) AS c FROM bookings WHERE DATE_FORMAT(booking_date,'%Y-%m')='$month'")->fetch_assoc()['c'];
$month_revenue  = $conn->query("SELECT SUM(p.amount) AS s FROM payments p JOIN bookings b ON p.booking_id=b.booking_id WHERE DATE_FORMAT(b.booking_date,'%Y-%m')='$month' AND p.payment_status='Paid'")->fetch_assoc()['s'] ?? 0;
$upcoming_c     = $conn->query("SELECT COUNT(*) AS c FROM bookings WHERE booking_date > '$today'")->fetch_assoc()['c'];
$paid_c         = $conn->query("SELECT COUNT(*) AS c FROM payments WHERE payment_status='Paid'")->fetch_assoc()['c'];

/* ── Slot popularity ── */
$slot_result = $conn->query("SELECT booking_time, COUNT(*) AS cnt FROM bookings GROUP BY booking_time ORDER BY cnt DESC");
$slots = []; $max_slot = 1;
if($slot_result) while($r = $slot_result->fetch_assoc()){
    $slots[] = $r;
    if($r['cnt'] > $max_slot) $max_slot = $r['cnt'];
}

/* ── Last 7 days ── */
$daily = [];
for($d = 6; $d >= 0; $d--){
    $day = date('Y-m-d', strtotime("-$d days"));
    $c   = $conn->query("SELECT COUNT(*) AS c FROM bookings WHERE booking_date='$day'")->fetch_assoc()['c'];
    $rev = $conn->query("SELECT SUM(p.amount) AS s FROM payments p JOIN bookings b ON p.booking_id=b.booking_id WHERE b.booking_date='$day' AND p.payment_status='Paid'")->fetch_assoc()['s'] ?? 0;
    $daily[] = ['date'=>$day, 'label'=>date('D', strtotime($day)), 'sublabel'=>date('d M', strtotime($day)), 'count'=>(int)$c, 'revenue'=>(float)$rev];
}
$max_daily = max(array_column($daily,'count')) ?: 1;

/* ── Recent 10 bookings ── */
$recent_result = $conn->query("SELECT b.booking_id, b.user_email, b.booking_date, b.booking_time, p.amount, p.txn_id
    FROM bookings b LEFT JOIN payments p ON b.booking_id=p.booking_id ORDER BY b.booking_date DESC LIMIT 10");
$recent = [];
if($recent_result) while($r = $recent_result->fetch_assoc()) $recent[] = $r;
?>
<!DOCTYPE html>
<html>
<head>
<title>Reports & Summary – Admin</title>
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
body { font-family: Arial, sans-serif; min-height: 100vh; padding: 36px 16px; }

.container {
    max-width: 1080px; margin: auto;
    background: rgba(255,255,255,0.98);
    border-radius: 20px; overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.28);
}

/* ── Header ── */
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

/* ── Body ── */
.body { padding: 28px 32px 40px; }

/* ── Section title ── */
.section-title {
    font-size: 13px; font-weight: 700; color: #999;
    text-transform: uppercase; letter-spacing: 2px;
    margin: 32px 0 16px;
    display: flex; align-items: center; gap: 12px;
}
.section-title::after { content:''; flex:1; height:1px; background:#e8e8e8; }
.section-title:first-child { margin-top: 0; }

/* ── Stat cards ── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(155px, 1fr));
    gap: 16px;
}
.stat-card {
    border-radius: 16px; padding: 20px 16px; text-align: center;
    border: 1px solid transparent; transition: transform 0.2s;
}
.stat-card:hover { transform: translateY(-3px); }

/* Darker backgrounds for clear text */
.stat-card.green  { background: #2e7d32; border-color: #1b5e20; }
.stat-card.blue   { background: #1565c0; border-color: #0d47a1; }
.stat-card.orange { background: #e65100; border-color: #bf360c; }
.stat-card.teal   { background: #00695c; border-color: #004d40; }
.stat-card.purple { background: #6a1b9a; border-color: #4a148c; }
.stat-card.red    { background: #c62828; border-color: #b71c1c; }
.stat-card.indigo { background: #283593; border-color: #1a237e; }
.stat-card.brown  { background: #4e342e; border-color: #3e2723; }

.stat-card .icon { font-size: 28px; margin-bottom: 10px; }
.stat-card .val  { font-size: 26px; font-weight: bold; color: #fff; text-shadow: 0 1px 6px rgba(0,0,0,0.3); }
.stat-card .lbl  { font-size: 12px; color: rgba(255,255,255,0.88); text-transform: uppercase; letter-spacing: 1px; margin-top: 5px; font-weight: 600; }

/* ── Month pill ── */
.month-pill {
    margin-top: 14px; padding: 16px 22px;
    background: #fff3e0; border: 1px solid #ffe0b2;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: space-between;
}
.month-pill .m-label { font-size: 14px; color: #555; font-weight: 600; }
.month-pill .m-val   { font-size: 22px; font-weight: bold; color: #e65100; }

/* ── Bar chart ── */
.chart-wrap {
    background: #f9fbe7; border: 1px solid #dcedc8;
    border-radius: 16px; padding: 20px 18px 14px;
}
.bar-chart { display: flex; align-items: flex-end; gap: 10px; height: 160px; }
.bar-col   { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 6px; height: 100%; justify-content: flex-end; position: relative; }
.bar {
    width: 100%; border-radius: 8px 8px 0 0;
    min-height: 6px; cursor: pointer;
    background: linear-gradient(180deg,#43a047,#1b5e20);
    transition: opacity 0.2s, transform 0.15s;
    position: relative;
}
.bar:hover { opacity: 0.85; transform: scaleY(1.03); transform-origin: bottom; }

/* Tooltip */
.bar-tooltip {
    display: none; position: absolute;
    bottom: calc(100% + 8px); left: 50%;
    transform: translateX(-50%);
    background: #1b5e20; color: #fff;
    font-size: 12px; font-weight: 600;
    padding: 5px 10px; border-radius: 7px;
    white-space: nowrap; z-index: 10;
    pointer-events: none;
}
.bar-tooltip::after {
    content:''; position:absolute; top:100%; left:50%;
    transform:translateX(-50%);
    border:5px solid transparent; border-top-color:#1b5e20;
}
.bar:hover .bar-tooltip { display: block; }

.bar-count { font-size: 13px; font-weight: bold; color: #2e7d32; }
.bar-label { font-size: 12px; font-weight: 700; color: #555; text-align: center; }
.bar-sublabel { font-size: 10px; color: #999; text-align: center; }

/* ── Revenue trend ── */
.rev-chart-wrap {
    background: #e8f5e9; border: 1px solid #c8e6c9;
    border-radius: 16px; padding: 20px 18px 14px;
    margin-top: 16px;
}
.rev-chart-title { font-size: 13px; font-weight: 700; color: #2e7d32; margin-bottom: 14px; }
.rev-bars { display: flex; align-items: flex-end; gap: 10px; height: 120px; }
.rev-col  { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 5px; height: 100%; justify-content: flex-end; }
.rev-bar  {
    width: 100%; border-radius: 8px 8px 0 0; min-height: 4px;
    background: linear-gradient(180deg,#26c6da,#00838f);
    position: relative; transition: opacity 0.2s;
}
.rev-bar:hover { opacity: 0.82; }
.rev-bar:hover .bar-tooltip { display: block; background: #00695c; }
.rev-bar:hover .bar-tooltip::after { border-top-color: #00695c; }
.rev-label { font-size: 12px; font-weight: 700; color: #555; }
.rev-amount { font-size: 11px; color: #00838f; font-weight: bold; }

/* ── Slot popularity ── */
.slot-grid { display: flex; flex-direction: column; gap: 12px; }
.slot-row  { display: flex; align-items: center; gap: 14px; }
.slot-rank {
    width: 28px; height: 28px; border-radius: 50%;
    background: #e65100; color: white;
    font-size: 12px; font-weight: bold;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.slot-rank.rank-1 { background: #f9a825; }
.slot-rank.rank-2 { background: #90a4ae; }
.slot-rank.rank-3 { background: #a1887f; }
.slot-name { width: 130px; font-size: 14px; font-weight: bold; color: #333; flex-shrink: 0; }
.slot-bar-wrap { flex: 1; background: #f1f8e9; border-radius: 8px; height: 26px; overflow: hidden; }
.slot-bar-fill {
    height: 100%; border-radius: 8px;
    background: linear-gradient(90deg,#43a047,#1b5e20);
    transition: width 0.8s cubic-bezier(0.4,0,0.2,1);
}
.slot-count { width: 40px; text-align: right; font-size: 14px; font-weight: bold; color: #2e7d32; flex-shrink: 0; }
.slot-pct   { width: 42px; text-align: right; font-size: 12px; color: #aaa; flex-shrink: 0; }

/* ── Recent table ── */
table { width: 100%; border-collapse: collapse; font-size: 15px; }
thead tr { background: #f1f8e9; }
th {
    padding: 12px 14px; text-align: left;
    font-size: 12px; font-weight: 700; color: #2e7d32;
    text-transform: uppercase; letter-spacing: 0.8px;
    border-bottom: 2px solid #c8e6c9;
}
tbody tr { border-bottom: 1px solid #f0f0f0; transition: background 0.15s; animation: fadeIn 0.3s ease both; }
tbody tr:hover { background: #f9fbe7; }
td { padding: 13px 14px; color: #1a1a1a; vertical-align: middle; }
@keyframes fadeIn { from{opacity:0;transform:translateY(4px)} to{opacity:1;transform:translateY(0)} }

.id-badge   { display:inline-block; background:#e8f5e9; color:#27500a; border-radius:6px; padding:3px 10px; font-size:13px; font-weight:bold; }
.time-badge { display:inline-block; background:#e3f2fd; color:#0c447c; border-radius:6px; padding:3px 10px; font-size:13px; font-weight:bold; }
.txn-code   { font-family:monospace; font-size:12px; background:#f5f5f5; padding:3px 8px; border-radius:4px; color:#444; }
.amount-cell { font-weight:bold; color:#16a085; font-size:15px; }
.status-upcoming { display:inline-flex; align-items:center; gap:5px; background:#e8f5e9; color:#27500a; border-radius:20px; padding:3px 10px; font-size:12px; font-weight:bold; }
.status-past     { display:inline-flex; align-items:center; gap:5px; background:#f5f5f5; color:#777; border-radius:20px; padding:3px 10px; font-size:12px; }
.dot-g { width:7px; height:7px; border-radius:50%; background:#2e7d32; display:inline-block; }
.dot-x { width:7px; height:7px; border-radius:50%; background:#aaa; display:inline-block; }

/* ── Back btn ── */
.back-btn { display:inline-flex; align-items:center; gap:7px; padding:11px 22px; background:#f5f5f5; color:#555; border-radius:10px; text-decoration:none; font-size:15px; font-weight:bold; margin-top:26px; transition:0.2s; }
.back-btn:hover { background:#e8e8e8; }

@media(max-width:650px){
    .stats-grid { grid-template-columns: repeat(2,1fr); }
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

        <!-- ── Overall Stats ── -->
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
                <div class="val">₹<?php echo number_format($total_revenue); ?></div>
                <div class="lbl">Total Revenue</div>
            </div>
            <div class="stat-card teal">
                <div class="icon">📅</div>
                <div class="val"><?php echo $today_bookings; ?></div>
                <div class="lbl">Today's Bookings</div>
            </div>
            <div class="stat-card purple">
                <div class="icon">💵</div>
                <div class="val">₹<?php echo number_format($today_revenue); ?></div>
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
        </div>

        <!-- Month pill -->
        <div class="month-pill">
            <span class="m-label">📆 <?php echo date('F Y'); ?> Revenue</span>
            <span class="m-val">₹<?php echo number_format($month_revenue); ?></span>
        </div>

        <!-- ── Bookings bar chart ── -->
        <div class="section-title">Last 7 Days — Daily Bookings</div>
        <div class="chart-wrap">
            <div class="bar-chart">
                <?php foreach($daily as $d):
                    $pct = $max_daily > 0 ? round(($d['count'] / $max_daily) * 130) : 6;
                ?>
                <div class="bar-col">
                    <div class="bar-count"><?php echo $d['count']; ?></div>
                    <div class="bar" style="height:<?php echo max($pct,6); ?>px;">
                        <div class="bar-tooltip"><?php echo $d['sublabel'].': '.$d['count'].' bookings'; ?></div>
                    </div>
                    <div class="bar-label"><?php echo $d['label']; ?></div>
                    <div class="bar-sublabel"><?php echo $d['sublabel']; ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ── Revenue bar chart ── -->
        <?php
        $max_rev = max(array_column($daily,'revenue')) ?: 1;
        ?>
        <div class="rev-chart-wrap">
            <div class="rev-chart-title">📈 Daily Revenue (Last 7 Days)</div>
            <div class="rev-bars">
                <?php foreach($daily as $d):
                    $rpct = $max_rev > 0 ? round(($d['revenue'] / $max_rev) * 100) : 4;
                ?>
                <div class="rev-col">
                    <div class="rev-amount">₹<?php echo $d['revenue'] > 0 ? number_format($d['revenue']) : '0'; ?></div>
                    <div class="rev-bar" style="height:<?php echo max($rpct,4); ?>px;">
                        <div class="bar-tooltip" style="background:#00695c;">₹<?php echo number_format($d['revenue']); ?><br><?php echo $d['sublabel']; ?></div>
                    </div>
                    <div class="rev-label"><?php echo $d['label']; ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ── Slot Popularity ── -->
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

        <!-- ── Recent 10 Bookings ── -->
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
                    $is_upcoming = $r['booking_date'] >= $today;
                ?>
                <tr style="animation-delay:<?php echo $i * 0.04; ?>s">
                    <td><span class="id-badge">B-<?php echo str_pad($i+1, 3, '0', STR_PAD_LEFT); ?></span></td>
                    <td><?php echo htmlspecialchars($r['user_email']); ?></td>
                    <td><strong><?php echo $r['booking_date'] ? date('M d, Y', strtotime($r['booking_date'])) : '—'; ?></strong></td>
                    <td><span class="time-badge"><?php echo htmlspecialchars($r['booking_time']); ?></span></td>
                    <td class="amount-cell"><?php echo $r['amount'] ? '₹'.number_format($r['amount']) : '<span style="color:#bbb">—</span>'; ?></td>
                    <td><?php echo $r['txn_id'] ? '<span class="txn-code">'.htmlspecialchars($r['txn_id']).'</span>' : '<span style="color:#bbb">—</span>'; ?></td>
                    <td>
                        <?php if($is_upcoming): ?>
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