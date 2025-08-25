<!doctype html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gambling Task</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen grid place-items-center bg-gray-50">
    <main class="p-4 bg-white rounded-2xl shadow md:px-24 py-8 w-full">
        <div id="app"></div>
    </main>
    </body>
</html>
