<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;

class BaseController extends Controller
{
    public function __construct()
    {
    }

    #[ArrayShape(["error" => "string[]"])]
    public static function formatErrorAPI(string $message): array
    {
        return [
            "error" => [
                $message
            ]
        ];
    }
}
