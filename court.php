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

/* Auto-create cancelled_bookings table (shared with admin) */
$conn->query("CREATE TABLE IF NOT EXISTS cancelled_bookings (booking_id INT PRIMARY KEY)");

/* ── Helper: fetch all booked (non-cancelled) times for a given date ── */
function getBookedTimes($conn, $date) {
    $booked = [];
    $stmt = $conn->prepare(
        "SELECT b.booking_time FROM bookings b
         LEFT JOIN cancelled_bookings cb ON b.booking_id = cb.booking_id
         WHERE b.booking_date = ? AND cb.booking_id IS NULL"
    );
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()){
        $booked[] = $row['booking_time'];
    }
    $stmt->close();
    return $booked;
}

/* ── Handle "Check Available Slots" ── */
if(isset($_POST['check'])){
    $selected_date = $_POST['date'];
    $booked_times  = getBookedTimes($conn, $selected_date);
}

/* ── Handle "Book" ── */
if(isset($_POST['book'])){
    $booking_date = $_POST['date'];
    $booking_time = $_POST['time'];

    $currently_booked = getBookedTimes($conn, $booking_date);

    if(in_array($booking_time, $currently_booked)){
        $_SESSION['slot_error']    = "Sorry, that slot was just booked by someone else. Please choose another.";
        $_SESSION['restore_date']  = $booking_date;
        header("Location: court.php");
        exit();
    }

    $slot_hours = [
        "6 AM - 7 AM"=>6,"7 AM - 8 AM"=>7,"8 AM - 9 AM"=>8,
        "5 PM - 6 PM"=>17,"6 PM - 7 PM"=>18,"7 PM - 8 PM"=>19,"8 PM - 9 PM"=>20
    ];
    if($booking_date === date('Y-m-d') && isset($slot_hours[$booking_time]) && $slot_hours[$booking_time] <= (int)date('H')){
        $_SESSION['slot_error']   = "That time slot has already passed. Please choose an upcoming slot.";
        $_SESSION['restore_date'] = $booking_date;
        header("Location: court.php");
        exit();
    }

    $_SESSION['booking_date'] = $booking_date;
    $_SESSION['booking_time'] = $booking_time;
    header("Location: payment.php");
    exit();
}

if(isset($_SESSION['restore_date'])){
    $selected_date = $_SESSION['restore_date'];
    $booked_times  = getBookedTimes($conn, $selected_date);
    unset($_SESSION['restore_date']);
}

$times = [
    "6 AM - 7 AM","7 AM - 8 AM","8 AM - 9 AM",
    "5 PM - 6 PM","6 PM - 7 PM","7 PM - 8 PM","8 PM - 9 PM"
];

$slot_start_hour = [
    "6 AM - 7 AM"  => 6,
    "7 AM - 8 AM"  => 7,
    "8 AM - 9 AM"  => 8,
    "5 PM - 6 PM"  => 17,
    "6 PM - 7 PM"  => 18,
    "7 PM - 8 PM"  => 19,
    "8 PM - 9 PM"  => 20,
];

$today        = date('Y-m-d');
$current_hour = (int)date('H');

$total = count($times);
$booked_count = count($booked_times);

$past_count = 0;
if($selected_date === $today){
    foreach($times as $t){
        if($slot_start_hour[$t] <= $current_hour){
            $past_count++;
        }
    }
}

$available_count = $total - $booked_count - $past_count;
?>
<!DOCTYPE html>
<html>
<head>
<title>Book Court</title>
<style>
/* (ALL YOUR ORIGINAL CSS — UNCHANGED) */
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
.box{
background:rgba(255,255,255,0.97);
width:100%;
max-width:650px;
border-radius:20px;
overflow:hidden;
}
.card-header{
background:linear-gradient(135deg,#1b5e20,#2e7d32);
padding:28px 32px 20px;
color:white;
text-align:center;
}
.card-header h2{ font-size:24px; }
.card-header p{ font-size:14px; opacity:0.85; }
.court-img-wrap{
width:100%;
height:280px;
overflow:hidden;
position:relative;
}
.court-img-wrap img{
width:100%;
height:100%;
object-fit:cover;
display:block;
}
.form-body{ padding:30px 34px; }

.slot-error{
background:#ffebee;
border:1px solid #ffcdd2;
color:#b71c1c;
border-radius:10px;
padding:13px 16px;
margin-bottom:18px;
font-size:14px;
font-weight:500;
}

/* 🔥 Added style for notice (only addition) */
.note{
color:red;
font-weight:bold;
margin-top:10px;
text-align:center;
}

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

input[type=date]{
width:100%;
padding:13px 14px;
border:1.5px solid #c8e6c9;
border-radius:10px;
font-size:15px;
}

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
pointer-events:none;
}
.time-slot.past{
background:#f5f5f5;
border-color:#e0e0e0;
color:#9e9e9e;
cursor:not-allowed;
pointer-events:none;
}
.booked-label{
display:block;
font-size:10px;
margin-top:3px;
color:#e57373;
text-transform:uppercase;
letter-spacing:0.5px;
font-weight:bold;
}
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
</style>
</head>
<body>

<div class="box">
  <div class="card-header">
    <h2>🏸 Badminton Court Booking</h2>
    <p>Select your preferred date and time slot</p>
  </div>

  <div class="court-img-wrap">
    <img src="badmintoncourt.jpg">
  </div>

  <div class="form-body">

    <?php if(isset($_SESSION['slot_error'])): ?>
    <div class="slot-error">
      ⚠️ <?php echo htmlspecialchars($_SESSION['slot_error']); unset($_SESSION['slot_error']); ?>
    </div>
    <?php endif; ?>

    <div class="info-bar">
      <div class="info-pill"><div class="val">₹200</div><div class="lbl">Per Hour</div></div>
      <div class="info-pill"><div class="val"><?php echo $total; ?></div><div class="lbl">Total Slots per day</div></div>
      <div class="info-pill"><div class="val"><?php echo $available_count; ?></div><div class="lbl">Available Slots</div></div>
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
        $is_past   = ($selected_date === $today && $slot_start_hour[$t] <= $current_hour);
        $disabled  = $is_booked || $is_past;
        $css_class = $is_past ? 'past' : ($is_booked ? 'booked' : '');
        ?>
        <div class="time-slot <?php echo $css_class; ?>"
        <?php if(!$disabled): ?>onclick="selectSlot(this,'<?php echo $t;?>')"<?php endif; ?>>
        <?php echo $t; ?>
        <?php if($is_booked): ?><span class="booked-label">Booked</span><?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>

      <input type="hidden" name="time" id="hidden-time">

      <!-- ✅ ONLY ADDED LINE -->
      <p class="note">⚠️ Note: This court booking is non-refundable.</p>

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