@extends('layout')

@section('content')
    <h1>Edit Post</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li style="color:red">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('posts.update', $post) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label>Title:</label><br>
            <input type="text" name="title" value="{{ old('title', $post->title) }}" required>
        </div>

        <div>
            <label>Body:</label><br>
            <textarea name="body" rows="5" required>{{ old('body', $post->body) }}</textarea>
        </div>

        <div>
            <button type="submit">Update Post</button>
        </div>
    </form>

    <a href="{{ route('posts.index') }}">Back to Posts</a>
@endsection
