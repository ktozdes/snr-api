<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Interfaces\ParserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ParserInterface $parserInterface
     * @return \Illuminate\Http\JsonResponse
     */
    public function total(ParserInterface $parserInterface)
    {
        Gate::authorize('social network user', 'view');
        $result = $parserInterface->post('api/report.get_total');
        return response()->json([
            'report' => $result,
        ]);
    }
}
