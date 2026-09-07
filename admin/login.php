<?php
/**
 * Elite Estates - Admin Login Portal
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/Models/Admin.php';

use EliteEstates\Config\AppConfig;
use EliteEstates\Models\Admin;

AppConfig::initSession();

// Redirect if already logged in
if (Admin::checkAuth()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username/email and password.';
    } else {
        try {
            $adminModel = new Admin();
            $admin = $adminModel->authenticate($username, $password);
            if ($admin) {
                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid credentials. Please verify your login details.';
            }
        } catch (Throwable $e) {
            $error = 'Database Error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Broker & Executive Portal | <?= htmlspecialchars(AppConfig::APP_NAME) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .serif { font-family: 'Playfair Display', serif; }
        .gold-gradient { background: linear-gradient(135deg, #c5a059 0%, #947a43 100%); }
        .bg-navy { background-color: #0f172a; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="w-14 h-14 gold-gradient rounded-2xl mx-auto flex items-center justify-center text-white mb-4 shadow-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
            </div>
            <h1 class="text-3xl font-bold tracking-tight">ELITE<span class="text-amber-500">ESTATES</span></h1>
            <p class="text-slate-400 text-sm mt-1">Management & Executive Console</p>
        </div>

        <!-- Login Card -->
        <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/60 p-8 rounded-3xl shadow-2xl">
            <?php if (!empty($error)): ?>
            <div class="bg-rose-900/50 border border-rose-500/50 text-rose-200 text-sm p-4 rounded-xl mb-6 flex items-start gap-3">
                <span class="text-rose-400 font-bold">⚠</span>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
            <?php endif; ?>

            <form method="POST" action="login.php" class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Username or Email</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? 'admin@eliteestates.com') ?>" required
                           class="w-full bg-slate-900/90 border border-slate-700 rounded-xl p-3.5 text-sm text-white placeholder-slate-500 outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Password</label>
                    </div>
                    <input type="password" name="password" placeholder="••••••••••••" required
                           class="w-full bg-slate-900/90 border border-slate-700 rounded-xl p-3.5 text-sm text-white placeholder-slate-500 outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full gold-gradient text-white font-bold py-3.5 rounded-xl hover:opacity-95 transition shadow-lg text-sm">
                        Authenticate Access
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-700/60 text-xs text-slate-400 text-center">
                <span>Default credentials after migration:</span><br>
                <code class="text-amber-400 font-mono">admin@eliteestates.com</code> / <code class="text-amber-400 font-mono">Admin@12345</code>
            </div>
        </div>

        <div class="text-center mt-6">
            <a href="../index.php" class="text-slate-400 hover:text-white text-xs transition flex items-center justify-center gap-1.5">
                &larr; Back to Public Luxury Portfolio
            </a>
        </div>
    </div>

</body>
</html>
