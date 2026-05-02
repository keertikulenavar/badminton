<?php
session_start();
include "badmintondb.php";

if(!isset($_SESSION['email'])){
    header("Location:user_login.php");
    exit();
}

if(!isset($_SESSION['booking_date']) || !isset($_SESSION['booking_time'])){
    header("Location:court.php");
    exit();
}

$user_email = $_SESSION['email'];
$date       = $_SESSION['booking_date'];
$time       = $_SESSION['booking_time'];
$amount     = 200;

$display_date = date("d M Y", strtotime($date));

if(isset($_POST['pay'])){
    $txn_id = trim($_POST['txn_id']);

    if(empty($txn_id)){
        $error = "Please enter your 12-digit UTR number to proceed.";
    } elseif(!preg_match('/^[0-9]{12}$/', $txn_id)){
        $error = "UTR number must contain exactly 12 digits.";
    } else {
        /* Exclude cancelled bookings so freed slots can be re-booked */
        $check = "SELECT b.booking_id FROM bookings b
                  LEFT JOIN cancelled_bookings cb ON b.booking_id = cb.booking_id
                  WHERE b.booking_date='$date' AND b.booking_time='$time'
                  AND cb.booking_id IS NULL";
        $result = $conn->query($check);

        if($result && $result->num_rows > 0){
            echo "<script>alert('This slot was just booked by someone else. Please choose another.');window.location='court.php';</script>";
            exit();
        } else {
            $sql = "INSERT INTO bookings(user_email, booking_date, booking_time)
                    VALUES('$user_email','$date','$time')";
            if($conn->query($sql)){
                $booking_id = $conn->insert_id;
                $payment = "INSERT INTO payments(booking_id, user_email, amount, payment_status, txn_id)
                            VALUES('$booking_id','$user_email','$amount','Paid','$txn_id')";
                $conn->query($payment);
                unset($_SESSION['booking_date']);
                unset($_SESSION['booking_time']);
                echo "<script>alert('Payment recorded! Court booked successfully.');window.location='history.php';</script>";
                exit();
            } else {
                $error = "Database error: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Payment</title>
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

.box{
    background:rgba(255,255,255,0.98);
    width:100%;
    max-width:460px;
    border-radius:20px;
    overflow:hidden;
}

.card-header{
    background:linear-gradient(135deg,#1b5e20,#2e7d32);
    padding:24px 28px 20px;
    color:white;
    text-align:center;
}
.card-header h2{ font-size:20px; letter-spacing:0.5px; margin-bottom:3px; }
.card-header p{ font-size:13px; opacity:0.8; }

.booking-summary{
    display:flex;
    border-bottom:1px solid #e8f5e9;
}
.summary-cell{
    flex:1;
    padding:14px 10px;
    text-align:center;
    border-right:1px solid #e8f5e9;
}
.summary-cell:last-child{ border-right:none; }
.summary-cell .lbl{
    font-size:11px;
    color:#777;
    text-transform:uppercase;
    letter-spacing:0.8px;
    margin-bottom:4px;
}
.summary-cell .val{
    font-size:15px;
    font-weight:bold;
    color:#1b5e20;
}

.amount-banner{
    background:#f1f8e9;
    border-bottom:1px solid #c8e6c9;
    padding:16px 28px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.amount-banner .label{
    font-size:14px;
    color:#555;
    font-weight:600;
    text-transform:uppercase;
    letter-spacing:0.5px;
}
.amount-banner .amount{
    font-size:28px;
    font-weight:bold;
    color:#1b5e20;
}

.body{ padding:24px 28px; }

.steps{
    display:flex;
    align-items:center;
    margin-bottom:22px;
}
.step{
    flex:1;
    text-align:center;
    font-size:11px;
    color:#aaa;
}
.step .num{
    width:28px; height:28px;
    border-radius:50%;
    border:2px solid #ddd;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:bold;
    font-size:13px;
    color:#bbb;
    margin:0 auto 4px;
    background:white;
}
.step.done .num{ background:#2e7d32; border-color:#2e7d32; color:white; }
.step.active .num{ background:#1976d2; border-color:#1976d2; color:white; }
.step.active{ color:#1976d2; }
.step.done{ color:#2e7d32; }
.step-line{ flex:1; height:2px; background:#ddd; margin-top:-14px; }
.step-line.done{ background:#2e7d32; }

.qr-wrap{
    background:#f9fbe7;
    border:1.5px dashed #a5d6a7;
    border-radius:14px;
    padding:18px;
    text-align:center;
    margin-bottom:18px;
}
.qr-wrap img{
    width:160px;
    height:160px;
    object-fit:contain;
    border-radius:10px;
    border:1px solid #ddd;
    background:white;
    display:block;
    margin:0 auto 12px;
}
.upi-badge{
    display:inline-flex;
    align-items:center;
    gap:6px;
    background:#e8f5e9;
    border:1px solid #a5d6a7;
    border-radius:20px;
    padding:6px 16px;
    font-size:14px;
    font-weight:bold;
    color:#2e7d32;
    cursor:pointer;
    transition:0.2s;
    border:none;
    outline:none;
}
.upi-badge:hover{ background:#c8e6c9; }
.copy-hint{
    font-size:11px;
    color:#aaa;
    margin-top:5px;
}

.error-box{
    background:#ffebee;
    border:1px solid #ffcdd2;
    border-radius:10px;
    padding:12px 16px;
    color:#b71c1c;
    font-size:14px;
    margin-bottom:16px;
    display:flex;
    align-items:center;
    gap:8px;
}

.field-label{
    font-size:12px;
    font-weight:700;
    color:#1b5e20;
    text-transform:uppercase;
    letter-spacing:0.8px;
    margin-bottom:7px;
    display:flex;
    align-items:center;
    gap:5px;
}
input[type=text]{
    width:100%;
    padding:12px 14px;
    border:1.5px solid #c8e6c9;
    border-radius:10px;
    font-size:15px;
    color:#1a1a1a;
    outline:none;
    transition:border 0.2s;
}
input[type=text]:focus{ border-color:#2e7d32; }

.btn-pay{
    width:100%;
    padding:14px;
    background:linear-gradient(135deg,#2e7d32,#1b5e20);
    color:white;
    border:none;
    border-radius:10px;
    font-size:16px;
    font-weight:bold;
    cursor:pointer;
    letter-spacing:0.5px;
    margin-top:14px;
    transition:0.2s;
}
.btn-pay:hover{ background:linear-gradient(135deg,#1b5e20,#134a18); transform:translateY(-1px); }

.security-note{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:6px;
    font-size:12px;
    color:#888;
    margin-top:10px;
}

.back-btn{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:6px;
    width:100%;
    padding:11px;
    background:#f5f5f5;
    color:#555;
    border-radius:10px;
    text-decoration:none;
    font-size:14px;
    font-weight:bold;
    margin-top:10px;
    transition:0.2s;
}
.back-btn:hover{ background:#eeeeee; }

@media(max-width:480px){
    .body{ padding:20px 18px; }
    .summary-cell .val{ font-size:13px; }
}

</style>
</head>
<body>

<div class="box">

  <div class="card-header">
    <h2>🏸 Complete Your Booking</h2>
    <p>Scan &amp; pay to confirm your court reservation</p>
  </div>

  <div class="booking-summary">
    <div class="summary-cell">
      <div class="lbl">Date</div>
      <div class="val"><?php echo htmlspecialchars($display_date); ?></div>
    </div>
    <div class="summary-cell">
      <div class="lbl">Time</div>
      <div class="val"><?php echo htmlspecialchars($time); ?></div>
    </div>
    <div class="summary-cell">
      <div class="lbl">Court</div>
      <div class="val">Court 1</div>
    </div>
  </div>

  <div class="amount-banner">
    <span class="label">💰 Amount Due</span>
    <span class="amount">₹<?php echo $amount; ?></span>
  </div>

  <div class="body">

    <?php if(!empty($error)): ?>
    <div class="error-box">⚠️ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="qr-wrap">
      <img src="qrcode.jpeg" alt="Scan to Pay via UPI">
      <button class="upi-badge" onclick="copyUPI()">📋 8660201@ybl</button>
      <div class="copy-hint" id="copyHint">Tap to copy UPI ID</div>
    </div>

    <form method="POST" autocomplete="off">
      <div class="field-label">🔢 UTR Number</div>
      <input
        type="text"
        name="txn_id"
        id="txn_id"
        placeholder="Enter 12 digit UTR number"
        value="<?php echo isset($_POST['txn_id']) ? htmlspecialchars($_POST['txn_id']) : ''; ?>"
        inputmode="numeric"
        pattern="[0-9]{12}"
        minlength="12"
        maxlength="12"
        title="Enter exactly 12 digits"
        autocomplete="off"
        autocorrect="off"
        autocapitalize="off"
        spellcheck="false"
        readonly
        onfocus="this.removeAttribute('readonly')"
        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12)"
        required>

      <button type="submit" name="pay" class="btn-pay">
        ✅ I Have Paid — Confirm Booking
      </button>
    </form>

    <div class="security-note">🔒 Your payment details are secure</div>
    <div class="security-note">⚠️ Non-refundable once payment is confirmed</div>

    <a href="court.php" class="back-btn">← Back to Court Selection</a>

  </div>
</div>

<script>
function copyUPI(){
    navigator.clipboard.writeText('8660201@ybl').then(function(){
        const hint = document.getElementById('copyHint');
        hint.textContent = '✓ Copied to clipboard!';
        hint.style.color = '#2e7d32';
        setTimeout(() => {
            hint.textContent = 'Tap to copy UPI ID';
            hint.style.color = '#aaa';
        }, 2000);
    });
}
</script>

</body>
</html>
