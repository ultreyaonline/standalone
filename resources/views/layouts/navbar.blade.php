  <nav class="navbar navbar-expand-md navbar-light navbar-whitebg-shadow navbar-static-top mb-3">
    <div class="container">
      <div class="navbar-header">
        <a class="navbar-brand " href="{{ url('/') }}">
        <?php $favicon_file = strtolower(config('site.community_acronym', 'notfound') . '/favicon.png'); ?>
        @if(file_exists(public_path($favicon_file)))
            <img src="/{{ $favicon_file }}" height="40px">
        @else
            {{ config('site.community_acronym') }}
        @endif
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
          <span class="navbar-toggler-icon"></span>
        </button>

      </div>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <!-- Left Side Of Navbar -->
        <ul class="navbar-nav mr-auto nav-tabs">
@can('view members')
          <li class="nav-item"><a class="nav-link {{ \Route::is('home') ? 'active' : '' }}" href="{{ route('home') }}" data-shortcut="home">Main</a></li>
@endcan
@can('view members')
          <li class="nav-item"><a class="nav-link {{ \Route::is('calendar') ? 'active' : '' }}" href="{{ route('calendar') }}" data-shortcut="c">Calendar</a></li>
@endcan
@can('view members')
          <li class="nav-item"><a class="nav-link {{ \Route::is('weekend') ? 'active' : '' }}" href="{{ route('weekend') }}" data-shortcut="w">Weekends</a></li>
@endcan
@can('view members')
          <li class="nav-item"><a class="nav-link {{ \Route::is('directory') ? 'active' : '' }}" href="{{ route('directory') }}" data-shortcut="d">Directory</a></li>
@endcan
@can('menu-see-admin-pane')
          <li class="nav-item"><a class="nav-link {{ \Route::is('admin') ? 'active' : '' }}" href="{{ route('admin') }}" data-shortcut="admin">Admin</a></li>
@endcan
        @guest
          <li class="nav-item"><a class="nav-link" href="{{ url('/') }}" data-shortcut="home">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ url('/about') }}" data-shortcut="a">About</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ url('/believe') }}" data-shortcut="b">Beliefs</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ url('/calendar') }}" data-shortcut="c">Calendar</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ url('/history') }}" data-shortcut="h">History</a></li>
        @endguest
        </ul>



        @can('view members')
          <form action="/members" class="form-inline ml-auto my-2 my-lg-0" role="search">
            <input type="search" name="q" id="searchfield" class="form-control mr-sm-2" aria-label="Member Search" value="{{ Request::input('q') }}" placeholder="Member Search" accesskey="s">
            <button type="submit" class="btn btn-outline-secondary my-2 my-sm-0"><i class="fa fa-btn fa-search"></i>Search</button>
          </form>
        @endcan


      <!-- Right Side Of Navbar -->
        <ul class="navbar-nav ml-auto">
          <!-- Authentication Links -->
          @guest
            <li class="nav-item"><a href="{{ route('login') }}" class="nav-link" data-shortcut="l"><i class="fa fa-btn fa-sign-in"></i>{{ __('Login') }}</a></li>
          @else
            <li class="nav-item dropdown">
              <a id="navbarDropdown" class="nav-link dropdown-toggle @impersonating alert-danger @endImpersonating" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                {{ Auth::user()->name }}
              </a>

              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
              @impersonating
              <a class="bg-danger dropdown-item" href="{{ route('impersonate.leave') }}" data-shortcut="leave"><i class="fa fa-btn fa-times-circle" aria-hidden="true"></i>Leave impersonation</a>
              @endImpersonating
              <a class="dropdown-item" href="{{ url('/profile') }}" data-shortcut="me"><i class="fa fa-btn fa-user"></i>My Profile</a>
              <a class="dropdown-item" href="{{ route('logout') }}" data-shortcut="logout"
              onclick="event.preventDefault();
              document.getElementById('logout-form').submit();">
                  <i class="fa fa-btn fa-sign-out"></i>{{ __('Logout') }}
              </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                  @csrf
                </form>
              </div>
            </li>
          @endguest
        </ul>

      </div>
    </div>
  </nav>
