<?php
namespace App\Http\Requests\Setting;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Exceptions\HttpResponseException;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\DataTransferObject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Setting\DTO\SettingDataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CreateRequest extends DataTransferObject
{
    /** @var SettingDataTransferObject[] $collection */
    #[CastWith(ArrayCaster::class, itemType: SettingDataTransferObject::class)]
    public array $collection;

    public function __construct(Request $request)
    {
        $validator = Validator::make($request->input('setting'), [
            '*.name'    => 'required|max:256',
            '*.value'   => 'required|max:512',
            '*.active'  => 'required|boolean'
        ]);

        if ($validator->fails()) {
            throw new HttpResponseException(response()->json($validator->errors(),422));
        }

        try {
            $this->collection = array_map(
                fn(array $data) => new SettingDataTransferObject(...$data),
                $request->input('setting')
            );
        } catch (UnknownProperties $e) {
            throw new HttpResponseException(response()->json(BaseController::formatErrorAPI($e->getMessage()),422));
        }
    }
}
