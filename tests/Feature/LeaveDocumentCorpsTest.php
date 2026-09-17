<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Personel;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Services\OrganizationStructureService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LeaveDocumentCorpsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->service = app(OrganizationStructureService::class);
        $this->seed(\Database\Seeders\OrganizationStructureSeeder::class);

        $this->admin = User::where('role', 'admin')->first();
        if (!$this->admin) {
            $this->admin = User::factory()->create(['role' => 'admin']);
        }

        // Ensure we have test personels for militer and pns
        Personel::firstOrCreate(
            ['nrp_nip' => '11000059490779'],
            [
                'nama' => 'Setyo Budi Nugroho, S.Sos.',
                'pangkat_golongan' => 'Kolonel',
                'corps' => 'Cke',
                'jabatan' => 'Kepala Bengpuskomlek',
                'satuan_bagian' => 'Kelompok Pimpinan',
                'jenis_personel' => 'militer',
                'status_aktif' => true,
            ]
        );

        Personel::firstOrCreate(
            ['nrp_nip' => '11960053320273'],
            [
                'nama' => 'M. Indra Hartanto',
                'pangkat_golongan' => 'Letkol',
                'corps' => 'Cke',
                'jabatan' => 'Wakil Kepala Bengpuskomlek',
                'satuan_bagian' => 'Kelompok Pimpinan',
                'jenis_personel' => 'militer',
                'status_aktif' => true,
            ]
        );

        Personel::firstOrCreate(
            ['nrp_nip' => '197109161994011001'],
            [
                'nama' => 'Usep Muljawan, S.T.',
                'pangkat_golongan' => 'III/d',
                'corps' => null,
                'jabatan' => 'Pranata Komputer Ahli Muda',
                'satuan_bagian' => 'Bagian Komunikasi',
                'jenis_personel' => 'pns',
                'status_aktif' => true,
            ]
        );
    }

    public function test_service_resolves_corps_correctly_from_nominatif()
    {
        $militer = Personel::where('nrp_nip', '11000059490779')->first();
        $pns = Personel::where('nrp_nip', '197109161994011001')->first();

        $this->assertNotNull($militer);
        $this->assertNotNull($pns);

        // Military should resolve to Cke
        $corpsMiliter = $this->service->resolveCorpsForPersonel($militer);
        $this->assertEquals('Cke', $corpsMiliter);

        // PNS should resolve to empty string
        $corpsPns = $this->service->resolveCorpsForPersonel($pns);
        $this->assertEquals('', $corpsPns);

        // Military by NRP string
        $corpsByMiliterNrp = $this->service->resolveCorpsForPersonel($militer->nrp_nip);
        $this->assertEquals('Cke', $corpsByMiliterNrp);

        // PNS by NIP string
        $corpsByPnsNip = $this->service->resolveCorpsForPersonel($pns->nrp_nip);
        $this->assertEquals('', $corpsByPnsNip);
    }

    public function test_signer_corps_is_resolved_from_nominatif_even_when_applicant_is_pns()
    {
        $wakaPersonel = Personel::where('nrp_nip', '11960053320273')->first();
        
        $wakilUnit = \App\Models\OrganizationUnit::where('name', 'WAKIL KEPALA')->first();
        if ($wakilUnit) {
            \App\Models\OrganizationOfficialAssignment::updateOrCreate(
                ['organization_unit_id' => $wakilUnit->id, 'is_active' => true],
                ['personel_id' => $wakaPersonel->id, 'role' => 'wakabeng', 'valid_from' => now()]
            );
        }

        $pns = Personel::where('nrp_nip', '197109161994011001')->first();
        $this->assertNotNull($pns);

        $user = User::factory()->create([
            'role' => 'user',
        ]);
        $pns->update(['user_id' => $user->id]);

        $leaveType = LeaveType::first();

        $leaveRequest = LeaveRequest::create([
            'request_number' => 'REQ-TEST-001',
            'user_id' => $user->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => now()->addDays(1)->format('Y-m-d'),
            'end_date' => now()->addDays(5)->format('Y-m-d'),
            'working_days_count' => 5,
            'reason' => 'Keperluan mendesak',
            'tujuan' => 'Bandung',
            'status' => 'approved',
        ]);

        // Issue official letter as Admin
        $response = $this->actingAs($this->admin)->post(route('admin.leave.issue_letter', $leaveRequest->id));
        $response->assertRedirect(route('admin.leave.show', $leaveRequest->id));

        $officialLetter = \App\Models\LeaveOfficialLetter::where('leave_request_id', $leaveRequest->id)->first();
        $this->assertNotNull($officialLetter);

        // Check snapshot data: signer corps must be Cke, not PNS empty/-
        $snapshot = $officialLetter->snapshot_data;
        $this->assertIsArray($snapshot);
        $this->assertEquals('Cke', $snapshot['penandatangan']['corps']);
        $this->assertNotEquals($snapshot['pemohon']['corps'], $snapshot['penandatangan']['corps']);

        // Inspect generated docx
        $filePath = storage_path('app/public/' . $officialLetter->file_path);
        $this->assertFileExists($filePath);

        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($filePath));
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        // Signer paragraph must contain Cke
        $this->assertStringContainsString('Cke', $xml);
    }
}
