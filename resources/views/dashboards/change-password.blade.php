@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
    <div class="password-page">
        <div class="password-heading">
            <div>
                <span class="password-eyebrow">ACCOUNT SECURITY</span>
                <h1>Change password</h1>
                <p>Keep your account secure by using a strong, unique password.</p>
            </div>
            <a href="{{ route('dashboard.profile.show') }}" class="back-link">
                <span aria-hidden="true">&larr;</span> Back to profile
            </a>
        </div>

        @if($errors->any())
            <div class="password-alert" role="alert">
                <span class="alert-icon" aria-hidden="true">!</span>
                <div>
                    <strong>Please check the information below.</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="password-layout">
            <section class="password-card" aria-labelledby="password-form-title">
                <div class="card-heading">
                    <div class="lock-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M7 10V7a5 5 0 0 1 10 0v3M6 10h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 14v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div>
                        <h2 id="password-form-title">Update your password</h2>
                        <p>Enter your current password, then choose a new one.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('dashboard.profile.password.update') }}" class="password-form">
                    @csrf
                    @method('PUT')

                    <div class="password-field">
                        <label for="current_password">Current password</label>
                        <div class="password-input-wrap">
                            <input id="current_password" type="password" name="current_password"
                                autocomplete="current-password" placeholder="Enter current password" required autofocus>
                            <button type="button" class="visibility-toggle" data-password-toggle="current_password" data-password-toggle-label="current password" aria-label="Show current password">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/>
                                </svg>
                            </button>
                        </div>
                        @error('current_password')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="password-field">
                        <label for="password">New password</label>
                        <div class="password-input-wrap">
                            <input id="password" type="password" name="password" autocomplete="new-password"
                                placeholder="Create a new password" minlength="8" required>
                            <button type="button" class="visibility-toggle" data-password-toggle="password" data-password-toggle-label="new password" aria-label="Show new password">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/>
                                </svg>
                            </button>
                        </div>
                        <span class="field-hint">Use at least 8 characters.</span>
                        @error('password')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="password-field">
                        <label for="password_confirmation">Confirm new password</label>
                        <div class="password-input-wrap">
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                autocomplete="new-password" placeholder="Re-enter your new password" minlength="8" required>
                            <button type="button" class="visibility-toggle" data-password-toggle="password_confirmation" data-password-toggle-label="password confirmation" aria-label="Show password confirmation">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/>
                                </svg>
                            </button>
                        </div>
                        @error('password_confirmation')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="password-actions">
                        <button type="submit" class="save-password">
                            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path d="m4 10 4 4 8-8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Update Password
                        </button>
                        <a href="{{ route('dashboard.profile.show') }}" class="cancel-password">Cancel</a>
                    </div>
                </form>
            </section>

            <aside class="security-note">
                <div class="security-note-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M12 3 4 6v5c0 5 3.4 8.5 8 10 4.6-1.5 8-5 8-10V6l-8-3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                        <path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h2>A safer password</h2>
                <p>Choose a password you don't use for other websites. A longer password is harder to guess.</p>
                <div class="security-divider"></div>
                <div class="security-tip">
                    <span class="tip-check" aria-hidden="true">✓</span>
                    <span>Use at least 8 characters</span>
                </div>
                <div class="security-tip">
                    <span class="tip-check" aria-hidden="true">✓</span>
                    <span>Don't reuse an old password</span>
                </div>
                <div class="security-tip">
                    <span class="tip-check" aria-hidden="true">✓</span>
                    <span>Never share your password</span>
                </div>
            </aside>
        </div>
    </div>

    <style>
        .password-page {
            --password-ink: #172033;
            --password-muted: #64748b;
            --password-border: #e2e8f0;
            max-width: 1080px;
            margin: 0 auto;
            padding: 12px 0 36px;
            color: var(--password-ink);
        }

        .password-heading {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
            margin-bottom: 28px;
        }

        .password-eyebrow {
            display: inline-block;
            margin-bottom: 8px;
            color: #4f46e5;
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .12em;
        }

        .password-heading h1 {
            margin: 0;
            color: var(--password-ink);
            font-size: clamp(1.75rem, 3vw, 2.25rem);
            font-weight: 750;
            letter-spacing: -.04em;
        }

        .password-heading p {
            margin: 8px 0 0;
            color: var(--password-muted);
            font-size: .97rem;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border: 1px solid var(--password-border);
            border-radius: 10px;
            background: #fff;
            color: #334155;
            font-size: .88rem;
            font-weight: 650;
            text-decoration: none;
            white-space: nowrap;
            transition: border-color .18s ease, color .18s ease, background .18s ease;
        }

        .back-link:hover {
            border-color: #c7d2fe;
            background: #f8faff;
            color: #4338ca;
        }

        .password-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.65fr) minmax(250px, .85fr);
            align-items: start;
            gap: 22px;
        }

        .password-card,
        .security-note {
            border: 1px solid var(--password-border);
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .055);
        }

        .password-card {
            padding: 28px;
        }

        .card-heading {
            display: flex;
            align-items: center;
            gap: 15px;
            padding-bottom: 23px;
            border-bottom: 1px solid #edf0f5;
        }

        .lock-icon,
        .security-note-icon {
            display: grid;
            flex: 0 0 auto;
            place-items: center;
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: #eef2ff;
            color: #4f46e5;
        }

        .lock-icon svg,
        .security-note-icon svg {
            width: 25px;
            height: 25px;
        }

        .card-heading h2,
        .security-note h2 {
            margin: 0;
            color: var(--password-ink);
            font-size: 1.08rem;
            font-weight: 750;
        }

        .card-heading p {
            margin: 5px 0 0;
            color: var(--password-muted);
            font-size: .88rem;
        }

        .password-form {
            display: grid;
            gap: 19px;
            padding-top: 24px;
        }

        .password-field label {
            display: block;
            margin-bottom: 7px;
            color: #334155;
            font-size: .87rem;
            font-weight: 700;
        }

        .password-input-wrap {
            position: relative;
        }

        .password-input-wrap input {
            display: block;
            box-sizing: border-box;
            width: 100%;
            min-height: 47px;
            padding: 11px 48px 11px 13px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            outline: none;
            background: #fff;
            color: #0f172a;
            font: inherit;
            font-size: .92rem;
            transition: border-color .18s ease, box-shadow .18s ease;
        }

        .password-input-wrap input::placeholder {
            color: #94a3b8;
        }

        .password-input-wrap input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, .13);
        }

        .visibility-toggle {
            position: absolute;
            top: 50%;
            right: 10px;
            display: grid;
            place-items: center;
            width: 30px;
            height: 30px;
            padding: 0;
            transform: translateY(-50%);
            border: 0;
            border-radius: 7px;
            background: transparent;
            color: #64748b;
            cursor: pointer;
        }

        .visibility-toggle:hover,
        .visibility-toggle:focus-visible {
            background: #f1f5f9;
            color: #4338ca;
            outline: none;
        }

        .visibility-toggle svg {
            width: 19px;
            height: 19px;
        }

        .field-hint,
        .field-error {
            display: block;
            margin-top: 6px;
            font-size: .79rem;
        }

        .field-hint {
            color: var(--password-muted);
        }

        .field-error {
            color: #b91c1c;
        }

        .password-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 3px;
            padding-top: 20px;
            border-top: 1px solid #edf0f5;
        }

        .save-password,
        .cancel-password {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 44px;
            padding: 0 17px;
            border-radius: 10px;
            font: inherit;
            font-size: .9rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: background .18s ease, box-shadow .18s ease, transform .18s ease;
        }

        .save-password {
            border: 1px solid #4f46e5;
            background: #4f46e5;
            color: #fff;
            box-shadow: 0 5px 12px rgba(79, 70, 229, .2);
        }

        .save-password:hover {
            transform: translateY(-1px);
            background: #4338ca;
            box-shadow: 0 7px 16px rgba(79, 70, 229, .25);
        }

        .save-password svg {
            width: 17px;
            height: 17px;
        }

        .cancel-password {
            border: 1px solid var(--password-border);
            background: #fff;
            color: #475569;
        }

        .cancel-password:hover {
            background: #f8fafc;
        }

        .security-note {
            padding: 25px;
            background: linear-gradient(145deg, #f8faff, #fff 80%);
        }

        .security-note-icon {
            margin-bottom: 17px;
        }

        .security-note h2 {
            font-size: 1.02rem;
        }

        .security-note > p {
            margin: 8px 0 19px;
            color: var(--password-muted);
            font-size: .88rem;
            line-height: 1.6;
        }

        .security-divider {
            height: 1px;
            margin-bottom: 17px;
            background: #e8edf5;
        }

        .security-tip {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-top: 13px;
            color: #475569;
            font-size: .84rem;
            line-height: 1.45;
        }

        .tip-check {
            display: grid;
            flex: 0 0 auto;
            place-items: center;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #dcfce7;
            color: #15803d;
            font-size: .72rem;
            font-weight: 800;
        }

        .password-alert {
            display: flex;
            gap: 12px;
            margin: 0 0 20px;
            padding: 14px 16px;
            border: 1px solid #fecaca;
            border-radius: 12px;
            background: #fff7f7;
            color: #7f1d1d;
            font-size: .88rem;
        }

        .password-alert strong {
            display: block;
            margin-bottom: 3px;
        }

        .password-alert ul {
            margin: 4px 0 0;
            padding-left: 18px;
        }

        .alert-icon {
            display: grid;
            flex: 0 0 auto;
            place-items: center;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #fee2e2;
            color: #b91c1c;
            font-weight: 800;
        }

        @media (max-width: 760px) {
            .password-heading {
                align-items: flex-start;
                flex-direction: column;
                gap: 15px;
            }

            .password-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .password-page {
                padding-top: 4px;
            }

            .password-card,
            .security-note {
                padding: 20px;
                border-radius: 14px;
            }

            .card-heading {
                align-items: flex-start;
            }

            .password-actions {
                align-items: stretch;
                flex-direction: column;
            }
        }
    </style>

    <script>
        document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                var input = document.getElementById(button.dataset.passwordToggle);
                var showing = input.type === 'password';

                input.type = showing ? 'text' : 'password';
                button.setAttribute('aria-label', (showing ? 'Hide ' : 'Show ') + button.dataset.passwordToggleLabel);
            });
        });
    </script>
@endsection
