const bcrypt = require('bcryptjs');
const MenuItem = require('./models/MenuItem');
const Admin = require('./models/Admin');

const initialMenuItems = [
  {
    name: "Truffle Glazed Wagyu Ribeye",
    category: "Main Course",
    price: 145,
    description: "A5 Japanese Wagyu served with black truffle reduction, smoked parsnip puree, and wild mushrooms.",
    image: "https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&q=80&w=800",
    dietary: ["Gluten-Free"]
  },
  {
    name: "Pan-Seared Chilean Sea Bass",
    category: "Main Course",
    price: 115,
    description: "Sustainably sourced sea bass, saffron risotto, baby heirloom tomatoes, and citrus emulsion.",
    image: "https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?auto=format&fit=crop&q=80&w=800",
    dietary: ["Gluten-Free"]
  },
  {
    name: "Oscietra Caviar & Blinis",
    category: "Starters",
    price: 180,
    description: "30g Royal Oscietra Caviar, traditional accompaniments, creme fraiche, and warm buckwheat blinis.",
    image: "https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?auto=format&fit=crop&q=80&w=800",
    dietary: []
  },
  {
    name: "Lobster Bisque Cappuccino",
    category: "Starters",
    price: 38,
    description: "Rich Maine lobster velvet, cognac cream, toasted brioche, and edible gold leaf garnish.",
    image: "https://images.unsplash.com/photo-1547592166-23ac45744acd?auto=format&fit=crop&q=80&w=800",
    dietary: []
  },
  {
    name: "Gold Leaf Chocolate Sphere",
    category: "Desserts",
    price: 45,
    description: "Valrhona dark chocolate dome melted with hot salted caramel, hazelnut praline, and vanilla bean gelato.",
    image: "https://images.unsplash.com/photo-1606313564200-e75d5e30476c?auto=format&fit=crop&q=80&w=800",
    dietary: ["Vegetarian"]
  },
  {
    name: "Smoked Old Fashioned Royale",
    category: "Signature Drinks",
    price: 32,
    description: "Aged Bourbon, Bitters, maple infusion, infusing hickory smoke under a crystal cloche.",
    image: "https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?auto=format&fit=crop&q=80&w=800",
    dietary: []
  }
];

async function seedInitialData() {
  try {
    // Seed Menu Items
    const count = await MenuItem.countDocuments();
    if (count === 0) {
      console.log('[Seed] Seeding initial menu items...');
      await MenuItem.insertMany(initialMenuItems);
      console.log(`[Seed] Seeded ${initialMenuItems.length} menu items.`);
    }

    // Seed Default Admin
    const defaultAdminEmail = 'admin@letoile.com';
    const existingAdmin = await Admin.findOne({ email: defaultAdminEmail });
    if (!existingAdmin) {
      console.log('[Seed] Creating default admin account...');
      const hashedPassword = await bcrypt.hash('admin123', 10);
      await Admin.create({
        name: "Executive Manager",
        email: defaultAdminEmail,
        password: hashedPassword,
        role: "General Manager"
      });
      console.log(`[Seed] Default admin created: ${defaultAdminEmail} / admin123`);
    }
  } catch (err) {
    console.error('[Seed] Error seeding data:', err.message);
  }
}

module.exports = { seedInitialData };
