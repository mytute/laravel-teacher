
@extends('layout')

@section('content')
    <a href="{{ route('posts.create') }}">Create New Post</a>

    @foreach($posts as $post)
        <div>
            <h2>{{ $post->title }}</h2>
            <p>{{ $post->body }}</p>
            <a href="{{ route('posts.edit', $post) }}">Edit</a>
            <form action="{{ route('posts.destroy', $post) }}" method="POST">
                @csrf @method('DELETE')
                <button type="submit">Delete</button>
            </form>
        </div>
    @endforeach

    {{ $posts->links() }}
@endsection
