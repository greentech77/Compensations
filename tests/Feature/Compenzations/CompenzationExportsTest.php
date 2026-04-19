<?php

namespace Tests\Feature\Compenzations;

use Tests\TestCase;

class CompenzationExportsTest extends TestCase
{
    public function test_compenzations_export_rejects_invalid_format()
    {
        $this->withoutMiddleware();

        $response = $this->get(route('compenzations.export', [
            'format' => 'pdf',
        ]));

        $response->assertSessionHasErrors('format');
    }

    public function test_stats_export_rejects_invalid_date_range()
    {
        $this->withoutMiddleware();

        $response = $this->get(route('compenzations.stats.export', [
            'date_from' => '2026-03-10',
            'date_to' => '2026-03-01',
        ]));

        $response->assertSessionHasErrors('date_to');
    }
}
