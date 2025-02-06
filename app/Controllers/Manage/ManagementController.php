<?php

namespace App\Controllers\Manage;

use App\Controllers\Controller;
use App\SessionGuard as Guard;
use App\Models\Product;
use App\Models\User;
use App\Models\ProductType;
use App\Models\TacGia;
use App\Models\NhaXuatBan;
use App\Models\Bill;
use App\Models\BillDetail;
use Carbon\Carbon;

class ManagementController extends Controller
{
    public function __construct()
    {
        if (!Guard::isUserLoggedIn()) {
            redirect('/home');
        }

        parent::__construct();
    }

    /*** MANAGE ALL PRODUCTS ***/
    // public function search()
	// {
	// 	if(isset($_POST['search']) && $_POST['search'] != ''){
	// 		$this->sendPage('layouts/search',['result'=>Product::where('ten_sach', 'like', '%' . $_POST['search']. '%')->get()]);
	// 	}else {
	// 		redirect('/home');
	// 	}
	// }

    public function getAllProducts()
    {
        $products_manage = Product::join('loaisach', 'loaisach.ma_loai_sach', '=', 'sach.ma_loai_sach')->orderBy('sach.ma_sach', 'ASC')->get();
        $this->sendPage('manage/manageProduct', [
            'products_manage' => $products_manage
        ]);
    }
    public function getAllProductsType()
{
    // Lấy tất cả dữ liệu từ bảng loaisach
    $products_type = ProductType::select('ma_loai_sach', 'ten_loai_sach')
                             ->orderBy('ma_loai_sach', 'ASC')
                             ->get();
    
    // Gửi dữ liệu đến view
    $this->sendPage('manage/manageProductType', [
        'products_type' => $products_type
    ]);
}


    /*** SORT PRODUCT ***/
    public function sortAllProducts()
    {
        if (isset($_POST['sort-price'])) {
            $sort_price = $_POST['sort-price'];
            if ($sort_price == 1) {
                $old_selected = array("val" => "1");
                $this->sendPage('manage/manageProduct', [
                    'products_manage' => Product::join('loaisach', 'loaisach.ma_loai_sach', '=', 'sach.ma_loai_sach')
                        ->orderBy('ma_sach', 'ASC')->get(),
                    'old_selected' => $old_selected
                ]);
            } else if ($sort_price == 2) {
                $old_selected = array("val" => "2");
                $this->sendPage('manage/manageProduct', [
                    'products_manage' => Product::join('loaisach', 'loaisach.ma_loai_sach', '=', 'sach.ma_loai_sach')
                        ->orderBy('gia_khuyen_mai', 'ASC')->get(),
                    'old_selected' => $old_selected
                ]);
            } else if ($sort_price == 3) {
                $old_selected = array("val" => "3");
                $this->sendPage('manage/manageProduct', [
                    'products_manage' => Product::join('loaisach', 'loaisach.ma_loai_sach', '=', 'sach.ma_loai_sach')
                        ->orderBy('gia_khuyen_mai', 'DESC')->get(),
                    'old_selected' => $old_selected
                ]);
            } else if ($sort_price == 4) {
                $old_selected = array("val" => "4");
                $this->sendPage('manage/manageProduct', [
                    'products_manage' => Product::join('loaisach', 'loaisach.ma_loai_sach', '=', 'sach.ma_loai_sach')
                        ->orderBy('sold', 'DESC')->get(),
                    'old_selected' => $old_selected
                ]);
            }
        }
    }

    /*** CREATE NEW PRODUCT ***/
    public function showCreatePage()
    {
        $last_product = $this->createNewProductId();

        $product_type = ProductType::all();
        $tacgia = TacGia::all();
        $nxb = NhaXuatBan::all();

        $this->sendPage('manage/create', [
            'errors' => session_get_once('errors'),
            'old' => $this->getSavedFormValues(),
            'ma_sp' => $last_product,
            'loai_sp' => $product_type,
            'tacgia' => $tacgia,
            'nxb' => $nxb
        ]);
    }

    public function createNewProductId()
    {
        $last_product = Product::latest('ma_sach')->first();
        if ($last_product) {
            $last_product_id = (int)trim($last_product['ma_sach'], "0"); // Bỏ "S" trong mã sản phẩm và chuyển về kiểu int
        } else {
            $last_product_id = 0; // Nếu không có sản phẩm nào, bắt đầu từ 0
        }
        $last_product_id++;
        $new_product_id = "0" . $last_product_id;
        $last_product['ma_sach'] = $new_product_id;
        return $last_product;
    }

    public function createProduct()
    {
        $data = $this->filterProductData($_POST);
        $model_errors = Product::validate($data);
        $this->uploadImage();

        //Cập nhật lại giá khuyến mãi
        if ($data['khuyen_mai'] && $data['khuyen_mai'] >= 0) {
            $data['gia_khuyen_mai'] = $data['gia_sach'] - $data['gia_sach'] * ($data['khuyen_mai'] / 100);
        } else {
            $data['gia_khuyen_mai'] = $data['gia_sach'];
        }

        //Ngày tạo = hiện tại
        $data['created_at'] = Carbon::now()->tz('Asia/Ho_Chi_Minh');

        if (empty($model_errors)) {
            $product = new Product();
            $product->fill($data);
            $product->save();
            redirect('/manageProduct');
        }

        // Lưu các giá trị của form vào $_SESSION['form']
        $this->saveFormValues($_POST);
        // Lưu các thông báo lỗi vào $_SESSION['errors']
        redirect('/create', ['errors' => $model_errors]);
    }

    protected function filterProductData(array $data)
    {
        return [
            'ma_sach' => $data['ma_sach'] ?? null,
            'ten_sach' => $data['ten_sach'] ?? null,
            'gia_sach' => $data['gia_sach'] ?? null,
            'khuyen_mai' => $data['khuyen_mai'] ?? null,
            'ma_loai_sach' => $data['ma_loai_sach'] ?? null,
            'ma_tac_gia' => $data['ma_tac_gia'] ?? null,
            'ma_nxb' => $data['ma_nxb'] ?? null,
            'so_luong' => $data['so_luong'] ?? null,
            'sold' => 0,
            'hinh_anh' => basename($_FILES["hinh_anh"]["name"]) ?? null,
            'anh_1' => basename($_FILES["anh"]["name"][0]) ?? null,
            'anh_2' => basename($_FILES["anh"]["name"][1]) ?? null,
            'mo_ta' => $data['mo_ta'] ?? null
        ];
    }

    protected function uploadImage()
{
    $allow_type = ['jpg', 'png', 'jpeg'];
    $target_dir = ROOTDIR . "/public/img/product/";

    // Helper function to upload a file
    function uploadFile($file, $target_dir, $allow_type) {
        $target_file = $target_dir . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (in_array($imageFileType, $allow_type) && $file["error"] === UPLOAD_ERR_OK) {
            move_uploaded_file($file["tmp_name"], $target_file);
        }
    }

    // Upload main image
    if (isset($_FILES["hinh_anh"])) {
        uploadFile($_FILES["hinh_anh"], $target_dir, $allow_type);
    }

    // Upload additional images
    if (isset($_FILES["anh"])) {
        foreach ($_FILES["anh"]["name"] as $index => $name) {
            if (!empty($name)) {
                uploadFile(['name' => $name, 'tmp_name' => $_FILES["anh"]["tmp_name"][$index], 'error' => $_FILES["anh"]["error"][$index]], $target_dir, $allow_type);
            }
        }
    }
}

public function showCreatetypePage()
    {
        $last_product_type = $this->createNewProductTypeId();

        // $product_type = ProductType::all();

        $this->sendPage('manage/createtype', [
            'errors' => session_get_once('errors'),
            'old' => $this->getSavedFormValues(),
            'ma_loaisp'=> $last_product_type,
            'loai_sp' => $last_product_type,
        ]);
    }

    public function createNewProductTypeId()
    {
        $last_product_type = ProductType::latest('ma_loai_sach')->first();
        if ($last_product_type) {
            $last_product_type_id = (int)trim($last_product_type['ma_loai_sach'], "B");    //Bỏ "SP" trong mã sản phẩm và chuyển về kiểu int 
        }else {
            $last_product_type_id = 0; // Nếu không có sản phẩm nào, bắt đầu từ 0
        }
        $last_product_type_id++;
        $new_product_type_id = "B" . $last_product_type_id;
        $last_product_type['ma_loai_sach'] = $new_product_type_id ;
        return $last_product_type;
    }

    // public function createProductType()
    // {
    //     $data = $this->filterProductData($_POST);
    //     $model_errors = Product::validate($data);
    //     $this->uploadImage();
  
        

    //     if (empty($model_errors)) {
    //         $product_type = new productType();
    //         $product_type->fill($data);
    //         $product_type->save();
    //         redirect('/manageProductType');
    //     }

    //     // Lưu các giá trị của form vào $_SESSION['form']
    //     $this->saveFormValues($_POST);
    //     // Lưu các thông báo lỗi vào $_SESSION['errors']
    //     redirect('/createtype', ['errors' => $model_errors]);
    // }

    // protected function filterProductTypeData(array $data)
    // {
    //     return [
    //         'ma_loai_sach' => $data['ma_loai_sach'] ?? null,
    //         'ten_loai_sach' => $data['ten_loai_sach'] ?? null,
    //     ];
    // }
    public function createProductType()
{
    $data = $this->filterProductTypeData($_POST);
    $model_errors = ProductType::validate($data);

    // Nếu không có lỗi, lưu loại sản phẩm mới
    if (empty($model_errors)) {
        $product_type = new ProductType();
        $product_type->fill($data);
        $product_type->save();
        redirect('/manageProductType');
    }

    // Lưu giá trị form vào session nếu có lỗi
    $this->saveFormValues($_POST);

    // Lưu lỗi vào session và chuyển hướng lại trang tạo mới
    redirect('/createtype', ['errors' => $model_errors]);
}

protected function filterProductTypeData(array $data)
{
    return [
        'ma_loai_sach' => $data['ma_loai_sach'] ?? null,
        'ten_loai_sach' => $data['ten_loai_sach'] ?? null,
        // 'mo_ta_loai_sach' => $data['mo_ta_loai_sach'] ?? null, // Thêm trường mô tả loại sách (nếu có)
    ];
}

    

    /*** UPDATE PRODUCT ***/
    public function showUpdatePage($productId)
    {
        $product = Product::where('sach.ma_sach', '=', $productId)->first();

        $product_type = ProductType::all();
        $tac_gia = TacGia::all();
        $nxb = NhaXuatBan::all();

        $this->sendPage('manage/update', [
            'errors' => session_get_once('errors_update'),
            'product' => $product,
            'old_value' => $this->getSavedUpdateFormValues(),
            'loai_sp' => $product_type,
            'tg' => $tac_gia,
            'nxb' => $nxb
        ]);
    }

    // public function update($productId)
    // {
    //     $product = Product::where('ma_sach', '=', $productId)->first();
    //     $data = $this->filterProductData($_POST);
    //     $model_errors = Product::validate($data);

    //     //Nếu không chọn ảnh cập nhật => không cập nhật ảnh (not bring $data['hinh_anh'] to fill function)
    //     //Input file ảnh có thể cập nhật hoặc không cập nhật => nếu không cập nhật, không báo lỗi input file image
    //     if (!$data['hinh_anh']) {
    //         unset($data['hinh_anh']);
    //         unset($model_errors['hinh_anh']);
    //     }

    //     if (!$data['anh_1']) {
    //         unset($data['anh_1']);
    //         unset($model_errors['anh_1']);
    //     }

    //     if (!$data['anh_2']) {
    //         unset($data['anh_2']);
    //         unset($model_errors['anh_2']);
    //     }

    //     //Không cập nhật lại Mã Sản Phẩm và Created_at và Số lượng sp đã bán
    //     unset($data['ma_sach']);
    //     unset($data['created_at']);
    //     unset($data['sold']);

    //     //Cập nhật lại giá khuyến mãi
    //     if ($data['khuyen_mai'] && $data['khuyen_mai'] >= 0) {
    //         $data['gia_khuyen_mai'] = $data['gia_sach'] - $data['gia_sach'] * ($data['khuyen_mai'] / 100);
    //     } else {
    //         $data['gia_khuyen_mai'] = $data['gia_sach'];
    //     }

    //     if (empty($model_errors)) {
    //         $product->update($data);
    //         $product->save();
    //         redirect('/manageProduct');
    //     }
    //     $this->saveUpdateFormValues($_POST);

    //     redirect('/manage/' . $productId, ['errors_update' => $model_errors]);
    // }

    public function showUpdatetypePage($product_type_id)
    {
        // Lấy thông tin loại sách theo ID
        $product_type = ProductType::where('ma_loai_sach', '=', $product_type_id)->first();
    
        // Gửi dữ liệu tới trang 'manage/updatetype'
        $this->sendPage('manage/updatetype', [
            'product_type' => $product_type,  // Quan trọng
            'errors' => session_get_once('errors_update'),
            'old_value' => $this->getSavedUpdateFormValues()
        ]);
    }

    // public function update($producttypeId)
    // {
    //     $product_type = ProductType::where('ma_loai_sach', '=', $producttypeId)->first();
    //     $data = $this->filterProductTypeData($_POST);
    //     $model_errors = ProductType::validate($data);

        
    //     //Không cập nhật lại Mã Sản Phẩm và Created_at và Số lượng sp đã bán
    //     unset($data['ma_loai_sach']);
    //     unset($data['created_at']);
    //     unset($data['sold']);


    //     if (empty($model_errors)) {
    //         $product_type->update($data);
    //         $product_type->save();
    //         redirect('/manageProductType');
    //     }
    //     $this->saveUpdateFormValues($_POST);

    //     redirect('/manage/' . $producttypeId, ['errors_update' => $model_errors]);
    // }
    // public function update($id, $type = 'product')
    // {
    //     // Xác định model và dữ liệu cần xử lý
    //     if ($type === 'product') {
    //         $item = Product::where('ma_sach', '=', $id)->first();
    //         $data = $this->filterProductData($_POST);
    //         $model_errors = Product::validate($data);
    
    //         // Nếu không chọn ảnh, không cập nhật ảnh
    //         if (!$data['hinh_anh']) {
    //             unset($data['hinh_anh']);
    //             unset($model_errors['hinh_anh']);
    //         }
    
    //         if (!$data['anh_1']) {
    //             unset($data['anh_1']);
    //             unset($model_errors['anh_1']);
    //         }
    
    //         if (!$data['anh_2']) {
    //             unset($data['anh_2']);
    //             unset($model_errors['anh_2']);
    //         }
    
    //         // Không cập nhật mã sản phẩm, ngày tạo và số lượng đã bán
    //         unset($data['ma_sach']);
    //         unset($data['created_at']);
    //         unset($data['sold']);
    
    //         // Cập nhật giá khuyến mãi nếu có
    //         if ($data['khuyen_mai'] && $data['khuyen_mai'] >= 0) {
    //             $data['gia_khuyen_mai'] = $data['gia_sach'] - $data['gia_sach'] * ($data['khuyen_mai'] / 100);
    //         } else {
    //             $data['gia_khuyen_mai'] = $data['gia_sach'];
    //         }
    //     } elseif ($type === 'product_type') {
    //         $item = ProductType::where('ma_loai_sach', '=', $id)->first();
    //         $data = $this->filterProductTypeData($_POST);
    //         $model_errors = ProductType::validate($data);
    
    //         // Không cập nhật mã loại sản phẩm và ngày tạo
    //         unset($data['ma_loai_sach']);
    //         unset($data['created_at']);
    //     }
    
    //     // Thực hiện cập nhật
    //     if (empty($model_errors)) {
    //         $item->update($data);
    //         $item->save();
    
    //         if ($type === 'product') {
    //             redirect('/manageProduct');
    //         } else {
    //             redirect('/manageProductType');
    //         }
    //     }
    
    //     // Lưu dữ liệu tạm thời khi có lỗi
    //     $this->saveUpdateFormValues($_POST);
    
    //     redirect('/manage/' . $id, ['errors_update' => $model_errors]);
    // }
    
    public function update($id, $type = 'product')
    {
        // Xác định model, dữ liệu và quy tắc kiểm tra dựa trên $type
        if ($type === 'product') {
            $item = Product::where('ma_sach', '=', $id)->first();
            $data = $this->filterProductData($_POST);
            $model_errors = Product::validate($data);
    
            // Nếu không chọn ảnh, không cập nhật ảnh
            if (!$data['hinh_anh']) {
                unset($data['hinh_anh']);
                unset($model_errors['hinh_anh']);
            }
    
            if (!$data['anh_1']) {
                unset($data['anh_1']);
                unset($model_errors['anh_1']);
            }
    
            if (!$data['anh_2']) {
                unset($data['anh_2']);
                unset($model_errors['anh_2']);
            }
    
            // Không cập nhật mã sản phẩm, ngày tạo và số lượng đã bán
            unset($data['ma_sach']);
            unset($data['created_at']);
            unset($data['sold']);
    
            // Cập nhật giá khuyến mãi nếu có
            if ($data['khuyen_mai'] && $data['khuyen_mai'] >= 0) {
                $data['gia_khuyen_mai'] = $data['gia_sach'] - $data['gia_sach'] * ($data['khuyen_mai'] / 100);
            } else {
                $data['gia_khuyen_mai'] = $data['gia_sach'];
            }
        } elseif ($type === 'product_type') {
            $item = ProductType::where('ma_loai_sach', '=', $id)->first();
            $data = $this->filterProductTypeData($_POST);
            $model_errors = ProductType::validate($data);
    
            // Không cập nhật mã loại sản phẩm và ngày tạo
            unset($data['ma_loai_sach']);
            unset($data['created_at']);
        } elseif ($type === 'tacgia') {
            $item = TacGia::where('ma_tac_gia', '=', $id)->first();
            $data = $this->filtertacgiaData($_POST);
            $model_errors = TacGia::validate($data);
    
            // Không cập nhật mã tác giả và ngày tạo
            unset($data['ma_tac_gia']);
            unset($data['created_at']);
        } elseif ($type === 'nhaxuatban') {
            $item = NhaXuatBan::where('ma_nxb', '=', $id)->first();
            $data = $this->filterNXBData($_POST);
            $model_errors = NhaXuatBan::validate($data);
    
            // Không cập nhật mã tác giả và ngày tạo
            unset($data['ma_nxb']);
            unset($data['created_at']);
        }
        else {
            // Nếu type không hợp lệ, xử lý lỗi
            echo "Loại dữ liệu không hợp lệ: $type";
            return;
        }
    
        // Thực hiện cập nhật
        if (empty($model_errors)) {
            $item->update($data);
            $item->save();
    
            // Chuyển hướng sau khi cập nhật thành công
            if ($type === 'product') {
                redirect('/manageProduct');
            } elseif ($type === 'product_type') {
                redirect('/manageProductType');
            } elseif ($type === 'tacgia') {
                redirect('/managetacgia');
            } elseif ($type === 'nhaxuatban') {
                redirect('/managenhaxuatban');
            }
        }
    
        // Lưu dữ liệu tạm thời khi có lỗi
        $this->saveUpdateFormValues($_POST);
    
        // Chuyển hướng kèm lỗi nếu cập nhật thất bại
        redirect('/manage/' . $id, ['errors_update' => $model_errors]);
    }
    



    /*** DELETE PRODUCT ***/
//     public function delete($id)
// {
//     // Kiểm tra nếu $id là mã sản phẩm (ma_sach)
//     $product = Product::where('ma_sach', '=', $id)->first();

//     if ($product) {
//         // Nếu là sản phẩm, xóa sản phẩm đó
//         $product->delete();
//         redirect('/manageProduct');
//     } else {
//         // Kiểm tra nếu $id là mã loại sản phẩm (ma_loai_sach)
//         $product_type = ProductType::where('ma_loai_sach', '=', $id)->first();

//         if ($product_type) {
//             // Nếu là loại sản phẩm, xóa loại sản phẩm đó
//             $product_type->delete();
//             redirect('/manageProductType');
//         } else {
//             // Nếu không tìm thấy sản phẩm hoặc loại sản phẩm, thông báo lỗi hoặc xử lý khác
//             echo "Không tìm thấy sản phẩm hoặc loại sản phẩm với ID: $id";
//             // redirect('/manageProduct'); // Bạn có thể chuyển hướng lại nếu cần
//         }
//     }
// }
// public function delete($id)
// {
//     // Kiểm tra nếu $id là mã sản phẩm (ma_sach)
//     $product = Product::where('ma_sach', '=', $id)->first();

//     if ($product) {
//         // Nếu là sản phẩm, xóa sản phẩm đó
//         $product->delete();
//         redirect('/manageProduct');
//     } else {
//         // Kiểm tra nếu $id là mã loại sản phẩm (ma_loai_sach)
//         $product_type = ProductType::where('ma_loai_sach', '=', $id)->first();

//         if ($product_type) {
//             // Nếu là loại sản phẩm, xóa loại sản phẩm đó
//             $product_type->delete();
//             redirect('/manageProductType');
//         } else {
//             // Kiểm tra nếu $id là mã tác giả (ma_tac_gia)
//             $tacgia = TacGia::where('ma_tac_gia', '=', $id)->first();

//             if ($tacgia) {
//                 try {
//                     // Xóa tác giả
//                     $tacgia->delete();
//                     redirect('/managetacgia');
//                 } catch (\Exception $e) {
//                     // Xử lý lỗi nếu không thể xóa tác giả do ràng buộc khóa ngoại
//                     echo "Không thể xóa tác giả vì đang được tham chiếu trong dữ liệu khác. Vui lòng kiểm tra lại.";
//                 }
//             } else {
//                 // Nếu không tìm thấy sản phẩm, loại sản phẩm hoặc tác giả
//                 echo "Không tìm thấy dữ liệu tương ứng với ID: $id";
//                 // redirect('/manage'); // Tùy chọn: Chuyển hướng về trang quản lý chung
//             }
//         }
//     }
// }
public function delete($id)
{
    // Kiểm tra nếu $id là mã sản phẩm (ma_sach)
    $product = Product::where('ma_sach', '=', $id)->first();

    if ($product) {
        // Nếu là sản phẩm, xóa sản phẩm đó
        $product->delete();
        redirect('/manageProduct');
    } else {
        // Kiểm tra nếu $id là mã loại sản phẩm (ma_loai_sach)
        $product_type = ProductType::where('ma_loai_sach', '=', $id)->first();

        if ($product_type) {
            // Nếu là loại sản phẩm, xóa loại sản phẩm đó
            $product_type->delete();
            redirect('/manageProductType');
        } else {
            // Kiểm tra nếu $id là mã tác giả (ma_tac_gia)
            $tacgia = TacGia::where('ma_tac_gia', '=', $id)->first();

            if ($tacgia) {
                try {
                    // Xóa tác giả
                    $tacgia->delete();
                    redirect('/managetacgia');
                } catch (\Exception $e) {
                    // Xử lý lỗi nếu không thể xóa tác giả do ràng buộc khóa ngoại
                    echo "Không thể xóa tác giả vì đang được tham chiếu trong dữ liệu khác. Vui lòng kiểm tra lại.";
                }
            } else {
                // Kiểm tra nếu $id là mã nhà xuất bản (ma_nxb)
                $nhaxuatban = NhaXuatBan::where('ma_nxb', '=', $id)->first();

                if ($nhaxuatban) {
                    try {
                        // Xóa nhà xuất bản
                        $nhaxuatban->delete();
                        redirect('/managenhaxuatban');
                    } catch (\Exception $e) {
                        // Xử lý lỗi nếu không thể xóa nhà xuất bản do ràng buộc khóa ngoại
                        echo "Không thể xóa nhà xuất bản vì đang được tham chiếu trong dữ liệu khác. Vui lòng kiểm tra lại.";
                    }
                } else {
                    // Nếu không tìm thấy bất kỳ đối tượng nào
                    echo "Không tìm thấy dữ liệu tương ứng với ID: $id";
                    // redirect('/manage'); // Tùy chọn: Chuyển hướng về trang quản lý chung
                }
            }
        }
    }
}




    /*** SORT PRODUCT ***/
    public function sortAllUsers()
    {
        if (isset($_POST['sort-user'])) {
            $sort_user = $_POST['sort-user'];
            if ($sort_user == 1) {
                $old_selected = array("val" => "1");
                $this->sendPage('manage/users', [
                    'users_manage' => User::all(),
                    'old_user_selected' => $old_selected
                ]);
            } else if ($sort_user == 2) {
                $old_selected = array("val" => "2");
                $this->sendPage('manage/users', [
                    'users_manage' => User::orderBy('id', 'ASC')->get(),
                    'old_user_selected' => $old_selected
                ]);
            } else if ($sort_user == 3) {
                $old_selected = array("val" => "3");
                $this->sendPage('manage/users', [
                    'users_manage' => User::orderBy('id', 'DESC')->get(),
                    'old_user_selected' => $old_selected
                ]);
            } else if ($sort_user == 4) {
                $old_selected = array("val" => "4");
                $this->sendPage('manage/users', [
                    'users_manage' => User::orderBy('created_at', 'DESC')->get(),
                    'old_user_selected' => $old_selected
                ]);
            }
        }
    }


    /*** MANAGE ALL USERS ***/
    public function getAllUsers()
    {
        $users_manage = User::all();
        $this->sendPage('manage/users', [
            'users_manage' => $users_manage
        ]);
    }

    /*** UPDATE USER'S ACCOUNT ***/
    protected function filterUserData(array $data)
    {
        return [
            'name' => $data['name'] ?? null,
            'email' => filter_var($data['email'], FILTER_VALIDATE_EMAIL),
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ];
    }

    public function userInfo()
    {
        $user = User::where('id', $_GET['userId'])->first();

        $this->sendPage('manage/userInfo', [
            'errors' => session_get_once('errors_update'),
            'user' => $user,
            'old_value' => $this->getSavedUpdateFormValues(),
        ]);
    }

    public function updateUser()
    {
        $user = User::where('id', $_POST['id'])->first();
        $data = $this->filterUserData($_POST);
        $model_errors = User::validateUpdate($data);
        if (!$data['email'] || $data['email']== $user->email) {
            unset($data['email']);
            unset($model_errors['email']);
        }
        if (empty($model_errors)) {
            $user->update([
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'address' => $_POST['address'],
            ]);
            redirect('/home');
        }
        
        $this->saveUpdateFormValues($_POST);
        redirect('/userInfo?userId=' . $user->id, ['errors_update' => $model_errors]);
    }

    /*** UPDATE USER'S ACCOUNT ***/
    protected function filterPassData(array $data)
    {
        return [
            'password' => $data['password'] ?? null,
            'new_password' => $data['new_password'] ?? null,
			'password_confirmation' => $data['password_confirmation'] ?? null
        ];
    }

    public function passChange()
    {
        $user = User::where('id', $_GET['id'])->first();

        $this->sendPage('manage/passChange', [
            'errors' => session_get_once('errors_update'),
            'user' => $user,
            'old_value' => $this->getSavedUpdateFormValues(),
        ]);
    }

    public function updatePass()
    {
        $user = User::where('id', $_POST['id'])->first();
        $data = $this->filterPassData($_POST);
        $model_errors = User::validatePass($data);
        $verify = password_verify($data['password'], $user->password);
        if (!$verify){
            $model_errors['password'] = 'Mật khẩu không đúng';
        }
        if (empty($model_errors) && $verify) {
            $user->update([
                'password' => password_hash($_POST['new_password'], PASSWORD_DEFAULT),
            ]);
            redirect('/home');
        }
        
        $this->saveUpdateFormValues($_POST);
        redirect('/passChange?id=' . $user->id, ['errors_update' => $model_errors]);
    }

    /*** MANAGE ALL BILLS ***/
    public function manageBill()
    {
        $khach = User::where('email', Guard::user()->email)->first();
        $this->sendPage('manage/manageBill', [
            'bills' => Bill::join('users', 'users.id', '=', 'hoadon.id')->orderBy('ma_hoa_don', 'DESC')->get()
        ]);
    }

    public function manageDetailBill()
    {
        $this->sendPage('manage/manageDetailBill', [
            'bill' => BillDetail::join('sach', 'sach.ma_sach', '=', 'chitiethoadon.ma_sach')->where('ma_hoa_don', $_GET['mhd'])->get(),
            'billdetail' => Bill::where('ma_hoa_don', $_GET['mhd'])->get()
        ]);
    }

    public function cancelBill($billId)
    {
        $data['trang_thai'] = "Canceled";
        $bill = Bill::where('ma_hoa_don', '=', $billId)->first();
        $bill->update($data);
        redirect('/manageBill');
    }

    public function send($billId)
    {
        $data['trang_thai'] = "sending";
        $bill = Bill::where('ma_hoa_don', '=', $billId)->first();
        $bill->update($data);
        redirect('/manageBill');
    }

    /*** BILL FILTER ***/
    public function sortBill()
    {
        if (isset($_POST['bill-filter'])) {
            $filter_bill = $_POST['bill-filter'];
            if ($filter_bill == 1) {
                $old_selected = array("val" => "1");
                $this->sendPage('manage/manageBill', [
                    'bills' => Bill::orderBy('ma_hoa_don', 'DESC')->get(),
                    'old_selected' => $old_selected
                ]);
            } else if ($filter_bill == 2) {
                $old_selected = array("val" => "2");
                $this->sendPage('manage/manageBill', [
                    'bills' => Bill::where('hoadon.trang_thai', '=', 'processing')->get(),
                    'old_selected' => $old_selected
                ]);
            } else if ($filter_bill == 3) {
                $old_selected = array("val" => "3");
                $this->sendPage('manage/manageBill', [
                    'bills' => Bill::where('hoadon.trang_thai', '=', 'sending')->get(),
                    'old_selected' => $old_selected
                ]);
            } else if ($filter_bill == 4) {
                $old_selected = array("val" => "4");
                $this->sendPage('manage/manageBill', [
                    'bills' => Bill::where('hoadon.trang_thai', '=', 'recieved')->get(),
                    'old_selected' => $old_selected
                ]);
            } else if ($filter_bill == 5) {
                $old_selected = array("val" => "5");
                $this->sendPage('manage/manageBill', [
                    'bills' => Bill::where('hoadon.trang_thai', '=', 'Canceled')->get(),
                    'old_selected' => $old_selected
                ]);
            }
        }
    }


        /*** tác giả ***/
        //tạo tác giả   //////////////////////////////////////////////////////////////
    public function getAlltacgia()
{
    // Lấy tất cả dữ liệu từ bảng tác giả
    $tacgia = TacGia::select('ma_tac_gia', 'ten_tac_gia')
                             ->orderBy('ma_tac_gia', 'ASC')
                             ->get();
    
    // Gửi dữ liệu đến view
    $this->sendPage('manage/managetacgia', [
        'tacgia' => $tacgia
    ]);

}
public function showCreatetacgiaPage()
    {
        $new_tacgia_id  = $this->createNewtacgiaid();

        // $product_type = ProductType::all();

        $this->sendPage('manage/createtacgia', [
            'errors' => session_get_once('errors'),
            'old' => $this->getSavedFormValues(),
            'ma_tac_gia'=> $new_tacgia_id,
            'tacgia' => $new_tacgia_id,
        ]);
    }

    // public function createNewtacgiaId()
    // {
    //     $last_tacgia = TacGia::latest('ma_tac_gia')->first();
    //     if ($last_tacgia) {
    //         $last_tacgia_id = (int)trim($last_tacgia['ma_tac_gia'], "T");    //Bỏ "SP" trong mã sản phẩm và chuyển về kiểu int 
    //     }else {
    //         $last_tacgia_id = 0; // Nếu không có sản phẩm nào, bắt đầu từ 0
    //     }
    //     $last_tacgia_id++;
    //     $new_tacgia_id = "T" . $last_tacgia_id;
    //     $last_tacgia['ma_tac_gia'] = $new_tacgia_id ;
    //     return $last_tacgia;
    // }
    public function createNewtacgiaid()
    {
        // Lấy bản ghi có mã lớn nhất
        $last_tacgia = TacGia::latest('ma_tac_gia')->first();
    
        // Nếu có bản ghi cuối, tách số thứ tự từ mã
        $last_tacgia_id = $last_tacgia ? (int) filter_var($last_tacgia['ma_tac_gia'], FILTER_SANITIZE_NUMBER_INT) : 0;
    
        // Tăng giá trị ID
        $new_tacgia_id = 'T' . ($last_tacgia_id + 1);
    
        // Kiểm tra xem ID mới có tồn tại không, nếu có, lặp tiếp để tìm ID hợp lệ
        while (TacGia::where('ma_tac_gia', $new_tacgia_id)->exists()) {
            $last_tacgia_id++;
            $new_tacgia_id = 'T' . $last_tacgia_id;
        }
    
        // Trả về ID mới
        return $new_tacgia_id;
    }



    public function createtacgia()
{
    $data = $this->filtertacgiaData($_POST);
    $model_errors = TacGia::validate($data);
    if (empty($data['ma_tac_gia'])) {
        $data['ma_tac_gia'] = $this->createNewtacgiaId();
    }

    // Nếu không có lỗi, lưu loại sản phẩm mới
    if (empty($model_errors)) {
        $tacgia = new TacGia();
        $tacgia->fill($data);
        $tacgia->save();
        redirect('/managetacgia');
    }

    // Lưu giá trị form vào session nếu có lỗi
    $this->saveFormValues($_POST);

    // Lưu lỗi vào session và chuyển hướng lại trang tạo mới
    redirect('/createtacgia', ['errors' => $model_errors]);
}

protected function filtertacgiaData(array $data)
{
    return [
        'ma_tac_gia' => $data['ma_tac_gia'] ?? null,
        'ten_tac_gia' => $data['ten_tac_gia'] ?? null,
        // 'mo_ta_loai_sach' => $data['mo_ta_loai_sach'] ?? null, // Thêm trường mô tả loại sách (nếu có)
    ];
}
// đóng tạo tác giả  ///////////////////////////////////////////////////////////////////

       // xóa tác giả /////////////////////////////////
// public function deletetacgia($tacgiaId)
//     {
//         $tacgia = TacGia::where('ma_tac_gia', '=', $tacgiaId)->first();
//         $tacgia->delete();
//         redirect('/managetacgia');
//     } 

// đóng xóa tác giả /////////////////////////////////////

// update tác giả
public function showUpdatetacgiaPage($tacgia_id)
    {
        // Lấy thông tin loại sách theo ID
        $tacgia = TacGia::where('ma_tac_gia', '=', $tacgia_id)->first();
    
        // Gửi dữ liệu tới trang 'manage/updatetype'
        $this->sendPage('manage/updatetacgia', [
            'tacgia' => $tacgia,  // Quan trọng
            'errors' => session_get_once('errors_update'),
            'old_value' => $this->getSavedUpdateFormValues()
        ]);
    }
    // public function updatetacgia($tacgiaid)
    // {
    //     $tacgia = TacGia::where('ma_tac_gia', '=', $tacgiaid)->first();
    //     $data = $this->filtertacgiaData($_POST);
    //     $model_errors = TacGia::validate($data);

        
    //     //Không cập nhật lại Mã Sản Phẩm và Created_at và Số lượng sp đã bán
    //     unset($data['ma_tac_gia']);
    //     unset($data['created_at']);
    //     unset($data['sold']);


    //     if (empty($model_errors)) {
    //         $tacgia->update($data);
    //         $tacgia->save();
    //         redirect('/managetacgia');
    //     }
    //     $this->saveUpdateFormValues($_POST);

    //     redirect('/manage/' . $tacgia, ['errors_update' => $model_errors]);
    // } 

// đóng tác gải ////////////////////////////////////////

// nhà xuất bản /////////////////////////////////////
public function getAllNXB()
{
    // Lấy tất cả dữ liệu từ bảng tác giả
    $nhaxuatban = NhaXuatBan::select('ma_nxb', 'ten_nxb','sdt_nxb','dia_chi_nxb')
                             ->orderBy('ma_nxb', 'ASC')
                             ->get();
    
    // Gửi dữ liệu đến view
    $this->sendPage('manage/managenhaxuatban', [
        'nhaxuatban' => $nhaxuatban
    ]);

}
public function showCreateNXBPage()
    {
        $new_Nxb_id = $this->createNewNXBid();

        // $product_type = ProductType::all();

        $this->sendPage('manage/createNXB', [
            'errors' => session_get_once('errors'),
            'old' => $this->getSavedFormValues(),
            'ma_nxb'=> $new_Nxb_id,
            'nhaxuatban' => $new_Nxb_id,
        ]);
        
    }

    // public function createNewNXBid()
    // {
    //     $last_NXB = NhaXuatBan::latest('ma_nxb')->first();
    //     if ($last_NXB) {
    //         $last_NXB_id = $last_NXB ? (int)filter_var($last_NXB['ma_nxb'], FILTER_SANITIZE_NUMBER_INT) : 0;    //Bỏ "SP" trong mã sản phẩm và chuyển về kiểu int 
    //     }else {
    //         $last_NXB_id = 0; // Nếu không có sản phẩm nào, bắt đầu từ 0
    //     }
    //     $new_NXB_id = 'N' . ($last_NXB_id + 1);
    //     $last_NXB_id ++;
    //     $new_NXB_id = "N" . $last_NXB_id ;
    //     $last_NXB['ma_nxb'] = $new_NXB_id ;
    //     return $last_NXB;
    // }

    public function createNewNXBid()
    {
        // Lấy bản ghi có mã lớn nhất
        $last_NXB = NhaXuatBan::latest('ma_nxb')->first();
    
        // Nếu có bản ghi cuối, tách số thứ tự từ mã
        $last_NXB_id = $last_NXB ? (int) filter_var($last_NXB['ma_nxb'], FILTER_SANITIZE_NUMBER_INT) : 0;
    
        // Tăng giá trị ID
        $new_NXB_id = 'N' . ($last_NXB_id + 1);
    
        // Kiểm tra xem ID mới có tồn tại không, nếu có, lặp tiếp để tìm ID hợp lệ
        while (NhaXuatBan::where('ma_nxb', $new_NXB_id)->exists()) {
            $last_NXB_id++;
            $new_NXB_id = 'N' . $last_NXB_id;
        }
    
        // Trả về ID mới
        return $new_NXB_id;
    }
    public function createNXB()
{
    $data = $this->filterNXBData($_POST);
    $model_errors = NhaXuatBan::validate($data);
    if (empty($data['ma_nxb'])) {
        $data['ma_nxb'] = $this->createNewtacgiaId();
    }

    // Nếu không có lỗi, lưu loại sản phẩm mới
    if (empty($model_errors)) {
        $nhaxuatban = new NhaXuatBan();
        $nhaxuatban->fill($data);
        $nhaxuatban->save();
        redirect('/managenhaxuatban');
    }

    // Lưu giá trị form vào session nếu có lỗi
    $this->saveFormValues($_POST);

    // Lưu lỗi vào session và chuyển hướng lại trang tạo mới
    redirect('/createNXB', ['errors' => $model_errors]);
}

protected function filterNXBData(array $data)
{
    return [
        'ma_nxb' => $data['ma_nxb'] ?? null,
        'ten_nxb' => $data['ten_nxb'] ?? null,
        'sdt_nxb' => $data['sdt_nxb'] ?? null,
        'dia_chi_nxb' => $data['dia_chi_nxb'] ?? null,
    ];
}

/// xóa nhà xuất bản //
// public function deleteNXB($NXBid)
//     {
//         $nhaxuatban = NhaXuatBan::where('ma_nxb', '=', $NXBid)->first();
//         $nhaxuatban->delete();
//         redirect('/managenhaxuatban');
//     } 
// đóng xóa nhà xuất bản

// update nhà xuất bản
public function showUpdateNXBPage($NXB_id)
    {
        // Lấy thông tin loại sách theo ID
        $nhaxuatban = NhaXuatBan::where('ma_nxb', '=', $NXB_id)->first();
    
        // Gửi dữ liệu tới trang 'manage/updatetype'
        $this->sendPage('manage/updateNXB', [
            'nhaxuatban' => $nhaxuatban,  // Quan trọng
            'errors' => session_get_once('errors_update'),
            'old_value' => $this->getSavedUpdateFormValues()
        ]);
    }





























}

