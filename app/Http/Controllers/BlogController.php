<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class BlogController extends Controller
{
    public function index()
    {
        $response = Http::timeout(10)->get(
            'https://gamesnag.com/wp-json/wp/v2/posts',
            [
                'per_page' => 9,
                '_embed' => true, // featured image + author
            ]
        );

        if (!$response->successful()) {
            $posts = [];
        } else {
            $posts = $response->json();
        }

        return view('blog.index', compact('posts'));
    }
}
