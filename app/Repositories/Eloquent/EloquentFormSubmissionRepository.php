<?php

namespace App\Repositories\Eloquent;

use App\Models\FormSubmission;
use App\Models\User;
use App\Repositories\Contracts\FormSubmissionRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class EloquentFormSubmissionRepository implements FormSubmissionRepositoryInterface
{
  public function getAllForUser(User $user): array
  {
    return Cache::remember("submissions_{$user->id}", 180, function () use ($user) {
      $submissions = FormSubmission::where('user_id', $user->id)
        ->orderBy('hari_ke')
        ->get();

      return [
        'submissions' => $submissions->toArray(),
        'submitted_days' => $submissions->pluck('hari_ke')->toArray(),
      ];
    });
  }

  public function getForUserAndDay(User $user, int $hariKe): ?FormSubmission
  {
    return Cache::remember("submission_{$user->id}_{$hariKe}", 180, function () use ($user, $hariKe) {
      return FormSubmission::where('user_id', $user->id)
        ->where('hari_ke', $hariKe)
        ->first();
    });
  }

  public function findByUserAndDay(User $user, int $hariKe): ?FormSubmission
  {
    return FormSubmission::where('user_id', $user->id)
      ->where('hari_ke', $hariKe)
      ->first();
  }

  public function updateOrCreate(User $user, int $hariKe, array $data): FormSubmission
  {
    $submission = FormSubmission::updateOrCreate(
      [
        'user_id' => $user->id,
        'hari_ke' => $hariKe,
      ],
      $data
    );

    // Bust caches
    Cache::forget("submissions_{$user->id}");
    Cache::forget("submission_{$user->id}_{$hariKe}");

    return $submission;
  }

  public function updateData(FormSubmission $submission, array $data): bool
  {
    return $submission->update(['data' => $data]);
  }
}
