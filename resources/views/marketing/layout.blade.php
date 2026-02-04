<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Asylon')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    @stack('meta')
    @php($assetBase = asset('asylonhtml/asylon'))
    <link rel="stylesheet" href="{{ $assetBase }}/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ $assetBase }}/css/slick.css">
    <link rel="stylesheet" href="{{ $assetBase }}/css/slick-theme.css">
    <link rel="stylesheet" href="{{ $assetBase }}/css/ui.css">
    <link rel="stylesheet" href="{{ $assetBase }}/css/media.css">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <style>
        .dropdown-toggle-no-caret::after { display: none !important; }
        .language-switcher .form-select {
            min-width: 72px;
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
<header class="site-header">
    <div class="site-container">
        <div class="header-wrapper">
            <div class="header-logo">
                <a href="{{ route('marketing.home') }}">
                    <img src="{{ $assetBase }}/images/logo.png" alt="{{ __('marketing.common.site_logo_alt') }}">
                </a>
                <div class="mobile-menu">
                    <button id="menuBtn">
                        <img src="{{ $assetBase }}/images/menu-icon.png" alt="{{ __('marketing.common.menu_alt') }}">
                    </button>
                </div>
            </div>

            <nav class="header-nav">
                <ul class="nav-list">
                    <li class="nav-item"><a href="{{ route('marketing.home') }}" class="nav-link">{{ __('frontend.nav.home') }}</a></li>
                    <li class="nav-item"><a href="{{ route('marketing.about') }}" class="nav-link">{{ __('frontend.nav.about') }}</a></li>
                    <li class="nav-item"><a href="{{ route('marketing.how') }}" class="nav-link">{{ __('frontend.nav.how_it_works') }}</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle dropdown-toggle-no-caret" href="#" id="solutionsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ __('frontend.nav.solutions') }} <img src="{{ asset('asylonhtml/asylon/images/vVector.png') }}" alt="">
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="solutionsDropdown">
                            <li><a class="dropdown-item" href="{{ route('marketing.solutions.schools') }}">{{ __('frontend.nav.solutions_schools') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('marketing.solutions.churches') }}">{{ __('frontend.nav.solutions_churches') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('marketing.solutions.organizations') }}">{{ __('frontend.nav.solutions_organizations') }}</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a href="{{ route('marketing.feature') }}" class="nav-link">{{ __('frontend.nav.features') }}</a></li>
                    <li class="nav-item"><a href="{{ route('marketing.resources') }}" class="nav-link">{{ __('frontend.nav.resources') }}</a></li>
                    <li class="nav-item"><a href="{{ route('blog.index') }}" class="nav-link">{{ __('frontend.nav.blog') }}</a></li>
                    <li class="nav-item"><a href="{{ route('marketing.contact') }}" class="nav-link">{{ __('frontend.nav.contact') }}</a></li>
                </ul>
            </nav>

            <div class="header-actions">
                <div class="language-switcher" style="width: 17px; float: right; padding-left: 8px; padding-top: 10px;">
                    <label class="visually-hidden" for="site-language">{{ __('frontend.language.label') }}</label>
                    <div class="dropdown">
                        <button class="btn btn-link p-0 text-decoration-none dropdown-toggle dropdown-toggle-no-caret" type="button"
                            id="site-language" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ __('frontend.language.label') }}">
                            <i class="fa-solid {{ app()->getLocale() === 'es' ? 'fa-flag' : 'fa-flag-usa' }}"></i>
                            <span class="ms-1">{{ strtoupper(app()->getLocale()) }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="site-language">
                            <li>
                                <a class="dropdown-item" href="{{ route('locale.switch', 'en') }}">
                                    <i class="fa-solid fa-flag-usa me-2"></i>EN
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('locale.switch', 'es') }}">
                                    <i class="fa-solid fa-flag me-2"></i>ES
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <a href="{{ route('marketing.demo') }}" class="site-btn-dark">{{ __('frontend.nav.book_demo') }}</a>
            </div>
        </div>
    </div>
</header>

<div class="side-menu" id="sideMenu">
    <span class="close-btn" id="closeMenu"><i class="fa-solid fa-xmark"></i></span>

    <ul class="nav-list">
        <li class="nav-item"><a href="{{ route('marketing.home') }}" class="nav-link">{{ __('frontend.nav.home') }}</a></li>
        <li class="nav-item"><a href="{{ route('marketing.about') }}" class="nav-link">{{ __('frontend.nav.about') }}</a></li>
        <li class="nav-item"><a href="{{ route('marketing.how') }}" class="nav-link">{{ __('frontend.nav.how_it_works') }}</a></li>
        <li class="nav-item"><a href="{{ route('marketing.solutions.schools') }}" class="nav-link">{{ __('frontend.nav.solutions') }}</a></li>
        <li class="nav-item"><a href="{{ route('marketing.solutions.schools') }}" class="nav-link">{{ __('frontend.nav.solutions_schools') }}</a></li>
        <li class="nav-item"><a href="{{ route('marketing.solutions.churches') }}" class="nav-link">{{ __('frontend.nav.solutions_churches') }}</a></li>
        <li class="nav-item"><a href="{{ route('marketing.solutions.organizations') }}" class="nav-link">{{ __('frontend.nav.solutions_organizations') }}</a></li>
        <li class="nav-item"><a href="{{ route('marketing.feature') }}" class="nav-link">{{ __('frontend.nav.features') }}</a></li>
        <li class="nav-item"><a href="{{ route('marketing.resources') }}" class="nav-link">{{ __('frontend.nav.resources') }}</a></li>
        <li class="nav-item"><a href="{{ route('blog.index') }}" class="nav-link">{{ __('frontend.nav.blog') }}</a></li>
        <li class="nav-item"><a href="{{ route('marketing.contact') }}" class="nav-link">{{ __('frontend.nav.contact') }}</a></li>
        <li class="nav-item"><a href="{{ route('marketing.demo') }}" class="nav-link">{{ __('frontend.nav.book_demo') }}</a></li>
        <li class="nav-item mt-2">
            <div class="language-switcher" style="width: 17px; float: right; padding-left: 8px; padding-top: 10px;">
                <label class="visually-hidden" for="site-language-mobile">{{ __('frontend.language.label') }}</label>
                <div class="dropdown">
                    <button class="btn btn-link p-0 text-decoration-none dropdown-toggle dropdown-toggle-no-caret" type="button"
                        id="site-language-mobile" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ __('frontend.language.label') }}">
                        <i class="fa-solid {{ app()->getLocale() === 'es' ? 'fa-flag' : 'fa-flag-usa' }}"></i>
                        <span class="ms-1">{{ strtoupper(app()->getLocale()) }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="site-language-mobile">
                        <li>
                            <a class="dropdown-item" href="{{ route('locale.switch', 'en') }}">
                                <i class="fa-solid fa-flag-usa me-2"></i>EN
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('locale.switch', 'es') }}">
                                <i class="fa-solid fa-flag me-2"></i>ES
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </li>
    </ul>
</div>

<div class="menu-overlay" id="menuOverlay"></div>

@yield('content')

<footer class="site-footer">
    <div class="site-container">
        <div class="footer-grid">
                <div class="footer-bx">
                    <div class="site-logo">
                        <a href="{{ route('marketing.home') }}">
                            <img src="{{ $assetBase }}/images/f-logo.png" alt="">
                        </a>
                    </div>
                <p>{{ __('frontend.footer.description') }}</p>
            </div>

            <div class="footer-right">
                <div class="footer-bx">
                    <div class="footer-title">
                        <h4>{{ __('frontend.footer.links_title') }}</h4>
                    </div>
                    <ul>
                        <li><a href="{{ route('marketing.home') }}">{{ __('frontend.nav.home') }}</a></li>
                        <li><a href="{{ route('marketing.about') }}">{{ __('frontend.nav.about') }}</a></li>
                        <li><a href="{{ route('marketing.how') }}">{{ __('frontend.nav.how_it_works') }}</a></li>
                        <li><a href="{{ route('marketing.feature') }}">{{ __('frontend.nav.features') }}</a></li>
                        <li><a href="{{ route('marketing.resources') }}">{{ __('frontend.nav.resources') }}</a></li>
                        <li><a href="{{ route('blog.index') }}">{{ __('frontend.nav.blog') }}</a></li>
                        <li><a href="{{ route('report.create') }}">{{ __('frontend.nav.submit_report') }}</a></li>
                        <li><a href="{{ route('login') }}">{{ __('frontend.nav.login') }}</a></li>
                        <li><a href="{{ route('marketing.contact') }}">{{ __('frontend.nav.contact') }}</a></li>
                    </ul>
                </div>
                <div class="footer-bx">
                    <div class="footer-title">
                        <h4>{{ __('frontend.footer.legal_title') }}</h4>
                    </div>
                    <ul>
                        <li><a href="{{ route('marketing.privacy') }}">{{ __('frontend.footer.privacy') }}</a></li>
                        <li><a href="{{ route('marketing.terms') }}">{{ __('frontend.footer.terms') }}</a></li>
                        <li><a href="{{ route('marketing.data_security') }}">{{ __('frontend.footer.data_security') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="copy-right">
        <p>{{ __('frontend.footer.copyright') }}</p>
    </div>
</footer>

<script src="{{ $assetBase }}/js/jquery-3.7.1.min.js"></script>
<script src="{{ $assetBase }}/js/bootstrap.bundle.min.js"></script>
<script src="{{ $assetBase }}/js/slick.min.js"></script>

<script>
    $(function () {
        $('.testimonials-slider').slick({
            slidesToShow: 3,
            slidesToScroll: 1,
            dots: true,
            arrows: false,
            autoplay: true,
            autoplaySpeed: 3000,
            responsive: [
                {
                    breakpoint: 1025,
                    settings: {
                        slidesToShow: 1,
                        centerMode: true,
                        centerPadding: '50px'
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 1
                    }
                }
            ]
        });
    });

    const menuBtn = document.getElementById('menuBtn');
    const sideMenu = document.getElementById('sideMenu');
    const closeMenu = document.getElementById('closeMenu');
    const overlay = document.getElementById('menuOverlay');

    const closeAll = () => {
        sideMenu?.classList.remove('active');
        overlay?.classList.remove('active');
    };

    if (menuBtn && sideMenu && overlay && closeMenu) {
        menuBtn.addEventListener('click', () => {
            sideMenu.classList.add('active');
            overlay.classList.add('active');
        });

        closeMenu.addEventListener('click', closeAll);
        overlay.addEventListener('click', closeAll);
    }
</script>
@stack('scripts')
</body>
</html>
