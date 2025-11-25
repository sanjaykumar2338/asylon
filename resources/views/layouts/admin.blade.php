<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? ($headerTitle ?? config('app.name', 'Laravel')) }}</title>
        <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">

        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=swap">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
        @stack('styles')
    </head>
    <body class="hold-transition sidebar-mini layout-fixed">
        <div class="wrapper">
            <!-- Navbar -->
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                    </li>
                </ul>

                <ul class="navbar-nav ml-auto">
                    <!-- User Dropdown Menu -->
                    <li class="nav-item dropdown">
                        <a class="nav-link" data-toggle="dropdown" href="#">
                            <i class="far fa-user"></i>
                            <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                <i class="fas fa-id-card mr-2"></i> {{ __('Profile') }}
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}" class="px-3 py-1">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-block btn-sm">
                                    <i class="fas fa-sign-out-alt mr-2"></i> {{ __('Log Out') }}
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </nav>
            <!-- /.navbar -->

            <!-- Main Sidebar Container -->
            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <a href="{{ route('dashboard') }}" class="brand-link text-decoration-none">
                    <span class="brand-text font-weight-light">{{ config('app.name', 'Admin') }}</span>
                </a>

                <div class="sidebar">
                    <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
                        <div class="image">
                            <img src="{{ Auth::user()->profile_photo_url }}"
                                class="img-circle elevation-2" alt="{{ Auth::user()->name }}" width="40" height="40">
                        </div>
                        <div class="info">
                            <span class="d-block text-white">{{ Auth::user()->name }}</span>
                            <small class="text-muted d-block">{{ Auth::user()->email }}</small>
                        </div>
                    </div>

                    <nav class="mt-2">
                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                            <li class="nav-item">
                                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-home"></i>
                                    <p>{{ __('dashboard.nav') }}</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('reviews.index') }}" class="nav-link {{ request()->routeIs('reviews.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-file-alt"></i>
                                    <p>{{ __('reports.nav') }}</p>
                                </a>
                            </li>

                            @if(auth()->user()?->hasRole(['org_admin', 'platform_admin']))
                                <li class="nav-item">
                                    <a href="{{ route('admin.orgs.index') }}" class="nav-link {{ request()->routeIs('admin.orgs.*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-building"></i>
                                        <p>{{ __('Organizations') }}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-users"></i>
                                        <p>{{ __('Users') }}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.alerts.index') }}" class="nav-link {{ request()->routeIs('admin.alerts.*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-bell"></i>
                                        <p>{{ __('Alert Contacts') }}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.report-categories.index') }}" class="nav-link {{ request()->routeIs('admin.report-categories.*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-tags"></i>
                                        <p>{{ __('Report Categories') }}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.analytics') }}" class="nav-link {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-chart-line"></i>
                                        <p>{{ __('Analytics') }}</p>
                                    </a>
                                </li>
                                @if(auth()->user()?->hasRole('platform_admin'))
                                    <li class="nav-item">
                                        <a href="{{ route('admin.settings.edit') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-tools"></i>
                                            <p>{{ __('Settings') }}</p>
                                        </a>
                                    </li>
                                @endif
                            @endif

                            <li class="nav-item mt-3">
                                <form method="POST" action="{{ route('logout') }}" onsubmit="return window.confirm('{{ __('Are you sure you want to log out?') }}');">
                                    @csrf
                                    <button type="submit" class="nav-link btn btn-link text-left text-white">
                                        <i class="nav-icon fas fa-sign-out-alt"></i>
                                        <p>{{ __('Log Out') }}</p>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </nav>
                </div>
            </aside>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                @isset($header)
                                    <h1 class="m-0 text-dark">{{ $header }}</h1>
                                @else
                                    <h1 class="m-0 text-dark">{{ $headerTitle ?? '' }}</h1>
                                @endisset
                            </div>
                            <div class="col-sm-6">
                                @yield('breadcrumb')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="content">
                    <div class="container-fluid">
                        {{ $slot }}
                    </div>
                </div>
            </div>

            @php
                $supportEmail = config('asylon.support_email', 'support@asylon.cc');
            @endphp
            <footer class="main-footer text-sm">
                <div class="float-right d-none d-sm-inline">
                    {{ __('Admin Panel') }}
                    @if (Route::has('support'))
                        <span class="mx-2">|</span>
                        <a href="{{ route('support') }}" class="text-reset">{{ __('Support') }}</a>
                    @endif
                </div>
                <div>
                    <strong>&copy; {{ now()->year }} {{ config('app.name', 'Admin') }}.</strong> {{ __('All rights reserved.') }}
                    <span class="ml-2">|</span>
                    <a class="text-reset" href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>
                </div>
            </footer>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
        @vite(['resources/js/app.js'])
        @php
            $flashOk = session('ok');
            $flashError = session('error');
        @endphp
        @if ($flashOk || $flashError)
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    if (window.Swal) {
                        @if ($flashOk)
                            Swal.fire({
                                icon: 'success',
                                title: @json($flashOk),
                                confirmButtonColor: '#2563eb',
                                customClass: {
                                    popup: 'shadow-lg'
                                }
                            });
                        @endif

                        @if ($flashError)
                            Swal.fire({
                                icon: 'error',
                                title: @json($flashError),
                                confirmButtonColor: '#dc2626',
                                customClass: {
                                    popup: 'shadow-lg'
                                }
                            });
                        @endif
                    }
                });
            </script>
        @endif
        @stack('scripts')
    </body>
</html>
