<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
    <style>
        body {
            background-color: #f8d7da;
            color: #721c24;
            padding: 40px;
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .error-box {
            background: #f1b0b7;
            padding: 20px;
            border-radius: 10px;
            display: inline-block;
        }
    </style>
</head>
<body>
<div class="error-box">
    <h1>Error</h1>
    <p>{{ $message }}</p>
</div>
</body>
</html>
