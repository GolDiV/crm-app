<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm sticky-top">
    <div class="container-fluid mx-5 px-0 px-xl-5">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <i class="bi bi-house"></i>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">
            {{-- Левое меню --}}
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('companies.*') ? 'active' : '' }}" href="{{ route('companies.index') }}">Компании</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('contacts.*') ? 'active' : '' }}" href="{{ route('contacts.index') }}">Контакты</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}" href="{{ route('projects.index') }}">Проекты</a></li>

                {{-- Справочники --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('lectors.*', 'regions.*', 'sferas.*', 'interes.*', 'statuses.*', 'our_companies.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                        Справочники
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item {{ request()->routeIs('lectors.*') ? 'active' : '' }}" href="{{ route('lectors.index') }}">Лектора</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('regions.*') ? 'active' : '' }}" href="{{ route('regions.index') }}">Регионы</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('sferas.*') ? 'active' : '' }}" href="{{ route('sferas.index') }}">Сферы деятельности</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('interes.*') ? 'active' : '' }}" href="{{ route('interes.index') }}">Темы</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('statuses.*') ? 'active' : '' }}" href="{{ route('statuses.index') }}">Статусы</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('our_companies.*') ? 'active' : '' }}" href="{{ route('our_companies.index') }}">Наши компании</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}" href="{{ route('tasks.index') }}">
                        Задачи <span class="badge bg-light text-dark">{{ $tasksCount ?? '' }}</span>
                    </a>
                </li>
            </ul>

            {{-- Правое меню пользователя --}}
            @auth
             @if(auth()->user()->role?->slug == 'admin')
                        
                            <a href="#" class="nav-link px-2 text-dark" title="Панель администратора">
                                <i class="bi bi-gear-fill fs-5"></i>
                            </a>
                       
                    @endif

                <ul class="navbar-nav mb-2 mb-lg-0">
                   
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            {{ Auth::user()->name ?? 'Пользователь' }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if(Auth::user()->role)
                                <li class="dropdown-item text-muted small text-uppercase">
                                    {{ Auth::user()->role->name }}
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            @endif
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Профиль</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Выход</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            @endauth

            @guest
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Вход</a></li>
                    @if (Route::has('register'))
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Регистрация</a></li>
                    @endif
                </ul>
            @endguest
        </div>
    </div>
</nav>
