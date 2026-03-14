<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Support\RoleGuideDefaults;
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

  public function bukuPanduan(string $role): View
  {
    $normalizedRole = strtolower(trim($role));

    $mappings = [
      'siswa' => [
        'key' => 'guide_siswa',
        'title' => 'Buku Panduan Siswa',
        'login_url' => url('/siswa/login'),
      ],
      'guru' => [
        'key' => 'guide_guru',
        'title' => 'Buku Panduan Guru',
        'login_url' => url('/portal-guru-smkn1/login'),
      ],
      'kesiswaan' => [
        'key' => 'guide_kesiswaan',
        'title' => 'Buku Panduan Kesiswaan',
        'login_url' => url('/portal-kesiswaan-smkn1/login'),
      ],
    ];

    abort_unless(isset($mappings[$normalizedRole]), 404);

    $config = $mappings[$normalizedRole];
    $setting = AppSetting::query()
      ->select(['key', 'value', 'updated_at'])
      ->where('key', $config['key'])
      ->first();

    $guideContent = (string) ($setting?->value ?: RoleGuideDefaults::forRole($normalizedRole));
    $updatedAtLabel = $setting?->updated_at?->format('d M Y H:i') ?? 'Belum diperbarui dari panel superadmin';

    $contactsKey = match ($normalizedRole) {
      'siswa' => 'guide_siswa_admin_contacts',
      'guru' => 'guide_guru_admin_contacts',
      'kesiswaan' => 'guide_kesiswaan_admin_contacts',
      default => null,
    };

    $flowKey = match ($normalizedRole) {
      'siswa' => 'guide_siswa_flow_steps',
      'guru' => 'guide_guru_flow_steps',
      'kesiswaan' => 'guide_kesiswaan_flow_steps',
      default => null,
    };

    $adminContacts = $contactsKey
      ? $this->normalizeAdminContacts(AppSetting::getValue($contactsKey, RoleGuideDefaults::defaultContacts($normalizedRole)))
      : [];

    if (empty($adminContacts)) {
      $adminContacts = RoleGuideDefaults::defaultContacts($normalizedRole);
    }

    $flowSteps = $flowKey
      ? $this->normalizeFlowSteps(AppSetting::getValue($flowKey, RoleGuideDefaults::defaultFlowSteps($normalizedRole)))
      : [];

    if (empty($flowSteps)) {
      $flowSteps = RoleGuideDefaults::defaultFlowSteps($normalizedRole);
    }

    return view('buku-panduan', [
      'title' => $config['title'],
      'role' => $normalizedRole,
      'guideContent' => $guideContent,
      'updatedAtLabel' => $updatedAtLabel,
      'adminContacts' => $adminContacts,
      'flowSteps' => $flowSteps,
      'loginUrl' => $config['login_url'],
    ]);
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

  /**
   * @param mixed $contacts
   * @return array<int, array{name: string, phone: string}>
   */
  private function normalizeAdminContacts(mixed $contacts): array
  {
    if (!is_array($contacts)) {
      return [];
    }

    $normalized = [];

    foreach ($contacts as $item) {
      if (!is_array($item)) {
        continue;
      }

      $name = trim((string) ($item['name'] ?? ''));
      $phone = trim((string) ($item['phone'] ?? ''));

      if ($phone === '') {
        continue;
      }

      $normalized[] = [
        'name' => $name !== '' ? $name : 'Admin',
        'phone' => $phone,
      ];
    }

    return $normalized;
  }

  /**
   * @param mixed $steps
   * @return array<int, array{title: string, desc: string}>
   */
  private function normalizeFlowSteps(mixed $steps): array
  {
    if (!is_array($steps)) {
      return [];
    }

    $normalized = [];

    foreach ($steps as $item) {
      if (!is_array($item)) {
        continue;
      }

      $title = trim((string) ($item['title'] ?? ''));
      $desc = trim((string) ($item['desc'] ?? ''));

      if ($title === '' && $desc === '') {
        continue;
      }

      $normalized[] = [
        'title' => $title !== '' ? $title : 'Langkah',
        'desc' => $desc,
      ];
    }

    return $normalized;
  }
}
