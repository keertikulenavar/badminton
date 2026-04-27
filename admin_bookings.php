<?php
session_start();
include "badmintondb.php";

if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

/* Cancel/delete a booking */
if(isset($_GET['cancel'])){
    $bid = (int)$_GET['cancel'];
    $conn->query("DELETE FROM bookings WHERE booking_id='$bid'");
    echo "<script>alert('Booking cancelled successfully.');window.location='admin_bookings.php';</script>";
    exit();
}

$today = date('Y-m-d');

/* Filter */
$filter_date   = isset($_GET['filter_date'])   ? $conn->real_escape_string($_GET['filter_date'])   : '';
$filter_status = isset($_GET['filter_status']) ? $_GET['filter_status'] : '';

$where = "1=1";
if($filter_date)   $where .= " AND b.booking_date = '$filter_date'";
if($filter_status === 'upcoming')  $where .= " AND b.booking_date >= '$today'";
if($filter_status === 'completed') $where .= " AND b.booking_date < '$today'";

$sql = "SELECT b.booking_id, b.user_email, b.booking_date, b.booking_time,
               p.payment_status, p.txn_id
        FROM bookings b
        LEFT JOIN payments p ON b.booking_id = p.booking_id
        WHERE $where
        ORDER BY b.booking_date DESC";
$result = $conn->query($sql);
$rows   = [];
$total  = 0;
if($result) while($r = $result->fetch_assoc()){ $rows[] = $r; $total++; }

/* Stats */
$all        = $conn->query("SELECT COUNT(*) AS c FROM bookings")->fetch_assoc()['c'];
$upcoming_c = $conn->query("SELECT COUNT(*) AS c FROM bookings WHERE booking_date >= '$today'")->fetch_assoc()['c'];
$past_c     = $conn->query("SELECT COUNT(*) AS c FROM bookings WHERE booking_date < '$today'")->fetch_assoc()['c'];
$today_c    = $conn->query("SELECT COUNT(*) AS c FROM bookings WHERE booking_date = '$today'")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html>
<head>
<title>Manage Bookings – Admin</title>
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
    max-width: 1000px; margin: auto;
    background: rgba(255,255,255,0.98);
    border-radius: 20px; overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.25);
}

.page-header {
    background: linear-gradient(135deg,#1b5e20,#2e7d32);
    padding: 24px 30px 20px; color: white;
    display: flex; justify-content: space-between; align-items: center;
}
.page-header-left h2 { font-size: 20px; margin-bottom: 3px; }
.page-header-left p  { font-size: 13px; opacity: 0.8; }
.back-header-btn {
    background: rgba(255,255,255,0.18);
    border: 1px solid rgba(255,255,255,0.3);
    color: white; padding: 8px 16px;
    border-radius: 8px; text-decoration: none;
    font-size: 13px; font-weight: bold; transition: 0.2s;
}
.back-header-btn:hover { background: rgba(255,255,255,0.28); }

.summary-bar { display: flex; border-bottom: 1px solid #e8f5e9; }
.sum-cell {
    flex: 1; padding: 14px 10px; text-align: center;
    border-right: 1px solid #e8f5e9;
}
.sum-cell:last-child { border-right: none; }
.sum-cell .lbl { font-size: 11px; color: #777; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 4px; }
.sum-cell .val { font-size: 20px; font-weight: bold; color: #1b5e20; }
.sum-cell .val.blue  { color: #1565c0; }
.sum-cell .val.gray  { color: #888; }
.sum-cell .val.orange{ color: #e65100; }

.table-wrap { padding: 22px 26px 28px; }

/* Filter bar */
.filter-bar {
    display: flex; gap: 12px; align-items: center;
    margin-bottom: 18px; flex-wrap: wrap;
}
.filter-bar input[type=date],
.filter-bar select {
    padding: 9px 13px;
    border: 1.5px solid #c8e6c9; border-radius: 10px;
    font-size: 14px; color: #1a1a1a; outline: none;
    transition: border 0.2s; background: white;
}
.filter-bar input[type=date]:focus,
.filter-bar select:focus { border-color: #2e7d32; }
.btn-filter {
    padding: 9px 18px;
    background: #2e7d32; color: white;
    border: none; border-radius: 10px;
    font-size: 14px; font-weight: bold; cursor: pointer;
}
.btn-filter:hover { background: #1b5e20; }
.btn-reset {
    padding: 9px 14px;
    background: #f5f5f5; color: #555;
    border: 1.5px solid #ddd; border-radius: 10px;
    font-size: 14px; text-decoration: none; font-weight: bold;
}
.btn-reset:hover { background: #eee; }
.search-input {
    flex: 1; min-width: 160px;
    padding: 9px 13px;
    border: 1.5px solid #c8e6c9; border-radius: 10px;
    font-size: 14px; color: #1a1a1a; outline: none;
}
.search-input:focus { border-color: #2e7d32; }

table { width: 100%; border-collapse: collapse; font-size: 15px; }
thead tr { background: #f1f8e9; }
th {
    padding: 12px 13px; text-align: left;
    font-size: 12px; font-weight: 700; color: #2e7d32;
    text-transform: uppercase; letter-spacing: 0.7px;
    border-bottom: 2px solid #c8e6c9;
}
tbody tr { border-bottom: 1px solid #f0f0f0; transition: background 0.15s; animation: fadeIn 0.3s ease both; }
tbody tr:hover { background: #f9fbe7; }
td { padding: 13px 13px; color: #1a1a1a; vertical-align: middle; }

@keyframes fadeIn { from{opacity:0;transform:translateY(5px)} to{opacity:1;transform:translateY(0)} }

.id-badge { display: inline-block; background: #f1f8e9; color: #27500a; border-radius: 6px; padding: 3px 10px; font-size: 13px; font-weight: bold; }
.time-badge { display: inline-block; background: #e3f2fd; color: #0c447c; border-radius: 6px; padding: 4px 11px; font-size: 13px; font-weight: bold; }
.date-main { font-weight: bold; color: #1b5e20; }
.date-sub  { font-size: 11px; color: #888; margin-top: 2px; }

.status-upcoming { display: inline-flex; align-items: center; gap: 5px; background: #e8f5e9; color: #27500a; border-radius: 20px; padding: 3px 10px; font-size: 12px; font-weight: bold; }
.status-past     { display: inline-flex; align-items: center; gap: 5px; background: #f5f5f5; color: #555; border-radius: 20px; padding: 3px 10px; font-size: 12px; }
.dot-green { width: 7px; height: 7px; border-radius: 50%; background: #2e7d32; display: inline-block; }
.dot-gray  { width: 7px; height: 7px; border-radius: 50%; background: #aaa;    display: inline-block; }

.cancel-btn { display: inline-block; background: #ffebee; color: #b71c1c; border: 1px solid #ffcdd2; padding: 5px 11px; border-radius: 7px; font-size: 12px; font-weight: bold; text-decoration: none; transition: 0.2s; }
.cancel-btn:hover { background: #ffcdd2; }
.past-label { color: #bbb; font-size: 12px; }

.txn-code { font-family: monospace; font-size: 12px; color: #555; background: #f5f5f5; padding: 2px 7px; border-radius: 4px; }

tfoot td { padding: 12px 13px; font-size: 13px; color: #888; border-top: 2px solid #e8f5e9; }

.empty-state { text-align: center; padding: 48px 20px; color: #999; }
.empty-state .icon { font-size: 44px; margin-bottom: 14px; }

.back-btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; background: #f5f5f5; color: #555; border-radius: 10px; text-decoration: none; font-size: 14px; font-weight: bold; margin-top: 18px; transition: 0.2s; }
.back-btn:hover { background: #eeeeee; }

@media(max-width:700px){
    .table-wrap { padding: 14px; }
    th, td { padding: 8px 7px; font-size: 12px; }
    .filter-bar { flex-direction: column; }
}
</style>
</head>
<body>

<div class="container">

    <div class="page-header">
        <div class="page-header-left">
            <h2><i class="fas fa-calendar-check"></i> Manage Bookings</h2>
            <p>All court reservations across all users</p>
        </div>
        <a href="admin_dashboard.php" class="back-header-btn">← Dashboard</a>
    </div>

    <div class="summary-bar">
        <div class="sum-cell"><div class="lbl">All Bookings</div><div class="val"><?php echo $all; ?></div></div>
        <div class="sum-cell"><div class="lbl">Upcoming</div><div class="val blue"><?php echo $upcoming_c; ?></div></div>
        <div class="sum-cell"><div class="lbl">Completed</div><div class="val gray"><?php echo $past_c; ?></div></div>
        <div class="sum-cell"><div class="lbl">Today</div><div class="val orange"><?php echo $today_c; ?></div></div>
    </div>

    <div class="table-wrap">

        <!-- Filters -->
        <form method="GET" action="">
            <div class="filter-bar">
                <input type="text" class="search-input" id="searchInput" placeholder="🔍 Search email or time...">
                <input type="date" name="filter_date" value="<?php echo htmlspecialchars($filter_date); ?>">
                <select name="filter_status">
                    <option value="">All Status</option>
                    <option value="upcoming"  <?php echo $filter_status==='upcoming'  ? 'selected':''; ?>>Upcoming</option>
                    <option value="completed" <?php echo $filter_status==='completed' ? 'selected':''; ?>>Completed</option>
                </select>
                <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Filter</button>
                <a href="admin_bookings.php" class="btn-reset">✕ Reset</a>
            </div>
        </form>

        <?php if($total > 0): ?>
        <table id="bookTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User Email</th>
                    <th>Date</th>
                    <th>Time Slot</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $serial = 1;
                foreach($rows as $i => $r):
                    $is_upcoming = ($r['booking_date'] >= $today);
                    $display_date = date('M d, Y', strtotime($r['booking_date']));
                    $day_name = date('l', strtotime($r['booking_date']));
                ?>
                <tr style="animation-delay:<?php echo $i * 0.03; ?>s">
                    <td><span class="id-badge"><?php echo $serial++; ?></span></td>
                    <td><?php echo htmlspecialchars($r['user_email']); ?></td>
                    <td>
                        <div class="date-main"><?php echo $display_date; ?></div>
                        <div class="date-sub"><?php echo $day_name; ?></div>
                    </td>
                    <td><span class="time-badge"><?php echo htmlspecialchars($r['booking_time']); ?></span></td>
                    <td>
                        <?php if($is_upcoming): ?>
                            <span class="status-upcoming"><span class="dot-green"></span>Upcoming</span>
                        <?php else: ?>
                            <span class="status-past"><span class="dot-gray"></span>Completed</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($is_upcoming): ?>
                        <a class="cancel-btn"
                           href="admin_bookings.php?cancel=<?php echo $r['booking_id']; ?>"
                           onclick="return confirm('Cancel booking #<?php echo $r['booking_id']; ?>?')">
                           ✕ Cancel
                        </a>
                        <?php else: ?>
                        <span class="past-label">Done</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" id="rowCount">Showing <?php echo $total; ?> of <?php echo $all; ?> bookings</td>
                </tr>
            </tfoot>
        </table>

        <?php else: ?>
        <div class="empty-state">
            <div class="icon">📋</div>
            <p>No bookings found for the selected filter.</p>
        </div>
        <?php endif; ?>

        <a href="admin_dashboard.php" class="back-btn">← Back to Dashboard</a>

    </div>
</div>

<script>
const si = document.getElementById('searchInput');
if(si){
    si.addEventListener('input', function(){
        const q = this.value.toLowerCase();
        const rows = document.querySelectorAll('#bookTable tbody tr');
        let v = 0;
        rows.forEach(row => {
            const show = row.textContent.toLowerCase().includes(q);
            row.style.display = show ? '' : 'none';
            if(show) v++;
        });
        document.getElementById('rowCount').textContent = 'Showing ' + v + ' of <?php echo $all; ?> bookings';
    });
}
</script>
</body>
</html>