<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserExcelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = base_path('daftarnamapegawaiptpelindo.xlsx');

        if (! file_exists($filePath)) {
            $this->command->warn("Excel file not found at: {$filePath}");
            return;
        }

        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        
        $isFirstRow = true;
        
        foreach ($worksheet->getRowIterator() as $row) {
            // Skip header row
            if ($isFirstRow) {
                $isFirstRow = false;
                continue;
            }

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            
            $data = [];
            foreach ($cellIterator as $cell) {
                $data[] = $cell->getValue();
            }
            
            // Kolom Excell: [0] => No, [1] => Nama Peminjam
            $name = trim($data[1] ?? '');
            
            if (! empty($name) && $name !== 'Nama Peminjam') {
                // Generate a clean email from the name
                $baseEmail = Str::slug($name, '.') . '@pelindo.co.id';
                $email = $baseEmail;
                
                // Add numeric counter if duplicate exists to avoid unique constraint errors
                $counter = 1;
                while (User::where('email', $email)->exists()) {
                    $email = Str::slug($name, '.') . $counter . '@pelindo.co.id';
                    $counter++;
                }

                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make('pelindo123'),
                    'email_verified_at' => now(),
                ]);

                // Assumes 'user' role was mapped out in RoleSeeder
                if (\Spatie\Permission\Models\Role::where('name', 'user')->exists()) {
                    $user->assignRole('user');
                }
            }
        }
        
        $this->command->info('Successfully seeded real employees from the Excel file!');
    }
}
