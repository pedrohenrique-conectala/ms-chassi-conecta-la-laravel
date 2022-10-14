<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Setting\CreateRequest;
use App\Http\Requests\Setting\UpdateRequest;
use App\Services\API\SettingServices;
use Illuminate\Http\JsonResponse;
use Exception;

class SettingController extends BaseController
{
    /**
     * Instantiate a new SettingController instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Consulta parâmetro.
     *
     * @param   string|null  $param Nome do parâmetro.
     * @return  JsonResponse
     */
    public function get(string $param = null): JsonResponse
    {
        if ($param !== null) {
            try {
                return response()->json((new SettingServices())->getSetting($param));
            } catch (Exception $exception) {
                return response()->json($this->formatErrorAPI($exception->getMessage()));
            }
        }

        return response()->json((new SettingServices())->listAllSetting());
    }

    /**
     * Atualizar parâmetros.
     *
     * @param   CreateRequest $request
     * @return  JsonResponse
     */
    public function create(CreateRequest $request): JsonResponse
    {
        try {
            (new SettingServices())->createSettings($request);
        } catch (Exception $exception) {
            return response()->json($this->formatErrorAPI($exception->getMessage()));
        }

        return response()->json(null, 201);
    }

    /**
     * Atualizar parâmetros.
     *
     * @param   UpdateRequest   $request
     * @param   string          $param  Nome do parâmetro.
     * @return  JsonResponse
     */
    public function update(UpdateRequest $request, string $param): JsonResponse
    {
        try {
            (new SettingServices())->updateSettings($request, $param);
        } catch (Exception $exception) {
            return response()->json($this->formatErrorAPI($exception->getMessage()));
        }

        return response()->json(null, 204);
    }
}
