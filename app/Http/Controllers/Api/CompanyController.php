<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateCompany;
use App\Http\Resources\CompanyResource;
use App\Jobs\CompanyCreated;
use App\Models\Company;
use App\Services\CompanyService;
use App\Services\EvaluationService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    protected $evaluationService;
    protected $companyService;

    public function __construct(EvaluationService $evaluationService, CompanyService $companyService)
    {
        $this->evaluationService = $evaluationService;
        $this->companyService = $companyService;
    }

    public function index(Request $request)
    {
        $companies = $this->companyService->getCompanies($request->get('filter', ''));

        return CompanyResource::collection($companies);
    }

    public function store(StoreUpdateCompany $request)
    {
        $company = $this->companyService->createNewCompany($request->validated(), $request->image);

        CompanyCreated::dispatch($company->email)
            ->onQueue('queue_email');

        return new CompanyResource($company);
    }

    public function show($uuid)
    {
        $company = $this->companyService->getCompanyByUUID($uuid);

        $evaluations = $this->evaluationService->getEvaluationsCompany($uuid);

        return (new CompanyResource($company))
            ->additional([
                'evaluations' => json_decode($evaluations)
            ]);
    }

    public function update(StoreUpdateCompany $request, $uuid)
    {
        $this->companyService->updateCompany($uuid, $request->validated(), $request->image);

        return response()->json([
            'message' => 'Updated'
        ]);
    }

    public function destroy($uuid)
    {
        $this->companyService->deleteCompany($uuid);

        return response()->json([], 204);
    }
}
