<?php

namespace App\Repositories\Contracts;

use App\Models\FormSubmission;
use App\Models\User;

interface FormSubmissionRepositoryInterface
{
  /**
   * Get all submissions for a user, ordered by hari_ke.
   *
   * @return array{submissions: array, submitted_days: array}
   */
  public function getAllForUser(User $user): array;

  /**
   * Get a specific submission for a user and day.
   */
  public function getForUserAndDay(User $user, int $hariKe): ?FormSubmission;

  /**
   * Find existing submission for user and day.
   */
  public function findByUserAndDay(User $user, int $hariKe): ?FormSubmission;

  /**
   * Create or update a submission.
   */
  public function updateOrCreate(User $user, int $hariKe, array $data): FormSubmission;

  /**
   * Update submission data field (for syncing prayer check-in data back).
   */
  public function updateData(FormSubmission $submission, array $data): bool;
}
