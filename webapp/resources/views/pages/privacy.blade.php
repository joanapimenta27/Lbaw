@extends('layouts.app')

@push('scripts')
    <script src="{{ asset('js/pages/home.js') }}"></script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/staticpages.css') }}">
@endpush

@section('content')
<div class="privacy">
    <div class="privacy-container">
        <h1>Privacy Policy</h1>
        <p>We value your privacy and are committed to protecting your personal information. This Privacy Policy outlines how we collect, use, and safeguard your data when you visit or interact with our website.</p>
        
        <h2>1. Information We Collect</h2>
        <p>We may collect the following types of information:</p>
        <ul>
            <li>Personal information (e.g., name, email address, phone number).</li>
            <li>Usage data (e.g., pages visited, time spent on our site, interactions in app).</li>
            <li>Cookies and tracking information.</li>
        </ul>
        
        <h2>2. How We Use Your Information</h2>
        <p>We use your information to:</p>
        <ul>
            <li>Provide, operate, and maintain our website.</li>
            <li>Improve user experience.</li>
            <li>Personalise our site to your taste.</li>
        </ul>
        
        <h2>3. Data Sharing</h2>
        <p>We do not sell, trade, or rent your personal information to others. We may share information with third-party service providers for specific purposes, such as analytics or customer support.</p>
        
        <h2>4. Cookies</h2>
        <p>We use cookies to enhance your browsing experience. You can manage your cookie preferences through your browser settings.</p>
        
        <h2>5. Your Rights</h2>
        <p>You have the right to access, correct, or delete your personal data. Please contact us at <strong>privacy@example.com</strong> for assistance.</p>
        
        <h2>6. Changes to this Policy</h2>
        <p>We may update this Privacy Policy from time to time. Any changes will be posted on this page with an updated effective date.</p>
    </div>
    <footer class="footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} Flick. All rights reserved. | <a href="/about"> About Us </a> | <a href="/contact">Contacts</a> | <a href="/terms">Terms and Service</a> </p>
        </div>
    </footer>
</div>

@endsection