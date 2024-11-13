<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login with Google</title>
</head>
<body>
    <!-- Button to redirect user to Google login -->
    <a href="{{ url('/api/auth/google/redirect') }}">
        <button>Login with Google</button>
    </a>
    <form action="{{ url('/api/auth/logout') }}" method="post">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>
