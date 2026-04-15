<!doctype html>
<?php
include '../includes/db-connect.php';
?>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Checkout | FoodRush</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
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
            primary: { DEFAULT: "#FF4D24", light: "#FF6B4A", dark: "#E03D18" },
            secondary: "#1A1A1A",
          },
          fontFamily: {
            sans: ["Inter", "sans-serif"],
            heading: ["Poppins", "sans-serif"],
          },
        },
      };
    </script>
  <link rel="stylesheet" href="../assets/css/custom.css" />
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>
<body class="bg-[#F8F9FA]">
  <!-- Navbar -->
  <nav class="bg-white border-b border-gray-200 sticky top-0 z-30">
    <div class="container-app flex items-center justify-between h-16">
      <a href="../index.php" class="flex items-center gap-2">
        <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
          <i data-lucide="zap" class="text-white w-4 h-4"></i>
        </div>
        <span class="font-heading font-bold text-lg">Food<span style="color:#FF4D24">Rush</span></span>
      </a>
      <button onclick="window.history.back()" class="btn btn-ghost btn-sm">← Back</button>
    </div>
  </nav>

  <div class="container-app py-8">
    <h1 class="font-heading font-bold text-3xl mb-8">Your Cart</h1>

    <div class="grid md:grid-cols-3 gap-8">
      <!-- Cart Items -->
      <div class="md:col-span-2">
        <div id="cart-items-container" class="bg-white rounded-2xl p-6 space-y-4">
          <!-- Items rendered by JS -->
        </div>
      </div>

      <!-- Order Summary -->
      <div class="bg-white rounded-2xl p-6 h-fit sticky top-24">
        <h2 class="font-heading font-bold text-lg mb-4">Order Summary</h2>
        
        <div class="space-y-3 mb-6 pb-6 border-b">
          <div class="flex justify-between text-sm">
            <span class="text-gray-600">Subtotal</span>
            <span id="subtotal" class="font-semibold">$0.00</span>
          </div>
          <div class="flex justify-between text-sm">
            <span class="text-gray-600">Delivery</span>
            <span id="delivery" class="font-semibold">$3.00</span>
          </div>
          <div class="flex justify-between text-sm">
            <span class="text-gray-600">Tax</span>
            <span id="tax" class="font-semibold">$0.00</span>
          </div>
        </div>

        <div class="flex justify-between mb-6 text-lg">
          <span class="font-heading font-bold">Total</span>
          <span id="total" class="font-heading font-bold text-primary text-xl">$0.00</span>
        </div>

        <button onclick="proceedToPayment()" class="btn btn-primary w-full mb-3">
          <i data-lucide="credit-card" class="w-4 h-4"></i> Proceed to Payment
        </button>
        <a href="../index.php" class="btn btn-ghost w-full text-center">Continue Shopping</a>
      </div>
    </div>
  </div>

  <script src="../js/cart.js"></script>
  <script>
    lucide.createIcons();

    // Get menu items from DB based on cart IDs
    async function renderCartItems() {
      const cart = getCart();
      const container = document.getElementById('cart-items-container');

      if (cart.length === 0) {
        container.innerHTML = '<p class="text-center text-gray-500 py-8">Your cart is empty</p>';
        updateSummary();
        return;
      }

      // Fetch full item details from DB
      const ids = cart.map(item => item.id).join(',');
      
      try {
        const response = await fetch(`../api/get-items.php?ids=${ids}`);
        if (!response.ok) throw new Error('Network error');
        
        const items = await response.json();
        
        if (!Array.isArray(items)) throw new Error('Invalid data');
        
        const itemMap = {};
        items.forEach(item => {
          itemMap[item.id] = item;
        });

        container.innerHTML = cart.map(cartItem => {
          const dbItem = itemMap[cartItem.id] || {};
          const img = dbItem.image_url ? `../uploads/${dbItem.image_url}` : 'https://via.placeholder.com/80';
          return `
            <div class="flex gap-4 pb-4 border-b last:border-b-0">
              <img src="${img}" 
                   alt="${cartItem.name}" 
                   class="w-24 h-24 object-cover rounded-lg flex-shrink-0" />
              <div class="flex-1">
                <div class="flex justify-between items-start mb-2">
                  <div>
                    <h3 class="font-semibold text-base">${cartItem.name}</h3>
                    <p class="text-sm text-gray-500">${dbItem.description || 'Delicious food'}</p>
                  </div>
                  <button onclick="removeItem(${cartItem.id})" 
                          class="text-gray-400 hover:text-red-500 transition flex-shrink-0">
                    <i data-lucide="x" class="w-5 h-5"></i>
                  </button>
                </div>
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2 bg-gray-100 rounded-lg p-1">
                    <button onclick="updateQty(${cartItem.id}, -1)" 
                            class="w-8 h-8 flex items-center justify-center hover:bg-primary hover:text-white rounded transition">−</button>
                    <input type="number" value="${cartItem.qty}" min="1" max="999"
                           onchange="setQty(${cartItem.id}, this.value)"
                           class="w-12 text-center border-0 bg-transparent font-semibold" />
                    <button onclick="updateQty(${cartItem.id}, 1)" 
                            class="w-8 h-8 flex items-center justify-center hover:bg-primary hover:text-white rounded transition">+</button>
                  </div>
                  <div class="text-right">
                    <div class="font-semibold text-primary text-lg">$${(cartItem.price * cartItem.qty).toFixed(2)}</div>
                    <div class="text-xs text-gray-500">@$${cartItem.price.toFixed(2)} each</div>
                  </div>
                </div>
              </div>
            </div>
          `;
        }).join('');

        lucide.createIcons();
        updateSummary();
      } catch (error) {
        console.error('Error fetching items:', error);
        container.innerHTML = '<p class="text-red-500 py-4">⚠️ Error loading cart items. Please try again.</p>';
      }
    }

    function updateQty(id, delta) {
      changeCartQty(id, delta);
      renderCartItems();
    }

    function setQty(id, qty) {
      const cart = getCart();
      const item = cart.find(i => i.id === id);
      if (item) {
        item.qty = Math.max(1, parseInt(qty) || 1);
        saveCart(cart);
        syncCartBadge();
        renderCartItems();
      }
    }

    function removeItem(id) {
      removeFromCart(id);
      syncCartBadge();
      renderCartItems();
    }

    function updateSummary() {
      const cart = getCart();
      const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
      const delivery = cart.length > 0 ? 3.00 : 0;
      const tax = subtotal * 0.05;
      const total = subtotal + delivery + tax;

      document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
      document.getElementById('delivery').textContent = '$' + delivery.toFixed(2);
      document.getElementById('tax').textContent = '$' + tax.toFixed(2);
      document.getElementById('total').textContent = '$' + total.toFixed(2);
    }

    function proceedToPayment() {
      const cart = getCart();
      if (cart.length === 0) {
        alert('Your cart is empty');
        return;
      }
      // TODO: Implement payment gateway
      alert('Proceeding to payment... (Coming soon)');
      // window.location.href = 'payment.php';
    }

    renderCartItems();
  </script>
</body>
</html>
