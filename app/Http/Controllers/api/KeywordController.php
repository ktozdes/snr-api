<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Keyword;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class KeywordController extends Controller
{
    /**
     * Sync user keywords.
     *
     * @param User $user
     * @param Request $request
     * @return JsonResponse
     */
    public function userSync(User $user, Request $request)
    {
        Gate::authorize('keyword', 'edit');
        $messages = [];
        if (is_array($request->keywords) && count($request->keywords) > 0) {
            $allKeywords = Keyword::where('organization_id', $user->organization_id)->get()->pluck('name')->toArray();
            $newKeywords = [];
            $keywordFilter = [];
            foreach ($request->keywords as $keyword) {
                if (!in_array($keyword, $allKeywords)) {
                    $newKeywords[] = [
                        'name' => $keyword,
                        'organization_id' => $user->organization_id,
                    ];
                }
                $keywordFilter[] = $keyword;
            }
            if (count($newKeywords) > 0) {
                Keyword::insert($newKeywords);
                $messages[] = __('New keywords assigned to organization');
            }
            $syncingKeywords = Keyword
                ::where('organization_id', $user->organization_id)
                ->where(function($query) use ($keywordFilter){
                    foreach ($keywordFilter as $filter) {
                        $query->orWhere('name', $filter);
                    }
                })
                ->get();
            $result = $user->keywords()->sync($syncingKeywords->pluck('id')->toArray());
            if (count($result['attached']) > 0) {
                $messages[] = __('User keywords synced');
            }
        }
        $user->load('keywords:id,name');
        $organization = Organization::find($user->organization_id)->load('keywords:name,organization_id');
        return response()->json([
            'user_keywords' => $user->keywords,
            'organization_keywords' =>$organization->keywords,
            'success_message' => $messages
        ]);
    }

    /**
     * Sync organization keywords
     *
     * @param Organization $organization
     * @param Request $request
     * @return JsonResponse
     */
    public function organizationSync(Organization $organization, Request $request)
    {
        Gate::authorize('keyword', 'edit');
        $messages = [];

        if (is_array($request->keywords) && count($request->keywords) > 0) {
            $allKeywords = Keyword::where('organization_id', $organization->id)->get()->pluck('name')->toArray();
            $keywordFilter = [];
            $newKeywords = [];
            foreach ($request->keywords as $keyword) {
                if (!in_array($keyword, $allKeywords)) {
                    $newKeywords[] = [
                        'name' => $keyword,
                        'organization_id' => $organization->id,
                    ];
                }
                $keywordFilter[] = ['name', '<>', $keyword];
            }

            $removingKeywords = Keyword::
            where('organization_id', $organization->id)
                ->where($keywordFilter)
                ->get();

            if (count($removingKeywords->pluck('id')->toArray()) > 0) {
                if (Keyword::destroy($removingKeywords->pluck('id'))) {
                    $messages[] = __('Organization keywords removed');
                }
            }
            if (count($newKeywords) > 0) {
                Keyword::insert($newKeywords);
                $messages[] = __('New keywords assigned to organization');
            }
        }
        $organization->load('keywords:name,organization_id');
        auth()->user()->load('keywords:id,name');
        return response()->json([
            'user_keywords' => auth()->user()->keywords,
            'organization_keywords' =>$organization->keywords,
            'success_message' => $messages
        ]);
    }
}
