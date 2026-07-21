<?php

namespace Modules\Manufacturing\Http\Requests;

use App\Http\Requests\BaseRequest;

class InitiateMrpRunRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // No strict payload needed for a manual run trigger in MVP, but let's keep the structure open
        ];
    }
}
