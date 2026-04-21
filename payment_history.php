<?php
session_start();
include "badmintondb.php";

if(!isset($_SESSION['email'])){
    header("Location:user_login.php");
    exit();
}

$email = $_SESSION['email'];

/* Fetch payment history — query payments directly by user_email so that
   payment records are never lost even when a booking is cancelled/deleted */
$sql = "SELECT payment_id, booking_id, amount, payment_status
        FROM payments
        WHERE user_email='$email' AND payment_status='Paid'";

$result = $conn->query($sql);

/* Compute summary stats */
$total_payments = 0;
$total_spent    = 0;
$rows           = [];

if($result && $result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        $rows[] = $row;
        $total_payments++;
        $total_spent += $row['amount'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Payment History</title>
<style>

*{ box-sizing:border-box; margin:0; padding:0; }

body{
    font-family:Arial,sans-serif;
    background-image:url("cmb.jpeg");
    background-size:cover;
    background-position:center;
    min-height:100vh;
    padding:36px 16px;
}

/* ── Card ── */
.container{
    max-width:780px;
    margin:auto;
    background:rgba(255,255,255,0.98);
    border-radius:20px;
    overflow:hidden;
}

/* ── Header ── */
.page-header{
    background:linear-gradient(135deg,#1b5e20,#2e7d32);
    padding:26px 32px 22px;
    color:white;
}
.page-header h2{ font-size:20px; letter-spacing:0.5px; margin-bottom:3px; }
.page-header p{ font-size:13px; opacity:0.8; }

/* ── Summary bar ── */
.summary-bar{
    display:flex;
    border-bottom:1px solid #e8f5e9;
}
.sum-cell{
    flex:1;
    padding:16px 12px;
    text-align:center;
    border-right:1px solid #e8f5e9;
}
.sum-cell:last-child{ border-right:none; }
.sum-cell .lbl{
    font-size:11px;
    color:#777;
    text-transform:uppercase;
    letter-spacing:0.8px;
    margin-bottom:5px;
}
.sum-cell .val{
    font-size:22px;
    font-weight:bold;
    color:#1b5e20;
}

/* ── Table wrapper ── */
.table-wrap{ padding:24px 28px 28px; }

/* ── Search ── */
.search-row{ display:flex; align-items:center; gap:10px; margin-bottom:18px; }
.search-row input{
    flex:1;
    padding:10px 14px;
    border:1.5px solid #c8e6c9;
    border-radius:10px;
    font-size:14px;
    color:#1a1a1a;
    outline:none;
    transition:border 0.2s;
}
.search-row input:focus{ border-color:#2e7d32; }

/* ── Table ── */
table{ width:100%; border-collapse:collapse; font-size:14px; }
thead tr{ background:#f1f8e9; }
th{
    padding:12px 14px;
    text-align:left;
    font-size:12px;
    font-weight:700;
    color:#2e7d32;
    text-transform:uppercase;
    letter-spacing:0.7px;
    border-bottom:2px solid #c8e6c9;
}
tbody tr{
    border-bottom:1px solid #f0f0f0;
    transition:background 0.15s;
    animation:fadeIn 0.3s ease both;
}
tbody tr:hover{ background:#f9fbe7; }
td{ padding:13px 14px; color:#1a1a1a; vertical-align:middle; }

@keyframes fadeIn{
    from{ opacity:0; transform:translateY(6px); }
    to{ opacity:1; transform:translateY(0); }
}

/* ── Badges ── */
.id-badge{
    display:inline-block;
    background:#e8f5e9;
    color:#27500a;
    border-radius:6px;
    padding:3px 10px;
    font-size:12px;
    font-weight:bold;
}
.booking-badge{
    display:inline-block;
    background:#e3f2fd;
    color:#0c447c;
    border-radius:6px;
    padding:3px 10px;
    font-size:12px;
    font-weight:bold;
}
.amount-cell{ font-size:15px; font-weight:bold; color:#1b5e20; }

.status-pill{
    display:inline-flex;
    align-items:center;
    gap:5px;
    background:#e8f5e9;
    color:#27500a;
    border-radius:20px;
    padding:4px 12px;
    font-size:12px;
    font-weight:bold;
}
.dot{
    width:7px; height:7px;
    border-radius:50%;
    background:#2e7d32;
    display:inline-block;
}

/* ── Footer row ── */
tfoot td{
    padding:14px;
    font-size:13px;
    color:#888;
    border-top:2px solid #e8f5e9;
}

/* ── Empty state ── */
.empty-state{
    text-align:center;
    padding:52px 20px;
    color:#999;
}
.empty-state .icon{ font-size:44px; margin-bottom:14px; }
.empty-state p{ font-size:15px; margin-bottom:16px; }
.empty-state a{
    display:inline-block;
    padding:10px 24px;
    background:#2e7d32;
    color:white;
    border-radius:10px;
    text-decoration:none;
    font-size:14px;
    font-weight:bold;
}

/* ── Back button ── */
.back-btn{
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:10px 20px;
    background:#f5f5f5;
    color:#555;
    border-radius:10px;
    text-decoration:none;
    font-size:14px;
    font-weight:bold;
    transition:0.2s;
    margin-top:18px;
}
.back-btn:hover{ background:#eeeeee; }

/* ── Responsive ── */
@media(max-width:600px){
    .table-wrap{ padding:16px; }
    .summary-bar{ flex-wrap:wrap; }
    .sum-cell{ min-width:50%; }
    th, td{ padding:10px 8px; font-size:13px; }
}

</style>
</head>
<body>

<div class="container">

    <!-- Header -->
    <div class="page-header">
        <h2>💳 My Payment History</h2>
        <p>All confirmed court booking payments</p>
    </div>

    <!-- Summary bar -->
    <div class="summary-bar">
        <div class="sum-cell">
            <div class="lbl">Total Payments</div>
            <div class="val"><?php echo $total_payments; ?></div>
        </div>
        <div class="sum-cell">
            <div class="lbl">Amount Spent</div>
            <div class="val">₹<?php echo number_format($total_spent); ?></div>
        </div>
        <div class="sum-cell">
            <div class="lbl">Status</div>
            <div class="val" style="font-size:15px;">
                <?php echo $total_payments > 0 ? '✅ All Paid' : '—'; ?>
            </div>
        </div>
    </div>

    <div class="table-wrap">

        <?php if($total_payments > 0): ?>

        <!-- Search bar -->
        <div class="search-row">
            <input type="text" id="searchInput"
                   placeholder="🔍  Search by Payment ID or Booking ID...">
        </div>

        <!-- Table -->
        <table id="payTable">
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Booking ID</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($rows as $i => $row): ?>
                <tr style="animation-delay:<?php echo $i * 0.05; ?>s">
                    <td><span class="id-badge">#<?php echo $row['payment_id']; ?></span></td>
                    <td><span class="booking-badge">#<?php echo $row['booking_id']; ?></span></td>
                    <td class="amount-cell">₹<?php echo number_format($row['amount']); ?></td>
                    <td>
                        <span class="status-pill">
                            <span class="dot"></span>
                            <?php echo htmlspecialchars($row['payment_status']); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" id="rowCount">
                        Showing <?php echo $total_payments; ?> of <?php echo $total_payments; ?> payments
                    </td>
                </tr>
            </tfoot>
        </table>

        <?php else: ?>

        <!-- Empty state -->
        <div class="empty-state">
            <div class="icon">🏸</div>
            <p>No payment history found yet.<br>Book a court and your payments will appear here.</p>
            <a href="court.php">Book a Court Now</a>
        </div>

        <?php endif; ?>

        <a href="user_dashboard.php" class="back-btn">← Back to Dashboard</a>

    </div>
</div>

<script>
const searchInput = document.getElementById('searchInput');
if(searchInput){
    searchInput.addEventListener('input', function(){
        const q = this.value.toLowerCase();
        const rows = document.querySelectorAll('#payTable tbody tr');
        let visible = 0;
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const show = text.includes(q);
            row.style.display = show ? '' : 'none';
            if(show) visible++;
        });
        document.getElementById('rowCount').textContent =
            'Showing ' + visible + ' of <?php echo $total_payments; ?> payments';
    });
}
</script>

</body>
</html>