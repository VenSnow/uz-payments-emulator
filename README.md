# 💳 Uz Payments Emulator

`version 0.2 BETA`

**Emulator for payment providers in Uzbekistan (PayMe, Click, Uzum)**

Designed for developers to test integrations in a safe and controlled environment

---

## 🚀 Features

- Emulates PayMe JSON-RPC API (CreateTransaction, PerformTransaction, CheckTransaction, CancelTransaction)
- Emulates Uzum REST API (/check, /create, /confirm, /reverse, /status)
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

`http://localhost/admin`

Use `php artisan make:filament-user` to create an admin account.

---

## 🔒 Security Note
**This project is intended for local development only. Never expose it to production or external users.**

---

## 📘 API Documentation

### Payme (JSON-RPC)
All requests should be sent to:
```http request
POST {{BASE_URL}}/api/payme
Content-Type: application/json
```

- **CreateTransaction**
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

- **PerformTransaction**
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

- **CheckTransaction**
```json
{
  "id": 3,
  "method": "CheckTransaction",
  "params": {
    "id": "payme_66bdfabc88a11"
  }
}
```

- **CancelTransaction**
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

**Note:** Amount is in tiyiyns (1 sum = 100 tiyiyn). Example: 500000 = 5000.00 UZS.

### Uzum (REST)
All requests should be sent to:
```http request
POST {{BASE_URL}}/uzum/{method}
Content-Type: application/json
```

- **/uzum/check**
```json
{
  "serviceId": 101202,
  "timestamp": 1698361456728,
  "params": {
    "account": 123456789
  }
}
```

- **/uzum/create**
```json
{
  "serviceId": 101202,
  "timestamp": 1698361456728,
  "transId": "5c398d7e-76b6-11ee-96da-f3a095c6289d",
  "params": {
    "account": 123456789
  },
  "amount": 2500000
}
```

- **/uzum/confirm**
```json
{
  "serviceId": 101202,
  "timestamp": 1698361456728,
  "transId": "5c398d7e-76b6-11ee-96da-f3a095c6289d"
}
```

- **/uzum/reverse**
```json
{
  "serviceId": 101202,
  "timestamp": 1698361456728,
  "transId": "5c398d7e-76b6-11ee-96da-f3a095c6289d"
}
```

- **/uzum/status**
```json
{
  "serviceId": 101202,
  "timestamp": 1698361456728,
  "transId": "5c398d7e-76b6-11ee-96da-f3a095c6289d"
}
```

**Note:** Amount is in tiyiyns (1 sum = 100 tiyiyn). Example: 2500000 = 25000.00 UZS.

### Debug Scenarios
The `uz-payments-emulator` supports simulation of predefined responses for testing purposes using the `debug_scenario` parameter.

**This only works when `APP_ENV=local`. In production, debug scenarios are ignored.**

Supported Scenarios:
Make `GET` request to `{{BASE_URL}}/api/enums/scenario-type`

**!Use only `value` for `debug_scenario`!**

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

#### Usage Example

Simply include `debug_scenario` in the request body.

**Payme CreateTransaction Example**
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

**Uzum CreateTransaction Example**
```json
{
  "serviceId": 101202,
  "timestamp": 1698361456728,
  "transId": "5c398d7e-76b6-11ee-96da-f3a095c6289d",
  "params": {
    "account": 123456789
  },
  "amount": 2500000,
  "debug_scenario": "insufficient_funds"
}
```

**Notes:**
- You can use `debug_scenario` with **any supported method** for both PayMe and Uzum.
- When `debug_scenario` is present, no real transaction logic is applied — the response is mocked.
- This feature is useful for testing client integration with various edge cases.

---

## 📬 Future Plans
- Full support for Click API
- Telegram bot for notifications
- Postman collection
- Docker support
- Enhanced Uzum API features (e.g., additional error codes)

---

## 🙃 Author
Timur Gabdurashitov (timhale2104) — https://www.linkedin.com/in/timhale2104/
