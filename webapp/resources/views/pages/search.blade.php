@extends('layouts.app')


@push('scripts')
    <script src="{{ asset('js/search.js') }}" defer></script>
@endpush

@section('content')
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
        <button class="close-btn" onclick="this.parentElement.style.display='none';">&times;</button>
    </div>
@endif
<div class="view-modal hidden">
    <div class="view-content"></div>
</div>

<div class="search-filters">
    <a href="{{ url()->current() }}?tag=accounts">
        <button id="User" data-query="accounts" class="filter-button">Accounts</button>
    </a>
    <a href="{{ url()->current() }}?tag=tags">
        <button id="Post-tags" data-query="tags" class="filter-button">Posts by tags</button>
    </a>
    <a href="{{ url()->current() }}?tag=titles">
        <button id="Post-titles" data-query="titles" class="filter-button">Posts by content</button>
    </a>

    @include('partials.advanced-filters', ['attributes' => $attributes])
</div>


</div>

    <section class="search-page-containerS">

        <main class="search-contentS">
            <div class="search-resultsS">
                <ul id="user-listS"></ul>
                <button id="load-more-button" class="hidden">Load More</button>
            </div>
        </main>
    </section>
@endsection

@push('scripts')
<script src="{{ asset('js/search.js') }}" defer></script>
@endpush



