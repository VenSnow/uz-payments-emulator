# 💳 Uz Payments Emulator

`version 0.2 BETA`

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

### Debug Scenarios
The `uz-payments-emulator` supports simulation of predefined responses for testing purposes using the `debug_scenario` parameter

**This only works when `APP_ENV=local`. In production, debug scenarios are ignored**

Supported Scenarios:
Make `GET` request to `{{BASE_URL}}api/enums/scenario-type`

**!Use only `value` for `debug_scneario`!**

```json
{
    "success": true,
    "data": [
        {
            // Simulates a successful transaction
            "value": "success",
            "label": "OK"
        },
        {
            // Simulates a failed transaction due to insufficient funds
            "value": "insufficient_funds",
            "label": "Insufficient funds"
        },
        {
            // Simulates a timeout error
            "value": "timeout",
            "label": "Gateway Timeout"
        },
        {
            // Simulates a signature verification failure
            "value": "signature_error",
            "label": "Invalid signature"
        }
    ]
}
```

### Usage Example

Simply include `debug_scenario` in the `params` field of your JSON-RPC request

**CreateTransaction Example**
```json
{
  "method": "CreateTransaction",
  "params": {
    "id": "tx_001",
    "time": 1720630000000,
    "amount": 500000,
    "account": {
      "phone": "998901234567"
    },
    "debug_scenario": "success"
  }
}
```
### Notes
* You can use `debug_scenario` with **any supported method**, including:
    * `CreateTransaction`
    * `PerformTransaction` 
    * `CancelTransaction`
    * `CheckTransaction`
* When `debug_scenario` is present, no real transaction logic is applied - the response is mocked
* This feature is useful for testing client integration with various edge cases.

---

## 📬 Future Plans
* Support for Click / Uzum APIs
* Telegram bot for notifications
* Postman collection
* Docker support

---

## 🙃 Author
Timur Gabdurashitov (timhale2104) — https://www.linkedin.com/in/timhale2104/
