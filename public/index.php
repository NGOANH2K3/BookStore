<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require __DIR__ . '/../bootstrap.php';

define('APPNAME', 'BookStore');

session_start();

$router = new \Bramus\Router\Router();

// Auth routes
$router->post('/logout', '\App\Controllers\Auth\LoginController@logout');
$router->get('/register', '\App\Controllers\Auth\RegisterController@showRegisterForm');
$router->post('/register', '\\App\Controllers\Auth\RegisterController@register');
$router->get('/login', '\App\Controllers\Auth\LoginController@showLoginForm');
$router->post('/login', '\App\Controllers\Auth\LoginController@login');

// Product routes
$router->get('/', '\App\Controllers\HomeController@index');
$router->get('/home', '\App\Controllers\HomeController@index');
$router->get('/about', '\App\Controllers\HomeController@about');
$router->post('/search', '\App\Controllers\HomeController@search');

$router->get('/product_all', '\App\Controllers\ProductController@product');
$router->post('/product', '\App\Controllers\ProductController@productOfType');
$router->get('/detail', '\App\Controllers\ProductController@detailProduct');

$router->get('/cart', '\App\Controllers\CartController@cart');
$router->post('/addCart', '\App\Controllers\CartController@addCart');
$router->post('/del', '\App\Controllers\CartController@del');
$router->get('/delCart', '\App\Controllers\CartController@delCart');
$router->post('/pay', '\App\Controllers\CartController@pay');

// Bill routes
$router->get('/payHistory', '\App\Controllers\BillController@payHistory');
$router->get('/detailBill', '\App\Controllers\BillController@detailBill');
$router->post('/cancleBill/([0-9]+)', '\App\Controllers\BillController@cancel');
$router->post('/recieved/([0-9]+)', '\App\Controllers\BillController@recieved');

// Management Products routes
$router->get('/manageProduct', '\App\Controllers\Manage\ManagementController@getAllProducts');
$router->post('/manageProduct', '\App\Controllers\Manage\ManagementController@sortAllProducts');

$router->get('/manageProductType', '\App\Controllers\Manage\ManagementController@getAllProductsType');
$router->post('/manageProductType', '\App\Controllers\Manage\ManagementController@sortAllProductsType');

$router->get('/create', '\App\Controllers\Manage\ManagementController@showCreatePage');
$router->post('/createProduct', '\App\Controllers\Manage\ManagementController@createProduct');

$router->get('/createtype', '\App\Controllers\Manage\ManagementController@showCreateTypePage');
$router->post('/createProducttype', '\App\Controllers\Manage\ManagementController@createProducttype');

// $router->get('/manage/([A-Za-z0-9]+)', function ($param) {
//     if (is_numeric($param)) {
//         // Gọi trực tiếp phương thức showUpdatePage
//         (new \App\Controllers\Manage\ManagementController())->showUpdatePage($param);
//     } else {
//         // Gọi trực tiếp phương thức showUpdatetypePage
//         (new \App\Controllers\Manage\ManagementController())->showUpdatetypePage($param);
//     }
// });
// $router->post('/manage/update/([A-Za-z0-9]+)', function ($param) {
//     if (is_numeric($param)) {
//         // Gọi trực tiếp phương thức update cho sản phẩm
//         (new \App\Controllers\Manage\ManagementController())->update($param, 'product');
//     } else {
//         // Gọi trực tiếp phương thức update cho loại sản phẩm
//         (new \App\Controllers\Manage\ManagementController())->update($param, 'product_type');
//     }
// });
// $router->post('/manage/updatetype/([A-Za-z0-9]+)', function ($param) {
//     if (is_numeric($param)) {
//         // Gọi trực tiếp phương thức update cho sản phẩm
//         (new \App\Controllers\Manage\ManagementController())->update($param, 'product');
//     } else {
//         // Gọi trực tiếp phương thức update cho loại sản phẩm
//         (new \App\Controllers\Manage\ManagementController())->update($param, 'product_type');
//     }
// });
// Route GET cho trang cập nhật
$router->get('/manage/([A-Za-z0-9]+)', function ($param) {
    if (str_starts_with($param, 'T')) {
        // Gọi phương thức cho tác giả
        (new \App\Controllers\Manage\ManagementController())->showUpdatetacgiaPage($param);
    } elseif (str_starts_with($param, 'N')) {
        // Gọi phương thức cho Nhà xuất bản
        (new \App\Controllers\Manage\ManagementController())->showUpdateNXBPage($param);
    }
    elseif (is_numeric($param)) {
        // Gọi phương thức cho sản phẩm
        (new \App\Controllers\Manage\ManagementController())->showUpdatePage($param);
    } else {
        // Gọi phương thức cho loại sản phẩm
        (new \App\Controllers\Manage\ManagementController())->showUpdatetypePage($param);
    }
});

// Route POST cho việc cập nhật thông tin
$router->post('/manage/update/([A-Za-z0-9]+)', function ($param) {
    if (str_starts_with($param, 'T')) {
        // Cập nhật tác giả
        (new \App\Controllers\Manage\ManagementController())->update($param, 'tacgia');
    } elseif (str_starts_with($param, 'N')) {
        // Cập nhật tác giả
        (new \App\Controllers\Manage\ManagementController())->update($param, 'nhaxuatban');
    }elseif (is_numeric($param)) {
        // Cập nhật sản phẩm
        (new \App\Controllers\Manage\ManagementController())->update($param, 'product');
    } else {
        // Cập nhật loại sản phẩm
        (new \App\Controllers\Manage\ManagementController())->update($param, 'product_type');
    }
});

// Route POST cho việc cập nhật thông tin loại sản phẩm
$router->post('/manage/updatetype/([A-Za-z0-9]+)', function ($param) {
    if (str_starts_with($param, 'T')) {
        // Cập nhật tác giả
        (new \App\Controllers\Manage\ManagementController())->update($param, 'tacgia');
    }elseif (str_starts_with($param, 'N')) {
        // Cập nhật tác giả
        (new \App\Controllers\Manage\ManagementController())->update($param, 'nhaxuatban');
    }elseif (is_numeric($param)) {
        // Cập nhật sản phẩm
        (new \App\Controllers\Manage\ManagementController())->update($param, 'product');
    } else {
        // Cập nhật loại sản phẩm
        (new \App\Controllers\Manage\ManagementController())->update($param, 'product_type');
    }
});
$router->post('/manage/updatetacgia/([A-Za-z0-9]+)', function ($param) {
    if (str_starts_with($param, 'T')) {
        // Cập nhật tác giả
        (new \App\Controllers\Manage\ManagementController())->update($param, 'tacgia');
    }elseif (str_starts_with($param, 'N')) {
        // Cập nhật tác giả
        (new \App\Controllers\Manage\ManagementController())->update($param, 'nhaxuatban');
    }elseif (is_numeric($param)) {
        // Cập nhật sản phẩm
        (new \App\Controllers\Manage\ManagementController())->update($param, 'product');
    } else {
        // Cập nhật loại sản phẩm
        (new \App\Controllers\Manage\ManagementController())->update($param, 'product_type');
    }
});
$router->post('/manage/updateNXB/([A-Za-z0-9]+)', function ($param) {
    if (str_starts_with($param, 'T')) {
        // Cập nhật tác giả
        (new \App\Controllers\Manage\ManagementController())->update($param, 'tacgia');
    }elseif (str_starts_with($param, 'N')) {
        // Cập nhật tác giả
        (new \App\Controllers\Manage\ManagementController())->update($param, 'nhaxuatban');
    }elseif (is_numeric($param)) {
        // Cập nhật sản phẩm
        (new \App\Controllers\Manage\ManagementController())->update($param, 'product');
    } else {
        // Cập nhật loại sản phẩm
        (new \App\Controllers\Manage\ManagementController())->update($param, 'product_type');
    }
});

// $router->post('/manage/update/([A-Za-z0-9]+)', '\App\Controllers\Manage\ManagementController@update');
// $router->post('/manage/updatetype/([A-Za-z0-9]+)', '\App\Controllers\Manage\ManagementController@update');
// $router->get('/manage/([A-Za-z0-9]+)', '\App\Controllers\Manage\ManagementController@showUpdatePage');
// $router->get('/manage/([A-Za-z0-9]+)', '\App\Controllers\Manage\ManagementController@showUpdatetypePage');

$router->post('/manage/delete/([A-Za-z0-9]+)', '\App\Controllers\Manage\ManagementController@delete');
$router->post('/manage/deletetype/([A-Za-z0-9]+)', '\App\Controllers\Manage\ManagementController@delete');

// Management Bill routes
$router->get('/manageBill', '\App\Controllers\Manage\ManagementController@manageBill');
$router->get('/manageDetailBill', '\App\Controllers\Manage\ManagementController@manageDetailBill');
$router->post('/manage/deleteBill/([0-9]+)', '\App\Controllers\Manage\ManagementController@cancelBill');
$router->post('/manage/sending/([0-9]+)', '\App\Controllers\Manage\ManagementController@send');
$router->post('/manageBill', '\App\Controllers\Manage\ManagementController@sortBill');

// Management Users routes
$router->get('/users', '\App\Controllers\Manage\ManagementController@getAllUsers');
$router->post('/users', '\App\Controllers\Manage\ManagementController@sortAllUsers');
$router->get('/userInfo', '\App\Controllers\Manage\ManagementController@userInfo');
$router->post('/updateUser', '\App\Controllers\Manage\ManagementController@updateUser');
$router->get('/passChange', '\App\Controllers\Manage\ManagementController@passChange');
$router->post('/updatePass', '\App\Controllers\Manage\ManagementController@updatePass');

//Management tac gia routes
$router->get('/managetacgia', '\App\Controllers\Manage\ManagementController@getAlltacgia');
$router->post('/managetacgia', '\App\Controllers\Manage\ManagementController@sortAlltacgia');

$router->get('/createtacgia', '\App\Controllers\Manage\ManagementController@showCreatetacgiaPage');
$router->post('/createtacgia2', '\App\Controllers\Manage\ManagementController@createtacgia');

$router->post('/manage/deletetacgia/([A-Za-z0-9]+)', '\App\Controllers\Manage\ManagementController@delete');

//Management nha xuat ban routes
$router->get('/managenhaxuatban', '\App\Controllers\Manage\ManagementController@getAllNXB');
$router->post('/managenhaxuatban', '\App\Controllers\Manage\ManagementController@sortAllNXB');

$router->get('/createNXB', '\App\Controllers\Manage\ManagementController@showCreateNXBPage');
$router->post('/createNXB2', '\App\Controllers\Manage\ManagementController@createNXB');

$router->post('/manage/deleteNXB/([A-Za-z0-9]+)', '\App\Controllers\Manage\ManagementController@delete');

$router->run();
