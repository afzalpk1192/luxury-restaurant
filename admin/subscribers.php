<?php
/**
 * Elite Estates - Admin Newsletter Subscribers
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/Models/Admin.php';
require_once __DIR__ . '/../src/Models/Subscriber.php';

use EliteEstates\Models\Admin;
use EliteEstates\Models\Subscriber;

Admin::requireAuth();

$subscriberModel = new Subscriber();

// Handle CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $subscribers = $subscriberModel->getAll(5000);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=elite_estates_subscribers_' . date('Y-m-d') . '.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID', 'Email Address', 'Status', 'Subscribed Date']);
    foreach ($subscribers as $s) {
        fputcsv($out, [$s['id'], $s['email'], $s['status'], $s['subscribed_at']]);
    }
    fclose($out);
    exit;
}

$pageTitle = 'VIP Newsletter Subscribers';
require_once __DIR__ . '/header.php';

$subscribers = $subscriberModel->getAll(200);
?>

<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-800 pb-6">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Newsletter & Private Portfolio Subscribers</h1>
            <p class="text-slate-400 text-sm mt-1">High-net-worth individuals subscribed to exclusive acquisition updates.</p>
        </div>
        <div class="flex gap-3">
            <a href="subscribers.php?export=csv" class="bg-slate-800 hover:bg-slate-700 text-white px-5 py-2.5 rounded-xl font-semibold text-sm transition border border-slate-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export CSV List
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-800/80 text-xs uppercase font-semibold text-slate-400 border-b border-slate-700/60">
                    <tr>
                        <th class="p-4">Subscriber Email</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Date Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php if (empty($subscribers)): ?>
                    <tr>
                        <td colspan="3" class="p-12 text-center text-slate-500">
                            No newsletter subscribers registered yet.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($subscribers as $s): ?>
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="p-4 font-medium text-white flex items-center gap-2">
                            <span class="text-amber-500">✉</span>
                            <?= htmlspecialchars($s['email']) ?>
                        </td>
                        <td class="p-4">
                            <span class="text-[11px] font-bold uppercase px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                <?= htmlspecialchars($s['status']) ?>
                            </span>
                        </td>
                        <td class="p-4 text-xs text-slate-400 font-mono">
                            <?= date('M d, Y - h:i A', strtotime($s['subscribed_at'])) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/footer.php'; ?>
