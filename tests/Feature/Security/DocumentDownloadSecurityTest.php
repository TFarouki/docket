<?php

namespace Tests\Feature\Security;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Matter;
use App\Models\Party;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DocumentDownloadSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        foreach ([
            'view documents', 'create documents', 'delete documents',
            'manage users',
            'view matters', 'create matters',
            'view parties',
        ] as $perm) {
            Permission::create(['name' => $perm]);
        }
    }

    public function test_unauthorized_user_cannot_download_other_users_document()
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        $attacker = User::factory()->create();
        $attacker->givePermissionTo('view documents'); // Has global view permission

        $category = DocumentCategory::create(['name' => 'ID Cards', 'type' => 'general']);

        $document = Document::create([
            'title' => 'Admin Secret',
            'document_category_id' => $category->id,
            'file_path' => 'documents/users/secret.pdf',
            'file_type' => 'pdf',
            'file_size' => 1024,
            'uploader_id' => $admin->id,
            'documentable_type' => User::class,
            'documentable_id' => $admin->id,
        ]);

        Storage::disk('public')->put($document->file_path, 'secret content');

        // Act as attacker
        $response = $this->actingAs($attacker)->get(route('documents.download', $document));

        // Should be forbidden (IDOR protection)
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_download_their_own_document()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->givePermissionTo('view documents');

        $category = DocumentCategory::create(['name' => 'My Docs', 'type' => 'general']);

        $document = Document::create([
            'title' => 'My File',
            'document_category_id' => $category->id,
            'file_path' => 'documents/users/myfile.pdf',
            'file_type' => 'pdf',
            'file_size' => 1024,
            'uploader_id' => $user->id,
            'documentable_type' => User::class,
            'documentable_id' => $user->id,
        ]);

        Storage::disk('public')->put($document->file_path, 'content');

        $response = $this->actingAs($user)->get(route('documents.download', $document));

        $response->assertStatus(200);
    }

    public function test_admin_can_download_user_documents()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $admin = User::factory()->create();
        $admin->givePermissionTo(['view documents', 'manage users']);

        $category = DocumentCategory::create(['name' => 'Docs', 'type' => 'general']);

        $document = Document::create([
            'title' => 'User File',
            'document_category_id' => $category->id,
            'file_path' => 'documents/users/file.pdf',
            'file_type' => 'pdf',
            'file_size' => 1024,
            'uploader_id' => $user->id,
            'documentable_type' => User::class,
            'documentable_id' => $user->id,
        ]);

        Storage::disk('public')->put($document->file_path, 'content');

        $response = $this->actingAs($admin)->get(route('documents.download', $document));

        $response->assertStatus(200);
    }

    public function test_unauthorized_user_cannot_download_restricted_matter_document()
    {
        Storage::fake('public');

        $lawyer = User::factory()->create();
        $attacker = User::factory()->create();
        $attacker->givePermissionTo('view documents');
        // Attacker does NOT have 'view matters'

        $party = Party::create(['full_name' => 'Client A', 'type' => 'client']);

        $matter = Matter::create([
            'title' => 'Confidential Case',
            'party_id' => $party->id,
            'responsible_lawyer_id' => $lawyer->id,
            'type' => 'litigation',
            'status' => 'open',
        ]);

        $category = DocumentCategory::create(['name' => 'Evidence', 'type' => 'general']);

        $document = Document::create([
            'title' => 'Sensitive Evidence',
            'document_category_id' => $category->id,
            'file_path' => 'documents/matters/evidence.pdf',
            'file_type' => 'pdf',
            'file_size' => 1024,
            'uploader_id' => $lawyer->id,
            'documentable_type' => Matter::class,
            'documentable_id' => $matter->id,
        ]);

        Storage::disk('public')->put($document->file_path, 'evidence');

        $response = $this->actingAs($attacker)->get(route('documents.download', $document));

        $response->assertStatus(403);
    }

    public function test_authorized_user_can_download_matter_document()
    {
        Storage::fake('public');

        $lawyer = User::factory()->create();
        $lawyer->givePermissionTo(['view documents', 'view matters']);

        $party = Party::create(['full_name' => 'Client A', 'type' => 'client']);

        $matter = Matter::create([
            'title' => 'Case',
            'party_id' => $party->id,
            'responsible_lawyer_id' => $lawyer->id,
            'type' => 'litigation',
            'status' => 'open',
        ]);

        $category = DocumentCategory::create(['name' => 'Evidence', 'type' => 'general']);

        $document = Document::create([
            'title' => 'Evidence',
            'document_category_id' => $category->id,
            'file_path' => 'documents/matters/evidence.pdf',
            'file_type' => 'pdf',
            'file_size' => 1024,
            'uploader_id' => $lawyer->id,
            'documentable_type' => Matter::class,
            'documentable_id' => $matter->id,
        ]);

        Storage::disk('public')->put($document->file_path, 'content');

        $response = $this->actingAs($lawyer)->get(route('documents.download', $document));

        $response->assertStatus(200);
    }
}
