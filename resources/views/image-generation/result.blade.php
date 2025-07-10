<!-- resources/views/image-generation/result.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Image Generation Result</h1>

    @if ($success)
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ $message }}
        </div>

        <div class="mt-6">
            <img src="{{ $imageUrl }}" alt="Generated image" class="max-w-full h-auto rounded-lg shadow-lg">
        </div>

        <div class="mt-6">
            <a href="{{ route('image.form') }}"
               class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg transition">
               Generate Another Image
            </a>
        </div>
    @else
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ $message }}
        </div>

        <div class="mt-6">
            <a href="{{ route('image.form') }}"
               class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg transition">
               Try Again
            </a>
        </div>
    @endif
</div>
@endsection