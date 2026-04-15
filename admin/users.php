<?php
include_once '../includes/db-connect.php';

$feedback = '';
$feedbackType = 'info';

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
        header('Location: users.php?message=' . urlencode($feedback) . '&type=' . $feedbackType);
        exit;
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
            header('Location: users.php?message=' . urlencode($feedback) . '&type=' . $feedbackType);
            exit;
        }
    }
}

if (isset($_GET['message'])) {
    $feedback = trim($_GET['message']);
    $feedbackType = $_GET['type'] ?? 'info';
}

$editUser = null;
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

$users = [];
$result = $conn->query("SELECT id, full_name, email, role FROM users WHERE role = 'customer' ORDER BY id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>User Management | FoodRush Admin</title>
  <meta name="description" content="Manage all users registered on FoodRush." />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config={theme:{extend:{colors:{primary:{DEFAULT:'#FF4D24'},secondary:'#1A1A1A'},fontFamily:{sans:['Inter','sans-serif'],heading:['Poppins','sans-serif']}}}}</script>
  <link rel="stylesheet" href="../assets/css/custom.css" />
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>
<body class="bg-[#F8F9FA]">
<div class="flex min-h-screen">
  <aside class="sidebar hidden md:flex flex-col py-6 flex-shrink-0">
    <div class="px-5 mb-8">
      <div class="flex items-center gap-2">
        <div class="w-9 h-9 bg-primary rounded-xl flex items-center justify-center"><i data-lucide="zap" class="text-white w-5 h-5"></i></div>
        <span class="font-heading font-black text-xl text-white">FoodRush</span>
      </div>
      <div class="text-xs text-gray-500 mt-1 ml-11">Super Admin</div>
    </div>
    <nav class="flex-1 px-3 space-y-1">
      <a href="admin-panel.php" class="sidebar-link"><i data-lucide="bar-chart-2" class="w-4 h-4"></i> Dashboard</a>
      <a href="users.php" class="sidebar-link active"><i data-lucide="users" class="w-4 h-4"></i> Users</a>
      <a href="../merchant/menu-manager.php" class="sidebar-link"><i data-lucide="utensils" class="w-4 h-4"></i> Menu Items</a>
      <a href="../index.html" class="sidebar-link"><i data-lucide="home" class="w-4 h-4"></i> Home Page</a>
    </nav>
    <div class="px-3 mt-auto pt-4 border-t border-white/10">
      <div class="sidebar-link cursor-pointer"><i data-lucide="settings" class="w-4 h-4"></i> Settings</div>
      <a href="../auth.html" class="sidebar-link"><i data-lucide="log-out" class="w-4 h-4"></i> Sign Out</a>
    </div>
  </aside>

  <div class="flex-1 overflow-auto">
    <header class="bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between sticky top-0 z-30">
      <div>
        <h1 class="font-heading font-bold text-xl">User Management</h1>
        <p class="text-sm text-gray-500">Manage all registered users in the system</p>
      </div>
      <a href="admin-panel.php" class="btn btn-ghost btn-sm flex items-center gap-2"><i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Dashboard</a>
    </header>

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
            <form method="post" action="users.php">
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
                <a href="users.php" class="btn btn-ghost">Cancel</a>
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
                      <a href="users.php?edit_id=<?php echo htmlspecialchars($user['id']); ?>" class="btn btn-ghost btn-sm mr-2">Edit</a>
                      <form method="post" action="users.php" class="inline-block" onsubmit="return confirm('Delete this customer account?');">
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
</div>
<script>lucide.createIcons();</script>
</body>
</html>
