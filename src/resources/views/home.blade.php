@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}

                    <div class="mt-4">
                        <!-- FTS Tile -->
                        <button id="fts-tile" class="btn btn-primary btn-lg">
                            FTS
                        </button>
                        <!-- In the future, add more tiles like ERP, HRMS, etc. -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        const token = @json(session('jwt'));

        if (token) {
            console.log("JWT Token:", token);
            localStorage.setItem('jwt', token);
        } else {
            console.warn("No JWT found in session.");
        }

        // Redirect to FTS with token on click
        const ftsTile = document.getElementById('fts-tile');
        ftsTile.addEventListener('click', function () {
            const jwt = localStorage.getItem('jwt');
            if (!jwt) {
                alert('No JWT token found. Please log in again.');
                return;
            }
            window.location.href = 'http://localhost:8001/token-login?token=' + encodeURIComponent(jwt);
        });
    })();
</script>
@endsection
