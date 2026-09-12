<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Support\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $category = $request->query('category');

        $posts = Post::published()
            ->latestFirst()
            ->when($category, fn ($query, $slug) => $query->where('category', $slug))
            ->paginate(9)
            ->withQueryString();

        return view('pages.posts.index', [
            'posts' => $posts,
            'category' => $category,
            // Only categories that have something published behind them.
            'categories' => Post::published()
                ->whereNotNull('category')
                ->distinct()
                ->pluck('category')
                ->all(),
        ]);
    }

    public function show(Post $post): View
    {
        abort_unless($post->isPublished(), 404);

        $article = Article::fromHtml((string) $post->body);

        return view('pages.posts.show', [
            'post' => $post,
            'article' => $article,
            'related' => $this->related($post),
        ]);
    }

    /**
     * Posts from the same category, topped up with the latest when that
     * category is thin, so the section is never half empty.
     *
     * @return Collection<int, Post>
     */
    protected function related(Post $post): Collection
    {
        $sameCategory = Post::published()
            ->latestFirst()
            ->whereKeyNot($post->getKey())
            ->when($post->category, fn ($query) => $query->where('category', $post->category))
            ->take(3)
            ->get();

        if ($sameCategory->count() >= 3) {
            return $sameCategory;
        }

        return $sameCategory->merge(
            Post::published()
                ->latestFirst()
                ->whereKeyNot($post->getKey())
                ->whereNotIn('id', $sameCategory->modelKeys())
                ->take(3 - $sameCategory->count())
                ->get(),
        );
    }
}
