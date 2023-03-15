<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>"{{ $title ?? 'Default Title' }}"</title>
        <meta name="description" content="{{ $description ?? 'Default Description' }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @yield('links')
    </head>
    <body>
        <header>
        @yield('header')
        </header>
        @yield('body')
        <footer>
            @yield('footer')
        </footer>
    </body>
</html>