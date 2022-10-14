<?php

namespace App\Services\API;

use App\Http\Requests\Setting\CreateRequest;
use App\Http\Requests\Setting\UpdateRequest;
use App\Http\Resources\API\SettingResource;
use App\Repositories\SettingRepository;
use App\Services\BaseServices;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingServices extends BaseServices
{
    /**
     * @var SettingRepository Repository parâmetros.
     */
    private SettingRepository $settingRepository;

    /**
     * Instantiate a new SettingServices instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->settingRepository = new SettingRepository();
    }

    /**
     * Listar todos os parâmetros.
     *
     * @param   string  $param  Nome do parâmetro.
     * @return  SettingResource
     * @throws  Exception
     */
    public function getSetting(string $param): SettingResource
    {
        $setting = $this->settingRepository->getByName($param);

        if (!$setting) {
            throw new Exception("Parâmetro não localizado.");
        }

        return new SettingResource($setting);
    }

    /**
     * Listar todos os parâmetros.
     *
     * @return array
     */
    public function listAllSetting(): array
    {
        $response = array();

        foreach ($this->settingRepository->getAll() as $setting) {
            $response[] = new SettingResource($setting);
        }

        return $response;
    }

    /**
     * Cadastrar parâmetros.
     *
     * @throws Exception
     */
    public function createSettings(CreateRequest $request)
    {
        DB::beginTransaction();

        $message = "Atualização de parâmetros.\nold=" . json_encode($this->settingRepository->getAll());

        try {
            $this->settingRepository->removeAll();

            foreach ($request->collection as $settings) {
                $setting = new SettingRepository();
                $setting->setAttribute('name', $settings->name);
                $setting->setAttribute('value', $settings->value);
                $setting->setAttribute('active', $settings->active);
                $setting->save();
            }
        } catch (Exception $exception) {
            Log::error("Ocorreu um erro para salvar os parâmetros.\n
            body=" . json_encode($request->collection) . "\n
            error={$exception->getMessage()}");
            DB::rollBack();
            throw new Exception("Ocorreu um erro para salvar os parâmetros.");
        }

        DB::commit();

        $message .= "\nnew=" . json_encode($this->settingRepository->getAll());
        Log::info($message);
    }

    /**
     * Atualziar parâmetro.
     *
     * @throws Exception
     */
    public function updateSettings(UpdateRequest $request, string $param)
    {
        if (!$this->settingRepository->getByName($param)) {
            throw new Exception('Parâmetro não localizado.');
        }

        DB::beginTransaction();

        $message = "Atualização do parâmetro ({$this->settingRepository->getByName($param)}).\nold=" . json_encode($this->settingRepository->getByName($param));

        try {
            $setting = $this->settingRepository->getByName($param);
            $setting->setAttribute('value', $request->value);
            $setting->setAttribute('active', $request->active);
            $setting->save();
        } catch (Exception $exception) {
            Log::error("Ocorreu um erro para salvar os parâmetros.\n
            body=" . json_encode($request->all()) . "\n
            error={$exception->getMessage()}");
            DB::rollBack();
            throw new Exception("Ocorreu um erro para salvar os parâmetros.");
        }

        DB::commit();

        $message .= "\nnew=" . json_encode($this->settingRepository->getByName($param));
        Log::info($message);
    }
}
