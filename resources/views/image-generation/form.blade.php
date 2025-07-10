<!-- resources/views/image-generation/form.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Image Generator</h1>

    <form id="imageForm" action="{{ route('image.generate') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="prompt" class="block text-gray-700 mb-2">Enter your prompt:</label>
            <textarea name="prompt" id="prompt" rows="3"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required></textarea>
        </div>

        <button type="submit" id="generateBtn"
            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg transition">
            Generate Image
        </button>

        <div id="loading" class="mt-4 hidden">
            <div class="flex items-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mr-3"></div>
                <span>Generating your image...</span>
            </div>
        </div>
    </form>

    <div id="resultContainer" class="mt-8 hidden">
        <h2 class="text-xl font-semibold mb-4">Result</h2>
        <div id="resultContent"></div>
    </div>
</div>

<script>
document.getElementById('imageForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;
    const generateBtn = document.getElementById('generateBtn');
    const loading = document.getElementById('loading');
    const resultContainer = document.getElementById('resultContainer');
    const resultContent = document.getElementById('resultContent');

    generateBtn.disabled = true;
    loading.classList.remove('hidden');
    resultContainer.classList.add('hidden');

    fetch(form.action, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(new FormData(form))
    })
    .then(response => response.json())
    .then(data => {
        loading.classList.add('hidden');
        generateBtn.disabled = false;

        if (data.status) {
            resultContent.innerHTML = `
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    ${data.message}
                </div>
                <div class="mt-4">
                    <img src="${data.image_url}" alt="Generated image" class="max-w-full h-auto rounded-lg shadow-lg">
                </div>
            `;
        } else {
            resultContent.innerHTML = `
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    ${data.message}
                </div>
            `;
        }

        resultContainer.classList.remove('hidden');
    })
    .catch(error => {
        loading.classList.add('hidden');
        generateBtn.disabled = false;

        resultContent.innerHTML = `
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                An error occurred. Please try again.
            </div>
        `;
        resultContainer.classList.remove('hidden');
    });
});
</script>
@endsection