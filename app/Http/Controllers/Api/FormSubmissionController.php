<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormSubmissionController extends Controller
{
  /**
   * Simpan / update formulir harian.
   */
  public function store(Request $request): JsonResponse
  {
    $request->validate([
      'hari_ke' => 'required|integer|min:1|max:30',
      'data'    => 'required|array',
    ]);

    $user = Auth::user();

    $submission = FormSubmission::updateOrCreate(
      [
        'user_id' => $user->id,
        'hari_ke' => $request->hari_ke,
      ],
      [
        'data' => $request->data,
        'status' => 'pending',
        'verified_by' => null,
        'verified_at' => null,
        'catatan_guru' => null,
      ]
    );

    return response()->json([
      'success' => true,
      'message' => 'Formulir hari ke-' . $request->hari_ke . ' berhasil disimpan.',
      'submission' => $submission,
    ]);
  }

  /**
   * Ambil semua submission milik user yang login.
   */
  public function index(): JsonResponse
  {
    $user = Auth::user();

    $submissions = FormSubmission::where('user_id', $user->id)
      ->orderBy('hari_ke')
      ->get();

    return response()->json([
      'success' => true,
      'submissions' => $submissions,
      'submitted_days' => $submissions->pluck('hari_ke')->toArray(),
    ]);
  }

  /**
   * Ambil submission untuk hari tertentu.
   */
  public function show(int $hariKe): JsonResponse
  {
    $user = Auth::user();

    $submission = FormSubmission::where('user_id', $user->id)
      ->where('hari_ke', $hariKe)
      ->first();

    return response()->json([
      'success'    => true,
      'submission' => $submission,
    ]);
  }
}
