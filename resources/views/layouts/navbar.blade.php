<nav class="navbar navbar-expand-lg bg-primary">
    <div class="container">
        <a class="navbar-brand fw-bold text-white" href="#">crickbuzz</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
            aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav ms-auto">
                {{-- <a class="nav-link text-white fw-bold mx-2" href="{{ route('teams') }}">Teams</a>
                <a class="nav-link text-white fw-bold mx-2" href="{{ route('players') }}">Players</a>
                <a class="nav-link text-white fw-bold mx-2" href="{{ route('matches') }}">Matches</a>
                <a class="nav-link text-white fw-bold mx-2" href="{{ route('venues') }}">Venues</a> --}}
                <a class="nav-link text-white fw-bold mx-2" href="{{ route('dashboard') }}">Dashboard</a>
                {{-- <a class="nav-link text-white fw-bold mx-2" href="{{ route('get.live.matches') }}">Live</a> --}}
                <a class="nav-link fw-bold mx-2" href="{{ route('logout') }}" style="color: red">Logout</a>
            </div>
        </div>
    </div>
</nav>
