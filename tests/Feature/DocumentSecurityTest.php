<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DocumentSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::firstOrCreate(['name' => 'view documents']);
        Permission::firstOrCreate(['name' => 'create documents']);
        Permission::firstOrCreate(['name' => 'delete documents']);
    }

    public function test_user_can_download_document_with_permission()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->givePermissionTo('view documents');

        $category = DocumentCategory::create(['name' => 'Contracts', 'type' => 'legal']);

        $document = Document::create([
            'title' => 'Test Doc',
            'document_category_id' => $category->id,
            'file_path' => 'documents/test.pdf',
            'file_type' => 'pdf',
            'file_size' => 1024,
            'uploader_id' => $user->id,
            'documentable_id' => 1,
            'documentable_type' => 'App\Models\Matter',
        ]);

        Storage::disk('public')->put('documents/test.pdf', 'content');

        $response = $this->actingAs($user)->get(route('documents.download', $document));

        // Before the fix, this might pass if the controller has no checks (which it doesn't).
        // But with the intended fix, this should be 200.
        // Wait, current code allows anyone. So this test PASSES now.
        $response->assertStatus(200);
    }

    public function test_user_cannot_download_document_without_permission()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        // No permission given

        $category = DocumentCategory::create(['name' => 'Contracts', 'type' => 'legal']);

        $document = Document::create([
            'title' => 'Test Doc',
            'document_category_id' => $category->id,
            'file_path' => 'documents/test.pdf',
            'file_type' => 'pdf',
            'file_size' => 1024,
            'uploader_id' => 999, // Another user
            'documentable_id' => 1,
            'documentable_type' => 'App\Models\Matter',
        ]);

        Storage::disk('public')->put('documents/test.pdf', 'content');

        $response = $this->actingAs($user)->get(route('documents.download', $document));

        // Before the fix, this fails (assertion 403, actual 200).
        $response->assertStatus(403);
    }
}
