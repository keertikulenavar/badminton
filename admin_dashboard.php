<?php
session_start();

// ── Simple auth guard ──
// if (!isset($_SESSION['admin_logged_in'])) {
//     header('Location: admin_login.php');
//     exit;
// }

// ── Sample data (replace with real DB queries) ──
$stats = [
    'total_bookings'  => 247,
    'today_bookings'  => 7,
    'total_amount'    => 49400,
    'total_users'     => 48,
];

$recent_bookings = [
    ['id'=>'#1047','name'=>'Ravi Kumar',   'email'=>'ravi@gmail.com',   'initial'=>'R','color'=>'linear-gradient(135deg,#1a5fd4,#00d4aa)','date'=>'20 Apr 2026','slot'=>'6 PM – 7 PM','slot_class'=>'badge-blue',  'status'=>'Confirmed','status_class'=>'badge-green','amount'=>'₹200'],
    ['id'=>'#1046','name'=>'Arjun Sharma', 'email'=>'arjun@gmail.com',  'initial'=>'A','color'=>'linear-gradient(135deg,#e67e22,#f39c12)', 'date'=>'20 Apr 2026','slot'=>'7 PM – 8 PM','slot_class'=>'badge-blue',  'status'=>'Confirmed','status_class'=>'badge-green','amount'=>'₹200'],
    ['id'=>'#1045','name'=>'Priya Rao',    'email'=>'priya@gmail.com',   'initial'=>'P','color'=>'linear-gradient(135deg,#8e44ad,#9b59b6)', 'date'=>'19 Apr 2026','slot'=>'8 AM – 9 AM','slot_class'=>'badge-orange','status'=>'Cancelled','status_class'=>'badge-red',  'amount'=>'—'],
    ['id'=>'#1044','name'=>'Karan Singh',  'email'=>'karan@gmail.com',   'initial'=>'K','color'=>'linear-gradient(135deg,#27ae60,#2ecc71)', 'date'=>'19 Apr 2026','slot'=>'7 AM – 8 AM','slot_class'=>'badge-blue',  'status'=>'Confirmed','status_class'=>'badge-green','amount'=>'₹200'],
    ['id'=>'#1043','name'=>'Meena Patel',  'email'=>'meena@gmail.com',   'initial'=>'M','color'=>'linear-gradient(135deg,#e74c3c,#c0392b)', 'date'=>'18 Apr 2026','slot'=>'5 PM – 6 PM','slot_class'=>'badge-blue',  'status'=>'Confirmed','status_class'=>'badge-green','amount'=>'₹200'],
];

$all_bookings = array_merge($recent_bookings, [
    ['id'=>'#1042','name'=>'Sunita Verma', 'email'=>'sunita@gmail.com', 'initial'=>'S','color'=>'linear-gradient(135deg,#2980b9,#3498db)', 'date'=>'18 Apr 2026','slot'=>'6 AM – 7 AM','slot_class'=>'badge-blue','status'=>'Confirmed','status_class'=>'badge-green','amount'=>'₹200'],
]);

$users = [
    ['no'=>'01','name'=>'Ravi Kumar',   'email'=>'ravi@gmail.com',   'initial'=>'R','color'=>'linear-gradient(135deg,#1a5fd4,#00d4aa)', 'phone'=>'9876543210','bookings'=>12,'spent'=>'₹2,400','joined'=>'Jan 2026'],
    ['no'=>'02','name'=>'Arjun Sharma', 'email'=>'arjun@gmail.com',  'initial'=>'A','color'=>'linear-gradient(135deg,#e67e22,#f39c12)', 'phone'=>'9123456789','bookings'=>8, 'spent'=>'₹1,600','joined'=>'Feb 2026'],
    ['no'=>'03','name'=>'Priya Rao',    'email'=>'priya@gmail.com',   'initial'=>'P','color'=>'linear-gradient(135deg,#8e44ad,#9b59b6)', 'phone'=>'9988776655','bookings'=>5, 'spent'=>'₹800',  'joined'=>'Mar 2026'],
    ['no'=>'04','name'=>'Karan Singh',  'email'=>'karan@gmail.com',   'initial'=>'K','color'=>'linear-gradient(135deg,#27ae60,#2ecc71)', 'phone'=>'9765432108','bookings'=>15,'spent'=>'₹3,000','joined'=>'Dec 2025'],
    ['no'=>'05','name'=>'Meena Patel',  'email'=>'meena@gmail.com',   'initial'=>'M','color'=>'linear-gradient(135deg,#e74c3c,#c0392b)', 'phone'=>'9654321087','bookings'=>3, 'spent'=>'₹600',  'joined'=>'Apr 2026'],
];

$payments = [
    ['pay_id'=>'#P201','book_id'=>'#1047','name'=>'Ravi Kumar',   'amount'=>'₹200','txn'=>'TXN78421','status'=>'Paid','date'=>'20 Apr 2026'],
    ['pay_id'=>'#P200','book_id'=>'#1046','name'=>'Arjun Sharma', 'amount'=>'₹200','txn'=>'TXN78420','status'=>'Paid','date'=>'20 Apr 2026'],
    ['pay_id'=>'#P199','book_id'=>'#1044','name'=>'Karan Singh',  'amount'=>'₹200','txn'=>'TXN78419','status'=>'Paid','date'=>'19 Apr 2026'],
    ['pay_id'=>'#P198','book_id'=>'#1043','name'=>'Meena Patel',  'amount'=>'₹200','txn'=>'TXN78418','status'=>'Paid','date'=>'18 Apr 2026'],
];

$slots = [
    ['time'=>'6 AM – 7 AM', 'status'=>'Available','class'=>'badge-green','bg'=>'rgba(0,212,170,0.06)','border'=>'var(--card-border)'],
    ['time'=>'7 AM – 8 AM', 'status'=>'Booked',   'class'=>'badge-red',  'bg'=>'rgba(232,69,69,0.06)','border'=>'rgba(232,69,69,0.3)'],
    ['time'=>'8 AM – 9 AM', 'status'=>'Booked',   'class'=>'badge-red',  'bg'=>'rgba(232,69,69,0.06)','border'=>'rgba(232,69,69,0.3)'],
    ['time'=>'5 PM – 6 PM', 'status'=>'Available','class'=>'badge-green','bg'=>'rgba(0,212,170,0.06)','border'=>'var(--card-border)'],
    ['time'=>'6 PM – 7 PM', 'status'=>'Booked',   'class'=>'badge-red',  'bg'=>'rgba(232,69,69,0.06)','border'=>'rgba(232,69,69,0.3)'],
    ['time'=>'7 PM – 8 PM', 'status'=>'Booked',   'class'=>'badge-red',  'bg'=>'rgba(232,69,69,0.06)','border'=>'rgba(232,69,69,0.3)'],
    ['time'=>'8 PM – 9 PM', 'status'=>'Available','class'=>'badge-green','bg'=>'rgba(0,212,170,0.06)','border'=>'var(--card-border)'],
];

$weekly_bars = [
    ['day'=>'Mon','h'=>55,'accent'=>false],
    ['day'=>'Tue','h'=>80,'accent'=>false],
    ['day'=>'Wed','h'=>100,'accent'=>true],
    ['day'=>'Thu','h'=>65,'accent'=>false],
    ['day'=>'Fri','h'=>70,'accent'=>false],
    ['day'=>'Sat','h'=>45,'accent'=>false],
    ['day'=>'Sun','h'=>30,'accent'=>false],
];

$monthly_bars = [
    ['month'=>'Oct','h'=>60,'accent'=>false],
    ['month'=>'Nov','h'=>75,'accent'=>false],
    ['month'=>'Dec','h'=>85,'accent'=>false],
    ['month'=>'Jan','h'=>100,'accent'=>true],
    ['month'=>'Feb','h'=>90,'accent'=>false],
    ['month'=>'Mar','h'=>78,'accent'=>false],
    ['month'=>'Apr','h'=>65,'accent'=>false],
];

$peak_hours = [
    ['label'=>'6 PM–7 PM','pct'=>92,'gradient'=>'linear-gradient(90deg,#00d4aa,#2d7ef0)'],
    ['label'=>'7 PM–8 PM','pct'=>88,'gradient'=>'linear-gradient(90deg,#2d7ef0,#1a5fd4)'],
    ['label'=>'7 AM–8 AM','pct'=>74,'gradient'=>'linear-gradient(90deg,#ff8c42,#ffcc02)'],
    ['label'=>'8 PM–9 PM','pct'=>65,'gradient'=>'linear-gradient(90deg,#9b59b6,#c39bd3)'],
    ['label'=>'6 AM–7 AM','pct'=>42,'gradient'=>'linear-gradient(90deg,#7f8c8d,#95a5a6)'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard — Badminton Court</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root {
  --navy:        #0a1628;
  --navy2:       #0f2044;
  --blue:        #1a5fd4;
  --blue2:       #2d7ef0;
  --accent:      #00d4aa;
  --accent2:     #00b894;
  --red:         #e84545;
  --orange:      #ff8c42;
  --purple:      #9b59b6;
  --white:       #ffffff;
  --text:        #e8edf5;
  --muted:       #8899bb;
  --card:        rgba(255,255,255,0.04);
  --card-border: rgba(255,255,255,0.08);
  --sidebar-w:   260px;
}
*{box-sizing:border-box;margin:0;padding:0;}
html,body{height:100%;font-family:'DM Sans',sans-serif;background:var(--navy);color:var(--text);overflow:hidden;}

/* ── SHELL ── */
.shell{display:flex;height:100vh;overflow:hidden;}

/* ── SIDEBAR ── */
.sidebar{width:var(--sidebar-w);min-width:var(--sidebar-w);background:var(--navy2);border-right:1px solid var(--card-border);display:flex;flex-direction:column;height:100vh;overflow:hidden;position:relative;z-index:10;}
.sidebar::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--blue),var(--accent));}
.sidebar-logo{padding:28px 24px 20px;border-bottom:1px solid var(--card-border);}
.logo-badge{display:flex;align-items:center;gap:12px;}
.logo-icon{width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,var(--blue),var(--accent));display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;}
.logo-text .brand{font-family:'Syne',sans-serif;font-weight:800;font-size:15px;color:var(--white);letter-spacing:.3px;line-height:1.2;}
.logo-text .sub{font-size:11px;color:var(--muted);letter-spacing:.5px;}
.sidebar-profile{padding:20px 24px;border-bottom:1px solid var(--card-border);display:flex;align-items:center;gap:12px;}
.avatar{width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--blue2),var(--accent));display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:white;flex-shrink:0;}
.profile-info .name{font-size:13px;font-weight:600;color:var(--white);}
.profile-info .role{font-size:11px;color:var(--accent);text-transform:uppercase;letter-spacing:.8px;}
.sidebar-nav{flex:1;overflow-y:auto;padding:12px 0;scrollbar-width:thin;scrollbar-color:rgba(255,255,255,.1) transparent;}
.nav-section{padding:14px 24px 6px;font-size:10px;text-transform:uppercase;letter-spacing:1.5px;color:var(--muted);font-weight:600;}
.nav-item{display:flex;align-items:center;gap:12px;padding:11px 24px;cursor:pointer;transition:all .2s;text-decoration:none;color:var(--muted);font-size:14px;font-weight:500;position:relative;}
.nav-item:hover{background:rgba(255,255,255,.05);color:var(--white);}
.nav-item.active{background:rgba(26,95,212,.15);color:var(--white);border-right:3px solid var(--blue2);}
.nav-item.active::before{content:'';position:absolute;left:0;top:6px;bottom:6px;width:3px;background:var(--blue2);border-radius:0 3px 3px 0;}
.nav-item i{width:20px;text-align:center;font-size:15px;}
.nav-item.active i{color:var(--blue2);}
.nav-badge{margin-left:auto;background:var(--blue2);color:white;font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px;min-width:20px;text-align:center;}
.nav-badge.accent{background:var(--accent);color:var(--navy);}
.sidebar-footer{padding:16px 24px;border-top:1px solid var(--card-border);}
.logout-btn{display:flex;align-items:center;gap:10px;width:100%;padding:11px 16px;background:rgba(232,69,69,.12);border:1px solid rgba(232,69,69,.25);border-radius:10px;color:#ff8080;font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;text-decoration:none;}
.logout-btn:hover{background:rgba(232,69,69,.22);color:#ffaaaa;}

/* ── MAIN ── */
.main{flex:1;display:flex;flex-direction:column;overflow:hidden;min-width:0;}
.topbar{padding:18px 32px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--card-border);background:rgba(10,22,40,.8);backdrop-filter:blur(10px);flex-shrink:0;}
.topbar-left h1{font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:var(--white);}
.topbar-left .breadcrumb{font-size:12px;color:var(--muted);margin-top:2px;}
.topbar-right{display:flex;align-items:center;gap:14px;}
.topbar-btn{width:38px;height:38px;border-radius:10px;border:1px solid var(--card-border);background:var(--card);color:var(--muted);display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:15px;transition:all .2s;}
.topbar-btn:hover{border-color:var(--blue2);color:var(--white);background:rgba(45,126,240,.12);}
.notification-dot{position:relative;}
.notification-dot::after{content:'';position:absolute;top:6px;right:6px;width:7px;height:7px;background:var(--red);border-radius:50%;border:2px solid var(--navy);}
.content-area{flex:1;overflow-y:auto;padding:28px 32px;scrollbar-width:thin;scrollbar-color:rgba(255,255,255,.1) transparent;}
.content-area::-webkit-scrollbar{width:6px;}
.content-area::-webkit-scrollbar-thumb{background:rgba(255,255,255,.1);border-radius:3px;}

/* ── PAGES ── */
.page{display:none;animation:fadeIn .3s ease both;}
.page.active{display:block;}
@keyframes fadeIn{from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:translateY(0);}}
.page-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;}
.page-head-title{font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:var(--white);}
.page-head-sub{font-size:13px;color:var(--muted);margin-top:2px;}

/* ── 4 BIG STAT CARDS ── */
.stats-quad{display:grid;grid-template-columns:repeat(2,1fr);gap:20px;margin-bottom:32px;}
.big-stat{background:var(--navy2);border:1px solid var(--card-border);border-radius:20px;padding:32px 28px;position:relative;overflow:hidden;transition:transform .2s,border-color .25s;cursor:default;}
.big-stat:hover{transform:translateY(-3px);border-color:rgba(255,255,255,.15);}
.big-stat::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;}
.big-stat.s-blue::before   {background:linear-gradient(90deg,#1a5fd4,#2d7ef0);}
.big-stat.s-green::before  {background:linear-gradient(90deg,#00b894,#00d4aa);}
.big-stat.s-orange::before {background:linear-gradient(90deg,#ff8c42,#ffcc02);}
.big-stat.s-purple::before {background:linear-gradient(90deg,#9b59b6,#c39bd3);}
.big-stat::after{content:'';position:absolute;width:140px;height:140px;border-radius:50%;bottom:-40px;right:-30px;opacity:.06;}
.big-stat.s-blue::after   {background:var(--blue2);}
.big-stat.s-green::after  {background:var(--accent);}
.big-stat.s-orange::after {background:var(--orange);}
.big-stat.s-purple::after {background:var(--purple);}
.bstat-icon{width:54px;height:54px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:20px;}
.big-stat.s-blue   .bstat-icon{background:rgba(45,126,240,.15);color:var(--blue2);}
.big-stat.s-green  .bstat-icon{background:rgba(0,212,170,.15); color:var(--accent);}
.big-stat.s-orange .bstat-icon{background:rgba(255,140,66,.15);color:var(--orange);}
.big-stat.s-purple .bstat-icon{background:rgba(155,89,182,.15);color:#c39bd3;}
.bstat-num{font-family:'Syne',sans-serif;font-size:44px;font-weight:800;color:var(--white);line-height:1;margin-bottom:6px;}
.bstat-lbl{font-size:13px;color:var(--muted);text-transform:uppercase;letter-spacing:1.2px;font-weight:500;}
.bstat-badge{position:absolute;top:22px;right:22px;font-size:11px;font-weight:600;padding:4px 10px;border-radius:20px;}
.big-stat.s-blue   .bstat-badge{background:rgba(45,126,240,.15);color:#7bb3ff;}
.big-stat.s-green  .bstat-badge{background:rgba(0,212,170,.15); color:var(--accent);}
.big-stat.s-orange .bstat-badge{background:rgba(255,140,66,.15);color:var(--orange);}
.big-stat.s-purple .bstat-badge{background:rgba(155,89,182,.15);color:#c39bd3;}

/* ── TABLE CARD ── */
.table-card{background:var(--card);border:1px solid var(--card-border);border-radius:16px;overflow:hidden;margin-bottom:24px;}
.table-header{padding:18px 22px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--card-border);}
.table-title{font-family:'Syne',sans-serif;font-size:15px;font-weight:700;color:var(--white);display:flex;align-items:center;gap:8px;}
.table-title .dot{width:8px;height:8px;border-radius:50%;background:var(--accent);animation:pulse 2s ease infinite;}
@keyframes pulse{0%,100%{opacity:1;}50%{opacity:.4;}}
.header-actions{display:flex;gap:8px;}
.btn-sm{padding:7px 14px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;border:none;transition:all .2s;display:inline-flex;align-items:center;gap:6px;text-decoration:none;font-family:'DM Sans',sans-serif;}
.btn-outline{background:transparent;border:1px solid var(--card-border);color:var(--muted);}
.btn-outline:hover{border-color:var(--blue2);color:var(--white);}
.btn-primary{background:var(--blue2);color:white;}
.btn-primary:hover{background:var(--blue);}
.btn-accent{background:var(--accent);color:var(--navy);}
.btn-accent:hover{background:var(--accent2);}
.btn-danger{background:rgba(232,69,69,.15);border:1px solid rgba(232,69,69,.3);color:#ff8080;}
.btn-danger:hover{background:rgba(232,69,69,.25);}
.btn{padding:10px 22px;border-radius:9px;font-size:14px;font-weight:600;cursor:pointer;border:none;transition:all .2s;display:inline-flex;align-items:center;gap:7px;font-family:'DM Sans',sans-serif;}
.table-scroll{overflow-x:auto;}
table{width:100%;border-collapse:collapse;font-size:13.5px;}
thead tr{background:rgba(255,255,255,.03);}
th{padding:12px 18px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:1px;border-bottom:1px solid var(--card-border);white-space:nowrap;}
tbody tr{border-bottom:1px solid rgba(255,255,255,.04);transition:background .15s;}
tbody tr:last-child{border-bottom:none;}
tbody tr:hover{background:rgba(255,255,255,.04);}
td{padding:14px 18px;vertical-align:middle;color:var(--text);}
.user-cell{display:flex;align-items:center;gap:10px;}
.user-av{width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:white;flex-shrink:0;}
.user-name{font-weight:600;color:var(--white);font-size:13px;}
.user-email{font-size:11px;color:var(--muted);margin-top:1px;}
.badge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600;}
.badge-blue   {background:rgba(45,126,240,.15);color:#7bb3ff;}
.badge-green  {background:rgba(0,212,170,.12); color:var(--accent);}
.badge-orange {background:rgba(255,140,66,.15);color:var(--orange);}
.badge-red    {background:rgba(232,69,69,.15); color:#ff8080;}
.badge-purple {background:rgba(155,89,182,.15);color:#c39bd3;}
.status-dot{width:7px;height:7px;border-radius:50%;display:inline-block;margin-right:5px;}

/* ── SEARCH ── */
.search-row{padding:14px 18px;border-bottom:1px solid var(--card-border);display:flex;align-items:center;gap:10px;}
.search-wrap{flex:1;position:relative;}
.search-wrap i{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:13px;}
.search-input{width:100%;background:rgba(255,255,255,.05);border:1px solid var(--card-border);border-radius:8px;padding:9px 14px 9px 36px;color:var(--text);font-size:13px;font-family:'DM Sans',sans-serif;outline:none;transition:border .2s;}
.search-input::placeholder{color:var(--muted);}
.search-input:focus{border-color:var(--blue2);}

/* ── FORMS ── */
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;padding:22px;}
.form-group{display:flex;flex-direction:column;gap:7px;}
.form-group.full{grid-column:span 2;}
.form-label{font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.8px;}
.form-input,.form-select{background:rgba(255,255,255,.05);border:1px solid var(--card-border);border-radius:9px;padding:11px 14px;color:var(--text);font-size:14px;font-family:'DM Sans',sans-serif;outline:none;transition:border .2s,box-shadow .2s;}
.form-input:focus,.form-select:focus{border-color:var(--blue2);box-shadow:0 0 0 3px rgba(45,126,240,.12);}
.form-select option{background:var(--navy2);}
.form-footer{padding:16px 22px;border-top:1px solid var(--card-border);display:flex;gap:10px;justify-content:flex-end;}

/* ── ACTIVITY ── */
.activity-item{display:flex;align-items:flex-start;gap:14px;padding:14px 18px;border-bottom:1px solid rgba(255,255,255,.04);transition:background .15s;}
.activity-item:hover{background:rgba(255,255,255,.03);}
.activity-item:last-child{border-bottom:none;}
.activity-icon{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;}
.act-blue  {background:rgba(45,126,240,.15);color:var(--blue2);}
.act-green {background:rgba(0,212,170,.12); color:var(--accent);}
.act-orange{background:rgba(255,140,66,.15);color:var(--orange);}
.act-red   {background:rgba(232,69,69,.15); color:var(--red);}
.activity-text{flex:1;font-size:13px;color:var(--text);line-height:1.4;}
.activity-text strong{color:var(--white);}
.activity-time{font-size:11px;color:var(--muted);margin-top:3px;}

/* ── SUMMARY ROW ── */
.summary-row{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:28px;}

/* ── CHART ── */
.chart-wrap{padding:22px;}
.chart-bars{display:flex;align-items:flex-end;gap:8px;height:140px;margin-bottom:8px;}
.bar-col{flex:1;display:flex;flex-direction:column;align-items:center;gap:6px;height:100%;justify-content:flex-end;}
.bar{width:100%;border-radius:6px 6px 0 0;background:linear-gradient(180deg,var(--blue2),var(--blue));transition:opacity .2s;min-height:4px;}
.bar:hover{opacity:.75;}
.bar.accent{background:linear-gradient(180deg,var(--accent),var(--accent2));}
.bar-label{font-size:10px;color:var(--muted);text-align:center;}

@media(max-width:1100px){.stats-quad{grid-template-columns:repeat(2,1fr);}}
@media(max-width:800px){.sidebar{width:60px;min-width:60px;}.logo-text,.profile-info,.nav-item span,.sidebar-footer span,.nav-badge,.nav-section{display:none;}}
</style>
</head>
<body>
<div class="shell">

  <!-- ═══ SIDEBAR ═══ -->
  <aside class="sidebar">
    <div class="sidebar-logo">
      <div class="logo-badge">
        <div class="logo-icon">🏸</div>
        <div class="logo-text">
          <div class="brand">BadmintonCourt</div>
          <div class="sub">Admin Control</div>
        </div>
      </div>
    </div>
    <div class="sidebar-profile">
      <div class="avatar">A</div>
      <div class="profile-info">
        <div class="name">Admin</div>
        <div class="role">Super Admin</div>
      </div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section">Main</div>
      <a class="nav-item active" onclick="showPage('dashboard',this)"><i class="fas fa-th-large"></i><span>Dashboard</span></a>
      <a class="nav-item" onclick="showPage('bookings',this)"><i class="fas fa-calendar-check"></i><span>Bookings</span><span class="nav-badge accent">12</span></a>
      <a class="nav-item" onclick="showPage('users',this)"><i class="fas fa-users"></i><span>Users</span><span class="nav-badge"><?= $stats['total_users'] ?></span></a>
      <a class="nav-item" onclick="showPage('payments',this)"><i class="fas fa-credit-card"></i><span>Payments</span></a>
      <div class="nav-section">Management</div>
      <a class="nav-item" onclick="showPage('courts',this)"><i class="fas fa-map-marker-alt"></i><span>Courts</span></a>
      <a class="nav-item" onclick="showPage('reports',this)"><i class="fas fa-chart-bar"></i><span>Reports</span></a>
      <a class="nav-item" onclick="showPage('settings',this)"><i class="fas fa-cog"></i><span>Settings</span></a>
    </nav>
    <div class="sidebar-footer">
      <a href="admin_login.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
  </aside>

  <!-- ═══ MAIN ═══ -->
  <div class="main">
    <div class="topbar">
      <div class="topbar-left">
        <h1 id="page-title">Overview</h1>
        <div class="breadcrumb" id="page-breadcrumb">Dashboard → Overview</div>
      </div>
      <div class="topbar-right">
        <div class="topbar-btn notification-dot" title="Notifications"><i class="fas fa-bell"></i></div>
        <div class="topbar-btn" title="Refresh" onclick="location.reload()"><i class="fas fa-sync-alt"></i></div>
        <div class="topbar-btn" title="Settings" onclick="showPage('settings',null)"><i class="fas fa-cog"></i></div>
      </div>
    </div>

    <div class="content-area">

      <!-- ════ DASHBOARD ════ -->
      <div class="page active" id="page-dashboard">
        <div class="page-head">
          <div>
            <div class="page-head-title">Welcome back, Admin 👋</div>
            <div class="page-head-sub">Here's what's happening with your courts today.</div>
          </div>
          <div style="display:flex;gap:8px;">
            <button class="btn btn-outline btn-sm" onclick="showPage('reports',null)"><i class="fas fa-chart-bar"></i> Reports</button>
            <button class="btn btn-primary btn-sm" onclick="showPage('bookings',null)"><i class="fas fa-plus"></i> New Booking</button>
          </div>
        </div>

        <!-- 4 BIG STAT CARDS -->
        <div class="stats-quad">
          <div class="big-stat s-blue">
            <span class="bstat-badge">All time</span>
            <div class="bstat-icon"><i class="fas fa-calendar-check"></i></div>
            <div class="bstat-num"><?= number_format($stats['total_bookings']) ?></div>
            <div class="bstat-lbl">Total Bookings</div>
          </div>
          <div class="big-stat s-green">
            <span class="bstat-badge">Today</span>
            <div class="bstat-icon"><i class="fas fa-calendar-day"></i></div>
            <div class="bstat-num"><?= $stats['today_bookings'] ?></div>
            <div class="bstat-lbl">Today's Bookings</div>
          </div>
          <div class="big-stat s-orange">
            <span class="bstat-badge">↑ 21%</span>
            <div class="bstat-icon"><i class="fas fa-rupee-sign"></i></div>
            <div class="bstat-num">₹<?= number_format($stats['total_amount']) ?></div>
            <div class="bstat-lbl">Total Amount</div>
          </div>
          <div class="big-stat s-purple">
            <span class="bstat-badge">↑ 8%</span>
            <div class="bstat-icon"><i class="fas fa-users"></i></div>
            <div class="bstat-num"><?= $stats['total_users'] ?></div>
            <div class="bstat-lbl">Total Users</div>
          </div>
        </div>

        <!-- Summary row -->
        <div class="summary-row">
          <!-- Weekly Revenue Chart -->
          <div class="table-card">
            <div class="table-header">
              <div class="table-title"><span class="dot"></span>Weekly Revenue</div>
              <span class="badge badge-green">This week</span>
            </div>
            <div class="chart-wrap">
              <div class="chart-bars">
                <?php foreach ($weekly_bars as $b): ?>
                <div class="bar-col">
                  <div class="bar <?= $b['accent'] ? 'accent' : '' ?>" style="height:<?= $b['h'] ?>%"></div>
                  <div class="bar-label"><?= $b['day'] ?></div>
                </div>
                <?php endforeach; ?>
              </div>
              <div style="display:flex;justify-content:space-between;padding-top:6px;">
                <span style="font-size:11px;color:var(--muted)">₹0</span>
                <span style="font-size:12px;color:var(--white);font-weight:600">Total: ₹9,800</span>
                <span style="font-size:11px;color:var(--muted)">₹2,800</span>
              </div>
            </div>
          </div>

          <!-- Activity Feed -->
          <div class="table-card">
            <div class="table-header">
              <div class="table-title"><span class="dot"></span>Recent Activity</div>
              <button class="btn-sm btn-outline" onclick="showPage('bookings',null)">View All</button>
            </div>
            <div class="activity-item"><div class="activity-icon act-green"><i class="fas fa-check"></i></div><div class="activity-text"><strong>Ravi Kumar</strong> booked Court 1<br><span class="activity-time">5 min ago · 6 PM – 7 PM</span></div></div>
            <div class="activity-item"><div class="activity-icon act-blue"><i class="fas fa-user-plus"></i></div><div class="activity-text"><strong>Meena Patel</strong> registered as new user<br><span class="activity-time">18 min ago</span></div></div>
            <div class="activity-item"><div class="activity-icon act-orange"><i class="fas fa-rupee-sign"></i></div><div class="activity-text"><strong>₹200</strong> payment received from Arjun S.<br><span class="activity-time">32 min ago · TXN #78421</span></div></div>
            <div class="activity-item"><div class="activity-icon act-red"><i class="fas fa-times"></i></div><div class="activity-text"><strong>Priya Rao</strong> cancelled booking<br><span class="activity-time">1 hr ago · 8 AM – 9 AM</span></div></div>
            <div class="activity-item"><div class="activity-icon act-green"><i class="fas fa-check"></i></div><div class="activity-text"><strong>Karan Singh</strong> booked Court 1<br><span class="activity-time">2 hr ago · 7 AM – 8 AM</span></div></div>
          </div>
        </div>

        <!-- Recent Bookings Table -->
        <div class="table-card">
          <div class="table-header">
            <div class="table-title"><span class="dot"></span>Recent Bookings</div>
            <div class="header-actions">
              <span style="font-size:12px;color:var(--muted);align-self:center;">Latest 5</span>
              <button class="btn-sm btn-primary" onclick="showPage('bookings',null)"><i class="fas fa-arrow-right"></i> All Bookings</button>
            </div>
          </div>
          <div class="table-scroll">
            <table>
              <thead><tr><th>#ID</th><th>User</th><th>Date</th><th>Time Slot</th><th>Status</th><th>Amount</th></tr></thead>
              <tbody>
                <?php foreach ($recent_bookings as $b): ?>
                <tr>
                  <td style="color:var(--muted);font-size:12px;"><?= htmlspecialchars($b['id']) ?></td>
                  <td>
                    <div class="user-cell">
                      <div class="user-av" style="background:<?= $b['color'] ?>"><?= htmlspecialchars($b['initial']) ?></div>
                      <div>
                        <div class="user-name"><?= htmlspecialchars($b['name']) ?></div>
                        <div class="user-email"><?= htmlspecialchars($b['email']) ?></div>
                      </div>
                    </div>
                  </td>
                  <td><?= htmlspecialchars($b['date']) ?></td>
                  <td><span class="badge <?= $b['slot_class'] ?>"><?= htmlspecialchars($b['slot']) ?></span></td>
                  <td>
                    <span class="badge <?= $b['status_class'] ?>">
                      <span class="status-dot" style="background:<?= $b['status']==='Confirmed' ? 'var(--accent)' : 'var(--red)' ?>"></span>
                      <?= htmlspecialchars($b['status']) ?>
                    </span>
                  </td>
                  <td style="color:<?= $b['amount']==='—' ? 'var(--muted)' : 'var(--accent)' ?>;font-weight:600;"><?= htmlspecialchars($b['amount']) ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ════ BOOKINGS ════ -->
      <div class="page" id="page-bookings">
        <div class="page-head">
          <div><div class="page-head-title">All Bookings</div><div class="page-head-sub">Manage all court reservations</div></div>
          <div style="display:flex;gap:8px;">
            <button class="btn btn-outline btn-sm"><i class="fas fa-download"></i> Export</button>
            <button class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Booking</button>
          </div>
        </div>
        <div class="table-card">
          <div class="search-row">
            <div class="search-wrap"><i class="fas fa-search"></i><input class="search-input" type="text" placeholder="Search bookings..." oninput="filterTable('bookings-tbody',this.value)"></div>
            <select class="form-select" style="width:140px;padding:9px 12px;font-size:13px;"><option>All Status</option><option>Confirmed</option><option>Cancelled</option></select>
          </div>
          <div class="table-scroll">
            <table>
              <thead><tr><th>#ID</th><th>User</th><th>Date</th><th>Time Slot</th><th>Court</th><th>Status</th><th>Payment</th><th>Action</th></tr></thead>
              <tbody id="bookings-tbody">
                <?php foreach ($all_bookings as $b): ?>
                <tr>
                  <td style="color:var(--muted);font-size:12px;"><?= htmlspecialchars($b['id']) ?></td>
                  <td><div class="user-cell"><div class="user-av" style="background:<?= $b['color'] ?>"><?= htmlspecialchars($b['initial']) ?></div><div><div class="user-name"><?= htmlspecialchars($b['name']) ?></div><div class="user-email"><?= htmlspecialchars($b['email']) ?></div></div></div></td>
                  <td><?= htmlspecialchars($b['date']) ?></td>
                  <td><span class="badge <?= $b['slot_class'] ?>"><?= htmlspecialchars($b['slot']) ?></span></td>
                  <td>Court 1</td>
                  <td><span class="badge <?= $b['status_class'] ?>"><?= htmlspecialchars($b['status']) ?></span></td>
                  <td><?php if($b['status']==='Confirmed'): ?><span class="badge badge-green">Paid <?= $b['amount'] ?></span><?php else: ?><span class="badge" style="color:var(--muted);">—</span><?php endif; ?></td>
                  <td><?php if($b['status']==='Confirmed'): ?><button class="btn-sm btn-danger" onclick="confirmCancel()"><i class="fas fa-trash"></i></button><?php else: ?><button class="btn-sm btn-outline" disabled style="opacity:.4;cursor:default;">—</button><?php endif; ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ════ USERS ════ -->
      <div class="page" id="page-users">
        <div class="page-head">
          <div><div class="page-head-title">Users</div><div class="page-head-sub">All registered members</div></div>
          <button class="btn btn-primary btn-sm"><i class="fas fa-user-plus"></i> Add User</button>
        </div>
        <div class="table-card">
          <div class="search-row"><div class="search-wrap"><i class="fas fa-search"></i><input class="search-input" type="text" placeholder="Search users..." oninput="filterTable('users-tbody',this.value)"></div></div>
          <div class="table-scroll">
            <table>
              <thead><tr><th>#</th><th>User</th><th>Contact</th><th>Bookings</th><th>Total Spent</th><th>Joined</th><th>Action</th></tr></thead>
              <tbody id="users-tbody">
                <?php foreach ($users as $u): ?>
                <tr>
                  <td style="color:var(--muted);font-size:12px;"><?= htmlspecialchars($u['no']) ?></td>
                  <td><div class="user-cell"><div class="user-av" style="background:<?= $u['color'] ?>"><?= htmlspecialchars($u['initial']) ?></div><div><div class="user-name"><?= htmlspecialchars($u['name']) ?></div><div class="user-email"><?= htmlspecialchars($u['email']) ?></div></div></div></td>
                  <td style="color:var(--muted);"><?= htmlspecialchars($u['phone']) ?></td>
                  <td><span class="badge badge-blue"><?= $u['bookings'] ?> bookings</span></td>
                  <td style="color:var(--accent);font-weight:600;"><?= htmlspecialchars($u['spent']) ?></td>
                  <td style="color:var(--muted);font-size:12px;"><?= htmlspecialchars($u['joined']) ?></td>
                  <td><button class="btn-sm btn-danger"><i class="fas fa-trash"></i></button></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ════ PAYMENTS ════ -->
      <div class="page" id="page-payments">
        <div class="page-head">
          <div><div class="page-head-title">Payments</div><div class="page-head-sub">Transaction history & revenue tracking</div></div>
          <button class="btn btn-outline btn-sm"><i class="fas fa-download"></i> Export CSV</button>
        </div>
        <div class="table-card">
          <div class="search-row"><div class="search-wrap"><i class="fas fa-search"></i><input class="search-input" type="text" placeholder="Search transactions..." oninput="filterTable('payments-tbody',this.value)"></div></div>
          <div class="table-scroll">
            <table>
              <thead><tr><th>Pay ID</th><th>Booking ID</th><th>User</th><th>Amount</th><th>TXN ID</th><th>Status</th><th>Date</th></tr></thead>
              <tbody id="payments-tbody">
                <?php foreach ($payments as $p): ?>
                <tr>
                  <td><span class="badge badge-purple"><?= htmlspecialchars($p['pay_id']) ?></span></td>
                  <td><span class="badge badge-blue"><?= htmlspecialchars($p['book_id']) ?></span></td>
                  <td><div class="user-name"><?= htmlspecialchars($p['name']) ?></div></td>
                  <td style="color:var(--accent);font-weight:600;"><?= htmlspecialchars($p['amount']) ?></td>
                  <td style="font-size:12px;color:var(--muted);"><?= htmlspecialchars($p['txn']) ?></td>
                  <td><span class="badge badge-green"><?= htmlspecialchars($p['status']) ?></span></td>
                  <td style="font-size:12px;color:var(--muted);"><?= htmlspecialchars($p['date']) ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ════ COURTS ════ -->
      <div class="page" id="page-courts">
        <div class="page-head"><div><div class="page-head-title">Courts</div><div class="page-head-sub">Manage court details & pricing</div></div></div>
        <div class="summary-row">
          <div class="table-card">
            <div class="table-header"><div class="table-title"><span class="dot"></span>Court Details</div></div>
            <div style="padding:22px;">
              <div style="display:flex;align-items:center;gap:18px;padding:18px;background:rgba(255,255,255,.04);border-radius:12px;border:1px solid var(--card-border);margin-bottom:14px;">
                <div style="width:52px;height:52px;background:linear-gradient(135deg,var(--blue),var(--accent));border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0;">🏸</div>
                <div><div style="font-family:'Syne',sans-serif;font-weight:700;font-size:16px;color:var(--white);">Court 1</div><div style="font-size:12px;color:var(--muted);margin-top:3px;">Indoor Badminton Court · Standard</div></div>
                <div style="margin-left:auto;"><span class="badge badge-green">Active</span></div>
              </div>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div style="padding:14px;background:rgba(255,255,255,.03);border-radius:10px;border:1px solid var(--card-border);"><div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Rate</div><div style="font-size:22px;font-weight:700;color:var(--accent);font-family:'Syne',sans-serif;">₹200/hr</div></div>
                <div style="padding:14px;background:rgba(255,255,255,.03);border-radius:10px;border:1px solid var(--card-border);"><div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Available Slots</div><div style="font-size:22px;font-weight:700;color:var(--blue2);font-family:'Syne',sans-serif;">7 / day</div></div>
                <div style="padding:14px;background:rgba(255,255,255,.03);border-radius:10px;border:1px solid var(--card-border);"><div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Opening</div><div style="font-size:15px;font-weight:600;color:var(--white);">6 AM</div></div>
                <div style="padding:14px;background:rgba(255,255,255,.03);border-radius:10px;border:1px solid var(--card-border);"><div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Closing</div><div style="font-size:15px;font-weight:600;color:var(--white);">9 PM</div></div>
              </div>
            </div>
          </div>
          <div class="table-card">
            <div class="table-header"><div class="table-title"><span class="dot"></span>Today's Slot Status</div></div>
            <div style="padding:18px;display:grid;gap:8px;">
              <?php foreach ($slots as $s): ?>
              <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 14px;border-radius:9px;border:1px solid <?= $s['border'] ?>;background:<?= $s['bg'] ?>;">
                <span style="font-size:13px;color:var(--white);"><?= htmlspecialchars($s['time']) ?></span>
                <span class="badge <?= $s['class'] ?>"><?= htmlspecialchars($s['status']) ?></span>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- ════ REPORTS ════ -->
      <div class="page" id="page-reports">
        <div class="page-head">
          <div><div class="page-head-title">Reports & Analytics</div><div class="page-head-sub">Revenue and booking performance overview</div></div>
          <button class="btn btn-outline btn-sm"><i class="fas fa-download"></i> Download PDF</button>
        </div>
        <div class="stats-quad">
          <div class="big-stat s-blue"><span class="bstat-badge">Lifetime</span><div class="bstat-icon"><i class="fas fa-chart-line"></i></div><div class="bstat-num">₹49,400</div><div class="bstat-lbl">Total Revenue</div></div>
          <div class="big-stat s-green"><span class="bstat-badge">This week</span><div class="bstat-icon"><i class="fas fa-calendar-week"></i></div><div class="bstat-num">₹9,800</div><div class="bstat-lbl">Weekly Revenue</div></div>
          <div class="big-stat s-orange"><span class="bstat-badge">This month</span><div class="bstat-icon"><i class="fas fa-calendar-alt"></i></div><div class="bstat-num">₹38,400</div><div class="bstat-lbl">Monthly Revenue</div></div>
          <div class="big-stat s-purple"><span class="bstat-badge">Rate</span><div class="bstat-icon"><i class="fas fa-percentage"></i></div><div class="bstat-num">93.6%</div><div class="bstat-lbl">Booking Rate</div></div>
        </div>
        <div class="summary-row">
          <div class="table-card">
            <div class="table-header"><div class="table-title"><span class="dot"></span>Monthly Revenue</div></div>
            <div class="chart-wrap">
              <div class="chart-bars" style="height:160px;">
                <?php foreach ($monthly_bars as $m): ?>
                <div class="bar-col">
                  <div class="bar <?= $m['accent'] ? 'accent' : '' ?>" style="height:<?= $m['h'] ?>%"></div>
                  <div class="bar-label"><?= $m['month'] ?></div>
                </div>
                <?php endforeach; ?>
              </div>
              <div style="text-align:center;font-size:12px;color:var(--muted);">Revenue trend — last 7 months</div>
            </div>
          </div>
          <div class="table-card">
            <div class="table-header"><div class="table-title"><span class="dot"></span>Peak Hours</div></div>
            <div style="padding:18px;display:grid;gap:10px;">
              <?php foreach ($peak_hours as $ph): ?>
              <div style="display:flex;align-items:center;gap:12px;">
                <span style="font-size:12px;color:var(--muted);width:80px;"><?= htmlspecialchars($ph['label']) ?></span>
                <div style="flex:1;height:8px;background:rgba(255,255,255,.07);border-radius:4px;overflow:hidden;">
                  <div style="width:<?= $ph['pct'] ?>%;height:100%;background:<?= $ph['gradient'] ?>;border-radius:4px;"></div>
                </div>
                <span style="font-size:12px;color:var(--white);font-weight:600;width:35px;"><?= $ph['pct'] ?>%</span>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- ════ SETTINGS ════ -->
      <div class="page" id="page-settings">
        <div class="page-head"><div><div class="page-head-title">Settings</div><div class="page-head-sub">System configuration & preferences</div></div></div>
        <div class="summary-row">
          <div class="table-card">
            <div class="table-header"><div class="table-title"><span class="dot"></span>Court Settings</div></div>
            <form method="POST" action="save_settings.php">
              <div class="form-grid">
                <div class="form-group"><div class="form-label">Court Name</div><input class="form-input" type="text" name="court_name" value="Court 1 — Indoor"></div>
                <div class="form-group"><div class="form-label">Price per Hour (₹)</div><input class="form-input" type="number" name="price" value="200"></div>
                <div class="form-group"><div class="form-label">Opening Time</div><input class="form-input" type="time" name="opening" value="06:00"></div>
                <div class="form-group"><div class="form-label">Closing Time</div><input class="form-input" type="time" name="closing" value="21:00"></div>
                <div class="form-group full"><div class="form-label">UPI Payment ID</div><input class="form-input" type="text" name="upi" value="8660201@ybl"></div>
              </div>
              <div class="form-footer">
                <button type="reset" class="btn btn-outline btn-sm">Reset</button>
                <button type="submit" class="btn btn-accent btn-sm"><i class="fas fa-save"></i> Save Changes</button>
              </div>
            </form>
          </div>
          <div class="table-card">
            <div class="table-header"><div class="table-title"><span class="dot"></span>Admin Account</div></div>
            <form method="POST" action="change_password.php">
              <div class="form-grid">
                <div class="form-group full"><div class="form-label">Admin Username</div><input class="form-input" type="text" name="username" value="admin"></div>
                <div class="form-group full"><div class="form-label">Current Password</div><input class="form-input" type="password" name="current_password" placeholder="••••••••"></div>
                <div class="form-group"><div class="form-label">New Password</div><input class="form-input" type="password" name="new_password" placeholder="New password"></div>
                <div class="form-group"><div class="form-label">Confirm Password</div><input class="form-input" type="password" name="confirm_password" placeholder="Confirm"></div>
              </div>
              <div class="form-footer">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-key"></i> Update Password</button>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div><!-- /content-area -->
  </div><!-- /main -->
</div><!-- /shell -->

<script>
const pageTitles = {
  dashboard:{ title:'Overview',   breadcrumb:'Dashboard → Overview' },
  bookings: { title:'Bookings',   breadcrumb:'Dashboard → Bookings' },
  users:    { title:'Users',      breadcrumb:'Dashboard → Users' },
  payments: { title:'Payments',   breadcrumb:'Dashboard → Payments' },
  courts:   { title:'Courts',     breadcrumb:'Dashboard → Courts' },
  reports:  { title:'Reports',    breadcrumb:'Dashboard → Reports' },
  settings: { title:'Settings',   breadcrumb:'Dashboard → Settings' },
};

function showPage(id, clickedEl) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  const target = document.getElementById('page-' + id);
  if (target) target.classList.add('active');

  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  if (clickedEl) {
    clickedEl.classList.add('active');
  } else {
    document.querySelectorAll('.nav-item').forEach(n => {
      if (n.getAttribute('onclick') && n.getAttribute('onclick').includes("'" + id + "'"))
        n.classList.add('active');
    });
  }

  const meta = pageTitles[id];
  if (meta) {
    document.getElementById('page-title').textContent = meta.title;
    document.getElementById('page-breadcrumb').textContent = meta.breadcrumb;
  }
  document.querySelector('.content-area').scrollTop = 0;
}

function filterTable(tbodyId, query) {
  const q = query.toLowerCase();
  document.querySelectorAll('#' + tbodyId + ' tr').forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
}

function confirmCancel() {
  if (confirm('Are you sure you want to cancel this booking?')) {
    alert('Booking cancelled successfully.');
  }
}
</script>
</body>
</html>