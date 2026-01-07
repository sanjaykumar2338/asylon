
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? ($headerTitle ?? config('app.name', 'Laravel')) }}</title>
        <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">

        <link rel="stylesheet" href="{{ asset('admin-theme/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('admin-theme/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('admin-theme/css/responsive-ui.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
            integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
            crossorigin="anonymous" referrerpolicy="no-referrer">
        @stack('styles')
    </head>
    <body>
        @php
            $navUser = Auth::user();
            $navUnreadCount = $navUser ? $navUser->unreadNotifications()->count() : 0;
            $navRecentNotifications = $navUser
                ? $navUser->notifications()->latest()->limit(10)->get()
                : collect();
            $profilePhoto = $navUser?->profile_photo_url ?: asset('admin-theme/images/TopBar.png');
            $isSuper = $navUser?->isSuperAdmin();
            $isPlatform = $navUser?->isPlatformAdmin();
            $isExec = $navUser?->hasRole('executive_admin');
            $isOrgAdmin = $navUser?->isOrgAdmin();
            $isReviewer = $navUser?->hasRole('reviewer');
        @endphp
        <main class="admin-root">
            <div class="admin-block-wrapper">
                <aside class="admin-sidebar">
                    <div class="sidebar-logo">
                        <a href="{{ route('dashboard') }}" class="text-decoration-none d-inline-block">
                            <img src="{{ asset('admin-theme/images/IMG_6451 1 (1).png') }}"
                                alt="{{ config('app.name', 'Asylon') }}">
                        </a>
                    </div>

                    <nav class="sidebar-nav">
                        <ul class="sidebar-menu">
                            <li class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <a href="{{ route('dashboard') }}">
                                    <span class="icon"><img src="{{ asset('admin-theme/images/Symbol.png') }}"
                                            alt=""></span>
                                    <span class="text">{{ __('dashboard.nav') }}</span>
                                </a>
                            </li>

                            <li class="sidebar-item {{ request()->routeIs('reviews.*') ? 'active' : '' }}">
                                <a href="{{ route('reviews.index') }}">
                                    <span class="icon"><img src="{{ asset('admin-theme/images/Symbol-1.png') }}"
                                            alt=""></span>
                                    <span class="text">{{ __('reports.nav') }}</span>
                                </a>
                            </li>
                            @if ($isSuper || $isPlatform)
                                <li class="sidebar-item {{ request()->routeIs('admin.orgs.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.orgs.index') }}">
                                        <span class="icon"><img src="{{ asset('admin-theme/images/Symbol3.png') }}"
                                                alt=""></span>
                                        <span class="text">{{ __('Organizations') }}</span>
                                    </a>
                                </li>
                                <li class="sidebar-item {{ request()->routeIs('admin.escalation-rules.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.escalation-rules.index') }}">
                                        <span class="icon"><img src="{{ asset('admin-theme/images/Symbol4.png') }}"
                                                alt=""></span>
                                        <span class="text">{{ __('Escalation Rules') }}</span>
                                    </a>
                                </li>
                                <li class="sidebar-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.users.index') }}">
                                        <span class="icon"><img src="{{ asset('admin-theme/images/Symbol5.png') }}"
                                                alt=""></span>
                                        <span class="text">{{ __('Users') }}</span>
                                    </a>
                                </li>
                                <li class="sidebar-item {{ request()->routeIs('admin.alerts.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.alerts.index') }}">
                                        <span class="icon"><img src="{{ asset('admin-theme/images/Symbol6.png') }}"
                                                alt=""></span>
                                        <span class="text">{{ __('Alert Contacts') }}</span>
                                    </a>
                                </li>
                                <li class="sidebar-item {{ request()->routeIs('admin.notifications.templates.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.notifications.templates.edit') }}">
                                        <span class="icon"><img src="{{ asset('admin-theme/images/Symbol7.png') }}"
                                                alt=""></span>
                                        <span class="text">{{ __('Notification Templates') }}</span>
                                    </a>
                                </li>
                                <li class="sidebar-item {{ request()->routeIs('admin.report-categories.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.report-categories.index') }}">
                                        <span class="icon"><img src="{{ asset('admin-theme/images/Symbol8.png') }}"
                                                alt=""></span>
                                        <span class="text">{{ __('Report Categories') }}</span>
                                    </a>
                                </li>
                                <li class="sidebar-item {{ request()->routeIs('admin.risk-keywords.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.risk-keywords.index') }}">
                                        <span class="icon"><img src="{{ asset('admin-theme/images/Symbol10.png') }}"
                                                alt=""></span>
                                        <span class="text">{{ __('Risk keywords') }}</span>
                                    </a>
                                </li>
                                <li class="sidebar-item {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                                    <a href="{{ route('admin.analytics') }}">
                                        <span class="icon"><img src="{{ asset('admin-theme/images/Symbol9.png') }}"
                                                alt=""></span>
                                        <span class="text">{{ __('Analytics') }}</span>
                                    </a>
                                </li>
                                <li class="sidebar-item {{ request()->routeIs('admin.demo-requests.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.demo-requests.index') }}">
                                        <span class="icon"><i class="fa-solid fa-calendar-check"></i></span>
                                        <span class="text">{{ __('Demo Requests') }}</span>
                                    </a>
                                </li>
                                <li class="sidebar-item {{ request()->routeIs('admin.contact-messages.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.contact-messages.index') }}">
                                        <span class="icon"><i class="fa-solid fa-envelope"></i></span>
                                        <span class="text">{{ __('Contact Messages') }}</span>
                                    </a>
                                </li>
                                <li class="sidebar-item {{ request()->routeIs('platform.organizations.*') ? 'active' : '' }}">
                                    <a href="{{ route('platform.organizations.index') }}">
                                        <span class="icon"><i class="fa-solid fa-layer-group"></i></span>
                                        <span class="text">{{ __('Platform Orgs') }}</span>
                                    </a>
                                </li>
                                @if ($isSuper)
                                    <li class="sidebar-item {{ request()->routeIs('platform.billing.revenue') ? 'active' : '' }}">
                                        <a href="{{ route('platform.billing.revenue') }}">
                                            <span class="icon"><i class="fa-solid fa-chart-line"></i></span>
                                            <span class="text">{{ __('Revenue Dashboard') }}</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-item {{ request()->routeIs('platform.billing.subscriptions.*') ? 'active' : '' }}">
                                        <a href="{{ route('platform.billing.subscriptions.index') }}">
                                            <span class="icon"><i class="fa-solid fa-file-invoice-dollar"></i></span>
                                            <span class="text">{{ __('Subscriptions') }}</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-item {{ request()->routeIs('platform.plans.*') ? 'active' : '' }}">
                                        <a href="{{ route('platform.plans.index') }}">
                                            <span class="icon"><i class="fa-solid fa-credit-card"></i></span>
                                            <span class="text">{{ __('Plans & Pricing') }}</span>
                                        </a>
                                    </li>
                                @endif
                                <li class="sidebar-item {{ request()->routeIs('admin.data_requests.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.data_requests.index') }}">
                                        <span class="icon"><i class="fa-solid fa-eraser"></i></span>
                                        <span class="text">{{ __('Data Requests') }}</span>
                                    </a>
                                </li>
                                @if ($isSuper)
                                    <li class="sidebar-item {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.audit-logs.index') }}">
                                            <span class="icon"><i class="fa-solid fa-clipboard-list"></i></span>
                                            <span class="text">{{ __('Audit Logs') }}</span>
                                        </a>
                                    </li>
                                @endif
                                <li class="sidebar-item {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.pages.index') }}">
                                        <span class="icon"><img src="{{ asset('admin-theme/images/Symbol11.png') }}"
                                                alt=""></span>
                                        <span class="text">{{ __('Pages') }}</span>
                                    </a>
                                </li>
                                <li class="sidebar-item {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.menus.index') }}">
                                        <span class="icon"><img src="{{ asset('admin-theme/images/Symbol13.png') }}"
                                                alt=""></span>
                                        <span class="text">{{ __('Menus') }}</span>
                                    </a>
                                </li>
                                <li class="sidebar-item {{ request()->routeIs('admin.blog-posts.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.blog-posts.index') }}">
                                        <span class="icon"><i class="fa-solid fa-blog"></i></span>
                                        <span class="text">{{ __('Blog Posts') }}</span>
                                    </a>
                                </li>
                                <li class="sidebar-item {{ request()->routeIs('admin.blog-categories.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.blog-categories.index') }}">
                                        <span class="icon"><i class="fa-solid fa-folder-open"></i></span>
                                        <span class="text">{{ __('Blog Categories') }}</span>
                                    </a>
                                </li>
                                <li class="sidebar-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.settings.edit') }}">
                                        <span class="icon"><img src="{{ asset('admin-theme/images/Symbol14.png') }}"
                                                alt=""></span>
                                        <span class="text">{{ __('Settings') }}</span>
                                    </a>
                                </li>
                            @elseif ($isExec || $isOrgAdmin)
                                <li class="sidebar-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.users.index') }}">
                                        <span class="icon"><img src="{{ asset('admin-theme/images/Symbol5.png') }}"
                                                alt=""></span>
                                        <span class="text">{{ __('Users') }}</span>
                                    </a>
                                </li>
                                <li class="sidebar-item {{ request()->routeIs('admin.alerts.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.alerts.index') }}">
                                        <span class="icon"><img src="{{ asset('admin-theme/images/Symbol6.png') }}"
                                                alt=""></span>
                                        <span class="text">{{ __('Alert Contacts') }}</span>
                                    </a>
                                </li>
                                <li class="sidebar-item {{ request()->routeIs('admin.notifications.templates.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.notifications.templates.edit') }}">
                                        <span class="icon"><img src="{{ asset('admin-theme/images/Symbol7.png') }}"
                                                alt=""></span>
                                        <span class="text">{{ __('Notification Templates') }}</span>
                                    </a>
                                </li>
                                <li class="sidebar-item {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                                    <a href="{{ route('admin.analytics') }}">
                                        <span class="icon"><img src="{{ asset('admin-theme/images/Symbol9.png') }}"
                                                alt=""></span>
                                        <span class="text">{{ __('Analytics') }}</span>
                                    </a>
                                </li>
                                <li class="sidebar-item {{ request()->routeIs('admin.demo-requests.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.demo-requests.index') }}">
                                        <span class="icon"><i class="fa-solid fa-calendar-check"></i></span>
                                        <span class="text">{{ __('Demo Requests') }}</span>
                                    </a>
                                </li>
                                <li class="sidebar-item {{ request()->routeIs('admin.contact-messages.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.contact-messages.index') }}">
                                        <span class="icon"><i class="fa-solid fa-envelope"></i></span>
                                        <span class="text">{{ __('Contact Messages') }}</span>
                                    </a>
                                </li>
                                @if ($isExec)
                                    <li class="sidebar-item {{ request()->routeIs('billing.*') ? 'active' : '' }}">
                                        <a href="{{ route('billing.settings') }}">
                                            <span class="icon"><i class="fa-solid fa-credit-card"></i></span>
                                            <span class="text">{{ __('Billing') }}</span>
                                        </a>
                                    </li>
                                @endif
                                <li class="sidebar-item {{ request()->routeIs('settings.organization.*') ? 'active' : '' }}">
                                    <a href="{{ route('settings.organization.edit') }}">
                                        <span class="icon"><i class="fa-solid fa-building"></i></span>
                                        <span class="text">{{ __('Org Settings') }}</span>
                                    </a>
                                </li>
                                <li class="sidebar-item {{ request()->routeIs('admin.blog-posts.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.blog-posts.index') }}">
                                        <span class="icon"><i class="fa-solid fa-blog"></i></span>
                                        <span class="text">{{ __('Blog Posts') }}</span>
                                    </a>
                                </li>
                                <li class="sidebar-item {{ request()->routeIs('admin.blog-categories.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.blog-categories.index') }}">
                                        <span class="icon"><i class="fa-solid fa-folder-open"></i></span>
                                        <span class="text">{{ __('Blog Categories') }}</span>
                                    </a>
                                </li>
                            @elseif ($isReviewer)
                                <li class="sidebar-item {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                                    <a href="{{ route('admin.analytics') }}">
                                        <span class="icon"><img src="{{ asset('admin-theme/images/Symbol9.png') }}"
                                                alt=""></span>
                                        <span class="text">{{ __('Analytics') }}</span>
                                    </a>
                                </li>
                            @endif

                            <li class="sidebar-item mt-3">
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <span class="icon"><i class="fa-solid fa-right-from-bracket"></i></span>
                                    <span class="text">{{ __('Log Out') }}</span>
                                </a>
                            </li>
                        </ul>
                    </nav>

                    <div id="closeSidebar" class="sidebar-close">
                        <i class="fa-solid fa-xmark"></i>
                    </div>
                </aside>

                <div class="admin-content">
                    <div class="admin-header">
                        <div class="left-block">
                            <span>
                                @isset($header)
                                    {{ $header }}
                                @else
                                    {{ $headerTitle ?? '' }}
                                @endisset
                            </span>
                            <div class="mobile-menu">
                                <div></div>
                                <div></div>
                                <div></div>
                            </div>
                        </div>
                        <div class="right-block">
                            <div class="account-root" id="notificationsRoot">
                                <button type="button" id="notificationsToggle">
                                    <img src="{{ asset('admin-theme/images/Icon (1).png') }}" class="img-fluid"
                                        alt="{{ __('Notifications') }}">
                                    @if ($navUnreadCount > 0)
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ $navUnreadCount > 9 ? '9+' : $navUnreadCount }}
                                        </span>
                                    @endif
                                </button>

                                <div class="dropdown-menu" id="notificationsMenu">
                                    <div class="px-3 py-2 d-flex justify-content-between align-items-center border-bottom">
                                        <span class="fw-semibold">{{ __('common.notifications') }}</span>
                                        <form method="POST" action="{{ route('notifications.markAllRead') }}">
                                            @csrf
                                            <button type="submit" class="btn btn-link p-0 text-decoration-none">
                                                {{ __('common.mark_all_read') }}
                                            </button>
                                        </form>
                                    </div>
                                    <ul class="list-unstyled mb-0">
                                        @forelse ($navRecentNotifications as $notification)
                                            @php
                                                $data = $notification->data ?? [];
                                                $title = $data['title'] ?? __('common.notification_single');
                                                $message = $data['message'] ?? '';
                                                $url = $data['url'] ?? route('notifications.index');
                                            @endphp
                                            <li class="border-bottom">
                                                <a href="{{ $url }}" class="d-block text-decoration-none px-3 py-2">
                                                    <span class="d-block fw-semibold">{{ $title }}</span>
                                                    @if ($message)
                                                        <small
                                                            class="d-block text-muted">{{ \Illuminate\Support\Str::limit($message, 90) }}</small>
                                                    @endif
                                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('notifications.markRead', $notification->getKey()) }}"
                                                    class="px-3 pb-2">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-link p-0 text-decoration-none">
                                                        {{ $notification->read_at ? __('Viewed') : __('Mark read') }}
                                                    </button>
                                                </form>
                                            </li>
                                        @empty
                                            <li class="px-3 py-3 text-muted text-center">
                                                {{ __('common.no_notifications') }}
                                            </li>
                                        @endforelse
                                    </ul>
                                    <div class="px-3 py-2 text-center border-top">
                                        <a href="{{ route('notifications.index') }}" class="text-decoration-none">
                                            {{ __('common.view_all_notifications') }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="account-root" id="accountRoot">
                                <span class="profile-image">
                                    <img src="{{ $profilePhoto }}" alt="{{ $navUser?->name ?? 'User' }}">
                                </span>
                                <span class="icon-wrapper" id="dropdownToggle">
                                    <div class="icon">
                                        <img src="{{ asset('admin-theme/images/right-arrow.png') }}" alt="">
                                    </div>
                                </span>

                                <div class="dropdown-menu" id="dropdownMenu">
                                    <ul>
                                        <li>
                                            <a href="{{ route('profile.edit') }}">
                                                {{ __('Profile') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('logout') }}"
                                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                {{ __('Log Out') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="admin-root-block">
                        @hasSection('breadcrumb')
                            <div class="mb-3">
                                @yield('breadcrumb')
                            </div>
                        @endhasSection

                        @php
                            $layoutOrg = auth()->user()?->org;
                        @endphp
                        @if ($layoutOrg && $layoutOrg->billing_status === 'trialing' && $layoutOrg->trial_ends_at)
                            <div class="alert alert-warning">
                                <i class="fa-solid fa-circle-info me-2"></i>
                                {{ __('Your trial ends on :date.', ['date' => $layoutOrg->trial_ends_at->format('M d, Y')]) }}
                                @if (! is_null($layoutOrg->trial_days_left))
                                    <span class="ms-2 text-muted">({{ $layoutOrg->trial_days_left }} {{ __('days left') }})</span>
                                @endif
                                <span class="ms-2">{{ __('Payment setup will be available soon.') }}</span>
                            </div>
                        @endif
                        @php
                            $canManageBilling = auth()->user()?->hasRole(['platform_admin', 'executive_admin']);
                        @endphp
                        @if ($layoutOrg && $layoutOrg->billing_status !== 'active' && $canManageBilling)
                            <div class="alert alert-warning d-flex justify-content-between align-items-center">
                                <span>{{ __('Your organization does not have an active subscription yet. Please choose a plan to continue using Asylon.') }}</span>
                                <a href="{{ route('billing.choose_plan') }}" class="btn btn-sm btn-primary">
                                    {{ __('Choose a Plan') }}
                                </a>
                            </div>
                        @endif

                        @hasSection('content')
                            @yield('content')
                        @else
                            @hasSection('page-content')
                                @yield('page-content')
                            @else
                                @hasSection('body')
                                    @yield('body')
                                @else
                                    {{ $slot ?? '' }}
                                @endhasSection
                            @endhasSection
                        @endhasSection
                    </div>

                    @php
                        $supportEmail = config('asylon.support_email', 'support@asylon.cc');
                    @endphp
                    <footer class="py-3 px-4 text-muted border-top">
                        <div class="d-flex flex-wrap justify-content-between gap-2">
                            <div>
                                <strong>&copy; {{ now()->year }} {{ config('app.name', 'Admin') }}.</strong>
                                {{ __('All rights reserved.') }}
                                <span class="ms-2">|</span>
                                <a class="text-reset" href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>
                                <span class="ms-2">|</span>
                                <a class="text-reset"
                                    href="{{ route('signup.show') }}">{{ __('Get Started as New Organization') }}</a>
                            </div>
                            <div>
                                {{ __('Admin Panel') }}
                                @if (Route::has('support'))
                                    <span class="mx-2">|</span>
                                    <a href="{{ route('support') }}" class="text-reset">{{ __('Support') }}</a>
                                @endif
                            </div>
                        </div>
                    </footer>
                </div>
            </div>
        </main>

        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="d-none">
            @csrf
        </form>

        <script src="{{ asset('admin-theme/js/jquery-3.7.1.min.js') }}"></script>
        <script src="{{ asset('admin-theme/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('admin-theme/js/admin.js') }}"></script>
        @vite(['resources/js/app.js'])
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const notificationsToggle = document.getElementById('notificationsToggle');
                const notificationsMenu = document.getElementById('notificationsMenu');

                if (!notificationsToggle || !notificationsMenu) {
                    return;
                }

                notificationsToggle.addEventListener('click', function (event) {
                    event.stopPropagation();
                    const isOpen = notificationsMenu.style.display === 'block';
                    notificationsMenu.style.display = isOpen ? 'none' : 'block';
                });

                document.body.addEventListener('click', function () {
                    notificationsMenu.style.display = 'none';
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        notificationsMenu.style.display = 'none';
                    }
                });
            });
        </script>
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
