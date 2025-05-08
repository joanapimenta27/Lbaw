@extends('layouts.minimal') 

@section('title', 'Flick - Edit Post')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/add-post.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/addpost.js') }}"></script>
@endpush

@section('content')
<div class="full-height-wrapper">
    <div class="side-additional-container">
        <div class="logo">
                <a href="{{ Request::is('login') || Request::is('register') ? '/' : '/home/foryou' }}">
                    <div class="logo-container-header">
                        <img src="{{ asset('images/Flick.png') }}" alt="Flick Logo" class="logo-img">
                    </div>
                </a>
            </div>
        </div>
    <div class="add-post-container">
        <div class="form-container" id="addpost-fc">
            <h2 class='general-title'>Edit your Post</h2>
            
            <form class="full-height-form" method="POST" action="{{ route('update-post', ['id' => $post->id]) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <input type="text" id="title" class="text_input" name="title" value="{{ old('title', $post->title) }}" placeholder="Title" maxlength="100" required>
                    @if ($errors->has('title'))
                        <span class="error">{{ $errors->first('title') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="description"> Description </label>
                    <textarea id="description" class="textarea_input" name="description" rows="4" maxlength="2000   ">{{ old('description', $post->description) }}</textarea>
                    @if ($errors->has('description'))
                        <span class="error">{{ $errors->first('description') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="content">Content</label>
                    <input type="file" id="content" name="content[]" accept="image/*,video/*" class="file-input" multiple hidden>
                    <button type="button" id="content-btn" class="button primary-btn">Choose File</button>
                    <div id="file-list" class="file-list-container">
                        @if ($post->media->count() > 0)
                            @foreach ($post->media->sortBy('order') as $media)
                                <div class="file-item existing-file" data-id="{{ $media->id }}" data-path="{{ asset('storage/' . $media->file_path) }}" data-name="{{ basename($media->file_path) }}" data-type="{{ $media->file_type }}">
                                    <label for="hidden"></label>
                                    <input type="hidden" name="existing_files[]" value="{{ $media->id }}">
                                </div>
                            @endforeach
                        @else
                            <div class="file-placeholder">No files selected</div>
                        @endif
                    </div>
                    <label for="hidden"></label>
                    <input type="hidden" name="file_order" id="file-order">
                    <div class="minimal-info">! Drag content to re-order it !</div>
                    @if ($errors->has('content'))
                        <span class="error">{{ $errors->first('content') }}</span>
                    @endif
                </div>

                <span class="fake-label">Visibility 
                    <span class="tooltip-container">
                        <img src="{{ asset('images/icon/helper.png') }}" width="15" height="15" alt="Help Icon">
                        <span class="tooltip-text">This controls whether the post is visible to everyone (Public) or only for your friends (Private).</span>
                    </span>
                </span>
                <div class="form-group checkbox-wrapper-10 padding-left-10px">
                    <label for="tgl tgl-flip"></label>

                    <input class="tgl tgl-flip" id="is_public" name="is_public" type="checkbox" 
                        {{ $post->is_public ? 'checked' : '' }}>
                    <label class="tgl-btn" data-tg-off="Private" data-tg-on="Public" for="is_public"></label>
                </div>

                <button type="submit" class="button primary-btn" id="add-post-btn">Save Changes</button>
            </form>
        </div>
        <div class="preview-section">
            <div class="container_flex_column">
                <h2 class='general-title'>Preview</h2>
                <div class="post">
                    <div class="post-header">
                        <div class="post-header-part">
                            <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('default-profile.png') }}" alt="Profile pic" class="mini-profile-pic">
                            <span class="post-name">{{ Auth::user()->name }}</span>
                        </div>
                        <div class="post-header-part">
                            <span class="post-date">{{ \Carbon\Carbon::parse($post->date)->format('d/m/y') }}</span>
                            <img src="{{ asset('images/icon/lock.png') }}" alt="Private" id="state-icon" width="40" height="40">
                        </div>
                    </div>
                    <div class="post-content" id="post-preview">
                        <h2 class="post-title">Preview Title</h2>    
                        <div class="post-description-container">
                            <p class="post-description">Preview description will appear here...</p>
                        </div>
                        <div class="post-slide-container">
                            <div class="post-media-container">
                                <!-- Media preview will be added here by JavaScript -->
                                <button id="left-arrow" class="nav-arrow" style="display:none;">&lt;</button>
                                <button id="right-arrow" class="nav-arrow" style="display:none;">&gt;</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection