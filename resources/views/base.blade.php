<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="/css/snipper.css">

    <title>Search view</title>
</head>
<body>
@include('searchForm')
<script src="/js/searchForm.js"></script>
</body>
</html>
