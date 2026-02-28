<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FormSubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormSubmissionController extends Controller
{
  public function __construct(
    private FormSubmissionService $formSubmissionService,
  ) {}

  /**
   * Simpan / update formulir harian.
   */
  public function store(Request $request): JsonResponse
  {
    $request->validate([
      'hari_ke' => 'required|integer|min:1|max:30',
      'data'    => 'required|array',
    ]);

    $result = $this->formSubmissionService->storeSubmission(
      Auth::user(),
      $request->hari_ke,
      $request->data
    );

    $statusCode = $result['status'] ?? ($result['success'] ? 200 : 500);

    return response()->json(
      collect($result)->except('status')->toArray(),
      $statusCode
    );
  }

  /**
   * Ambil semua submission milik user yang login.
   */
  public function index(): JsonResponse
  {
    $data = $this->formSubmissionService->getAllForUser(Auth::user());

    return response()->json([
      'success' => true,
      'submissions' => $data['submissions'],
      'submitted_days' => $data['submitted_days'],
    ]);
  }

  /**
   * Ambil submission untuk hari tertentu.
   */
  public function show(int $hariKe): JsonResponse
  {
    $submission = $this->formSubmissionService->getForDay(Auth::user(), $hariKe);

    return response()->json([
      'success'    => true,
      'submission' => $submission,
    ]);
  }
}
