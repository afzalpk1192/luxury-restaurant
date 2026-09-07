<?php
/**
 * Elite Estates - Admin Executive Overview Dashboard
 */

declare(strict_types=1);

$pageTitle = 'Executive Overview';
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/../src/Models/Property.php';

use EliteEstates\Models\Admin;
use EliteEstates\Models\Property;
use EliteEstates\Models\Inquiry;

$adminModel = new Admin();
$metrics = $adminModel->getDashboardMetrics();

$propertyModel = new Property();
$recentProperties = $propertyModel->getAll([], 5);

$inquiryModel = new Inquiry();
$recentInquiries = $inquiryModel->getAll(null, 5);
?>

<div class="space-y-8">
    
    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-800 pb-6">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Executive Dashboard</h1>
            <p class="text-slate-400 text-sm mt-1">Real-time luxury portfolio metrics and prospective client inquiries.</p>
        </div>
        <div class="flex gap-3">
            <a href="property-form.php" class="gold-gradient text-white px-5 py-2.5 rounded-xl font-semibold text-sm hover:opacity-95 transition shadow-lg flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Luxury Listing
            </a>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl shadow-sm">
            <div class="text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2 flex justify-between">
                <span>Active Listings</span>
                <span class="text-amber-500 font-mono">LIVE</span>
            </div>
            <div class="text-3xl font-bold text-white"><?= $metrics['active_listings'] ?></div>
            <div class="text-xs text-slate-500 mt-2">Out of <?= $metrics['total_listings'] ?> total database entries</div>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl shadow-sm">
            <div class="text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2 flex justify-between">
                <span>Portfolio Valuation</span>
                <span class="text-emerald-400 font-mono">USD</span>
            </div>
            <div class="text-3xl font-bold text-amber-500"><?= $metrics['formatted_portfolio'] ?></div>
            <div class="text-xs text-slate-500 mt-2">Active luxury inventory volume</div>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl shadow-sm">
            <div class="text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2 flex justify-between">
                <span>Client Inquiries</span>
                <?php if ($metrics['new_inquiries'] > 0): ?>
                <span class="bg-rose-500/20 text-rose-400 text-[10px] px-2 py-0.5 rounded-full font-bold">
                    <?= $metrics['new_inquiries'] ?> NEW
                </span>
                <?php endif; ?>
            </div>
            <div class="text-3xl font-bold text-white"><?= $metrics['total_inquiries'] ?></div>
            <div class="text-xs text-slate-500 mt-2">Private viewing & brokerage leads</div>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl shadow-sm">
            <div class="text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2 flex justify-between">
                <span>Newsletter Subscribers</span>
                <span class="text-blue-400 font-mono">VIP</span>
            </div>
            <div class="text-3xl font-bold text-white"><?= $metrics['total_subscribers'] ?></div>
            <div class="text-xs text-slate-500 mt-2">High-net-worth investor audience</div>
        </div>

    </div>

    <!-- Two Column Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Recent Inquiries -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-bold text-white">Recent Client Inquiries</h2>
                    <p class="text-slate-400 text-xs mt-0.5">Latest private viewing and acquisition requests</p>
                </div>
                <a href="inquiries.php" class="text-xs text-amber-400 hover:text-amber-300 font-semibold">View All &rarr;</a>
            </div>

            <?php if (empty($recentInquiries)): ?>
                <div class="text-center py-12 text-slate-500 text-sm">No client inquiries received yet.</div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($recentInquiries as $inq): ?>
                    <div class="bg-slate-800/60 border border-slate-700/50 p-4 rounded-xl flex justify-between items-start">
                        <div class="space-y-1">
                            <div class="font-bold text-white text-sm"><?= htmlspecialchars($inq['full_name']) ?></div>
                            <div class="text-xs text-slate-400"><?= htmlspecialchars($inq['email']) ?> &bull; <?= htmlspecialchars($inq['phone']) ?></div>
                            <div class="text-xs text-slate-300 line-clamp-1 italic mt-1">"<?= htmlspecialchars($inq['message']) ?>"</div>
                            <?php if (!empty($inq['property_title'])): ?>
                            <div class="text-[11px] text-amber-400 font-medium">Re: <?= htmlspecialchars($inq['property_title']) ?> (<?= htmlspecialchars($inq['property_ref_code']) ?>)</div>
                            <?php endif; ?>
                        </div>
                        <span class="text-[10px] font-bold uppercase px-2.5 py-1 rounded-full <?php
                            echo match($inq['status']) {
                                'new' => 'bg-rose-500/20 text-rose-300 border border-rose-500/30',
                                'in_review' => 'bg-amber-500/20 text-amber-300 border border-amber-500/30',
                                'contacted' => 'bg-blue-500/20 text-blue-300 border border-blue-500/30',
                                'closed' => 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30',
                                default => 'bg-slate-700 text-slate-300'
                            };
                        ?>">
                            <?= htmlspecialchars($inq['status']) ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recent Properties -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-bold text-white">Featured Portfolio Listings</h2>
                    <p class="text-slate-400 text-xs mt-0.5">Active trophy real estate across key destinations</p>
                </div>
                <a href="properties.php" class="text-xs text-amber-400 hover:text-amber-300 font-semibold">View All &rarr;</a>
            </div>

            <?php if (empty($recentProperties)): ?>
                <div class="text-center py-12 text-slate-500 text-sm">No properties in portfolio. Click "Add Luxury Listing".</div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($recentProperties as $prop): ?>
                    <div class="bg-slate-800/60 border border-slate-700/50 p-3.5 rounded-xl flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <img src="<?= htmlspecialchars($prop['featured_image']) ?>" class="w-14 h-14 rounded-lg object-cover bg-slate-700 border border-slate-700">
                            <div>
                                <div class="font-bold text-white text-sm line-clamp-1"><?= htmlspecialchars($prop['title']) ?></div>
                                <div class="text-xs text-slate-400"><?= htmlspecialchars($prop['location']) ?> &bull; <span class="font-mono text-[11px]"><?= htmlspecialchars($prop['ref_code']) ?></span></div>
                                <div class="text-xs text-amber-500 font-semibold mt-0.5"><?= $prop['formatted_price'] ?></div>
                            </div>
                        </div>
                        <a href="property-form.php?id=<?= $prop['id'] ?>" class="text-xs bg-slate-700 hover:bg-slate-600 text-slate-200 px-3 py-1.5 rounded-lg transition font-medium">
                            Edit
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>

</div>

<?php require_once __DIR__ . '/footer.php'; ?>
