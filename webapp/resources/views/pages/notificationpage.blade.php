@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Notifications</h1>
    @include('partials.notifications', ['notifications' => $notifications])
</div>
@endsection
