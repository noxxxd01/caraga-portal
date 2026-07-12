<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/install.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $db->prepare("SELECT id, username, password_hash FROM `users` WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — DICT Caraga Region</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen flex items-center justify-center bg-slate-50 font-sans">
    <div class="w-full max-w-sm bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3" style="background-color:#0F172A;">
                <i class="fa-solid fa-shield-halved text-white text-lg"></i>
            </div>
            <h1 class="text-lg font-extrabold text-slate-900">DICT Caraga Region</h1>
            <p class="text-xs text-slate-500">Training Management Division Portal</p>
        </div>

        <?php if ($error): ?>
        <div class="mb-4 p-3 bg-rose-50 border border-rose-100 text-rose-700 text-xs font-semibold rounded-lg">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Username</label>
                <input type="text" name="username" required autofocus class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Password</label>
                <input type="password" name="password" required class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <button type="submit" class="w-full py-2.5 bg-blue-800 hover:bg-blue-900 text-white rounded-lg text-xs font-bold transition-all">
                Sign In
            </button>
        </form>
    </div>
</body>
</html>