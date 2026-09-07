<?php
/**
 * Elite Estates - Admin Properties Management
 */

declare(strict_types=1);

$pageTitle = 'Property Inventory';
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/../src/Models/Property.php';

use EliteEstates\Models\Property;

$propertyModel = new Property();
$message = '';
$error = '';

// Handle Delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $deleteId = (int)($_POST['id'] ?? 0);
    if ($deleteId > 0) {
        if ($propertyModel->delete($deleteId)) {
            $message = "Property #{$deleteId} has been successfully removed from the portfolio.";
        } else {
            $error = "Failed to remove property.";
        }
    }
}

// Handle Status toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
    $toggleId = (int)($_POST['id'] ?? 0);
    $newStatus = $_POST['status'] ?? 'active';
    if ($toggleId > 0) {
        $propertyModel->update($toggleId, ['status' => $newStatus]);
        $message = "Listing status updated to " . strtoupper($newStatus);
    }
}

$statusFilter = $_GET['status'] ?? null;
$properties = $propertyModel->getAll($statusFilter ? ['status' => $statusFilter] : [], 100);
?>

<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-800 pb-6">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Luxury Property Portfolio</h1>
            <p class="text-slate-400 text-sm mt-1">Manage, edit, and publish premier residential listings.</p>
        </div>
        <div class="flex gap-3">
            <a href="property-form.php" class="gold-gradient text-white px-5 py-2.5 rounded-xl font-semibold text-sm hover:opacity-95 transition shadow-lg flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create New Listing
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if ($message): ?>
    <div class="bg-emerald-900/40 border border-emerald-500/50 text-emerald-200 text-sm p-4 rounded-xl flex items-center justify-between">
        <span><?= htmlspecialchars($message) ?></span>
    </div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="bg-rose-900/40 border border-rose-500/50 text-rose-200 text-sm p-4 rounded-xl flex items-center justify-between">
        <span><?= htmlspecialchars($error) ?></span>
    </div>
    <?php endif; ?>

    <!-- Filter Bar -->
    <div class="flex gap-2 border-b border-slate-800 pb-4 text-xs font-semibold">
        <a href="properties.php" class="px-3 py-1.5 rounded-lg transition <?= empty($statusFilter) ? 'bg-amber-500 text-navy font-bold' : 'text-slate-400 hover:text-white bg-slate-900' ?>">All Listings</a>
        <a href="properties.php?status=active" class="px-3 py-1.5 rounded-lg transition <?= $statusFilter === 'active' ? 'bg-amber-500 text-navy font-bold' : 'text-slate-400 hover:text-white bg-slate-900' ?>">Active (Public)</a>
        <a href="properties.php?status=pending" class="px-3 py-1.5 rounded-lg transition <?= $statusFilter === 'pending' ? 'bg-amber-500 text-navy font-bold' : 'text-slate-400 hover:text-white bg-slate-900' ?>">Pending</a>
        <a href="properties.php?status=sold" class="px-3 py-1.5 rounded-lg transition <?= $statusFilter === 'sold' ? 'bg-amber-500 text-navy font-bold' : 'text-slate-400 hover:text-white bg-slate-900' ?>">Sold</a>
        <a href="properties.php?status=draft" class="px-3 py-1.5 rounded-lg transition <?= $statusFilter === 'draft' ? 'bg-amber-500 text-navy font-bold' : 'text-slate-400 hover:text-white bg-slate-900' ?>">Drafts</a>
    </div>

    <!-- Listings Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-800/80 text-xs uppercase font-semibold text-slate-400 border-b border-slate-700/60">
                    <tr>
                        <th class="p-4">Property</th>
                        <th class="p-4">Type</th>
                        <th class="p-4">Price</th>
                        <th class="p-4">Specifications</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php if (empty($properties)): ?>
                    <tr>
                        <td colspan="6" class="p-12 text-center text-slate-500">
                            No listings match the selected status.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($properties as $p): ?>
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="p-4">
                            <div class="flex items-center gap-4">
                                <img src="<?= htmlspecialchars($p['featured_image']) ?>" class="w-16 h-16 rounded-xl object-cover border border-slate-700 bg-slate-800">
                                <div>
                                    <div class="font-bold text-white text-base"><?= htmlspecialchars($p['title']) ?></div>
                                    <div class="text-xs text-slate-400"><?= htmlspecialchars($p['location']) ?></div>
                                    <div class="text-[11px] font-mono text-amber-500 mt-0.5">Ref: <?= htmlspecialchars($p['ref_code']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            <span class="text-xs bg-slate-800 text-slate-300 px-2.5 py-1 rounded-lg border border-slate-700">
                                <?= htmlspecialchars($p['property_type']) ?>
                            </span>
                        </td>
                        <td class="p-4 font-serif font-bold text-amber-400 text-base">
                            <?= $p['formatted_price'] ?>
                        </td>
                        <td class="p-4 text-xs text-slate-400">
                            <div><?= $p['beds'] ?> Beds &bull; <?= $p['baths'] ?> Baths</div>
                            <div class="mt-0.5"><?= $p['formatted_sqft'] ?> SqFt <?= !empty($p['lot_size']) ? ' &bull; ' . htmlspecialchars($p['lot_size']) : '' ?></div>
                        </td>
                        <td class="p-4">
                            <form method="POST" class="inline">
                                <input type="hidden" name="action" value="toggle_status">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <select name="status" onchange="this.form.submit()" class="bg-slate-800 border border-slate-700 rounded-lg text-xs py-1 px-2 text-white outline-none focus:ring-1 focus:ring-amber-500">
                                    <option value="active" <?= $p['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="pending" <?= $p['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="sold" <?= $p['status'] === 'sold' ? 'selected' : '' ?>>Sold</option>
                                    <option value="draft" <?= $p['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                                </select>
                            </form>
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="property-form.php?id=<?= $p['id'] ?>" class="bg-slate-800 hover:bg-slate-700 text-slate-200 px-3 py-1.5 rounded-lg text-xs font-semibold transition border border-slate-700">
                                    Edit
                                </a>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this listing?');" class="inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <button type="submit" class="bg-rose-950/60 hover:bg-rose-900 text-rose-300 px-3 py-1.5 rounded-lg text-xs font-semibold transition border border-rose-800/60">
                                        Delete
                                    </button>
                                </form>
                            </div>
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
