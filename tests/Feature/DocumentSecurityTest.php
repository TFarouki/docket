<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\User;
use App\Models\Matter;
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
        Permission::firstOrCreate(['name' => 'view matters']);
    }

    public function test_user_can_download_document_with_permission()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->givePermissionTo('view documents');
        $user->givePermissionTo('view matters');

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

        $response->assertStatus(200);
    }

    public function test_user_cannot_download_document_without_permission()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        // No permission given

        $category = DocumentCategory::create(['name' => 'Contracts', 'type' => 'legal']);
        $otherUser = User::factory()->create();

        $document = Document::create([
            'title' => 'Test Doc',
            'document_category_id' => $category->id,
            'file_path' => 'documents/test.pdf',
            'file_type' => 'pdf',
            'file_size' => 1024,
            'uploader_id' => $otherUser->id,
            'documentable_id' => 1,
            'documentable_type' => 'App\Models\Matter',
        ]);

        Storage::disk('public')->put('documents/test.pdf', 'content');

        $response = $this->actingAs($user)->get(route('documents.download', $document));

        $response->assertStatus(403);
    }

    public function test_user_cannot_upload_document_to_invalid_model_type()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $user->givePermissionTo('create documents');

        $category = DocumentCategory::create(['name' => 'Misc', 'type' => 'general']);
        $file = UploadedFile::fake()->create('test.pdf', 100);

        $response = $this->actingAs($user)->post(route('documents.store'), [
            'title' => 'Invalid Type Doc',
            'document_category_id' => $category->id,
            'file' => $file,
            'documentable_id' => $user->id,
            'documentable_type' => 'App\Models\SystemSetting', // Invalid type
        ]);

        $response->assertSessionHasErrors(['documentable_type']);
    }

    public function test_user_cannot_upload_document_to_nonexistent_model_id()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $user->givePermissionTo('create documents');

        $category = DocumentCategory::create(['name' => 'Misc', 'type' => 'general']);
        $file = UploadedFile::fake()->create('test.pdf', 100);

        $response = $this->actingAs($user)->post(route('documents.store'), [
            'title' => 'Ghost Doc',
            'document_category_id' => $category->id,
            'file' => $file,
            'documentable_id' => 99999, // Non-existent ID
            'documentable_type' => 'App\Models\User', // Valid type
        ]);

        $response->assertSessionHasErrors(['documentable_id']);
    }

    public function test_user_can_upload_document_to_valid_target()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $user->givePermissionTo('create documents');

        $category = DocumentCategory::create(['name' => 'Misc', 'type' => 'general']);
        $file = UploadedFile::fake()->create('valid.pdf', 100);

        $response = $this->actingAs($user)->post(route('documents.store'), [
            'title' => 'Valid Doc',
            'document_category_id' => $category->id,
            'file' => $file,
            'documentable_id' => $user->id,
            'documentable_type' => 'App\Models\User',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('documents', [
            'title' => 'Valid Doc',
            'documentable_type' => 'App\Models\User',
            'documentable_id' => $user->id,
        ]);
    }
}
