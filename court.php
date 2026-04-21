<?php
session_start();
include "badmintondb.php";

if(!isset($_SESSION['email'])){
    header("Location: user_login.php");
    exit();
}

$user_email = $_SESSION['email'];
$selected_date = "";
$booked_times = [];

if(isset($_POST['check'])){
    $selected_date = $_POST['date'];
    $sql = "SELECT booking_time FROM bookings WHERE booking_date='$selected_date'";
    $result = $conn->query($sql);
    if($result){
        while($row = $result->fetch_assoc()){
            $booked_times[] = $row['booking_time'];
        }
    }
}

if(isset($_POST['book'])){
    $_SESSION['booking_date'] = $_POST['date'];
    $_SESSION['booking_time'] = $_POST['time'];
    header("Location: payment.php");
    exit();
}

$times = [
    "6 AM - 7 AM","7 AM - 8 AM","8 AM - 9 AM",
    "5 PM - 6 PM","6 PM - 7 PM","7 PM - 8 PM","8 PM - 9 PM"
];
$total = count($times);
$booked_count = count($booked_times);
$available_count = $total - $booked_count;
?>
<!DOCTYPE html>
<html>
<head>
<title>Book Court</title>
<style>

*{ box-sizing:border-box; margin:0; padding:0; }

body{
font-family:Arial,sans-serif;
background-image:url("cmb.jpeg");
background-size:cover;
background-position:center;
min-height:100vh;
display:flex;
justify-content:center;
align-items:flex-start;
padding:30px 16px;
}

/* ── Card (BIGGER) ── */
.box{
background:rgba(255,255,255,0.97);
width:100%;
max-width:650px; /* increased */
border-radius:20px;
overflow:hidden;
}

/* ── Header ── */
.card-header{
background:linear-gradient(135deg,#1b5e20,#2e7d32);
padding:28px 32px 20px;
color:white;
text-align:center;
}
.card-header h2{ font-size:24px; }
.card-header p{ font-size:14px; opacity:0.85; }

/* ── Court image (BIGGER) ── */
.court-img-wrap{
width:100%;
height:280px; /* increased */
overflow:hidden;
position:relative;
}
.court-img-wrap img{
width:100%;
height:100%;
object-fit:cover;
display:block;
}


/* ── Body ── */
.form-body{ padding:30px 34px; }

/* ── Info pills ── */
.info-bar{
display:flex;
gap:14px;
margin-bottom:22px;
}
.info-pill{
flex:1;
background:#f1f8e9;
border:1px solid #c8e6c9;
border-radius:12px;
padding:12px 10px;
text-align:center;
}
.info-pill .val{
font-size:22px;
font-weight:bold;
color:#2e7d32;
}
.info-pill .lbl{
font-size:11px;
color:#555;
margin-top:2px;
text-transform:uppercase;
letter-spacing:0.8px;
}

/* ── Inputs ── */
input[type=date]{
width:100%;
padding:13px 14px;
border:1.5px solid #c8e6c9;
border-radius:10px;
font-size:15px;
}

/* ── Time grid ── */
.time-grid{
display:grid;
grid-template-columns:1fr 1fr;
gap:12px;
}
.time-slot{
padding:14px 10px;
border:1.5px solid #c8e6c9;
border-radius:10px;
text-align:center;
cursor:pointer;
}
.time-slot.selected{
background:#e8f5e9;
border-color:#2e7d32;
font-weight:bold;
}
.time-slot.booked{
background:#ffebee;
border-color:#ffcdd2;
color:#b71c1c;
cursor:not-allowed;
}

/* ── Buttons ── */
.btn-check,.btn-book{
width:100%;
padding:14px;
border:none;
border-radius:10px;
font-weight:bold;
cursor:pointer;
margin-top:10px;
}
.btn-check{
background:#1565c0;
color:white;
}
.btn-book{
background:#2e7d32;
color:white;
}
.btn-book:disabled{
background:#aaa;
cursor:not-allowed;
}

/* ── Back ── */
.back-btn{
display:block;
text-align:center;
margin-top:14px;
padding:12px;
background:#f5f5f5;
border-radius:10px;
text-decoration:none;
color:#444;
}

/* Responsive */
@media(max-width:480px){
.time-grid{ grid-template-columns:1fr; }
}

</style>
</head>
<body>

<div class="box">

  <div class="card-header">
    <h2>🏸 Badminton Court Booking</h2>
    <p>Select your preferred date and time slot</p>
  </div>

  <!-- Bigger Image -->
  <div class="court-img-wrap">
    <img src="badmintoncourt.jpg" >
  </div>

  <div class="form-body">

    <div class="info-bar">
      <div class="info-pill">
        <div class="val">₹200</div>
        <div class="lbl">Per Hour</div>
      </div>
      <div class="info-pill">
        <div class="val"><?php echo $total; ?></div>
        <div class="lbl">Total Slots per day</div>
      </div>
      <div class="info-pill">
        <div class="val"><?php echo $available_count; ?></div>
        <div class="lbl">Available Slots</div>
      </div>
    </div>

    <form method="POST">

      <input type="date" name="date" required
             value="<?php echo htmlspecialchars($selected_date); ?>"
             min="<?php echo date('Y-m-d'); ?>">

      <button type="submit" name="check" class="btn-check">Check Available Slots</button>

      <?php if($selected_date != ""): ?>

      <div class="time-grid">
        <?php foreach($times as $t):
        $is_booked = in_array($t, $booked_times);
        ?>
        <div class="time-slot <?php echo $is_booked?'booked':'';?>"
        <?php if(!$is_booked): ?>onclick="selectSlot(this,'<?php echo $t;?>')"<?php endif; ?>>
        <?php echo $t;?>
        </div>
        <?php endforeach; ?>
      </div>

      <input type="hidden" name="time" id="hidden-time">

      <button type="submit" name="book" class="btn-book" id="bookBtn" disabled>
        Select Time First
      </button>

      <?php endif; ?>

    </form>

    <a href="user_dashboard.php" class="back-btn">← Back</a>

  </div>
</div>

<script>
function selectSlot(el,time){
document.querySelectorAll('.time-slot').forEach(s=>s.classList.remove('selected'));
el.classList.add('selected');
document.getElementById('hidden-time').value=time;
document.getElementById('bookBtn').disabled=false;
document.getElementById('bookBtn').textContent="Confirm Booking → "+time;
}
</script>

</body>
</html>