<?php

declare(strict_types=1);

namespace Astacode\Kernel\Http\Requests;

use Astacode\Kernel\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Class BaseFormRequest
 * Base FormRequest for all ASTACODE microservices ensuring uniform RFC 7807 validation error payloads.
 */
abstract class BaseFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator): void
    {
        $invalidParams = [];
        foreach ($validator->errors()->toArray() as $field => $messages) {
            $invalidParams[] = [
                'name' => $field,
                'reason' => $messages[0] ?? 'Invalid value',
                'all_reasons' => $messages,
            ];
        }

        throw new HttpResponseException(
            ApiResponse::rfcProblem(
                title: 'Unprocessable Entity (Validation Failed)',
                status: 422,
                detail: 'The given request data failed business schema validation checks.',
                type: 'https://astacode.com/errors/validation-error',
                invalidParams: $invalidParams
            )
        );
    }
}

