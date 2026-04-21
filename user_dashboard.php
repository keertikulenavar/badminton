<?php
session_start();
if(!isset($_SESSION['email'])){
    header("Location: index.php");
    exit();
}
$username = isset($_SESSION['name']) ? $_SESSION['name'] : "User";
?>
<!DOCTYPE html>
<html>
<head>
<title>User Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>

body::before{
content:"";
position:fixed;
top:0;left:0;
width:100%;height:100%;
background-image:url("indorecourt1.jpg");
background-size:cover;
background-position:center;
filter:blur(8px);
z-index:-1;
}

body{
margin:0;
font-family:"Times New Roman",serif;
color:white;
}

/* ── Header ── */
.header{
background:rgba(0,0,0,0.75);
padding:14px 24px;
display:flex;
justify-content:space-between;
align-items:center;
border-bottom:1px solid rgba(255,255,255,0.1);
}
.header h2{ margin:0; font-size:20px; letter-spacing:1px; }
.header-right{ display:flex; align-items:center; gap:14px; font-size:15px; }

.logout{
background:#c0392b;
padding:8px 18px;
color:white;
text-decoration:none;
border-radius:6px;
font-size:14px;
transition:0.3s;
}
.logout:hover{ background:#e74c3c; }

/* ── Hero ── */
.hero{
text-align:center;
padding:36px 20px 10px;
}
.clock{
font-size:14px;
color:#111;
font-weight:bold;
letter-spacing:1px;
margin-bottom:6px;
text-shadow:0 1px 3px rgba(255,255,255,0.3);
}
.hero h1{
font-size:30px;
font-weight:bold;
margin-bottom:6px;
color:#000;
text-shadow:0 1px 4px rgba(255,255,255,0.4);
}

.greeting-badge{
display:inline-block;
background:rgba(107, 104, 104, 0.45);
border:1px solid rgba(10, 10, 10, 0.25);
border-radius:20px;
padding:5px 18px;
font-size:14px;
color:#000;
font-weight:bold;
margin-bottom:28px;
text-shadow:0 0 6px rgba(255,255,255,0.6);
}

.divider{
width:40px; height:2px;
background:rgba(0,0,0,0.4);
margin:0 auto 28px;
border-radius:2px;
}

/* ── Stats ── */
.stats{
display:flex;
justify-content:center;
gap:20px;
flex-wrap:wrap;
margin-bottom:36px;
}
.stat{
background:rgba(133, 122, 122, 0.08);
border:1px solid rgba(4, 3, 3, 0.2);
border-radius:12px;
padding:20px 48px;
text-align:center;
min-width:160px;
}
.stat-num{
font-size:32px;
font-weight:bold;
color:#000;
text-shadow:0 0 8px rgba(255,255,255,0.5);
animation:countUp 0.6s ease both;
}
.stat-lbl{
font-size:12px;
color:#111;
font-weight:bold;
letter-spacing:2px;
margin-top:4px;
text-shadow:0 0 6px rgba(255,255,255,0.4);
}
@keyframes countUp{
from{ opacity:0; transform:translateY(10px); }
to{ opacity:1; transform:translateY(0); }
}

/* ── Cards ── */
.cards{
display:flex;
justify-content:center;
gap:28px;
flex-wrap:wrap;
padding:0 20px 48px;
}
.card{
width:400px;
border-radius:18px;
overflow:hidden;
position:relative;
transition:transform 0.3s, box-shadow 0.3s;
cursor:pointer;
}
.card:hover{
background:rgba(15, 150, 0, 0.1);
transform:translateY(-10px) scale(1.04);
box-shadow:0 20px 40px rgba(0,0,0,0.5);
}

.card-shine{
position:absolute;
top:0; left:-60%;
width:40%; height:100%;
background:rgba(255,255,255,0.07);
transform:skewX(-20deg);
transition:left 0.5s;
pointer-events:none;
}
.card:hover .card-shine{ left:120%; }

.card-inner{ padding:32px 28px 28px; }

.card.book    { background:linear-gradient(145deg,#27ae60,#1e8449); }
.card.history { background:linear-gradient(145deg,#2980b9,#1a5276); }
.card.payment { background:linear-gradient(145deg,#16a085,#0e6655); }

.card i{ font-size:42px; margin-bottom:16px; display:block; }
.card h3{ font-size:20px; margin-bottom:10px; font-weight:normal; letter-spacing:0.5px; }
.card p{ font-size:14px; opacity:0.85; margin-bottom:24px; line-height:1.5; }

.card-btn{
display:inline-block;
background:rgba(255,255,255,0.95);
color:#1a1a1a;
padding:10px 22px;
border-radius:7px;
font-size:14px;
font-weight:bold;
text-decoration:none;
transition:0.2s;
}
.card-btn:hover{ background:white; transform:scale(1.05); }

/* ── Responsive ── */
@media(max-width:700px){
.cards{ flex-direction:column; align-items:center; }
.stats{ gap:10px; }
}

</style>
</head>
<body>

<!-- Header -->
<div class="header">
    <h2>🏸 Badminton Court Management</h2>
    <div class="header-right">
        <span>Welcome, <strong><?php echo htmlspecialchars($username); ?></strong></span>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>

<!-- Hero -->
<div class="hero">
    <div class="clock" id="clock"></div>
    <p class="greeting-badge" id="greeting">👋 Good day! Ready to play?</p>
    <h1>User Dashboard</h1>
    <div class="divider"></div>
</div>

<!-- Cards -->
<div class="cards">

    <div class="card book">
        <div class="card-shine"></div>
        <div class="card-inner">
            <i class="fas fa-calendar-check"></i>
            <h3>Book a Court</h3>
            <p>Reserve a badminton court for your next game session.</p>
            <a href="court.php" class="card-btn">Book Now →</a>
        </div>
    </div>

    <div class="card history">
        <div class="card-shine"></div>
        <div class="card-inner">
            <i class="fas fa-history"></i>
            <h3>Booking History</h3>
            <p>View and manage all your previous court reservations.</p>
            <a href="history.php" class="card-btn">View History →</a>
        </div>
    </div>

    <div class="card payment">
        <div class="card-shine"></div>
        <div class="card-inner">
            <i class="fas fa-credit-card"></i>
            <h3>Payment History</h3>
            <p>Track all your payment transactions and receipts.</p>
            <a href="payment_history.php" class="card-btn">View Payments →</a>
        </div>
    </div>

</div>

<script>
function updateClock(){
    const now = new Date();
    const opts = {weekday:'long',year:'numeric',month:'long',day:'numeric',hour:'2-digit',minute:'2-digit',second:'2-digit'};
    document.getElementById('clock').textContent = now.toLocaleDateString('en-IN', opts);
    const h = now.getHours();
    let g = 'Good morning';
    if(h >= 12 && h < 17) g = 'Good afternoon';
    else if(h >= 17) g = 'Good evening';
    document.getElementById('greeting').textContent = '👋 ' + g + '! Ready to play?';
}
updateClock();
setInterval(updateClock, 1000);

function animateCount(id, target, prefix, duration){
    let start = 0, step = target / 60;
    const el = document.getElementById(id);
    const iv = setInterval(() => {
        start = Math.min(start + step, target);
        el.textContent = prefix + Math.round(start).toLocaleString('en-IN');
        if(start >= target) clearInterval(iv);
    }, duration / 60);
}
setTimeout(() => animateCount('s1', 12,  '',  800), 300);
setTimeout(() => animateCount('s3', 2400, '₹', 1000), 700);
</script>
</body>
</html>