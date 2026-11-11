# Frontend Backend Contract

## Base URL
- `/api/v1`
- All protected endpoints require `Authorization: Bearer <token>` or `auth:sanctum` session token.

## Common Response Format

### Success
```json
{
  "status": "success",
  "message": "Transaksi berhasil dicatat.",
  "data": {}
}
```

### Error
```json
{
  "status": "error",
  "message": "Validasi gagal.",
  "errors": {
    "email": ["Email atau password salah."]
  }
}
```

## Authentication

### 1) Register
- `POST /api/v1/auth/register`
- Body:
```json
{
  "name": "Hafid Hidayat",
  "email": "user@example.com",
  "password": "secretpassword"
}
```
- Response `201`:
```json
{
  "status": "success",
  "message": "Registrasi berhasil.",
  "data": {
    "token": "1|sanctum_token_string_here",
    "user": {
      "id": 1,
      "name": "Hafid Hidayat",
      "email": "user@example.com"
    }
  }
}
```

### 2) Login
- `POST /api/v1/auth/login`
- Body:
```json
{
  "email": "user@example.com",
  "password": "secretpassword"
}
```
- Response `200`:
```json
{
  "status": "success",
  "message": "Login berhasil.",
  "data": {
    "token": "1|sanctum_token_string_here",
    "user": {
      "id": 1,
      "name": "Hafid Hidayat",
      "email": "user@example.com"
    }
  }
}
```

## Dashboard

### 3) Dashboard Summary
- `GET /api/v1/dashboard/summary?month=11&year=2026`
- Auth required.
- Response `200`:
```json
{
  "status": "success",
  "data": {
    "current_balance": 12500000.00,
    "total_income_this_month": 8000000.00,
    "total_expense_this_month": 3200000.00,
    "recent_transactions": [
      {
        "id": 101,
        "type": "expense",
        "category_name": "Makanan",
        "amount": 45000.00,
        "transaction_date": "2026-11-10",
        "description": "Makan Malam"
      }
    ],
    "insights": [
      "Pengeluaran Anda bulan ini meningkat 12% dibanding bulan lalu.",
      "Kategori pengeluaran terbesar bulan ini adalah Makanan."
    ]
  }
}
```

## Categories

### 4) List Categories
- `GET /api/v1/categories?type=expense`
- Auth required.
- Response `200`:
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "Makanan & Minuman",
      "type": "expense",
      "icon": "utensils",
      "color": "#EF4444",
      "is_system_default": true
    }
  ]
}
```

### Create, read, update, and delete personal categories
- `POST /api/v1/categories` creates a category owned by the authenticated user. Body fields: `name` (required, max 100), `type` (`income` or `expense`), optional `icon` (max 50), and optional six-digit hex `color`.
- `GET /api/v1/categories/{id}` returns a category visible to that user, including system defaults.
- `PUT /api/v1/categories/{id}` replaces the category fields using the create body.
- `DELETE /api/v1/categories/{id}` deletes a personal category only when no transactions or budgets use it.
- Attempts to change the type of a category already used by a transaction or budget, or delete an in-use category, return `422`. System defaults and categories belonging to another user cannot be modified or deleted; they return `404`.

## Budgets

### 6) List Budgets
- `GET /api/v1/budgets?month=11&year=2026`
- Auth required.
- Response `200`:
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "category_id": 2,
      "category_name": "Transportasi",
      "amount_limit": 1500000.00,
      "month": 11,
      "year": 2026
    }
  ]
}
```

### 7) Create Budget
- `POST /api/v1/budgets`
- Auth required.
- Body:
```json
{
  "category_id": 2,
  "amount_limit": 1500000.00,
  "month": 11,
  "year": 2026
}
```
- Response `201`:
```json
{
  "status": "success",
  "message": "Budget berhasil disimpan.",
  "data": {
    "id": 1,
    "category_id": 2,
    "amount_limit": 1500000.00,
    "month": 11,
    "year": 2026
  }
}
```

### 8) Read, update, and delete budgets
- `GET /api/v1/budgets/{id}` returns one of the authenticated user's budgets.
- `PUT /api/v1/budgets/{id}` uses the same body as create and returns `200`.
- `DELETE /api/v1/budgets/{id}` returns `200` with a success message.
- Both operations are scoped to the authenticated user; another user's budget returns `404`.
- Budgets can only reference an expense category visible to that user, and the same category can only have one budget per month/year.
- `month` must be between 1 and 12; `year` must be between 2000 and 32767.

## Transactions

### 9) List Transactions
- `GET /api/v1/transactions?month=11&year=2026&type=expense`
- Auth required.
- Response `200`:
```json
{
  "status": "success",
  "data": [
    {
      "id": 101,
      "type": "expense",
      "category_id": 1,
      "category_name": "Makanan & Minuman",
      "amount": 45000.00,
      "transaction_date": "2026-11-10",
      "description": "Makan Malam"
    }
  ]
}
```

### 10) Create Transaction
- `POST /api/v1/transactions`
- Auth required.
- Body:
```json
{
  "type": "expense",
  "category_id": 1,
  "amount": 75000.00,
  "transaction_date": "2026-11-11",
  "description": "Pembelian perlengkapan kantor"
}
```
- Response `201`:
```json
{
  "status": "success",
  "message": "Transaksi berhasil dicatat.",
  "data": {
    "id": 102,
    "type": "expense",
    "amount": 75000.00,
    "transaction_date": "2026-11-11",
    "category_id": 1,
    "description": "Pembelian perlengkapan kantor"
  }
}
```

### 11) Get Transaction Detail
- `GET /api/v1/transactions/{id}`
- Auth required.
- Response `200` with same fields as create response.
- Response `404` if the transaction belongs to another user or does not exist.

### 12) Update and delete transactions
- `PUT /api/v1/transactions/{id}` accepts the same required fields as create and returns the updated transaction.
- `DELETE /api/v1/transactions/{id}` returns `200` with a success message.
- Both operations are scoped to the authenticated user; another user's transaction returns `404`.

## Financial Insights

Manual insights are saved separately from automatically generated dashboard analysis.

- `GET /api/v1/insights?month=11&year=2026` lists manual insights. `month` and `year` filters are optional.
- `POST /api/v1/insights` creates an insight with required `message` (max 500 characters), `month` (1-12), and `year` (2000-32767); returns `201`.
- `GET /api/v1/insights/{id}` reads one insight owned by the authenticated user.
- `PUT /api/v1/insights/{id}` updates the same fields as create.
- `DELETE /api/v1/insights/{id}` deletes the insight.
- Reading or mutating another user's insight returns `404`.

## Reports

### 13) Reports Summary
- `GET /api/v1/reports/summary?start_date=2026-01-01&end_date=2026-11-11`
- Auth required.
- Response `200`:
```json
{
  "status": "success",
  "data": {
    "total_income": 88000000.00,
    "total_expense": 45000000.00,
    "net_savings": 43000000.00,
    "category_breakdown": [
      {
        "category_name": "Makanan & Minuman",
        "total_amount": 18000000.00,
        "percentage": 40.0
      }
    ],
    "monthly_trends": [
      {
        "month": "2026-09",
        "income": 8000000.00,
        "expense": 3800000.00
      }
    ]
  }
}
```

### 14) Chart Data
- `GET /api/v1/reports/chart-data?start_date=2026-01-01&end_date=2026-11-11`
- Auth required.
- Returns the same reporting payload as `/reports/summary` for chart rendering.
- Both dates must use `YYYY-MM-DD`; `end_date` must not precede `start_date`.
- If omitted, the range defaults to the first day of the current month through today.

## HTTP Status Codes
- `200 OK` — successful fetch or login.
- `201 Created` — transaction created or user registered.
- `401 Unauthorized` — missing or invalid token.
- `404 Not Found` — resource not found or access to another user’s transaction.
- `422 Unprocessable Entity` — validation or credentials error.
