@extends('marketing.layout')

@section('title', __('marketing.titles.resources'))

@section('content')
@php($assetBase = asset('asylonhtml/asylon'))
<section class="inner-pages-header">
    <div class="site-container">
        <div class="page-header">
            <div class="section-title">
                <h2>{{ __('frontend.nav.resources') }}</h2>
            </div>
            <div class="page-link">
                <span><a href="{{ route('marketing.home') }}">{{ __('frontend.nav.home') }} </a></span>
                <span>/</span>
                <span><a href="{{ route('marketing.resources') }}">{{ __('frontend.nav.resources') }}</a></span>

            </div>

        </div>
    </div>
</section>

<section class="Resources-Block block-left" >
    <div class="site-container">
        <div class="section-head text-center">
            <div class="section-subtitle">
                <span>{{ __('marketing.resources.section_label') }}</span>
            </div>
            <div class="section-title">
                <h2>{{ __('marketing.resources.watch_title') }}</h2>
            </div>
        </div>
        <div class="Resources-grid">
            <div class="video-block-card">
                <div class="main-video">
                    <video src="{{ $assetBase }}/images/1118041_1080p_4k_1280x720.mp4" poster="{{ $assetBase }}/images/1Rectangle.png"></video>
                    <div class="icon">
                        <img src="{{ $assetBase }}/images/videoVector.png" alt="">
                    </div>
                </div>
                <div class="video-title">
                    <h2>{{ __('marketing.resources.video_title') }}</h2>
                </div>
            </div>
            <div class="video-block-card">
                <div class="main-video">
                    <video src="{{ $assetBase }}/images/1118041_1080p_4k_1280x720.mp4" poster="{{ $assetBase }}/images/2Rectangle.png"></video>
                    <div class="icon">
                        <img src="{{ $assetBase }}/images/videoVector.png" alt="">
                    </div>
                </div>
                <div class="video-title">
                    <h2>{{ __('marketing.resources.video_title') }}</h2>
                </div>
            </div>

            <div class="video-block-card">
                <div class="main-video">
                    <video src="{{ $assetBase }}/images/1118041_1080p_4k_1280x720.mp4" poster="{{ $assetBase }}/images/3Rectangle3.png"></video>
                    <div class="icon">
                        <img src="{{ $assetBase }}/images/videoVector.png" alt="">
                    </div>
                </div>
                <div class="video-title">
                    <h2>{{ __('marketing.resources.video_title') }}</h2>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="report-section block-p2 bg2">
    <div class="site-container">
        <div class="report-grid">



            <div class="report-bx">
                <div class="section-header">

                    <div class="section-subtitle">
                        <span>{{ __('marketing.resources.download_label') }}</span>
                    </div>

                    <div class="section-title">
                        <h2>{{ __('marketing.resources.download_title') }}</h2>
                    </div>

                    <ul class="site-list">

                        <li>{{ __('marketing.resources.download_item_1') }}</li>
                        <li>{{ __('marketing.resources.download_item_2') }}</li>
                        <li>{{ __('marketing.resources.download_item_3') }}</li>
                        <li>{{ __('marketing.resources.download_item_4') }}</li>
                    </ul>
                    <div class="action-btn">
                        <a href="{{ route('marketing.resources') }}" class="site-btn-dark">{{ __('frontend.cta.download_guides') }}</a>
                    </div>

                </div>
            </div>

            <div class="report-bx">
                <div class="right-image">
                    <img src="{{ $assetBase }}/images/fGroup.png" alt="">
                </div>
            </div>


        </div>
    </div>


</section>

<section class="faq-section ">
    <div class="container">



        <div class="section-head text-center">
            <div class="section-subtitle">
                <span>{{ __('marketing.resources.faq_label') }}</span>
            </div>
            <div class="section-title">
                <h2>{{ __('marketing.resources.faq_title') }}</h2>
            </div>
        </div>

        <div class="row ">
            <div class="col-md-6">
                <div class="accordion" id="accordionLeft">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                {{ __('marketing.resources.faq_1_question') }}
                                <span class="ms-auto toggle-icon"><img src="{{ $assetBase }}/images/plus-1.png" alt=""></span>
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                            data-bs-parent="#accordionLeft">
                            <div class="accordion-body">
                                {{ __('marketing.resources.faq_1_answer') }}
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                {{ __('marketing.resources.faq_2_question') }}
                                <span class="ms-auto toggle-icon"><img src="{{ $assetBase }}/images/plus-1.png" alt=""></span>

                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                            data-bs-parent="#accordionLeft">
                            <div class="accordion-body">
                                {{ __('marketing.resources.faq_2_answer') }}
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                {{ __('marketing.resources.faq_3_question') }}
                                <span class="ms-auto toggle-icon"><img src="{{ $assetBase }}/images/plus-1.png" alt=""></span>


                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive"
                            data-bs-parent="#accordionLeft">
                            <div class="accordion-body">
                                {{ __('marketing.resources.faq_3_answer') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="accordion" id="accordionRight">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                {{ __('marketing.resources.faq_4_question') }}
                                <span class="ms-auto toggle-icon"><img src="{{ $assetBase }}/images/plus-1.png" alt=""></span>

                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                            data-bs-parent="#accordionRight">
                            <div class="accordion-body">
                                {{ __('marketing.resources.faq_4_answer') }}
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                {{ __('marketing.resources.faq_5_question') }}
                                <span class="ms-auto toggle-icon"><img src="{{ $assetBase }}/images/plus-1.png" alt=""></span>

                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                            data-bs-parent="#accordionRight">
                            <div class="accordion-body">
                                {{ __('marketing.resources.faq_5_answer') }}
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingSix">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                {{ __('marketing.resources.faq_6_question') }}
                                <span class="ms-auto toggle-icon"><img src="{{ $assetBase }}/images/plus-1.png" alt=""></span>

                            </button>
                        </h2>
                        <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix"
                            data-bs-parent="#accordionRight">
                            <div class="accordion-body">
                                {{ __('marketing.resources.faq_6_answer') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>document.querySelectorAll('.video-block-card').forEach(card => {
        const video = card.querySelector('video');
        const icon = card.querySelector('.icon');

        const togglePlay = () => {
            document.querySelectorAll('.video-block-card').forEach(otherCard => {
                const otherVideo = otherCard.querySelector('video');
                const otherIcon = otherCard.querySelector('.icon');

                if (otherVideo !== video) {
                    otherVideo.pause();
                    otherIcon.style.display = 'block';
                }
            });

            if (video.paused) {
                video.play();
                icon.style.display = 'none';
            } else {
                video.pause();
                icon.style.display = 'block';
            }
        };

        icon.addEventListener('click', togglePlay);

        video.addEventListener('click', togglePlay);

        video.addEventListener('ended', () => {
            icon.style.display = 'block';
        });
    });
</script>


<script>
    document.querySelectorAll('.accordion-button').forEach(btn => {
        btn.addEventListener('click', function () {
            const icon = this.querySelector('.toggle-icon');

            icon.style.transition = 'transform 0.3s ease';

            if (this.classList.contains('collapsed')) {
                icon.querySelector('img').src = '{{ $assetBase }}/images/mVecto.png';
                icon.style.transform = 'rotate(0deg)';
            } else {
                icon.querySelector('img').src = '{{ $assetBase }}/images/plus-1.png';
                icon.style.transform = 'rotate(43deg)';
            }
        });
    });
</script>
@endpush
