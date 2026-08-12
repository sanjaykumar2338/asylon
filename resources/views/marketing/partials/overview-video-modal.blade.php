@php
    $overviewVideoUrl = asset('asylon-explainer-video.mp4');
@endphp

<div class="modal fade asylon-overview-modal" id="asylonOverviewVideoModal" tabindex="-1" aria-labelledby="asylonOverviewVideoTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <button type="button" class="asylon-overview-modal-close" data-bs-dismiss="modal" aria-label="{{ __('frontend.video_modal.close') }}">
                <span aria-hidden="true">&times;</span>
            </button>

            <div class="modal-body">
                <h2 id="asylonOverviewVideoTitle" class="visually-hidden">{{ __('frontend.cta.watch_overview') }}</h2>
                <div class="asylon-overview-video-frame">
                    <video id="asylonOverviewVideo" controls preload="metadata" playsinline>
                        <source src="{{ $overviewVideoUrl }}" type="video/mp4">
                        {{ __('frontend.video_modal.unsupported') }}
                    </video>
                </div>
            </div>
        </div>
    </div>
</div>
