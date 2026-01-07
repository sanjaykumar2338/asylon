@extends('marketing.layout')

@section('title', 'Asylon | Login')

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

        .auth-form-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .remember-checkbox {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #484848;
        }

        .auth-form-actions input[type="checkbox"] {
            width: auto;
            height: auto;
        }

        .auth-form-actions .forgot-link {
            font-size: 14px;
            font-weight: 600;
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
                    <h2>{{ __('Login') }}</h2>
                </div>
                <div class="page-link">
                    <span><a href="{{ route('marketing.home') }}">{{ __('Home') }} </a></span>
                    <span>/</span>
                    <span><a href="{{ route('login') }}">{{ __('Login') }}</a></span>
                </div>
            </div>
        </div>
    </section>

    <section class="demo-form-section block-left auth-form-section">
        <div class="site-container">
            <div class="section-title text-center">
                <h2>{{ __('Access your account') }}</h2>
                <p>{{ __('Log in to manage reports, demos, and organization settings.') }}</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success text-center" role="status">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="demo-form" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-grid">
                    <div class="form-group full">
                        <label for="email">{{ __('Work Email') }}</label>
                        <input id="email"
                               type="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="{{ __('Enter your email') }}"
                               required
                               autofocus
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
                                   placeholder="{{ __('Enter your password') }}"
                                   required
                                   autocomplete="current-password">
                            <button type="button"
                                    id="password-toggle"
                                    class="password-toggle"
                                    aria-label="{{ __('Show password') }}">
                                <svg id="password-eye-open" width="22" height="22" viewBox="0 0 24 24" fill="none"
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
                                <svg id="password-eye-closed" width="22" height="22" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg" class="d-none">
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
                </div>

                <div class="auth-form-actions">
                    <label for="remember_me" class="remember-checkbox">
                        <input id="remember_me" type="checkbox" name="remember">
                        <span>{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="forgot-link" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>

                <button type="submit" class="site-btn-dark">
                    {{ __('Log in') }}
                </button>

                <div class="auth-form-footer">
                    {{ __("Don't have an account?") }}
                    <a href="{{ route('register') }}">{{ __('Create one') }}</a>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('password-toggle');
            const eyeOpen = document.getElementById('password-eye-open');
            const eyeClosed = document.getElementById('password-eye-closed');

            if (!passwordInput || !toggleButton || !eyeOpen || !eyeClosed) {
                return;
            }

            toggleButton.addEventListener('click', () => {
                const isVisible = passwordInput.type === 'text';
                passwordInput.type = isVisible ? 'password' : 'text';

                eyeOpen.classList.toggle('d-none', !isVisible);
                eyeClosed.classList.toggle('d-none', isVisible);
                toggleButton.setAttribute('aria-label', isVisible ? '{{ __('Show password') }}' : '{{ __('Hide password') }}');
            });
        });
    </script>
@endpush
