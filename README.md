# FoodRush — Food Delivery Web Application

A professional, fully responsive **Food Delivery Web Application** built with **HTML5**, **Tailwind CSS (CDN)**, and **Lucide Icons**. Mobile-first design using the *Sunset Orange* (`#FF4D24`) design system.

---

## 📂 Project Structure

```
food-ordering-website/
│
├── index.html              ← 🏠 Home page (restaurant listings, hero, categories)
├── onboarding.html         ← 🎉 3-slide onboarding (Speed / Taste / Offers)
├── auth.html               ← 🔐 Login, Register, OTP, Social login, Forgot password
├── README.md
│
├── assets/
│   └── css/
│       └── custom.css      ← 🎨 Shared design system (tokens, components, bottom-nav)
│
├── includes/
│   └── schema.sql          ← 🗄️ Enhanced MySQL schema (v2)
│
├── customer/               ← 👤 Customer-facing pages
│   ├── restaurant.html     ← Restaurant detail + food customizer modal
│   ├── checkout.html       ← Address, payment method, voucher application
│   ├── tracking.html       ← Live GPS order tracking (Leaflet.js)
│   ├── history-reviews.html← Order history + star ratings + photo reviews
│   └── profile.html        ← User profile, saved addresses, preferences
│
├── merchant/               ← 🏪 Merchant-facing pages
│   ├── merchant-dashboard.html ← KPI cards, incoming orders, revenue chart
│   └── menu-manager.html   ← Menu CRUD table + add/edit modal
│
└── admin/
    └── admin-panel.html    ← 📊 Combined analytics + voucher management
```

---

## 🖥️ Pages (11 total)

| # | Page | Path | Description |
|---|------|------|-------------|
| 1 | **Onboarding** | `onboarding.html` | 3-slide splash with swipe/keyboard/auto-advance |
| 2 | **Auth** | `auth.html` | Login · Register · OTP · Social · Forgot Password |
| 3 | **Home** | `index.html` | Hero search, category filter, promo banner, restaurant grid |
| 4 | **Restaurant** | `customer/restaurant.html` | Menu tabs, sticky cart, food customizer modal |
| 5 | **Checkout** | `customer/checkout.html` | Address picker, payment method, voucher code |
| 6 | **Live Tracking** | `customer/tracking.html` | Real-time Leaflet map, driver animation, status stepper |
| 7 | **Order History & Reviews** | `customer/history-reviews.html` | Filterable orders, star ratings, photo upload, quick tags |
| 8 | **Profile** | `customer/profile.html` | Edit info, saved addresses, account settings |
| 9 | **Merchant Dashboard** | `merchant/merchant-dashboard.html` | Incoming orders, Chart.js revenue, store toggle |
| 10 | **Menu Manager** | `merchant/menu-manager.html` | Full CRUD table, search/filter, add/edit modal |
| 11 | **Admin Panel** | `admin/admin-panel.html` | Analytics charts + voucher creation/management |

---

## 🎨 Design System

| Token | Value |
|-------|-------|
| Primary | `#FF4D24` (Sunset Orange) |
| Secondary | `#1A1A1A` (Near Black) |
| Background | `#F8F9FA` (Off-White) |
| Success | `#22C55E` |
| Warning | `#F59E0B` |
| Danger | `#EF4444` |
| Font Heading | Poppins (800/900) |
| Font Body | Inter (400/500/600) |

### CSS Components (`assets/css/custom.css`)
`.btn` · `.btn-primary` · `.btn-outline` · `.btn-ghost` · `.card` · `.input-field` · `.badge` · `.pill-*` · `.tag-chip` · `.modal-overlay` · `.navbar` · `.bottom-nav` · `.stat-card` · `.data-table` · `.stepper` · `.toggle` · `.qty-control` · `.toast` · `.skeleton` · `.sidebar`

---

## 📅 Tech Stack

| Layer | Technology |
|-------|-----------|
| Markup | HTML5 semantic |
| Styling | Tailwind CSS (CDN) + `custom.css` |
| Icons | Lucide Icons (CDN) |
| Maps | Leaflet.js (CDN — tracking page) |
| Charts | Chart.js (CDN — merchant & admin) |
| Fonts | Google Fonts (Poppins + Inter) |
| Database | MySQL (schema in `/includes/schema.sql`) |

---

## 🗄️ Database Schema (v2)

Key tables: `users` · `user_sessions` · `categories` · `restaurants` · `menu_items` · `vouchers` · `orders` · `order_items` · `order_status_logs` · `reviews` · `saved_addresses`

**Highlights:**
- `users` — social login IDs (`google_id`, `facebook_id`), phone OTP (`otp_code`, `otp_expires_at`, `phone_verified`)
- `reviews` — per-order unique review with sub-category ratings (`food_quality`, `delivery_speed`, `packaging`), `quick_tags` (JSON), `photo_urls` (JSON)
- `menu_items` — `discount_price` for promotional pricing
- Full index coverage on all foreign keys and commonly queried columns

---

## 🔗 Navigation Flow

```
onboarding.html
      ↓
  auth.html  ←→  (Google / Facebook / Phone OTP / Forgot PW)
      ↓
  index.html  →  customer/restaurant.html  →  customer/checkout.html  →  customer/tracking.html
      ↓                                              ↓
  customer/                                  customer/history-reviews.html
  profile.html                                         ↓ (Write Review modal)
      ↑________________________________________________|

  merchant/merchant-dashboard.html  ←→  merchant/menu-manager.html
  admin/admin-panel.html  (Analytics ↔ Vouchers tabbed)
```

---

## 📱 Mobile UX

All customer pages include a **fixed bottom navigation bar** (Home · Orders · Cart · Profile · Sign In) that auto-hides on desktop (`md:` breakpoint). Onboarding supports **touch swipe**, **mouse drag**, and **keyboard arrow** navigation.

---

*Built with ❤️ by NguyenQuangNhat — FoodRush Platform 2026*