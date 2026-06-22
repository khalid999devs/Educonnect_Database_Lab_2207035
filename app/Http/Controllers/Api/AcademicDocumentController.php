<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Documents\AddExtractedDataRequest;
use App\Http\Requests\Documents\DocumentIndexRequest;
use App\Http\Requests\Documents\StoreAcademicDocumentRequest;
use App\Http\Requests\Documents\UpdateAcademicDocumentRequest;
use App\Models\AcademicDocument;
use App\Services\OracleProcedureService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class AcademicDocumentController extends ApiController
{
    public function index(DocumentIndexRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $search = strtolower($filters['search'] ?? '');

        $documents = AcademicDocument::query()
            ->with(['student.user'])
            ->when($filters['student_id'] ?? null, fn ($query, $studentId) => $query->where('student_id', $studentId))
            ->when($filters['document_type'] ?? null, fn ($query, $type) => $query->where('document_type', $type))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($search !== '', fn ($query) => $query->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"]))
            ->orderByDesc('created_at')
            ->paginate($request->perPage())
            ->withQueryString();

        return ApiResponse::success('Academic documents retrieved', $documents);
    }

    public function store(StoreAcademicDocumentRequest $request): JsonResponse
    {
        $document = AcademicDocument::query()->create($request->validated());

        return ApiResponse::success('Academic document created successfully', $document, 201);
    }

    public function show(int $id): JsonResponse
    {
        $document = AcademicDocument::query()
            ->with(['student.user', 'extractedData'])
            ->findOrFail($id);

        return ApiResponse::success('Academic document retrieved', $document);
    }

    public function update(UpdateAcademicDocumentRequest $request, int $id): JsonResponse
    {
        $document = AcademicDocument::query()->findOrFail($id);
        $document->update($request->validated());

        return ApiResponse::success('Academic document updated successfully', $document->refresh());
    }

    public function destroy(int $id): JsonResponse
    {
        AcademicDocument::query()->findOrFail($id)->delete();

        return ApiResponse::success('Academic document deleted successfully');
    }

    public function addExtractedData(
        AddExtractedDataRequest $request,
        int $id,
        OracleProcedureService $oracleProcedures,
    ): JsonResponse {
        return $this->runOracleOperation(function () use ($request, $id, $oracleProcedures): AcademicDocument {
            $oracleProcedures->addExtractedData([
                ...$request->validated(),
                'document_id' => $id,
            ]);

            return AcademicDocument::query()->with('extractedData')->findOrFail($id);
        }, 'Extracted document data added successfully', 201);
    }
}
