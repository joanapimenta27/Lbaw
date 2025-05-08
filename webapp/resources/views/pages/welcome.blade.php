@extends('layouts.minimal')

@section('title', 'Welcome to Flick')

@push('scripts')
    <script src="{{ asset('js/effects/zapEffect.js') }}" defer></script>
@endpush

@section('content')
    @auth
        <script>window.location.href = "{!! route('home', ['type' => 'public']) !!}";</script>
    @endauth
    <div class="all-height-container">
        <div class="left-section" style="flex:13">
            <div class="text-overlay">
                <div class="top-right flick-title padding-right-10p padding-top-5p">FLICK</div>
                <div class="bottom-left title-description">It has never been easier to make connections</div>
            </div>
            <svg id="zap-effect" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80">
                <polyline points="32,0 40,24 24,24 32,48 16,48 20,80" fill="none" stroke="#00ffff" stroke-width="4" stroke-linejoin="round" />
            </svg>
        </div>
        <div class="right-section" style="flex:7">
            <div class="logo-container">
                <img src="{{ asset('images/Flick.png') }}" alt="Flick Logo" class="logo padding-top-18p">
            </div>
            <div class="vertical-container gap-20p">
                @guest
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="button button-XL button-width-400 primary-btn">Start Connecting</a>
                    @endif
                    @if (Route::has('home'))
                        <a href="{{ route('home', ['type' => 'public']) }}" class="button button-M button-width-250 secondary-btn">Explore Anonymously</a>
                    @endif
                @endguest
            </div>
        </div>
    </div>
</html>