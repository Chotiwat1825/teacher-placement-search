<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Keep for truncate if needed
use App\Models\EducationalArea; // Import model

class EducationalAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Consider truncating the table if you want a fresh start with only the new 245 areas
        // EducationalArea::truncate();
        // Or if you prefer DB facade and want to reset auto-increment (for MySQL):
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // DB::table('educational_areas')->truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Define province abbreviations map
        $provinceAbbr = [
            'กระบี่' => 'กบ',
            'กรุงเทพมหานคร' => 'กทม',
            'กาญจนบุรี' => 'กจ',
            'กาฬสินธุ์' => 'กส',
            'กำแพงเพชร' => 'กพ',
            'ขอนแก่น' => 'ขก',
            'จันทบุรี' => 'จบ',
            'ฉะเชิงเทรา' => 'ฉช',
            'ชลบุรี' => 'ชบ',
            'ชัยนาท' => 'ชน',
            'ชัยภูมิ' => 'ชย',
            'ชุมพร' => 'ชพ',
            'เชียงราย' => 'ชร',
            'เชียงใหม่' => 'ชม',
            'ตรัง' => 'ตง',
            'ตราด' => 'ตร', // ตราด เขต 1, make sure it's just 'ตร'
            'ตาก' => 'ตก',
            'นครนายก' => 'นย',
            'นครปฐม' => 'นฐ',
            'นครพนม' => 'นพ',
            'นครราชสีมา' => 'นม',
            'นครศรีธรรมราช' => 'นศ',
            'นครสวรรค์' => 'นว',
            'นนทบุรี' => 'นบ',
            'นราธิวาส' => 'นธ',
            'น่าน' => 'นน',
            'บุรีรัมย์' => 'บร',
            'ปทุมธานี' => 'ปท',
            'ประจวบคีรีขันธ์' => 'ปข',
            'ปราจีนบุรี' => 'ปจ',
            'ปัตตานี' => 'ปน',
            'พระนครศรีอยุธยา' => 'อย',
            'พะเยา' => 'พย',
            'พังงา' => 'พง',
            'พัทลุง' => 'พท',
            'พิจิตร' => 'พจ',
            'พิษณุโลก' => 'พล',
            'เพชรบุรี' => 'พบ',
            'เพชรบูรณ์' => 'พช',
            'แพร่' => 'พร',
            'ภูเก็ต' => 'ภก',
            'มหาสารคาม' => 'มค',
            'มุกดาหาร' => 'มห',
            'แม่ฮ่องสอน' => 'มส',
            'ยโสธร' => 'ยส',
            'ยะลา' => 'ยล',
            'ร้อยเอ็ด' => 'รอ',
            'ระนอง' => 'รน',
            'ระยอง' => 'รย',
            'ราชบุรี' => 'รบ',
            'ลพบุรี' => 'ลบ',
            'ลำปาง' => 'ลป',
            'ลำพูน' => 'ลพ',
            'เลย' => 'ลย',
            'ศรีสะเกษ' => 'ศก',
            'สกลนคร' => 'สน',
            'สงขลา' => 'สข',
            'สตูล' => 'สต',
            'สมุทรปราการ' => 'สป',
            'สมุทรสงคราม' => 'สส',
            'สมุทรสาคร' => 'สค',
            'สระแก้ว' => 'สก',
            'สระบุรี' => 'สบ',
            'สิงห์บุรี' => 'สห',
            'สุโขทัย' => 'สท',
            'สุพรรณบุรี' => 'สพ',
            'สุราษฎร์ธานี' => 'สฎ',
            'สุรินทร์' => 'สร',
            'หนองคาย' => 'นค',
            'หนองบัวลำภู' => 'นภ',
            'อ่างทอง' => 'อท',
            'อำนาจเจริญ' => 'อจ',
            'อุดรธานี' => 'อด',
            'อุตรดิตถ์' => 'อต',
            'อุทัยธานี' => 'อน',
            'อุบลราชธานี' => 'อบ',
            'บึงกาฬ' => 'บก',
            // Special cases for single word areas in สพป
            'ตราด เขต 1' => 'ตร', // Overriding 'ตราด' for this specific name
        ];

        $new_areas_input = [
            // Pasted list of 245 areas will go here
            'สพป.กระบี่',
            'สพป.กรุงเทพมหานคร',
            'สพป.กาญจนบุรี เขต 1',
            'สพป.กาญจนบุรี เขต 2',
            'สพป.กาญจนบุรี เขต 3',
            'สพป.กาญจนบุรี เขต 4',
            'สพป.กาฬสินธุ์ เขต 1',
            'สพป.กาฬสินธุ์ เขต 2',
            'สพป.กาฬสินธุ์ เขต 3',
            'สพป.กำแพงเพชร เขต 1',
            'สพป.กำแพงเพชร เขต 2',
            'สพป.ขอนแก่น เขต 1',
            'สพป.ขอนแก่น เขต 2',
            'สพป.ขอนแก่น เขต 3',
            'สพป.ขอนแก่น เขต 4',
            'สพป.ขอนแก่น เขต 5',
            'สพป.จันทบุรี เขต 1',
            'สพป.จันทบุรี เขต 2',
            'สพป.ฉะเชิงเทรา เขต 1',
            'สพป.ฉะเชิงเทรา เขต 2',
            'สพป.ชลบุรี เขต 1',
            'สพป.ชลบุรี เขต 2',
            'สพป.ชลบุรี เขต 3',
            'สพป.ชัยนาท',
            'สพป.ชัยภูมิ เขต 1',
            'สพป.ชัยภูมิ เขต 2',
            'สพป.ชัยภูมิ เขต 3',
            'สพป.ชุมพร เขต 1',
            'สพป.ชุมพร เขต 2',
            'สพป.เชียงราย เขต 1',
            'สพป.เชียงราย เขต 2',
            'สพป.เชียงราย เขต 3',
            'สพป.เชียงราย เขต 4',
            'สพป.เชียงใหม่ เขต 1',
            'สพป.เชียงใหม่ เขต 2',
            'สพป.เชียงใหม่ เขต 3',
            'สพป.เชียงใหม่ เขต 4',
            'สพป.เชียงใหม่ เขต 5',
            'สพป.เชียงใหม่ เขต 6',
            'สพป.ตรัง เขต 1',
            'สพป.ตรัง เขต 2',
            'สพป.ตราด เขต 1',
            'สพป.ตาก เขต 1',
            'สพป.ตาก เขต 2',
            'สพป.นครนายก',
            'สพป.นครปฐม เขต 1',
            'สพป.นครปฐม เขต 2',
            'สพป.นครพนม เขต 1',
            'สพป.นครพนม เขต 2',
            'สพป.นครราชสีมา เขต 1',
            'สพป.นครราชสีมา เขต 2',
            'สพป.นครราชสีมา เขต 3',
            'สพป.นครราชสีมา เขต 4',
            'สพป.นครราชสีมา เขต 5',
            'สพป.นครราชสีมา เขต 6',
            'สพป.นครราชสีมา เขต 7',
            'สพป.นครศรีธรรมราช เขต 1',
            'สพป.นครศรีธรรมราช เขต 2',
            'สพป.นครศรีธรรมราช เขต 3',
            'สพป.นครศรีธรรมราช เขต 4',
            'สพป.นครสวรรค์ เขต 1',
            'สพป.นครสวรรค์ เขต 2',
            'สพป.นครสวรรค์ เขต 3',
            'สพป.นนทบุรี เขต 1',
            'สพป.นนทบุรี เขต 2',
            'สพป.นราธิวาส เขต 1',
            'สพป.นราธิวาส เขต 2',
            'สพป.นราธิวาส เขต 3',
            'สพป.น่าน เขต 1',
            'สพป.น่าน เขต 2',
            'สพป.บุรีรัมย์ เขต 1',
            'สพป.บุรีรัมย์ เขต 2',
            'สพป.บุรีรัมย์ เขต 3',
            'สพป.บุรีรัมย์ เขต 4',
            'สพป.ปทุมธานี เขต 1',
            'สพป.ปทุมธานี เขต 2',
            'สพป.ประจวบคีรีขันธ์ เขต 1',
            'สพป.ประจวบคีรีขันธ์ เขต 2',
            'สพป.ปราจีนบุรี เขต 1',
            'สพป.ปราจีนบุรี เขต 2',
            'สพป.ปัตตานี เขต 1',
            'สพป.ปัตตานี เขต 2',
            'สพป.ปัตตานี เขต 3',
            'สพป.พระนครศรีอยุธยา เขต 1',
            'สพป.พระนครศรีอยุธยา เขต 2',
            'สพป.พะเยา เขต 1',
            'สพป.พะเยา เขต 2',
            'สพป.พังงา',
            'สพป.พัทลุง เขต 1',
            'สพป.พัทลุง เขต 2',
            'สพป.พิจิตร เขต 1',
            'สพป.พิจิตร เขต 2',
            'สพป.พิษณุโลก เขต 1',
            'สพป.พิษณุโลก เขต 2',
            'สพป.พิษณุโลก เขต 3',
            'สพป.เพชรบุรี เขต 1',
            'สพป.เพชรบุรี เขต 2',
            'สพป.เพชรบูรณ์ เขต 1',
            'สพป.เพชรบูรณ์ เขต 2',
            'สพป.เพชรบูรณ์ เขต 3',
            'สพป.แพร่ เขต 1',
            'สพป.แพร่ เขต 2',
            'สพป.ภูเก็ต',
            'สพป.มหาสารคาม เขต 1',
            'สพป.มหาสารคาม เขต 2',
            'สพป.มหาสารคาม เขต 3',
            'สพป.มุกดาหาร',
            'สพป.แม่ฮ่องสอน เขต 1',
            'สพป.แม่ฮ่องสอน เขต 2',
            'สพป.ยโสธร เขต 1',
            'สพป.ยโสธร เขต 2',
            'สพป.ยะลา เขต 1',
            'สพป.ยะลา เขต 2',
            'สพป.ยะลา เขต 3',
            'สพป.ร้อยเอ็ด เขต 1',
            'สพป.ร้อยเอ็ด เขต 2',
            'สพป.ร้อยเอ็ด เขต 3',
            'สพป.ระนอง',
            'สพป.ระยอง เขต 1',
            'สพป.ระยอง เขต 2',
            'สพป.ราชบุรี เขต 1',
            'สพป.ราชบุรี เขต 2',
            'สพป.ลพบุรี เขต 1',
            'สพป.ลพบุรี เขต 2',
            'สพป.ลำปาง เขต 1',
            'สพป.ลำปาง เขต 2',
            'สพป.ลำปาง เขต 3',
            'สพป.ลำพูน เขต 1',
            'สพป.ลำพูน เขต 2',
            'สพป.เลย เขต 1',
            'สพป.เลย เขต 2',
            'สพป.เลย เขต 3',
            'สพป.ศรีสะเกษ เขต 1',
            'สพป.ศรีสะเกษ เขต 2',
            'สพป.ศรีสะเกษ เขต 3',
            'สพป.ศรีสะเกษ เขต 4',
            'สพป.สกลนคร เขต 1',
            'สพป.สกลนคร เขต 2',
            'สพป.สกลนคร เขต 3',
            'สพป.สงขลา เขต 1',
            'สพป.สงขลา เขต 2',
            'สพป.สงขลา เขต 3',
            'สพป.สตูล',
            'สพป.สมุทรปราการ เขต 1',
            'สพป.สมุทรปราการ เขต 2',
            'สพป.สมุทรสงคราม',
            'สพป.สมุทรสาคร',
            'สพป.สระแก้ว เขต 1',
            'สพป.สระแก้ว เขต 2',
            'สพป.สระบุรี เขต 1',
            'สพป.สระบุรี เขต 2',
            'สพป.สิงห์บุรี',
            'สพป.สุโขทัย เขต 1',
            'สพป.สุโขทัย เขต 2',
            'สพป.สุพรรณบุรี เขต 1',
            'สพป.สุพรรณบุรี เขต 2',
            'สพป.สุพรรณบุรี เขต 3',
            'สพป.สุราษฎร์ธานี เขต 1',
            'สพป.สุราษฎร์ธานี เขต 2',
            'สพป.สุราษฎร์ธานี เขต 3',
            'สพป.สุรินทร์ เขต 1',
            'สพป.สุรินทร์ เขต 2',
            'สพป.สุรินทร์ เขต 3',
            'สพป.หนองคาย เขต 1',
            'สพป.หนองคาย เขต 2',
            'สพป.หนองบัวลำภู เขต 1',
            'สพป.หนองบัวลำภู เขต 2',
            'สพป.อ่างทอง',
            'สพป.อำนาจเจริญ',
            'สพป.อุดรธานี เขต 1',
            'สพป.อุดรธานี เขต 2',
            'สพป.อุดรธานี เขต 3',
            'สพป.อุดรธานี เขต 4',
            'สพป.อุตรดิตถ์ เขต 1',
            'สพป.อุตรดิตถ์ เขต 2',
            'สพป.อุทัยธานี เขต 1',
            'สพป.อุทัยธานี เขต 2',
            'สพป.อุบลราชธานี เขต 1',
            'สพป.อุบลราชธานี เขต 2',
            'สพป.อุบลราชธานี เขต 3',
            'สพป.อุบลราชธานี เขต 4',
            'สพป.อุบลราชธานี เขต 5',
            'สพป.บึงกาฬ',
            'สพม.กรุงเทพมหานคร เขต 1',
            'สพม.กรุงเทพมหานคร เขต 2',
            'สพม.นนทบุรี',
            'สพม.ปทุมธานี',
            'สพม.สิงห์บุรี อ่างทอง',
            'สพม.ฉะเชิงเทรา',
            'สพม.ปราจีนบุรี นครนายก',
            'สพม.ราชบุรี',
            'สพม.สุพรรณบุรี',
            'สพม.เพชรบุรี',
            'สพม.สุราษฎร์ธานี ชุมพร',
            'สพม.นครศรีธรรมราช',
            'สพม.ตรัง กระบี่',
            'สพม.พังงา ภูเก็ต ระนอง',
            'สพม.นราธิวาส',
            'สพม.สงขลา สตูล',
            'สพม.จันทบุรี ตราด',
            'สพม.ชลบุรี ระยอง',
            'สพม.เลย หนองบัวลำภู',
            'สพม.อุดรธานี',
            'สพม.หนองคาย',
            'สพม.นครพนม',
            'สพม.สกลนคร',
            'สพม.กาฬสินธุ์',
            'สพม.ขอนแก่น',
            'สพม.มหาสารคาม',
            'สพม.ร้อยเอ็ด',
            'สพม.ศรีสะเกษ ยโสธร',
            'สพม.อุบลราชธานี อำนาจเจริญ',
            'สพม.ชัยภูมิ',
            'สพม.นครราชสีมา',
            'สพม.บุรีรัมย์',
            'สพม.สุรินทร์',
            'สพม.เชียงใหม่',
            'สพม.ลำปาง ลำพูน',
            'สพม.เชียงราย',
            'สพม.แพร่',
            'สพม.สุโขทัย',
            'สพม.พิษณุโลก อุตรดิตถ์',
            'สพม.เพชรบูรณ์',
            'สพม.กำแพงเพชร',
            'สพม.นครสวรรค์',
            'สพม.กาญจนบุรี',
            'สพม.พระนครศรีอยุธยา',
            'สพม.สระบุรี',
            'สพม.สมุทรปราการ',
            'สพม.สระแก้ว',
            'สพม.นครปฐม',
            'สพม.ประจวบคีรีขันธ์',
            'สพม.สมุทรสาคร สมุทรสงคราม',
            'สพม.พัทลุง',
            'สพม.ปัตตานี',
            'สพม.ยะลา',
            'สพม.บึงกาฬ',
            'สพม.มุกดาหาร',
            'สพม.แม่ฮ่องสอน',
            'สพม.พะเยา',
            'สพม.น่าน',
            'สพม.ตาก',
            'สพม.พิจิตร',
            'สพม.อุทัยธานี ชัยนาท',
            'สพม.ลพบุรี',
        ];

        $areas_to_seed = [];

        foreach ($new_areas_input as $fullName) {
            $originalFullName = $fullName; // Keep original for map lookup if needed
            $type = '';
            $code = '';
            $districtNumber = '';
            $areaNamePart = '';

            if (strpos($fullName, 'สพป.') === 0) {
                $type = 'primary';
                $areaNamePart = trim(str_replace('สพป.', '', $fullName));
            } elseif (strpos($fullName, 'สพม.') === 0) {
                $type = 'secondary';
                $areaNamePart = trim(str_replace('สพม.', '', $fullName));
            }

            // Extract district number if present (e.g., "เขต 1")
            if (preg_match('/เขต\s*(\d+)$/', $areaNamePart, $matches)) {
                $districtNumber = $matches[1];
                $provincePart = trim(preg_replace('/\s*เขต\s*\d+$/', '', $areaNamePart));
            } else {
                $provincePart = $areaNamePart;
            }

            // Generate code
            $provinceNames = preg_split('/\s+/', $provincePart); // Split by space for multi-province names
            $codeParts = [];

            foreach ($provinceNames as $pName) {
                if (isset($provinceAbbr[$pName])) {
                    $codeParts[] = $provinceAbbr[$pName];
                } else {
                    // Fallback for names not in map (e.g. if map is incomplete)
                    // This is a simple fallback, might need refinement for Thai characters
                    $codeParts[] = mb_substr($pName, 0, 2, 'UTF-8');
                }
            }

            if ($type === 'primary') {
                // For สพป. 'ตราด เขต 1', we want 'ตร' not 'ตร1' based on current setup
                // For others, if 'เขต 1' exists, it should be e.g. 'กจ1'
                if ($originalFullName === 'สพป.ตราด เขต 1') {
                    $baseCode = $provinceAbbr['ตราด เขต 1']; // Use specific map entry
                } else {
                    $baseCode = implode('', $codeParts);
                }

                $code = $baseCode . $districtNumber;
            } elseif ($type === 'secondary') {
                $baseCode = implode('-', $codeParts);
                $code = 'สพม.' . $baseCode . $districtNumber; // districtNumber usually empty for สพม in new list, except กทม
            }

            $areas_to_seed[] = [
                'name' => $originalFullName,
                'code' => $code,
                'type' => $type,
            ];
        }

        foreach ($areas_to_seed as $area) {
            EducationalArea::firstOrCreate(
                ['name' => $area['name']], // Key to check for existence
                [
                    // Values to insert or update
                    'code' => $area['code'],
                    'type' => $area['type'],
                    // 'created_at' and 'updated_at' will be handled automatically
                ],
            );
        }

        // You can add a success message to the console
        // $this->command->info('Educational areas seeded successfully with 245 entries.');
    }
}
