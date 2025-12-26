<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Asylon')</title>
    @php($assetBase = asset('asylonhtml/asylon'))
    <link rel="stylesheet" href="{{ $assetBase }}/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ $assetBase }}/css/slick.css">
    <link rel="stylesheet" href="{{ $assetBase }}/css/slick-theme.css">
    <link rel="stylesheet" href="{{ $assetBase }}/css/ui.css">
    <link rel="stylesheet" href="{{ $assetBase }}/css/media.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
          integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
          crossorigin="anonymous" referrerpolicy="no-referrer">
</head>
<body>
<header class="site-header">
    <div class="site-container">
        <div class="header-wrapper">
            <div class="header-logo">
                <a href="{{ route('marketing.home') }}">
                    <img src="{{ $assetBase }}/images/logo.png" alt="Site Logo">
                </a>
                <div class="mobile-menu">
                    <button id="menuBtn">
                        <img src="{{ $assetBase }}/images/menu-icon.png" alt="Menu">
                    </button>
                </div>
            </div>

            <nav class="header-nav">
                <ul class="nav-list">
                    <li class="nav-item"><a href="{{ route('marketing.home') }}" class="nav-link">Home</a></li>
                    <li class="nav-item"><a href="{{ route('marketing.about') }}" class="nav-link">About</a></li>
                    <li class="nav-item"><a href="{{ route('marketing.how') }}" class="nav-link">How it works</a></li>
                    <li class="nav-item">
                        <a href="{{ route('marketing.solutions.school') }}" class="nav-link">Solutions
                            <img src="{{ $assetBase }}/images/vVector.png" alt="">
                        </a>
                    </li>
                    <li class="nav-item"><a href="{{ route('marketing.feature') }}" class="nav-link">Features</a></li>
                    <li class="nav-item"><a href="{{ route('marketing.resources') }}" class="nav-link">Resources</a></li>
                    <li class="nav-item"><a href="{{ route('marketing.contact') }}" class="nav-link">Contact</a></li>
                </ul>
            </nav>

            <div class="header-actions">
                <a href="{{ route('marketing.demo') }}" class="site-btn-dark">Book a Demo</a>
            </div>
        </div>
    </div>
</header>

<div class="side-menu" id="sideMenu">
    <span class="close-btn" id="closeMenu"><i class="fa-solid fa-xmark"></i></span>

    <ul class="nav-list">
        <li class="nav-item"><a href="{{ route('marketing.home') }}" class="nav-link">Home</a></li>
        <li class="nav-item"><a href="{{ route('marketing.about') }}" class="nav-link">About</a></li>
        <li class="nav-item"><a href="{{ route('marketing.how') }}" class="nav-link">How it works</a></li>
        <li class="nav-item"><a href="{{ route('marketing.solutions.school') }}" class="nav-link">Solutions
                <img src="{{ $assetBase }}/images/vVector.png" alt=""></a></li>
        <li class="nav-item"><a href="{{ route('marketing.solutions.school') }}" class="nav-link">School</a></li>
        <li class="nav-item"><a href="{{ route('marketing.solutions.churches') }}" class="nav-link">Churches</a></li>
        <li class="nav-item"><a href="{{ route('marketing.solutions.organization') }}" class="nav-link">Organization</a></li>
        <li class="nav-item"><a href="{{ route('marketing.feature') }}" class="nav-link">Features</a></li>
        <li class="nav-item"><a href="{{ route('marketing.resources') }}" class="nav-link">Resources</a></li>
        <li class="nav-item"><a href="{{ route('marketing.contact') }}" class="nav-link">Contact</a></li>
        <li class="nav-item"><a href="{{ route('marketing.demo') }}" class="nav-link">Book a Demo</a></li>
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
                <p>Asylon is a secure reporting and threat-assessment platform helping schools, churches, and
                    organizations catch warning signs earlier and respond with confidence.</p>
            </div>

            <div class="footer-right">
                <div class="footer-bx">
                    <div class="footer-title">
                        <h4>Links</h4>
                    </div>
                    <ul>
                        <li><a href="{{ route('marketing.home') }}">Home</a></li>
                        <li><a href="{{ route('marketing.about') }}">About</a></li>
                        <li><a href="{{ route('marketing.how') }}">How it works</a></li>
                        <li><a href="{{ route('marketing.solutions.school') }}">Solutions <img src="{{ $assetBase }}/images/Vectora1.png" alt=""></a></li>
                        <li><a href="{{ route('marketing.feature') }}">Features</a></li>
                        <li><a href="{{ route('marketing.resources') }}">Resources</a></li>
                        <li><a href="{{ route('report.create') }}">Submit a Report</a></li>
                        <li><a href="{{ route('marketing.contact') }}">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-bx">
                    <div class="footer-title">
                        <h4>Legal links</h4>
                    </div>
                    <ul>
                        <li><a href="{{ route('marketing.privacy') }}">Privacy Policy</a></li>
                        <li><a href="{{ route('marketing.terms') }}">Terms of Use</a></li>
                        <li><a href="{{ route('marketing.data_security') }}">Data Security</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="copy-right">
        <p>(c) 2025 Asylon. All rights reserved.</p>
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
