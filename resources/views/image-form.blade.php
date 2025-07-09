<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Text to Image Generator</title>
</head>
<body>
    <h2>FLUX.1 Text to Image Generator</h2>

    @if ($errors->any())
        <p style="color:red">{{ $errors->first() }}</p>
    @endif

    <form method="POST" action="{{ route('generate.image') }}">
        @csrf
        <input type="text" name="prompt" placeholder="Enter your image description" value="{{ old('prompt', $prompt ?? '') }}" style="width: 400px;">
        <button type="submit">Generate Image</button>
    </form>

    @if (!empty($image))
        <div style="margin-top: 20px;">
            <h3>Result for: "{{ $prompt }}"</h3>
            <img src="{{ $image }}" alt="Generated Image" style="max-width: 500px;">
        </div>
    @endif
</body>
</html>
