@extends('layouts.app')

@push('scripts')
    <script src="{{ asset('js/pages/home.js') }}"></script>
@endpush
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/staticpages.css') }}">
@endpush

@section('content')
    <body>
        <header class="welcome">
            <div class="container">
                <h1>Welcome to Flick</h1>
                <p>The perfect place for sharing, connecting, and exploring moments that matter.</p>
            </div>
        </header>
        
        <main>
            <section class="section">
                <div class="container">
                    <h2>Our Mission</h2>
                    <p>To empower users to express themselves freely and build genuine connections through series of different interactions.</p>
                </div>
            </section>

            <section class="section bg-light">
                <div class="container ">
                    <h2>Key Features</h2>
                    <ul>
                        <li><strong>Posts:</strong> Share your favorite moments with photos, videos and text.</li>
                        <li><strong>Engagement Tools:</strong> Like, comment, share, flick and interact in meaningful ways.</li>
                        <li><strong>Community Focus:</strong> Join a positive and inclusive platform.</li>
                        <li><strong>Privacy First:</strong> Your data and privacy are always our priority, you can even choose which posts are private or public.</li>
                    </ul>
                </div>
            </section>

            <section class="section">
                <div class="container">
                    <h2>The Story Behind Flick</h2>
                    <p>Flick was born out of the need for a platform that allows people to connect freely and choose what they want to share with the public and what they want to keep in a smaller circle. We’re here to create a space where every moment matters and where your account doesn't need to be exclusively public or private, it can be a bit of both.</p>
                </div>
            </section>

            <section class="section bg-light">
                <div class="container">
                    <h2>Why Choose Flick?</h2>
                    <p>At Flick, we value authenticity and community. With us, you can share your life authentically and be part of a thriving community.</p>
                </div>
            </section>

            <section class="section">
                <div class="container">
                    <h2>Join the Movement</h2>
                    <p>Register today and be part of something extraordinary. <a href="{{ url('/register') }}" class="cta">Get Started</a></p>
                </div>
            </section>
        </main>

        <footer class="footer">
            <div class="container">
                <p>&copy; {{ date('Y') }} Flick. All rights reserved. | <a href="/privacy">Privacy Policy</a> | <a href="/contact"> Contact </a>| <a href="/terms">Terms and Service</a> </p>
            </div>
        </footer>
    </body>
@endsection