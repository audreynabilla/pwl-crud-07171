<?php
require_once __DIR__ . '/../models/UserModel.php';

class AuthController
{
    private $userModel;

    public function __construct(PDO $conn)
    {
        $this->userModel = new UserModel($conn);
    }

    public function login()
    {
        if (isLoggedIn()) {
            redirectTo('index.php?url=produk/index');
        }

        $data = [
            'title' => 'Login',
            'error' => '',
            'username' => $_COOKIE['username'] ?? '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $data['error'] = 'Akses tidak sah (CSRF).';
                $this->view('auth/login', $data);
                return;
            }

            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $data['username'] = $username;

            $user = $this->userModel->verifyLogin($username, $password);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                if (isset($_POST['remember'])) {
                    setcookie('username', $user['username'], time() + (86400 * 7), '/crud_mvc/');
                } else {
                    setcookie('username', '', time() - 3600, '/crud_mvc/');
                }

                redirectTo('index.php?url=produk/index');
            }

            $data['error'] = 'Username atau password salah!';
        }

        $this->view('auth/login', $data);
    }

    public function register()
    {
        if (isLoggedIn()) {
            redirectTo('index.php?url=produk/index');
        }

        $data = [
            'title' => 'Register',
            'error' => '',
            'success' => '',
            'username' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $data['error'] = 'Akses tidak sah (CSRF).';
                $this->view('auth/register', $data);
                return;
            }

            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $confirm = trim($_POST['confirm'] ?? '');
            $data['username'] = $username;

            if ($username === '' || $password === '' || $confirm === '') {
                $data['error'] = 'Semua field wajib diisi!';
            } elseif ($password !== $confirm) {
                $data['error'] = 'Konfirmasi password tidak sama!';
            } elseif ($this->userModel->findByUsername($username)) {
                $data['error'] = 'Username sudah digunakan!';
            } elseif ($this->userModel->createUser($username, $password)) {
                $data['success'] = 'Akun berhasil dibuat! Silakan login.';
                $data['username'] = '';
            } else {
                $data['error'] = 'Terjadi kesalahan saat membuat akun!';
            }
        }

        $this->view('auth/register', $data);
    }

    public function logout()
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
        setcookie('username', '', time() - 3600, '/crud_mvc/');
        redirectTo('index.php?url=auth/login');
    }

    private function view($view, $data = [])
    {
        extract($data);
        require __DIR__ . '/../views/' . $view . '.php';
    }
}
