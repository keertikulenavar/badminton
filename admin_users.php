<?php
session_start();
include "badmintondb.php";

if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

/* Delete user */
if(isset($_GET['delete'])){
    $del_email = $conn->real_escape_string($_GET['delete']);
    $conn->query("DELETE FROM payments WHERE user_email='$del_email'");
    $conn->query("DELETE FROM bookings  WHERE user_email='$del_email'");
    $conn->query("DELETE FROM users     WHERE email='$del_email'");
    echo "<script>alert('User and all related data deleted.');window.location='admin_users.php';</script>";
    exit();
}

/* Fetch all users with booking count — ASC so newest appears at bottom */
$sql = "SELECT u.user_id, u.name, u.email, u.contact,
               COUNT(b.booking_id) AS booking_count
        FROM users u
        LEFT JOIN bookings b ON u.email = b.user_email
        GROUP BY u.user_id
        ORDER BY u.user_id ASC";
$result = $conn->query($sql);
$users  = [];
$total  = 0;
if($result) while($row = $result->fetch_assoc()){ $users[] = $row; $total++; }
?>
<!DOCTYPE html>
<html>
<head>
<title>Manage Users – Admin</title>
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
    max-width: 900px; margin: auto;
    background: rgba(255,255,255,0.98);
    border-radius: 20px; overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.25);
}

.page-header {
    background: linear-gradient(135deg,#1565c0,#0d47a1);
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

.summary-bar { display: flex; border-bottom: 1px solid #e3f2fd; }
.sum-cell {
    flex: 1; padding: 15px 10px; text-align: center;
    border-right: 1px solid #e3f2fd;
}
.sum-cell:last-child { border-right: none; }
.sum-cell .lbl { font-size: 11px; color: #777; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 4px; }
.sum-cell .val { font-size: 22px; font-weight: bold; color: #1565c0; }

.table-wrap { padding: 22px 26px 28px; }

.search-row { margin-bottom: 16px; }
.search-row input {
    width: 100%; padding: 10px 14px;
    border: 1.5px solid #bbdefb; border-radius: 10px;
    font-size: 14px; color: #1a1a1a; outline: none; transition: border 0.2s;
}
.search-row input:focus { border-color: #1565c0; }

table { width: 100%; border-collapse: collapse; font-size: 14px; }
thead tr { background: #e3f2fd; }
th {
    padding: 12px 14px; text-align: left;
    font-size: 12px; font-weight: 700; color: #1565c0;
    text-transform: uppercase; letter-spacing: 0.7px;
    border-bottom: 2px solid #bbdefb;
}
tbody tr { border-bottom: 1px solid #f0f0f0; transition: background 0.15s; animation: fadeIn 0.3s ease both; }
tbody tr:hover { background: #e3f2fd22; }
td { padding: 13px 14px; color: #1a1a1a; vertical-align: middle; }

@keyframes fadeIn { from{opacity:0;transform:translateY(5px)} to{opacity:1;transform:translateY(0)} }

.name-main { font-weight: bold; color: #0d47a1; }
.name-sub  { font-size: 11px; color: #888; margin-top: 2px; }

.booking-badge {
    display: inline-block; background: #e8f5e9;
    color: #27500a; border-radius: 6px;
    padding: 3px 10px; font-size: 12px; font-weight: bold;
}

.delete-btn {
    display: inline-block; background: #ffebee;
    color: #b71c1c; border: 1px solid #ffcdd2;
    padding: 6px 12px; border-radius: 7px;
    font-size: 12px; font-weight: bold;
    text-decoration: none; transition: 0.2s; cursor: pointer;
}
.delete-btn:hover { background: #ffcdd2; }

tfoot td {
    padding: 12px 14px; font-size: 13px;
    color: #888; border-top: 2px solid #e3f2fd;
}

.empty-state { text-align: center; padding: 48px 20px; color: #999; }
.empty-state .icon { font-size: 44px; margin-bottom: 14px; }

.back-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 20px; background: #f5f5f5; color: #555;
    border-radius: 10px; text-decoration: none; font-size: 14px;
    font-weight: bold; margin-top: 18px; transition: 0.2s;
}
.back-btn:hover { background: #eeeeee; }

@media(max-width:600px){
    .table-wrap { padding: 14px; }
    th, td { padding: 9px 8px; font-size: 12px; }
}
</style>
</head>
<body>

<div class="container">

    <div class="page-header">
        <div class="page-header-left">
            <h2><i class="fas fa-users"></i> Manage Users</h2>
            <p>All registered users on the platform</p>
        </div>
        <a href="admin_dashboard.php" class="back-header-btn">← Dashboard</a>
    </div>

    <div class="summary-bar">
        <div class="sum-cell">
            <div class="lbl">Total Users</div>
            <div class="val"><?php echo $total; ?></div>
        </div>
        <div class="sum-cell">
            <div class="lbl">Active</div>
            <div class="val" style="color:#2e7d32;"><?php echo $total; ?></div>
        </div>
    </div>

    <div class="table-wrap">

        <?php if($total > 0): ?>
        <div class="search-row">
            <input type="text" id="searchInput" placeholder="🔍  Search by name, email or contact...">
        </div>

        <table id="userTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Bookings</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $i => $u): ?>
                <tr style="animation-delay:<?php echo $i * 0.04; ?>s">
                    <td><?php echo $i + 1; ?></td>
                    <td>
                        <div class="name-main"><?php echo htmlspecialchars($u['name']); ?></div>
                    </td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td><?php echo htmlspecialchars($u['contact']); ?></td>
                    <td>
                        <span class="booking-badge"><?php echo $u['booking_count']; ?></span>
                    </td>
                    <td>
                        <a class="delete-btn"
                           href="admin_users.php?delete=<?php echo urlencode($u['email']); ?>"
                           onclick="return confirm('Delete user <?php echo htmlspecialchars($u['name']); ?> and ALL their data?')">
                           <i class="fas fa-trash-alt"></i> Delete
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" id="rowCount">Showing <?php echo $total; ?> of <?php echo $total; ?> users</td>
                </tr>
            </tfoot>
        </table>

        <?php else: ?>
        <div class="empty-state">
            <div class="icon">👥</div>
            <p>No users registered yet.</p>
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
        const rows = document.querySelectorAll('#userTable tbody tr');
        let v = 0;
        rows.forEach(row => {
            const show = row.textContent.toLowerCase().includes(q);
            row.style.display = show ? '' : 'none';
            if(show) v++;
        });
        document.getElementById('rowCount').textContent = 'Showing ' + v + ' of <?php echo $total; ?> users';
    });
}
</script>
</body>
</html>