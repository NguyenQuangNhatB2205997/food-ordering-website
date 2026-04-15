<!doctype html>
<?php
include 'includes/db-connect.php';
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$searchEscaped = mysqli_real_escape_string($conn, $search);
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int) $_GET['page'] : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;
$whereClause = '';
if ($searchEscaped !== '') {
    $whereClause = "WHERE mi.name LIKE '%$searchEscaped%' OR mi.description LIKE '%$searchEscaped%'";
}
$countSql = "SELECT COUNT(DISTINCT mi.id) AS total FROM menu_items mi $whereClause";
$countResult = $conn->query($countSql);
$totalItems = ($countResult && $countResult->num_rows > 0) ? (int) $countResult->fetch_assoc()['total'] : 0;
$totalPages = max(1, (int) ceil($totalItems / $perPage));
if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $perPage;
}
$searchSql = "SELECT mi.id, mi.name, mi.description, mi.image_url, mi.price, COALESCE(SUM(oi.quantity), 0) AS sold_count, 'Sample Restaurant' as restaurant_name FROM menu_items mi LEFT JOIN order_items oi ON oi.menu_item_id = mi.id $whereClause GROUP BY mi.id, mi.name, mi.description, mi.image_url, mi.price ORDER BY mi.id LIMIT $perPage OFFSET $offset";
$result = $conn->query($searchSql);
?>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Search Results - FoodRush</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              primary: {
                DEFAULT: "#FF4D24",
                light: "#FF6B4A",
                dark: "#E03D18",
                alpha: "rgba(255,77,36,0.12)",
              },
              secondary: "#1A1A1A",
              surface: { DEFAULT: "#FFFFFF", 2: "#F1F3F5" },
              border: "#E5E7EB",
            },
            fontFamily: {
              sans: ["Inter", "sans-serif"],
              heading: ["Poppins", "sans-serif"],
            },
            boxShadow: { primary: "0 8px 24px rgba(255,77,36,.30)" },
            borderRadius: { "2xl": "20px", "3xl": "28px" },
          },
        },
      };
    </script>
    <link rel="stylesheet" href="assets/css/custom.css" />
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
  </head>
  <body class="bg-[#F8F9FA]">
    <section class="py-10">
      <div class="container-app">
        <div class="mb-8">
          <a href="index.php" class="text-sm text-primary font-semibold">← Back to Home</a>
          <div class="mt-4">
            <h1 class="font-heading font-black text-3xl md:text-4xl mb-3">Search Results</h1>
            <p class="text-gray-500 text-sm">
              <?php if ($search !== ''): ?>
                Showing items matching "<?php echo htmlspecialchars($search); ?>"
              <?php else: ?>
                Showing all menu items
              <?php endif; ?>
            </p>
          </div>
        </div>

        <form action="search.php" method="GET" class="mb-8 max-w-2xl">
          <div class="relative">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-4 h-4"></i>
            <input
              name="q"
              type="text"
              value="<?php echo htmlspecialchars($search); ?>"
              placeholder="Search dishes or descriptions…"
              class="input-field pl-9 pr-4 py-3 w-full rounded-2xl"
            />
          </div>
        </form>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($dish = $result->fetch_assoc()): ?>
              <?php
                $image = $dish['image_url'] ?: 'https://via.placeholder.com/500x220?text=No+Image';
                $price = number_format((int)$dish['price'], 0, ',', '.') . ' ₫';
              ?>
              <div class="dish-card block animate-fade-in-up border border-gray-200 rounded-2xl bg-white overflow-hidden">
                <div class="banner-wrap">
                  <img class="banner" src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($dish['name']); ?>" />
                </div>
                <div class="p-4">
                  <h3 class="font-heading font-bold text-base mb-1"><?php echo htmlspecialchars($dish['name']); ?></h3>
                  <p class="text-sm text-gray-500 mb-2"><?php echo htmlspecialchars($dish['restaurant_name']); ?></p>
                  <p class="text-sm text-gray-600 mb-3"><?php echo htmlspecialchars(substr($dish['description'] ?: '', 0, 50)); ?>...</p>
                  <div class="text-xs text-gray-400 mb-3">Sold <?php echo (int) $dish['sold_count']; ?> times</div>
                  <div class="flex items-center justify-between">
                    <span class="font-semibold text-primary"><?php echo $price; ?></span>
                    <button class="btn btn-primary btn-sm" onclick="event.preventDefault();">Add to Cart</button>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <div class="col-span-full text-center py-16 text-gray-500">
              No items matched your search.
            </div>
          <?php endif; ?>
        </div>

        <?php if ($totalPages > 1): ?>
          <div class="mt-8 flex flex-wrap items-center justify-center gap-2">
            <?php if ($page > 1): ?>
              <a href="search.php?q=<?php echo urlencode($search); ?>&page=<?php echo $page - 1; ?>" class="btn btn-outline btn-sm">Previous</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <a href="search.php?q=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>" class="btn btn-sm <?php echo $i === $page ? 'btn-primary' : 'btn-outline'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
              <a href="search.php?q=<?php echo urlencode($search); ?>&page=<?php echo $page + 1; ?>" class="btn btn-outline btn-sm">Next</a>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <script>lucide.createIcons();</script>
  </body>
</html>
