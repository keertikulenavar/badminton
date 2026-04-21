<?php
session_start();
include "badmintondb.php";

if(!isset($_SESSION['email'])){
    header("Location:user_login.php");
    exit();
}

$user_email = $_SESSION['email'];

/* Cancel booking — deletes ONLY from bookings table.
   Payments table is never touched, so payment_history stays intact. */
if(isset($_GET['date']) && isset($_GET['time'])){
    $date = $_GET['date'];
    $time = $_GET['time'];
    $delete = "DELETE FROM bookings
               WHERE user_email='$user_email'
               AND booking_date='$date'
               AND booking_time='$time'";
    $conn->query($delete);
    echo "<script>alert('Booking cancelled successfully.');window.location='history.php';</script>";
    exit();
}

/* Fetch all bookings */
$sql = "SELECT * FROM bookings WHERE user_email='$user_email' ORDER BY booking_date DESC";
$result = $conn->query($sql);

/* Build rows array + compute stats */
$rows      = [];
$upcoming  = 0;
$completed = 0;
$today     = date('Y-m-d');

if($result && $result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        $rows[] = $row;
        if($row['booking_date'] >= $today) $upcoming++;
        else $completed++;
    }
}
$total = count($rows);
?>
<!DOCTYPE html>
<html>
<head>
<title>My Booking History</title>
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
    max-width:740px;
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
    padding:15px 10px;
    text-align:center;
    border-right:1px solid #e8f5e9;
}
.sum-cell:last-child{ border-right:none; }
.sum-cell .lbl{
    font-size:11px;
    color:#777;
    text-transform:uppercase;
    letter-spacing:0.8px;
    margin-bottom:4px;
}
.sum-cell .val{ font-size:21px; font-weight:bold; color:#1b5e20; }
.sum-cell .val.blue{ color:#1976d2; }
.sum-cell .val.gray{ color:#888; }

/* ── Table wrap ── */
.table-wrap{ padding:22px 28px 28px; }

/* ── Search ── */
.search-row{ margin-bottom:16px; }
.search-row input{
    width:100%;
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
    from{ opacity:0; transform:translateY(5px); }
    to{ opacity:1; transform:translateY(0); }
}

/* ── Date cell ── */
.date-main{ font-weight:bold; color:#1b5e20; }
.date-sub{ font-size:11px; color:#888; margin-top:2px; }

/* ── Time badge ── */
.time-badge{
    display:inline-block;
    background:#e3f2fd;
    color:#0c447c;
    border-radius:6px;
    padding:4px 11px;
    font-size:13px;
    font-weight:bold;
}

/* ── Status pills ── */
.status-upcoming{
    display:inline-flex;
    align-items:center;
    gap:5px;
    background:#e8f5e9;
    color:#27500a;
    border-radius:20px;
    padding:4px 10px;
    font-size:12px;
    font-weight:bold;
}
.status-past{
    display:inline-flex;
    align-items:center;
    gap:5px;
    background:#f5f5f5;
    color:#555;
    border-radius:20px;
    padding:4px 10px;
    font-size:12px;
}
.dot-green{ width:7px; height:7px; border-radius:50%; background:#2e7d32; display:inline-block; }
.dot-gray{  width:7px; height:7px; border-radius:50%; background:#aaa;    display:inline-block; }

/* ── Cancel button ── */
.cancel-btn{
    display:inline-block;
    background:#ffebee;
    color:#b71c1c;
    border:1px solid #ffcdd2;
    padding:6px 13px;
    border-radius:7px;
    font-size:12px;
    font-weight:bold;
    text-decoration:none;
    transition:0.2s;
    cursor:pointer;
}
.cancel-btn:hover{ background:#ffcdd2; }
.past-label{ color:#bbb; font-size:13px; }

/* ── Footer row ── */
tfoot td{
    padding:12px 14px;
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
.empty-state p{ font-size:15px; margin-bottom:18px; }
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
    margin-top:18px;
    transition:0.2s;
}
.back-btn:hover{ background:#eeeeee; }

/* ── Responsive ── */
@media(max-width:580px){
    .table-wrap{ padding:16px; }
    th, td{ padding:10px 8px; font-size:13px; }
    .summary-bar{ flex-wrap:wrap; }
    .sum-cell{ min-width:50%; }
}

</style>
</head>
<body>

<div class="container">

    <!-- Header -->
    <div class="page-header">
        <h2>📋 My Booking History</h2>
        <p>All your badminton court reservations</p>
    </div>

    <!-- Summary bar -->
    <div class="summary-bar">
        <div class="sum-cell">
            <div class="lbl">Total Bookings</div>
            <div class="val"><?php echo $total; ?></div>
        </div>
        <div class="sum-cell">
            <div class="lbl">Upcoming</div>
            <div class="val blue"><?php echo $upcoming; ?></div>
        </div>
        <div class="sum-cell">
            <div class="lbl">Completed</div>
            <div class="val gray"><?php echo $completed; ?></div>
        </div>
    </div>

    <div class="table-wrap">

        <?php if($total > 0): ?>

        <!-- Search -->
        <div class="search-row">
            <input type="text" id="searchInput"
                   placeholder="🔍  Search by date or time...">
        </div>

        <!-- Table -->
        <table id="bookTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time Slot</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($rows as $i => $row):
                    $bdate      = $row['booking_date'];
                    $btime      = $row['booking_time'];
                    $is_upcoming = ($bdate >= $today);
                    $display_date = date('M d, Y', strtotime($bdate));
                    $day_name     = date('l', strtotime($bdate));
                    $enc_date     = urlencode($bdate);
                    $enc_time     = urlencode($btime);
                ?>
                <tr style="animation-delay:<?php echo $i * 0.05; ?>s">
                    <td>
                        <div class="date-main"><?php echo $display_date; ?></div>
                        <div class="date-sub"><?php echo $day_name; ?></div>
                    </td>
                    <td>
                        <span class="time-badge"><?php echo htmlspecialchars($btime); ?></span>
                    </td>
                    <td>
                        <?php if($is_upcoming): ?>
                            <span class="status-upcoming">
                                <span class="dot-green"></span>Upcoming
                            </span>
                        <?php else: ?>
                            <span class="status-past">
                                <span class="dot-gray"></span>Completed
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($is_upcoming): ?>
                            <a class="cancel-btn"
                               href="history.php?date=<?php echo $enc_date; ?>&time=<?php echo $enc_time; ?>"
                               onclick="return confirm('Cancel this booking on <?php echo $display_date; ?> at <?php echo htmlspecialchars($btime); ?>?')">
                               ✕ Cancel
                            </a>
                        <?php else: ?>
                            <span class="past-label">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" id="rowCount">
                        Showing <?php echo $total; ?> of <?php echo $total; ?> bookings
                    </td>
                </tr>
            </tfoot>
        </table>

        <?php else: ?>

        <!-- Empty state -->
        <div class="empty-state">
            <div class="icon">🏸</div>
            <p>No bookings yet!<br>Head over to court booking and reserve your first slot.</p>
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
        const rows = document.querySelectorAll('#bookTable tbody tr');
        let visible = 0;
        rows.forEach(row => {
            const show = row.textContent.toLowerCase().includes(q);
            row.style.display = show ? '' : 'none';
            if(show) visible++;
        });
        document.getElementById('rowCount').textContent =
            'Showing ' + visible + ' of <?php echo $total; ?> bookings';
    });
}
</script>

</body>
</html>