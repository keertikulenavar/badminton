<?php
session_start();
include "badmintondb.php";

if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

/* Fetch all payments joined with booking info */
$sql = "SELECT p.payment_id, p.booking_id, p.user_email,
               p.amount, p.payment_status, p.txn_id,
               b.booking_date, b.booking_time
        FROM payments p
        LEFT JOIN bookings b ON p.booking_id = b.booking_id
        ORDER BY p.payment_id DESC";
$result   = $conn->query($sql);
$rows     = [];
$total    = 0;
$revenue  = 0;
if($result) while($r = $result->fetch_assoc()){ $rows[] = $r; $total++; $revenue += $r['amount']; }
?>
<!DOCTYPE html>
<html>
<head>
<title>Payment Records – Admin</title>
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
    max-width: 1020px; margin: auto;
    background: rgba(255,255,255,0.98);
    border-radius: 20px; overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.25);
}

.page-header {
    background: linear-gradient(135deg,#16a085,#0e6655);
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

.summary-bar { display: flex; border-bottom: 1px solid #e0f2f1; }
.sum-cell {
    flex: 1; padding: 15px 10px; text-align: center;
    border-right: 1px solid #e0f2f1;
}
.sum-cell:last-child { border-right: none; }
.sum-cell .lbl { font-size: 11px; color: #777; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 4px; }
.sum-cell .val { font-size: 22px; font-weight: bold; color: #16a085; }
.sum-cell .val.green { color: #2e7d32; }

.table-wrap { padding: 22px 26px 28px; }

.search-row { margin-bottom: 16px; }
.search-row input {
    width: 100%; padding: 10px 14px;
    border: 1.5px solid #b2dfdb; border-radius: 10px;
    font-size: 14px; color: #1a1a1a; outline: none;
}
.search-row input:focus { border-color: #16a085; }

table { width: 100%; border-collapse: collapse; font-size: 15px; }
thead tr { background: #e0f2f1; }
th {
    padding: 12px 13px; text-align: left;
    font-size: 12px; font-weight: 700; color: #16a085;
    text-transform: uppercase; letter-spacing: 0.7px;
    border-bottom: 2px solid #b2dfdb;
}
tbody tr { border-bottom: 1px solid #f0f0f0; transition: background 0.15s; animation: fadeIn 0.3s ease both; }
tbody tr:hover { background: #e0f2f133; }
td { padding: 13px 13px; color: #1a1a1a; vertical-align: middle; }

@keyframes fadeIn { from{opacity:0;transform:translateY(5px)} to{opacity:1;transform:translateY(0)} }

.id-badge       { display:inline-block; background:#e0f2f1; color:#004d40; border-radius:6px; padding:3px 10px; font-size:13px; font-weight:bold; }
.booking-badge  { display:inline-block; background:#e3f2fd; color:#0c447c; border-radius:6px; padding:3px 10px; font-size:13px; font-weight:bold; }
.amount-cell    { font-size: 16px; font-weight: bold; color: #16a085; }
.txn-code       { font-family: monospace; font-size: 13px; background: #f5f5f5; padding: 3px 8px; border-radius: 4px; color: #333; }
.status-pill    { display:inline-flex; align-items:center; gap:5px; background:#e8f5e9; color:#27500a; border-radius:20px; padding:4px 12px; font-size:13px; font-weight:bold; }
.dot            { width:7px; height:7px; border-radius:50%; background:#2e7d32; display:inline-block; }

.time-badge { display:inline-block; background:#f3e5f5; color:#4a148c; border-radius:6px; padding:2px 9px; font-size:12px; font-weight:bold; }

tfoot td { padding: 12px 13px; font-size: 13px; color: #888; border-top: 2px solid #e0f2f1; }
tfoot .total-row { font-weight: bold; color: #16a085; font-size: 14px; }

.empty-state { text-align: center; padding: 48px 20px; color: #999; }
.empty-state .icon { font-size: 44px; margin-bottom: 14px; }

.back-btn { display:inline-flex; align-items:center; gap:6px; padding:10px 20px; background:#f5f5f5; color:#555; border-radius:10px; text-decoration:none; font-size:14px; font-weight:bold; margin-top:18px; transition:0.2s; }
.back-btn:hover { background: #eeeeee; }

@media(max-width:700px){
    .table-wrap { padding: 14px; }
    th, td { padding: 8px 7px; font-size: 12px; }
}
</style>
</head>
<body>

<div class="container">

    <div class="page-header">
        <div class="page-header-left">
            <h2><i class="fas fa-credit-card"></i> Payment Records</h2>
            <p>All payment transactions collected</p>
        </div>
        <a href="admin_dashboard.php" class="back-header-btn">← Dashboard</a>
    </div>

    <div class="summary-bar">
        <div class="sum-cell">
            <div class="lbl">Total Payments</div>
            <div class="val"><?php echo $total; ?></div>
        </div>
        <div class="sum-cell">
            <div class="lbl">Total Revenue</div>
            <div class="val green">₹<?php echo number_format($revenue); ?></div>
        </div>
        <div class="sum-cell">
            <div class="lbl">Avg Per Booking</div>
            <div class="val" style="color:#e65100;">₹<?php echo $total > 0 ? number_format($revenue/$total) : 0; ?></div>
        </div>
    </div>

    <div class="table-wrap">

        <?php if($total > 0): ?>
        <div class="search-row">
            <input type="text" id="searchInput" placeholder="🔍  Search by email, TXN ID or booking ID...">
        </div>

        <table id="payTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Booking Ref</th>
                    <th>User Email</th>
                    <th>Date & Time</th>
                    <th>Amount</th>
                    <th>TXN / UTR</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $serial = 1;
                foreach($rows as $i => $r): ?>
                <tr style="animation-delay:<?php echo $i * 0.03; ?>s">
                    <td><span class="id-badge"><?php echo $serial; ?></span></td>
                    <td><span class="booking-badge">B-<?php echo $serial++; ?></span></td>
                    <td><?php echo htmlspecialchars($r['user_email']); ?></td>
                    <td>
                        <?php if($r['booking_date']): ?>
                            <strong><?php echo date('M d, Y', strtotime($r['booking_date'])); ?></strong><br>
                            <span class="time-badge"><?php echo htmlspecialchars($r['booking_time']); ?></span>
                        <?php else: ?>
                            <span style="color:#bbb;">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="amount-cell">₹<?php echo number_format($r['amount']); ?></td>
                    <td>
                        <?php echo $r['txn_id']
                            ? '<span class="txn-code">'.htmlspecialchars($r['txn_id']).'</span>'
                            : '<span style="color:#bbb">—</span>'; ?>
                    </td>
                    <td>
                        <span class="status-pill">
                            <span class="dot"></span>
                            <?php echo htmlspecialchars($r['payment_status']); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" id="rowCount">Showing <?php echo $total; ?> of <?php echo $total; ?> payments</td>
                    <td colspan="3" class="total-row">Total: ₹<?php echo number_format($revenue); ?></td>
                </tr>
            </tfoot>
        </table>

        <?php else: ?>
        <div class="empty-state">
            <div class="icon">💳</div>
            <p>No payment records found yet.</p>
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
        const rows = document.querySelectorAll('#payTable tbody tr');
        let v = 0;
        rows.forEach(row => {
            const show = row.textContent.toLowerCase().includes(q);
            row.style.display = show ? '' : 'none';
            if(show) v++;
        });
        document.getElementById('rowCount').textContent = 'Showing ' + v + ' of <?php echo $total; ?> payments';
    });
}
</script>
</body>
</html>