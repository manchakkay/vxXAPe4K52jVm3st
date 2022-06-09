<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\_Templates\Controller;
use App\Models\News;
use App\Models\NewsFavorite;
use App\Models\Page;

class Home_MainController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @param String $mode Mode of page load (preload or default)
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function get()
    {
        $pages = Page::where('is_structured', true)->where('parent_id', null)->with('children')->orderBy('sort', 'ASC')->get()->take(6);

        foreach ($pages as $index => $page) {
            $page_menu_type = "long";
            if (count($page->children) <= 14) {
                $page_menu_type = "short";

                if (count($page->children) > 4) {
                    foreach ($page->children as $child) {
                        if (count($child->children) > 0) {
                            $page_menu_type = "long";
                            break;
                        }
                    }
                }
            }

            $pages[$index]['menu_type'] = $page_menu_type;
        }

        return view(
            "home",
            [
                "news" => News::limit(32)->has('thumbnail')->orderBy('published_at', 'DESC')->orderBy('created_at', 'DESC')->get(),
                "pages" => $pages,
                "important_news" => NewsFavorite::limit(12)->get(),
            ]
        );
    }
}
