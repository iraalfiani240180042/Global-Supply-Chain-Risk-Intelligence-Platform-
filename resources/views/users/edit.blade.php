@extends('layouts.app')

@section('title','Edit User')

@section('content')

<div class="container">

<div class="card shadow-sm border-0">

<div class="card-body">

<h3 class="fw-bold mb-4">

Edit User

</h3>

<form action="{{ route('users.update',$user->id) }}" method="POST">

@csrf
@method('PUT')

<div class="mb-3">

<label>Name</label>

<input
type="text"
name="name"
class="form-control"
value="{{ $user->name }}"
required>

</div>

<div class="mb-3">

<label>Email</label>

<input
type="email"
name="email"
class="form-control"
value="{{ $user->email }}"
required>

</div>

<div class="mb-3">

<label>Password</label>

<input
type="password"
name="password"
class="form-control">

<small class="text-muted">
Leave blank if you don't want to change the password.
</small>

</div>

<button class="btn btn-primary">

Update

</button>

<a href="{{ route('users.index') }}"
class="btn btn-secondary">

Back

</a>

</form>

</div>

</div>

</div>

@endsection