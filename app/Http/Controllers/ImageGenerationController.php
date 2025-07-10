<?php

namespace App\Http\Controllers;

use App\Services\HuggingFaceFlux;
use Illuminate\Http\Request;

class ImageGenerationController extends Controller
{
    protected $imageService;

    public function __construct(HuggingFaceFlux $imageService)
    {
        $this->imageService = $imageService;
    }

    public function showForm()
    {
        return view('image-generation.form');
    }

    public function generateImage(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:500'
        ]);

        $result = $this->imageService->generateImage($request->input('prompt'));

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return view('image-generation.result', [
            'success' => $result['status'],
            'imageUrl' => $result['image_url'] ?? null,
            'message' => $result['message']
        ]);
    }
}
