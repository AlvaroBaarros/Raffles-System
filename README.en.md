<div align="center">

<img src="https://capsule-render.vercel.app/api?type=waving&color=0:A80030,100:1a1a2e&height=200&section=header&text=Raffle%20System&fontSize=48&fontColor=ffffff&animation=fadeIn&fontAlignY=38&desc=by%20debian&descAlignY=58&descSize=20" width="100%" alt="Raffle System by debian"/>

[🇧🇷 Português](README.md) · 🇺🇸 **English**

### 🎟️ A complete platform to create, sell and draw online raffles

Ticket sales with **automatic Pix payments**, an admin dashboard, an **affiliate program**, buyer rankings and **WhatsApp** notifications, all in one system.

<br/>

<img src="https://img.shields.io/badge/PHP-7.4-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 7.4"/>
<img src="https://img.shields.io/badge/Laravel-5.8-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 5.8"/>
<img src="https://img.shields.io/badge/MySQL-MariaDB-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL"/>
<img src="https://img.shields.io/badge/Bootstrap-4-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap"/>
<img src="https://img.shields.io/badge/Pix-Payments-32BCAD?style=for-the-badge&logo=pix&logoColor=white" alt="Pix"/>

</div>

---

## ✨ Features

<table>
<tr>
<td width="50%" valign="top">

### 🛒 For buyers
- 🔢 Pick numbers manually or get **random numbers**
- ⚡ Fast checkout with **Pix copy-and-paste code and QR Code**
- ✅ Automatic payment confirmation
- 🔎 Look up reservations by phone number
- 🏆 **Winners** page and raffle rules
- 📱 Responsive, mobile-first layout

</td>
<td width="50%" valign="top">

### 🧑‍💼 For admins
- 🎁 Create, edit and **duplicate raffles**
- 📅 Draw scheduling and expected draw date
- 🏅 Buyer **rankings** and winner selection
- 📊 **Profit**, active raffles and pending payment reports
- 🧾 Purchase reports in **PDF**
- 🎯 **Pixel** integration for ad campaigns

</td>
</tr>
<tr>
<td width="50%" valign="top">

### 🤝 Affiliate program
- 🔗 Unique affiliate link for each raffle
- 💰 Configurable percentage commission
- 🏦 **Payout** requests and approval
- 📋 Dedicated affiliate dashboard

</td>
<td width="50%" valign="top">

### 🔔 Automation
- 💬 Automatic **WhatsApp** messages
- ⏱️ Payment checks **every minute**
- ♻️ Automatic release of expired reservations
- 🎁 Bulk-purchase promotions

</td>
</tr>
</table>

---

## 💳 Payment gateways

<div align="center">

| Gateway | Method |
|:---:|:---:|
| **Mercado Pago** | Pix |
| **Asaas** | Pix |
| **Paggue** | Pix |

</div>

> **Pix** is Brazil's instant payment system, run by the Central Bank.

---

## 🛠️ Tech stack

<div align="center">

<img src="https://skillicons.dev/icons?i=php,laravel,mysql,js,jquery,bootstrap,html,css&theme=dark" alt="Tech stack"/>

</div>

---

## 🚀 Running locally

> **Requirements:** PHP 7.2.5 to 7.4 · MySQL or MariaDB · Composer

```bash
# 1. Clone the repository
git clone https://github.com/AlvaroBaarros/Raffles-System.git
cd Raffles-System

# 2. Install dependencies
composer install

# 3. Set up the environment
cp .env.example .env
php artisan key:generate

# 4. Create the database and run migrations
php artisan migrate --seed

# 5. Start the server
php artisan serve
```

Open **http://localhost:8000** 🎉

### ⏰ Scheduled tasks

For automatic payment confirmation and reservation release, add this to the server's cron:

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

| Command | Frequency | What it does |
|---|:---:|---|
| `pix:check-payment` | every minute | Confirms pending Pix payments |
| `reservas:expiradas` | every minute | Releases numbers from unpaid reservations |

---

## 📁 Project structure

```
📦 rifas
 ┣ 📂 app
 ┃ ┣ 📂 Console/Commands   → scheduled tasks (payments, reservations)
 ┃ ┣ 📂 Http/Controllers   → checkout, raffles, affiliates, dashboard
 ┃ ┣ 📂 Models             → products, participants, affiliates
 ┃ ┗ 📂 Services           → payment gateway integrations
 ┣ 📂 database/migrations  → database schema
 ┣ 📂 resources/views      → pages (Blade)
 ┗ 📂 routes               → web routes
```

---

<div align="center">

## 📬 Contact

Made with 💙 by **debian**

<a href="https://github.com/AlvaroBaarros">
  <img src="https://img.shields.io/badge/GitHub-AlvaroBaarros-181717?style=for-the-badge&logo=github&logoColor=white" alt="GitHub AlvaroBaarros"/>
</a>

<br/><br/>

⭐ If this project helped you, give it a star!

<img src="https://capsule-render.vercel.app/api?type=waving&color=0:1a1a2e,100:A80030&height=120&section=footer" width="100%" alt=""/>

</div>
