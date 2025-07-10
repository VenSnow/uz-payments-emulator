<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <title>Callback Result</title>
    <style>
        body { font-family: sans-serif; background: #111; color: #eee; padding: 2rem; }
        .box { background: #222; padding: 1rem; border-radius: 8px; }
        pre { white-space: pre-wrap; word-wrap: break-word; }
    </style>
</head>
<body>
<h1>🔄 Callback Page</h1>
<div class="box">
    <h3>Request Data:</h3>
    <pre>{{ json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
</div>
</body>
</html>
