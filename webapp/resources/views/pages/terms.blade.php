@extends('layouts.app')

@push('scripts')
    <script src="{{ asset('js/pages/home.js') }}"></script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/staticpages.css') }}">
@endpush

@section('content')
<div class="terms">
    <div class="privacy-container">
        <h1>Terms and Services</h1>
        <p>Welcome to Flick Terms and Services! These Terms and Services govern your use of our site. By accessing or using our website, you agree to comply with these terms. Please read them carefully.</p>
        
        <h2>1. Use of the Website</h2>
        <p>You agree to use our website for lawful purposes only. You must not:</p>
        <ul>
            <li>Use this website if you don't meet the minimum age of 13 years old.</li>
            <li>Engage in any activity that may damage or interfere with the website.</li>
            <li>Attempt to gain unauthorized access to our systems or data.</li>
            <li>Use the website for fraudulent or malicious purposes.</li>
        </ul>
        
        <h2>2. Intellectual Property</h2>
        <p>All content on this website, including text, images, and logos, is owned by us or our licensors. You may not use, reproduce, or distribute any content without prior written permission.</p>
        
        <h2>3. Limitation of Liability</h2>
        <p>We are not liable for any damages arising from your use of this website. This includes direct, indirect, or consequential damages.</p>
        
        <h2>4. Third-Party Links</h2>
        <p>Our website may contain links to third-party websites. We are not responsible for the content or practices of these external sites.</p>
        
        <h2>5. Changes to the Terms</h2>
        <p>We may update these Terms and Services from time to time. Please review this page periodically for any changes.</p>

    </div>
    <footer class="footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} Flick. All rights reserved. | <a href="/privacy">Privacy Policy</a> | <a href="/contact"> Contact </a>| <a href="/about">About Us</a> </p>
        </div>
    </footer>
</div>

@endsection