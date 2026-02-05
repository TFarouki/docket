<?php

namespace Tests\Feature\Security;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\User;
use App\Models\Matter;
use App\Models\Party;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DocumentDownloadIDORTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Reset permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define relevant permissions
        Permission::firstOrCreate(['name' => 'view documents']);
        Permission::firstOrCreate(['name' => 'view matters']);
        Permission::firstOrCreate(['name' => 'view parties']);
        Permission::firstOrCreate(['name' => 'manage users']);
    }

    public function test_user_cannot_download_document_attached_to_another_user_without_permission()
    {
        Storage::fake('public');

        // Attacker: Has 'view documents' but not 'manage users'
        $attacker = User::factory()->create();
        $attacker->givePermissionTo('view documents');

        // Victim: Another user
        $victim = User::factory()->create();

        // Document attached to Victim
        $category = DocumentCategory::create(['name' => 'HR', 'type' => 'restricted']);
        $document = Document::create([
            'title' => 'Contract',
            'document_category_id' => $category->id,
            'file_path' => 'documents/contract.pdf',
            'file_type' => 'pdf',
            'file_size' => 1024,
            'uploader_id' => $victim->id,
            'documentable_id' => $victim->id,
            'documentable_type' => User::class,
        ]);

        Storage::disk('public')->put('documents/contract.pdf', 'secret content');

        // Attacker tries to download Victim's document
        $response = $this->actingAs($attacker)->get(route('documents.download', $document));

        // CURRENTLY: This likely returns 200 (Vulnerable)
        // DESIRED: 403 Forbidden
        // Assert 403 to prove it's fixed (this test should fail initially if vulnerable)
        $response->assertStatus(403);
    }

    public function test_user_cannot_download_document_attached_to_restricted_matter()
    {
        Storage::fake('public');

        // Attacker: Has 'view documents' but NOT 'view matters'
        $attacker = User::factory()->create();
        $attacker->givePermissionTo('view documents');
        // Note: intentionally NOT giving 'view matters'

        // Restricted Matter
        $matter = Matter::create([
            'reference_number' => 'REF-001',
            'title' => 'Secret Case',
            'type' => 'Litigation',
            'status' => 'Open',
            'start_date' => now(),
            'party_id' => Party::create(['full_name' => 'Client X'])->id,
        ]);

        // Document attached to Matter
        $category = DocumentCategory::create(['name' => 'Evidence', 'type' => 'legal']);
        $document = Document::create([
            'title' => 'Evidence 1',
            'document_category_id' => $category->id,
            'file_path' => 'documents/evidence.pdf',
            'file_type' => 'pdf',
            'file_size' => 1024,
            'uploader_id' => User::factory()->create()->id,
            'documentable_id' => $matter->id,
            'documentable_type' => Matter::class,
        ]);

        Storage::disk('public')->put('documents/evidence.pdf', 'evidence content');

        $response = $this->actingAs($attacker)->get(route('documents.download', $document));

        // Should be 403 because they can't view the matter
        $response->assertStatus(403);
    }
}
