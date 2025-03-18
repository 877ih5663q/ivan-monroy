# Setup instructions

1.  Clone this repository.

2.  Run `composer install` inside the repository.

3.  Use `env.example` to create a new `.env` file.
    
    ```sh
    cp env.example .env
    ```

4.  Set the database credentials in the new `.env` file.
    
    ```sh
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=tasks
    DB_USERNAME=user
    DB_PASSWORD=password
    ```

5.  Apply the migrations with the `php artisan migrate` command.

6.  Run the app with the `composer run dev` command.


# Application endpoints

We can use the `curl` commands below to query the app's endpoints.

Note that the bearer token is obtained when the user queries the `/api/login` endpoint:

```sh
curl 'http://127.0.0.1:8000/api/login' \
  -X POST \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  --data-raw '{
    "email": "tsui@hark.com",
    "password": "tsui12345"
  }'

{"token":"2|f1v0wbqmzY42xCim7CkBhXVF5oXk6Q32vtlrObPya53c401c"}
```

In the example above, user 2 would pass the `2|f1v0wbqmzY42xCim7CkBhXVF5oXk6Q32vtlrObPya53c401c` in the `Authorization` header of their subsequent interactions with protected routes.

```sh
curl 'http://127.0.0.1:8000/api/register' \
  -X POST \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  --data-raw '{
    "name": "Ringo Lam",
    "email": "ringo@lam.com",
    "password": "ringo123",
    "password_confirmation": "ringo123"
  }'

curl 'http://127.0.0.1:8000/api/register' \
  -X POST \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  --data-raw '{
    "name": "Tsui Hark",
    "email": "tsui@hark.com",
    "password": "tsui12345",
    "password_confirmation": "tsui12345"
  }'

curl 'http://127.0.0.1:8000/api/login' \
  -X POST \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  --data-raw '{
    "email": "ringo@lam.com",
    "password": "ringo123"
  }'

curl 'http://127.0.0.1:8000/api/login' \
  -X POST \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  --data-raw '{
    "email": "tsui@hark.com",
    "password": "tsui12345"
  }'

curl 'http://127.0.0.1:8000/api/user' \
  -X GET \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|O8rY4eV6vdLaxdy0472KM1NbkVh4NmvwP1RiOtnM5825cb68"

curl -X POST 'http://127.0.0.1:8000/api/tasks' \
     -H "Authorization: Bearer 1|O8rY4eV6vdLaxdy0472KM1NbkVh4NmvwP1RiOtnM5825cb68" \
     -H "Content-Type: application/json" \
     --data-raw '{
           "title": "New Task",
           "description": "This is a new task",
           "status": "pending",
           "due_date": "2025-04-01",
           "assigned_to": 2
         }'

curl -X GET "http://127.0.0.1:8000/api/tasks" \
     -H "Authorization: Bearer 1|O8rY4eV6vdLaxdy0472KM1NbkVh4NmvwP1RiOtnM5825cb68"

curl -X GET "http://127.0.0.1:8000/api/tasks/1" \
     -H "Authorization: Bearer 1|O8rY4eV6vdLaxdy0472KM1NbkVh4NmvwP1RiOtnM5825cb68"

curl -X PUT "http://127.0.0.1:8000/api/tasks/1" \
     -H "Authorization: Bearer 1|O8rY4eV6vdLaxdy0472KM1NbkVh4NmvwP1RiOtnM5825cb68" \
     -H "Content-Type: application/json" \
     -d '{
           "title": "Updated Task",
           "description": "This is an updated task",
           "status": "in_progress",
           "due_date": "2023-12-31",
           "assigned_to": 2
         }'

curl -X DELETE "http://127.0.0.1:8000/api/tasks/1" \
     -H "Authorization: Bearer 1|O8rY4eV6vdLaxdy0472KM1NbkVh4NmvwP1RiOtnM5825cb68"

curl -X POST "http://127.0.0.1:8000/api/tasks/1/time-log" \
     -H "Content-Type: application/json" \
     -H "Authorization: Bearer 2|f1v0wbqmzY42xCim7CkBhXVF5oXk6Q32vtlrObPya53c401c" \
     --data-raw '{
         "minutes": 20
     }'
```
