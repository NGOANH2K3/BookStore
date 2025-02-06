<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TacGia extends Model
{
    protected $table = 'tacgia';
    protected $primaryKey = 'ma_tac_gia';
    public $incrementing = false;
    protected $typeKey = 'string';
    public $timestamps = false;
    protected $fillable = ['ma_tac_gia', 'ten_tac_gia', 'created_at', 'updated_at' ];
    
    public function __construct($data = null) {
        $this->created_at = $data;
    }

    public static function validate(array $data)
    {
        $errors = [];
        //Kiểm tra tên SP
        if (!$data['ten_tac_gia']) {
            $errors['ten_tac_gia'] = 'Vui lòng nhập tên sản phẩm.';
        }
        else{
        return $errors;
        }
    }
}