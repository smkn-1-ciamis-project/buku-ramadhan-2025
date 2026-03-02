<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PageController extends Controller
{
  public function __construct(
    private UserService $userService,
  ) {}

  public function index(): RedirectResponse
  {
    return redirect('/siswa/login');
  }

  public function timPengembang(): View
  {
    return view('tim-pengembang');
  }

  public function siswaDashboard(): RedirectResponse
  {
    if (!Auth::check()) {
      return redirect('/siswa/login');
    }
    return redirect('/siswa/home');
  }

  public function changePassword(Request $request): JsonResponse
  {
    $request->validate([
      'current_password' => 'required|string',
      'new_password' => 'required|string|min:8|confirmed',
    ]);

    /** @var \App\Models\User $user */
    $user = Auth::user();

    $result = $this->userService->changePassword(
      $user,
      $request->current_password,
      $request->new_password
    );

    $statusCode = $result['status'] ?? ($result['success'] ? 200 : 500);

    return response()->json(
      collect($result)->except('status')->toArray(),
      $statusCode
    );
  }

  public function formSettings(string $agama): JsonResponse
  {
    $result = $this->userService->getFormSettings($agama);

    if (!$result['success']) {
      $statusCode = $result['status'] ?? 500;
      $response = collect($result)->except(['status', 'success'])->toArray();

      return response()->json($response, $statusCode);
    }

    return response()->json($result['data']);
  }

  public function appSettings(): JsonResponse
  {
    return response()->json(AppSetting::getForFrontend());
  }
}
