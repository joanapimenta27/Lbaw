@extends('layouts.app')

@push('scripts')
    <script src="{{ asset('js/pages/home.js') }}"></script>
@endpush

@section('content')
    <div class="view-modal hidden">
        <div class="view-content"></div>
    </div>
    <aside class="sidebar">
        <div class="sidebar-content">
            @if (Auth::check())   
            <a href="{{ url('/search') }}" class="search">
                <i class="fas fa-search padding-right-10px"></i> Search
            </a>
            <div class="button-container">
                <a href="{{ route('add-post') }}" class="button primary-btn button-width-100p">+ Add Post</a>
            </div>
            <hr>
            @else  
                <a href="{{ url('/search') }}" class="search">
                    <i class="fas fa-search"></i> Search
                </a>
                <hr>
                <div class="button-container">
                    <div class="loginregister">
                        <a href="{{ url('/login') }}"><i class="fa-solid fa-circle-user"></i>Login in</a>
                        <a href="{{ url('/register') }}"><i class="fa-solid fa-pencil"></i>Register</a>
                    </div>
                </div>
            @endif
            @include('layouts.footer')
        </div>
        <button class="sidebar-toggle pulse-left">
            <i class="fas fa-chevron-left"></i>
        </button>
    </aside>

    <div class="container">
        <div class="content">
            <div class="posts">
                @foreach($posts as $post)
                    @include('partials.post', ['post' => $post])
                @endforeach
            </div>
        </div>
    </div>
@endsection
    