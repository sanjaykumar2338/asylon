@extends('marketing.layout')

@section('title', 'Asylon | Register')

@push('meta')
    <style>
        .auth-form-section .section-title p {
            margin-top: 8px;
            color: #484848;
        }

        .password-field {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .password-field input {
            flex: 1;
        }

        .password-toggle {
            width: 58px;
            height: 55px;
            border-radius: 12px;
            border: 1px solid #dcdcdc;
            background: #f7f8fb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.22);
            cursor: pointer;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .password-toggle:hover {
            background: #eef1f6;
            color: #0b1f3b;
        }

        .auth-form-footer {
            margin-top: 18px;
            text-align: center;
            color: #484848;
            font-size: 14px;
        }

        .auth-form-footer a {
            color: #0b1f3b;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    <section class="inner-pages-header">
        <div class="site-container">
            <div class="page-header">
                <div class="section-title">
                    <h2>{{ __('Register') }}</h2>
                </div>
                <div class="page-link">
                    <span><a href="{{ route('marketing.home') }}">{{ __('Home') }} </a></span>
                    <span>/</span>
                    <span><a href="{{ route('register') }}">{{ __('Register') }}</a></span>
                </div>
            </div>
        </div>
    </section>

    <section class="demo-form-section block-left auth-form-section">
        <div class="site-container">
            <div class="section-title text-center">
                <h2>{{ __('Create your account') }}</h2>
                <p>{{ __('Join Asylon to manage reports, demos, and organization settings.') }}</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="demo-form" method="POST" action="{{ route('register') }}">
                @csrf
                <div class="form-grid">
                    <div class="form-group full">
                        <label for="name">{{ __('Full Name') }}</label>
                        <input id="name"
                               type="text"
                               name="name"
                               value="{{ old('name') }}"
                               placeholder="{{ __('Enter your name') }}"
                               required
                               autofocus
                               autocomplete="name">
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group full">
                        <label for="email">{{ __('Work Email') }}</label>
                        <input id="email"
                               type="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="{{ __('Enter your email') }}"
                               required
                               autocomplete="username">
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group full">
                        <label for="password">{{ __('Password') }}</label>
                        <div class="password-field">
                            <input id="password"
                                   type="password"
                                   name="password"
                                   placeholder="{{ __('Create a password') }}"
                                   required
                                   autocomplete="new-password">
                            <button type="button"
                                    class="password-toggle"
                                    data-target="password"
                                    aria-label="{{ __('Show password') }}">
                                <svg class="eye-open" width="22" height="22" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.03555 12.3224C1.96647 12.1151 1.9664 11.8907 2.03536 11.6834C3.42372 7.50972 7.36079 4.5 12.0008 4.5C16.6387 4.5 20.5742 7.50692 21.9643 11.6776C22.0334 11.8849 22.0335 12.1093 21.9645 12.3166C20.5761 16.4903 16.6391 19.5 11.9991 19.5C7.36119 19.5 3.42564 16.4931 2.03555 12.3224Z"
                                          stroke="currentColor"
                                          stroke-width="1.5"
                                          stroke-linecap="round"
                                          stroke-linejoin="round" />
                                    <path d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z"
                                          stroke="currentColor"
                                          stroke-width="1.5"
                                          stroke-linecap="round"
                                          stroke-linejoin="round" />
                                </svg>
                                <svg class="eye-closed d-none" width="22" height="22" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3.97993 8.22257C3.05683 9.31382 2.35242 10.596 1.93436 12.0015C3.22565 16.338 7.24311 19.5 11.9991 19.5C12.9917 19.5 13.9521 19.3623 14.8623 19.1049M6.22763 6.22763C7.88389 5.13558 9.86771 4.5 12 4.5C16.756 4.5 20.7734 7.66205 22.0647 11.9985C21.3528 14.3919 19.8106 16.4277 17.772 17.772M6.22763 6.22763L3 3M6.22763 6.22763L9.87868 9.87868M17.772 17.772L21 21M17.772 17.772L14.1213 14.1213M14.1213 14.1213C14.6642 13.5784 15 12.8284 15 12C15 10.3431 13.6569 9 12 9C11.1716 9 10.4216 9.33579 9.87868 9.87868M14.1213 14.1213L9.87868 9.87868"
                                          stroke="currentColor"
                                          stroke-width="1.5"
                                          stroke-linecap="round"
                                          stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group full">
                        <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                        <div class="password-field">
                            <input id="password_confirmation"
                                   type="password"
                                   name="password_confirmation"
                                   placeholder="{{ __('Re-enter your password') }}"
                                   required
                                   autocomplete="new-password">
                            <button type="button"
                                    class="password-toggle"
                                    data-target="password_confirmation"
                                    aria-label="{{ __('Show password') }}">
                                <svg class="eye-open" width="22" height="22" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.03555 12.3224C1.96647 12.1151 1.9664 11.8907 2.03536 11.6834C3.42372 7.50972 7.36079 4.5 12.0008 4.5C16.6387 4.5 20.5742 7.50692 21.9643 11.6776C22.0334 11.8849 22.0335 12.1093 21.9645 12.3166C20.5761 16.4903 16.6391 19.5 11.9991 19.5C7.36119 19.5 3.42564 16.4931 2.03555 12.3224Z"
                                          stroke="currentColor"
                                          stroke-width="1.5"
                                          stroke-linecap="round"
                                          stroke-linejoin="round" />
                                    <path d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z"
                                          stroke="currentColor"
                                          stroke-width="1.5"
                                          stroke-linecap="round"
                                          stroke-linejoin="round" />
                                </svg>
                                <svg class="eye-closed d-none" width="22" height="22" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3.97993 8.22257C3.05683 9.31382 2.35242 10.596 1.93436 12.0015C3.22565 16.338 7.24311 19.5 11.9991 19.5C12.9917 19.5 13.9521 19.3623 14.8623 19.1049M6.22763 6.22763C7.88389 5.13558 9.86771 4.5 12 4.5C16.756 4.5 20.7734 7.66205 22.0647 11.9985C21.3528 14.3919 19.8106 16.4277 17.772 17.772M6.22763 6.22763L3 3M6.22763 6.22763L9.87868 9.87868M17.772 17.772L21 21M17.772 17.772L14.1213 14.1213M14.1213 14.1213C14.6642 13.5784 15 12.8284 15 12C15 10.3431 13.6569 9 12 9C11.1716 9 10.4216 9.33579 9.87868 9.87868M14.1213 14.1213L9.87868 9.87868"
                                          stroke="currentColor"
                                          stroke-width="1.5"
                                          stroke-linecap="round"
                                          stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="site-btn-dark">
                    {{ __('Register') }}
                </button>

                <div class="auth-form-footer">
                    {{ __('Already registered?') }}
                    <a href="{{ route('login') }}">{{ __('Log in') }}</a>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const showLabel = @json(__('Show password'));
            const hideLabel = @json(__('Hide password'));

            document.querySelectorAll('.password-toggle').forEach((toggle) => {
                const targetId = toggle.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const eyeOpen = toggle.querySelector('.eye-open');
                const eyeClosed = toggle.querySelector('.eye-closed');

                if (!input || !eyeOpen || !eyeClosed) {
                    return;
                }

                toggle.addEventListener('click', () => {
                    const isVisible = input.type === 'text';
                    input.type = isVisible ? 'password' : 'text';

                    const nowVisible = input.type === 'text';
                    eyeOpen.classList.toggle('d-none', nowVisible);
                    eyeClosed.classList.toggle('d-none', !nowVisible);
                    toggle.setAttribute('aria-label', nowVisible ? hideLabel : showLabel);
                });
            });
        });
    </script>
@endpush
