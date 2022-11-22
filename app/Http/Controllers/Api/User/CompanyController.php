<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Services\Contracts\CompanyRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CompanyController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'title' => 'Companies',
            'data' => CompanyResource::collection(auth()->user()->companies)
        ]);
    }

    /**
     * @param Request $request
     * @param CompanyRepositoryInterface $companyRepository
     * @return JsonResponse
     * @throws ValidationException
     */
    public function create(Request $request, CompanyRepositoryInterface $companyRepository): JsonResponse
    {
        $this->validate($request, $this->rules());

        try {
            $company = $companyRepository->create([
                ... $request->only('name', 'phone', 'description'),
                ... ['user_id' => auth()->id()]
            ]);

            return response()->json(['company' => $company, 'message' => 'Company Creation Succeed.'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Company Creation Failed!'], 409);
        }
    }

    /**
     * @return string[]
     */
    protected function rules(): array
    {
        return  [
            'name' => 'required|string|unique:companies|max:75',
            'phone' => 'required|string|unique:companies|digits_between:6,25',
            'description' => 'required|string|min:10|max:255'
        ];
    }
}
