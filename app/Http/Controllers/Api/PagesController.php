<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Page;

class PagesController extends Controller
{
    // разрешенные теги в поле html
    const ALLOWED_TAGS = ['a', 'p', 'ol', 'ul', 'li', 'h2', 'h3'];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // откуда обращаемся – с поиска или с общей страницы?
        $controller_with_params = collect(explode('/', $request->header('referer')))->last();
        $controller = collect(explode('?', $controller_with_params))->first();
        $search = isset($_COOKIE[$controller]) ? json_decode($_COOKIE[$controller]) : (object)[];
        $query = Page::search($search);
        if ($request->sort) {
            $query->orderBy('updated_at', 'desc');
        } else {
            $query->orderBy('keyphrase');
        }
        return $query->paginate(30);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return Page::create($request->input())->fresh();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Page::with(['useful'])->find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id§
     * @return \Illumi§nate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // validate tags
        if ($request->html) {
            preg_match_all("/<[^>]*>/", $request->html, $tags);
            $counts = [];
            foreach($tags[0] as $tag) {
                // only <a></a> is allowed with attributes
                if (strpos($tag, '<a') === 0) {
                    @$counts['a']++;
                    continue;
                }
                $allowed = false;
                foreach(self::ALLOWED_TAGS as $allowed_tag) {
                    if (in_array($tag, ["<{$allowed_tag}>", "</{$allowed_tag}>"])) {
                        if (! isset($counts[$allowed_tag])) {
                            $counts[$allowed_tag] = 0;
                        }

                        if ($tag[1] == '/') {
                            $counts[$allowed_tag]--;
                        } else {
                            $counts[$allowed_tag]++;
                        }
                        $allowed = true;
                        break;
                    }
                }
                if (! $allowed) {
                    return response()->json("Тег " . htmlspecialchars($tag) . " запрещен", 403);
                }
            }
            // validate opening tags count = enclosing tags count
            foreach($counts as $tag => $tag_count_difference) {
                if ($tag_count_difference != 0) {
                    return response()->json("Тег {$tag} не сбалансирован", 403);
                }
            }
        }

        Page::find($id)->update($request->input());
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Page::destroy($id);
    }

    /**
     * Check page existance
     */
     public function checkExistance(Request $request, $id = null)
     {
         $query = Page::query();

         if ($id !== null) {
             $query->where('id', '!=', $id);
         }

         return ['exists' => $query->where($request->field, $request->value)->exists()];
     }

     /**
      * Search (used in Link Manager)
      */
    public function search(Request $request)
    {
        return Page::where('keyphrase', 'like', '%' . $request->q . '%')->orWhere('id', $request->q)
            ->select('id', 'keyphrase', \DB::raw("CONCAT(id, ' – ', keyphrase) as title"))->get()->all();
    }
}
