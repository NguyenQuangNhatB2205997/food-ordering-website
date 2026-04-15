// ============================================================
// js/auth.js  —  FoodRush Authentication Logic
// Phụ trách: Bình (JavaScript)
// Chức năng:
//   1. Form Validation phía client (login + register)
//   2. Gọi PHP backend qua fetch() để kiểm tra/lưu DB
//   3. Lưu trạng thái đăng nhập vào localStorage
//   4. Cập nhật UI navbar sau khi đăng nhập/đăng ký
// ============================================================

// ── Hàm tiện ích: rung lắc phần tử khi có lỗi ──
function shakeElement(el) {
  el.classList.remove("shake");
  // Buộc reflow để animation chạy lại nếu đang chạy
  void el.offsetWidth;
  el.classList.add("shake");
  setTimeout(() => el.classList.remove("shake"), 400);
}

// ── Hàm tiện ích: hiển thị thông báo lỗi dưới input ──
function showFieldError(inputEl, message) {
  // Xóa thông báo lỗi cũ nếu có
  const parent = inputEl.closest("div");
  const old = parent.querySelector(".field-error");
  if (old) old.remove();

  // Tạo thông báo mới
  const err = document.createElement("p");
  err.className = "field-error text-xs text-red-500 mt-1 font-medium";
  err.textContent = "⚠ " + message;
  parent.appendChild(err);

  // Tô đỏ viền input
  inputEl.style.borderColor = "#EF4444";
  inputEl.style.boxShadow = "0 0 0 3px rgba(239,68,68,0.12)";
}

// ── Hàm tiện ích: xóa lỗi khi người dùng gõ lại ──
function clearFieldError(inputEl) {
  const parent = inputEl.closest("div");
  const old = parent.querySelector(".field-error");
  if (old) old.remove();
  inputEl.style.borderColor = "";
  inputEl.style.boxShadow = "";
}

// ── Hàm tiện ích: hiển thị thông báo toast ──
function showToast(message, type = "success") {
  // Xóa toast cũ
  const old = document.querySelector(".auth-toast");
  if (old) old.remove();

  const toast = document.createElement("div");
  toast.className = "auth-toast";
  toast.style.cssText = `
    position: fixed;
    top: 24px;
    left: 50%;
    transform: translateX(-50%);
    background: ${type === "success" ? "#22C55E" : "#EF4444"};
    color: white;
    padding: 12px 24px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    z-index: 9999;
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    animation: slideDown 0.3s ease;
    white-space: nowrap;
  `;
  toast.textContent = (type === "success" ? "✓ " : "✗ ") + message;
  document.body.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = "0";
    toast.style.transition = "opacity 0.3s";
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// ── Gắn sự kiện xóa lỗi khi người dùng gõ lại ──
document.addEventListener("DOMContentLoaded", () => {
  const fields = [
    "login-email",
    "login-password",
    "reg-name",
    "reg-email",
    "reg-phone",
    "reg-password",
  ];
  fields.forEach((id) => {
    const el = document.getElementById(id);
    if (el) el.addEventListener("input", () => clearFieldError(el));
  });
});

// ============================================================
// 1. XỬ LÝ ĐĂNG NHẬP
// ============================================================
function doLogin() {
  const emailInput = document.getElementById("login-email");
  const pwdInput = document.getElementById("login-password");
  const btn = document.getElementById("login-btn");

  const email = emailInput.value.trim();
  const pwd = pwdInput.value.trim();

  // Xóa lỗi cũ
  clearFieldError(emailInput);
  clearFieldError(pwdInput);

  // --- Validation phía client ---
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  let hasError = false;

  if (!email) {
    showFieldError(emailInput, "Vui lòng nhập địa chỉ Email.");
    shakeElement(emailInput.parentElement);
    hasError = true;
  } else if (!emailRegex.test(email)) {
    showFieldError(
      emailInput,
      "Định dạng Email không hợp lệ (ví dụ: ten@gmail.com).",
    );
    shakeElement(emailInput.parentElement);
    hasError = true;
  }

  if (!pwd) {
    showFieldError(pwdInput, "Vui lòng nhập Mật khẩu.");
    shakeElement(pwdInput.parentElement);
    hasError = true;
  }

  if (hasError) return;

  // --- Gửi lên server để kiểm tra database ---
  btn.innerHTML =
    '<span style="display:inline-block;width:16px;height:16px;border:2px solid white;border-top-color:transparent;border-radius:50%;animation:spin 0.7s linear infinite;margin-right:8px;vertical-align:middle"></span> Đang đăng nhập…';
  btn.disabled = true;

  fetch("api/login.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ email, password: pwd }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        // Lưu thông tin user vào localStorage
        localStorage.setItem("fr_user", JSON.stringify(data.user));
        localStorage.setItem("fr_logged_in", "true");

        showToast("Đăng nhập thành công! Đang chuyển trang…", "success");

        // Chuyển trang sau 1 giây
        setTimeout(() => {
          window.location.href = "index.html";
        }, 1000);
      } else {
        // Server báo sai thông tin
        showToast(data.message, "error");
        showFieldError(emailInput, data.message);
        shakeElement(emailInput.parentElement);

        // Reset button
        btn.innerHTML =
          '<i data-lucide="log-in" class="w-4 h-4" style="display:inline;margin-right:6px"></i> Sign In';
        btn.disabled = false;
        if (typeof lucide !== "undefined") lucide.createIcons();
      }
    })
    .catch(() => {
      // Lỗi mạng hoặc server không phản hồi
      showToast(
        "Không thể kết nối server.",
        "error",
      );
      btn.innerHTML =
        '<i data-lucide="log-in" class="w-4 h-4" style="display:inline;margin-right:6px"></i> Sign In';
      btn.disabled = false;
      if (typeof lucide !== "undefined") lucide.createIcons();
    });
}

// ============================================================
// 2. XỬ LÝ ĐĂNG KÝ
// ============================================================
function doRegister() {
  const nameInput = document.getElementById("reg-name");
  const emailInput = document.getElementById("reg-email");
  const phoneInput = document.getElementById("reg-phone");
  const pwdInput = document.getElementById("reg-password");
  const tosCheck = document.getElementById("tos-check");

  // Xóa lỗi cũ
  [nameInput, emailInput, phoneInput, pwdInput].forEach(clearFieldError);

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  let hasError = false;

  // --- Kiểm tra Họ tên ---
  if (!nameInput.value.trim()) {
    showFieldError(nameInput, "Vui lòng nhập Họ và tên đầy đủ.");
    shakeElement(nameInput.parentElement);
    hasError = true;
  }

  // --- Kiểm tra Email ---
  if (!emailInput.value.trim()) {
    showFieldError(emailInput, "Vui lòng nhập Email.");
    shakeElement(emailInput.parentElement);
    hasError = true;
  } else if (!emailRegex.test(emailInput.value.trim())) {
    showFieldError(emailInput, "Định dạng Email không hợp lệ.");
    shakeElement(emailInput.parentElement);
    hasError = true;
  }

  // --- Kiểm tra Số điện thoại ---
  let phoneVal = phoneInput.value.trim().replace(/[\s\-]/g, "");
  if (phoneVal.startsWith("+84")) {
    phoneVal = "0" + phoneVal.slice(3);
  } else if (phoneVal.length === 9 && /^[35789]/.test(phoneVal)) {
    phoneVal = "0" + phoneVal;
  }
  const phoneRegex = /^0[35789][0-9]{8}$/;
  if (!phoneRegex.test(phoneVal)) {
    showFieldError(
      phoneInput,
      "Số điện thoại không hợp lệ. Nhập 9 số sau +84, hoặc 10 số bắt đầu bằng 0.",
    );
    shakeElement(phoneInput.parentElement);
    hasError = true;
  }

  // --- Kiểm tra Mật khẩu ---
  if (pwdInput.value.length < 8) {
    showFieldError(pwdInput, "Mật khẩu phải có ít nhất 8 ký tự.");
    shakeElement(pwdInput.parentElement);
    hasError = true;
  }

  // --- Kiểm tra checkbox điều khoản ---
  if (!tosCheck.checked) {
    shakeElement(tosCheck.parentElement);
    showToast("Bạn phải đồng ý với Điều khoản dịch vụ để tiếp tục.", "error");
    hasError = true;
  }

  if (hasError) return;

  // --- Lấy button và đổi trạng thái ---
  const btn = document.querySelector('[onclick="doRegister()"]');
  const originalHTML = btn.innerHTML;
  btn.innerHTML =
    '<span style="display:inline-block;width:16px;height:16px;border:2px solid white;border-top-color:transparent;border-radius:50%;animation:spin 0.7s linear infinite;margin-right:8px;vertical-align:middle"></span> Đang đăng ký…';
  btn.disabled = true;

  // --- Gửi lên server để lưu database ---
  fetch("../api/register.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      full_name: nameInput.value.trim(),
      email: emailInput.value.trim(),
      phone: phoneVal,
      password: pwdInput.value,
    }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        // Lưu thông tin user mới vào localStorage (tự động đăng nhập)
        localStorage.setItem("fr_user", JSON.stringify(data.user));
        localStorage.setItem("fr_logged_in", "true");

        showToast(
          "🎉 Đăng ký thành công! Chào mừng bạn đến với FoodRush.",
          "success",
        );

        setTimeout(() => {
          window.location.href = "index.html";
        }, 1200);
      } else {
        // Server báo lỗi (ví dụ: email đã tồn tại)
        showToast(data.message, "error");

        // Nếu lỗi liên quan email thì highlight ô email
        if (data.message.toLowerCase().includes("email")) {
          showFieldError(emailInput, data.message);
          shakeElement(emailInput.parentElement);
        }

        btn.innerHTML = originalHTML;
        btn.disabled = false;
      }
    })
    .catch(() => {
      showToast(
        "Không thể kết nối server.",
        "error",
      );
      btn.innerHTML = originalHTML;
      btn.disabled = false;
    });
}

// ============================================================
// 3. THÊM CSS ANIMATION SPIN vào trang (dùng cho loading button)
// ============================================================
(function addSpinStyle() {
  const style = document.createElement("style");
  style.textContent = `
    @keyframes spin { to { transform: rotate(360deg); } }
    @keyframes slideDown {
      from { opacity: 0; transform: translateX(-50%) translateY(-10px); }
      to   { opacity: 1; transform: translateX(-50%) translateY(0); }
    }
  `;
  document.head.appendChild(style);
})();
