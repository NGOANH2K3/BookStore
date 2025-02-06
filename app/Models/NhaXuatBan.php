<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhaXuatBan extends Model
{
    protected $table = 'nhaxuatban';
    protected $primaryKey = 'ma_nxb';
    protected $typeKey = 'string';

    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['ma_nxb', 'ten_nxb', 'sdt_nxb', 'dia_chi_nxb','created_at', 'updated_at'];

    public function __construct($data = null) {
        $this->created_at = $data;
    }

    public static function validate(array $data)
    {
        $errors = [];
        //Kiểm tra tên SP
        if (!$data['ten_nxb']) {
            $errors['ten_nxb'] = 'Vui lòng nhập tên sản phẩm.';
        }
        if (!preg_match("/^[0-9]{10,11}$/", $data['sdt_nxb'])) {
            $errors['sdt_nxb'] = 'Định dạng số điện thoại không đúng';
        } 
        if (!strlen($data['dia_chi_nxb'])) {
            $errors['dia_chi_nxb'] = 'Vui lòng nhập địa chỉ';   
        } 
        else{
        return $errors;
        }
    }
}