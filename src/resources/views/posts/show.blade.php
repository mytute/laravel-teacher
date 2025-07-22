@extends('layout')

@section('content')
    <h1>{{ $post->title }}</h1>
    <p>{{ $post->body }}</p>

    <a href="{{ route('posts.index') }}">← Back to Posts</a>
@endsection
