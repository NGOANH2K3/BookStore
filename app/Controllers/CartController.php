<?php

namespace App\Controllers;

use App\Models\User;
use App\Controllers\Controller;
use App\Models\Cart;
use App\Models\BillDetail;
use App\Models\Bill;
use App\Models\Product;
use Illuminate\Support\Facades\Date;
use App\SessionGuard as Guard;
use PDOException;

class CartController extends Controller
{
	public function addCart()
	{
		$user_id = User::where('email', Guard::user()->email)->first()->id;
		try {
			Cart::create([
				'ma_sach' => $_POST['masp'],
				'id' => $user_id,
				'so_luong_sach' => $_POST['so-luong']
			]);
		} catch (PDOException $pe) {
			$soluong = Cart::where('ma_sach', $_POST['masp'])->where('id', $user_id)->first()->so_luong_sach;
			$gio = Cart::join('sach', 'sach.ma_sach', '=', 'GioHang.ma_sach')->where('sach.ma_sach', $_POST['masp'])->first();
			if ($gio->so_luong_sach < $gio->so_luong) {
				Cart::where('ma_sach', $_POST['masp'])->where('id', $user_id)->update([
					'so_luong_sach' => ($soluong + $_POST['so-luong'])
				]);
				redirect('/cart');
					
			}
		}
		redirect('/cart');
	}


	public function del()
	{
		$user_id = User::where('email', Guard::user()->email)->first()->id;
		$soluong = Cart::where('ma_sach', $_POST['masp'])->where('id', $user_id)->first()->so_luong_sach;
		if ($soluong > 1) {
			Cart::where('ma_sach', $_POST['masp'])->where('id', $user_id)->update([
				'so_luong_sach' => ($soluong - $_POST['so-luong'])
			]);
		}
		redirect('/cart');
	}

	public function cart()
	{
		if (!Guard::isUserLoggedIn()) {
			redirect('/login');
		}
		$user_id = User::where('email', Guard::user()->email)->first()->id;
		$this->sendPage('cart/cart', ['carts' => Cart::join('sach', 'sach.ma_sach', '=', 'giohang.ma_sach')->where('id', $user_id)->get()]);
	}


	public function delCart()
	{
		$user= User::where('email', Guard::user()->email)->first();
		Cart::where('ma_sach', $_GET['masp'])->where('id', $user->id)->delete();
		redirect('/cart');
	}

	public function pay()
	{
		$khach = User::where('email', Guard::user()->email)->first();
		$gio = Cart::join('sach', 'sach.ma_sach', '=', 'giohang.ma_sach')->get();

		foreach ($gio as $gh) {
			foreach (Product::where('ma_sach', $gh->ma_sach)->get() as $prd) {
				$prd->update(['so_luong' => ($gh->so_luong - $gh->so_luong_sach),
								'sold' => ($gh->sold + $gh->so_luong_sach)]);
			}
		}
		
		$hoadon=Bill::create([
			'ngay_lap' => Date::now(7),
			'tong_tien' => $_POST['tong-tien'],
			'trang_thai' => 'processing',
			'trang_thai_thanh_toan' => 'Chưa thanh toán',
			'id'=> $khach->id,
			'sdt' => $_POST['phone'],
			'ten_khach_hang'=> $khach->name,
			'dia_chi'=>$_POST['address'],
			'ma_khuyen_mai' => ''
		]);

		foreach (Cart::where('id', $khach->id)->get() as $cart) {
			BillDetail::create([
				'ma_hoa_don'=>$hoadon->ma_hoa_don,
				'ma_sach'=>$cart->ma_sach,
				'so_luong_sp'=>$cart->so_luong_sach
			]);

			Cart::where('ma_sach', $cart->ma_sach)->where('id', $cart->id)->delete();
			$_SESSION['subtotal'] = 0;
		}
		$this->sendPage('cart/pay', ['bill' =>BillDetail::join('sach', 'sach.ma_sach', '=', 'chitiethoadon.ma_sach')->where('ma_hoa_don', $hoadon->ma_hoa_don)->get(),
		'billdetail' => Bill::where('ma_hoa_don', $hoadon->ma_hoa_don)->get()]);
	}
}
