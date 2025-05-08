@foreach ($posts as $post)
    @include('partials.post', ['post' => $post])
@endforeach