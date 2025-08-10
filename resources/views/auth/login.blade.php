<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Вход в CRM</title>

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-light">
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow-sm w-100" style="max-width: 400px;">
            <div class="card-header bg-light text-dark text-center">
                <h4 class="mb-0">Вход в систему</h4>
            </div>
            <div class="card-body">

                {{-- Статус сессии --}}
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Логин --}}
                    <div class="mb-3">
                        <label for="login" class="form-label">Логин</label>
                        <input type="text" name="login" id="login" class="form-control @error('login') is-invalid @enderror"
                            value="{{ old('login') }}" required autofocus>
                        @error('login')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Пароль --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"
                            required autocomplete="current-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Запомнить меня --}}
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="remember" id="remember_me" class="form-check-input">
                        <label class="form-check-label" for="remember_me">Запомнить меня</label>
                    </div>

                    {{-- Кнопка входа --}}
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-primary">
                            Войти
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</body>
</html>
