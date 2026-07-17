<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\TitikWisataGeoJsonExporter;
use Illuminate\Http\JsonResponse;

class TitikWisataController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(TitikWisataGeoJsonExporter::toArray());
    }
}
