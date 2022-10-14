<?php
namespace App\Http\Requests\Setting;

use Illuminate\Http\Exceptions\HttpResponseException;
use Spatie\DataTransferObject\DataTransferObject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UpdateRequest extends DataTransferObject
{
    /** @var string */
    public string $value;

    /** @var bool */
    public bool $active;

    public function __construct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'value'     => 'required|max:512',
            'active'    => 'required|boolean'
        ]);

        if ($validator->fails()) {
            throw new HttpResponseException(response()->json($validator->errors(),422));
        }

        $this->value    = $request->input('value');
        $this->active   = $request->input('active');
    }
}
