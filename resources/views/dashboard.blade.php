@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Dashboard</h1>
    <p>Welcome, <strong>{{ $user->name }}</strong>!</p>

    <div class="card mt-4" style="max-width: 400px;">
        <div class="card-body">
            <h5 class="card-title">Your Projects</h5>
            <p class="card-text">You have <strong>{{ $projectsCount }}</strong> projects.</p>
            <a href="{{ route('projects.index') }}" class="btn btn-primary">View Projects</a>
        </div>
    </div>
</div>
@endsection
