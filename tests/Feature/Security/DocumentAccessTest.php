<?php

namespace Tests\Feature\Security;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Matter;
use App\Models\Party;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_user_cannot_download_other_users_document()
    {
        Storage::fake('public');

        // 1. Setup Victim and their document
        $victim = User::factory()->create();
        $category = DocumentCategory::create(['name' => 'ID Cards', 'type' => 'general']);

        $file = UploadedFile::fake()->create('victim-id.pdf', 100);
        $path = $file->store('documents/user', 'public');

        $document = Document::create([
            'title' => 'Victim ID',
            'document_category_id' => $category->id,
            'file_path' => $path,
            'file_type' => 'pdf',
            'file_size' => 1024,
            'uploader_id' => $victim->id,
            'documentable_id' => $victim->id,
            'documentable_type' => User::class,
        ]);

        // 2. Setup Attacker (e.g., Employed Lawyer who has 'view documents' but not 'manage users')
        $attacker = User::factory()->create();
        $attacker->assignRole('employed-lawyer'); // Has 'view documents', but NOT 'manage users'

        // Verify attacker permissions
        $this->assertTrue($attacker->can('view documents'));
        $this->assertFalse($attacker->can('manage users'));

        // 3. Attempt download
        $response = $this->actingAs($attacker)->get(route('documents.download', $document));

        // 4. Expect Forbidden (403)
        // CURRENTLY this will fail (it returns 200)
        $response->assertStatus(403);
    }

    public function test_user_can_download_own_document()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        // Give user 'view documents' because the base check still requires it currently
        // Our plan says we will retain base check, OR we can relax it for own documents.
        // For now, let's assume they have it or we grant it.
        // Actually, a generic user might NOT have 'view documents'.
        // If they don't, they get 403 immediately.
        // So this test only passes if the user HAS 'view documents'.
        // Let's give them the permission.
        $user->givePermissionTo('view documents');

        $category = DocumentCategory::create(['name' => 'My Docs', 'type' => 'general']);
        $file = UploadedFile::fake()->create('my-doc.pdf', 100);
        $path = $file->store('documents/user', 'public');

        $document = Document::create([
            'title' => 'My Doc',
            'document_category_id' => $category->id,
            'file_path' => $path,
            'file_type' => 'pdf',
            'file_size' => 1024,
            'uploader_id' => $user->id,
            'documentable_id' => $user->id,
            'documentable_type' => User::class,
        ]);

        $response = $this->actingAs($user)->get(route('documents.download', $document));

        $response->assertStatus(200);
    }

    public function test_user_can_download_matter_document_if_authorized()
    {
        Storage::fake('public');

        // Create Matter
        $party = Party::create(['full_name' => 'John Doe', 'type' => 'client']);
        $matter = Matter::create([
            'title' => 'Test Matter',
            'party_id' => $party->id,
            'type' => 'litigation',
            'status' => 'open'
        ]);

        $category = DocumentCategory::create(['name' => 'Evidence', 'type' => 'general']);
        $file = UploadedFile::fake()->create('evidence.pdf', 100);
        $path = $file->store('documents/matter', 'public');

        // User with permissions
        $user = User::factory()->create();
        $user->givePermissionTo(['view documents', 'view matters']);

        $document = Document::create([
            'title' => 'Evidence',
            'document_category_id' => $category->id,
            'file_path' => $path,
            'file_type' => 'pdf',
            'file_size' => 1024,
            'uploader_id' => $user->id,
            'documentable_id' => $matter->id,
            'documentable_type' => Matter::class,
        ]);

        $response = $this->actingAs($user)->get(route('documents.download', $document));

        $response->assertStatus(200);
    }

    public function test_user_cannot_download_matter_document_without_matter_permission()
    {
        Storage::fake('public');

        $party = Party::create(['full_name' => 'Jane Doe', 'type' => 'client']);
        $matter = Matter::create([
            'title' => 'Secret Matter',
            'party_id' => $party->id,
            'type' => 'litigation',
            'status' => 'open'
        ]);

        $category = DocumentCategory::create(['name' => 'Secret', 'type' => 'general']);
        $file = UploadedFile::fake()->create('secret.pdf', 100);
        $path = $file->store('documents/matter', 'public');

        // User with ONLY 'view documents', NOT 'view matters'
        $user = User::factory()->create();

        $document = Document::create([
            'title' => 'Secret Doc',
            'document_category_id' => $category->id,
            'file_path' => $path,
            'file_type' => 'pdf',
            'file_size' => 1024,
            'uploader_id' => $user->id,
            'documentable_id' => $matter->id,
            'documentable_type' => Matter::class,
        ]);
        $user->givePermissionTo('view documents');
        // Ensure they don't have 'view matters'
        // (Default user has no permissions, so just adding one is enough)

        $response = $this->actingAs($user)->get(route('documents.download', $document));

        $response->assertStatus(403);
    }
}
