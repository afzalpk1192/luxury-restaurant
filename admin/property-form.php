<?php
/**
 * Elite Estates - Admin Add / Edit Property Form
 */

declare(strict_types=1);

$pageTitle = 'Manage Listing';
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/../src/Models/Property.php';
require_once __DIR__ . '/../src/Helpers/Validator.php';

use EliteEstates\Models\Property;
use EliteEstates\Helpers\Validator;

$propertyModel = new Property();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = ($id > 0);
$property = null;
$error = '';

if ($isEdit) {
    $property = $propertyModel->getById($id);
    if (!$property) {
        header('Location: properties.php');
        exit;
    }
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = Validator::sanitizeString($_POST['title'] ?? '');
    $refCode = Validator::sanitizeString($_POST['ref_code'] ?? '');
    $tag = Validator::sanitizeString($_POST['tag'] ?? 'For Sale');
    $propertyType = Validator::sanitizeString($_POST['property_type'] ?? 'Modern Villa');
    $location = Validator::sanitizeString($_POST['location'] ?? '');
    $city = Validator::sanitizeString($_POST['city'] ?? '');
    $state = Validator::sanitizeString($_POST['state'] ?? '');
    $address = Validator::sanitizeString($_POST['address'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $beds = (int)($_POST['beds'] ?? 1);
    $baths = (float)($_POST['baths'] ?? 1.0);
    $sqft = (int)($_POST['sqft'] ?? 1000);
    $lotSize = Validator::sanitizeString($_POST['lot_size'] ?? '');
    $yearBuilt = !empty($_POST['year_built']) ? (int)$_POST['year_built'] : null;
    $featuredImage = trim($_POST['featured_image'] ?? '');
    $description = Validator::sanitizeString($_POST['description'] ?? '');
    $status = $_POST['status'] ?? 'active';
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;

    // Process amenities from comma or newline separated text
    $amenitiesInput = $_POST['amenities'] ?? '';
    $amenitiesArray = array_values(array_filter(array_map('trim', preg_split('/[,\n]+/', $amenitiesInput))));

    if (empty($title) || empty($location) || $price <= 0 || empty($featuredImage)) {
        $error = 'Please fill out all required fields (Title, Location, Price, and Featured Image URL).';
    } else {
        $payload = [
            'ref_code'       => $refCode ?: ('EE-' . rand(10000, 99999) . '-LST'),
            'title'          => $title,
            'tag'            => $tag,
            'property_type'  => $propertyType,
            'location'       => $location,
            'city'           => $city,
            'state'          => $state,
            'address'        => $address,
            'price'          => $price,
            'beds'           => $beds,
            'baths'          => $baths,
            'sqft'           => $sqft,
            'lot_size'       => $lotSize,
            'year_built'     => $yearBuilt,
            'featured_image' => $featuredImage,
            'description'    => $description,
            'amenities'      => $amenitiesArray,
            'status'         => $status,
            'is_featured'    => $isFeatured
        ];

        try {
            if ($isEdit) {
                $propertyModel->update($id, $payload);
            } else {
                $propertyModel->create($payload);
            }
            header('Location: properties.php');
            exit;
        } catch (Throwable $e) {
            $error = 'Database Operation Error: ' . $e->getMessage();
        }
    }
}

// Convert amenities array to string for textarea
$amenitiesString = '';
if (!empty($property['amenities_list'])) {
    $amenitiesString = implode("\n", $property['amenities_list']);
}
?>

<div class="max-w-4xl mx-auto space-y-6">

    <!-- Form Header -->
    <div class="flex justify-between items-center border-b border-slate-800 pb-6">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight">
                <?= $isEdit ? 'Edit Luxury Property' : 'Create New Luxury Listing' ?>
            </h1>
            <p class="text-slate-400 text-sm mt-1">
                <?= $isEdit ? 'Update specifications and presentation for this estate.' : 'Publish a new residential masterpiece to the portfolio.' ?>
            </p>
        </div>
        <a href="properties.php" class="text-xs text-slate-400 hover:text-white bg-slate-800 px-4 py-2 rounded-xl transition border border-slate-700">
            &larr; Return to Inventory
        </a>
    </div>

    <!-- Error Banner -->
    <?php if (!empty($error)): ?>
    <div class="bg-rose-900/50 border border-rose-500/50 text-rose-200 text-sm p-4 rounded-xl flex items-center gap-3">
        <span class="text-rose-400 font-bold">⚠</span>
        <span><?= htmlspecialchars($error) ?></span>
    </div>
    <?php endif; ?>

    <!-- Form Container -->
    <form method="POST" class="bg-slate-900 border border-slate-800 p-8 rounded-2xl shadow-xl space-y-8">

        <!-- 1. Core Identification -->
        <div class="space-y-4">
            <h2 class="text-lg font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-2">
                <span class="text-amber-500">1.</span> Listing Details
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Property Title *</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($_POST['title'] ?? $property['title'] ?? '') ?>" required placeholder="e.g. Oceanfront Majesty"
                           class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3.5 text-sm text-white outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Reference Code</label>
                    <input type="text" name="ref_code" value="<?= htmlspecialchars($_POST['ref_code'] ?? $property['ref_code'] ?? ('EE-' . rand(10000, 99999) . '-LST')) ?>" placeholder="EE-99210-MAL"
                           class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3.5 text-sm text-amber-400 font-mono outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Property Type</label>
                    <select name="property_type" class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3.5 text-sm text-white outline-none focus:ring-2 focus:ring-amber-500">
                        <?php
                        $types = ['Modern Villa', 'Penthouse', 'Townhouse', 'Estate', 'Chalet'];
                        $currentType = $_POST['property_type'] ?? $property['property_type'] ?? 'Modern Villa';
                        foreach ($types as $t) {
                            $selected = ($t === $currentType) ? 'selected' : '';
                            echo "<option value='{$t}' {$selected}>{$t}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Tag / Badge</label>
                    <input type="text" name="tag" value="<?= htmlspecialchars($_POST['tag'] ?? $property['tag'] ?? 'For Sale') ?>" placeholder="For Sale, New Listing, Exclusive"
                           class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3.5 text-sm text-white outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Publish Status</label>
                    <select name="status" class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3.5 text-sm text-white outline-none focus:ring-2 focus:ring-amber-500">
                        <?php
                        $statuses = ['active' => 'Active (Published)', 'pending' => 'Pending', 'sold' => 'Sold', 'draft' => 'Draft (Unpublished)'];
                        $currentStatus = $_POST['status'] ?? $property['status'] ?? 'active';
                        foreach ($statuses as $k => $v) {
                            $selected = ($k === $currentStatus) ? 'selected' : '';
                            echo "<option value='{$k}' {$selected}>{$v}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- 2. Location & Pricing -->
        <div class="space-y-4">
            <h2 class="text-lg font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-2">
                <span class="text-amber-500">2.</span> Location & Pricing
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Display Location *</label>
                    <input type="text" name="location" value="<?= htmlspecialchars($_POST['location'] ?? $property['location'] ?? '') ?>" required placeholder="e.g. Malibu, California"
                           class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3.5 text-sm text-white outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Price (USD $) *</label>
                    <input type="number" step="10000" name="price" value="<?= htmlspecialchars((string)($_POST['price'] ?? $property['price'] ?? '')) ?>" required placeholder="12500000"
                           class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3.5 text-sm text-amber-400 font-bold outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">City</label>
                    <input type="text" name="city" value="<?= htmlspecialchars($_POST['city'] ?? $property['city'] ?? '') ?>" placeholder="Malibu"
                           class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3.5 text-sm text-white outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">State / Region</label>
                    <input type="text" name="state" value="<?= htmlspecialchars($_POST['state'] ?? $property['state'] ?? '') ?>" placeholder="California"
                           class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3.5 text-sm text-white outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Full Street Address</label>
                    <input type="text" name="address" value="<?= htmlspecialchars($_POST['address'] ?? $property['address'] ?? '') ?>" placeholder="31200 Broad Beach Rd"
                           class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3.5 text-sm text-white outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>
        </div>

        <!-- 3. Specifications -->
        <div class="space-y-4">
            <h2 class="text-lg font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-2">
                <span class="text-amber-500">3.</span> Specifications & Architecture
            </h2>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Bedrooms</label>
                    <input type="number" name="beds" value="<?= htmlspecialchars((string)($_POST['beds'] ?? $property['beds'] ?? 5)) ?>" min="1" max="50"
                           class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3.5 text-sm text-white outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Bathrooms</label>
                    <input type="number" step="0.5" name="baths" value="<?= htmlspecialchars((string)($_POST['baths'] ?? $property['baths'] ?? 6.0)) ?>" min="1" max="50"
                           class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3.5 text-sm text-white outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">SqFt Area</label>
                    <input type="number" name="sqft" value="<?= htmlspecialchars((string)($_POST['sqft'] ?? $property['sqft'] ?? 7500)) ?>" min="100"
                           class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3.5 text-sm text-white outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Lot Size</label>
                    <input type="text" name="lot_size" value="<?= htmlspecialchars($_POST['lot_size'] ?? $property['lot_size'] ?? '2.5 Acres') ?>" placeholder="e.g. 2.5 Acres"
                           class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3.5 text-sm text-white outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Year Built</label>
                    <input type="number" name="year_built" value="<?= htmlspecialchars((string)($_POST['year_built'] ?? $property['year_built'] ?? date('Y'))) ?>" min="1800" max="2100"
                           class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3.5 text-sm text-white outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>
        </div>

        <!-- 4. Media & Description -->
        <div class="space-y-4">
            <h2 class="text-lg font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-2">
                <span class="text-amber-500">4.</span> Media & Marketing Content
            </h2>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Featured Image URL *</label>
                <input type="url" name="featured_image" value="<?= htmlspecialchars($_POST['featured_image'] ?? $property['featured_image'] ?? '') ?>" required placeholder="https://images.unsplash.com/..."
                       class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3.5 text-sm text-white outline-none focus:ring-2 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Listing Narrative / Description *</label>
                <textarea name="description" rows="4" required placeholder="Describe the architectural uniqueness, views, and materials..."
                          class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3.5 text-sm text-white outline-none focus:ring-2 focus:ring-amber-500"><?= htmlspecialchars($_POST['description'] ?? $property['description'] ?? '') ?></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Prime Amenities (One per line or comma separated)</label>
                <textarea name="amenities" rows="3" placeholder="Private Beach Access&#10;Infinity Edge Pool&#10;Smart Home Automation"
                          class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3.5 text-sm text-white outline-none focus:ring-2 focus:ring-amber-500 font-mono"><?= htmlspecialchars($_POST['amenities'] ?? $amenitiesString) ?></textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <input type="checkbox" id="is_featured" name="is_featured" value="1" <?= (!empty($property['is_featured']) || !isset($property)) ? 'checked' : '' ?>
                       class="w-5 h-5 rounded bg-slate-800 border-slate-700 text-amber-500 focus:ring-amber-500">
                <label for="is_featured" class="text-sm font-medium text-slate-300">Feature this residence prominently on homepage banner</label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-4 border-t border-slate-800 flex justify-end gap-4">
            <a href="properties.php" class="px-6 py-3 rounded-xl text-sm font-medium text-slate-400 hover:text-white transition">Cancel</a>
            <button type="submit" class="gold-gradient text-white px-8 py-3 rounded-xl font-bold text-sm hover:opacity-95 transition shadow-lg">
                <?= $isEdit ? 'Save Changes & Update Listing' : 'Publish Luxury Listing' ?>
            </button>
        </div>

    </form>

</div>

<?php require_once __DIR__ . '/footer.php'; ?>
