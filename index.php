<?php
/**
 * Elite Estates - Luxury Real Estate Public Platform
 * Integrated with dynamic PHP backend & MySQL database
 */

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/Models/Property.php';

use EliteEstates\Config\Database;
use EliteEstates\Config\AppConfig;
use EliteEstates\Models\Property;

$dbConnected = false;
$dbError = '';
$initialProperties = [];
$distinctLocations = ['Beverly Hills, CA', 'Miami, FL', 'Manhattan, NY', 'Malibu, California', 'Aspen, Colorado'];
$distinctTypes = ['Modern Villa', 'Penthouse', 'Townhouse'];

try {
    $propertyModel = new Property();
    $initialProperties = $propertyModel->getAll(['status' => 'active'], 12);
    $dbLocations = $propertyModel->getDistinctLocations();
    if (!empty($dbLocations)) {
        $distinctLocations = $dbLocations;
    }
    $dbTypes = $propertyModel->getDistinctTypes();
    if (!empty($dbTypes)) {
        $distinctTypes = $dbTypes;
    }
    $dbConnected = true;
} catch (Throwable $e) {
    $dbError = $e->getMessage();
    // Senior fallback: supply default luxury properties so the frontend renders flawlessly even before MySQL is started
    $initialProperties = [
        [
            'id' => 1,
            'ref_code' => 'EE-99210-MAL',
            'title' => 'Oceanfront Majesty',
            'tag' => 'For Sale',
            'property_type' => 'Modern Villa',
            'location' => 'Malibu, California',
            'price' => 12500000.00,
            'formatted_price' => '$12,500,000',
            'beds' => 5,
            'baths' => 6,
            'sqft' => 7500,
            'formatted_sqft' => '7,500',
            'lot_size' => '2.5 Acres',
            'year_built' => 2023,
            'featured_image' => 'https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&q=80&w=1200',
            'description' => 'This architectural masterpiece offers floor-to-ceiling windows, a private infinity pool, and integrated smart home technology. Located in the most exclusive zip code, it provides unparalleled privacy and stunning panoramic views.',
            'amenities_list' => ['Private Beach Access', 'Infinity Edge Pool', 'Smart Home Automation', 'Wine Cellar']
        ],
        [
            'id' => 2,
            'ref_code' => 'EE-48102-BEV',
            'title' => 'Modernist Skyline Villa',
            'tag' => 'New Listing',
            'property_type' => 'Modern Villa',
            'location' => 'Beverly Hills, CA',
            'price' => 8900000.00,
            'formatted_price' => '$8,900,000',
            'beds' => 4,
            'baths' => 4,
            'sqft' => 4200,
            'formatted_sqft' => '4,200',
            'lot_size' => '1.2 Acres',
            'year_built' => 2024,
            'featured_image' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&q=80&w=1200',
            'description' => 'Perched in prime Beverly Hills, this newly minted modernist estate commands sweeping 270-degree views from Downtown Los Angeles to Santa Monica.',
            'amenities_list' => ['Panoramic Skyline Views', 'Cantilevered Terraces', 'Outdoor Kitchen', 'Gated Motor Court']
        ],
        [
            'id' => 3,
            'ref_code' => 'EE-77301-ASP',
            'title' => 'The Glass House',
            'tag' => 'For Sale',
            'property_type' => 'Modern Villa',
            'location' => 'Aspen, Colorado',
            'price' => 15200000.00,
            'formatted_price' => '$15,200,000',
            'beds' => 6,
            'baths' => 7,
            'sqft' => 9800,
            'formatted_sqft' => '9,800',
            'lot_size' => '4.8 Acres',
            'year_built' => 2022,
            'featured_image' => 'https://images.unsplash.com/photo-1600566753376-12c8ab7fb75b?auto=format&fit=crop&q=80&w=1200',
            'description' => 'Nestled in the prestigious Red Mountain enclave of Aspen, The Glass House is a triumph of alpine contemporary architecture with heated indoor pool.',
            'amenities_list' => ['Ski-In / Ski-Out', 'Geothermal Heating', 'Heated Indoor Lap Pool', 'Snow-Melt Driveway']
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(AppConfig::APP_NAME) ?> | Luxury Real Estate & Private Residences</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; scroll-behavior: smooth; }
        h1, h2, h3, h4, .serif { font-family: 'Playfair Display', serif; }
        .gold-gradient { background: linear-gradient(135deg, #c5a059 0%, #947a43 100%); }
        .gold-text { background: linear-gradient(135deg, #d4af37 0%, #aa8c2c 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .bg-navy { background-color: #0f172a; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 selection:bg-amber-500 selection:text-white" 
      x-data="estateApp()" 
      x-init="initData()">

    <!-- System Setup Notice if DB not ready -->
    <?php if (!$dbConnected): ?>
    <div class="bg-amber-600 text-white px-4 py-3 text-center text-sm font-medium sticky top-0 z-[60] shadow-md flex items-center justify-center gap-4">
        <span>MySQL Database is not initialized yet. Run the automated database installer:</span>
        <a href="database/migrate.php" class="bg-navy px-4 py-1 rounded-full text-xs font-bold hover:bg-slate-800 transition">
            Run Database Migration & Seeder &rarr;
        </a>
    </div>
    <?php endif; ?>

    <!-- Toast Notification System -->
    <div class="fixed bottom-8 left-8 z-50 flex flex-col gap-2 pointer-events-none" x-cloak>
        <template x-for="(toast, index) in toasts" :key="index">
            <div :class="toast.type === 'success' ? 'bg-emerald-800 text-emerald-100 border-emerald-600' : 'bg-rose-800 text-rose-100 border-rose-600'"
                 class="px-5 py-4 rounded-2xl shadow-2xl border text-sm max-w-md pointer-events-auto flex items-start gap-3 transform transition-all duration-300">
                <span x-text="toast.type === 'success' ? '✓' : '⚠'" class="font-bold text-lg"></span>
                <div class="flex-1" x-text="toast.message"></div>
                <button @click="removeToast(index)" class="text-white/60 hover:text-white">&times;</button>
            </div>
        </template>
    </div>

    <!-- Navbar -->
    <nav class="fixed w-full z-50 bg-white/90 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <a href="#home" class="flex items-center gap-2">
                    <div class="w-10 h-10 gold-gradient rounded-lg flex items-center justify-center text-white shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    </div>
                    <span class="text-2xl font-bold tracking-tighter bg-navy text-transparent bg-clip-text">ELITE<span class="text-amber-600">ESTATES</span></span>
                </a>
                
                <div class="hidden md:flex items-center space-x-8 font-medium">
                    <a href="#home" class="hover:text-amber-600 transition">Home</a>
                    <a href="#properties" class="hover:text-amber-600 transition">Properties</a>
                    <a href="#services" class="hover:text-amber-600 transition">Services</a>
                    <a href="#about" class="hover:text-amber-600 transition">About</a>
                    <a href="admin/login.php" class="text-slate-500 hover:text-navy transition text-sm flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Admin Portal
                    </a>
                    <a href="#contact" class="bg-navy text-white px-6 py-2.5 rounded-full hover:bg-amber-600 transition shadow-sm">Contact Us</a>
                </div>

                <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 text-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div x-show="mobileMenu" x-cloak class="md:hidden bg-white border-b px-6 py-6 space-y-4 shadow-xl">
            <a href="#home" @click="mobileMenu = false" class="block text-lg font-medium text-slate-700">Home</a>
            <a href="#properties" @click="mobileMenu = false" class="block text-lg font-medium text-slate-700">Properties</a>
            <a href="#services" @click="mobileMenu = false" class="block text-lg font-medium text-slate-700">Services</a>
            <a href="#about" @click="mobileMenu = false" class="block text-lg font-medium text-slate-700">About</a>
            <a href="admin/login.php" class="block text-slate-600">Admin Portal</a>
            <a href="#contact" @click="mobileMenu = false" class="block text-lg font-bold text-amber-600">Contact Us</a>
        </div>
    </nav>

    <!-- Section 1: Hero Section -->
    <section id="home" class="relative h-screen flex items-center justify-center overflow-hidden">
        <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&q=80&w=2000" class="absolute inset-0 w-full h-full object-cover" alt="Luxury Home">
        <div class="absolute inset-0 bg-navy/60 backdrop-blur-[1px]"></div>
        
        <div class="relative z-10 text-center text-white px-4 max-w-5xl">
            <span class="text-amber-400 uppercase tracking-widest text-sm font-semibold mb-4 inline-block">Curated Ultra-Luxury Real Estate</span>
            <h1 class="text-5xl md:text-8xl mb-6 font-bold leading-tight">Discover Your <br><span class="text-amber-400">Signature</span> Residence</h1>
            <p class="text-xl md:text-2xl mb-10 font-light opacity-90 max-w-2xl mx-auto">Luxury real estate services tailored to the most discerning clientele. Buy, sell, and invest with complete confidence.</p>
            
            <div class="flex flex-col md:flex-row gap-4 justify-center items-center">
                <a href="#properties" class="gold-gradient px-10 py-4 rounded-full font-bold text-lg hover:scale-105 transition transform shadow-xl text-white">View Listings</a>
                <a href="#contact" class="bg-white/10 backdrop-blur-md border border-white/30 px-10 py-4 rounded-full font-bold text-lg hover:bg-white hover:text-black transition">Consult an Agent</a>
            </div>
        </div>
    </section>

    <!-- Section 2: Live Search / Filter Bar -->
    <section class="max-w-6xl mx-auto -mt-16 relative z-20 px-4">
        <div class="bg-white p-6 md:p-8 rounded-3xl shadow-2xl border border-slate-100">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Location</label>
                    <select x-model="filterLocation" @change="applyFilters()" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-sm font-medium outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="all">All Locations</option>
                        <?php foreach ($distinctLocations as $loc): ?>
                        <option value="<?= htmlspecialchars($loc) ?>"><?= htmlspecialchars($loc) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Property Type</label>
                    <select x-model="filterType" @change="applyFilters()" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-sm font-medium outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="all">All Types</option>
                        <?php foreach ($distinctTypes as $type): ?>
                        <option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Price Range</label>
                    <select x-model="filterPrice" @change="applyFilters()" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-sm font-medium outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="all">Any Price</option>
                        <option value="$1M - $5M">$1M - $5M</option>
                        <option value="$5M - $10M">$5M - $10M</option>
                        <option value="$10M+">$10M+</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button @click="applyFilters()" class="w-full bg-navy text-white h-[52px] rounded-xl font-bold hover:bg-amber-600 transition flex items-center justify-center gap-2 shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Search Properties
                    </button>
                </div>
            </div>
            
            <!-- Quick search & Active count -->
            <div class="mt-4 pt-4 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center text-xs text-slate-500 gap-2">
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <span class="font-semibold text-slate-600">Quick Search:</span>
                    <input type="text" x-model="searchQuery" @input.debounce.300ms="applyFilters()" placeholder="Title, features, city..." class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 outline-none focus:border-amber-500 w-full md:w-64">
                </div>
                <div>
                    Showing <span class="font-bold text-navy" x-text="properties.length"></span> Luxury Listings
                </div>
            </div>
        </div>
    </section>

    <!-- Section 3: Properties Listing -->
    <section id="properties" class="py-24 max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
            <span class="text-amber-600 uppercase tracking-widest text-xs font-bold mb-2 block">Our Exclusive Portfolio</span>
            <h2 class="text-4xl md:text-5xl mb-4 font-bold">Featured Listings</h2>
            <div class="w-24 h-1 gold-gradient mx-auto rounded-full"></div>
        </div>

        <!-- Loading Spinner -->
        <div x-show="loading" class="text-center py-20" x-cloak>
            <div class="w-12 h-12 border-4 border-amber-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-slate-500">Querying real-time luxury portfolio...</p>
        </div>

        <!-- No Results Fallback -->
        <div x-show="!loading && properties.length === 0" class="text-center py-20 bg-white rounded-3xl border border-slate-200 p-8" x-cloak>
            <div class="w-16 h-16 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-2xl font-bold mb-2">No Properties Match Your Filter</h3>
            <p class="text-slate-500 mb-6">Try broadening your criteria or reset the search to view our full collection.</p>
            <button @click="resetFilters()" class="gold-gradient text-white px-6 py-2.5 rounded-full font-bold text-sm">Reset All Filters</button>
        </div>

        <!-- Properties Grid (Dynamic Alpine / Hydrated from DB) -->
        <div x-show="!loading && properties.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <template x-for="item in properties" :key="item.id">
                <div class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl transition duration-500 group cursor-pointer border border-slate-100 flex flex-col justify-between"
                     @click="openPropertyModal(item.id)">
                    
                    <div>
                        <div class="relative h-72 overflow-hidden bg-slate-200">
                            <img :src="item.featured_image" :alt="item.title" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                            <div class="absolute top-4 left-4 bg-white/95 backdrop-blur px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider text-navy shadow-md"
                                 x-text="item.tag || 'Exclusive'"></div>
                            <div class="absolute top-4 right-4 bg-navy/80 backdrop-blur px-3 py-1 rounded-full text-[10px] font-semibold text-white/90"
                                 x-text="item.ref_code"></div>
                        </div>

                        <div class="p-8">
                            <div class="text-xs uppercase tracking-wider text-amber-600 font-bold mb-1" x-text="item.property_type"></div>
                            <h3 class="text-2xl font-bold mb-2 text-navy line-clamp-1" x-text="item.title"></h3>
                            <p class="text-slate-500 mb-6 flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"/></svg>
                                <span x-text="item.location"></span>
                            </p>
                        </div>
                    </div>

                    <div class="px-8 pb-8">
                        <div class="flex justify-between border-t border-slate-100 pt-6 items-end">
                            <div class="text-center">
                                <span class="block font-bold text-slate-800 text-lg" x-text="item.beds"></span>
                                <span class="text-xs text-slate-400">Beds</span>
                            </div>
                            <div class="text-center">
                                <span class="block font-bold text-slate-800 text-lg" x-text="item.baths"></span>
                                <span class="text-xs text-slate-400">Baths</span>
                            </div>
                            <div class="text-center">
                                <span class="block font-bold text-slate-800 text-lg" x-text="item.formatted_sqft || item.sqft"></span>
                                <span class="text-xs text-slate-400">SqFt</span>
                            </div>
                            <div class="text-right font-serif text-xl font-bold text-amber-600" x-text="item.formatted_price"></div>
                        </div>
                    </div>

                </div>
            </template>
        </div>
    </section>

    <!-- Section 4: About & Services -->
    <section id="services" class="bg-navy py-28 text-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-2 gap-20 items-center relative z-10">
            <div>
                <span class="text-amber-400 uppercase tracking-widest text-xs font-bold mb-3 block">White-Glove Brokerage</span>
                <h2 class="text-4xl md:text-6xl mb-8 font-bold leading-tight">Elevating Your <br>Real Estate Experience</h2>
                <p class="text-slate-400 text-lg mb-12 font-light leading-relaxed">With over 25 years of mastery in premier global markets, our private advisory team provides bespoke concierge service for buyers, sellers, and family offices alike.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="p-6 border border-white/10 rounded-2xl hover:bg-white/5 transition group bg-white/[0.02]">
                        <div class="w-12 h-12 gold-gradient rounded-xl mb-4 flex items-center justify-center text-white shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <h4 class="text-xl font-bold mb-2">Asset Protection</h4>
                        <p class="text-sm text-slate-400 leading-relaxed">Comprehensive legal, tax, and discrete ownership structuring for your acquisitions.</p>
                    </div>
                    <div class="p-6 border border-white/10 rounded-2xl hover:bg-white/5 transition group bg-white/[0.02]">
                        <div class="w-12 h-12 gold-gradient rounded-xl mb-4 flex items-center justify-center text-white shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                        <h4 class="text-xl font-bold mb-2">Market Analysis</h4>
                        <p class="text-sm text-slate-400 leading-relaxed">Proprietary valuation models, off-market deal flow, and predictive trend analytics.</p>
                    </div>
                </div>
            </div>
            <div class="relative">
                <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&q=80&w=800" class="rounded-3xl shadow-2xl relative z-10 w-full object-cover">
                <div class="absolute -top-10 -right-10 w-72 h-72 gold-gradient rounded-full blur-3xl opacity-20"></div>
                <div class="absolute -bottom-10 -left-10 w-72 h-72 bg-amber-600 rounded-full blur-3xl opacity-10"></div>
            </div>
        </div>
    </section>

    <!-- Section 5: Why Choose Us (Stats) -->
    <section id="about" class="py-24 max-w-7xl mx-auto px-4 text-center">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="p-6 bg-white rounded-2xl border border-slate-100 shadow-sm">
                <span class="block text-5xl font-serif text-amber-600 mb-2 font-bold">$4B+</span>
                <span class="text-slate-500 uppercase tracking-widest text-xs font-bold">Total Sales Volume</span>
            </div>
            <div class="p-6 bg-white rounded-2xl border border-slate-100 shadow-sm">
                <span class="block text-5xl font-serif text-amber-600 mb-2 font-bold">250+</span>
                <span class="text-slate-500 uppercase tracking-widest text-xs font-bold">Global Advisors</span>
            </div>
            <div class="p-6 bg-white rounded-2xl border border-slate-100 shadow-sm">
                <span class="block text-5xl font-serif text-amber-600 mb-2 font-bold">15</span>
                <span class="text-slate-500 uppercase tracking-widest text-xs font-bold">International Hubs</span>
            </div>
            <div class="p-6 bg-white rounded-2xl border border-slate-100 shadow-sm">
                <span class="block text-5xl font-serif text-amber-600 mb-2 font-bold">99%</span>
                <span class="text-slate-500 uppercase tracking-widest text-xs font-bold">Client Retention</span>
            </div>
        </div>
    </section>

    <!-- Section 6: Contact Us / Lead Capture -->
    <section id="contact" class="py-24 bg-slate-100">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div>
                <span class="text-amber-600 uppercase tracking-widest text-xs font-bold mb-3 block">Private Advisory</span>
                <h2 class="text-4xl md:text-5xl mb-6 font-bold">Let's Find Your <br>Next Masterpiece</h2>
                <p class="text-slate-600 text-lg mb-8 leading-relaxed">Connect with our senior partners to arrange a discreet private showing or discuss representing your premier residence.</p>
                
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm text-amber-600">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/></svg>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block uppercase font-bold">Direct Line</span>
                            <span class="text-lg font-bold text-navy"><?= htmlspecialchars(AppConfig::CONTACT_PHONE) ?></span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm text-amber-600">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"/></svg>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block uppercase font-bold">Headquarters</span>
                            <span class="text-lg font-bold text-navy"><?= htmlspecialchars(AppConfig::OFFICE_ADDRESS) ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-8 md:p-12 rounded-3xl shadow-xl border border-slate-200/60">
                <form @submit.prevent="submitInquiry()" class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <input type="text" x-model="inquiryForm.full_name" placeholder="Full Name *" required
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition">
                        </div>
                        <div>
                            <input type="email" x-model="inquiryForm.email" placeholder="Email Address *" required
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition">
                        </div>
                    </div>
                    <div>
                        <input type="tel" x-model="inquiryForm.phone" placeholder="Phone Number *" required
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition">
                    </div>
                    <div>
                        <textarea x-model="inquiryForm.message" placeholder="Tell us about your acquisition requirements or timeline..." rows="4" required
                                  class="w-full bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition"></textarea>
                    </div>

                    <button type="submit" :disabled="inquirySubmitting"
                            class="w-full bg-navy text-white py-4 rounded-xl font-bold text-lg hover:bg-amber-600 transition shadow-lg flex items-center justify-center gap-3 disabled:opacity-50">
                        <span x-show="!inquirySubmitting">Send Confidential Inquiry</span>
                        <span x-show="inquirySubmitting" class="flex items-center gap-2">
                            <span class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                            Transmitting...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Newsletter & Footer -->
    <footer class="bg-navy text-white pt-24 pb-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-20">
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-3xl mb-4 font-bold">Elite Estates</h3>
                    <p class="text-slate-400 max-w-sm mb-8 leading-relaxed">Stay updated with our latest private off-market listings, architectural releases, and global luxury market reports.</p>
                    
                    <form @submit.prevent="submitNewsletter()" class="flex max-w-md">
                        <input type="email" x-model="newsletterEmail" placeholder="Enter your email address" required
                               class="bg-white/10 border border-white/20 border-r-0 rounded-l-xl p-4 flex-1 text-sm outline-none focus:border-amber-500 text-white placeholder-slate-400">
                        <button type="submit" :disabled="newsletterSubmitting"
                                class="gold-gradient px-8 rounded-r-xl font-bold uppercase text-xs tracking-widest hover:opacity-90 transition disabled:opacity-50">
                            <span x-show="!newsletterSubmitting">Join</span>
                            <span x-show="newsletterSubmitting">...</span>
                        </button>
                    </form>
                </div>
                <div>
                    <h4 class="font-bold mb-6 text-amber-400 uppercase tracking-wider text-xs">Quick Links</h4>
                    <ul class="space-y-4 text-slate-400 text-sm">
                        <li><a href="#properties" class="hover:text-white transition">Featured Properties</a></li>
                        <li><a href="#services" class="hover:text-white transition">Advisory Services</a></li>
                        <li><a href="admin/login.php" class="hover:text-amber-400 transition">Broker Login</a></li>
                        <li><a href="#contact" class="hover:text-white transition">Private Viewing</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-6 text-amber-400 uppercase tracking-wider text-xs">Connect</h4>
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-amber-600 transition cursor-pointer font-bold text-xs">IG</div>
                        <div class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-amber-600 transition cursor-pointer font-bold text-xs">LI</div>
                        <div class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-amber-600 transition cursor-pointer font-bold text-xs">TW</div>
                    </div>
                </div>
            </div>
            <div class="border-t border-white/10 pt-12 text-center text-slate-500 text-xs">
                &copy; <?= date('Y') ?> Elite Estates Luxury Group. All rights reserved. Database & Backend Engine Powered by PHP & MySQL.
            </div>
        </div>
    </footer>

    <!-- Property Details Dynamic Modal (Connected to Database) -->
    <div x-show="modalOpen" class="fixed inset-0 z-[100] overflow-y-auto px-4 py-8 md:py-12" x-transition x-cloak>
        <div class="fixed inset-0 bg-navy/90 backdrop-blur-md" @click="closeModal()"></div>
        
        <div class="relative bg-white max-w-5xl mx-auto rounded-3xl overflow-hidden shadow-2xl border border-slate-100 z-10">
            <button @click="closeModal()" class="absolute top-6 right-6 z-20 bg-black/60 text-white w-10 h-10 rounded-full flex items-center justify-center hover:bg-amber-600 transition font-bold text-lg">✕</button>
            
            <template x-if="modalLoading">
                <div class="py-32 text-center">
                    <div class="w-12 h-12 border-4 border-amber-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                    <p class="text-slate-500">Retrieving property details...</p>
                </div>
            </template>

            <template x-if="!modalLoading && activeProperty">
                <div class="grid grid-cols-1 md:grid-cols-2">
                    <div class="relative h-96 md:h-full min-h-[400px] bg-slate-100">
                        <img :src="activeProperty.featured_image" :alt="activeProperty.title" class="w-full h-full object-cover">
                        <div class="absolute bottom-4 left-4 bg-black/60 backdrop-blur px-3 py-1.5 rounded-lg text-white text-xs font-semibold"
                             x-text="activeProperty.location"></div>
                    </div>
                    
                    <div class="p-8 md:p-12 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-amber-600 font-bold uppercase tracking-widest text-xs" x-text="activeProperty.tag || 'Luxury Listing'"></span>
                                <span class="font-serif text-2xl font-bold text-amber-600" x-text="activeProperty.formatted_price"></span>
                            </div>
                            
                            <h2 class="text-3xl md:text-4xl mb-4 font-bold text-navy" x-text="activeProperty.title"></h2>
                            <p class="text-slate-600 mb-6 text-sm leading-relaxed" x-text="activeProperty.description"></p>
                            
                            <!-- Specifications Grid -->
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                                    <span class="text-[11px] text-slate-400 block uppercase font-bold mb-0.5">Lot Size</span>
                                    <span class="font-bold text-navy" x-text="activeProperty.lot_size || 'N/A'"></span>
                                </div>
                                <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                                    <span class="text-[11px] text-slate-400 block uppercase font-bold mb-0.5">Year Built</span>
                                    <span class="font-bold text-navy" x-text="activeProperty.year_built || '2023'"></span>
                                </div>
                                <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                                    <span class="text-[11px] text-slate-400 block uppercase font-bold mb-0.5">Living Space</span>
                                    <span class="font-bold text-navy" x-text="(activeProperty.formatted_sqft || activeProperty.sqft) + ' SqFt'"></span>
                                </div>
                                <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                                    <span class="text-[11px] text-slate-400 block uppercase font-bold mb-0.5">Bedrooms / Baths</span>
                                    <span class="font-bold text-navy" x-text="activeProperty.beds + ' Beds / ' + activeProperty.baths + ' Baths'"></span>
                                </div>
                            </div>

                            <!-- Amenities Tags -->
                            <div class="mb-6" x-show="activeProperty.amenities_list && activeProperty.amenities_list.length > 0">
                                <span class="text-[11px] text-slate-400 block uppercase font-bold mb-2">Prime Features</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="amenity in activeProperty.amenities_list" :key="amenity">
                                        <span class="bg-amber-50 text-amber-800 text-[11px] font-semibold px-2.5 py-1 rounded-md" x-text="amenity"></span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div>
                            <button @click="bookViewing(activeProperty)" class="w-full bg-navy text-white py-4 rounded-xl font-bold text-base hover:bg-amber-600 transition shadow-lg mb-3">
                                Book Private Viewing
                            </button>
                            <p class="text-center text-xs text-slate-400">Agent Ref: <span class="font-mono font-bold text-slate-600" x-text="activeProperty.ref_code"></span></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/18005550199?text=Hello%20Elite%20Estates,%20I%20am%20interested%20in%20your%20luxury%20properties." target="_blank" rel="noopener" class="fixed bottom-8 right-8 z-40 bg-emerald-500 text-white p-4 rounded-full shadow-2xl hover:scale-110 transition active:scale-95">
        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M12.012 2c-5.508 0-9.987 4.479-9.987 9.988 0 1.757.463 3.467 1.341 4.966L2 22l5.247-1.377a9.948 9.948 0 004.765 1.214h.005c5.508 0 9.987-4.479 9.987-9.988 0-2.659-1.035-5.159-2.915-7.04a9.907 9.907 0 00-7.078-2.909z"/></svg>
    </a>

    <!-- Alpine.js Application Logic -->
    <script>
        function estateApp() {
            return {
                mobileMenu: false,
                loading: false,
                modalLoading: false,
                modalOpen: false,
                activeProperty: null,
                filterLocation: 'all',
                filterType: 'all',
                filterPrice: 'all',
                searchQuery: '',
                
                // Pre-populated from server-side PHP query for instant render
                properties: <?= json_encode($initialProperties, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>,
                
                // Forms
                inquiryForm: {
                    property_id: null,
                    full_name: '',
                    email: '',
                    phone: '',
                    message: ''
                },
                inquirySubmitting: false,
                newsletterEmail: '',
                newsletterSubmitting: false,

                // Toast notifications
                toasts: [],
                addToast(message, type = 'success') {
                    this.toasts.push({ message, type });
                    setTimeout(() => {
                        this.toasts.shift();
                    }, 5000);
                },
                removeToast(index) {
                    this.toasts.splice(index, 1);
                },

                initData() {
                    // If properties array was empty on SSR, fetch from API
                    if (this.properties.length === 0) {
                        this.applyFilters();
                    }
                },

                // Query API endpoint with active filters
                async applyFilters() {
                    this.loading = true;
                    try {
                        const params = new URLSearchParams();
                        if (this.filterLocation !== 'all') params.append('location', this.filterLocation);
                        if (this.filterType !== 'all') params.append('property_type', this.filterType);
                        if (this.filterPrice !== 'all') params.append('price_range', this.filterPrice);
                        if (this.searchQuery.trim() !== '') params.append('search', this.searchQuery.trim());

                        const res = await fetch('api/properties.php?' + params.toString());
                        const json = await res.json();
                        if (json.success) {
                            this.properties = json.data.properties;
                        }
                    } catch (err) {
                        console.error('Failed to filter properties:', err);
                    } finally {
                        this.loading = false;
                    }
                },

                resetFilters() {
                    this.filterLocation = 'all';
                    this.filterType = 'all';
                    this.filterPrice = 'all';
                    this.searchQuery = '';
                    this.applyFilters();
                },

                // Fetch dynamic property details from API
                async openPropertyModal(id) {
                    this.modalOpen = true;
                    this.modalLoading = true;
                    try {
                        const res = await fetch(`api/property.php?id=${id}`);
                        const json = await res.json();
                        if (json.success) {
                            this.activeProperty = json.data;
                        } else {
                            this.addToast(json.message || 'Unable to load listing details.', 'error');
                            this.modalOpen = false;
                        }
                    } catch (err) {
                        this.addToast('Network error while loading listing details.', 'error');
                        this.modalOpen = false;
                    } finally {
                        this.modalLoading = false;
                    }
                },

                closeModal() {
                    this.modalOpen = false;
                    this.activeProperty = null;
                },

                bookViewing(prop) {
                    this.inquiryForm.property_id = prop.id;
                    this.inquiryForm.message = `I would like to schedule a private viewing for "${prop.title}" (Ref: ${prop.ref_code}).`;
                    this.closeModal();
                    
                    // Scroll to contact form smoothly
                    const contactSection = document.getElementById('contact');
                    if (contactSection) {
                        contactSection.scrollIntoView({ behavior: 'smooth' });
                    }
                },

                async submitInquiry() {
                    this.inquirySubmitting = true;
                    try {
                        const res = await fetch('api/inquiry.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(this.inquiryForm)
                        });
                        const json = await res.json();
                        if (json.success) {
                            this.addToast(json.message, 'success');
                            this.inquiryForm = {
                                property_id: null,
                                full_name: '',
                                email: '',
                                phone: '',
                                message: ''
                            };
                        } else {
                            this.addToast(json.message || 'Failed to submit inquiry.', 'error');
                        }
                    } catch (err) {
                        this.addToast('Connection error. Please try again.', 'error');
                    } finally {
                        this.inquirySubmitting = false;
                    }
                },

                async submitNewsletter() {
                    if (!this.newsletterEmail) return;
                    this.newsletterSubmitting = true;
                    try {
                        const res = await fetch('api/newsletter.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ email: this.newsletterEmail })
                        });
                        const json = await res.json();
                        if (json.success) {
                            this.addToast(json.message, 'success');
                            this.newsletterEmail = '';
                        } else {
                            this.addToast(json.message || 'Subscription failed.', 'error');
                        }
                    } catch (err) {
                        this.addToast('Connection error. Please try again.', 'error');
                    } finally {
                        this.newsletterSubmitting = false;
                    }
                }
            };
        }
    </script>
</body>
</html>
