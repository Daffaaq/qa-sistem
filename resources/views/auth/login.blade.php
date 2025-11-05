<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login Arsip Document</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="icon" href="assets/images/logo.webp" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="{{ asset('auth/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link rel="stylesheet" href="{{ asset('auth/css/sweetalert2.min.css') }}">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, rgba(62, 75, 91, 0.95) 0%, rgba(44, 62, 80, 0.95) 100%),
                url('{{ asset('img/bg.jpg') }}') center/cover no-repeat;
            background-blend-mode: overlay;
            height: 100vh;
            display: flex;
            align-items: center;
        }

        .login-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .login-card:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }

        .login-left {
            background-color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-right {
            background-color: white;
            padding: 3rem;
        }

        .logo-img {
            max-width: 80%;
            height: auto;
            transition: transform 0.3s ease;
        }

        .logo-img:hover {
            transform: scale(1.05);
        }

        .login-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1.5rem;
            position: relative;
        }

        .login-title:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background-color: var(--secondary-color);
        }

        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }

        .btn-login {
            background-color: var(--primary-color);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .footer-links {
            font-size: 0.85rem;
            color: var(--dark-color);
        }

        .footer-links a {
            color: var(--secondary-color);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: var(--accent-color);
        }

        .alert {
            border-radius: 8px;
        }

        .form-check-label {
            margin-left: 5px;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('modernize/libs/sweetalert2/dist/sweetalert2.min.css') }}">
    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="card login-card">
                    <div class="row g-0">
                        <div class="col-md-6 login-left">
                            <img src="{{ asset('img/logoipgv2.svg') }}" alt="Company Logo" class="logo-img">
                        </div>
                        <div class="col-md-6 login-right">
                            <div class="card-body p-4">
                                <form method="POST" action="{{ route('login') }}" id="loginForm">
                                    @csrf

                                    <h2 class="login-title text-center">LOGIN</h2>

                                    <p class="text-muted mb-4">Please enter your credentials to access the system</p>
                                    @if (session('logoutSuccess'))
                                        <div class="alert alert-success text-center py-1 mb-2 small" role="alert"
                                            id="logoutAlert">
                                            <i class="ti ti-check-circle"></i>
                                            {{ session('logoutSuccess') }}
                                        </div>
                                    @endif

                                    @if (session('loginFailed'))
                                        <div class="alert alert-danger text-center py-1 mb-2 small" role="alert"
                                            id="loginFailedAlert">
                                            <i class="ti ti-alert-circle"></i>
                                            {{ session('loginFailed') }}
                                        </div>
                                    @endif
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" id="email" name="email" class="form-control"
                                            placeholder="Enter your registration number">
                                        @error('email')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" id="password" name="password" class="form-control"
                                            placeholder="Enter your password">
                                        @error('password')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror

                                    </div>

                                    <div class="d-grid mb-4">
                                        <button type="submit" class="btn btn-primary btn-login">LOGIN</button>
                                    </div>

                                    <div class="text-center footer-links">
                                        <span>IPG Plant 1</span> |
                                        <span>Information Systems</span>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('modernize/libs/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script>
        @if (session('loginSuccess'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('loginSuccess') }}',
                timer: 1500,
                showConfirmButton: false,
                willClose: () => {
                    // Setelah SweetAlert selesai, redirect ke dashboard
                    window.location.href = "{{ route('dashboard') }}";
                }
            });
        @endif


        setTimeout(function() {
            const logoutAlert = document.getElementById('logoutAlert');
            if (logoutAlert) {
                logoutAlert.style.display = 'none';
            }

            const loginFailedAlert = document.getElementById('loginFailedAlert');
            if (loginFailedAlert) {
                loginFailedAlert.style.display = 'none';
            }
        }, 5000);
    </script>
</body>
