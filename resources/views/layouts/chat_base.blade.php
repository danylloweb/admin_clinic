<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Chat')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- CSS --}}
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/chat.css') }}">
    @stack('styles')
</head>
<body>
@yield('content')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
@stack('scripts')
</body>
</html>
