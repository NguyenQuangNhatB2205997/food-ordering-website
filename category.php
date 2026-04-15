<?php include 'includes/db-connect.php';
$categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$categoryName = null;
$items = [];
if ($categoryId > 0) {
    $stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
    $stmt->bind_param('i', $categoryId);
    $stmt->execute();
    $stmt->bind_result($categoryName);
    $stmt->fetch();
    $stmt->close();

    if ($categoryName) {
        $sql = "SELECT mi.id, mi.name, mi.description, mi.image_url, mi.price, '' AS restaurant_name
                FROM menu_items mi
                WHERE mi.category_id = ?
                ORDER BY mi.name";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FoodRush — Category<?php echo $categoryName ? ': ' . htmlspecialchars($categoryName) : ''; ?></title>
    <meta name="description" content="Browse menu items for the selected FoodRush category." />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              primary: { DEFAULT: "#FF4D24", light: "#FF6B4A", dark: "#E03D18", alpha: "rgba(255,77,36,0.12)" },
              secondary: "#1A1A1A",
              surface: { DEFAULT: "#FFFFFF", 2: "#F1F3F5" },
              border: "#E5E7EB"
            },
            fontFamily: { sans: ["Inter", "sans-serif"], heading: ["Poppins", "sans-serif"] },
            boxShadow: { primary: "0 8px 24px rgba(255,77,36,.30)" },
            borderRadius: { "2xl": "20px", "3xl": "28px" }
          }
        }
      };
    </script>
    <link rel="stylesheet" href="assets/css/custom.css" />
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
  </head>
  <body class="bg-[#F8F9FA]">
    <nav class="navbar" id="navbar">
      <div class="container-app flex items-center justify-between h-[68px] gap-4">
        <a href="index.php" class="flex items-center gap-2 flex-shrink-0">
          <div class="w-9 h-9 bg-primary rounded-xl flex items-center justify-center">
            <i data-lucide="zap" class="text-white w-5 h-5"></i>
          </div>
          <span class="font-heading font-800 text-xl" style="font-weight: 800">
            Food<span style="color: #ff4d24">Rush</span>
          </span>
        </a>
        <div class="hidden md:flex flex-1 max-w-md relative">
          <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-4 h-4"></i>
          <input type="text" placeholder="Search restaurants, dishes…" class="input-field pl-9 pr-4 py-2.5 text-sm rounded-2xl" disabled />
        </div>
        <div class="flex items-center gap-2">
          <a href="customer/history-reviews.html" class="btn btn-ghost btn-icon hidden md:flex" title="Orders"><i data-lucide="package" class="w-5 h-5"></i></a>
          <a href="merchant/merchant-dashboard.html" class="btn btn-ghost btn-sm hidden md:flex gap-1.5" title="Merchant"><i data-lucide="store" class="w-4 h-4"></i><span class="hidden lg:inline">Merchant</span></a>
          <button id="cart-btn" class="btn btn-ghost btn-icon relative" title="Cart"><i data-lucide="shopping-bag" class="w-5 h-5"></i><span id="cart-count" class="absolute -top-1 -right-1 w-5 h-5 bg-primary text-white text-[10px] font-bold rounded-full flex items-center justify-center">3</span></button>
          <a href="auth.html" class="btn btn-primary btn-sm hidden md:flex" id="signin-btn"><i data-lucide="log-in" class="w-4 h-4"></i> Sign In</a>
          <a href="customer/profile.html" class="ml-1 w-9 h-9 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-primary/30 hidden md:block"><img src="https://api.dicebear.com/7.x/avataaars/svg?seed=user123" alt="Profile" class="w-full h-full object-cover" /></a>
          <a href="auth.html" class="btn btn-ghost btn-icon md:hidden" title="Sign In"><i data-lucide="log-in" class="w-5 h-5"></i></a>
        </div>
      </div>
    </nav>

    <section class="py-16">
      <div class="container-app">
        <div class="flex flex-col gap-4 mb-8">
          <div class="flex items-center gap-3">
            <a href="index.php" class="text-sm text-primary font-semibold hover:underline">Back to Home</a>
            <span class="text-gray-300">/</span>
            <h1 class="font-heading font-black text-3xl"><?php echo $categoryName ? htmlspecialchars($categoryName) : 'Category'; ?></h1>
          </div>
          <p class="text-gray-500 max-w-2xl">
            <?php if ($categoryName): ?>
              Browse menu items for "<?php echo htmlspecialchars($categoryName); ?>".
            <?php else: ?>
              Category not found or no category selected. Please choose a category from the homepage.
            <?php endif; ?>
          </p>
        </div>

        <?php if (!$categoryName): ?>
          <div class="rounded-3xl bg-white p-10 text-center shadow">
            <p class="text-gray-500">Category không tồn tại. <a href="index.php" class="text-primary font-semibold">Quay lại trang chủ</a>.</p>
          </div>
        <?php else: ?>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            <?php if (count($items) === 0): ?>
              <div class="col-span-full rounded-3xl bg-white p-10 text-center shadow">
                <p class="text-gray-500">Không có món nào thuộc category này.</p>
              </div>
            <?php endif; ?>
            <?php foreach ($items as $item): ?>
              <?php $image = $item['image_url'] ?: 'https://via.placeholder.com/500x320?text=No+Image'; ?>
              <?php $price = number_format((int)$item['price'], 0, ',', '.') . ' ₫'; ?>
              <div class="restaurant-card block animate-fade-in-up border border-gray-200 overflow-hidden rounded-2xl shadow-sm bg-white">
                <div class="banner-wrap">
                  <img class="banner" src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" />
                </div>
                <div class="p-4">
                  <div class="flex items-center justify-between gap-3 mb-3">
                    <div>
                      <h2 class="font-heading font-bold text-base"><?php echo htmlspecialchars($item['name']); ?></h2>
                      <p class="text-sm text-gray-500 mt-1"><?php echo htmlspecialchars($item['restaurant_name'] ?: 'Local restaurant'); ?></p>
                    </div>
                    <span class="text-primary font-semibold"><?php echo $price; ?></span>
                  </div>
                  <p class="text-sm text-gray-600 mb-4"><?php echo htmlspecialchars(substr($item['description'] ?: 'Delicious menu item', 0, 80)); ?>...</p>
                  <div class="flex items-center justify-between">
                    <button class="btn btn-primary btn-sm" onclick="window.location.href='customer/restaurant.html'">View</button>
                    <button class="btn btn-ghost btn-sm" onclick="addToCart(<?php echo (int)$item['id']; ?>)">Add to Cart</button>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <footer class="footer py-12">
      <div class="container-app text-center text-sm text-gray-500">&copy; 2026 FoodRush. All rights reserved.</div>
    </footer>

    <script>lucide.createIcons();</script>
    <script src="js/navbar.js"></script>
    <script src="js/cart.js"></script>
  </body>
</html>
