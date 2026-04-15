// ============================================================
// js/cart.js  —  FoodRush Cart Logic
// Phụ trách: Bình (JavaScript)
// Chức năng:
//   1. Lưu giỏ hàng vào localStorage để đi xuyên trang
//   2. Tăng/giảm số lượng, xóa món trong giỏ
//   3. Hiển thị tổng tiền real-time
//   4. Đồng bộ badge số lượng trên navbar
//   5. Render giỏ hàng ở checkout.html
//   6. Validation form checkout trước khi đặt hàng
// ============================================================

// ============================================================
// PHẦN 1: QUẢN LÝ DỮ LIỆU GIỎ HÀNG (localStorage)
// ============================================================

const CART_KEY = "fr_cart"; // Key lưu trong localStorage

/** Lấy giỏ hàng hiện tại từ localStorage */
function getCart() {
  const raw = localStorage.getItem(CART_KEY);
  return raw ? JSON.parse(raw) : [];
}

/** Lưu giỏ hàng vào localStorage */
function saveCart(cart) {
  localStorage.setItem(CART_KEY, JSON.stringify(cart));
}

/** Thêm món vào giỏ hàng (ID từ menu_items table) */
function addToCart(id, name, price) {
  const cart = getCart();

  const existing = cart.find((i) => i.id === id);
  if (existing) {
    existing.qty++;
  } else {
    cart.push({ id, name, price, qty: 1 });
  }

  saveCart(cart);
  syncCartBadge();
  showAddedToast(name);
  return true;
}

/** Thay đổi số lượng món trong giỏ (delta: +1 hoặc -1) */
function changeCartQty(id, delta) {
  const cart = getCart();
  const idx = cart.findIndex((i) => i.id === id);
  if (idx === -1) return;

  cart[idx].qty += delta;
  if (cart[idx].qty <= 0) {
    cart.splice(idx, 1); // Xóa khỏi giỏ nếu qty = 0
  }

  saveCart(cart);
  syncCartBadge();
}

/** Xóa hẳn 1 món khỏi giỏ */
function removeFromCart(id) {
  const cart = getCart().filter((i) => i.id !== id);
  saveCart(cart);
  syncCartBadge();
}

/** Xóa toàn bộ giỏ hàng */
function clearCart() {
  localStorage.removeItem(CART_KEY);
  syncCartBadge();
}

/** Tính tổng tiền */
function getCartTotal() {
  return getCart().reduce((sum, i) => sum + i.price * i.qty, 0);
}

/** Tính tổng số lượng món */
function getCartCount() {
  return getCart().reduce((sum, i) => sum + i.qty, 0);
}

// ============================================================
// PHẦN 2: ĐỒNG BỘ BADGE SỐ LƯỢNG TRÊN NAVBAR
// ============================================================

/** Cập nhật badge số lượng trên tất cả các phần tử có id cart-count / cart-badge */
function syncCartBadge() {
  const count = getCartCount();

  // Badge ở index.html (id="cart-count")
  const badge1 = document.getElementById("cart-count");
  if (badge1) badge1.textContent = count;

  // Badge ở restaurant.html (id="cart-badge")
  const badge2 = document.getElementById("cart-badge");
  if (badge2) badge2.textContent = count;
}

// ============================================================
// PHẦN 3: TOAST THÔNG BÁO "ĐÃ THÊM VÀO GIỎ"
// ============================================================

function showAddedToast(itemName) {
  // Xóa toast cũ nếu đang hiển thị
  const old = document.getElementById("cart-toast");
  if (old) old.remove();

  const toast = document.createElement("div");
  toast.id = "cart-toast";
  toast.style.cssText = `
    position: fixed;
    bottom: 80px;
    left: 50%;
    transform: translateX(-50%);
    background: #1A1A1A;
    color: white;
    padding: 12px 20px;
    border-radius: 14px;
    font-size: 14px;
    font-weight: 600;
    z-index: 9999;
    box-shadow: 0 8px 24px rgba(0,0,0,0.25);
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
    animation: slideUpToast 0.3s ease;
  `;
  toast.innerHTML = `
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
         fill="none" stroke="#22C55E" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <polyline points="20 6 9 17 4 12"/>
    </svg>
    Đã thêm: <strong style="color:#FF4D24;margin-left:4px;">${itemName}</strong>
  `;

  // Thêm CSS animation nếu chưa có
  if (!document.getElementById("cart-toast-style")) {
    const s = document.createElement("style");
    s.id = "cart-toast-style";
    s.textContent = `
      @keyframes slideUpToast {
        from { opacity:0; transform:translateX(-50%) translateY(12px); }
        to   { opacity:1; transform:translateX(-50%) translateY(0); }
      }
    `;
    document.head.appendChild(s);
  }

  document.body.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = "0";
    toast.style.transition = "opacity 0.3s";
    setTimeout(() => toast.remove(), 300);
  }, 2000);
}

// ============================================================
// PHẦN 4: RENDER GIỎ HÀNG TRÊN TRANG RESTAURANT.HTML
// ============================================================

/**
 * Gọi hàm này ở restaurant.html để render giỏ hàng trực tiếp trên trang.
 * Thay thế logic cart cũ (dùng biến local) bằng localStorage.
 */
function renderRestaurantCart() {
  const cart = getCart();
  const list = document.getElementById("cart-list");
  const empty = document.getElementById("cart-empty");
  const summary = document.getElementById("cart-summary");

  if (!list) return; // Không có cart panel thì bỏ qua

  if (cart.length === 0) {
    list.classList.add("hidden");
    if (empty) empty.classList.remove("hidden");
    if (summary) summary.classList.add("hidden");
    return;
  }

  list.classList.remove("hidden");
  if (empty) empty.classList.add("hidden");
  if (summary) summary.classList.remove("hidden");

  // Render từng item
  list.innerHTML = cart
    .map(
      (item) => `
    <li class="flex items-center gap-3 text-sm" data-id="${item.id}">
      <span class="flex-1 font-medium truncate" title="${item.name}">${item.name}</span>
      <div class="qty-control">
        <button class="qty-btn text-sm"
          onclick="changeCartQty(${item.id}, -1); renderRestaurantCart();"
          aria-label="Giảm số lượng">&minus;</button>
        <span class="qty-val text-sm">${item.qty}</span>
        <button class="qty-btn text-sm"
          onclick="changeCartQty(${item.id}, 1); renderRestaurantCart();"
          aria-label="Tăng số lượng">+</button>
      </div>
      <span class="font-bold text-primary w-16 text-right">
        $${(item.price * item.qty).toFixed(2)}
      </span>
    </li>
  `,
    )
    .join("");

  // Cập nhật tổng tiền
  const total = getCartTotal();
  const subtotalEl = document.getElementById("subtotal");
  const totalEl = document.getElementById("total");
  if (subtotalEl) subtotalEl.textContent = `$${total.toFixed(2)}`;
  if (totalEl) totalEl.textContent = `$${total.toFixed(2)}`;

  syncCartBadge();
}

// ============================================================
// PHẦN 5: RENDER GIỎ HÀNG TRÊN TRANG CHECKOUT.HTML
// ============================================================

/**
 * Gọi hàm này ở checkout.html để hiện đúng các món từ localStorage.
 */
function renderCheckoutCart() {
  const cart = getCart();
  const itemsEl = document.getElementById("checkout-items");
  const subtotalEl = document.getElementById("checkout-subtotal");
  const totalEl = document.getElementById("checkout-total");
  const restaurantEl = document.getElementById("checkout-restaurant-name");

  if (!itemsEl) return;

  if (cart.length === 0) {
    itemsEl.innerHTML = `
      <div class="text-center py-8 text-gray-400">
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" class="mx-auto mb-3"
             fill="none" stroke="#D1D5DB" stroke-width="1.5" viewBox="0 0 24 24">
          <path d="M6 2 3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
          <line x1="3" y1="6" x2="21" y2="6"/>
          <path d="M16 10a4 4 0 01-8 0"/>
        </svg>
        <p class="text-sm font-medium">Giỏ hàng trống</p>
        <a href="../customer/restaurant.html"
           class="mt-3 inline-block text-primary text-sm font-semibold hover:underline">
          Quay lại menu &rarr;
        </a>
      </div>`;
    return;
  }

  // Tên nhà hàng
  if (restaurantEl && cart[0].restaurantName) {
    restaurantEl.textContent = cart[0].restaurantName;
  }

  // Render từng item với nút tăng/giảm
  itemsEl.innerHTML = cart
    .map(
      (item) => `
    <div class="flex items-center justify-between text-sm py-2 border-b border-gray-50 last:border-0">
      <div class="flex-1 min-w-0">
        <span class="font-medium text-gray-800 truncate block">${item.name}</span>
        <span class="text-xs text-gray-400">$${item.price.toFixed(2)} / món</span>
      </div>
      <div class="flex items-center gap-2 ml-3">
        <div class="qty-control" style="gap:0;">
          <button class="qty-btn text-xs"
            onclick="changeCartQty('${item.name.replace(/'/g, "\\'")}', -1); renderCheckoutCart();"
            style="width:28px;height:28px;">&minus;</button>
          <span class="qty-val text-sm" style="min-width:28px;">${item.qty}</span>
          <button class="qty-btn text-xs"
            onclick="changeCartQty('${item.name.replace(/'/g, "\\'")}', 1); renderCheckoutCart();"
            style="width:28px;height:28px;">+</button>
        </div>
        <span class="font-bold text-primary w-14 text-right">
          $${(item.price * item.qty).toFixed(2)}
        </span>
      </div>
    </div>
  `,
    )
    .join("");

  // Cập nhật tổng
  const total = getCartTotal();
  if (subtotalEl) subtotalEl.textContent = `$${total.toFixed(2)}`;
  if (totalEl) totalEl.textContent = `$${total.toFixed(2)}`;

  // Cập nhật lại discount nếu đang có voucher
  reapplyVoucherIfAny();
}

// Biến lưu voucher đang dùng (dùng lại khi re-render)
let activeVoucher = null;

function reapplyVoucherIfAny() {
  if (!activeVoucher) return;
  const base = getCartTotal();
  const disc =
    activeVoucher.type === "percent"
      ? (base * activeVoucher.val) / 100
      : activeVoucher.val;
  const final = Math.max(0, base - disc);

  const discRow = document.getElementById("discount-row");
  const discVal = document.getElementById("discount-val");
  const totalEl = document.getElementById("checkout-total");

  if (discRow) discRow.classList.remove("hidden");
  if (discVal) discVal.textContent = `-$${disc.toFixed(2)}`;
  if (totalEl) totalEl.textContent = `$${final.toFixed(2)}`;
}

// ============================================================
// PHẦN 6: VALIDATION FORM CHECKOUT (ĐẶT HÀNG)
// ============================================================

/**
 * Validate trước khi đặt hàng:
 * 1. Phải có ít nhất 1 món trong giỏ
 * 2. Phải chọn địa chỉ giao hàng
 * 3. Phải chọn phương thức thanh toán
 * 4. Nếu đặt thành công → lưu order vào localStorage, xóa cart
 */
function validateAndPlaceOrder(btnEl) {
  const cart = getCart();

  // ── Check 1: Giỏ hàng không được rỗng ──
  if (cart.length === 0) {
    showCheckoutError(
      "Giỏ hàng của bạn đang trống. Vui lòng thêm món trước khi đặt hàng.",
    );
    return;
  }

  // ── Check 2: Phải chọn địa chỉ ──
  const selectedAddress = document.querySelector(
    'input[name="address"]:checked',
  );
  if (!selectedAddress) {
    showCheckoutError("Vui lòng chọn địa chỉ giao hàng.");
    return;
  }

  // ── Check 3: Phải chọn phương thức thanh toán ──
  const selectedPayment = document.querySelector(
    'input[name="payment"]:checked',
  );
  if (!selectedPayment) {
    showCheckoutError("Vui lòng chọn phương thức thanh toán.");
    return;
  }

  // ── Check 4: Phải đăng nhập ──
  const isLoggedIn = localStorage.getItem("fr_logged_in") === "true";
  if (!isLoggedIn) {
    showCheckoutError("Bạn cần đăng nhập để đặt hàng.");
    setTimeout(() => {
      window.location.href = "../auth.html";
    }, 1500);
    return;
  }

  // ── Tất cả hợp lệ → xử lý đặt hàng ──
  btnEl.innerHTML = `
    <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg"
         fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10"
              stroke="currentColor" stroke-width="4"></circle>
      <path class="opacity-75" fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
    </svg> Đang xử lý…`;
  btnEl.style.pointerEvents = "none";

  // Lưu thông tin đơn hàng vào localStorage để trang tracking hiển thị
  const order = {
    id: "FR-" + Date.now(),
    items: cart,
    total: getCartTotal(),
    voucher: activeVoucher,
    address: selectedAddress.closest("label")
      ? selectedAddress.closest("label").querySelector(".text-xs")?.textContent
      : "Địa chỉ đã chọn",
    payment: selectedPayment.value,
    status: "pending",
    createdAt: new Date().toISOString(),
  };
  localStorage.setItem("fr_last_order", JSON.stringify(order));

  // Xóa giỏ hàng sau khi đặt
  clearCart();

  // Chuyển sang trang tracking
  setTimeout(() => {
    window.location.href = "tracking.html";
  }, 1200);
}

/** Hiện thông báo lỗi trên trang checkout */
function showCheckoutError(msg) {
  // Xóa thông báo cũ
  const old = document.getElementById("checkout-error");
  if (old) old.remove();

  const el = document.createElement("div");
  el.id = "checkout-error";
  el.style.cssText = `
    position: fixed;
    top: 80px;
    left: 50%;
    transform: translateX(-50%);
    background: #EF4444;
    color: white;
    padding: 12px 24px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    z-index: 9999;
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    max-width: 90vw;
    text-align: center;
  `;
  el.textContent = "⚠ " + msg;
  document.body.appendChild(el);

  setTimeout(() => {
    el.style.opacity = "0";
    el.style.transition = "opacity 0.3s";
    setTimeout(() => el.remove(), 300);
  }, 3000);
}

// ============================================================
// PHẦN 7: AUTO-INIT — Chạy khi DOM sẵn sàng
// ============================================================

document.addEventListener("DOMContentLoaded", function () {
  // Đồng bộ badge ngay khi load trang
  syncCartBadge();

  // Nếu đang ở trang restaurant → render cart từ localStorage
  if (document.getElementById("cart-list")) {
    renderRestaurantCart();
  }

  // Nếu đang ở trang checkout → render cart + gắn lại nút Place Order
  if (document.getElementById("checkout-items")) {
    renderCheckoutCart();

    // Gắn lại hàm Place Order mới (có validation)
    const placeBtn = document.getElementById("place-order-btn");
    if (placeBtn) {
      // Xóa onclick cũ
      placeBtn.removeAttribute("onclick");
      placeBtn.addEventListener("click", function (e) {
        e.preventDefault();
        validateAndPlaceOrder(this);
      });
    }
  }
});
