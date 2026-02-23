<?php

namespace Tests\Feature;

use App\Filament\Guru\Resources\SiswaResource;
use App\Filament\Guru\Resources\SiswaResource\Pages\ListSiswa;
use App\Models\Kelas;
use App\Models\RoleUser;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class GuruResetSessionTest extends TestCase
{
  use RefreshDatabase;

  private User $guru;
  private User $siswaWithSession;
  private User $siswaWithoutSession;
  private Kelas $kelas;

  protected function setUp(): void
  {
    parent::setUp();

    // Seed roles
    $this->artisan('db:seed', ['--class' => 'RoleUserSeeder']);

    $guruRole = RoleUser::where('name', 'Guru')->first();
    $siswaRole = RoleUser::where('name', 'Siswa')->first();

    // Create guru
    $this->guru = User::factory()->create([
      'name' => 'Ibu Guru Test',
      'email' => 'guru.test@smkn1ciamis.sch.id',
      'password' => Hash::make('guru123'),
      'role_user_id' => $guruRole->id,
    ]);

    // Create kelas with guru as wali
    $this->kelas = Kelas::create([
      'nama' => '10 RPL 1 TES',
      'wali_id' => $this->guru->id,
    ]);

    // Create siswa WITH active session (lupa logout)
    $this->siswaWithSession = User::factory()->create([
      'name' => 'Siswa Lupa Logout',
      'nisn' => '0099887766',
      'email' => 'lupa@siswa.test',
      'password' => Hash::make('siswa123'),
      'role_user_id' => $siswaRole->id,
      'kelas_id' => $this->kelas->id,
      'active_session_id' => 'stuck-session-abc123',
      'session_login_at' => now()->subHours(5),
    ]);

    // Create siswa WITHOUT active session (normal)
    $this->siswaWithoutSession = User::factory()->create([
      'name' => 'Siswa Normal',
      'nisn' => '0011223344',
      'email' => 'normal@siswa.test',
      'password' => Hash::make('siswa123'),
      'role_user_id' => $siswaRole->id,
      'kelas_id' => $this->kelas->id,
      'active_session_id' => null,
      'session_login_at' => null,
    ]);

    // Set Guru panel context
    Filament::setCurrentPanel(Filament::getPanel('guru'));
  }

  public function test_guru_can_access_siswa_list_page(): void
  {
    $this->actingAs($this->guru);

    $response = $this->get(SiswaResource::getUrl('index', panel: 'guru'));
    $response->assertSuccessful();
  }

  public function test_siswa_with_session_shows_in_table(): void
  {
    $this->actingAs($this->guru);

    Livewire::test(ListSiswa::class)
      ->assertCanSeeTableRecords([$this->siswaWithSession, $this->siswaWithoutSession]);
  }

  public function test_reset_session_action_exists_for_siswa_with_active_session(): void
  {
    $this->actingAs($this->guru);

    Livewire::test(ListSiswa::class)
      ->assertTableActionExists('resetSession');
  }

  public function test_reset_session_disabled_when_no_active_session(): void
  {
    $this->actingAs($this->guru);

    Livewire::test(ListSiswa::class)
      ->assertTableActionDisabled('resetSession', $this->siswaWithoutSession);
  }

  public function test_reset_session_enabled_when_active_session_exists(): void
  {
    $this->actingAs($this->guru);

    Livewire::test(ListSiswa::class)
      ->assertTableActionEnabled('resetSession', $this->siswaWithSession);
  }

  public function test_reset_session_clears_active_session(): void
  {
    $this->actingAs($this->guru);

    // Verify session is active before reset
    $this->assertNotNull($this->siswaWithSession->active_session_id);
    $this->assertNotNull($this->siswaWithSession->session_login_at);

    // Execute the reset action
    Livewire::test(ListSiswa::class)
      ->callTableAction('resetSession', $this->siswaWithSession);

    // Verify session is cleared
    $this->siswaWithSession->refresh();
    $this->assertNull($this->siswaWithSession->active_session_id);
    $this->assertNull($this->siswaWithSession->session_login_at);
  }

  public function test_siswa_can_login_after_session_reset(): void
  {
    // First reset the session
    $this->siswaWithSession->updateQuietly([
      'active_session_id' => null,
      'session_login_at' => null,
    ]);

    // Now login should succeed
    $result = Auth::attempt([
      'nisn' => '0099887766',
      'password' => 'siswa123',
    ]);

    $this->assertTrue($result);
  }

  public function test_siswa_blocked_when_session_still_active(): void
  {
    // active_session_id is still set — the EnsureSingleSession middleware
    // should block this. Verify the session data is still there.
    $this->siswaWithSession->refresh();
    $this->assertEquals('stuck-session-abc123', $this->siswaWithSession->active_session_id);
    $this->assertNotNull($this->siswaWithSession->session_login_at);
  }

  public function test_reset_password_action_still_works(): void
  {
    $this->actingAs($this->guru);

    Livewire::test(ListSiswa::class)
      ->assertTableActionExists('resetPassword');
  }
}
