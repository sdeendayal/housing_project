<!DOCTYPE html>
<html lang="en">

<head>

    @include('mmgay.layouts.head')

</head>

<body class="flex min-h-screen overflow-hidden">

    @include('mmgay.layouts.sidebar')

    <div class="flex-1 ml-[260px]">

        @include('mmgay.layouts.header')

        <main class="mt-16 p-6 bg-background min-h-screen">

            @yield('content')

        </main>

    </div>

    @include('mmgay.layouts.scripts')

</body>

</html>