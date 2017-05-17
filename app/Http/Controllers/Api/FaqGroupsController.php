<?php

namespace App\Http\Controllers\Api;

use App\Models\Faq;
use App\Models\FaqGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;

class FaqGroupsController extends Controller
{
    public function store(Request $request)
    {
        $faq_group = FaqGroup::create();
        $faq_group->load('faq');
        Faq::whereId($request->faq_id)->update([
            'group_id' => $faq_group->id,
        ]);
        return $faq_group;
    }

    public function update(Request $request, $id)
    {
        FaqGroup::find($id)->update($request->input());
    }

    public function destroy($id)
    {
        FaqGroup::destroy($id);
    }
}
