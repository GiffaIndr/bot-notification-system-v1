<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <link href="{{asset("assets/css/sb-admin-2.min.css")}}" rel="stylesheet">
    <link href="{{asset("assets/css/sb-admin-2.css")}}" rel="stylesheet"> --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>library</title>
</head>

<body>

    <div class="container-fluid">

        <div class="row">

            <!-- Sidebar -->
            <div class="col-md-2 bg-dark text-white vh-100 p-3">
                {{ Auth::user()->name }}
                <h4 class="mb-4">Announcement Bot</h4>

                <ul class="nav flex-column">

                    <li class="nav-item mb-2">
                        <a href="/dashboard" class="nav-link text-white">Dashboard</a>
                    </li>

                    <li class="nav-item mb-2">
                        <a href="/groups" class="nav-link text-white">My Groups</a>
                    </li>

                    <li class="nav-item mb-2">
                        <a href="/subscription" class="nav-link text-white">Subscription</a>
                    </li>

                </ul>

            </div>


            <!-- Content -->
            <div class="col-md-10 p-4">

                @yield('content')

            </div>

        </div>

    </div>

</body>
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
</script>


</html>
