<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class Game extends BaseModel
{

    // use HasFactory;
    static function get_reward_by_id($id){
        $rewards_list = self::rewards_list();
        foreach($rewards_list as $k=>$v){
            if( $v["id"] == $id ){
                return $v;
            }
        }
        return false;
    }
    static function rewards_list()
    {
        
        $rewards_list___ = 
            [
                [
                    "id" => 1,
                    "company_name" =>  "Rạng Đông",
                    "gift_name" =>  "Phích nước Rạng đông phiên bản Tết",
                    "amount" =>  10,
                    "money" =>  200000,
                    "slug"  => '02'
                ],
                [
                    "id" => 2,
                    "company_name" =>  "Vinaga",
                    "gift_name" =>  "Dầu gấc viên nang Vinaga - DHA",
                    "amount" =>  20,
                    "money" =>  100000,
                    "slug"  => '48'
                ],
                [
                    "id" => 3,
                    "company_name" =>  "Lock and Lock",
                    "gift_name" =>  "Bình giữ nhiệt",
                    "amount" =>  10,
                    "money" =>  590000,
                    "slug"  => '6a'
                ],
                
                [
                    "id" => 4,
                    "company_name" =>  "Cốc Cốc",
                    "gift_name" =>  "200 Cốc cốc point",
                    "amount" =>  20,
                    "money" =>  0,
                    "slug"  => '10'
                ],
                [
                    "id" => 5,
                    "company_name" =>  "VR Plus",
                    "gift_name" =>  "Voucher giảm 20%",
                    "amount" =>  20,
                    "money" =>  0,
                    "slug"  => '16'
                ],
                [
                    "id" => 6,
                    "company_name" =>  "Gapowork",
                    "gift_name" =>  "Voucher giảm 20%",
                    "amount" =>  20,
                    "money" =>  0,
                    "slug"  => '12'
                ],
                [
                    "id" => 7,
                    "company_name" =>  "Timostore",
                    "gift_name" =>  "Voucher trị giá 100.000đ khi mua sản phẩm tại timostore.vn",
                    "amount" =>  20,
                    "money" =>  0,
                    "slug"  => '15'
                ],
                [
                    "id" => 8,
                    "company_name" =>  "Gojek",
                    "gift_name" =>  "Voucher trị giá 50.000đ khi sử dụng dịch vụ Gocar của Gojek",
                    "amount" =>  20,
                    "money" =>  0,
                    "slug"  => '11'
                ],

                // [
                //     "id" => 9,
                //     "company_name" =>  "Edupia",
                //     "gift_name" =>  "Khoá học tiếng Anh trọn đời Edupia dành cho học sinh tiểu học",
                //     "amount" =>  3,
                //     "money" =>  1490000,
                //     "slug"  => '02'
                // ],
                // [
                //     "id" => 10,
                //     "company_name" =>  "Elsa Speak",
                //     "gift_name" =>  "Khoá học Elsa Pro 1 năm",
                //     "amount" =>  3,
                //     "money" =>  1095000,
                //     "slug"  => '02'
                // ],
            ];
        
            $rewards_list = 
            [
                [
                    "id" => 0,
                    "company_name" =>  "Rạng Đông",
                    "gift_name" =>  "Phích nước Rạng đông phiên bản Tết",
                    "amount" =>  10,
                    "money" =>  200000,
                    "slug"  => '02'
                ],
                [
                    "id" => 2,
                    "company_name" =>  "Vinaga",
                    "gift_name" =>  "Dầu gấc viên nang Vinaga - DHA",
                    "amount" =>  20,
                    "money" =>  100000,
                    "slug"  => '48'
                ],
                [
                    "id" => 3,
                    "company_name" =>  "Lock and Lock",
                    "gift_name" =>  "Bình giữ nhiệt",
                    "amount" =>  10,
                    "money" =>  590000,
                    "slug"  => '6a'
                ],
                [
                    "id" => 4,
                    "company_name" =>  "Công ty cổ phần giáo dục Educa Corporation",
                    "gift_name" =>  "Khoá học tiếng Anh trọn đời Edupia dành cho học sinh tiểu học",
                    "amount" =>  3,
                    "money" =>  1490000,
                    "slug"  => '9999'
                ],
                [
                    "id" => 6,
                    "company_name" =>  "Timostore",
                    "gift_name" =>  "Voucher trị giá 100.000đ khi mua sản phẩm tại timostore.vn",
                    "amount" =>  100,
                    "money" =>  0,
                    "slug"  => '15'
                ],
                [
                    "id" => 7,
                    "company_name" =>  "Gojek",
                    "gift_name" =>  "Voucher trị giá 300.000đ khi sử dụng dịch vụ của Gojek",
                    "amount" =>  20,
                    "money" =>  0,
                    "slug"  => '11'
                ],

                
               
            ];
        
        return $rewards_list;
    }
}
