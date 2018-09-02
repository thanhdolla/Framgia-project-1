<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\AuthController;
use App\Models\News;
use App\Http\Requests\NewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ContentController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $news = News::getNews();

        return view('admin.content.news.index', compact('news'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.content.news.add');
    }

    /**
     * @param NewsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(NewsRequest $request)
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $name = $file->getClientOriginalName();

            $file->move(config('app.newsUrl'), $name);

            $request->merge([
                'avatar' => $name,
            ]);
        }

        News::create($request->all());

        return redirect()->route('news.index')->with('success', trans('common.with.add_success'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        try {
            $news = News::findOrFail($id);

            return view('admin/content/news/edit', compact('news'));
        } catch (ModelNotFoundException $e) {
            return view('admin.404');
        }
    }

    /**
     * @param $id
     * @param UpdateNewsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, UpdateNewsRequest $request)
    {
        $news = News::findOrFail($id);

        $imgOld = $news->avatar;

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $name = $file->getClientOriginalName();

            if (file_exists(config('app.newsUrl') . '/' . $imgOld)) {
                unlink(config('app.newsUrl') . '/' . $imgOld);
            }

            $file->move(config('app.newsUrl'), $name);

            $request->merge([
                'avatar' => $name,
            ]);

        } else {
            $request->merge([
                'avatar' => $imgOld,
            ]);
        }
        $news->update($request->all());

        return redirect()->route('news.index')->with('success', trans('common.with.edit_success'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function deleteNews($id)
    {
        $news = News::findOrFail($id);

        $news->delete();

        return redirect()->route('news.index')->with('success', trans('common.with.delete_success'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteManyNews(Request $request)
    {
        if ($request->check == null) {
            return redirect()->back()->with('success', trans('common.with.delete_success'));
        }
        for ($i = 0; $i < count($request->check); $i++) {
            $news = News::findOrFail($request->check[$i]);

            $news->delete();
        }

        return redirect()->route('news.index')->with('success', trans('common.with.delete_success'));
    }

    /**
     * @param Request $req
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function searchNews(Request $req)
    {
        $news = News::search($req->key)->get();

        return view('admin.content.news.index', compact('news'));
    }
}

