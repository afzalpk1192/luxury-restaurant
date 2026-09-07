<?php
/**
 * Elite Estates - Admin Layout Header
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/Models/Admin.php';
require_once __DIR__ . '/../src/Models/Inquiry.php';

use EliteEstates\Config\AppConfig;
use EliteEstates\Models\Admin;
use EliteEstates\Models\Inquiry;

Admin::requireAuth();

$inquiryModel = new Inquiry();
$unreadInquiries = $inquiryModel->countNew();

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Executive Dashboard' ?> | <?= htmlspecialchars(AppConfig::APP_NAME) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .serif { font-family: 'Playfair Display', serif; }
        .gold-gradient { background: linear-gradient(135deg, #c5a059 0%, #947a43 100%); }
        .bg-navy { background-color: #0f172a; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col">

    <!-- Top Admin Navigation Bar -->
    <header class="bg-slate-900 border-b border-slate-800 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                
                <div class="flex items-center gap-8">
                    <!-- Brand -->
                    <a href="index.php" class="flex items-center gap-2">
                        <div class="w-10 h-10 gold-gradient rounded-lg flex items-center justify-center text-white shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                        </div>
                        <span class="text-xl font-bold tracking-tighter">ELITE<span class="text-amber-500">ESTATES</span> <span class="text-xs font-mono uppercase bg-slate-800 text-slate-400 px-2 py-0.5 rounded border border-slate-700 ml-1">ADMIN</span></span>
                    </a>

                    <!-- Nav Links -->
                    <nav class="hidden md:flex space-x-1">
                        <a href="index.php" class="px-4 py-2 rounded-xl text-sm font-medium transition <?= $currentPage === 'index.php' ? 'bg-slate-800 text-amber-400 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' ?>">
                            Overview
                        </a>
                        <a href="properties.php" class="px-4 py-2 rounded-xl text-sm font-medium transition <?= in_array($currentPage, ['properties.php', 'property-form.php']) ? 'bg-slate-800 text-amber-400 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' ?>">
                            Listings
                        </a>
                        <a href="inquiries.php" class="px-4 py-2 rounded-xl text-sm font-medium transition relative flex items-center gap-2 <?= $currentPage === 'inquiries.php' ? 'bg-slate-800 text-amber-400 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' ?>">
                            Inquiries
                            <?php if ($unreadInquiries > 0): ?>
                            <span class="bg-amber-500 text-navy text-[10px] font-bold px-1.5 py-0.5 rounded-full"><?= $unreadInquiries ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="subscribers.php" class="px-4 py-2 rounded-xl text-sm font-medium transition <?= $currentPage === 'subscribers.php' ? 'bg-slate-800 text-amber-400 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' ?>">
                            Subscribers
                        </a>
                    </nav>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-4">
                    <a href="../index.php" target="_blank" class="hidden sm:flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-amber-400 transition bg-slate-800/60 px-3 py-2 rounded-lg border border-slate-700/60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        View Live Site
                    </a>

                    <div class="flex items-center gap-3 pl-2 border-l border-slate-800">
                        <div class="text-right hidden sm:block">
                            <div class="text-xs font-semibold text-white"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Administrator') ?></div>
                            <div class="text-[10px] text-amber-500 font-mono"><?= htmlspecialchars($_SESSION['admin_role'] ?? 'superadmin') ?></div>
                        </div>
                        <a href="logout.php" title="Sign Out" class="p-2 text-slate-400 hover:text-rose-400 transition rounded-lg hover:bg-slate-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
