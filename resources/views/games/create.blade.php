@extends('layouts.app')

@section('page-title', 'Add Ball Game')

@section('content')
<div class="mb-6">
    <p class="text-sm text-gray-600 mt-1">
        Add a new ball game to the system by filling out the form below.
    </p>
</div>

{{-- Single Game Form Only --}}
@include('games.partials.create_single')

@endsection
