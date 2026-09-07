<?php
/**
 * Elite Estates - Admin Inquiries & Leads Management
 */

declare(strict_types=1);

$pageTitle = 'Client Inquiries & Private Viewings';
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/../src/Models/Inquiry.php';

use EliteEstates\Models\Inquiry;

$inquiryModel = new Inquiry();
$message = '';

// Handle Status Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $id = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? 'new';
    if ($id > 0) {
        $inquiryModel->updateStatus($id, $status);
        $message = "Inquiry #{$id} status updated to " . strtoupper($status);
    }
}

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $inquiryModel->delete($id);
        $message = "Inquiry #{$id} removed.";
    }
}

$statusFilter = $_GET['status'] ?? null;
$inquiries = $inquiryModel->getAll($statusFilter, 100);
?>

<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-800 pb-6">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Client Inquiries & Leads</h1>
            <p class="text-slate-400 text-sm mt-1">Review private viewing requests and high-value prospective buyer messages.</p>
        </div>
    </div>

    <!-- Message Alert -->
    <?php if ($message): ?>
    <div class="bg-emerald-900/40 border border-emerald-500/50 text-emerald-200 text-sm p-4 rounded-xl flex items-center justify-between">
        <span><?= htmlspecialchars($message) ?></span>
    </div>
    <?php endif; ?>

    <!-- Filter Bar -->
    <div class="flex gap-2 border-b border-slate-800 pb-4 text-xs font-semibold">
        <a href="inquiries.php" class="px-3 py-1.5 rounded-lg transition <?= empty($statusFilter) ? 'bg-amber-500 text-navy font-bold' : 'text-slate-400 hover:text-white bg-slate-900' ?>">All Inquiries</a>
        <a href="inquiries.php?status=new" class="px-3 py-1.5 rounded-lg transition <?= $statusFilter === 'new' ? 'bg-amber-500 text-navy font-bold' : 'text-slate-400 hover:text-white bg-slate-900' ?>">New Leads</a>
        <a href="inquiries.php?status=in_review" class="px-3 py-1.5 rounded-lg transition <?= $statusFilter === 'in_review' ? 'bg-amber-500 text-navy font-bold' : 'text-slate-400 hover:text-white bg-slate-900' ?>">In Review</a>
        <a href="inquiries.php?status=contacted" class="px-3 py-1.5 rounded-lg transition <?= $statusFilter === 'contacted' ? 'bg-amber-500 text-navy font-bold' : 'text-slate-400 hover:text-white bg-slate-900' ?>">Contacted</a>
        <a href="inquiries.php?status=closed" class="px-3 py-1.5 rounded-lg transition <?= $statusFilter === 'closed' ? 'bg-amber-500 text-navy font-bold' : 'text-slate-400 hover:text-white bg-slate-900' ?>">Closed</a>
    </div>

    <!-- Inquiries Cards / Table -->
    <div class="space-y-4">
        <?php if (empty($inquiries)): ?>
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-12 text-center text-slate-500 text-sm">
            No inquiries currently found matching this criteria.
        </div>
        <?php else: ?>
        <?php foreach ($inquiries as $inq): ?>
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-sm hover:border-slate-700 transition">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-slate-800 pb-4 mb-4">
                <div>
                    <div class="flex items-center gap-3">
                        <span class="text-lg font-bold text-white"><?= htmlspecialchars($inq['full_name']) ?></span>
                        <span class="text-xs text-slate-500 font-mono"><?= date('M d, Y - h:i A', strtotime($inq['created_at'])) ?></span>
                    </div>
                    <div class="flex items-center gap-4 text-xs text-slate-400 mt-1">
                        <a href="mailto:<?= htmlspecialchars($inq['email']) ?>" class="hover:text-amber-400 transition flex items-center gap-1">
                            ✉ <?= htmlspecialchars($inq['email']) ?>
                        </a>
                        <a href="tel:<?= htmlspecialchars($inq['phone']) ?>" class="hover:text-amber-400 transition flex items-center gap-1">
                            📞 <?= htmlspecialchars($inq['phone']) ?>
                        </a>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <form method="POST" class="inline">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?= $inq['id'] ?>">
                        <select name="status" onchange="this.form.submit()"
                                class="bg-slate-800 border border-slate-700 text-xs text-white rounded-lg px-3 py-1.5 outline-none focus:ring-1 focus:ring-amber-500 font-semibold uppercase">
                            <option value="new" <?= $inq['status'] === 'new' ? 'selected' : '' ?>>New</option>
                            <option value="in_review" <?= $inq['status'] === 'in_review' ? 'selected' : '' ?>>In Review</option>
                            <option value="contacted" <?= $inq['status'] === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                            <option value="closed" <?= $inq['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                        </select>
                    </form>

                    <form method="POST" onsubmit="return confirm('Delete this inquiry?');" class="inline">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $inq['id'] ?>">
                        <button type="submit" class="text-slate-500 hover:text-rose-400 p-1.5 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Property Reference if linked -->
            <?php if (!empty($inq['property_title'])): ?>
            <div class="bg-slate-800/70 border border-slate-700/60 rounded-xl px-4 py-2 text-xs text-slate-300 flex items-center justify-between mb-4">
                <div>
                    <span class="text-slate-400">Associated Estate:</span>
                    <strong class="text-white ml-1"><?= htmlspecialchars($inq['property_title']) ?></strong>
                    <span class="text-amber-500 font-mono ml-1">(<?= htmlspecialchars($inq['property_ref_code']) ?>)</span>
                </div>
                <div class="font-serif text-amber-400 font-bold">
                    $<?= number_format((float)$inq['property_price'], 0) ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Message Text -->
            <div class="text-sm text-slate-200 bg-slate-950/60 p-4 rounded-xl border border-slate-800/80 leading-relaxed whitespace-pre-wrap">
                <?= htmlspecialchars($inq['message']) ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<?php require_once __DIR__ . '/footer.php'; ?>
