 @extends('layout.cdn')
 @section('content2')
     <div class="container-fluid">
         <div class="row">
             <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                 <div class="sidebar-brand">
                     <i class="fas fa-robot"></i>
                     <span>AnnounceBot</span>
                 </div>

                 <div class="user-section text-center">
                     @auth
                         <small class="text-muted d-block mb-1">Welcome back,</small>
                         <strong>{{ Auth::user()->name }}</strong>
                     @endauth
                     @guest
                         <a href="{{ route('login') }}" class="btn btn-primary btn-sm w-100 rounded-pill">
                             Login Account
                         </a>
                     @endguest
                 </div>

                 <ul class="nav flex-column">
                     <li class="nav-item">
                         <a href="/dashboard"
                             class="nav-link link-dashboard {{ Request::is('dashboard') ? 'active' : '' }}">
                             <i class="fas fa-chart-pie"></i> Dashboard
                         </a>
                     </li>
                     <li class="nav-item">
                         <a href="/groups" class="nav-link link-groups {{ Request::is('groups') ? 'active' : '' }}">
                             <i class="fa-solid fa-layer-group"></i> Groups
                         </a>
                     </li>
                     <li class="nav-item">
                         <a href="/paymentlogs"
                             class="nav-link link-payments {{ Request::is('paymentlogs') ? 'active' : '' }}">
                             <i class="fa-solid fa-receipt"></i> Payment Logs
                         </a>
                     </li>
                 </ul>

                 @auth
                     <form action="{{ route('logout') }}" method="POST" class="mt-auto">
                         @csrf
                         <button type="submit" class="btn btn-logout nav-link">
                             <i class="fas fa-sign-out-alt"></i> Logout
                         </button>
                     </form>
                 @endauth
             </nav>

             <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                 <div class="pt-4">
                     @yield('content')
                 </div>
             </main>
         </div>
     </div>
 @endsection
