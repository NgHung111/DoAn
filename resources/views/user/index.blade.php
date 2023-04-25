<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @include('user.layouts.head')
    @include('user.layouts.css')
    @include('user.layouts.js')
    
</head>
<body>
    <div class="contain_main">
        @include('user.layouts.header')
        @include('user.layouts.menu')
        @include('user.layouts.mmenu')
        @include('user.layouts.slide')
        <div class="wap-home">
            @include('user.home.home')
        </div>
        @include('user.layouts.footer')
    </div>
</body>
</html>