<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        Gate::authorize('organization', 'view');
        return response()->json([
            'items' => Organization::select('*')->paginate($this->perPage)->onEachSide(2),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param ImageController $imageController
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, ImageController $imageController)
    {
        Gate::authorize('organization', 'create');
        $messages = [];
        $request->validate([
            'name' => 'required|unique:organizations'
        ]);

        $organization = Organization::create(
            $request->all()
        );
        $messages[] = $organization ? __('Organization created') : __('Organization not created');
        if (is_array($request->keywords) && count($request->keywords) > 0) {
            $keywordArray = [];
            foreach ($request->keywords as $keyword) {
                $keywordArray[] = ['name' => $keyword];
            }
            $result = $organization->keywords()->createMany(
                $keywordArray
            );
            $messages[] = $result ? __('Keywords assigned') : __('Keywords not assigned');
        }

        $imageUploadResult = $imageController->uploadLogo($request, $organization);
        if ($imageUploadResult !== false ) {
            $messages[] = $imageUploadResult;
        }

        return $organization
            ? response()->json([
                'result' => $result,
                'success_message' => $messages
            ])
            : response()->json([
                'result' => $result,
                'error_message' => [
                    __('Something went wrong')
                ]
            ]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Organization $organization
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Organization $organization)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Organization $organization
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Organization $organization)
    {
        Gate::authorize('organization', 'edit');
        $organization->load(['keywords', 'logo']);
        return response()->json([
            'organization' => $organization,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Organization $organization
     * @param ImageController $imageController
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Organization $organization, ImageController $imageController)
    {
        Gate::authorize('organization', 'edit');
        $messages = [];
        $request->validate([
            'name' => ['required', Rule::unique('organizations')->ignore($organization)],
            //'image' => 'nullable|image'
        ]);

        $result = $organization->update(
            $request->all()
        );

        $messages[] = $result ? __('Organization updated') : __('Organization not updated');
        if (is_array($request->keywords) && count($request->keywords) > 0) {
            $organization->keywords()->delete();
            $keywordArray = [];
            foreach ($request->keywords as $keyword) {
                $keywordArray[] = ['name' => $keyword];
            }
            $result = $organization->keywords()->createMany(
                $keywordArray
            );
            $messages[] = $result ? __('Keywords assigned') : __('Keywords not assigned');
        }

        $imageUploadResult = $imageController->uploadLogo($request, $organization);
        if ($imageUploadResult !== false ) {
            $messages[] = $imageUploadResult;
        }

        $organization->load('keywords:name,organization_id');
        $organization->load('logo:attachable_id,name,url,thumbnail_url');

        return $result
            ? response()->json([
                'organization' => $organization,
                'success_message' => $messages
            ])
            : response()->json([
                'error_message' => [
                    __('Something went wrong')
                ]
            ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Organization $organization
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Organization $organization)
    {
        Gate::authorize('organization', 'delete');
        $result = $organization->delete();
        return $result
            ? response()->json([
                'result' => $result,
                'success_message' => [
                    __('Organization deleted'),
                ]
            ])
            : response()->json([
                'result' => $result,
                'error_message' => [
                    __('Something went wrong')
                ]
            ]);
    }
}
