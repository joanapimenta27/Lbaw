
<header>
  <div class="flex">
    <div class="header-container">
      <div class="logo">
          <a href="{{ Request::is('login') || Request::is('register') ? '/home/public' : '/home/foryou' }}">
              <div class="logo-container-header">
                  <img src="{{ asset('images/Flick.png') }}" alt="Flick Logo" class="logo-img">
              </div>
          </a>
      </div>
      <nav class="nav-middle">
        @if (Request::is('search'))
        <div class="search-bar">
          <label for="searchD"></label>
          <input class="search-input" type="text" id="searchD" name="search" placeholder="Type to search..." autocomplete="off">

          <button type="submit"><i class="fa-solid fa-search"></i></button>
        </div>
        @elseif (Request::is('login') || Request::is('register') && !auth()->check())
          <ul>
            <li><a href="{{ url('/login') }}" class="{{ Request::is('login') ? 'active' : '' }}">Login</a></li>
            <li><a href="{{ url('/register') }}" class="{{ Request::is('register') ? 'active' : '' }}">Register</a></li>
          </ul>
        @else
          <ul>
            @auth
              <li><a href="{{ route('home', ['type' => 'foryou']) }}" class="{{ Request::route('type') === 'foryou' ? 'active' : 'feed-btn' }}">For you</a></li>
              <li><a href="{{ route('home', ['type' => 'public']) }}" class="{{ Request::route('type') === 'public' ? 'active' : 'feed-btn' }}">Public feed</a></li>
            @else
                <li class="feed-not-logged">Public feed</li>
            @endauth
          </ul>
        @endif
      </nav>
      <div class="icons-right">
        @auth
          <div class="multiple-icons-right-container">
            @if(Auth::user()->isAdmin())
              <a href="{{ url('/register') }}"><i class="fa-solid fa-user-plus"></i></a>
            @endif
            <div class=dropdown-notifications>
              <a href="{{ url('/notifications') }}">
                <i class="fa-solid fa-bell"></i>
              </a>
            </div>
            @if (!Request::routeIs('chatMenu.index'))
              <a href="{{ route('chatMenu.index') }}"><i class="fa-regular fa-message"></i></a>
            @endif
            @if (!(Request::routeIs('profile') && Request::route('userId') == Auth::id()))
              <a href="{{ route('profile', ['userId' => Auth::user()->id]) }}">
                <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('default-profile.png') }}" alt="Profile pic" class="mini-profile-pic">
              </a>
              
            @endif
          </div>
          <div class="condensed-icons-right-container">
            <button class="blank-button toggle-menu-button">
              <img src="{{ asset('images/icon/menu2.png') }}" alt="Menu" class="menu-icon" id="post-menu-icon" width="40" height="25">
            </button>
            <div class="dropdown-menu hidden">
                @if(Auth::user()->isAdmin())
                  <a href="{{ url('/register') }}"><i class="fa-solid fa-user-plus"></i></a>
                @endif
                <a href="{{ url('/notifications') }}"><i class="fa-solid fa-bell"></i></a>
                <a href="{{ url('/messages') }}"><i class="fa-regular fa-message"></i></a>
                @if (!Request::routeIs('profile'))
                  <a href="{{ route('profile', ['userId' => Auth::user()->id]) }}">
                    <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('default-profile.png') }}" alt="Profile pic" class="mini-profile-pic">
                  </a>
                @endif
            </div>
          </div>
        @else
          <a href="{{ route('profile', 1) }}"><i class="fa-solid fa-user"></i></a>
        @endif
      </div>
    </div>
  </div>
</header>