<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\EducationalArea; // Import model

class EducationalAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ล้างข้อมูลเก่า (ถ้าต้องการ)
        // DB::table('educational_areas')->delete();

        $areas = [
            // สพป. (สำนักงานเขตพื้นที่การศึกษาประถมศึกษา) - ตัวอย่าง
            ['name' => 'สพป.กรุงเทพมหานคร', 'code' => 'กทม', 'type' => 'primary'],
            ['name' => 'สพป.กระบี่', 'code' => 'กบ', 'type' => 'primary'],
            ['name' => 'สพป.กาญจนบุรี เขต 1', 'code' => 'กจ1', 'type' => 'primary'],
            ['name' => 'สพป.กาญจนบุรี เขต 2', 'code' => 'กจ2', 'type' => 'primary'],
            ['name' => 'สพป.กาญจนบุรี เขต 3', 'code' => 'กจ3', 'type' => 'primary'],
            ['name' => 'สพป.กาญจนบุรี เขต 4', 'code' => 'กจ4', 'type' => 'primary'],
            ['name' => 'สพป.กาฬสินธุ์ เขต 1', 'code' => 'กส1', 'type' => 'primary'],
            ['name' => 'สพป.กาฬสินธุ์ เขต 2', 'code' => 'กส2', 'type' => 'primary'],
            ['name' => 'สพป.กาฬสินธุ์ เขต 3', 'code' => 'กส3', 'type' => 'primary'],
            ['name' => 'สพป.กำแพงเพชร เขต 1', 'code' => 'กพ1', 'type' => 'primary'],
            ['name' => 'สพป.กำแพงเพชร เขต 2', 'code' => 'กพ2', 'type' => 'primary'],
            ['name' => 'สพป.ขอนแก่น เขต 1', 'code' => 'ขก1', 'type' => 'primary'],
            ['name' => 'สพป.ขอนแก่น เขต 2', 'code' => 'ขก2', 'type' => 'primary'],
            ['name' => 'สพป.ขอนแก่น เขต 3', 'code' => 'ขก3', 'type' => 'primary'],
            ['name' => 'สพป.ขอนแก่น เขต 4', 'code' => 'ขก4', 'type' => 'primary'],
            ['name' => 'สพป.ขอนแก่น เขต 5', 'code' => 'ขก5', 'type' => 'primary'],
            ['name' => 'สพป.จันทบุรี เขต 1', 'code' => 'จบ1', 'type' => 'primary'],
            ['name' => 'สพป.จันทบุรี เขต 2', 'code' => 'จบ2', 'type' => 'primary'],
            ['name' => 'สพป.ฉะเชิงเทรา เขต 1', 'code' => 'ฉช1', 'type' => 'primary'],
            ['name' => 'สพป.ฉะเชิงเทรา เขต 2', 'code' => 'ฉช2', 'type' => 'primary'],
            ['name' => 'สพป.ชลบุรี เขต 1', 'code' => 'ชบ1', 'type' => 'primary'],
            ['name' => 'สพป.ชลบุรี เขต 2', 'code' => 'ชบ2', 'type' => 'primary'],
            ['name' => 'สพป.ชลบุรี เขต 3', 'code' => 'ชบ3', 'type' => 'primary'],
            ['name' => 'สพป.ชัยนาท', 'code' => 'ชน', 'type' => 'primary'],
            ['name' => 'สพป.ชัยภูมิ เขต 1', 'code' => 'ชย1', 'type' => 'primary'],
            ['name' => 'สพป.ชัยภูมิ เขต 2', 'code' => 'ชย2', 'type' => 'primary'],
            ['name' => 'สพป.ชัยภูมิ เขต 3', 'code' => 'ชย3', 'type' => 'primary'],
            ['name' => 'สพป.ชุมพร เขต 1', 'code' => 'ชพ1', 'type' => 'primary'],
            ['name' => 'สพป.ชุมพร เขต 2', 'code' => 'ชพ2', 'type' => 'primary'],
            ['name' => 'สพป.เชียงราย เขต 1', 'code' => 'ชร1', 'type' => 'primary'],
            ['name' => 'สพป.เชียงราย เขต 2', 'code' => 'ชร2', 'type' => 'primary'],
            ['name' => 'สพป.เชียงราย เขต 3', 'code' => 'ชร3', 'type' => 'primary'],
            ['name' => 'สพป.เชียงราย เขต 4', 'code' => 'ชร4', 'type' => 'primary'],
            ['name' => 'สพป.เชียงใหม่ เขต 1', 'code' => 'ชม1', 'type' => 'primary'],
            ['name' => 'สพป.เชียงใหม่ เขต 2', 'code' => 'ชม2', 'type' => 'primary'],
            ['name' => 'สพป.เชียงใหม่ เขต 3', 'code' => 'ชม3', 'type' => 'primary'],
            ['name' => 'สพป.เชียงใหม่ เขต 4', 'code' => 'ชม4', 'type' => 'primary'],
            ['name' => 'สพป.เชียงใหม่ เขต 5', 'code' => 'ชม5', 'type' => 'primary'],
            ['name' => 'สพป.เชียงใหม่ เขต 6', 'code' => 'ชม6', 'type' => 'primary'],
            ['name' => 'สพป.ตรัง เขต 1', 'code' => 'ตง1', 'type' => 'primary'],
            ['name' => 'สพป.ตรัง เขต 2', 'code' => 'ตง2', 'type' => 'primary'],
            ['name' => 'สพป.ตราด', 'code' => 'ตร', 'type' => 'primary'],
            ['name' => 'สพป.ตาก เขต 1', 'code' => 'ตก1', 'type' => 'primary'],
            ['name' => 'สพป.ตาก เขต 2', 'code' => 'ตก2', 'type' => 'primary'],
            ['name' => 'สพป.นครนายก', 'code' => 'นย', 'type' => 'primary'],
            ['name' => 'สพป.นครปฐม เขต 1', 'code' => 'นฐ1', 'type' => 'primary'],
            ['name' => 'สพป.นครปฐม เขต 2', 'code' => 'นฐ2', 'type' => 'primary'],
            ['name' => 'สพป.นครพนม เขต 1', 'code' => 'นพ1', 'type' => 'primary'],
            ['name' => 'สพป.นครพนม เขต 2', 'code' => 'นพ2', 'type' => 'primary'],
            ['name' => 'สพป.นครราชสีมา เขต 1', 'code' => 'นม1', 'type' => 'primary'],
            ['name' => 'สพป.นครราชสีมา เขต 2', 'code' => 'นม2', 'type' => 'primary'],
            ['name' => 'สพป.นครราชสีมา เขต 3', 'code' => 'นม3', 'type' => 'primary'],
            ['name' => 'สพป.นครราชสีมา เขต 4', 'code' => 'นม4', 'type' => 'primary'],
            ['name' => 'สพป.นครราชสีมา เขต 5', 'code' => 'นม5', 'type' => 'primary'],
            ['name' => 'สพป.นครราชสีมา เขต 6', 'code' => 'นม6', 'type' => 'primary'],
            ['name' => 'สพป.นครราชสีมา เขต 7', 'code' => 'นม7', 'type' => 'primary'],
            ['name' => 'สพป.นครศรีธรรมราช เขต 1', 'code' => 'นศ1', 'type' => 'primary'],
            ['name' => 'สพป.นครศรีธรรมราช เขต 2', 'code' => 'นศ2', 'type' => 'primary'],
            ['name' => 'สพป.นครศรีธรรมราช เขต 3', 'code' => 'นศ3', 'type' => 'primary'],
            ['name' => 'สพป.นครศรีธรรมราช เขต 4', 'code' => 'นศ4', 'type' => 'primary'],
            ['name' => 'สพป.นครสวรรค์ เขต 1', 'code' => 'นว1', 'type' => 'primary'],
            ['name' => 'สพป.นครสวรรค์ เขต 2', 'code' => 'นว2', 'type' => 'primary'],
            ['name' => 'สพป.นครสวรรค์ เขต 3', 'code' => 'นว3', 'type' => 'primary'],
            ['name' => 'สพป.นนทบุรี เขต 1', 'code' => 'นบ1', 'type' => 'primary'],
            ['name' => 'สพป.นนทบุรี เขต 2', 'code' => 'นบ2', 'type' => 'primary'],
            ['name' => 'สพป.นราธิวาส เขต 1', 'code' => 'นธ1', 'type' => 'primary'],
            ['name' => 'สพป.นราธิวาส เขต 2', 'code' => 'นธ2', 'type' => 'primary'],
            ['name' => 'สพป.นราธิวาส เขต 3', 'code' => 'นธ3', 'type' => 'primary'],
            ['name' => 'สพป.น่าน เขต 1', 'code' => 'นน1', 'type' => 'primary'],
            ['name' => 'สพป.น่าน เขต 2', 'code' => 'นน2', 'type' => 'primary'],
            ['name' => 'สพป.บึงกาฬ', 'code' => 'บก', 'type' => 'primary'],
            ['name' => 'สพป.บุรีรัมย์ เขต 1', 'code' => 'บร1', 'type' => 'primary'],
            ['name' => 'สพป.บุรีรัมย์ เขต 2', 'code' => 'บร2', 'type' => 'primary'],
            ['name' => 'สพป.บุรีรัมย์ เขต 3', 'code' => 'บร3', 'type' => 'primary'],
            ['name' => 'สพป.บุรีรัมย์ เขต 4', 'code' => 'บร4', 'type' => 'primary'],
            ['name' => 'สพป.ปทุมธานี เขต 1', 'code' => 'ปท1', 'type' => 'primary'],
            ['name' => 'สพป.ปทุมธานี เขต 2', 'code' => 'ปท2', 'type' => 'primary'],
            ['name' => 'สพป.ประจวบคีรีขันธ์ เขต 1', 'code' => 'ปข1', 'type' => 'primary'],
            ['name' => 'สพป.ประจวบคีรีขันธ์ เขต 2', 'code' => 'ปข2', 'type' => 'primary'],
            ['name' => 'สพป.ปราจีนบุรี เขต 1', 'code' => 'ปจ1', 'type' => 'primary'],
            ['name' => 'สพป.ปราจีนบุรี เขต 2', 'code' => 'ปจ2', 'type' => 'primary'],
            ['name' => 'สพป.ปัตตานี เขต 1', 'code' => 'ปน1', 'type' => 'primary'],
            ['name' => 'สพป.ปัตตานี เขต 2', 'code' => 'ปน2', 'type' => 'primary'],
            ['name' => 'สพป.ปัตตานี เขต 3', 'code' => 'ปน3', 'type' => 'primary'],
            ['name' => 'สพป.พระนครศรีอยุธยา เขต 1', 'code' => 'อย1', 'type' => 'primary'],
            ['name' => 'สพป.พระนครศรีอยุธยา เขต 2', 'code' => 'อย2', 'type' => 'primary'],
            ['name' => 'สพป.พะเยา เขต 1', 'code' => 'พย1', 'type' => 'primary'],
            ['name' => 'สพป.พะเยา เขต 2', 'code' => 'พย2', 'type' => 'primary'],
            ['name' => 'สพป.พังงา', 'code' => 'พง', 'type' => 'primary'],
            ['name' => 'สพป.พัทลุง เขต 1', 'code' => 'พท1', 'type' => 'primary'],
            ['name' => 'สพป.พัทลุง เขต 2', 'code' => 'พท2', 'type' => 'primary'],
            ['name' => 'สพป.พิจิตร เขต 1', 'code' => 'พจ1', 'type' => 'primary'],
            ['name' => 'สพป.พิจิตร เขต 2', 'code' => 'พจ2', 'type' => 'primary'],
            ['name' => 'สพป.พิษณุโลก เขต 1', 'code' => 'พล1', 'type' => 'primary'],
            ['name' => 'สพป.พิษณุโลก เขต 2', 'code' => 'พล2', 'type' => 'primary'],
            ['name' => 'สพป.พิษณุโลก เขต 3', 'code' => 'พล3', 'type' => 'primary'],
            ['name' => 'สพป.เพชรบุรี เขต 1', 'code' => 'พบ1', 'type' => 'primary'],
            ['name' => 'สพป.เพชรบุรี เขต 2', 'code' => 'พบ2', 'type' => 'primary'],
            ['name' => 'สพป.เพชรบูรณ์ เขต 1', 'code' => 'พช1', 'type' => 'primary'],
            ['name' => 'สพป.เพชรบูรณ์ เขต 2', 'code' => 'พช2', 'type' => 'primary'],
            ['name' => 'สพป.เพชรบูรณ์ เขต 3', 'code' => 'พช3', 'type' => 'primary'],
            ['name' => 'สพป.แพร่ เขต 1', 'code' => 'พร1', 'type' => 'primary'],
            ['name' => 'สพป.แพร่ เขต 2', 'code' => 'พร2', 'type' => 'primary'],
            ['name' => 'สพป.ภูเก็ต', 'code' => 'ภก', 'type' => 'primary'],
            ['name' => 'สพป.มหาสารคาม เขต 1', 'code' => 'มค1', 'type' => 'primary'],
            ['name' => 'สพป.มหาสารคาม เขต 2', 'code' => 'มค2', 'type' => 'primary'],
            ['name' => 'สพป.มหาสารคาม เขต 3', 'code' => 'มค3', 'type' => 'primary'],
            ['name' => 'สพป.มุกดาหาร', 'code' => 'มห', 'type' => 'primary'],
            ['name' => 'สพป.แม่ฮ่องสอน เขต 1', 'code' => 'มส1', 'type' => 'primary'],
            ['name' => 'สพป.แม่ฮ่องสอน เขต 2', 'code' => 'มส2', 'type' => 'primary'],
            ['name' => 'สพป.ยโสธร เขต 1', 'code' => 'ยส1', 'type' => 'primary'],
            ['name' => 'สพป.ยโสธร เขต 2', 'code' => 'ยส2', 'type' => 'primary'],
            ['name' => 'สพป.ยะลา เขต 1', 'code' => 'ยล1', 'type' => 'primary'],
            ['name' => 'สพป.ยะลา เขต 2', 'code' => 'ยล2', 'type' => 'primary'],
            ['name' => 'สพป.ยะลา เขต 3', 'code' => 'ยล3', 'type' => 'primary'],
            ['name' => 'สพป.ร้อยเอ็ด เขต 1', 'code' => 'รอ1', 'type' => 'primary'],
            ['name' => 'สพป.ร้อยเอ็ด เขต 2', 'code' => 'รอ2', 'type' => 'primary'],
            ['name' => 'สพป.ร้อยเอ็ด เขต 3', 'code' => 'รอ3', 'type' => 'primary'],
            ['name' => 'สพป.ระนอง', 'code' => 'รน', 'type' => 'primary'],
            ['name' => 'สพป.ระยอง เขต 1', 'code' => 'รย1', 'type' => 'primary'],
            ['name' => 'สพป.ระยอง เขต 2', 'code' => 'รย2', 'type' => 'primary'],
            ['name' => 'สพป.ราชบุรี เขต 1', 'code' => 'รบ1', 'type' => 'primary'],
            ['name' => 'สพป.ราชบุรี เขต 2', 'code' => 'รบ2', 'type' => 'primary'],
            ['name' => 'สพป.ลพบุรี เขต 1', 'code' => 'ลบ1', 'type' => 'primary'],
            ['name' => 'สพป.ลพบุรี เขต 2', 'code' => 'ลบ2', 'type' => 'primary'],
            ['name' => 'สพป.ลำปาง เขต 1', 'code' => 'ลป1', 'type' => 'primary'],
            ['name' => 'สพป.ลำปาง เขต 2', 'code' => 'ลป2', 'type' => 'primary'],
            ['name' => 'สพป.ลำปาง เขต 3', 'code' => 'ลป3', 'type' => 'primary'],
            ['name' => 'สพป.ลำพูน เขต 1', 'code' => 'ลพ1', 'type' => 'primary'],
            ['name' => 'สพป.ลำพูน เขต 2', 'code' => 'ลพ2', 'type' => 'primary'],
            ['name' => 'สพป.เลย เขต 1', 'code' => 'ลย1', 'type' => 'primary'],
            ['name' => 'สพป.เลย เขต 2', 'code' => 'ลย2', 'type' => 'primary'],
            ['name' => 'สพป.เลย เขต 3', 'code' => 'ลย3', 'type' => 'primary'],
            ['name' => 'สพป.ศรีสะเกษ เขต 1', 'code' => 'ศก1', 'type' => 'primary'],
            ['name' => 'สพป.ศรีสะเกษ เขต 2', 'code' => 'ศก2', 'type' => 'primary'],
            ['name' => 'สพป.ศรีสะเกษ เขต 3', 'code' => 'ศก3', 'type' => 'primary'],
            ['name' => 'สพป.ศรีสะเกษ เขต 4', 'code' => 'ศก4', 'type' => 'primary'],
            ['name' => 'สพป.สกลนคร เขต 1', 'code' => 'สน1', 'type' => 'primary'],
            ['name' => 'สพป.สกลนคร เขต 2', 'code' => 'สน2', 'type' => 'primary'],
            ['name' => 'สพป.สกลนคร เขต 3', 'code' => 'สน3', 'type' => 'primary'],
            ['name' => 'สพป.สงขลา เขต 1', 'code' => 'สข1', 'type' => 'primary'],
            ['name' => 'สพป.สงขลา เขต 2', 'code' => 'สข2', 'type' => 'primary'],
            ['name' => 'สพป.สงขลา เขต 3', 'code' => 'สข3', 'type' => 'primary'],
            ['name' => 'สพป.สตูล', 'code' => 'สต', 'type' => 'primary'],
            ['name' => 'สพป.สมุทรปราการ เขต 1', 'code' => 'สป1', 'type' => 'primary'],
            ['name' => 'สพป.สมุทรปราการ เขต 2', 'code' => 'สป2', 'type' => 'primary'],
            ['name' => 'สพป.สมุทรสงคราม', 'code' => 'สส', 'type' => 'primary'],
            ['name' => 'สพป.สมุทรสาคร', 'code' => 'สค', 'type' => 'primary'],
            ['name' => 'สพป.สระแก้ว เขต 1', 'code' => 'สก1', 'type' => 'primary'],
            ['name' => 'สพป.สระแก้ว เขต 2', 'code' => 'สก2', 'type' => 'primary'],
            ['name' => 'สพป.สระบุรี เขต 1', 'code' => 'สบ1', 'type' => 'primary'],
            ['name' => 'สพป.สระบุรี เขต 2', 'code' => 'สบ2', 'type' => 'primary'],
            ['name' => 'สพป.สิงห์บุรี', 'code' => 'สห', 'type' => 'primary'],
            ['name' => 'สพป.สุโขทัย เขต 1', 'code' => 'สท1', 'type' => 'primary'],
            ['name' => 'สพป.สุโขทัย เขต 2', 'code' => 'สท2', 'type' => 'primary'],
            ['name' => 'สพป.สุพรรณบุรี เขต 1', 'code' => 'สพ1', 'type' => 'primary'],
            ['name' => 'สพป.สุพรรณบุรี เขต 2', 'code' => 'สพ2', 'type' => 'primary'],
            ['name' => 'สพป.สุพรรณบุรี เขต 3', 'code' => 'สพ3', 'type' => 'primary'],
            ['name' => 'สพป.สุราษฎร์ธานี เขต 1', 'code' => 'สฎ1', 'type' => 'primary'],
            ['name' => 'สพป.สุราษฎร์ธานี เขต 2', 'code' => 'สฎ2', 'type' => 'primary'],
            ['name' => 'สพป.สุราษฎร์ธานี เขต 3', 'code' => 'สฎ3', 'type' => 'primary'],
            ['name' => 'สพป.สุรินทร์ เขต 1', 'code' => 'สร1', 'type' => 'primary'],
            ['name' => 'สพป.สุรินทร์ เขต 2', 'code' => 'สร2', 'type' => 'primary'],
            ['name' => 'สพป.สุรินทร์ เขต 3', 'code' => 'สร3', 'type' => 'primary'],
            ['name' => 'สพป.หนองคาย เขต 1', 'code' => 'นค1', 'type' => 'primary'],
            ['name' => 'สพป.หนองคาย เขต 2', 'code' => 'นค2', 'type' => 'primary'],
            ['name' => 'สพป.หนองบัวลำภู เขต 1', 'code' => 'นภ1', 'type' => 'primary'],
            ['name' => 'สพป.หนองบัวลำภู เขต 2', 'code' => 'นภ2', 'type' => 'primary'],
            ['name' => 'สพป.อ่างทอง', 'code' => 'อท', 'type' => 'primary'],
            ['name' => 'สพป.อำนาจเจริญ', 'code' => 'อจ', 'type' => 'primary'],
            ['name' => 'สพป.อุดรธานี เขต 1', 'code' => 'อด1', 'type' => 'primary'],
            ['name' => 'สพป.อุดรธานี เขต 2', 'code' => 'อด2', 'type' => 'primary'],
            ['name' => 'สพป.อุดรธานี เขต 3', 'code' => 'อด3', 'type' => 'primary'],
            ['name' => 'สพป.อุดรธานี เขต 4', 'code' => 'อด4', 'type' => 'primary'],
            ['name' => 'สพป.อุตรดิตถ์ เขต 1', 'code' => 'อต1', 'type' => 'primary'],
            ['name' => 'สพป.อุตรดิตถ์ เขต 2', 'code' => 'อต2', 'type' => 'primary'],
            ['name' => 'สพป.อุทัยธานี เขต 1', 'code' => 'อน1', 'type' => 'primary'],
            ['name' => 'สพป.อุทัยธานี เขต 2', 'code' => 'อน2', 'type' => 'primary'],
            ['name' => 'สพป.อุบลราชธานี เขต 1', 'code' => 'อบ1', 'type' => 'primary'],
            ['name' => 'สพป.อุบลราชธานี เขต 2', 'code' => 'อบ2', 'type' => 'primary'],
            ['name' => 'สพป.อุบลราชธานี เขต 3', 'code' => 'อบ3', 'type' => 'primary'],
            ['name' => 'สพป.อุบลราชธานี เขต 4', 'code' => 'อบ4', 'type' => 'primary'],
            ['name' => 'สพป.อุบลราชธานี เขต 5', 'code' => 'อบ5', 'type' => 'primary'],

            // สพม. (สำนักงานเขตพื้นที่การศึกษามัธยมศึกษา) - ตัวอย่าง
            ['name' => 'สพม.กรุงเทพมหานคร เขต 1', 'code' => 'สพม.กท1', 'type' => 'secondary'],
            ['name' => 'สพม.กรุงเทพมหานคร เขต 2', 'code' => 'สพม.กท2', 'type' => 'secondary'],
            ['name' => 'สพม.กระบี่', 'code' => 'สพม.กบ', 'type' => 'secondary'],
            ['name' => 'สพม.กาญจนบุรี', 'code' => 'สพม.กจ', 'type' => 'secondary'],
            ['name' => 'สพม.กาฬสินธุ์', 'code' => 'สพม.กส', 'type' => 'secondary'],
            ['name' => 'สพม.กำแพงเพชร', 'code' => 'สพม.กพ', 'type' => 'secondary'],
            ['name' => 'สพม.ขอนแก่น', 'code' => 'สพม.ขก', 'type' => 'secondary'],
            ['name' => 'สพม.จันทบุรี ตราด', 'code' => 'สพม.จบ-ตร', 'type' => 'secondary'],
            ['name' => 'สพม.ฉะเชิงเทรา', 'code' => 'สพม.ฉช', 'type' => 'secondary'],
            ['name' => 'สพม.ชลบุรี ระยอง', 'code' => 'สพม.ชบ-รย', 'type' => 'secondary'],
            ['name' => 'สพม.ชัยนาท', 'code' => 'สพม.ชน', 'type' => 'secondary'], // อาจรวมกับอุทัยธานี
            ['name' => 'สพม.ชัยภูมิ', 'code' => 'สพม.ชย', 'type' => 'secondary'],
            ['name' => 'สพม.ชุมพร ระนอง', 'code' => 'สพม.ชพ-รน', 'type' => 'secondary'],
            ['name' => 'สพม.เชียงราย พะเยา', 'code' => 'สพม.ชร-พย', 'type' => 'secondary'],
            ['name' => 'สพม.เชียงใหม่', 'code' => 'สพม.ชม', 'type' => 'secondary'],
            ['name' => 'สพม.ตรัง กระบี่', 'code' => 'สพม.ตง-กบ', 'type' => 'secondary'], // เช็คข้อมูล สพม.ตรัง อาจรวมกับสตูล
            ['name' => 'สพม.ตาก', 'code' => 'สพม.ตก', 'type' => 'secondary'],
            ['name' => 'สพม.นครนายก', 'code' => 'สพม.นย', 'type' => 'secondary'], // อาจรวมกับ ปราจีนบุรี สระแก้ว
            ['name' => 'สพม.นครปฐม', 'code' => 'สพม.นฐ', 'type' => 'secondary'],
            ['name' => 'สพม.นครพนม', 'code' => 'สพม.นพ', 'type' => 'secondary'], // อาจรวมกับ มุกดาหาร
            ['name' => 'สพม.นครราชสีมา', 'code' => 'สพม.นม', 'type' => 'secondary'],
            ['name' => 'สพม.นครศรีธรรมราช', 'code' => 'สพม.นศ', 'type' => 'secondary'],
            ['name' => 'สพม.นครสวรรค์', 'code' => 'สพม.นว', 'type' => 'secondary'], // อาจรวมกับ อุทัยธานี
            ['name' => 'สพม.นนทบุรี', 'code' => 'สพม.นบ', 'type' => 'secondary'],
            ['name' => 'สพม.นราธิวาส', 'code' => 'สพม.นธ', 'type' => 'secondary'],
            ['name' => 'สพม.น่าน', 'code' => 'สพม.นน', 'type' => 'secondary'],
            ['name' => 'สพม.บึงกาฬ', 'code' => 'สพม.บก', 'type' => 'secondary'],
            ['name' => 'สพม.บุรีรัมย์', 'code' => 'สพม.บร', 'type' => 'secondary'],
            ['name' => 'สพม.ปทุมธานี', 'code' => 'สพม.ปท', 'type' => 'secondary'],
            ['name' => 'สพม.ประจวบคีรีขันธ์', 'code' => 'สพม.ปข', 'type' => 'secondary'], // อาจรวมกับ เพชรบุรี สมุทรสงคราม สมุทรสาคร
            ['name' => 'สพม.ปราจีนบุรี นครนายก สระแก้ว', 'code' => 'สพม.ปจ-นย-สก', 'type' => 'secondary'],
            ['name' => 'สพม.ปัตตานี', 'code' => 'สพม.ปน', 'type' => 'secondary'],
            ['name' => 'สพม.พระนครศรีอยุธยา', 'code' => 'สพม.อย', 'type' => 'secondary'], // อาจรวมกับ อ่างทอง สระบุรี ลพบุรี สิงห์บุรี
            ['name' => 'สพม.พะเยา', 'code' => 'สพม.พย', 'type' => 'secondary'], // อาจรวมกับเชียงราย
            ['name' => 'สพม.พังงา ภูเก็ต ระนอง', 'code' => 'สพม.พง-ภก-รน', 'type' => 'secondary'], // เช็คข้อมูล
            ['name' => 'สพม.พัทลุง', 'code' => 'สพม.พท', 'type' => 'secondary'], // อาจรวมกับ สตูล
            ['name' => 'สพม.พิจิตร', 'code' => 'สพม.พจ', 'type' => 'secondary'],
            ['name' => 'สพม.พิษณุโลก อุตรดิตถ์', 'code' => 'สพม.พล-อต', 'type' => 'secondary'],
            ['name' => 'สพม.เพชรบุรี', 'code' => 'สพม.พบ', 'type' => 'secondary'], // อาจรวมกับ ประจวบฯ
            ['name' => 'สพม.เพชรบูรณ์', 'code' => 'สพม.พช', 'type' => 'secondary'],
            ['name' => 'สพม.แพร่', 'code' => 'สพม.พร', 'type' => 'secondary'],
            ['name' => 'สพม.ภูเก็ต', 'code' => 'สพม.ภก', 'type' => 'secondary'], // อาจรวมกับพังงา
            ['name' => 'สพม.มหาสารคาม', 'code' => 'สพม.มค', 'type' => 'secondary'],
            ['name' => 'สพม.มุกดาหาร', 'code' => 'สพม.มห', 'type' => 'secondary'], // อาจรวมกับนครพนม
            ['name' => 'สพม.แม่ฮ่องสอน', 'code' => 'สพม.มส', 'type' => 'secondary'],
            ['name' => 'สพม.ยโสธร', 'code' => 'สพม.ยส', 'type' => 'secondary'], // อาจรวมกับ อำนาจเจริญ
            ['name' => 'สพม.ยะลา', 'code' => 'สพม.ยล', 'type' => 'secondary'],
            ['name' => 'สพม.ร้อยเอ็ด', 'code' => 'สพม.รอ', 'type' => 'secondary'],
            ['name' => 'สพม.ระนอง', 'code' => 'สพม.รน', 'type' => 'secondary'], // อาจรวมกับชุมพร
            ['name' => 'สพม.ระยอง', 'code' => 'สพม.รย', 'type' => 'secondary'], // อาจรวมกับชลบุรี
            ['name' => 'สพม.ราชบุรี', 'code' => 'สพม.รบ', 'type' => 'secondary'],
            ['name' => 'สพม.ลพบุรี', 'code' => 'สพม.ลบ', 'type' => 'secondary'],
            ['name' => 'สพม.ลำปาง ลำพูน', 'code' => 'สพม.ลป-ลพ', 'type' => 'secondary'],
            ['name' => 'สพม.ลำพูน', 'code' => 'สพม.ลพ', 'type' => 'secondary'], // อาจรวมกับลำปาง
            ['name' => 'สพม.เลย หนองบัวลำภู', 'code' => 'สพม.ลย-นภ', 'type' => 'secondary'],
            ['name' => 'สพม.ศรีสะเกษ ยโสธร', 'code' => 'สพม.ศก-ยส', 'type' => 'secondary'], // เช็คข้อมูล
            ['name' => 'สพม.สกลนคร', 'code' => 'สพม.สน', 'type' => 'secondary'],
            ['name' => 'สพม.สงขลา สตูล', 'code' => 'สพม.สข-สต', 'type' => 'secondary'],
            ['name' => 'สพม.สตูล', 'code' => 'สพม.สต', 'type' => 'secondary'], // อาจรวมกับสงขลา
            ['name' => 'สพม.สมุทรปราการ', 'code' => 'สพม.สป', 'type' => 'secondary'],
            ['name' => 'สพม.สมุทรสงคราม', 'code' => 'สพม.สส', 'type' => 'secondary'], // อาจรวมกับเพชรบุรี
            ['name' => 'สพม.สมุทรสาคร', 'code' => 'สพม.สค', 'type' => 'secondary'], // อาจรวมกับเพชรบุรี
            ['name' => 'สพม.สระแก้ว', 'code' => 'สพม.สก', 'type' => 'secondary'], // อาจรวมกับปราจีนบุรี
            ['name' => 'สพม.สระบุรี', 'code' => 'สพม.สบ', 'type' => 'secondary'],
            ['name' => 'สพม.สิงห์บุรี อ่างทอง', 'code' => 'สพม.สห-อท', 'type'
            => 'secondary'],
            ['name' => 'สพม.สุโขทัย', 'code' => 'สพม.สท', 'type' => 'secondary'],
            ['name' => 'สพม.สุพรรณบุรี', 'code' => 'สพม.สพ', 'type' => 'secondary'],
            ['name' => 'สพม.สุราษฎร์ธานี ชุมพร', 'code' => 'สพม.สฎ-ชพ', 'type' => 'secondary'], // เช็คข้อมูล
            ['name' => 'สพม.สุรินทร์', 'code' => 'สพม.สร', 'type' => 'secondary'],
            ['name' => 'สพม.หนองคาย', 'code' => 'สพม.นค', 'type' => 'secondary'], // อาจรวมกับบึงกาฬ
            ['name' => 'สพม.หนองบัวลำภู', 'code' => 'สพม.นภ', 'type' => 'secondary'], // อาจรวมกับเลย
            ['name' => 'สพม.อ่างทอง', 'code' => 'สพม.อท', 'type' => 'secondary'], // อาจรวมกับสิงห์บุรี
            ['name' => 'สพม.อำนาจเจริญ', 'code' => 'สพม.อจ', 'type' => 'secondary'], // อาจรวมกับอุบลราชธานี
            ['name' => 'สพม.อุดรธานี', 'code' => 'สพม.อด', 'type' => 'secondary'],
            ['name' => 'สพม.อุตรดิตถ์', 'code' => 'สพม.อต', 'type' => 'secondary'], // อาจรวมกับพิษณุโลก
            ['name' => 'สพม.อุทัยธานี ชัยนาท', 'code' => 'สพม.อน-ชน', 'type' => 'secondary'],
            ['name' => 'สพม.อุบลราชธานี อำนาจเจริญ', 'code' => 'สพม.อบ-อจ', 'type' => 'secondary'],
             // เพิ่มเติมจนครบ 245 เขต (สพป. 183 เขต + สพม. 62 เขต เดิม หรือตามโครงสร้างใหม่)
            // ข้อมูล ณ ปี 256X อาจมีการเปลี่ยนแปลง โปรดตรวจสอบกับแหล่งข้อมูลล่าสุด
        ];

        foreach ($areas as $area) {
            EducationalArea::firstOrCreate(
                ['name' => $area['name']], // Key to check for existence
                [ // Values to insert or update
                    'code' => $area['code'] ?? null,
                    'type' => $area['type'] ?? 'primary',
                ]
            );
        }
    }
}