<!DOCTYPE html>
<?php
include_once '../includes/db-connect.php';

$totalUsers = 0;
$totalOrders = 0;
$totalRevenue = 0;
$totalMenuItems = 0;
$topItems = [];

$result = $conn->query("SELECT COUNT(*) AS cnt FROM users");
if ($result) {
    $row = $result->fetch_assoc();
    $totalUsers = (int)$row['cnt'];
}

$result = $conn->query("SELECT COUNT(*) AS cnt FROM orders");
if ($result) {
    $row = $result->fetch_assoc();
    $totalOrders = (int)$row['cnt'];
}

$result = $conn->query("SELECT IFNULL(SUM(total_amount),0) AS revenue FROM orders");
if ($result) {
    $row = $result->fetch_assoc();
    $totalRevenue = (int)$row['revenue'];
}

$result = $conn->query("SELECT COUNT(*) AS cnt FROM menu_items");
if ($result) {
    $row = $result->fetch_assoc();
    $totalMenuItems = (int)$row['cnt'];
}

$result = $conn->query("SELECT m.name AS item_name, COALESCE(SUM(oi.quantity),0) AS orders, COALESCE(SUM(oi.quantity * oi.price),0) AS revenue FROM order_items oi JOIN menu_items m ON oi.menu_item_id = m.id GROUP BY oi.menu_item_id ORDER BY orders DESC LIMIT 5");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $topItems[] = $row;
    }
}

$feedback = '';
$feedbackType = 'info';
$editUser = null;
$users = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($action === 'delete' && $userId > 0) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'customer'");
        $stmt->bind_param('i', $userId);
        if ($stmt->execute()) {
            $feedback = 'Customer account deleted successfully.';
            $feedbackType = 'success';
        } else {
            $feedback = 'Failed to delete user: ' . $stmt->error;
            $feedbackType = 'error';
        }
    }

    if ($action === 'edit' && $userId > 0) {
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($fullName === '' || $email === '') {
            $feedback = 'Name and email are required.';
            $feedbackType = 'error';
        } else {
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ? AND role = 'customer'");
            $stmt->bind_param('ssi', $fullName, $email, $userId);
            if ($stmt->execute()) {
                $feedback = 'Customer account updated successfully.';
                $feedbackType = 'success';
            } else {
                $feedback = 'Failed to update user: ' . $stmt->error;
                $feedbackType = 'error';
            }
        }
    }
}

if (isset($_GET['edit_id'])) {
    $editId = (int)$_GET['edit_id'];
    if ($editId > 0) {
        $stmt = $conn->prepare("SELECT id, full_name, email, role FROM users WHERE id = ? AND role = 'customer'");
        $stmt->bind_param('i', $editId);
        $stmt->execute();
        $result = $stmt->get_result();
        $editUser = $result->fetch_assoc();
    }
}

$result = $conn->query("SELECT id, full_name, email, role FROM users WHERE role = 'customer' ORDER BY id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>
<html lang="en">
<head>
  <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Panel | FoodRush</title>
  <meta name="description" content="FoodRush super admin panel." />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config={theme:{extend:{colors:{primary:{DEFAULT:'#FF4D24'},secondary:'#1A1A1A'},fontFamily:{sans:['Inter','sans-serif'],heading:['Poppins','sans-serif']}}}}</script>
  <link rel="stylesheet" href="../assets/css/custom.css" />
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .panel-view { display: none; }
    .panel-view.active { display: block; }
    .panel-frame {
      width: 100%;
      min-height: calc(100vh - 220px);
      border: 0;
      border-radius: 0.75rem;
      background: #fff;
    }
  </style>
</head>
<body class="bg-[#F8F9FA]">
<div class="flex min-h-screen">

  <!-- ═══ SIDEBAR ═══ -->
  <aside class="sidebar hidden md:flex flex-col py-6 flex-shrink-0">
    <div class="px-5 mb-8">
      <div class="flex items-center gap-2">
        <div class="w-9 h-9 bg-primary rounded-xl flex items-center justify-center"><i data-lucide="zap" class="text-white w-5 h-5"></i></div>
        <span class="font-heading font-black text-xl text-white">FoodRush</span>
      </div>
      <div class="text-xs text-gray-500 mt-1 ml-11">Super Admin</div>
    </div>
    <nav class="flex-1 px-3 space-y-1">
      <a href="#" class="sidebar-link active" data-panel="analytics"><i data-lucide="bar-chart-2" class="w-4 h-4"></i> Dashboard</a>
      <a href="#" class="sidebar-link" data-panel="users"><i data-lucide="users" class="w-4 h-4"></i> Users</a>
      <a href="#" class="sidebar-link" data-panel="menu-items"><i data-lucide="utensils" class="w-4 h-4"></i> Menu Items</a>
      <a href="#" class="sidebar-link" data-panel="orders"><i data-lucide="package" class="w-4 h-4"></i> Orders</a>
      <a href="../index.php" class="sidebar-link"><i data-lucide="home" class="w-4 h-4"></i> Home Page</a>
    </nav>
  </aside>

  <!-- ═══ MAIN ═══ -->
  <div class="flex-1 overflow-auto">
    <!-- Top Header -->
    <header id="main-header" class="bg-white border-b border-gray-100 px-6 py-4 flex items-center sticky top-0 z-30">
      <div>
        <h1 class="font-heading font-bold text-xl" id="panel-title">Admin Dashboard</h1>
      </div>
    </header>

    <!-- ═══════ ANALYTICS PANEL ═══════ -->
    <div class="panel-view active" id="panel-analytics">
      <div class="p-6 space-y-6">
        <!-- KPI Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="stat-card"><div class="icon-wrap" style="background:#FFF3ED"><i data-lucide="users" class="w-6 h-6 text-primary"></i></div><div class="value"><?php echo number_format($totalUsers); ?></div><div class="label">Total Users</div><div class="trend trend-up"><i data-lucide="trending-up" class="w-4 h-4"></i>Live from DB</div></div>
          <div class="stat-card"><div class="icon-wrap bg-blue-50"><i data-lucide="shopping-bag" class="w-6 h-6 text-blue-600"></i></div><div class="value"><?php echo number_format($totalOrders); ?></div><div class="label">Total Orders</div><div class="trend trend-up"><i data-lucide="trending-up" class="w-4 h-4"></i>Live from DB</div></div>
          <div class="stat-card"><div class="icon-wrap bg-green-50"><i data-lucide="dollar-sign" class="w-6 h-6 text-green-600"></i></div><div class="value">$<?php echo number_format($totalRevenue); ?></div><div class="label">Total Revenue</div><div class="trend trend-up"><i data-lucide="trending-up" class="w-4 h-4"></i>Live from DB</div></div>
          <div class="stat-card"><div class="icon-wrap bg-purple-50"><i data-lucide="package" class="w-6 h-6 text-purple-600"></i></div><div class="value"><?php echo number_format($totalMenuItems); ?></div><div class="label">Menu Items</div><div class="trend trend-up"><i data-lucide="trending-up" class="w-4 h-4"></i>Live from DB</div></div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="card p-5 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
              <h2 class="font-heading font-bold text-base">Revenue Overview</h2>
              <div class="tabs-bar w-auto">
                <button class="tab-btn active px-3 py-1.5 text-xs" id="rev-7d" onclick="switchRevChart('7d')">7D</button>
                <button class="tab-btn px-3 py-1.5 text-xs" id="rev-30d" onclick="switchRevChart('30d')">30D</button>
                <button class="tab-btn px-3 py-1.5 text-xs" id="rev-12m" onclick="switchRevChart('12m')">12M</button>
              </div>
            </div>
            <canvas id="revenueLineChart" height="100"></canvas>
          </div>
          <div class="card p-5">
            <h2 class="font-heading font-bold text-base mb-4">Order Breakdown</h2>
            <canvas id="orderDonut" height="180"></canvas>
            <div class="mt-4 space-y-2 text-sm">
              <div class="flex items-center justify-between"><span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-green-400 inline-block"></span>Completed</span><span class="font-bold">78%</span></div>
              <div class="flex items-center justify-between"><span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-primary inline-block"></span>Shipping</span><span class="font-bold">14%</span></div>
              <div class="flex items-center justify-between"><span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-red-400 inline-block"></span>Cancelled</span><span class="font-bold">8%</span></div>
            </div>
          </div>
        </div>

        <!-- Top Restaurants -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="card p-5">
            <h2 class="font-heading font-bold text-base mb-4">User Growth</h2>
            <canvas id="usersChart" height="130"></canvas>
          </div>
          <div class="card overflow-hidden">
            <div class="p-5 border-b"><h2 class="font-heading font-bold text-base">Top Selling Items</h2></div>
            <table class="data-table">
              <thead><tr><th>#</th><th>Item</th><th>Orders</th><th>Revenue</th></tr></thead>
              <tbody>
                <?php if (count($topItems) > 0): ?>
                    <?php foreach ($topItems as $index => $item): ?>
                        <tr>
                          <td class="font-bold text-primary"><?php echo $index + 1; ?></td>
                          <td class="font-medium"><?php echo htmlspecialchars($item['item_name']); ?></td>
                          <td><?php echo number_format($item['orders']); ?></td>
                          <td class="font-semibold">$<?php echo number_format($item['revenue']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center text-gray-500 py-4">No ordered items yet.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- ═══════ USERS PANEL ═══════ -->
    <div class="panel-view" id="panel-users">
      <div class="p-6">
        <?php if ($feedback !== ''): ?>
          <div class="mb-4 rounded-lg px-4 py-3 text-sm <?php echo $feedbackType === 'success' ? 'bg-green-50 text-green-700' : ($feedbackType === 'error' ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-700'); ?>">
            <?php echo htmlspecialchars($feedback); ?>
          </div>
        <?php endif; ?>

        <?php if ($editUser): ?>
          <div class="card mb-6 overflow-hidden">
            <div class="p-5 border-b">
              <h2 class="font-heading font-bold text-base">Edit Customer Account</h2>
            </div>
            <div class="p-5">
              <form method="post" action="admin-panel.php?panel=users">
                <input type="hidden" name="action" value="edit" />
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($editUser['id']); ?>" />
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Full Name</label>
                    <input name="full_name" type="text" value="<?php echo htmlspecialchars($editUser['full_name']); ?>" class="input-field mt-1 text-sm w-full" required />
                  </div>
                  <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Email</label>
                    <input name="email" type="email" value="<?php echo htmlspecialchars($editUser['email']); ?>" class="input-field mt-1 text-sm w-full" required />
                  </div>
                </div>
                <div class="flex gap-3 mt-4">
                  <button type="submit" class="btn btn-primary">Save Changes</button>
                  <a href="admin-panel.php?panel=users" class="btn btn-ghost">Cancel</a>
                </div>
              </form>
            </div>
          </div>
        <?php endif; ?>

        <div class="card overflow-hidden">
          <div class="p-5 border-b flex items-center justify-between">
            <div>
              <h2 class="font-heading font-bold text-base">Registered Users</h2>
              <p class="text-sm text-gray-500 mt-1">Total users: <?php echo number_format(count($users)); ?></p>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="data-table w-full">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Status</th>
                  <th>Joined</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (count($users) > 0): ?>
                  <?php foreach ($users as $user): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($user['id']); ?></td>
                      <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                      <td><?php echo htmlspecialchars($user['email']); ?></td>
                      <td><?php echo htmlspecialchars($user['role']); ?></td>
                      <td><span class="pill pill-completed">Active</span></td>
                      <td>—</td>
                      <td class="text-right">
                        <a href="admin-panel.php?panel=users&edit_id=<?php echo htmlspecialchars($user['id']); ?>" class="btn btn-ghost btn-sm mr-2">Edit</a>
                        <form method="post" action="admin-panel.php?panel=users" class="inline-block" onsubmit="return confirm('Delete this customer account?');">
                          <input type="hidden" name="action" value="delete" />
                          <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>" />
                          <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="7" class="text-center py-10 text-gray-400">No users found.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- ═══════ MENU ITEMS PANEL ═══════ -->
    <div class="panel-view" id="panel-menu-items">
      <div class="p-6">
        <h2 class="font-heading font-bold text-xl mb-4">Menu Items</h2>
        <div class="card overflow-hidden">
          <iframe src="../merchant/menu-manager.php" class="panel-frame" title="Menu Manager"></iframe>
        </div>
      </div>
    </div>

    <!-- ═══════ ORDERS PANEL ═══════ -->
    <div class="panel-view" id="panel-orders">
      <div class="p-6">
        <h2 class="font-heading font-bold text-xl mb-4">Orders</h2>
        <div class="card overflow-hidden">
          <iframe src="../customer/history-reviews.html" class="panel-frame" title="Orders"></iframe>
        </div>
      </div>
    </div>

    <!-- ═══════ VOUCHERS PANEL ═══════ -->
    <div class="panel-view" id="panel-vouchers">
      <div class="p-6 space-y-6">
        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div class="stat-card"><div class="icon-wrap" style="background:#FFF3ED"><i data-lucide="tag" class="w-6 h-6 text-primary"></i></div><div class="value">12</div><div class="label">Active Vouchers</div></div>
          <div class="stat-card"><div class="icon-wrap bg-green-50"><i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i></div><div class="value">3,841</div><div class="label">Times Redeemed</div></div>
          <div class="stat-card"><div class="icon-wrap bg-red-50"><i data-lucide="dollar-sign" class="w-6 h-6 text-red-500"></i></div><div class="value">$18,400</div><div class="label">Discounts Given</div></div>
          <div class="stat-card"><div class="icon-wrap bg-blue-50"><i data-lucide="percent" class="w-6 h-6 text-blue-600"></i></div><div class="value">12.4%</div><div class="label">Usage Rate</div></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Create Form -->
          <div class="card p-5">
            <h2 class="font-heading font-bold text-base mb-4 flex items-center gap-2"><i data-lucide="plus-circle" class="w-5 h-5 text-primary"></i> Quick Create</h2>
            <div class="space-y-3">
              <div><label class="text-xs font-semibold text-gray-500 uppercase">Voucher Code *</label>
                <div class="flex gap-2 mt-1"><input id="v-code" type="text" placeholder="e.g. SUMMER30" class="input-field text-sm uppercase" />
                  <button onclick="generateCode()" class="btn btn-ghost border border-gray-200 btn-sm px-3" title="Auto-generate"><i data-lucide="refresh-cw" class="w-4 h-4"></i></button></div></div>
              <div><label class="text-xs font-semibold text-gray-500 uppercase">Discount Type</label>
                <select id="v-type" class="input-field mt-1 text-sm" onchange="updateDiscLabel()">
                  <option value="percent">Percentage (%)</option><option value="fixed">Fixed Amount ($)</option>
                </select></div>
              <div><label id="v-disc-label" class="text-xs font-semibold text-gray-500 uppercase">Discount Value (%)</label>
                <input id="v-disc" type="number" min="1" placeholder="20" class="input-field mt-1 text-sm" /></div>
              <div><label class="text-xs font-semibold text-gray-500 uppercase">Min Order Spend ($)</label>
                <input id="v-min" type="number" min="0" placeholder="0" class="input-field mt-1 text-sm" /></div>
              <div><label class="text-xs font-semibold text-gray-500 uppercase">Max Uses (blank = ∞)</label>
                <input id="v-max" type="number" min="1" placeholder="Unlimited" class="input-field mt-1 text-sm" /></div>
              <div><label class="text-xs font-semibold text-gray-500 uppercase">Expiry Date *</label>
                <input id="v-expiry" type="date" class="input-field mt-1 text-sm" /></div>
              <button onclick="createVoucher()" class="btn btn-primary w-full mt-2"><i data-lucide="plus" class="w-4 h-4"></i> Create Voucher</button>
            </div>
          </div>

          <!-- Voucher Table -->
          <div class="lg:col-span-2 card overflow-hidden">
            <div class="flex items-center justify-between p-5 border-b">
              <h2 class="font-heading font-bold text-base">Active Promotions</h2>
              <div class="flex gap-2">
                <div class="relative"><i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-3.5 h-3.5"></i>
                  <input type="text" placeholder="Search codes…" class="input-field py-2 pl-8 text-sm w-36" oninput="searchVouchers(this.value)" /></div>
              </div>
            </div>
            <div class="overflow-x-auto">
              <table class="data-table" id="voucher-table">
                <thead><tr><th>Code</th><th>Type</th><th>Discount</th><th>Min</th><th>Uses</th><th>Expires</th><th>Status</th><th></th></tr></thead>
                <tbody id="voucher-body"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div><!-- /main -->
</div>

<script>
lucide.createIcons();

/* ── Panel switching ── */
function switchPanel(id) {
  document.querySelectorAll('.panel-view').forEach(p => p.classList.remove('active'));
  const panel = document.getElementById('panel-' + id);
  if (panel) panel.classList.add('active');

  document.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));
  const activeLink = document.querySelector(`[data-panel="${id}"]`);
  if (activeLink) activeLink.classList.add('active');

  const titles = {
    analytics: 'Admin Dashboard',
    users: 'User Management',
    'menu-items': 'Menu Items',
    orders: 'Orders',
    vouchers: 'Voucher Management'
  };
  document.getElementById('panel-title').textContent = titles[id] || 'Admin Panel';

  const header = document.getElementById('main-header');
  if (id === 'menu-items' || id === 'orders') {
    header.style.display = 'none';
  } else {
    header.style.display = 'flex';
  }

  if (id === 'analytics') initCharts();
  lucide.createIcons();
}

document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.sidebar-link[data-panel]').forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      const target = this.dataset.panel;
      switchPanel(target);
      const url = new URL(window.location);
      if (target === 'analytics') url.searchParams.delete('panel');
      else url.searchParams.set('panel', target);
      window.history.replaceState({}, '', url);
    });
  });

  const urlParams = new URLSearchParams(window.location.search);
  const initialPanel = urlParams.get('panel') || (urlParams.get('edit_id') ? 'users' : 'analytics');
  switchPanel(initialPanel);
});

/* ── Charts ── */
let chartsInitialized = false;
function initCharts() {
  if (chartsInitialized) return;
  chartsInitialized = true;

  const months7 = ['Apr 4','Apr 5','Apr 6','Apr 7','Apr 8','Apr 9','Apr 10'];
  const data7d = [4200,6800,5100,7900,8400,9200,8700];
  const lineCtx = document.getElementById('revenueLineChart').getContext('2d');
  const revGrad = lineCtx.createLinearGradient(0,0,0,200);
  revGrad.addColorStop(0,'rgba(255,77,36,.25)');
  revGrad.addColorStop(1,'rgba(255,77,36,.0)');

  const revChart = new Chart(lineCtx, {
    type:'line',
    data:{ labels:months7, datasets:[{ label:'Revenue ($)', data:data7d, borderColor:'#FF4D24', borderWidth:2.5, backgroundColor:revGrad, fill:true, tension:.42, pointBackgroundColor:'#FF4D24', pointRadius:4, pointHoverRadius:7 }] },
    options:{ responsive:true, plugins:{legend:{display:false}}, scales:{
      y:{beginAtZero:true, grid:{color:'#F1F3F5'}, ticks:{callback:v=>'$'+v.toLocaleString(),font:{size:11}}},
      x:{grid:{display:false}, ticks:{font:{size:11}}}
    }}
  });

  const revData = {
    '7d':{ labels:months7, data:data7d },
    '30d':{ labels:['W1','W2','W3','W4'], data:[28000,34000,41000,38000] },
    '12m':{ labels:['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'], data:[120000,145000,132000,198000,210000,195000,220000,245000,230000,260000,280000,310000] }
  };

  window.switchRevChart = function(period) {
    ['7d','30d','12m'].forEach(p => document.getElementById('rev-'+p).classList.remove('active'));
    document.getElementById('rev-'+period).classList.add('active');
    revChart.data.labels = revData[period].labels;
    revChart.data.datasets[0].data = revData[period].data;
    revChart.update();
  };

  new Chart(document.getElementById('orderDonut'), {
    type:'doughnut',
    data:{ labels:['Completed','Shipping','Cancelled'], datasets:[{ data:[78,14,8], backgroundColor:['#22C55E','#FF4D24','#EF4444'], borderWidth:0, hoverOffset:6 }] },
    options:{ responsive:true, cutout:'72%', plugins:{legend:{display:false}} }
  });

  new Chart(document.getElementById('usersChart'), {
    type:'line',
    data:{ labels:['Jan','Feb','Mar','Apr','May','Jun','Jul'], datasets:[{ label:'New Users', data:[4200,5800,7100,8900,10200,12400,14800], borderColor:'#3B82F6', borderWidth:2.5, backgroundColor:'rgba(59,130,246,.1)', fill:true, tension:.42, pointRadius:4, pointBackgroundColor:'#3B82F6' }] },
    options:{ responsive:true, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true,grid:{color:'#F1F3F5'},ticks:{font:{size:11}}},x:{grid:{display:false},ticks:{font:{size:11}}}} }
  });
}

initCharts();

/* ── Vouchers ── */
let vouchers = [
  { id:1, code:'RUSH20', type:'percent', disc:20, min:0, max:null, used:842, expiry:'2026-06-30', active:true },
  { id:2, code:'FREEDELIVERY', type:'fixed', disc:2.50, min:15, max:1000, used:355, expiry:'2026-05-31', active:true },
  { id:3, code:'WELCOME5', type:'fixed', disc:5, min:10, max:500, used:1204, expiry:'2026-12-31', active:true },
  { id:4, code:'RUSH30', type:'percent', disc:30, min:0, max:100, used:100, expiry:'2026-04-15', active:true },
  { id:5, code:'PIZZA10', type:'percent', disc:10, min:20, max:null, used:412, expiry:'2026-03-31', active:false },
];

function renderVouchers(list) {
  const body = document.getElementById('voucher-body');
  const today = new Date();
  body.innerHTML = list.map(v => {
    const expired = new Date(v.expiry) < today;
    const status = (!v.active || expired) ? 'expired' : 'active';
    return `
    <tr class="voucher-row" data-code="${v.code.toLowerCase()}" data-status="${status}">
      <td><div class="flex items-center gap-2">
        <span class="font-bold font-mono text-sm bg-gray-100 px-2.5 py-1 rounded-lg">${v.code}</span>
        <button onclick="copyCode('${v.code}')" class="btn btn-ghost btn-icon btn-sm" title="Copy"><i data-lucide="copy" class="w-3.5 h-3.5"></i></button>
      </div></td>
      <td>${v.type==='percent'?'<span class="badge badge-primary text-xs">%</span>':'<span class="badge badge-success text-xs">$</span>'}</td>
      <td class="font-bold">${v.type==='percent'?v.disc+'%':'$'+v.disc.toFixed(2)}</td>
      <td>${v.min>0?'$'+v.min:'<span class="text-gray-400">—</span>'}</td>
      <td><div class="text-sm">${v.used}/${v.max||'∞'}</div>${v.max?`<div class="h-1.5 bg-gray-100 rounded-full mt-1 w-16"><div class="h-full bg-primary rounded-full" style="width:${Math.min(100,v.used/v.max*100)}%"></div></div>`:''}</td>
      <td class="text-sm ${new Date(v.expiry)<today?'text-red-500':'text-gray-600'}">${v.expiry}</td>
      <td>${status==='active'?'<span class="pill pill-completed">Active</span>':'<span class="pill pill-cancelled">Expired</span>'}</td>
      <td><div class="flex gap-1">
        <button onclick="toggleVoucher(${v.id})" class="btn btn-ghost btn-sm border border-gray-200 text-xs">${v.active?'Disable':'Enable'}</button>
        <button onclick="deleteVoucher(${v.id})" class="btn btn-sm btn-icon" style="background:#FEE2E2;color:#DC2626;"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button>
      </div></td>
    </tr>`;
  }).join('');
  lucide.createIcons();
}
renderVouchers(vouchers);

function generateCode() { const c='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'; document.getElementById('v-code').value=[...Array(8)].map(()=>c[Math.random()*c.length|0]).join(''); }
function updateDiscLabel() { document.getElementById('v-disc-label').textContent = document.getElementById('v-type').value==='percent'?'Discount Value (%)':'Discount Value ($)'; }
function createVoucher() {
  const code=document.getElementById('v-code').value.trim().toUpperCase();
  const disc=parseFloat(document.getElementById('v-disc').value);
  const expiry=document.getElementById('v-expiry').value;
  if(!code||!disc||!expiry){alert('Fill Code, Discount, and Expiry.');return;}
  if(vouchers.find(v=>v.code===code)){alert('Code already exists!');return;}
  vouchers.unshift({id:Date.now(),code,type:document.getElementById('v-type').value,disc,min:parseFloat(document.getElementById('v-min').value)||0,max:parseInt(document.getElementById('v-max').value)||null,used:0,expiry,active:true});
  renderVouchers(vouchers);
  ['v-code','v-disc','v-min','v-max','v-expiry'].forEach(id=>document.getElementById(id).value='');
}
function toggleVoucher(id){const v=vouchers.find(v=>v.id===id);if(v)v.active=!v.active;renderVouchers(vouchers);}
function deleteVoucher(id){if(!confirm('Delete this voucher?'))return;vouchers=vouchers.filter(v=>v.id!==id);renderVouchers(vouchers);}
function copyCode(code){navigator.clipboard.writeText(code).then(()=>alert('Copied: '+code));}
function searchVouchers(q){document.querySelectorAll('.voucher-row').forEach(r=>{r.style.display=r.dataset.code.includes(q.toLowerCase())?'':' none';});}
</script>
</body>
</html>
