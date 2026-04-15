<!doctype html>
<?php include 'includes/db-connect.php'; ?>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FoodRush &mdash; Order Delicious Food, Delivered Fast</title>
    <meta
      name="description"
      content="FoodRush delivers your favorite meals from top-rated restaurants right to your door. Browse menus, track orders live, and enjoy exclusive deals."
    />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
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
    <!-- ============================== NAVBAR ============================== -->
    <nav class="navbar" id="navbar">
      <div
        class="container-app flex items-center justify-between h-[68px] gap-4"
      >
        <a href="index.html" class="flex items-center gap-2 flex-shrink-0">
          <div
            class="w-9 h-9 bg-primary rounded-xl flex items-center justify-center"
          >
            <i data-lucide="zap" class="text-white w-5 h-5"></i>
          </div>
          <span class="font-heading font-800 text-xl" style="font-weight: 800">
            Food<span style="color: #ff4d24">Rush</span>
          </span>
        </a>
        <div class="hidden md:flex flex-1 max-w-md relative">
          <i
            data-lucide="search"
            class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-4 h-4"
          ></i>
          <input
            id="nav-search"
            type="text"
            placeholder="Search restaurants, dishes&hellip;"
            class="input-field pl-9 pr-4 py-2.5 text-sm rounded-2xl"
          />
        </div>
        <div class="flex items-center gap-2">
          <a
            href="customer/history-reviews.html"
            class="btn btn-ghost btn-icon hidden md:flex"
            title="Orders"
          >
            <i data-lucide="package" class="w-5 h-5"></i>
          </a>
          <a
            href="merchant/merchant-dashboard.html"
            class="btn btn-ghost btn-sm hidden md:flex gap-1.5"
            title="Merchant"
          >
            <i data-lucide="store" class="w-4 h-4"></i>
            <span class="hidden lg:inline">Merchant</span>
          </a>
          <button
            id="cart-btn"
            class="btn btn-ghost btn-icon relative"
            title="Cart"
          >
            <i data-lucide="shopping-bag" class="w-5 h-5"></i>
            <span
              id="cart-count"
              class="absolute -top-1 -right-1 w-5 h-5 bg-primary text-white text-[10px] font-bold rounded-full flex items-center justify-center"
              >3</span
            >
          </button>
          <a
            href="auth.html"
            class="btn btn-primary btn-sm hidden md:flex"
            id="signin-btn"
          >
            <i data-lucide="log-in" class="w-4 h-4"></i> Sign In
          </a>
          <a
            href="customer/profile.html"
            class="ml-1 w-9 h-9 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-primary/30 hidden md:block"
          >
            <img
              src="https://api.dicebear.com/7.x/avataaars/svg?seed=user123"
              alt="Profile"
              class="w-full h-full object-cover"
            />
          </a>
          <a
            href="auth.html"
            class="btn btn-ghost btn-icon md:hidden"
            title="Sign In"
          >
            <i data-lucide="log-in" class="w-5 h-5"></i>
          </a>
        </div>
      </div>
      <div class="md:hidden px-4 pb-3">
        <div class="relative">
          <i
            data-lucide="search"
            class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-4 h-4"
          ></i>
          <input
            id="mobile-search"
            type="text"
            placeholder="Search restaurants, dishes&hellip;"
            class="input-field pl-9 py-2.5 text-sm rounded-2xl"
          />
        </div>
      </div>
    </nav>

    <!-- ============================== HERO ============================== -->
    <section
      class="hero-section min-h-[520px] md:min-h-[620px] flex items-center relative py-16 md:py-0"
    >
      <div
        class="absolute top-10 right-1/4 w-64 h-64 rounded-full opacity-10"
        style="background: radial-gradient(circle, #ff4d24, transparent)"
      ></div>
      <div
        class="absolute bottom-0 left-1/3 w-96 h-40 rounded-full opacity-5"
        style="background: radial-gradient(circle, #ff8c6b, transparent)"
      ></div>
      <div
        class="container-app relative z-10 grid md:grid-cols-2 gap-12 items-center"
      >
        <div class="text-white animate-fade-in-up">
          <div class="badge badge-primary mb-5 text-sm px-4 py-2">
            <i data-lucide="zap" class="w-3.5 h-3.5 mr-1"></i>
            30-Minute Delivery Guarantee
          </div>
          <h1
            class="font-heading font-black text-4xl md:text-5xl lg:text-6xl leading-tight mb-6"
          >
            Delicious Food,<br />
            <span class="text-gradient">Delivered Fast</span>
          </h1>
          <p class="text-gray-300 text-lg mb-8 max-w-md">
            Order from 500+ top-rated restaurants and get fresh meals delivered
            to your door in under 30 minutes.
          </p>
          <div class="bg-white rounded-3xl p-2 flex gap-2 shadow-lg max-w-lg">
            <div class="flex-1 flex items-center gap-2 pl-3">
              <i
                data-lucide="map-pin"
                class="text-primary w-5 h-5 flex-shrink-0"
              ></i>
              <input
                id="hero-location"
                type="text"
                placeholder="Enter your delivery address&hellip;"
                class="flex-1 text-secondary text-sm outline-none font-medium"
              />
            </div>
            <button
              id="hero-search-btn"
              class="btn btn-primary btn-lg"
              onclick="
                document
                  .getElementById('dishes')
                  .scrollIntoView({ behavior: 'smooth' })
              "
            >
              <i data-lucide="search" class="w-4 h-4"></i>
              Find Food
            </button>
          </div>
          <div class="flex gap-8 mt-10">
            <div>
              <div class="font-heading font-black text-2xl text-white">
                500+
              </div>
              <div class="text-gray-400 text-sm">Restaurants</div>
            </div>
            <div>
              <div class="font-heading font-black text-2xl text-white">
                50K+
              </div>
              <div class="text-gray-400 text-sm">Happy Customers</div>
            </div>
            <div>
              <div class="font-heading font-black text-2xl text-white">
                4.9&#x2605;
              </div>
              <div class="text-gray-400 text-sm">Average Rating</div>
            </div>
          </div>
        </div>
        <div
          class="hidden md:grid grid-cols-2 gap-4 animate-fade-in-up animate-delay-2"
        >
          <div class="space-y-4 mt-8">
            <div class="restaurant-card overflow-hidden rounded-2xl shadow-lg">
              <img
                src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=300&h=200&fit=crop"
                alt="Pizza"
                class="w-full h-36 object-cover"
              />
              <div class="p-3">
                <div class="font-semibold text-sm">Margherita Pizza</div>
                <div class="text-xs text-gray-500">Italian &bull; 25 min</div>
              </div>
            </div>
            <div class="restaurant-card overflow-hidden rounded-2xl shadow-lg">
              <img
                src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=300&h=200&fit=crop"
                alt="Salad"
                class="w-full h-36 object-cover"
              />
              <div class="p-3">
                <div class="font-semibold text-sm">Garden Fresh Bowl</div>
                <div class="text-xs text-gray-500">Healthy &bull; 20 min</div>
              </div>
            </div>
          </div>
          <div class="space-y-4">
            <div class="restaurant-card overflow-hidden rounded-2xl shadow-lg">
              <img
                src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=300&h=200&fit=crop"
                alt="Burger"
                class="w-full h-36 object-cover"
              />
              <div class="p-3">
                <div class="font-semibold text-sm">Classic Smash Burger</div>
                <div class="text-xs text-gray-500">American &bull; 22 min</div>
              </div>
            </div>
            <div class="bg-primary rounded-2xl p-4 text-white shadow-lg">
              <div class="text-3xl mb-2">
                <i data-lucide="gift" class="w-8 h-8"></i>
              </div>
              <div class="font-bold text-sm">First Order 20% OFF</div>
              <div class="text-xs text-white/70 mt-1">Use code: RUSH20</div>
              <div
                class="mt-3 bg-white/20 rounded-lg px-3 py-1.5 text-xs font-bold inline-block"
              >
                RUSH20
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ============================== CATEGORIES ============================== -->
    <section class="py-10 bg-white border-b border-gray-100 category-section">
      <div class="container-app">
        <div class="flex items-center justify-between mb-6">
          <h2 class="font-heading font-bold text-xl">Browse Categories</h2>
          <button
            class="text-primary text-sm font-semibold flex items-center gap-1 hover:underline"
          >
            View all <i data-lucide="chevron-right" class="w-4 h-4"></i>
          </button>
        </div>
        <div
          id="categories-slider"
          class="flex gap-3 overflow-x-auto pb-2 scroll-smooth"
          style="-ms-overflow-style: none; scrollbar-width: none"
        >
          <a href="index.php" class="category-chip active flex-shrink-0">
            <span class="text-2xl leading-none">&#x1F37D;&#xFE0F;</span>
            <span class="label">All</span>
          </a>
<?php
$categorySql = "SELECT id, name FROM categories ORDER BY name";
$categoryResult = $conn->query($categorySql);
if ($categoryResult && $categoryResult->num_rows > 0) {
    while ($category = $categoryResult->fetch_assoc()) {
        $name = $category['name'];
        echo '<a href="category.php?id=' . (int)$category['id'] . '" class="category-chip flex-shrink-0">';
        echo '<span class="text-2xl leading-none">🍽️</span>';
        echo '<span class="label">' . htmlspecialchars($name) . '</span>';
        echo '</a>';
    }
} else {
    echo '<div class="text-sm text-gray-500">No categories found.</div>';
}
?>
        </div>
      </div>
    </section>

    <!-- ============================== FILTER BAR ============================== -->
    <section class="py-5 bg-white">
      <div
        class="container-app flex items-center gap-3 overflow-x-auto pb-1"
        style="-ms-overflow-style: none; scrollbar-width: none"
      >
        <span class="text-sm font-semibold text-gray-500 flex-shrink-0">Sort by:</span>
        <button
          class="sort-btn btn btn-outline btn-sm active"
          data-sort="price-asc"
          onclick="sortDishes('price-asc')"
        >
          Price: Low to High
        </button>
        <button
          class="sort-btn btn btn-outline btn-sm"
          data-sort="price-desc"
          onclick="sortDishes('price-desc')"
        >
          Price: High to Low
        </button>
        <button
          class="sort-btn btn btn-outline btn-sm"
          data-sort="sold-desc"
          onclick="sortDishes('sold-desc')"
        >
          Best Sellers
        </button>
      </div>
    </section>

    <!-- ============================== POPULAR DISHES ============================== -->
    <section id="dishes" class="py-10">
      <div class="container-app">
        <div class="flex items-center justify-between mb-6">
          <div>
            <p class="section-label mb-1">Curated For You</p>
            <h2 class="section-title">
              Popular <span class="text-gradient">Dishes</span>
            </h2>
          </div>
          <button class="btn btn-outline btn-sm">
            View All <i data-lucide="arrow-right" class="w-4 h-4"></i>
          </button>
        </div>
        <div
          id="dish-grid"
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5"
        >
<?php
$sql = "SELECT mi.id, mi.name, mi.description, mi.image_url, mi.price, 'Sample Restaurant' as restaurant_name FROM menu_items mi ORDER BY mi.id LIMIT 12";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($dish = $result->fetch_assoc()) {
        $image = $dish['image_url'] ?: 'https://via.placeholder.com/500x220?text=No+Image';
        $sold = rand(10, 250);
        $price = number_format((int)$dish['price'], 0, ',', '.') . ' ₫';
        echo '<div class="dish-card block animate-fade-in-up border border-gray-200 rounded-2xl bg-white overflow-hidden" data-price="' . (int)$dish['price'] . '" data-sold="' . $sold . '">
            <div class="banner-wrap">
                <img class="banner" src="' . htmlspecialchars($image) . '" alt="' . htmlspecialchars($dish['name']) . '" />
            </div>
            <div class="p-4">
                <h3 class="font-heading font-bold text-base mb-1">' . htmlspecialchars($dish['name']) . '</h3>
                <p class="text-sm text-gray-500 mb-2">' . htmlspecialchars($dish['restaurant_name']) . '</p>
                <p class="text-sm text-gray-600 mb-3">' . htmlspecialchars(substr($dish['description'] ?: '', 0, 50)) . '...</p>
                <div class="text-xs text-gray-400 mb-3">Sold ' . $sold . ' times</div>
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-primary">' . $price . '</span>
                    <button class="btn btn-primary btn-sm" onclick="addToCart(' . $dish['id'] . ')">Add to Cart</button>
                </div>
            </div>
        </div>';

    }
} else {
    echo '<p class="col-span-full text-center text-gray-500">No dishes available at the moment.</p>';
}
?>
          <!-- <a
            href="customer/restaurant.html"
            class="restaurant-card block animate-fade-in-up"
            data-category="pizza"
          >
            <div class="banner-wrap">
              <img
                class="banner"
                src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=500&h=220&fit=crop"
                alt="The Pizza Lab"
              />
            </div>
            <div class="p-4">
              <div class="flex items-center gap-2 mb-1">
                <img
                  src="https://api.dicebear.com/7.x/shapes/svg?seed=pizza&backgroundColor=FF4D24"
                  alt=""
                  class="w-8 h-8 rounded-full"
                />
                <h3 class="font-heading font-bold text-base">The Pizza Lab</h3>
                <span class="ml-auto badge badge-success text-xs">Open</span>
              </div>
              <p class="text-sm text-gray-500 mb-3">
                &#x1F355; Italian &bull; Pizza &bull; Pasta
              </p>
              <div class="flex items-center justify-between text-sm">
                <span class="flex items-center gap-1 font-semibold"
                  ><i
                    data-lucide="star"
                    class="w-3.5 h-3.5 text-amber-400 fill-amber-400"
                  ></i
                  >4.8 (1.2k)</span
                >
                <span class="text-gray-400">&middot;</span>
                <span class="text-gray-500"
                  ><i data-lucide="clock" class="w-3 h-3 inline"></i>
                  20&ndash;35 min</span
                >
                <span class="text-gray-400">&middot;</span>
                <span class="text-primary font-semibold">Free delivery</span>
              </div>
            </div>
          </a>

          <a
            href="customer/restaurant.html"
            class="restaurant-card block animate-fade-in-up animate-delay-1"
            data-category="burger"
          >
            <div class="relative">
              <div class="banner-wrap">
                <img
                  class="banner"
                  src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500&h=220&fit=crop"
                  alt="Smash & Go"
                />
              </div>
              <div class="absolute top-3 left-3">
                <span class="badge" style="background: #ff4d24; color: #fff"
                  ><i data-lucide="trending-up" class="w-3 h-3 mr-0.5"></i>
                  Trending</span
                >
              </div>
            </div>
            <div class="p-4">
              <div class="flex items-center gap-2 mb-1">
                <img
                  src="https://api.dicebear.com/7.x/shapes/svg?seed=burger&backgroundColor=1A1A1A"
                  alt=""
                  class="w-8 h-8 rounded-full"
                />
                <h3 class="font-heading font-bold text-base">Smash &amp; Go</h3>
                <span class="ml-auto badge badge-success text-xs">Open</span>
              </div>
              <p class="text-sm text-gray-500 mb-3">
                &#x1F354; American &bull; Burgers &bull; Sides
              </p>
              <div class="flex items-center justify-between text-sm">
                <span class="flex items-center gap-1 font-semibold"
                  ><i
                    data-lucide="star"
                    class="w-3.5 h-3.5 text-amber-400 fill-amber-400"
                  ></i
                  >4.9 (3.4k)</span
                >
                <span class="text-gray-400">&middot;</span>
                <span class="text-gray-500"
                  ><i data-lucide="clock" class="w-3 h-3 inline"></i>
                  15&ndash;25 min</span
                >
                <span class="text-gray-400">&middot;</span>
                <span class="text-gray-500">$1.50 fee</span>
              </div>
            </div>
          </a>

          <a
            href="customer/restaurant.html"
            class="restaurant-card block animate-fade-in-up animate-delay-2"
            data-category="sushi"
          >
            <div class="banner-wrap">
              <img
                class="banner"
                src="https://images.unsplash.com/photo-1553621042-f6e147245754?w=500&h=220&fit=crop"
                alt="Sakura Sushi"
              />
            </div>
            <div class="p-4">
              <div class="flex items-center gap-2 mb-1">
                <img
                  src="https://api.dicebear.com/7.x/shapes/svg?seed=sushi&backgroundColor=E03D18"
                  alt=""
                  class="w-8 h-8 rounded-full"
                />
                <h3 class="font-heading font-bold text-base">Sakura Sushi</h3>
                <span class="ml-auto badge badge-success text-xs">Open</span>
              </div>
              <p class="text-sm text-gray-500 mb-3">
                &#x1F371; Japanese &bull; Sushi &bull; Ramen
              </p>
              <div class="flex items-center justify-between text-sm">
                <span class="flex items-center gap-1 font-semibold"
                  ><i
                    data-lucide="star"
                    class="w-3.5 h-3.5 text-amber-400 fill-amber-400"
                  ></i
                  >4.7 (890)</span
                >
                <span class="text-gray-400">&middot;</span>
                <span class="text-gray-500"
                  ><i data-lucide="clock" class="w-3 h-3 inline"></i>
                  30&ndash;45 min</span
                >
                <span class="text-gray-400">&middot;</span>
                <span class="text-primary font-semibold">Free delivery</span>
              </div>
            </div>
          </a>

          <a
            href="customer/restaurant.html"
            class="restaurant-card block animate-fade-in-up animate-delay-3"
            data-category="healthy"
          >
            <div class="relative">
              <div class="banner-wrap">
                <img
                  class="banner"
                  src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=500&h=220&fit=crop"
                  alt="Green Bowl"
                />
              </div>
              <div class="absolute top-3 left-3">
                <span class="badge badge-success"
                  ><i data-lucide="sparkles" class="w-3 h-3 mr-0.5"></i>
                  New</span
                >
              </div>
            </div>
            <div class="p-4">
              <div class="flex items-center gap-2 mb-1">
                <img
                  src="https://api.dicebear.com/7.x/shapes/svg?seed=healthy&backgroundColor=22C55E"
                  alt=""
                  class="w-8 h-8 rounded-full"
                />
                <h3 class="font-heading font-bold text-base">Green Bowl Co.</h3>
                <span class="ml-auto badge badge-warning text-xs">Busy</span>
              </div>
              <p class="text-sm text-gray-500 mb-3">
                &#x1F957; Healthy &bull; Bowls &bull; Smoothies
              </p>
              <div class="flex items-center justify-between text-sm">
                <span class="flex items-center gap-1 font-semibold"
                  ><i
                    data-lucide="star"
                    class="w-3.5 h-3.5 text-amber-400 fill-amber-400"
                  ></i
                  >4.6 (420)</span
                >
                <span class="text-gray-400">&middot;</span>
                <span class="text-gray-500"
                  ><i data-lucide="clock" class="w-3 h-3 inline"></i>
                  20&ndash;30 min</span
                >
                <span class="text-gray-400">&middot;</span>
                <span class="text-primary font-semibold">Free delivery</span>
              </div>
            </div>
          </a>

          <a
            href="customer/restaurant.html"
            class="restaurant-card block animate-fade-in-up animate-delay-4"
            data-category="chicken"
          >
            <div class="banner-wrap">
              <img
                class="banner"
                src="https://images.unsplash.com/photo-1527477396000-e27163b481c2?w=500&h=220&fit=crop"
                alt="Crispy Wings"
              />
            </div>
            <div class="p-4">
              <div class="flex items-center gap-2 mb-1">
                <img
                  src="https://api.dicebear.com/7.x/shapes/svg?seed=chicken&backgroundColor=F59E0B"
                  alt=""
                  class="w-8 h-8 rounded-full"
                />
                <h3 class="font-heading font-bold text-base">
                  Crispy Wing House
                </h3>
                <span class="ml-auto badge badge-success text-xs">Open</span>
              </div>
              <p class="text-sm text-gray-500 mb-3">
                &#x1F357; American &bull; Wings &bull; Fried Chicken
              </p>
              <div class="flex items-center justify-between text-sm">
                <span class="flex items-center gap-1 font-semibold"
                  ><i
                    data-lucide="star"
                    class="w-3.5 h-3.5 text-amber-400 fill-amber-400"
                  ></i
                  >4.8 (2.1k)</span
                >
                <span class="text-gray-400">&middot;</span>
                <span class="text-gray-500"
                  ><i data-lucide="clock" class="w-3 h-3 inline"></i>
                  18&ndash;28 min</span
                >
                <span class="text-gray-400">&middot;</span>
                <span class="text-gray-500">$0.99 fee</span>
              </div>
            </div>
          </a>

          <a
            href="customer/restaurant.html"
            class="restaurant-card block animate-fade-in-up animate-delay-5"
            data-category="noodles"
          >
            <div class="banner-wrap">
              <img
                class="banner"
                src="https://images.unsplash.com/photo-1569050467447-ce54b3bbc37d?w=500&h=220&fit=crop"
                alt="Pho House"
              />
            </div>
            <div class="p-4">
              <div class="flex items-center gap-2 mb-1">
                <img
                  src="https://api.dicebear.com/7.x/shapes/svg?seed=pho&backgroundColor=3B82F6"
                  alt=""
                  class="w-8 h-8 rounded-full"
                />
                <h3 class="font-heading font-bold text-base">Pho &amp; Co.</h3>
                <span class="ml-auto badge badge-success text-xs">Open</span>
              </div>
              <p class="text-sm text-gray-500 mb-3">
                &#x1F35C; Vietnamese &bull; Noodles &bull; Soup
              </p>
              <div class="flex items-center justify-between text-sm">
                <span class="flex items-center gap-1 font-semibold"
                  ><i
                    data-lucide="star"
                    class="w-3.5 h-3.5 text-amber-400 fill-amber-400"
                  ></i
                  >4.9 (5.6k)</span
                >
                <span class="text-gray-400">&middot;</span>
                <span class="text-gray-500"
                  ><i data-lucide="clock" class="w-3 h-3 inline"></i>
                  25&ndash;40 min</span
                >
                <span class="text-gray-400">&middot;</span>
                <span class="text-primary font-semibold">Free delivery</span>
              </div>
            </div>
          </a>

          <a
            href="customer/restaurant.html"
            class="restaurant-card block animate-fade-in-up"
            data-category="dessert"
          >
            <div class="banner-wrap">
              <img
                class="banner"
                src="https://images.unsplash.com/photo-1551024601-bec78aea704b?w=500&h=220&fit=crop"
                alt="Sweet Heaven"
              />
            </div>
            <div class="p-4">
              <div class="flex items-center gap-2 mb-1">
                <img
                  src="https://api.dicebear.com/7.x/shapes/svg?seed=sweet&backgroundColor=EC4899"
                  alt=""
                  class="w-8 h-8 rounded-full"
                />
                <h3 class="font-heading font-bold text-base">Sweet Heaven</h3>
                <span class="ml-auto badge badge-success text-xs">Open</span>
              </div>
              <p class="text-sm text-gray-500 mb-3">
                &#x1F370; Desserts &bull; Cakes &bull; Ice Cream
              </p>
              <div class="flex items-center justify-between text-sm">
                <span class="flex items-center gap-1 font-semibold"
                  ><i
                    data-lucide="star"
                    class="w-3.5 h-3.5 text-amber-400 fill-amber-400"
                  ></i
                  >4.7 (780)</span
                >
                <span class="text-gray-400">&middot;</span>
                <span class="text-gray-500"
                  ><i data-lucide="clock" class="w-3 h-3 inline"></i>
                  20&ndash;30 min</span
                >
                <span class="text-gray-400">&middot;</span>
                <span class="text-gray-500">$1.00 fee</span>
              </div>
            </div>
          </a>

          <a
            href="customer/restaurant.html"
            class="restaurant-card block animate-fade-in-up animate-delay-1"
            data-category="tacos"
          >
            <div class="relative">
              <div class="banner-wrap">
                <img
                  class="banner"
                  src="https://images.unsplash.com/photo-1599974579688-8dbdd335c77f?w=500&h=220&fit=crop"
                  alt="Loco Tacos"
                />
              </div>
              <div class="absolute top-3 left-3">
                <span class="badge" style="background: #f59e0b; color: #fff">
                  <i data-lucide="flame" class="w-3 h-3 mr-0.5"></i> Spicy Favs
                </span>
              </div>
            </div>
            <div class="p-4">
              <div class="flex items-center gap-2 mb-1">
                <img
                  src="https://api.dicebear.com/7.x/shapes/svg?seed=tacos&backgroundColor=F97316"
                  alt=""
                  class="w-8 h-8 rounded-full"
                />
                <h3 class="font-heading font-bold text-base">Loco Tacos</h3>
                <span class="ml-auto badge badge-success text-xs">Open</span>
              </div>
              <p class="text-sm text-gray-500 mb-3">
                &#x1F32E; Mexican &bull; Tacos &bull; Burritos
              </p>
              <div class="flex items-center justify-between text-sm">
                <span class="flex items-center gap-1 font-semibold"
                  ><i
                    data-lucide="star"
                    class="w-3.5 h-3.5 text-amber-400 fill-amber-400"
                  ></i
                  >4.6 (1.5k)</span
                >
                <span class="text-gray-400">&middot;</span>
                <span class="text-gray-500"
                  ><i data-lucide="clock" class="w-3 h-3 inline"></i>
                  15&ndash;25 min</span
                >
                <span class="text-gray-400">&middot;</span>
                <span class="text-primary font-semibold">Free delivery</span>
              </div>
            </div>
          </a> -->
        </div>
      </div>
    </section>

    <!-- ============================== HOW IT WORKS ============================== -->
    <section class="py-16 bg-white">
      <div class="container-app text-center">
        <p class="section-label mb-2">Simple Process</p>
        <h2 class="section-title mb-4">
          How <span class="text-gradient">FoodRush</span> Works
        </h2>
        <p class="text-gray-500 max-w-xl mx-auto mb-12">
          Three easy steps to get your favorite food delivered fresh and hot to
          your doorstep.
        </p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
          <div
            class="hidden md:block absolute top-12 left-1/4 right-1/4 h-0.5 bg-gradient-to-r from-primary to-primary-light"
          ></div>
          <div class="text-center animate-fade-in-up">
            <div
              class="w-24 h-24 mx-auto rounded-3xl bg-primary/10 flex items-center justify-center mb-5 transition-transform hover:scale-110"
            >
              <i data-lucide="search" class="w-10 h-10 text-primary"></i>
            </div>
            <div class="font-heading font-bold text-lg mb-2">
              Choose Restaurant
            </div>
            <p class="text-gray-500 text-sm">
              Browse hundreds of local restaurants and discover your next
              favorite meal
            </p>
          </div>
          <div class="text-center animate-fade-in-up animate-delay-2">
            <div
              class="w-24 h-24 mx-auto rounded-3xl bg-primary/10 flex items-center justify-center mb-5 transition-transform hover:scale-110"
            >
              <i data-lucide="shopping-cart" class="w-10 h-10 text-primary"></i>
            </div>
            <div class="font-heading font-bold text-lg mb-2">
              Place Your Order
            </div>
            <p class="text-gray-500 text-sm">
              Customize your food, apply vouchers, and securely choose your
              payment method
            </p>
          </div>
          <div class="text-center animate-fade-in-up animate-delay-4">
            <div
              class="w-24 h-24 mx-auto rounded-3xl bg-primary/10 flex items-center justify-center mb-5 transition-transform hover:scale-110"
            >
              <i data-lucide="bike" class="w-10 h-10 text-primary"></i>
            </div>
            <div class="font-heading font-bold text-lg mb-2">Fast Delivery</div>
            <p class="text-gray-500 text-sm">
              Track your order in real-time and enjoy fresh food delivered in
              under 30 minutes
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- ============================== APP DOWNLOAD ============================== -->
    <section
      class="py-12"
      style="background: linear-gradient(135deg, #1a1a1a 0%, #2d1810 100%)"
    >
      <div class="container-app grid md:grid-cols-2 gap-10 items-center">
        <div class="text-white">
          <p class="section-label mb-3" style="color: #ff6b4a">Mobile App</p>
          <h2 class="font-heading font-black text-3xl md:text-4xl mb-4">
            Order Even Faster<br />On Our App
          </h2>
          <p class="text-gray-300 mb-8">
            Get exclusive app-only deals, faster reordering, and real-time push
            notifications for your delivery status.
          </p>
          <div class="flex flex-wrap gap-3">
            <button
              class="flex items-center gap-3 bg-white text-secondary px-5 py-3 rounded-2xl font-semibold text-sm hover:bg-gray-100 transition"
            >
              <i data-lucide="apple" class="w-6 h-6"></i>
              <div class="text-left">
                <div class="text-xs text-gray-500">Download on the</div>
                <div class="font-bold">App Store</div>
              </div>
            </button>
            <button
              class="flex items-center gap-3 bg-white text-secondary px-5 py-3 rounded-2xl font-semibold text-sm hover:bg-gray-100 transition"
            >
              <i data-lucide="play-circle" class="w-6 h-6"></i>
              <div class="text-left">
                <div class="text-xs text-gray-500">Get it on</div>
                <div class="font-bold">Google Play</div>
              </div>
            </button>
          </div>
        </div>
        <div class="flex justify-center md:justify-end gap-4 items-center">
          <div
            class="w-20 h-20 rounded-3xl bg-white/10 flex items-center justify-center flex-shrink-0"
          >
            <i data-lucide="smartphone" class="w-10 h-10 text-white"></i>
          </div>
          <div class="space-y-3 self-center">
            <div class="glass-dark rounded-2xl p-4 text-white text-sm w-44">
              <div class="flex items-center gap-2 mb-1">
                <i
                  data-lucide="check-circle"
                  class="w-4 h-4 text-green-400"
                ></i>
                Order confirmed!
              </div>
              <div class="text-gray-400 text-xs">
                Your pizza is being prepared&hellip;
              </div>
            </div>
            <div class="glass-dark rounded-2xl p-4 text-white text-sm w-44">
              <div class="flex items-center gap-2 mb-1">
                <i data-lucide="bike" class="w-4 h-4 text-primary"></i> On the
                way!
              </div>
              <div class="text-gray-400 text-xs">Arriving in 12 minutes</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ============================== FOOTER ============================== -->
    <footer class="footer py-12">
      <div class="container-app grid grid-cols-2 md:grid-cols-4 gap-8">
        <div class="col-span-2 md:col-span-1">
          <div class="flex items-center gap-2 mb-4">
            <div
              class="w-9 h-9 bg-primary rounded-xl flex items-center justify-center"
            >
              <i data-lucide="zap" class="text-white w-5 h-5"></i>
            </div>
            <span class="font-heading font-black text-xl text-white"
              >FoodRush</span
            >
          </div>
          <p class="text-gray-400 text-sm mb-4">
            Delivering happiness, one meal at a time.
          </p>
          <div class="flex gap-3">
            <a
              href="#"
              class="w-9 h-9 bg-white/10 rounded-xl flex items-center justify-center hover:bg-primary transition"
              ><i data-lucide="instagram" class="w-4 h-4 text-white"></i
            ></a>
            <a
              href="#"
              class="w-9 h-9 bg-white/10 rounded-xl flex items-center justify-center hover:bg-primary transition"
              ><i data-lucide="facebook" class="w-4 h-4 text-white"></i
            ></a>
            <a
              href="#"
              class="w-9 h-9 bg-white/10 rounded-xl flex items-center justify-center hover:bg-primary transition"
              ><i data-lucide="twitter" class="w-4 h-4 text-white"></i
            ></a>
          </div>
        </div>
        <div>
          <h4 class="font-heading font-bold text-white mb-4">Company</h4>
          <ul class="space-y-2 text-sm text-gray-400">
            <li>
              <a href="#" class="hover:text-primary transition">About Us</a>
            </li>
            <li>
              <a href="#" class="hover:text-primary transition">Careers</a>
            </li>
            <li><a href="#" class="hover:text-primary transition">Press</a></li>
            <li><a href="#" class="hover:text-primary transition">Blog</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-heading font-bold text-white mb-4">For Business</h4>
          <ul class="space-y-2 text-sm text-gray-400">
            <li>
              <a
                href="merchant/merchant-dashboard.html"
                class="hover:text-primary transition"
                >Become a Partner</a
              >
            </li>
            <li>
              <a
                href="admin/admin-panel.html"
                class="hover:text-primary transition"
                >Admin Panel</a
              >
            </li>
            <li>
              <a href="#" class="hover:text-primary transition">Advertise</a>
            </li>
          </ul>
        </div>
        <div>
          <h4 class="font-heading font-bold text-white mb-4">Help</h4>
          <ul class="space-y-2 text-sm text-gray-400">
            <li><a href="#" class="hover:text-primary transition">FAQ</a></li>
            <li>
              <a href="#" class="hover:text-primary transition">Contact Us</a>
            </li>
            <li>
              <a href="#" class="hover:text-primary transition"
                >Privacy Policy</a
              >
            </li>
            <li>
              <a href="#" class="hover:text-primary transition"
                >Terms of Service</a
              >
            </li>
          </ul>
        </div>
      </div>
      <div
        class="container-app mt-10 pt-6 border-t border-white/10 flex flex-col md:flex-row items-center justify-between gap-3 text-sm text-gray-500"
      >
        <span>&copy; 2026 FoodRush. All rights reserved.</span>
        <span
          >Made with
          <i
            data-lucide="heart"
            class="w-4 h-4 inline text-primary fill-primary"
          ></i>
          for hungry people everywhere</span
        >
      </div>
    </footer>

    <!-- ============================== MOBILE BOTTOM NAV ============================== -->
    <nav class="bottom-nav">
      <a href="index.html" class="bottom-nav-item active" id="bnav-home">
        <i data-lucide="home" class="nav-icon"></i>
        <span>Home</span>
      </a>
      <a
        href="customer/history-reviews.html"
        class="bottom-nav-item"
        id="bnav-orders"
      >
        <i data-lucide="package" class="nav-icon"></i>
        <span>Orders</span>
      </a>
      <a
        href="customer/checkout.html"
        class="bottom-nav-item"
        id="bnav-cart"
        style="color: var(--primary)"
      >
        <div class="relative">
          <i
            data-lucide="shopping-bag"
            class="nav-icon"
            style="color: var(--primary)"
          ></i>
          <span
            class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-primary text-white text-[9px] font-black rounded-full flex items-center justify-center"
            >3</span
          >
        </div>
        <span style="color: var(--primary)">Cart</span>
      </a>
      <a href="customer/profile.html" class="bottom-nav-item" id="bnav-profile">
        <i data-lucide="user" class="nav-icon"></i>
        <span>Profile</span>
      </a>
      <a href="auth.html" class="bottom-nav-item" id="bnav-signin">
        <i data-lucide="log-in" class="nav-icon"></i>
        <span>Sign In</span>
      </a>
    </nav>

    <script>
      lucide.createIcons();
      window.addEventListener("scroll", () => {
        document
          .getElementById("navbar")
          .classList.toggle("scrolled", window.scrollY > 10);
      });
      function filterCategory(el, cat) {
        document
          .querySelectorAll(".category-chip")
          .forEach((c) => c.classList.remove("active"));
        el.classList.add("active");
        document
          .querySelectorAll("#restaurant-grid [data-category]")
          .forEach((card) => {
            card.style.display =
              cat === "all" || card.dataset.category === cat ? "block" : "none";
          });
      }
      function sortDishes(order) {
        const grid = document.getElementById('dish-grid');
        if (!grid) return;
        const cards = Array.from(grid.querySelectorAll('.dish-card'));
        const sorted = cards.sort((a, b) => {
          const aPrice = parseInt(a.dataset.price || '0', 10);
          const bPrice = parseInt(b.dataset.price || '0', 10);
          const aSold = parseInt(a.dataset.sold || '0', 10);
          const bSold = parseInt(b.dataset.sold || '0', 10);
          if (order === 'price-asc') return aPrice - bPrice;
          if (order === 'price-desc') return bPrice - aPrice;
          if (order === 'sold-desc') return bSold - aSold;
          return 0;
        });
        sorted.forEach((card) => grid.appendChild(card));
      }
      document.getElementById("cart-btn").addEventListener("click", () => {
        window.location.href = "customer/checkout.html";
      });
    </script>
    <script src="js/navbar.js"></script>
  </body>
</html>
