<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\ErrorLog;
use Illuminate\Support\Facades\Log;

class SubmitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150|unique:users,email',
            'message' => 'nullable|string|max:1000',
        ];
    }

     protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();

        // سجّل الخطأ في جدول error_logs
        ErrorLog::create([
            'endpoint' => '/api/submit',
            'request_data' => json_encode($this->all()),
            'error_message' => implode("; ", $errors)
        ]);

        // سجّل في Laravel log أيضاً
        Log::error('Validation Error: '.implode("; ", $errors));

        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => 'خطأ في التحقق من البيانات',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
