# 💳 Uz Payments Emulator

`version 0.1 BETA`

**Emulator for payment providers in Uzbekistan (PayMe, Click, Uzum)**  

Designed for developers to test integrations in a safe and controlled environment

---

## 🚀 Features

- Emulates PayMe JSON-RPC API (CreateTransaction, PerformTransaction, CheckTransaction, CancelTransaction)
- Webhook support with history
- Scenario system for simulating different responses
- Built-in admin panel (Filament)
- Local-only mode for security
- Extensible architecture for adding more providers

---

## ⚙️ Installation

```bash
git clone https://github.com/your-username/uz-payments-emulator.git
cd uz-payments-emulator

composer install
cp .env.example .env
php artisan key:generate

# Configure .env (DB, APP_URL, DEBUG_NOTIFY_URL, DEBUG_CALLBACK_URL, etc.)

php artisan migrate
php artisan serve
php artisan queue:work
```

---

## 🛠 Admin Panel

Filament dashboard is available at:

`http://localhost:8000/admin`

Use `php artisan make:filament-user` to create an admin account.

---

## 🔒 Security Note
**This project is intended for local development only. Never expose it to production or external users**

---

## 📘 API Documentation – PayMe (JSON-RPC)
All requests should be sent to:
```http request
POST {{BASE_URL}}/api/payme
Content-Type: application/json
```

* CreateTransaction
```json
{
  "id": 1,
  "method": "CreateTransaction",
  "params": {
    "id": "123456",
    "time": 1720630000000,
    "amount": 500000,
    "account": {
      "phone": "998901234567"
    }
  }
}
```

* PerformTransaction
```json
{
  "id": 2,
  "method": "PerformTransaction",
  "params": {
    "id": "payme_66bdfabc88a11",
    "time": 1720630100000
  }
}
```

* CheckTransaction
```json
{
  "id": 3,
  "method": "CheckTransaction",
  "params": {
    "id": "payme_66bdfabc88a11"
  }
}
```

* CancelTransaction
```json
{
  "id": 4,
  "method": "CancelTransaction",
  "params": {
    "id": "payme_66bdfabc88a11",
    "reason": 3
  }
}
```

``!!!Amount is in tyiyns (1 sum = 100 tyiyn). Example: 500000 = 5000.00 UZS!!!``

---

## 📬 Future Plans
* Support for Click / Uzum APIs
* Telegram bot for notifications
* Postman collection
* Docker support

---

## 🙃 Author
Timur Gabdurashitov (timhale2104) — https://www.linkedin.com/in/timhale2104/
