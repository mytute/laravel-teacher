@extends('layout')

@section('content')
    <h1>Create New Post</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li style="color:red">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('posts.store') }}" method="POST">
        @csrf

        <div>
            <label>Title:</label><br>
            <input type="text" name="title" value="{{ old('title') }}" required>
        </div>

        <div>
            <label>Body:</label><br>
            <textarea name="body" rows="5" required>{{ old('body') }}</textarea>
        </div>

        <div>
            <button type="submit">Create Post</button>
        </div>
    </form>

    <a href="{{ route('posts.index') }}">Back to Posts</a>
@endsection
