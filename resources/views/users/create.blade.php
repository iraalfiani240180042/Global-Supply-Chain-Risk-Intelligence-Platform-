@extends('layouts.app')

@section('title','Add User')

@section('content')

<div class="container">

<div class="card shadow-sm border-0">

<div class="card-body">

<h3 class="fw-bold mb-4">
Add User
</h3>

<form action="{{ route('users.store') }}" method="POST">

@csrf

<div class="mb-3">

<label>Name</label>

<input
type="text"
name="name"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Email</label>

<input
type="email"
name="email"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Password</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>

<button class="btn btn-primary">

Save User

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