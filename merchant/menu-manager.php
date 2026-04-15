<?php
include_once '../includes/db-connect.php';

$menuItems = [];
$categories = [];

$result = $conn->query("SELECT id, name FROM categories ORDER BY name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

$sql = "SELECT m.id, m.name, m.description, m.price, m.discount_price, m.is_available, m.image_url, m.category_id, COALESCE(c.name, 'Uncategorized') AS category_name
        FROM menu_items m
        LEFT JOIN categories c ON m.category_id = c.id
        ORDER BY m.name";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $menuItems[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'desc' => $row['description'],
            'cat' => $row['category_name'],
            'category_id' => (int)$row['category_id'],
            'price' => (float)$row['price'],
            'disc' => $row['discount_price'] !== null ? (float)$row['discount_price'] : null,
            'avail' => (bool)$row['is_available'],
            'img' => $row['image_url'] ? '../uploads/' . $row['image_url'] : 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=60&h=60&fit=crop'
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Menu Manager | FoodRush Merchant</title>
  <meta name="description" content="Manage your restaurant menu â€” edit prices, descriptions and toggle item availability." />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config={theme:{extend:{colors:{primary:{DEFAULT:'#FF4D24'},secondary:'#1A1A1A'},fontFamily:{sans:['Inter','sans-serif'],heading:['Poppins','sans-serif']}}}}</script>
  <link rel="stylesheet" href="../assets/css/custom.css" />
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>
<body class="bg-[#F8F9FA]">
<nav class="navbar">
  <div class="container-app flex items-center justify-between h-[68px]">
    <div class="flex items-center gap-3">
      <a href="merchant-dashboard.html" class="btn btn-ghost btn-icon"><i data-lucide="arrow-left" class="w-5 h-5"></i></a>
      <h1 class="font-heading font-bold text-lg">Menu Manager</h1>
    </div>
    <button onclick="openAddModal()" class="btn btn-primary btn-sm">
      <i data-lucide="plus" class="w-4 h-4"></i> Add Item
    </button>
  </div>
</nav>

<div class="container-app py-6">
  <!-- Search & Filter -->
  <div class="flex flex-col sm:flex-row gap-3 mb-6">
    <div class="relative flex-1">
      <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-4 h-4"></i>
      <input type="text" placeholder="Search menu items&hellip;" class="input-field pl-9 text-sm" oninput="filterMenu(this.value)" />
    </div>
    <select class="input-field text-sm w-full sm:w-44" onchange="filterCategory(this.value)">
      <option value="">All Categories</option>
      <?php foreach ($categories as $category): ?>
        <option value="<?php echo htmlspecialchars($category['name']); ?>"><?php echo htmlspecialchars($category['name']); ?></option>
      <?php endforeach; ?>
    </select>
    <select class="input-field text-sm w-full sm:w-40">
      <option>All Status</option>
      <option>In Stock</option>
      <option>Out of Stock</option>
    </select>
  </div>

  <!-- Table -->
  <div class="card overflow-x-auto">
    <table class="data-table" id="menu-table">
      <thead>
        <tr>
          <th>Item</th>
          <th>Category</th>
          <th>Price</th>
          <th>Discount</th>
          <th>Stock</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="menu-body">
        <!-- JS Rendered -->
      </tbody>
    </table>
  </div>
</div>

<!-- Edit Modal -->
<div id="edit-modal" class="modal-overlay">
  <div class="modal-box p-6" style="border-radius:20px">
    <div class="flex items-center justify-between mb-4">
      <h2 class="font-heading font-bold text-lg" id="modal-title">Edit Item</h2>
      <button onclick="closeEditModal()" class="btn btn-ghost btn-icon"><i data-lucide="x" class="w-5 h-5"></i></button>
    </div>
    <form id="item-form" enctype="multipart/form-data">
      <div class="space-y-3">
        <div><label class="text-xs font-semibold text-gray-500 uppercase">Item Name</label>
          <input id="e-name" name="name" type="text" class="input-field mt-1 text-sm" /></div>
        <div><label class="text-xs font-semibold text-gray-500 uppercase">Description</label>
          <textarea id="e-desc" name="description" rows="2" class="input-field mt-1 text-sm"></textarea></div>
        <div class="grid grid-cols-2 gap-3">
          <div><label class="text-xs font-semibold text-gray-500 uppercase">Price ($)</label>
            <input id="e-price" name="price" type="number" step="0.01" class="input-field mt-1 text-sm" /></div>
          <div><label class="text-xs font-semibold text-gray-500 uppercase">Discount ($)</label>
            <input id="e-disc" name="discount_price" type="number" step="0.01" placeholder="0.00" class="input-field mt-1 text-sm" /></div>
        </div>
        <div><label class="text-xs font-semibold text-gray-500 uppercase">Category</label>
          <select id="e-cat" name="category_id" class="input-field mt-1 text-sm">
            <?php foreach ($categories as $category): ?>
              <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
            <?php endforeach; ?>
          </select></div>
        <div><label class="text-xs font-semibold text-gray-500 uppercase">Image</label>
          <input id="e-image" name="image" type="file" accept="image/*" class="input-field mt-1 text-sm" />
          <div id="current-image" class="mt-2 hidden">
            <img id="current-image-preview" src="" alt="Current image" class="w-16 h-16 rounded-lg object-cover" />
          </div></div>
      </div>
      <div class="flex gap-3 mt-5">
        <button type="button" onclick="closeEditModal()" class="btn btn-ghost flex-1 border border-gray-200">Cancel</button>
        <button type="submit" class="btn btn-primary flex-1">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<script>
lucide.createIcons();
let editingIdx = null;
const menuItems = <?php echo json_encode($menuItems, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

function renderTable(items) {
  const body = document.getElementById('menu-body');
  if (!items.length) { body.innerHTML = `<tr><td colspan="6" class="text-center py-10 text-gray-400">No items found</td></tr>`; return; }
  body.innerHTML = items.map((item, idx) => `
    <tr class="menu-row" data-cat="${item.cat}" data-name="${item.name.toLowerCase()}">
      <td>
        <div class="flex items-center gap-3">
          <img src="${item.img}" alt="" class="w-12 h-12 rounded-xl object-cover flex-shrink-0" />
          <div>
            <div class="font-semibold text-sm">${item.name}</div>
            <div class="text-xs text-gray-400 line-clamp-1 max-w-[180px]">${item.desc}</div>
          </div>
        </div>
      </td>
      <td><span class="badge badge-neutral">${item.cat}</span></td>
      <td><span class="font-bold text-sm">$${item.price.toFixed(2)}</span></td>
      <td>${item.disc ? `<span class="font-bold text-green-600 text-sm">$${item.disc.toFixed(2)}</span>` : '<span class="text-gray-300 text-sm">â€”</span>'}</td>
      <td>
        <label class="toggle">
          <input type="checkbox" ${item.avail ? 'checked' : ''} onchange="toggleAvail(${idx}, this.checked)" />
          <span class="toggle-slider"></span>
        </label>
      </td>
      <td>
        <div class="flex items-center gap-2">
          <button onclick="openEditModal(${idx})" class="btn btn-ghost btn-sm border border-gray-200">
            <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
          </button>
          <button onclick="deleteItem(${idx})" class="btn btn-sm" style="background:#FEE2E2;color:#DC2626;">
            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
          </button>
        </div>
      </td>
    </tr>
  `).join('');
  lucide.createIcons();
}

renderTable(menuItems);

function filterMenu(q) {
  document.querySelectorAll('.menu-row').forEach(r => {
    r.style.display = r.dataset.name.includes(q.toLowerCase()) ? '' : 'none';
  });
}
function filterCategory(cat) {
  document.querySelectorAll('.menu-row').forEach(r => {
    r.style.display = (!cat || r.dataset.cat === cat) ? '' : 'none';
  });
}
async function toggleAvail(idx, checked) {
  const item = menuItems[idx];
  try {
    const formData = new FormData();
    formData.append('id', item.id);
    formData.append('name', item.name);
    formData.append('description', item.desc);
    formData.append('price', item.price);
    formData.append('discount_price', item.disc || '');
    formData.append('category_id', item.category_id);
    formData.append('is_available', checked ? '1' : '0');

    const response = await fetch('../api/menu-api.php', {
      method: 'POST',
      body: formData
    });

    const result = await response.json();

    if (!result.success) {
      alert('Error updating availability: ' + result.message);
      // Revert checkbox
      event.target.checked = !checked;
    }
  } catch (error) {
    alert('Error updating availability: ' + error.message);
    // Revert checkbox
    event.target.checked = !checked;
  }
}
function deleteItem(idx) {
  if (!confirm('Delete "'+menuItems[idx].name+'"?')) return;
  menuItems.splice(idx, 1);
  renderTable(menuItems);
}
function openEditModal(idx) {
  editingIdx = idx;
  const item = menuItems[idx];
  document.getElementById('modal-title').textContent = 'Edit: ' + item.name;
  document.getElementById('e-name').value = item.name;
  document.getElementById('e-desc').value = item.desc;
  document.getElementById('e-price').value = item.price;
  document.getElementById('e-disc').value = item.disc || '';
  document.getElementById('e-cat').value = item.category_id || '';
  document.getElementById('current-image').classList.remove('hidden');
  document.getElementById('current-image-preview').src = item.img;
  document.getElementById('edit-modal').classList.add('open');
}
function openAddModal() {
  editingIdx = null;
  document.getElementById('modal-title').textContent = 'Add New Item';
  document.getElementById('item-form').reset();
  document.getElementById('current-image').classList.add('hidden');
  document.getElementById('edit-modal').classList.add('open');
}
function closeEditModal() { document.getElementById('edit-modal').classList.remove('open'); }

document.getElementById('item-form').addEventListener('submit', async function(e) {
  e.preventDefault();

  const formData = new FormData(this);
  if (editingIdx !== null) {
    formData.append('id', menuItems[editingIdx].id);
  }

  try {
    const response = await fetch('../api/menu-api.php', {
      method: 'POST',
      body: formData
    });

    const result = await response.json();

    if (result.success) {
      alert(result.message);
      closeEditModal();
      location.reload(); // Reload to refresh data from database
    } else {
      alert('Error: ' + result.message);
    }
  } catch (error) {
    alert('Error saving item: ' + error.message);
  }
});

async function deleteItem(idx) {
  if (!confirm('Delete "' + menuItems[idx].name + '"?')) return;

  try {
    const response = await fetch('../api/menu-api.php?id=' + menuItems[idx].id, {
      method: 'DELETE'
    });

    const result = await response.json();

    if (result.success) {
      alert(result.message);
      location.reload(); // Reload to refresh data from database
    } else {
      alert('Error: ' + result.message);
    }
  } catch (error) {
    alert('Error deleting item: ' + error.message);
  }
}
</script>
</body>
</html>


