<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ImageGenController extends Controller
{
    public function showForm()
    {
        return view('image-form');
    }

    public function generate(Request $request)
    {
        $prompt = $request->input('prompt');

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('HUGGINGFACE_API_KEY'),
            ])->timeout(60)->post('https://api-inference.huggingface.co/models/black-forest-labs/FLUX.1-dev', [
                'inputs' => $prompt,
            ]);

            // Check if response is an image
            if (str_starts_with($response->header('content-type'), 'image')) {
                $imageData = $response->body();
                $fileName = uniqid('img_') . '.png';
                $filePath = public_path('images/' . $fileName);

                file_put_contents($filePath, $imageData);

                return view('image-form', [
                    'image' => asset('images/' . $fileName),
                    'prompt' => $prompt,
                ]);
            }

            // If not image, return response json for debugging
            dd($response->json()); // <-- only if not image

            return back()->withErrors(['error' => 'Unexpected response']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

}
