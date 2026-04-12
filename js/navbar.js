// ============================================================
// js/navbar.js  —  FoodRush Navbar Auth State
// Phụ trách: Bình (JavaScript)
// ============================================================

document.addEventListener("DOMContentLoaded", function () {
  const isLoggedIn = localStorage.getItem("fr_logged_in") === "true";
  const userRaw = localStorage.getItem("fr_user");
  const user = userRaw ? JSON.parse(userRaw) : null;

  if (!isLoggedIn || !user) return; // Chưa đăng nhập → không làm gì

  // ── Ẩn nút Sign In (desktop) ──
  const signinBtn = document.getElementById("signin-btn");
  if (signinBtn) signinBtn.style.display = "none";

  // ── Ẩn avatar mặc định (ảnh cứng khi chưa đăng nhập) ──
  const defaultAvatar = document.querySelector(
    'a[href="customer/profile.html"].hidden',
  );
  if (defaultAvatar) defaultAvatar.style.display = "none";

  // ── Ẩn nút Sign In mobile bottom nav ──
  const bnavSignin = document.getElementById("bnav-signin");
  if (bnavSignin) bnavSignin.style.display = "none";

  // ── Tìm khu vực bên phải navbar ──
  const navRight = document.querySelector(
    ".navbar .container-app .flex.items-center.gap-2",
  );
  if (!navRight) return;

  // ── Thêm CSS cho dropdown items ──
  if (!document.getElementById("navbar-js-style")) {
    const style = document.createElement("style");
    style.id = "navbar-js-style";
    style.textContent = `
      .dd-item {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 12px; border-radius: 10px;
        font-size: 14px; color: #374151; text-decoration: none;
        transition: background 0.15s; cursor: pointer;
      }
      .dd-item:hover { background: #F3F4F6; }
      .dd-item.logout { color: #DC2626; }
      .dd-item.logout:hover { background: #FEE2E2; }
      @media (min-width: 768px) { #user-name-label { display: inline !important; } }
    `;
    document.head.appendChild(style);
  }

  // ── Tạo wrapper ──
  const userMenu = document.createElement("div");
  userMenu.id = "user-menu-wrapper";
  userMenu.style.cssText = "position:relative;display:flex;align-items:center;";

  // ── Tạo nút trigger (dùng div, KHÔNG dùng button) ──
  const menuBtn = document.createElement("div");
  menuBtn.id = "user-menu-btn";
  menuBtn.setAttribute("role", "button");
  menuBtn.setAttribute("tabindex", "0");
  menuBtn.style.cssText = `
    display:flex; align-items:center; gap:8px;
    padding:6px 10px; border-radius:12px;
    cursor:pointer; transition:background 0.2s; user-select:none;
  `;
  menuBtn.innerHTML = `
    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=${encodeURIComponent(user.email)}"
         alt="${user.full_name}" style="width:34px;height:34px;border-radius:50%;border:2px solid #FF4D24;flex-shrink:0;" />
    <span id="user-name-label" style="display:none;font-size:14px;font-weight:600;color:#1A1A1A;max-width:90px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
      ${user.full_name.split(" ").pop()}
    </span>
    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
      <polyline points="6 9 12 15 18 9"></polyline>
    </svg>
  `;

  menuBtn.addEventListener(
    "mouseenter",
    () => (menuBtn.style.background = "#F3F4F6"),
  );
  menuBtn.addEventListener(
    "mouseleave",
    () => (menuBtn.style.background = "transparent"),
  );

  // ── Tạo dropdown menu ──
  const dropdown = document.createElement("div");
  dropdown.id = "user-dropdown";
  dropdown.style.cssText = `
    display:none; position:absolute; right:0; top:calc(100% + 10px);
    width:230px; background:white; border-radius:16px;
    box-shadow:0 8px 32px rgba(0,0,0,0.15); border:1px solid #E5E7EB;
    z-index:9999; overflow:hidden;
  `;
  dropdown.innerHTML = `
    <div style="padding:14px 16px;border-bottom:1px solid #F3F4F6;background:#FAFAFA;">
      <div style="font-weight:700;font-size:14px;color:#1A1A1A;margin-bottom:2px;">${user.full_name}</div>
      <div style="font-size:12px;color:#6B7280;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${user.email}</div>
      ${user.role === "admin" ? '<span style="display:inline-block;margin-top:6px;background:#FEE2E2;color:#DC2626;font-size:11px;font-weight:700;padding:2px 10px;border-radius:99px;">Admin</span>' : ""}
    </div>
    <div style="padding:8px;">
      <a href="customer/profile.html" class="dd-item">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="#FF4D24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
        </svg> Hồ sơ của tôi
      </a>
      <a href="customer/history-reviews.html" class="dd-item">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="#FF4D24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
          <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
        </svg> Lịch sử đơn hàng
      </a>
      ${
        user.role === "admin"
          ? `
      <a href="admin/admin-panel.html" class="dd-item">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="#FF4D24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
          <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
        </svg> Admin Panel
      </a>`
          : ""
      }
      <div style="height:1px;background:#F3F4F6;margin:6px 0;"></div>
      <a href="#" id="logout-link" class="dd-item logout">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
        </svg> Đăng xuất
      </a>
    </div>
  `;

  // ── Ráp vào wrapper và chèn vào navbar ──
  userMenu.appendChild(menuBtn);
  userMenu.appendChild(dropdown);
  navRight.appendChild(userMenu);

  // =================================================================
  // KHU VỰC ĐÃ FIX LỖI RELOAD TRANG
  // =================================================================
  menuBtn.addEventListener("click", function (e) {
    // 1. Chặn ĐỨT ĐIỂM hành vi mặc định của HTML (chặn thẻ <a> bọc ngoài điều hướng trang)
    e.preventDefault();

    // 2. Chặn event nổi bọt lên DOM
    e.stopPropagation();

    const isOpen = dropdown.style.display === "block";
    dropdown.style.display = isOpen ? "none" : "block";
  });

  // Keyboard: Enter / Space mở dropdown
  menuBtn.addEventListener("keydown", function (e) {
    if (e.key === "Enter" || e.key === " ") {
      e.preventDefault();
      menuBtn.click();
    }
  });

  // ── Đóng dropdown khi click ra ngoài ──
  document.addEventListener("click", function (e) {
    if (!userMenu.contains(e.target)) {
      dropdown.style.display = "none";
    }
  });

  // ── Sự kiện Đăng xuất ──
  const logoutLink = document.getElementById("logout-link");
  if (logoutLink) {
    logoutLink.addEventListener("click", function (e) {
      e.preventDefault(); // Chặn href="#" cuộn lên đầu trang
      e.stopPropagation();
      doLogout();
    });
  }
});

// ============================================================
// Hàm Đăng xuất — khai báo global để có thể gọi từ bất kỳ đâu
// ============================================================
function doLogout() {
  localStorage.removeItem("fr_user");
  localStorage.removeItem("fr_logged_in");

  const toast = document.createElement("div");
  toast.style.cssText = `
    position:fixed; top:24px; left:50%; transform:translateX(-50%);
    background:#1A1A1A; color:white; padding:12px 24px; border-radius:12px;
    font-size:14px; font-weight:600; z-index:9999;
    box-shadow:0 8px 24px rgba(0,0,0,0.2); white-space:nowrap;
  `;
  toast.textContent = "✓ Đã đăng xuất. Hẹn gặp lại!";
  document.body.appendChild(toast);

  setTimeout(() => {
    window.location.href = "auth.html";
  }, 1000);
}
