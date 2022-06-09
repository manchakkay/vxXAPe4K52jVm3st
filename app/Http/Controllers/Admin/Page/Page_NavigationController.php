<?php

namespace App\Http\Controllers\Admin\Page;

use App\Http\Controllers\_Templates\Controller;
use App\Models\Page;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Request;

class Page_NavigationController extends Controller
{
    public function get()
    {
        return view(
            'admin.pages.navigations',
            [
                "pages_structure" => Page::where('is_structured', true)
                    ->where('parent_id', null)
                    ->with('children')
                    ->orderBy('sort', 'ASC')
                    ->orderBy('id', 'DESC')
                    ->get(['id', 'sort', 'parent_id', 'content_title', 'slug']),

                "pages_cache" => Page::where('is_structured', false)
                    ->orderBy('id', 'DESC')
                    ->get(['id', 'sort', 'parent_id', 'content_title', 'slug']),
            ]
        );
    }

    public function post(Request $req)
    {
        Log::debug("post catched", [$req->all()]);

        $structure_array = json_decode($req->s);
        $cache_array = json_decode($req->c);

        foreach ($structure_array as $index => $page) {
            Page::where('id', $page->i)->update([
                'sort' => $page->s,
                'parent_id' => $page->p,
                'is_structured' => true,
            ]);
        }

        foreach ($cache_array as $index => $page) {
            Page::where('id', $page->i)->update([
                'sort' => null,
                'parent_id' => null,
                'is_structured' => false,
            ]);
        }
    }
}
