<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Response;

class PostController extends Controller
{
    private string $title = 'Tymur Mardas\' Blog';

    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Return all resources.
     *
     * @return View|Response
     */
    public function index(): View|Response
    {
        $posts = Post::latest()->paginate(5);

        return renderViewDynamic('blog.index', [
            'posts' => $posts,
            'pagination' => $posts->links()->render(),
            'title' => $this->title,
            'success' => Session::get('success')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View|Response
     */
    public function create(): View|Response
    {
        return renderViewDynamic('blog.create', [
            'title' => $this->title,
            'action' => 'blog.store'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Redirector|Application|RedirectResponse
     */
    public function store(Request $request): Redirector|Application|RedirectResponse
    {
        $request->validate([
            'title' => 'required',
        ]);
        Post::create([...$request->post(), 'user_id' => Auth::id()]);

        return redirect('blog')->with('success', 'Post created');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return View
     */
    public function show(int $id): View|Response
    {
        $post = Post::find($id);
        return renderViewDynamic('blog.show', [
            'post' => $post,
            'title' => $post->title
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return View
     */
    public function edit(int $id): View|Response
    {
        $post = Post::findOrFail($id);

        return renderViewDynamic('blog.create', [
            'post' => $post,
            'title' => $this->title,
            'action' => 'blog.update'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return View|Response
     */
    public function update(Request $request, int $id): View|Response
    {
        $post = Post::findOrFail($id);

        $request->validate([
            'title' => 'required',
        ]);

        $post->fill($request->post())->save();

        return renderViewDynamic('blog.show', [
            'post' => $post,
            'title' => $this->title,
            'action' => 'blog.update'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect()->route('blog.index')
            ->with('success', 'Post deleted successfully');
    }
}
