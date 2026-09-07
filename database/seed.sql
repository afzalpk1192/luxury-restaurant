-- ==============================================================================
-- Elite Estates - Luxury Seed Data
-- ==============================================================================

USE `elite_estates`;

-- Disable FK checks during seeding
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `property_images`;
TRUNCATE TABLE `inquiries`;
TRUNCATE TABLE `subscribers`;
TRUNCATE TABLE `properties`;
TRUNCATE TABLE `admins`;
SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------------------------
-- 1. Insert Default Administrator
-- Username: admin / admin@eliteestates.com
-- Password: Admin@12345
-- ------------------------------------------------------------------------------
INSERT INTO `admins` (`id`, `username`, `email`, `password_hash`, `full_name`, `role`) VALUES
(1, 'admin', 'admin@eliteestates.com', '$2y$12$b4TcXrgKmAaKOXdm1tRPIuqixAqofLGr.8LkmTE3tdOR1jhM9mN5y', 'Alexander Vance (Senior Director)', 'superadmin');

-- ------------------------------------------------------------------------------
-- 2. Insert Luxury Properties
-- ------------------------------------------------------------------------------
INSERT INTO `properties` (
    `id`, `ref_code`, `title`, `slug`, `tag`, `property_type`,
    `location`, `city`, `state`, `address`,
    `price`, `beds`, `baths`, `sqft`, `lot_size`, `year_built`,
    `featured_image`, `description`, `amenities`, `status`, `is_featured`
) VALUES
(
    1,
    'EE-99210-MAL',
    'Oceanfront Majesty',
    'oceanfront-majesty',
    'For Sale',
    'Modern Villa',
    'Malibu, California',
    'Malibu',
    'California',
    '31200 Broad Beach Rd, Malibu, CA 90265',
    12500000.00,
    5,
    6.0,
    7500,
    '2.5 Acres',
    2023,
    'https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&q=80&w=1200',
    'This architectural masterpiece offers panoramic Pacific Ocean views, floor-to-ceiling motorized Fleetwood glass walls, a zero-edge infinity pool cascading toward the beach, and state-of-the-art Lutron smart home integration. Custom Italian marble finishes, temperature-controlled 800-bottle wine room, and a private wellness sanctuary with cedar sauna.',
    '["Private Beach Access", "Infinity Edge Pool", "Smart Home Automation", "Temperature-Controlled Wine Cellar", "Private Wellness Spa", "Home Cinema Theater", "Sub-Zero & Miele Kitchen"]',
    'active',
    1
),
(
    2,
    'EE-48102-BEV',
    'Modernist Skyline Villa',
    'modernist-skyline-villa',
    'New Listing',
    'Modern Villa',
    'Beverly Hills, CA',
    'Beverly Hills',
    'California',
    '1145 Loma Linda Dr, Beverly Hills, CA 90210',
    8900000.00,
    4,
    4.0,
    4200,
    '1.2 Acres',
    2024,
    'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&q=80&w=1200',
    'Perched in prime Beverly Hills, this newly minted modernist estate commands sweeping 270-degree views from Downtown Los Angeles to Santa Monica. Highlights include cantilevered architectural terraces, terrazzo flooring, bespoke walnut cabinetry, and an open-concept indoor-outdoor layout designed for world-class entertaining.',
    '["Panoramic Skyline Views", "Cantilevered Terraces", "Outdoor Kitchen & BBQ", "Gated Motor Court", "Poliform Custom Closets", "Designer Fireplace", "Security Control Room"]',
    'active',
    1
),
(
    3,
    'EE-77301-ASP',
    'The Glass House',
    'the-glass-house',
    'For Sale',
    'Modern Villa',
    'Aspen, Colorado',
    'Aspen',
    'Colorado',
    '450 Red Mountain Rd, Aspen, CO 81611',
    15200000.00,
    6,
    7.0,
    9800,
    '4.8 Acres',
    2022,
    'https://images.unsplash.com/photo-1600566753376-12c8ab7fb75b?auto=format&fit=crop&q=80&w=1200',
    'Nestled in the prestigious Red Mountain enclave of Aspen, The Glass House is a triumph of alpine contemporary architecture. Constructed with geothermal heating, radiant snow-melt driveways, triple-pane structural glass, heated indoor lap pool, and direct private ski trail connectivity.',
    '["Direct Ski-In / Ski-Out", "Geothermal Heating", "Heated Indoor Lap Pool", "Snow-Melt Driveway", "Bunk Room & Game Lounge", "Vaulted Cedar Ceilings", "Expansive Mountain Vista Deck"]',
    'active',
    1
),
(
    4,
    'EE-33139-MIA',
    'The Grand Penthouse at One Ocean',
    'the-grand-penthouse-at-one-ocean',
    'Exclusive',
    'Penthouse',
    'Miami, FL',
    'Miami Beach',
    'Florida',
    '1 Ocean Dr, Penthouse 1, Miami Beach, FL 33139',
    18500000.00,
    5,
    6.5,
    6800,
    'Rooftop Terrace',
    2023,
    'https://images.unsplash.com/photo-1567496898669-ee935f5f647a?auto=format&fit=crop&q=80&w=1200',
    'Crowning the South of Fifth district, this palatial duplex penthouse features a private rooftop pool, 3,000 sq ft wrap-around terrace, private high-speed elevator entry, and uninterrupted Atlantic sunrise-to-sunset vistas. Full concierge service, 4 reserved subterranean parking bays, and private yacht slip available.',
    '["Private Rooftop Pool", "Private Elevator Access", "3,000 SqFt Wrap-around Terrace", "24/7 White-Glove Concierge", "Private Yacht Slip Rights", "Boffi Kitchen", "Direct Atlantic Oceanfront"]',
    'active',
    1
),
(
    5,
    'EE-10021-MAN',
    'Upper East Side Classic Townhouse',
    'upper-east-side-classic-townhouse',
    'Featured',
    'Townhouse',
    'Manhattan, NY',
    'New York',
    'New York',
    '18 E 73rd St, New York, NY 10021',
    14200000.00,
    6,
    5.5,
    8100,
    'Private Garden',
    2021,
    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&q=80&w=1200',
    'A landmark 22-foot-wide limestone townhouse steps from Central Park. Meticulously restored with six levels of grand living, elevator serving all floors, historic wood-burning fireplaces, private landscaped Japanese zen garden, and a finished English basement featuring a fitness studio and tasting cellar.',
    '["Elevator to All 6 Levels", "Private Landscaped Garden", "Steps from Central Park", "6 Wood-Burning Fireplaces", "Wine Tasting Cellar", "Private Gym", "Limestone Historic Facade"]',
    'active',
    1
),
(
    6,
    'EE-33480-PLM',
    'Villa Bellisima Palm Beach',
    'villa-bellisima-palm-beach',
    'New Listing',
    'Modern Villa',
    'Miami, FL',
    'Palm Beach',
    'Florida',
    '520 S Ocean Blvd, Palm Beach, FL 33480',
    22000000.00,
    7,
    8.5,
    11500,
    '3.2 Acres',
    2024,
    'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&q=80&w=1200',
    'A sprawling Mediterranean-modern transitional estate boasting 200 feet of direct Lake Worth Lagoon frontage with private deep-water dock. Complete with detached two-bedroom guest villa, dual tennis/pickleball court, 60-foot resort swimming pool, and bespoke finishes curated by world-renowned interior designers.',
    '["Private Deep-Water Dock", "Resort Pool & Cabana", "Detached 2-Bed Guest House", "Pickleball & Tennis Court", "Lakeside Loggia", "Dual Master Suites", "Commercial Grade Chef Kitchen"]',
    'active',
    1
);

-- ------------------------------------------------------------------------------
-- 3. Insert Property Gallery Images
-- ------------------------------------------------------------------------------
INSERT INTO `property_images` (`property_id`, `image_url`, `display_order`, `caption`) VALUES
(1, 'https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&q=80&w=1200', 1, 'Front Facade & Ocean Deck'),
(1, 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&q=80&w=1200', 2, 'Great Room & Fleetwood Glass'),
(1, 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&q=80&w=1200', 3, 'Primary Suite Terrace'),
(2, 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&q=80&w=1200', 1, 'Aerial Architecture'),
(2, 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&q=80&w=1200', 2, 'Designer Living Quarters'),
(3, 'https://images.unsplash.com/photo-1600566753376-12c8ab7fb75b?auto=format&fit=crop&q=80&w=1200', 1, 'Aspen Red Mountain Frontage'),
(3, 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&q=80&w=1200', 2, 'Indoor Heated Pool');

-- ------------------------------------------------------------------------------
-- 4. Insert Initial Mock Inquiries
-- ------------------------------------------------------------------------------
INSERT INTO `inquiries` (`property_id`, `full_name`, `email`, `phone`, `message`, `status`) VALUES
(1, 'Lord Harrison Sterling', 'sterling@investmentcapital.uk', '+44 20 7946 0912', 'Interested in scheduling an in-person confidential viewing of Oceanfront Majesty for this coming weekend.', 'in_review'),
(2, 'Dr. Victoria Chen', 'vchen@stanfordmed.org', '+1 (310) 555-0182', 'Inquiring regarding financing options and HOA/tax figures for the Modernist Skyline Villa.', 'new'),
(NULL, 'Marcus Dupont', 'm.dupont@geneve-holdings.ch', '+41 22 819 1800', 'General portfolio inquiry: looking for a trophy modern villa in Southern California or South Florida in the $15M-$25M bracket.', 'contacted');

-- ------------------------------------------------------------------------------
-- 5. Insert Sample Subscribers
-- ------------------------------------------------------------------------------
INSERT INTO `subscribers` (`email`, `status`) VALUES
('vip.acquisitions@luxurycap.com', 'subscribed'),
('elena.rostova@monaco-wealth.mc', 'subscribed'),
('david.becker@nycinvestors.com', 'subscribed');
