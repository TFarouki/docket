<?php

namespace Tests\Feature\Performance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class NewIndexesTest extends TestCase
{
    use RefreshDatabase;

    public function test_indexes_exist()
    {
        // By default Laravel names indexes as table_column_index
        $this->assertTrue(Schema::hasIndex('matters', 'matters_reference_number_index'), 'Index on matters.reference_number missing');
        $this->assertTrue(Schema::hasIndex('parties', 'parties_full_name_index'), 'Index on parties.full_name missing');
    }
}
