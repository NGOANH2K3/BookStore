<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Controllers\Controller;
use App\SessionGuard as Guard;

class LoginController extends Controller
{
	public function showLoginForm()
	{
		if (Guard::isUserLoggedIn()) {
			redirect('/home');
		}

		$data = [
			'messages' => session_get_once('messages'),
			'old' => $this->getSavedFormValues(),
			'errors' => session_get_once('errors'),
			'error_wrong' => session_get_once('error_wrong')
		];

		$this->sendPage('auth/login', $data);
	}

	public function login()
	{
		$user_credentials = $this->filterUserCredentials($_POST);

		$errors = [];
		$error_wrong = [];
		$user = User::where('email', $user_credentials['email'])->first();

		if (!$user) {
			// Người dùng không tồn tại...
			$error_wrong['email_password'] = 'Email hoặc mật khẩu không đúng.';
		} else if ((!$user_credentials['email']) && (!$user_credentials['password'])) {
			// Chưa nhập email và mật khẩu...
			$errors['email'] = 'Bạn chưa nhập email.';
			$errors['password'] = 'Bạn chưa nhập mật khẩu.';
		} else if (!$user_credentials['email']) {
			// Chưa nhập email...
			$errors['email'] = 'Bạn chưa nhập email.';
		} else if (!$user_credentials['password']) {
			// Chưa nhập mật khẩu...
			$errors['password'] = 'Bạn chưa nhập mật khẩu.';
		} else if (Guard::login($user, $user_credentials)) {
			// Đăng nhập thành công...
			redirect('/home');
		} else {
			// Mật khẩu không đúng...
			$error_wrong['email_password'] = 'Mật khẩu không đúng.';
		}


		// Đăng nhập không thành công: lưu giá trị trong form, trừ password
		$this->saveFormValues($_POST, ['password']);
		redirect('/login', [
			'errors' => $errors,
			'error_wrong' => $error_wrong
		]);
	}

	public function logout()
	{
		Guard::logout();
		redirect('/login');
	}

	protected function filterUserCredentials(array $data)
	{
		return [
			'email' => filter_var($data['email'], FILTER_VALIDATE_EMAIL),
			'password' => $data['password'] ?? null
		];
	}
}
