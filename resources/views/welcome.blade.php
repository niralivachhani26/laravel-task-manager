<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Welcome to Task Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<div class="container text-center mt-5">
    <h1>Welcome to Task Manager</h1>
    <p class="lead">Manage your projects and tasks efficiently.</p>

    <a href="{{ route('login') }}" class="btn btn-primary mx-2">Login</a>
    <a href="{{ route('register') }}" class="btn btn-success mx-2">Register</a>
</div>

</body>
</html>
