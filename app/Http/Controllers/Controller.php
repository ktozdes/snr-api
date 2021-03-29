<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $perPage;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->perPage = config('constants.per_page');
    }

    protected function sortBy(Request $request): array
    {
        $field = 'id';
        $sortOption = 'desc';
        if (isset($request->sort_by) && (str_contains($request->sort_by, '_desc') || str_contains($request->sort_by, '_asc'))) {
            $sortOption = strpos($request->sort_by, 'desc') !== false ? 'desc' : 'asc';
            $field = str_replace(['_desc', '_asc'], '', $request->sort_by);
        }
        return [
            'field' => $field,
            'value' => $sortOption,
        ];
    }
}
