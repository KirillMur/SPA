<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="/css/app.css">

    <title>Search view</title>
</head>
<body>
<div id="mainDiv">
    <p><input type="text" name="name" id="nm"><label for="nm"> Enter to search by name</label></p>
    <p><input type="text" name="price" id="pr"><label for="pr"> ..by price</label></p>
    <p><input type="text" name="bedrooms" id="bdr"><label for="bdr"> ..by number of bedrooms</label></p>
    <p><input type="text" name="bathrooms" id="btr"><label for="btr"> ..by number of bathrooms</label></p>
    <p><input type="text" name="storeys" id="st"><label for="st"> ..by number of storeys</label></p>
    <p> <input type="text" name="garages" id="gr"><label for="gr"> ..by number of garages</label></p>
    <button id="btn">Go!</button>
</div>
<script src="/js/searchForm.js"></script>
</body>
</html>
