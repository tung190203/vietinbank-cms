<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vrarea;

class VrareaSkinLabelSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title' => 'PHÂN KHU TRUYỀN THÔNG THƯƠNG HIỆU',
                'skin_label' => 'bt ttth',
            ],
            [
                'title' => 'PHÂN KHU QUÁ TRÌNH HÌNH THÀNH VÀ PHÁT TRIỂN',
                'skin_label' => 'bt qthtvpt',
            ],
            [
                'title' => 'PHÂN KHU HOẠT ĐỘNG ĐOÀN THỂ',
                'skin_label' => 'bt hddt',
            ],
            [
                'title' => 'PHÂN KHU CÔNG NGHỆ THÔNG TIN',
                'skin_label' => 'bt cntt',
            ],
            [
                'title' => 'PHÂN KHU SỰ QUAN TÂM CỦA ĐẢNG VÀ NHÀ NƯỚC',
                'skin_label' => 'bt sqtcdvnn',
            ],
            [
                'title' => 'PHÂN KHU KINH DOANH VÀ HỢP TÁC QUỐC NỘI',
                'skin_label' => 'bt kdvhtqn',
            ],
            [
                'title' => 'PHÂN KHU SẢN PHẨM VÀ DỊCH VỤ TIÊU BIỂU',
                'skin_label' => 'bt spvdvtb',
            ],
            [
                'title' => 'SẢNH CHÍNH',
                'skin_label' => 'bt sc',
            ],
            [
                'title' => 'PHÂN KHU KINH DOANH VÀ HỢP TÁC QUỐC TẾ',
                'skin_label' => 'bt kdvhtqt',
            ],
            [
                'title' => 'PHÂN KHU HOẠT ĐỘNG AN SINH XÃ HỘI',
                'skin_label' => 'bt hdasxh',
            ],
        ];

        foreach ($data as $item) {

            $area = Vrarea::where('name', $item['title'])->first();

            if ($area) {

                $area->skin_label = [
                    $item['skin_label']
                ];

                $area->save();

                $this->command->info(
                    'Updated: ' . $area->name .
                    ' => ' . $item['skin_label']
                );

            } else {

                $this->command->warn(
                    'Not found: ' . $item['title']
                );
            }
        }
    }
}
