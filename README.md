# Finance App
This is a finance app build using Laravel 7 + Vue.js + Tailwind CSS

The user should be able to Add, Edit, Delete and Import transactions.


## Instalation
This app is currently working with Laravel Echo with the Pusher driver which means you need to 
use your pusher api keys to have it working 100%.

## API Endpoints
```
GET /api/users/{user}/transactions/total-by-date?date=2020-06-17
```
returns the user's total transactions for a given date

```
{
  "data": {
    "total": -1363.14
  }
}
```
---

```
GET /api/users/{user}/transactions
```
returns all transactions for that given user. Users pagination of 100

Optional Query Params:
- include_total_per_date=1
- include_total_balance=1

```
  "data": [
    {
      "id": 502,
      "label": "quam cum id",
      "amount": -915.2,
      "transaction_at": "2020-06-18T00:12:41",
      "user_id": 1
    },
    {
      "id": 505,
      "label": "molestias totam ut",
      "amount": 674.39,
      "transaction_at": "2020-06-18T00:12:41",
      "user_id": 1
    },
  ],
  "total_per_date": {
    "2020-06-18": 1952.77,
    "2020-06-17": -1363.14,
    "2020-06-15": 342.72,
    "2020-06-14": 706.18,
    "2020-06-13": -2061.34,
    "2020-06-12": -2040.69,
    "2020-06-11": -1816.16,
    "2020-06-10": 953.17
  },
  "total_balance": -14748.42,
  "links": {
    "first": "http://127.0.0.1:8000/api/users/1/transactions?page=1",
    "last": "http://127.0.0.1:8000/api/users/1/transactions?page=6",
    "prev": null,
    "next": "http://127.0.0.1:8000/api/users/1/transactions?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 6,
    "path": "http://127.0.0.1:8000/api/users/1/transactions",
    "per_page": 100,
    "to": 100,
    "total": 510
  }
}
```

---

```
POST /api/users/{user}/transactions
```
Creates a new transaction for a given user

Expects:
```
{
  "label": "some label",
  "transaction_at": "2000-01-01 20:00:00",
  "amount": 100.99
}
```

---
```
GET /api/users/{user}/balance
```
Return user's current balance

```
{
  "data": {
    "balance": -14708.42
  }
}
```

--- 
```
DELETE /api/transactions/{transaction}
```
Deletes a given transaction

---

```
PUT /api/transactions/{transaction}
```
Updates a given transaction

Expects:
```
{
  "label": "some label",
  "transaction_at": "2000-01-01 20:00:00",
  "amount": 100.99
}
```

```
GET /api/users/{user}/transaction-imports/importing
```
Checks whether a user is currently importing a balance file or not

Returns:
```
{
  "data": {
    "importing": true,
    "transaction_import": {
      "id": 43,
      "user_id": 1,
      "file_path": "users/1/transactions/1592790834_5000-balance-entries.csv",
      "file_name": "5000-balance-entries.csv",
      "total_records": 5000,
      "failed_at": null,
      "finished_at": null,
      "created_at": "2020-06-22T01:53:54.000000Z",
      "updated_at": "2020-06-22T01:54:08.000000Z"
    }
  }
}
```

---

```
POST /api/users/{user}/transaction-imports
```
Imports a new CSV balance file

Expects a file called `transactions`
and return the total number of records for that file

```
{
  "data": {
    "records": 5000
  }
}
```
