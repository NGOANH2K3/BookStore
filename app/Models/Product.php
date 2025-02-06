<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'sach';
    protected $primaryKey = 'ma_sach';
    protected $foreignKey = ['ma_loai_sach', 'ma_tac_gia', 'ma_nxb'];
    protected $typeKey = 'string';

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['ma_sach', 'ten_sach', 'gia_sach', 'khuyen_mai', 'gia_khuyen_mai', 'mo_ta', 'so_luong', 'sold',
                            'hinh_anh', 'anh_1', 'anh_2', 'ma_loai_sach','ma_tac_gia','ma_nxb', 'created_at', 'updated_at'];


    public function __construct($data = null) {
        $this->created_at = $data;
    }

    public static function validate(array $data)
    {
        $errors = [];
        //Kiểm tra tên SP
        if (!$data['ten_sach']) {
            $errors['ten_sach'] = 'Vui lòng nhập tên sản phẩm.';
        }

        //Kiểm tra giá SP
        if (!$data['gia_sach']) {
            if ($data['gia_sach'] != 0) {
                $errors['gia_sach'] = 'Vui lòng nhập giá sản phẩm.';
            }
        } else if ($data['gia_sach'] < 0) {
            $errors['gia_sach'] = 'Giá sản phẩm không được âm.';
        }

        //Kiểm tra phần trăm khuyến mãi
        if (!$data['khuyen_mai']) {
            if ($data['khuyen_mai'] != 0) {
                $errors['khuyen_mai'] = 'Vui lòng nhập phần trăm khuyến mãi.';
            }
        } else if ($data['khuyen_mai'] < 0) {
            $errors['khuyen_mai'] = 'Phần trăm khuyến mãi không được âm.';
        } else if ($data['khuyen_mai'] > 100) {
            $errors['khuyen_mai'] = 'Khuyến mãi tối đa 100%.';
        }

        //Kiểm tra số lượng SP
        if (!$data['so_luong']) {
            if ($data['so_luong'] != 0) {
                $errors['so_luong'] = 'Vui lòng nhập số lượng sản phẩm.';
            } else {
                $errors['so_luong'] = 'Số lượng sản phẩm phải lớn hơn 0.';
            }
        } else if ($data['so_luong'] <= 0) {
            $errors['so_luong'] = 'Số lượng sản phẩm phải lớn hơn 0.';
        }

        //Kiểm tra ảnh SP
        if (!($data['hinh_anh'])) {
            $errors['hinh_anh'] = 'Vui lòng chọn ảnh sản phẩm.';
        }

        //Kiểm tra mô tả SP
        if (!($data['mo_ta'])) {
            $errors['mo_ta'] = 'Vui lòng nhập mô tả sản phẩm.';
        } else if (strlen($data['mo_ta']) > 500) {
            $errors['mo_ta'] = 'Mô tả tối đa 500 ký tự.';
        }

        return $errors;
    }

}
