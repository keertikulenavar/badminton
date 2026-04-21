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
  --navy: #0a1628;
  --navy2: #0f2044;
  --blue: #1a5fd4;
  --blue2: #2d7ef0;
  --accent: #00d4aa;
  --accent2: #00b894;
  --red: #e84545;
  --orange: #ff8c42;
  --purple: #9b59b6;
  --gold: #f5c518;
  --white: #ffffff;
  --text: #e8edf5;
  --muted: #8899bb;
  --card: rgba(255,255,255,0.04);
  --card-border: rgba(255,255,255,0.08);
  --sidebar-w: 260px;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

html, body {
  height: 100%;
  font-family: 'DM Sans', sans-serif;
  background: var(--navy);
  color: var(--text);
  overflow: hidden;
}

/* ═══════════ LAYOUT ═══════════ */
.shell {
  display: flex;
  height: 100vh;
  overflow: hidden;
}

/* ═══════════ SIDEBAR ═══════════ */
.sidebar {
  width: var(--sidebar-w);
  min-width: var(--sidebar-w);
  background: var(--navy2);
  border-right: 1px solid var(--card-border);
  display: flex;
  flex-direction: column;
  height: 100vh;
  overflow: hidden;
  position: relative;
  z-index: 10;
}

.sidebar::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 3px;
  background: linear-gradient(90deg, var(--blue), var(--accent));
}

.sidebar-logo {
  padding: 28px 24px 20px;
  border-bottom: 1px solid var(--card-border);
}

.logo-badge {
  display: flex;
  align-items: center;
  gap: 12px;
}

.logo-icon {
  width: 42px; height: 42px;
  border-radius: 12px;
  background: linear-gradient(135deg, var(--blue), var(--accent));
  display: flex; align-items: center; justify-content: center;
  font-size: 20px;
  flex-shrink: 0;
}

.logo-text .brand {
  font-family: 'Syne', sans-serif;
  font-weight: 800;
  font-size: 15px;
  color: var(--white);
  letter-spacing: 0.3px;
  line-height: 1.2;
}

.logo-text .sub {
  font-size: 11px;
  color: var(--muted);
  letter-spacing: 0.5px;
}

/* Admin profile in sidebar */
.sidebar-profile {
  padding: 20px 24px;
  border-bottom: 1px solid var(--card-border);
  display: flex;
  align-items: center;
  gap: 12px;
}

.avatar {
  width: 40px; height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--blue2), var(--accent));
  display: flex; align-items: center; justify-content: center;
  font-size: 16px;
  font-weight: 700;
  color: white;
  flex-shrink: 0;
}

.profile-info .name {
  font-size: 13px;
  font-weight: 600;
  color: var(--white);
}

.profile-info .role {
  font-size: 11px;
  color: var(--accent);
  text-transform: uppercase;
  letter-spacing: 0.8px;
}

/* NAV */
.sidebar-nav {
  flex: 1;
  overflow-y: auto;
  padding: 12px 0;
  scrollbar-width: thin;
  scrollbar-color: rgba(255,255,255,0.1) transparent;
}

.nav-section {
  padding: 14px 24px 6px;
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  color: var(--muted);
  font-weight: 600;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 11px 24px;
  cursor: pointer;
  border-radius: 0;
  transition: all 0.2s;
  text-decoration: none;
  color: var(--muted);
  font-size: 14px;
  font-weight: 500;
  position: relative;
}

.nav-item:hover {
  background: rgba(255,255,255,0.05);
  color: var(--white);
}

.nav-item.active {
  background: rgba(26, 95, 212, 0.15);
  color: var(--white);
  border-right: 3px solid var(--blue2);
}

.nav-item.active::before {
  content: '';
  position: absolute;
  left: 0; top: 6px; bottom: 6px;
  width: 3px;
  background: var(--blue2);
  border-radius: 0 3px 3px 0;
}

.nav-item i {
  width: 20px;
  text-align: center;
  font-size: 15px;
}

.nav-item.active i {
  color: var(--blue2);
}

.nav-badge {
  margin-left: auto;
  background: var(--blue2);
  color: white;
  font-size: 10px;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 10px;
  min-width: 20px;
  text-align: center;
}

.nav-badge.red { background: var(--red); }
.nav-badge.accent { background: var(--accent); color: var(--navy); }

/* Sidebar bottom */
.sidebar-footer {
  padding: 16px 24px;
  border-top: 1px solid var(--card-border);
}

.logout-btn {
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
  padding: 11px 16px;
  background: rgba(232, 69, 69, 0.12);
  border: 1px solid rgba(232, 69, 69, 0.25);
  border-radius: 10px;
  color: #ff8080;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
}

.logout-btn:hover {
  background: rgba(232, 69, 69, 0.22);
  color: #ffaaaa;
}

/* ═══════════ MAIN ═══════════ */
.main {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  min-width: 0;
}

/* Topbar */
.topbar {
  padding: 18px 32px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid var(--card-border);
  background: rgba(10,22,40,0.8);
  backdrop-filter: blur(10px);
  flex-shrink: 0;
}

.topbar-left h1 {
  font-family: 'Syne', sans-serif;
  font-size: 20px;
  font-weight: 700;
  color: var(--white);
}

.topbar-left .breadcrumb {
  font-size: 12px;
  color: var(--muted);
  margin-top: 2px;
}

.topbar-right {
  display: flex;
  align-items: center;
  gap: 14px;
}

.topbar-btn {
  width: 38px; height: 38px;
  border-radius: 10px;
  border: 1px solid var(--card-border);
  background: var(--card);
  color: var(--muted);
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
  font-size: 15px;
  transition: all 0.2s;
}

.topbar-btn:hover {
  border-color: var(--blue2);
  color: var(--white);
  background: rgba(45,126,240,0.12);
}

.notification-dot {
  position: relative;
}

.notification-dot::after {
  content: '';
  position: absolute;
  top: 6px; right: 6px;
  width: 7px; height: 7px;
  background: var(--red);
  border-radius: 50%;
  border: 2px solid var(--navy);
}

/* ═══════════ CONTENT AREA ═══════════ */
.content-area {
  flex: 1;
  overflow-y: auto;
  padding: 28px 32px;
  scrollbar-width: thin;
  scrollbar-color: rgba(255,255,255,0.1) transparent;
}

.content-area::-webkit-scrollbar { width: 6px; }
.content-area::-webkit-scrollbar-track { background: transparent; }
.content-area::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }

/* ═══════════ PAGES (tab content) ═══════════ */
.page { display: none; animation: fadeIn 0.3s ease both; }
.page.active { display: block; }

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* ═══════════ STATS GRID ═══════════ */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 18px;
  margin-bottom: 28px;
}

.stat-card {
  background: var(--card);
  border: 1px solid var(--card-border);
  border-radius: 16px;
  padding: 22px 20px;
  position: relative;
  overflow: hidden;
  transition: transform 0.2s, border-color 0.2s;
}

.stat-card:hover {
  transform: translateY(-2px);
  border-color: rgba(255,255,255,0.14);
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 2px;
}

.stat-card.blue::before  { background: linear-gradient(90deg, var(--blue), var(--blue2)); }
.stat-card.green::before { background: linear-gradient(90deg, var(--accent2), var(--accent)); }
.stat-card.orange::before{ background: linear-gradient(90deg, var(--orange), #ffcc02); }
.stat-card.purple::before{ background: linear-gradient(90deg, var(--purple), #c39bd3); }

.stat-icon-wrap {
  width: 46px; height: 46px;
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 20px;
  margin-bottom: 14px;
}

.stat-card.blue   .stat-icon-wrap { background: rgba(26,95,212,0.15); color: var(--blue2); }
.stat-card.green  .stat-icon-wrap { background: rgba(0,212,170,0.12); color: var(--accent); }
.stat-card.orange .stat-icon-wrap { background: rgba(255,140,66,0.15); color: var(--orange); }
.stat-card.purple .stat-icon-wrap { background: rgba(155,89,182,0.15); color: #c39bd3; }

.stat-num {
  font-family: 'Syne', sans-serif;
  font-size: 30px;
  font-weight: 800;
  color: var(--white);
  line-height: 1;
  margin-bottom: 4px;
}

.stat-lbl {
  font-size: 11px;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 500;
}

.stat-change {
  position: absolute;
  top: 18px; right: 18px;
  font-size: 11px;
  font-weight: 600;
  padding: 3px 8px;
  border-radius: 20px;
}

.stat-change.up   { background: rgba(0,212,170,0.15); color: var(--accent); }
.stat-change.down { background: rgba(232,69,69,0.15); color: var(--red); }

/* ═══════════ TABLE CARD ═══════════ */
.table-card {
  background: var(--card);
  border: 1px solid var(--card-border);
  border-radius: 16px;
  overflow: hidden;
  margin-bottom: 24px;
}

.table-header {
  padding: 18px 22px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid var(--card-border);
}

.table-title {
  font-family: 'Syne', sans-serif;
  font-size: 15px;
  font-weight: 700;
  color: var(--white);
  display: flex;
  align-items: center;
  gap: 8px;
}

.table-title .dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  background: var(--accent);
  animation: pulse 2s ease infinite;
}

@keyframes pulse {
  0%,100% { opacity: 1; }
  50%      { opacity: 0.4; }
}

.header-actions {
  display: flex;
  gap: 8px;
}

.btn-sm {
  padding: 7px 14px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 6px;
  text-decoration: none;
}

.btn-outline {
  background: transparent;
  border: 1px solid var(--card-border);
  color: var(--muted);
}

.btn-outline:hover {
  border-color: var(--blue2);
  color: var(--white);
}

.btn-primary {
  background: var(--blue2);
  color: white;
}

.btn-primary:hover { background: var(--blue); }

.btn-accent {
  background: var(--accent);
  color: var(--navy);
}

.btn-accent:hover { background: var(--accent2); }

.btn-danger {
  background: rgba(232,69,69,0.15);
  border: 1px solid rgba(232,69,69,0.3);
  color: #ff8080;
}

.btn-danger:hover { background: rgba(232,69,69,0.25); }

/* Scrollable table wrapper */
.table-scroll {
  overflow-x: auto;
  scrollbar-width: thin;
  scrollbar-color: rgba(255,255,255,0.08) transparent;
}

table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13.5px;
}

thead tr {
  background: rgba(255,255,255,0.03);
}

th {
  padding: 12px 18px;
  text-align: left;
  font-size: 11px;
  font-weight: 700;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: 1px;
  border-bottom: 1px solid var(--card-border);
  white-space: nowrap;
}

tbody tr {
  border-bottom: 1px solid rgba(255,255,255,0.04);
  transition: background 0.15s;
}

tbody tr:last-child { border-bottom: none; }
tbody tr:hover { background: rgba(255,255,255,0.04); }

td {
  padding: 14px 18px;
  vertical-align: middle;
  color: var(--text);
}

.user-cell { display: flex; align-items: center; gap: 10px; }

.user-av {
  width: 34px; height: 34px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--blue), var(--accent));
  display: flex; align-items: center; justify-content: center;
  font-size: 13px;
  font-weight: 700;
  color: white;
  flex-shrink: 0;
}

.user-name { font-weight: 600; color: var(--white); font-size: 13px; }
.user-email { font-size: 11px; color: var(--muted); margin-top: 1px; }

.badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
}

.badge-blue   { background: rgba(45,126,240,0.15); color: #7bb3ff; }
.badge-green  { background: rgba(0,212,170,0.12); color: var(--accent); }
.badge-orange { background: rgba(255,140,66,0.15); color: var(--orange); }
.badge-red    { background: rgba(232,69,69,0.15); color: #ff8080; }
.badge-purple { background: rgba(155,89,182,0.15); color: #c39bd3; }

/* ═══════════ SEARCH BAR ═══════════ */
.search-row {
  padding: 14px 18px;
  border-bottom: 1px solid var(--card-border);
  display: flex;
  align-items: center;
  gap: 10px;
}

.search-input {
  flex: 1;
  background: rgba(255,255,255,0.05);
  border: 1px solid var(--card-border);
  border-radius: 8px;
  padding: 9px 14px 9px 36px;
  color: var(--text);
  font-size: 13px;
  font-family: 'DM Sans', sans-serif;
  outline: none;
  transition: border 0.2s;
}

.search-input::placeholder { color: var(--muted); }
.search-input:focus { border-color: var(--blue2); }

.search-icon {
  position: relative;
}

.search-wrap {
  flex: 1;
  position: relative;
}

.search-wrap i {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--muted);
  font-size: 13px;
}

/* ═══════════ FORM STYLES ═══════════ */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 18px;
  padding: 22px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 7px;
}

.form-group.full { grid-column: span 2; }

.form-label {
  font-size: 11px;
  font-weight: 700;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: 0.8px;
}

.form-input, .form-select {
  background: rgba(255,255,255,0.05);
  border: 1px solid var(--card-border);
  border-radius: 9px;
  padding: 11px 14px;
  color: var(--text);
  font-size: 14px;
  font-family: 'DM Sans', sans-serif;
  outline: none;
  transition: border 0.2s, box-shadow 0.2s;
}

.form-input:focus, .form-select:focus {
  border-color: var(--blue2);
  box-shadow: 0 0 0 3px rgba(45,126,240,0.12);
}

.form-select option { background: var(--navy2); }

.form-footer {
  padding: 16px 22px;
  border-top: 1px solid var(--card-border);
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}

.btn {
  padding: 10px 22px;
  border-radius: 9px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 7px;
  font-family: 'DM Sans', sans-serif;
}

/* ═══════════ ACTIVITY FEED ═══════════ */
.activity-item {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  padding: 14px 18px;
  border-bottom: 1px solid rgba(255,255,255,0.04);
  transition: background 0.15s;
}

.activity-item:hover { background: rgba(255,255,255,0.03); }
.activity-item:last-child { border-bottom: none; }

.activity-icon {
  width: 36px; height: 36px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 14px;
  flex-shrink: 0;
}

.act-blue   { background: rgba(45,126,240,0.15); color: var(--blue2); }
.act-green  { background: rgba(0,212,170,0.12); color: var(--accent); }
.act-orange { background: rgba(255,140,66,0.15); color: var(--orange); }
.act-red    { background: rgba(232,69,69,0.15); color: var(--red); }

.activity-text {
  flex: 1;
  font-size: 13px;
  color: var(--text);
  line-height: 1.4;
}

.activity-text strong { color: var(--white); }
.activity-time { font-size: 11px; color: var(--muted); margin-top: 3px; }

/* ═══════════ SUMMARY ROW ═══════════ */
.summary-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 18px;
  margin-bottom: 28px;
}

/* ═══════════ PAGE HEADER ═══════════ */
.page-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 22px;
}

.page-head-title {
  font-family: 'Syne', sans-serif;
  font-size: 22px;
  font-weight: 800;
  color: var(--white);
}

.page-head-sub {
  font-size: 13px;
  color: var(--muted);
  margin-top: 2px;
}

/* ═══════════ REVENUE CHART (CSS) ═══════════ */
.chart-wrap {
  padding: 22px;
}

.chart-bars {
  display: flex;
  align-items: flex-end;
  gap: 8px;
  height: 140px;
  margin-bottom: 8px;
}

.bar-col {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  height: 100%;
  justify-content: flex-end;
}

.bar {
  width: 100%;
  border-radius: 6px 6px 0 0;
  background: linear-gradient(180deg, var(--blue2), var(--blue));
  transition: opacity 0.2s;
  min-height: 4px;
}

.bar:hover { opacity: 0.75; }

.bar.accent { background: linear-gradient(180deg, var(--accent), var(--accent2)); }

.bar-label {
  font-size: 10px;
  color: var(--muted);
  text-align: center;
}

/* ═══════════ EMPTY STATE ═══════════ */
.empty-state {
  text-align: center;
  padding: 52px 20px;
}

.empty-icon { font-size: 44px; margin-bottom: 14px; }
.empty-text { font-size: 15px; color: var(--muted); margin-bottom: 18px; }

/* ═══════════ STATUS PILL IN TABLE ═══════════ */
.status-dot {
  width: 7px; height: 7px;
  border-radius: 50%;
  display: inline-block;
  margin-right: 5px;
}

/* ═══════════ SCROLLBAR GLOBAL ═══════════ */
.sidebar-nav::-webkit-scrollbar { width: 4px; }
.sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 2px; }

/* ═══════════ RESPONSIVE GRID FALLBACK ═══════════ */
@media(max-width: 1100px) {
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
}

@media(max-width: 800px) {
  .sidebar { width: 60px; min-width: 60px; }
  .logo-text, .profile-info, .nav-item span, .sidebar-footer span { display: none; }
  .nav-badge { display: none; }
  .nav-section { display: none; }
}
</style>
</head>
<body>

<div class="shell">

  <!-- ═══════ SIDEBAR ═══════ -->
  <aside class="sidebar">
    <div class="sidebar::before"></div>

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

      <a class="nav-item active" onclick="showPage('dashboard', this)">
        <i class="fas fa-th-large"></i>
        <span>Dashboard</span>
      </a>

      <a class="nav-item" onclick="showPage('bookings', this)">
        <i class="fas fa-calendar-check"></i>
        <span>Bookings</span>
        <span class="nav-badge accent">12</span>
      </a>

      <a class="nav-item" onclick="showPage('users', this)">
        <i class="fas fa-users"></i>
        <span>Users</span>
        <span class="nav-badge">48</span>
      </a>

      <a class="nav-item" onclick="showPage('payments', this)">
        <i class="fas fa-credit-card"></i>
        <span>Payments</span>
      </a>

      <div class="nav-section">Management</div>

      <a class="nav-item" onclick="showPage('courts', this)">
        <i class="fas fa-map-marker-alt"></i>
        <span>Courts</span>
      </a>

      <a class="nav-item" onclick="showPage('reports', this)">
        <i class="fas fa-chart-bar"></i>
        <span>Reports</span>
      </a>

      <a class="nav-item" onclick="showPage('settings', this)">
        <i class="fas fa-cog"></i>
        <span>Settings</span>
      </a>
    </nav>

    <div class="sidebar-footer">
      <a href="admin_login.html" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </a>
    </div>
  </aside>

  <!-- ═══════ MAIN ═══════ -->
  <div class="main">

    <!-- Topbar -->
    <div class="topbar">
      <div class="topbar-left">
        <h1 id="page-title">Overview</h1>
        <div class="breadcrumb" id="page-breadcrumb">Dashboard → Overview</div>
      </div>
      <div class="topbar-right">
        <div class="topbar-btn notification-dot" title="Notifications">
          <i class="fas fa-bell"></i>
        </div>
        <div class="topbar-btn" title="Refresh">
          <i class="fas fa-sync-alt"></i>
        </div>
        <div class="topbar-btn" title="Settings" onclick="showPage('settings', null)">
          <i class="fas fa-cog"></i>
        </div>
      </div>
    </div>

    <!-- ═══════ CONTENT AREA ═══════ -->
    <div class="content-area">

      <!-- ════ PAGE: DASHBOARD ════ -->
      <div class="page active" id="page-dashboard">

        <div class="page-head">
          <div>
            <div class="page-head-title">Welcome back, Admin 👋</div>
            <div class="page-head-sub">Here's what's happening with your courts today.</div>
          </div>
          <div style="display:flex;gap:8px;">
            <button class="btn btn-outline btn-sm" onclick="showPage('reports', null)"><i class="fas fa-chart-bar"></i> Reports</button>
            <button class="btn btn-primary btn-sm" onclick="showPage('bookings', null)"><i class="fas fa-plus"></i> New Booking</button>
          </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
          <div class="stat-card blue">
            <div class="stat-change up">↑ 12%</div>
            <div class="stat-icon-wrap"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-num">247</div>
            <div class="stat-lbl">Total Bookings</div>
          </div>
          <div class="stat-card green">
            <div class="stat-change up">↑ 8%</div>
            <div class="stat-icon-wrap"><i class="fas fa-users"></i></div>
            <div class="stat-num">48</div>
            <div class="stat-lbl">Registered Users</div>
          </div>
          <div class="stat-card orange">
            <div class="stat-change up">↑ 21%</div>
            <div class="stat-icon-wrap"><i class="fas fa-rupee-sign"></i></div>
            <div class="stat-num">₹49,400</div>
            <div class="stat-lbl">Total Revenue</div>
          </div>
          <div class="stat-card purple">
            <div class="stat-change down">↓ 3%</div>
            <div class="stat-icon-wrap"><i class="fas fa-calendar-day"></i></div>
            <div class="stat-num">7</div>
            <div class="stat-lbl">Today's Bookings</div>
          </div>
        </div>

        <!-- Summary row -->
        <div class="summary-row">

          <!-- Revenue chart -->
          <div class="table-card">
            <div class="table-header">
              <div class="table-title">
                <span class="dot"></span>
                Weekly Revenue
              </div>
              <span class="badge badge-green">This week</span>
            </div>
            <div class="chart-wrap">
              <div class="chart-bars">
                <div class="bar-col"><div class="bar" style="height:55%"></div><div class="bar-label">Mon</div></div>
                <div class="bar-col"><div class="bar" style="height:80%"></div><div class="bar-label">Tue</div></div>
                <div class="bar-col"><div class="bar accent" style="height:100%"></div><div class="bar-label">Wed</div></div>
                <div class="bar-col"><div class="bar" style="height:65%"></div><div class="bar-label">Thu</div></div>
                <div class="bar-col"><div class="bar" style="height:70%"></div><div class="bar-label">Fri</div></div>
                <div class="bar-col"><div class="bar" style="height:45%"></div><div class="bar-label">Sat</div></div>
                <div class="bar-col"><div class="bar" style="height:30%"></div><div class="bar-label">Sun</div></div>
              </div>
              <div style="display:flex;justify-content:space-between;padding-top:6px;">
                <span style="font-size:11px;color:var(--muted)">₹0</span>
                <span style="font-size:12px;color:var(--white);font-weight:600">Total: ₹9,800</span>
                <span style="font-size:11px;color:var(--muted)">₹2,800</span>
              </div>
            </div>
          </div>

          <!-- Activity feed -->
          <div class="table-card">
            <div class="table-header">
              <div class="table-title">
                <span class="dot"></span>
                Recent Activity
              </div>
              <button class="btn-sm btn-outline" onclick="showPage('bookings', null)">View All</button>
            </div>
            <div class="activity-item">
              <div class="activity-icon act-green"><i class="fas fa-check"></i></div>
              <div class="activity-text">
                <strong>Ravi Kumar</strong> booked Court 1<br>
                <span class="activity-time">5 min ago · 6 PM – 7 PM</span>
              </div>
            </div>
            <div class="activity-item">
              <div class="activity-icon act-blue"><i class="fas fa-user-plus"></i></div>
              <div class="activity-text">
                <strong>Meena Patel</strong> registered as new user<br>
                <span class="activity-time">18 min ago</span>
              </div>
            </div>
            <div class="activity-item">
              <div class="activity-icon act-orange"><i class="fas fa-rupee-sign"></i></div>
              <div class="activity-text">
                <strong>₹200</strong> payment received from Arjun S.<br>
                <span class="activity-time">32 min ago · TXN #78421</span>
              </div>
            </div>
            <div class="activity-item">
              <div class="activity-icon act-red"><i class="fas fa-times"></i></div>
              <div class="activity-text">
                <strong>Priya Rao</strong> cancelled booking<br>
                <span class="activity-time">1 hr ago · 8 AM – 9 AM</span>
              </div>
            </div>
            <div class="activity-item">
              <div class="activity-icon act-green"><i class="fas fa-check"></i></div>
              <div class="activity-text">
                <strong>Karan Singh</strong> booked Court 1<br>
                <span class="activity-time">2 hr ago · 7 AM – 8 AM</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Bookings Table -->
        <div class="table-card">
          <div class="table-header">
            <div class="table-title">
              <span class="dot"></span>
              Recent Bookings
            </div>
            <div class="header-actions">
              <span style="font-size:12px;color:var(--muted);align-self:center;">Latest 10</span>
              <button class="btn-sm btn-primary" onclick="showPage('bookings', null)">
                <i class="fas fa-arrow-right"></i> All Bookings
              </button>
            </div>
          </div>
          <div class="table-scroll">
            <table>
              <thead>
                <tr>
                  <th>#ID</th>
                  <th>User</th>
                  <th>Date</th>
                  <th>Time Slot</th>
                  <th>Status</th>
                  <th>Amount</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td style="color:var(--muted);font-size:12px;">#1047</td>
                  <td><div class="user-cell"><div class="user-av">R</div><div><div class="user-name">Ravi Kumar</div><div class="user-email">ravi@gmail.com</div></div></div></td>
                  <td>20 Apr 2026</td>
                  <td><span class="badge badge-blue">6 PM – 7 PM</span></td>
                  <td><span class="badge badge-green"><span class="status-dot" style="background:var(--accent)"></span>Confirmed</span></td>
                  <td style="color:var(--accent);font-weight:600;">₹200</td>
                </tr>
                <tr>
                  <td style="color:var(--muted);font-size:12px;">#1046</td>
                  <td><div class="user-cell"><div class="user-av" style="background:linear-gradient(135deg,#e67e22,#f39c12)">A</div><div><div class="user-name">Arjun Sharma</div><div class="user-email">arjun@gmail.com</div></div></div></td>
                  <td>20 Apr 2026</td>
                  <td><span class="badge badge-blue">7 PM – 8 PM</span></td>
                  <td><span class="badge badge-green"><span class="status-dot" style="background:var(--accent)"></span>Confirmed</span></td>
                  <td style="color:var(--accent);font-weight:600;">₹200</td>
                </tr>
                <tr>
                  <td style="color:var(--muted);font-size:12px;">#1045</td>
                  <td><div class="user-cell"><div class="user-av" style="background:linear-gradient(135deg,#8e44ad,#9b59b6)">P</div><div><div class="user-name">Priya Rao</div><div class="user-email">priya@gmail.com</div></div></div></td>
                  <td>19 Apr 2026</td>
                  <td><span class="badge badge-orange">8 AM – 9 AM</span></td>
                  <td><span class="badge badge-red"><span class="status-dot" style="background:var(--red)"></span>Cancelled</span></td>
                  <td style="color:var(--muted);">—</td>
                </tr>
                <tr>
                  <td style="color:var(--muted);font-size:12px;">#1044</td>
                  <td><div class="user-cell"><div class="user-av" style="background:linear-gradient(135deg,#27ae60,#2ecc71)">K</div><div><div class="user-name">Karan Singh</div><div class="user-email">karan@gmail.com</div></div></div></td>
                  <td>19 Apr 2026</td>
                  <td><span class="badge badge-blue">7 AM – 8 AM</span></td>
                  <td><span class="badge badge-green"><span class="status-dot" style="background:var(--accent)"></span>Confirmed</span></td>
                  <td style="color:var(--accent);font-weight:600;">₹200</td>
                </tr>
                <tr>
                  <td style="color:var(--muted);font-size:12px;">#1043</td>
                  <td><div class="user-cell"><div class="user-av" style="background:linear-gradient(135deg,#e74c3c,#c0392b)">M</div><div><div class="user-name">Meena Patel</div><div class="user-email">meena@gmail.com</div></div></div></td>
                  <td>18 Apr 2026</td>
                  <td><span class="badge badge-blue">5 PM – 6 PM</span></td>
                  <td><span class="badge badge-green"><span class="status-dot" style="background:var(--accent)"></span>Confirmed</span></td>
                  <td style="color:var(--accent);font-weight:600;">₹200</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ════ PAGE: BOOKINGS ════ -->
      <div class="page" id="page-bookings">
        <div class="page-head">
          <div>
            <div class="page-head-title">All Bookings</div>
            <div class="page-head-sub">Manage all court reservations</div>
          </div>
          <div style="display:flex;gap:8px;">
            <button class="btn btn-outline btn-sm"><i class="fas fa-download"></i> Export</button>
            <button class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Booking</button>
          </div>
        </div>

        <div class="stats-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px;">
          <div class="stat-card blue" style="padding:16px 18px;">
            <div class="stat-icon-wrap" style="width:38px;height:38px;font-size:16px;margin-bottom:10px;"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-num" style="font-size:24px;">247</div>
            <div class="stat-lbl">Total</div>
          </div>
          <div class="stat-card green" style="padding:16px 18px;">
            <div class="stat-icon-wrap" style="width:38px;height:38px;font-size:16px;margin-bottom:10px;"><i class="fas fa-check-circle"></i></div>
            <div class="stat-num" style="font-size:24px;">231</div>
            <div class="stat-lbl">Confirmed</div>
          </div>
          <div class="stat-card" style="padding:16px 18px;border-color:rgba(232,69,69,0.2);">
            <div style="position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,var(--red),#ff6b6b);"></div>
            <div class="stat-icon-wrap" style="width:38px;height:38px;font-size:16px;margin-bottom:10px;background:rgba(232,69,69,0.12);color:var(--red);"><i class="fas fa-times-circle"></i></div>
            <div class="stat-num" style="font-size:24px;">16</div>
            <div class="stat-lbl">Cancelled</div>
          </div>
        </div>

        <div class="table-card">
          <div class="search-row">
            <div class="search-wrap">
              <i class="fas fa-search"></i>
              <input class="search-input" type="text" placeholder="Search bookings by name, date, time..." oninput="filterTable('bookings-tbody', this.value)">
            </div>
            <select class="form-select" style="width:140px;padding:9px 12px;font-size:13px;">
              <option>All Status</option>
              <option>Confirmed</option>
              <option>Cancelled</option>
            </select>
          </div>
          <div class="table-scroll">
            <table>
              <thead>
                <tr>
                  <th>#ID</th>
                  <th>User</th>
                  <th>Date</th>
                  <th>Time Slot</th>
                  <th>Court</th>
                  <th>Status</th>
                  <th>Payment</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="bookings-tbody">
                <tr>
                  <td style="color:var(--muted);font-size:12px;">#1047</td>
                  <td><div class="user-cell"><div class="user-av">R</div><div><div class="user-name">Ravi Kumar</div><div class="user-email">ravi@gmail.com</div></div></div></td>
                  <td>20 Apr 2026</td>
                  <td><span class="badge badge-blue">6 PM – 7 PM</span></td>
                  <td>Court 1</td>
                  <td><span class="badge badge-green">Confirmed</span></td>
                  <td><span class="badge badge-green">Paid ₹200</span></td>
                  <td><button class="btn-sm btn-danger" onclick="confirmCancel()"><i class="fas fa-trash"></i></button></td>
                </tr>
                <tr>
                  <td style="color:var(--muted);font-size:12px;">#1046</td>
                  <td><div class="user-cell"><div class="user-av" style="background:linear-gradient(135deg,#e67e22,#f39c12)">A</div><div><div class="user-name">Arjun Sharma</div><div class="user-email">arjun@gmail.com</div></div></div></td>
                  <td>20 Apr 2026</td>
                  <td><span class="badge badge-blue">7 PM – 8 PM</span></td>
                  <td>Court 1</td>
                  <td><span class="badge badge-green">Confirmed</span></td>
                  <td><span class="badge badge-green">Paid ₹200</span></td>
                  <td><button class="btn-sm btn-danger" onclick="confirmCancel()"><i class="fas fa-trash"></i></button></td>
                </tr>
                <tr>
                  <td style="color:var(--muted);font-size:12px;">#1045</td>
                  <td><div class="user-cell"><div class="user-av" style="background:linear-gradient(135deg,#8e44ad,#9b59b6)">P</div><div><div class="user-name">Priya Rao</div><div class="user-email">priya@gmail.com</div></div></div></td>
                  <td>19 Apr 2026</td>
                  <td><span class="badge badge-orange">8 AM – 9 AM</span></td>
                  <td>Court 1</td>
                  <td><span class="badge badge-red">Cancelled</span></td>
                  <td><span class="badge" style="color:var(--muted);">—</span></td>
                  <td><button class="btn-sm btn-outline" style="cursor:default;opacity:0.4;" disabled>—</button></td>
                </tr>
                <tr>
                  <td style="color:var(--muted);font-size:12px;">#1044</td>
                  <td><div class="user-cell"><div class="user-av" style="background:linear-gradient(135deg,#27ae60,#2ecc71)">K</div><div><div class="user-name">Karan Singh</div><div class="user-email">karan@gmail.com</div></div></div></td>
                  <td>19 Apr 2026</td>
                  <td><span class="badge badge-blue">7 AM – 8 AM</span></td>
                  <td>Court 1</td>
                  <td><span class="badge badge-green">Confirmed</span></td>
                  <td><span class="badge badge-green">Paid ₹200</span></td>
                  <td><button class="btn-sm btn-danger" onclick="confirmCancel()"><i class="fas fa-trash"></i></button></td>
                </tr>
                <tr>
                  <td style="color:var(--muted);font-size:12px;">#1043</td>
                  <td><div class="user-cell"><div class="user-av" style="background:linear-gradient(135deg,#e74c3c,#c0392b)">M</div><div><div class="user-name">Meena Patel</div><div class="user-email">meena@gmail.com</div></div></div></td>
                  <td>18 Apr 2026</td>
                  <td><span class="badge badge-blue">5 PM – 6 PM</span></td>
                  <td>Court 1</td>
                  <td><span class="badge badge-green">Confirmed</span></td>
                  <td><span class="badge badge-green">Paid ₹200</span></td>
                  <td><button class="btn-sm btn-danger" onclick="confirmCancel()"><i class="fas fa-trash"></i></button></td>
                </tr>
                <tr>
                  <td style="color:var(--muted);font-size:12px;">#1042</td>
                  <td><div class="user-cell"><div class="user-av" style="background:linear-gradient(135deg,#2980b9,#3498db)">S</div><div><div class="user-name">Sunita Verma</div><div class="user-email">sunita@gmail.com</div></div></div></td>
                  <td>18 Apr 2026</td>
                  <td><span class="badge badge-blue">6 AM – 7 AM</span></td>
                  <td>Court 1</td>
                  <td><span class="badge badge-green">Confirmed</span></td>
                  <td><span class="badge badge-green">Paid ₹200</span></td>
                  <td><button class="btn-sm btn-danger" onclick="confirmCancel()"><i class="fas fa-trash"></i></button></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ════ PAGE: USERS ════ -->
      <div class="page" id="page-users">
        <div class="page-head">
          <div>
            <div class="page-head-title">Users</div>
            <div class="page-head-sub">All registered members</div>
          </div>
          <button class="btn btn-primary btn-sm"><i class="fas fa-user-plus"></i> Add User</button>
        </div>

        <div class="table-card">
          <div class="search-row">
            <div class="search-wrap">
              <i class="fas fa-search"></i>
              <input class="search-input" type="text" placeholder="Search users by name or email..." oninput="filterTable('users-tbody', this.value)">
            </div>
          </div>
          <div class="table-scroll">
            <table>
              <thead>
                <tr>
                  <th>#</th>
                  <th>User</th>
                  <th>Contact</th>
                  <th>Bookings</th>
                  <th>Total Spent</th>
                  <th>Joined</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="users-tbody">
                <tr>
                  <td style="color:var(--muted);font-size:12px;">01</td>
                  <td><div class="user-cell"><div class="user-av">R</div><div><div class="user-name">Ravi Kumar</div><div class="user-email">ravi@gmail.com</div></div></div></td>
                  <td style="color:var(--muted);">9876543210</td>
                  <td><span class="badge badge-blue">12 bookings</span></td>
                  <td style="color:var(--accent);font-weight:600;">₹2,400</td>
                  <td style="color:var(--muted);font-size:12px;">Jan 2026</td>
                  <td><button class="btn-sm btn-danger"><i class="fas fa-trash"></i></button></td>
                </tr>
                <tr>
                  <td style="color:var(--muted);font-size:12px;">02</td>
                  <td><div class="user-cell"><div class="user-av" style="background:linear-gradient(135deg,#e67e22,#f39c12)">A</div><div><div class="user-name">Arjun Sharma</div><div class="user-email">arjun@gmail.com</div></div></div></td>
                  <td style="color:var(--muted);">9123456789</td>
                  <td><span class="badge badge-blue">8 bookings</span></td>
                  <td style="color:var(--accent);font-weight:600;">₹1,600</td>
                  <td style="color:var(--muted);font-size:12px;">Feb 2026</td>
                  <td><button class="btn-sm btn-danger"><i class="fas fa-trash"></i></button></td>
                </tr>
                <tr>
                  <td style="color:var(--muted);font-size:12px;">03</td>
                  <td><div class="user-cell"><div class="user-av" style="background:linear-gradient(135deg,#8e44ad,#9b59b6)">P</div><div><div class="user-name">Priya Rao</div><div class="user-email">priya@gmail.com</div></div></div></td>
                  <td style="color:var(--muted);">9988776655</td>
                  <td><span class="badge badge-blue">5 bookings</span></td>
                  <td style="color:var(--accent);font-weight:600;">₹800</td>
                  <td style="color:var(--muted);font-size:12px;">Mar 2026</td>
                  <td><button class="btn-sm btn-danger"><i class="fas fa-trash"></i></button></td>
                </tr>
                <tr>
                  <td style="color:var(--muted);font-size:12px;">04</td>
                  <td><div class="user-cell"><div class="user-av" style="background:linear-gradient(135deg,#27ae60,#2ecc71)">K</div><div><div class="user-name">Karan Singh</div><div class="user-email">karan@gmail.com</div></div></div></td>
                  <td style="color:var(--muted);">9765432108</td>
                  <td><span class="badge badge-blue">15 bookings</span></td>
                  <td style="color:var(--accent);font-weight:600;">₹3,000</td>
                  <td style="color:var(--muted);font-size:12px;">Dec 2025</td>
                  <td><button class="btn-sm btn-danger"><i class="fas fa-trash"></i></button></td>
                </tr>
                <tr>
                  <td style="color:var(--muted);font-size:12px;">05</td>
                  <td><div class="user-cell"><div class="user-av" style="background:linear-gradient(135deg,#e74c3c,#c0392b)">M</div><div><div class="user-name">Meena Patel</div><div class="user-email">meena@gmail.com</div></div></div></td>
                  <td style="color:var(--muted);">9654321087</td>
                  <td><span class="badge badge-blue">3 bookings</span></td>
                  <td style="color:var(--accent);font-weight:600;">₹600</td>
                  <td style="color:var(--muted);font-size:12px;">Apr 2026</td>
                  <td><button class="btn-sm btn-danger"><i class="fas fa-trash"></i></button></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ════ PAGE: PAYMENTS ════ -->
      <div class="page" id="page-payments">
        <div class="page-head">
          <div>
            <div class="page-head-title">Payments</div>
            <div class="page-head-sub">Transaction history & revenue tracking</div>
          </div>
          <button class="btn btn-outline btn-sm"><i class="fas fa-download"></i> Export CSV</button>
        </div>

        <div class="stats-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px;">
          <div class="stat-card green" style="padding:16px 18px;">
            <div class="stat-icon-wrap" style="width:38px;height:38px;font-size:16px;margin-bottom:10px;"><i class="fas fa-rupee-sign"></i></div>
            <div class="stat-num" style="font-size:24px;">₹49,400</div>
            <div class="stat-lbl">Total Collected</div>
          </div>
          <div class="stat-card blue" style="padding:16px 18px;">
            <div class="stat-icon-wrap" style="width:38px;height:38px;font-size:16px;margin-bottom:10px;"><i class="fas fa-receipt"></i></div>
            <div class="stat-num" style="font-size:24px;">247</div>
            <div class="stat-lbl">Transactions</div>
          </div>
          <div class="stat-card orange" style="padding:16px 18px;">
            <div class="stat-icon-wrap" style="width:38px;height:38px;font-size:16px;margin-bottom:10px;"><i class="fas fa-calendar-alt"></i></div>
            <div class="stat-num" style="font-size:24px;">₹1,400</div>
            <div class="stat-lbl">Today's Revenue</div>
          </div>
        </div>

        <div class="table-card">
          <div class="search-row">
            <div class="search-wrap">
              <i class="fas fa-search"></i>
              <input class="search-input" type="text" placeholder="Search by transaction ID or user..." oninput="filterTable('payments-tbody', this.value)">
            </div>
          </div>
          <div class="table-scroll">
            <table>
              <thead>
                <tr>
                  <th>Pay ID</th>
                  <th>Booking ID</th>
                  <th>User</th>
                  <th>Amount</th>
                  <th>TXN ID</th>
                  <th>Status</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody id="payments-tbody">
                <tr>
                  <td><span class="badge badge-purple">#P201</span></td>
                  <td><span class="badge badge-blue">#1047</span></td>
                  <td><div class="user-name">Ravi Kumar</div></td>
                  <td style="color:var(--accent);font-weight:600;">₹200</td>
                  <td style="font-size:12px;color:var(--muted);">TXN78421</td>
                  <td><span class="badge badge-green">Paid</span></td>
                  <td style="font-size:12px;color:var(--muted);">20 Apr 2026</td>
                </tr>
                <tr>
                  <td><span class="badge badge-purple">#P200</span></td>
                  <td><span class="badge badge-blue">#1046</span></td>
                  <td><div class="user-name">Arjun Sharma</div></td>
                  <td style="color:var(--accent);font-weight:600;">₹200</td>
                  <td style="font-size:12px;color:var(--muted);">TXN78420</td>
                  <td><span class="badge badge-green">Paid</span></td>
                  <td style="font-size:12px;color:var(--muted);">20 Apr 2026</td>
                </tr>
                <tr>
                  <td><span class="badge badge-purple">#P199</span></td>
                  <td><span class="badge badge-blue">#1044</span></td>
                  <td><div class="user-name">Karan Singh</div></td>
                  <td style="color:var(--accent);font-weight:600;">₹200</td>
                  <td style="font-size:12px;color:var(--muted);">TXN78419</td>
                  <td><span class="badge badge-green">Paid</span></td>
                  <td style="font-size:12px;color:var(--muted);">19 Apr 2026</td>
                </tr>
                <tr>
                  <td><span class="badge badge-purple">#P198</span></td>
                  <td><span class="badge badge-blue">#1043</span></td>
                  <td><div class="user-name">Meena Patel</div></td>
                  <td style="color:var(--accent);font-weight:600;">₹200</td>
                  <td style="font-size:12px;color:var(--muted);">TXN78418</td>
                  <td><span class="badge badge-green">Paid</span></td>
                  <td style="font-size:12px;color:var(--muted);">18 Apr 2026</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ════ PAGE: COURTS ════ -->
      <div class="page" id="page-courts">
        <div class="page-head">
          <div>
            <div class="page-head-title">Courts</div>
            <div class="page-head-sub">Manage court details & pricing</div>
          </div>
        </div>

        <div class="summary-row">
          <div class="table-card">
            <div class="table-header">
              <div class="table-title"><span class="dot"></span> Court Details</div>
            </div>
            <div style="padding:22px;">
              <div style="display:flex;align-items:center;gap:18px;padding:18px;background:rgba(255,255,255,0.04);border-radius:12px;border:1px solid var(--card-border);margin-bottom:14px;">
                <div style="width:52px;height:52px;background:linear-gradient(135deg,var(--blue),var(--accent));border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0;">🏸</div>
                <div>
                  <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:16px;color:var(--white);">Court 1</div>
                  <div style="font-size:12px;color:var(--muted);margin-top:3px;">Indoor Badminton Court · Standard</div>
                </div>
                <div style="margin-left:auto;"><span class="badge badge-green">Active</span></div>
              </div>

              <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div style="padding:14px;background:rgba(255,255,255,0.03);border-radius:10px;border:1px solid var(--card-border);">
                  <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:6px;">Rate</div>
                  <div style="font-size:22px;font-weight:700;color:var(--accent);font-family:'Syne',sans-serif;">₹200/hr</div>
                </div>
                <div style="padding:14px;background:rgba(255,255,255,0.03);border-radius:10px;border:1px solid var(--card-border);">
                  <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:6px;">Available Slots</div>
                  <div style="font-size:22px;font-weight:700;color:var(--blue2);font-family:'Syne',sans-serif;">7 / day</div>
                </div>
                <div style="padding:14px;background:rgba(255,255,255,0.03);border-radius:10px;border:1px solid var(--card-border);">
                  <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:6px;">Opening</div>
                  <div style="font-size:15px;font-weight:600;color:var(--white);">6 AM</div>
                </div>
                <div style="padding:14px;background:rgba(255,255,255,0.03);border-radius:10px;border:1px solid var(--card-border);">
                  <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:6px;">Closing</div>
                  <div style="font-size:15px;font-weight:600;color:var(--white);">9 PM</div>
                </div>
              </div>
            </div>
          </div>

          <div class="table-card">
            <div class="table-header">
              <div class="table-title"><span class="dot"></span> Today's Slot Status</div>
            </div>
            <div style="padding:18px;display:grid;gap:8px;">
              <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 14px;border-radius:9px;border:1px solid var(--card-border);background:rgba(0,212,170,0.06);">
                <span style="font-size:13px;color:var(--white);">6 AM – 7 AM</span><span class="badge badge-green">Available</span>
              </div>
              <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 14px;border-radius:9px;border:1px solid rgba(232,69,69,0.3);background:rgba(232,69,69,0.06);">
                <span style="font-size:13px;color:var(--white);">7 AM – 8 AM</span><span class="badge badge-red">Booked</span>
              </div>
              <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 14px;border-radius:9px;border:1px solid rgba(232,69,69,0.3);background:rgba(232,69,69,0.06);">
                <span style="font-size:13px;color:var(--white);">8 AM – 9 AM</span><span class="badge badge-red">Booked</span>
              </div>
              <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 14px;border-radius:9px;border:1px solid var(--card-border);background:rgba(0,212,170,0.06);">
                <span style="font-size:13px;color:var(--white);">5 PM – 6 PM</span><span class="badge badge-green">Available</span>
              </div>
              <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 14px;border-radius:9px;border:1px solid rgba(232,69,69,0.3);background:rgba(232,69,69,0.06);">
                <span style="font-size:13px;color:var(--white);">6 PM – 7 PM</span><span class="badge badge-red">Booked</span>
              </div>
              <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 14px;border-radius:9px;border:1px solid rgba(232,69,69,0.3);background:rgba(232,69,69,0.06);">
                <span style="font-size:13px;color:var(--white);">7 PM – 8 PM</span><span class="badge badge-red">Booked</span>
              </div>
              <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 14px;border-radius:9px;border:1px solid var(--card-border);background:rgba(0,212,170,0.06);">
                <span style="font-size:13px;color:var(--white);">8 PM – 9 PM</span><span class="badge badge-green">Available</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ════ PAGE: REPORTS ════ -->
      <div class="page" id="page-reports">
        <div class="page-head">
          <div>
            <div class="page-head-title">Reports & Analytics</div>
            <div class="page-head-sub">Revenue and booking performance overview</div>
          </div>
          <button class="btn btn-outline btn-sm"><i class="fas fa-download"></i> Download PDF</button>
        </div>

        <div class="stats-grid">
          <div class="stat-card blue">
            <div class="stat-icon-wrap"><i class="fas fa-chart-line"></i></div>
            <div class="stat-num">₹49,400</div>
            <div class="stat-lbl">Lifetime Revenue</div>
          </div>
          <div class="stat-card green">
            <div class="stat-icon-wrap"><i class="fas fa-calendar-week"></i></div>
            <div class="stat-num">₹9,800</div>
            <div class="stat-lbl">This Week</div>
          </div>
          <div class="stat-card orange">
            <div class="stat-icon-wrap"><i class="fas fa-calendar-alt"></i></div>
            <div class="stat-num">₹38,400</div>
            <div class="stat-lbl">This Month</div>
          </div>
          <div class="stat-card purple">
            <div class="stat-icon-wrap"><i class="fas fa-percentage"></i></div>
            <div class="stat-num">93.6%</div>
            <div class="stat-lbl">Booking Rate</div>
          </div>
        </div>

        <div class="summary-row">
          <div class="table-card">
            <div class="table-header">
              <div class="table-title"><span class="dot"></span> Monthly Revenue</div>
            </div>
            <div class="chart-wrap">
              <div class="chart-bars" style="height:160px;">
                <div class="bar-col"><div class="bar" style="height:60%"></div><div class="bar-label">Oct</div></div>
                <div class="bar-col"><div class="bar" style="height:75%"></div><div class="bar-label">Nov</div></div>
                <div class="bar-col"><div class="bar" style="height:85%"></div><div class="bar-label">Dec</div></div>
                <div class="bar-col"><div class="bar accent" style="height:100%"></div><div class="bar-label">Jan</div></div>
                <div class="bar-col"><div class="bar" style="height:90%"></div><div class="bar-label">Feb</div></div>
                <div class="bar-col"><div class="bar" style="height:78%"></div><div class="bar-label">Mar</div></div>
                <div class="bar-col"><div class="bar" style="height:65%"></div><div class="bar-label">Apr</div></div>
              </div>
              <div style="text-align:center;font-size:12px;color:var(--muted);">Revenue trend — last 7 months</div>
            </div>
          </div>

          <div class="table-card">
            <div class="table-header">
              <div class="table-title"><span class="dot"></span> Peak Hours</div>
            </div>
            <div style="padding:18px;display:grid;gap:10px;">
              <div style="display:flex;align-items:center;gap:12px;">
                <span style="font-size:12px;color:var(--muted);width:80px;">6 PM–7 PM</span>
                <div style="flex:1;height:8px;background:rgba(255,255,255,0.07);border-radius:4px;overflow:hidden;">
                  <div style="width:92%;height:100%;background:linear-gradient(90deg,var(--accent),var(--blue2));border-radius:4px;"></div>
                </div>
                <span style="font-size:12px;color:var(--white);font-weight:600;width:30px;">92%</span>
              </div>
              <div style="display:flex;align-items:center;gap:12px;">
                <span style="font-size:12px;color:var(--muted);width:80px;">7 PM–8 PM</span>
                <div style="flex:1;height:8px;background:rgba(255,255,255,0.07);border-radius:4px;overflow:hidden;">
                  <div style="width:88%;height:100%;background:linear-gradient(90deg,var(--blue2),var(--blue));border-radius:4px;"></div>
                </div>
                <span style="font-size:12px;color:var(--white);font-weight:600;width:30px;">88%</span>
              </div>
              <div style="display:flex;align-items:center;gap:12px;">
                <span style="font-size:12px;color:var(--muted);width:80px;">7 AM–8 AM</span>
                <div style="flex:1;height:8px;background:rgba(255,255,255,0.07);border-radius:4px;overflow:hidden;">
                  <div style="width:74%;height:100%;background:linear-gradient(90deg,var(--orange),#ffcc02);border-radius:4px;"></div>
                </div>
                <span style="font-size:12px;color:var(--white);font-weight:600;width:30px;">74%</span>
              </div>
              <div style="display:flex;align-items:center;gap:12px;">
                <span style="font-size:12px;color:var(--muted);width:80px;">8 PM–9 PM</span>
                <div style="flex:1;height:8px;background:rgba(255,255,255,0.07);border-radius:4px;overflow:hidden;">
                  <div style="width:65%;height:100%;background:linear-gradient(90deg,var(--purple),#c39bd3);border-radius:4px;"></div>
                </div>
                <span style="font-size:12px;color:var(--white);font-weight:600;width:30px;">65%</span>
              </div>
              <div style="display:flex;align-items:center;gap:12px;">
                <span style="font-size:12px;color:var(--muted);width:80px;">6 AM–7 AM</span>
                <div style="flex:1;height:8px;background:rgba(255,255,255,0.07);border-radius:4px;overflow:hidden;">
                  <div style="width:42%;height:100%;background:linear-gradient(90deg,#7f8c8d,#95a5a6);border-radius:4px;"></div>
                </div>
                <span style="font-size:12px;color:var(--white);font-weight:600;width:30px;">42%</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ════ PAGE: SETTINGS ════ -->
      <div class="page" id="page-settings">
        <div class="page-head">
          <div>
            <div class="page-head-title">Settings</div>
            <div class="page-head-sub">System configuration & preferences</div>
          </div>
        </div>

        <div class="summary-row">
          <div class="table-card">
            <div class="table-header">
              <div class="table-title"><span class="dot"></span> Court Settings</div>
            </div>
            <div class="form-grid">
              <div class="form-group">
                <div class="form-label">Court Name</div>
                <input class="form-input" type="text" value="Court 1 — Indoor">
              </div>
              <div class="form-group">
                <div class="form-label">Price per Hour (₹)</div>
                <input class="form-input" type="number" value="200">
              </div>
              <div class="form-group">
                <div class="form-label">Opening Time</div>
                <input class="form-input" type="time" value="06:00">
              </div>
              <div class="form-group">
                <div class="form-label">Closing Time</div>
                <input class="form-input" type="time" value="21:00">
              </div>
              <div class="form-group full">
                <div class="form-label">UPI Payment ID</div>
                <input class="form-input" type="text" value="8660201@ybl">
              </div>
            </div>
            <div class="form-footer">
              <button class="btn btn-outline btn-sm">Reset</button>
              <button class="btn btn-accent btn-sm"><i class="fas fa-save"></i> Save Changes</button>
            </div>
          </div>

          <div class="table-card">
            <div class="table-header">
              <div class="table-title"><span class="dot"></span> Admin Account</div>
            </div>
            <div class="form-grid">
              <div class="form-group full">
                <div class="form-label">Admin Username</div>
                <input class="form-input" type="text" value="admin">
              </div>
              <div class="form-group full">
                <div class="form-label">Current Password</div>
                <input class="form-input" type="password" placeholder="••••••••">
              </div>
              <div class="form-group">
                <div class="form-label">New Password</div>
                <input class="form-input" type="password" placeholder="New password">
              </div>
              <div class="form-group">
                <div class="form-label">Confirm Password</div>
                <input class="form-input" type="password" placeholder="Confirm">
              </div>
            </div>
            <div class="form-footer">
              <button class="btn btn-primary btn-sm"><i class="fas fa-key"></i> Update Password</button>
            </div>
          </div>
        </div>
      </div>

    </div><!-- /content-area -->
  </div><!-- /main -->
</div><!-- /shell -->

<script>
const pageTitles = {
  dashboard: { title: 'Overview', breadcrumb: 'Dashboard → Overview' },
  bookings:  { title: 'Bookings', breadcrumb: 'Dashboard → Bookings' },
  users:     { title: 'Users', breadcrumb: 'Dashboard → Users' },
  payments:  { title: 'Payments', breadcrumb: 'Dashboard → Payments' },
  courts:    { title: 'Courts', breadcrumb: 'Dashboard → Courts' },
  reports:   { title: 'Reports', breadcrumb: 'Dashboard → Reports' },
  settings:  { title: 'Settings', breadcrumb: 'Dashboard → Settings' },
};

function showPage(id, clickedEl) {
  // Hide all pages
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  // Show target
  const target = document.getElementById('page-' + id);
  if (target) target.classList.add('active');

  // Update nav active state
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  if (clickedEl) {
    clickedEl.classList.add('active');
  } else {
    // Find the matching nav item
    document.querySelectorAll('.nav-item').forEach(n => {
      if (n.getAttribute('onclick') && n.getAttribute('onclick').includes("'" + id + "'")) {
        n.classList.add('active');
      }
    });
  }

  // Update topbar
  const meta = pageTitles[id];
  if (meta) {
    document.getElementById('page-title').textContent = meta.title;
    document.getElementById('page-breadcrumb').textContent = meta.breadcrumb;
  }

  // Scroll content area to top
  document.querySelector('.content-area').scrollTop = 0;
}

function filterTable(tbodyId, query) {
  const q = query.toLowerCase();
  const rows = document.querySelectorAll('#' + tbodyId + ' tr');
  rows.forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
}

function confirmCancel() {
  if (confirm('Are you sure you want to cancel this booking?')) {
    alert('Booking cancelled successfully.');
  }
}

// Live clock
function updateClock() {
  const now = new Date();
  const crumb = document.getElementById('page-breadcrumb');
  // We don't update breadcrumb with clock; instead show time in topbar
}
</script>
</body>
</html>