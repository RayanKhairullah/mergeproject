<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // We make sure divisions exist or fallback
        $divDireksi = Division::firstOrCreate(['name' => 'Direksi', 'slug' => 'direksi']);
        $divIT = Division::firstOrCreate(['name' => 'Teknologi Informasi', 'slug' => 'teknologi-informasi']);
        $divKeuangan = Division::firstOrCreate(['name' => 'Keuangan', 'slug' => 'keuangan']);
        $divSDM = Division::firstOrCreate(['name' => 'Sumber Daya Manusia (SDM)', 'slug' => 'sdm']);
        $divOps = Division::firstOrCreate(['name' => 'Operasional', 'slug' => 'operasional']);

        // Seed Org Sections
        $secDireksi = \App\Models\OrgSection::firstOrCreate(['name' => 'Direksi Utama'], [
            'display_mode' => 'tree',
            'order' => 1,
        ]);

        $secOrganik = \App\Models\OrgSection::firstOrCreate(['name' => 'Pegawai Organik'], [
            'display_mode' => 'table',
            'order' => 2,
            'table_columns' => [
                ['header' => 'Nama Karyawan', 'field' => 'name'],
                ['header' => 'Nomor Pegawai', 'field' => 'nip'],
                ['header' => 'Jabatan', 'field' => 'position'],
                ['header' => 'Grade / Golongan', 'field' => 'grade'],
            ]
        ]);

        $secNonOrganik = \App\Models\OrgSection::firstOrCreate(['name' => 'Pegawai Non Organik'], [
            'display_mode' => 'table',
            'order' => 3,
            'table_columns' => [
                ['header' => 'Nama PIC', 'field' => 'name'],
                ['header' => 'Posisi', 'field' => 'position'],
                ['header' => 'Instansi Asal', 'field' => 'instansi'],
                ['header' => 'Tanggal Akhir Kontrak', 'field' => 'contract_end'],
            ]
        ]);

        Employee::query()->delete();

        // --- 1. HIERARCHY TREE (13 Employees) ---
        // Level 1: 1 Person
        $dirut = Employee::create([
            'parent_id' => null,
            'org_section_id' => $secDireksi->id,
            'division_id' => $divDireksi->id,
            'name' => 'Drs. Budi Santoso, MBA',
            'nip' => '10001',
            'gender' => 'male',
            'position' => 'Direktur Utama',
            'order' => 1,
        ]);

        // Level 2: 2 Directors
        $dirOps = Employee::create([
            'parent_id' => $dirut->id,
            'org_section_id' => $secDireksi->id,
            'division_id' => $divOps->id,
            'name' => 'Ir. Anita Wibowo, M.Sc',
            'nip' => '10002',
            'gender' => 'female',
            'position' => 'Direktur Operasional',
            'order' => 1,
        ]);

        $dirKeuangan = Employee::create([
            'parent_id' => $dirut->id,
            'org_section_id' => $secDireksi->id,
            'division_id' => $divKeuangan->id,
            'name' => 'Michael Salim, M.Fin',
            'nip' => '10003',
            'gender' => 'male',
            'position' => 'Direktur Keuangan & SDM',
            'order' => 2,
        ]);

        // Level 3: 2 VPs under Ops
        $vpOpsNames = ['Joko Priyono', 'Sarah Aulia'];
        foreach ($vpOpsNames as $index => $name) {
            Employee::create([
                'parent_id' => $dirOps->id,
                'org_section_id' => $secDireksi->id,
                'division_id' => $divOps->id,
                'name' => $name,
                'nip' => '200' . ($index + 1),
                'gender' => $index == 1 ? 'female' : 'male',
                'position' => 'VP Operasional ' . ($index + 1),
                'order' => $index + 1,
            ]);
        }

        // Level 3: 2 VPs under Keuangan
        $vpKeuanganNames = ['Andi Gunawan', 'Diana Kusuma'];
        foreach ($vpKeuanganNames as $index => $name) {
            Employee::create([
                'parent_id' => $dirKeuangan->id,
                'org_section_id' => $secDireksi->id,
                'division_id' => $divKeuangan->id,
                'name' => $name,
                'nip' => '300' . ($index + 1),
                'gender' => $index == 1 ? 'female' : 'male',
                'position' => 'VP Keuangan ' . ($index + 1),
                'order' => $index + 1,
            ]);
        }

        // Total Direksi = 1 + 2 + 2 + 2 = 7

        // --- 2. PEGAWAI ORGANIK (10 Employees) ---
        $jabatanOrganik = [
            'Manager Akuntansi', 'IT Specialist', 'Senior HR Staff', 'Senior Auditor', 
            'Legal Officer', 'Operational Supervisor', 'Corporate Secretary Staff', 
            'Finance Analyst', 'System Administrator', 'Recruitment Specialist'
        ];
        
        for ($i = 1; $i <= 10; $i++) {
            $gender = $faker->randomElement(['male', 'female']);
            Employee::create([
                'parent_id' => null, // Attached to none in tree since they are table-based
                'org_section_id' => $secOrganik->id,
                'division_id' => $faker->randomElement([$divIT->id, $divKeuangan->id, $divSDM->id, $divOps->id]),
                'name' => $faker->name($gender),
                'nip' => '500' . str_pad((string)$i, 2, '0', STR_PAD_LEFT),
                'gender' => $gender,
                'position' => $jabatanOrganik[$i - 1],
                'order' => $i,
                'custom_fields' => [
                    'grade' => $faker->randomElement(['Staff', 'Junior Management', 'Middle Management']),
                ],
            ]);
        }

        // --- 3. PEGAWAI NON ORGANIK (10 Employees) ---
        $jabatanNonOrganik = [
            'Security Officer', 'Cleaning Staff', 'Outsource Developer', 'Driver', 
            'Receptionist', 'Data Entry', 'Call Center Agent', 'Technician', 
            'Office Boy', 'Warehouse Staff'
        ];
        $instansiList = ['PT Karya Sejahtera', 'PT Outsourcing Mandiri', 'CV Solusi Cepat'];

        for ($i = 1; $i <= 10; $i++) {
            $gender = $faker->randomElement(['male', 'female']);
            Employee::create([
                'parent_id' => null,
                'org_section_id' => $secNonOrganik->id,
                'division_id' => $faker->randomElement([$divIT->id, $divKeuangan->id, $divSDM->id, $divOps->id]),
                'name' => $faker->name($gender),
                'nip' => null,
                'gender' => $gender,
                'position' => $jabatanNonOrganik[$i - 1],
                'order' => $i,
                'custom_fields' => [
                    'instansi' => $faker->randomElement($instansiList),
                    'contract_end' => $faker->dateTimeBetween('now', '+2 years')->format('Y-m-d'),
                ],
            ]);
        }
    }
}
