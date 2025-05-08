@extends('layouts.app')

@push('scripts')
    <script src="{{ asset('js/pages/home.js') }}"></script>
@endpush
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/staticpages.css') }}">
@endpush

@section('content')
<div class="contact">
    <div class="contact-container">
        <h1>Contact Us</h1>
        <div class="contact-details">
            <h2>Contact Information</h2>
            <ul>
                <li><strong>Address:</strong> 123 Tech Street, Innovation City</li>
                <li><strong>Phone:</strong> +351 911 222 333</li>
                <li><strong>Email:</strong> contactflick@example.com</li>
                <li><strong>Working Hours:</strong> Mon - Fri, 9:00 AM - 5:00 PM</li>
            </ul>
        </div>
    </div>
    <footer class="footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} Flick. All rights reserved. | <a href="/privacy">Privacy Policy</a> | <a href="/about"> About Us </a>| <a href="/terms">Terms and Service</a> </p>
        </div>
    </footer>
</div>
@endsection