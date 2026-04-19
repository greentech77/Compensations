<?php

namespace Tests\Feature\Exports;

use App\Services\Exports\ContractsExportService;
use Tests\TestCase;

class ContractsExportTest extends TestCase
{
    public function test_contracts_export_requires_date_range_and_format()
    {
        $this->withoutMiddleware();

        $response = $this->post(route('exports.contracts.export'), []);

        $response->assertSessionHasErrors([
            'format',
            'date_from',
            'date_to',
        ]);
    }

    public function test_contracts_export_returns_csv_payload()
    {
        $this->withoutMiddleware();

        $this->app->instance(ContractsExportService::class, new class extends ContractsExportService {
            public function rows(?string $dateFrom = null, ?string $dateTo = null): array
            {
                return [[
                    'naziv_partnerja' => 'ACME d.o.o.',
                    'naslov_partnerja' => 'Glavna cesta 1',
                    'davcna_stevilka_partnerja' => 'SI12345678',
                    'stevilka_pogodbe' => 12,
                    'datum_pogodbe' => '12.01.2026',
                    'znesek_provizije' => '123,45',
                ]];
            }
        });

        $response = $this->post(route('exports.contracts.export'), [
            'format' => 'csv',
            'date_from' => '2026-01-01',
            'date_to' => '2026-01-31',
        ]);

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Naziv_partnerja', $response->streamedContent());
        $this->assertStringContainsString('ACME d.o.o.', $response->streamedContent());
    }
}
