<?php

namespace Tests\Feature;

use App\Enums\RequestStatus;
use App\Filament\Portal\Resources\FeatureRequests\Pages\CreateFeatureRequest;
use App\Filament\Portal\Resources\FeatureRequests\Pages\ViewFeatureRequest;
use App\Filament\Resources\FeatureRequests\Pages\EditFeatureRequest as AdminEditFeatureRequest;
use App\Filament\Resources\FeatureRequests\RelationManagers\NotesRelationManager;
use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\Client;
use App\Models\ClientApp;
use App\Models\FeatureRequest;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    // AuthenticateSession logs out when the session holds another user's password hash.
    private function loginAs(User $user): void
    {
        $this->flushSession();
        $this->actingAs($user);
    }

    /** @return array{0: User, 1: ClientApp} */
    private function clientAccount(string $name): array
    {
        $client = Client::create(['name' => $name]);
        $app = ClientApp::create(['client_id' => $client->id, 'name' => "Aplikasi {$name}"]);
        $user = User::factory()->create(['client_id' => $client->id]);
        $user->assignRole('client');

        return [$user, $app];
    }

    private function request(ClientApp $app, string $title = 'Fitur laporan stok'): FeatureRequest
    {
        return FeatureRequest::create([
            'client_app_id' => $app->id,
            'title' => $title,
            'description' => 'Laporan stok harian per gudang.',
            'status' => RequestStatus::Submitted,
        ]);
    }

    public function test_admin_and_portal_are_separated_by_role(): void
    {
        [$clientUser] = $this->clientAccount('Samora');

        $this->get('/portal')->assertRedirect('/portal/login');

        $this->loginAs(User::first());
        $this->get('/admin/feature-requests')->assertOk();
        $this->get('/portal/permintaan')->assertForbidden();

        $this->loginAs($clientUser);
        $this->get('/portal/permintaan')->assertOk();
        $this->get('/admin')->assertForbidden();

        // A client role without a company link must not reach the portal.
        $clientUser->update(['client_id' => null]);
        $this->get('/portal/permintaan')->assertForbidden();
    }

    public function test_client_only_sees_and_submits_requests_for_own_apps(): void
    {
        [$user, $ownApp] = $this->clientAccount('Samora');
        [, $otherApp] = $this->clientAccount('Lain');
        $other = $this->request($otherApp, 'Rahasia klien lain');

        $this->loginAs($user);
        $this->get('/portal/permintaan')->assertOk()->assertDontSee('Rahasia klien lain');
        $this->get("/portal/permintaan/{$other->id}")->assertNotFound();
        $this->get("/portal/permintaan/{$other->id}/edit")->assertNotFound();

        Filament::setCurrentPanel('portal');
        $form = ['type' => 'feature', 'title' => 'Export Excel', 'description' => 'Export laporan ke Excel.', 'budget' => 5000000];

        Livewire::test(CreateFeatureRequest::class)
            ->fillForm([...$form, 'client_app_id' => $otherApp->id])
            ->call('create')
            ->assertHasFormErrors(['client_app_id']);

        Livewire::test(CreateFeatureRequest::class)
            ->fillForm([...$form, 'client_app_id' => $ownApp->id])
            ->call('create')
            ->assertHasNoFormErrors();

        $created = FeatureRequest::where('title', 'Export Excel')->sole();
        $this->assertSame($ownApp->id, $created->client_app_id);
        $this->assertSame($user->id, $created->user_id);
        $this->assertSame(RequestStatus::Submitted, $created->status);
        $this->assertSame(1, FeatureRequest::where('client_app_id', $otherApp->id)->count());
    }

    public function test_status_change_is_logged_locks_editing_and_client_can_reply(): void
    {
        [$user, $app] = $this->clientAccount('Samora');
        $request = $this->request($app);

        $this->loginAs($user);
        $this->get("/portal/permintaan/{$request->id}/edit")->assertOk();

        $this->loginAs(User::first());
        $this->get("/admin/feature-requests/{$request->id}/edit")->assertOk();
        Filament::setCurrentPanel('admin');
        Livewire::test(AdminEditFeatureRequest::class, ['record' => $request->getRouteKey()])
            ->fillForm(['status' => RequestStatus::Approved->value, 'agreed_price' => 7500000, 'due_date' => '2026-11-30'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('feature_request_notes', [
            'feature_request_id' => $request->id,
            'user_id' => User::first()->id,
            'body' => 'Status diubah menjadi Disetujui.',
        ]);

        $this->loginAs($user);
        $this->get("/portal/permintaan/{$request->id}/edit")->assertForbidden();
        $this->get("/portal/permintaan/{$request->id}")->assertOk()
            ->assertSee('Disetujui')->assertSee('7.500.000')->assertSee('30 Nov 2026');

        Filament::setCurrentPanel('portal');
        Livewire::test(NotesRelationManager::class, ['ownerRecord' => $request, 'pageClass' => ViewFeatureRequest::class])
            ->assertSee('Status diubah menjadi Disetujui.')
            ->callTableAction('create', data: ['body' => 'Setuju, silakan mulai.'])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('feature_request_notes', [
            'feature_request_id' => $request->id,
            'user_id' => $user->id,
            'body' => 'Setuju, silakan mulai.',
        ]);
    }

    public function test_admin_creates_client_account_with_role(): void
    {
        $client = Client::create(['name' => 'Samora']);
        $this->loginAs(User::first());
        $this->get('/admin/users')->assertOk();
        $this->get('/admin/client-apps')->assertOk();

        Filament::setCurrentPanel('admin');
        Livewire::test(ManageUsers::class)
            ->callAction('create', data: [
                'name' => 'Budi Samora',
                'email' => 'budi@samora.test',
                'password' => 'rahasia-123',
                'role' => 'client',
                'client_id' => $client->id,
            ])
            ->assertHasNoActionErrors();

        $budi = User::where('email', 'budi@samora.test')->sole();
        $this->assertTrue($budi->hasRole('client'));
        $this->assertEquals($client->id, $budi->client_id);
        $this->assertTrue($budi->canAccessPanel(Filament::getPanel('portal')));
        $this->assertFalse($budi->canAccessPanel(Filament::getPanel('admin')));
        $this->assertTrue(User::first()->hasRole('admin'));
    }
}
