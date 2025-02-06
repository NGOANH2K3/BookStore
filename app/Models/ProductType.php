<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    protected $table = 'loaisach';
    protected $primaryKey = 'ma_loai_sach';
    protected $typeKey = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['ma_loai_sach', 'ten_loai_sach','created_at', 'updated_at'];

    public function __construct($data = null) {
        $this->created_at = $data;
    }

    public static function validate(array $data)
    {
        $errors = [];
        //Kiểm tra tên SP
        if (!$data['ten_loai_sach']) {
            $errors['ten_loai_sach'] = 'Vui lòng nhập tên sản phẩm.';
        }
        else{
        return $errors;
        }
    }
}
