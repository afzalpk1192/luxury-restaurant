const express = require('express');
const mongoose = require('mongoose');
const cors = require('cors');
const jwt = require('jsonwebtoken');
const bcrypt = require('bcryptjs');
const net = require('net');
const { spawn } = require('child_process');
const path = require('path');
const fs = require('fs');
require('dotenv').config();

const Reservation = require('./models/Reservation');
const MenuItem = require('./models/MenuItem');
const Admin = require('./models/Admin');
const Order = require('./models/Order');
const { seedInitialData } = require('./seed');

const JWT_SECRET = process.env.JWT_SECRET || 'letoile_luxury_secret_jwt_2026';

const app = express();
app.use(cors());
app.use(express.json());

// Helper: Check if a TCP port is open
function isPortOpen(port, host = '127.0.0.1') {
  return new Promise((resolve) => {
    const socket = new net.Socket();
    socket.setTimeout(1500);
    socket.once('connect', () => {
      socket.destroy();
      resolve(true);
    });
    socket.once('timeout', () => {
      socket.destroy();
      resolve(false);
    });
    socket.once('error', () => {
      resolve(false);
    });
    socket.connect(port, host);
  });
}

// Helper: Ensure MongoDB process is running
async function ensureMongoRunning() {
  const mongoUri = process.env.MONGO_URI || 'mongodb://127.0.0.1:27017/letoile_db';
  
  if (!mongoUri.includes('127.0.0.1') && !mongoUri.includes('localhost')) {
    return;
  }

  const isOpen = await isPortOpen(27017);
  if (isOpen) {
    console.log('[Database] MongoDB is active on port 27017.');
    return;
  }

  console.log('[Database] MongoDB is not running. Starting local MongoDB instance...');
  const candidateMongodPaths = [
    process.env.MONGOD_PATH,
    path.resolve(__dirname, '..', '..', 'mongodb', 'bin', 'mongod.exe'),
    path.resolve(__dirname, '..', 'mongodb', 'bin', 'mongod.exe'),
    path.resolve(__dirname, 'bin', 'mongod.exe')
  ].filter(Boolean);

  let mongodPath = candidateMongodPaths.find(p => fs.existsSync(p));

  const candidateDbPaths = [
    process.env.MONGOD_DBPATH,
    path.resolve(__dirname, '..', '..', 'mongodb', 'data', 'db'),
    path.resolve(__dirname, '..', 'mongodb', 'data', 'db'),
    path.resolve(__dirname, 'data', 'db')
  ].filter(Boolean);

  let dbPath = candidateDbPaths.find(p => fs.existsSync(p)) || candidateDbPaths[0];
  let logPath = path.resolve(path.dirname(dbPath), 'mongod.log');

  if (!fs.existsSync(dbPath)) {
    fs.mkdirSync(dbPath, { recursive: true });
  }

  if (mongodPath && fs.existsSync(mongodPath)) {
    console.log(`[Database] Found mongod at: ${mongodPath}`);
    console.log(`[Database] Using dbpath: ${dbPath}`);
    const mongoProcess = spawn(mongodPath, [
      '--dbpath', dbPath,
      '--port', '27017',
      '--logpath', logPath
    ], {
      detached: true,
      stdio: 'ignore'
    });
    mongoProcess.unref();

    for (let i = 0; i < 20; i++) {
      await new Promise(r => setTimeout(r, 500));
      if (await isPortOpen(27017)) {
        console.log('[Database] Local MongoDB process started successfully.');
        return;
      }
    }
    console.warn('[Database] Timed out waiting for MongoDB to open port 27017.');
  } else {
    console.warn('[Database] Local mongod binary not found in candidate paths:', candidateMongodPaths);
  }
}

// Auth Middleware
function verifyAdminToken(req, res, next) {
  const authHeader = req.headers.authorization;
  if (!authHeader || !authHeader.startsWith('Bearer ')) {
    return res.status(401).json({ success: false, message: 'Authentication required' });
  }

  const token = authHeader.split(' ')[1];
  try {
    const decoded = jwt.verify(token, JWT_SECRET);
    req.admin = decoded;
    next();
  } catch (err) {
    return res.status(401).json({ success: false, message: 'Invalid or expired token' });
  }
}

// --- HEALTH & STATUS ROUTE ---
app.get('/api/health', (req, res) => {
  const states = ['Disconnected', 'Connected', 'Connecting', 'Disconnecting'];
  const state = states[mongoose.connection.readyState] || 'Unknown';
  res.json({
    status: 'ok',
    database: state,
    timestamp: new Date().toISOString()
  });
});

// --- ADMIN AUTH ROUTES ---
app.post('/api/admin/register', async (req, res) => {
  try {
    const { name, email, password, role } = req.body;
    if (!name || !email || !password) {
      return res.status(400).json({ success: false, message: 'Name, email, and password are required' });
    }

    const cleanEmail = email.toLowerCase().trim();
    const existing = await Admin.findOne({ email: cleanEmail });
    if (existing) {
      return res.status(409).json({ success: false, message: 'Admin account with this email already exists' });
    }

    const hashedPassword = await bcrypt.hash(password, 10);
    const newAdmin = await Admin.create({
      name: name.trim(),
      email: cleanEmail,
      password: hashedPassword,
      role: role ? role.trim() : 'Manager'
    });

    const token = jwt.sign(
      { id: newAdmin._id, email: newAdmin.email, name: newAdmin.name, role: newAdmin.role },
      JWT_SECRET,
      { expiresIn: '7d' }
    );

    res.status(201).json({
      success: true,
      message: 'Admin account registered successfully',
      token,
      admin: {
        id: newAdmin._id,
        name: newAdmin.name,
        email: newAdmin.email,
        role: newAdmin.role
      }
    });
  } catch (error) {
    console.error('Admin registration error:', error);
    res.status(500).json({ success: false, message: error.message });
  }
});

app.post('/api/admin/login', async (req, res) => {
  try {
    const { email, password } = req.body;
    if (!email || !password) {
      return res.status(400).json({ success: false, message: 'Email and password are required' });
    }

    const admin = await Admin.findOne({ email: email.toLowerCase().trim() });
    if (!admin) {
      return res.status(401).json({ success: false, message: 'Invalid credentials' });
    }

    const isMatch = await bcrypt.compare(password, admin.password);
    if (!isMatch) {
      return res.status(401).json({ success: false, message: 'Invalid credentials' });
    }

    const token = jwt.sign(
      { id: admin._id, email: admin.email, name: admin.name, role: admin.role },
      JWT_SECRET,
      { expiresIn: '7d' }
    );

    res.json({
      success: true,
      message: 'Authentication successful',
      token,
      admin: {
        id: admin._id,
        name: admin.name,
        email: admin.email,
        role: admin.role
      }
    });
  } catch (error) {
    res.status(500).json({ success: false, message: error.message });
  }
});

app.get('/api/admin/verify', verifyAdminToken, (req, res) => {
  res.json({ success: true, admin: req.admin });
});

app.get('/api/admin/profile', verifyAdminToken, async (req, res) => {
  try {
    const admin = await Admin.findById(req.admin.id).select('-password');
    if (!admin) {
      return res.status(404).json({ success: false, message: 'Admin not found' });
    }
    res.json({ success: true, admin });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// --- ORDER PLACE SYSTEM ROUTES ---
app.post('/api/orders', async (req, res) => {
  try {
    const {
      customerName,
      customerEmail,
      customerPhone,
      orderType,
      tableNumber,
      deliveryAddress,
      items,
      specialInstructions,
      paymentMethod
    } = req.body;

    if (!customerName || !customerEmail || !customerPhone) {
      return res.status(400).json({ 
        success: false, 
        message: 'Customer name, email, and phone number are required' 
      });
    }

    if (!Array.isArray(items) || items.length === 0) {
      return res.status(400).json({ 
        success: false, 
        message: 'Order must include at least one dish' 
      });
    }

    // Compute subtotal and tax
    const subtotal = items.reduce((sum, item) => sum + (Number(item.price) || 0) * (Number(item.quantity) || 1), 0);
    const tax = parseFloat((subtotal * 0.1).toFixed(2));
    const total = parseFloat((subtotal + tax).toFixed(2));

    // Generate unique order number (e.g. LE-82491)
    const randomSuffix = Math.floor(10000 + Math.random() * 90000);
    const orderNumber = `LE-${randomSuffix}`;

    const newOrder = new Order({
      orderNumber,
      customerName: customerName.trim(),
      customerEmail: customerEmail.trim().toLowerCase(),
      customerPhone: customerPhone.trim(),
      orderType: orderType || 'Delivery',
      tableNumber: tableNumber ? tableNumber.trim() : '',
      deliveryAddress: deliveryAddress ? deliveryAddress.trim() : '',
      items: items.map(item => ({
        id: item.id || item._id,
        name: item.name,
        price: Number(item.price),
        quantity: Number(item.quantity) || 1,
        category: item.category || 'Main Course'
      })),
      subtotal: parseFloat(subtotal.toFixed(2)),
      tax,
      total,
      specialInstructions: specialInstructions || '',
      paymentMethod: paymentMethod || 'Cash on Delivery',
      paymentStatus: 'Pending',
      status: 'Pending'
    });

    await newOrder.save();

    res.status(201).json({
      success: true,
      message: 'Order placed successfully',
      data: newOrder
    });
  } catch (error) {
    console.error('Order creation error:', error);
    res.status(400).json({ success: false, error: error.message });
  }
});

// List all orders (Admin / Management)
app.get('/api/orders', async (req, res) => {
  try {
    const orders = await Order.find().sort({ createdAt: -1 });
    res.json({ success: true, data: orders });
  } catch (error) {
    res.status(500).json({ success: false, error: error.message });
  }
});

// Get single order by ID or orderNumber
app.get('/api/orders/:id', async (req, res) => {
  try {
    const identifier = req.params.id;
    let order;
    if (mongoose.Types.ObjectId.isValid(identifier)) {
      order = await Order.findById(identifier);
    }
    if (!order) {
      order = await Order.findOne({ orderNumber: identifier.toUpperCase() });
    }

    if (!order) {
      return res.status(404).json({ success: false, message: 'Order not found' });
    }

    res.json({ success: true, data: order });
  } catch (error) {
    res.status(500).json({ success: false, error: error.message });
  }
});

// Public track order endpoint
app.get('/api/orders/track/:orderNumber', async (req, res) => {
  try {
    const order = await Order.findOne({ 
      orderNumber: req.params.orderNumber.toUpperCase().trim() 
    });

    if (!order) {
      return res.status(404).json({ success: false, message: 'Order not found with this order number' });
    }

    res.json({
      success: true,
      data: {
        orderNumber: order.orderNumber,
        customerName: order.customerName,
        status: order.status,
        orderType: order.orderType,
        total: order.total,
        itemsCount: order.items.reduce((sum, item) => sum + item.quantity, 0),
        items: order.items,
        createdAt: order.createdAt
      }
    });
  } catch (error) {
    res.status(500).json({ success: false, error: error.message });
  }
});

// Update Order Status
app.patch('/api/orders/:id/status', async (req, res) => {
  try {
    const { status, paymentStatus } = req.body;
    const allowedStatuses = ['Pending', 'Confirmed', 'Preparing', 'Ready', 'Delivered', 'Cancelled'];

    const updateFields = {};
    if (status) {
      if (!allowedStatuses.includes(status)) {
        return res.status(400).json({ success: false, message: 'Invalid status value' });
      }
      updateFields.status = status;
    }
    if (paymentStatus && ['Pending', 'Paid'].includes(paymentStatus)) {
      updateFields.paymentStatus = paymentStatus;
    }

    const updated = await Order.findByIdAndUpdate(
      req.params.id,
      updateFields,
      { new: true }
    );

    if (!updated) {
      return res.status(404).json({ success: false, message: 'Order not found' });
    }

    res.json({ success: true, message: 'Order status updated', data: updated });
  } catch (error) {
    res.status(500).json({ success: false, error: error.message });
  }
});

// Delete Order
app.delete('/api/orders/:id', async (req, res) => {
  try {
    const deleted = await Order.findByIdAndDelete(req.params.id);
    if (!deleted) {
      return res.status(404).json({ success: false, message: 'Order not found' });
    }
    res.json({ success: true, message: 'Order deleted successfully' });
  } catch (error) {
    res.status(500).json({ success: false, error: error.message });
  }
});

// --- RESERVATION ROUTES (with embedded Orders) ---
app.post('/api/reservations', async (req, res) => {
  try {
    const { name, email, date, time, guests, orders, orderTotal, specialRequests } = req.body;
    const reservation = new Reservation({
      name,
      email,
      date,
      time,
      guests: Number(guests) || 1,
      orders: Array.isArray(orders) ? orders : [],
      orderTotal: Number(orderTotal) || 0,
      specialRequests: specialRequests || '',
      status: 'Pending'
    });

    await reservation.save();
    res.status(201).json({
      success: true,
      message: 'Reservation created successfully with pre-order details',
      data: reservation
    });
  } catch (error) {
    console.error('Reservation creation error:', error);
    res.status(400).json({ success: false, error: error.message });
  }
});

app.get('/api/reservations', async (req, res) => {
  try {
    const reservations = await Reservation.find().sort({ createdAt: -1 });
    res.json({ success: true, data: reservations });
  } catch (error) {
    res.status(500).json({ success: false, error: error.message });
  }
});

// Update Reservation Status (Admin)
app.patch('/api/reservations/:id/status', async (req, res) => {
  try {
    const { status } = req.body;
    if (!['Pending', 'Confirmed', 'Cancelled'].includes(status)) {
      return res.status(400).json({ success: false, message: 'Invalid status value' });
    }

    const updated = await Reservation.findByIdAndUpdate(
      req.params.id,
      { status },
      { new: true }
    );
    if (!updated) {
      return res.status(404).json({ success: false, message: 'Reservation not found' });
    }

    res.json({ success: true, message: 'Status updated successfully', data: updated });
  } catch (error) {
    res.status(500).json({ success: false, error: error.message });
  }
});

// Delete Reservation
app.delete('/api/reservations/:id', async (req, res) => {
  try {
    await Reservation.findByIdAndDelete(req.params.id);
    res.json({ success: true, message: 'Reservation deleted' });
  } catch (error) {
    res.status(500).json({ success: false, error: error.message });
  }
});

// --- MENU MANAGEMENT ROUTES ---
app.get('/api/menu', async (req, res) => {
  try {
    const menuItems = await MenuItem.find();
    res.json({ success: true, data: menuItems });
  } catch (error) {
    res.status(500).json({ success: false, error: error.message });
  }
});

app.post('/api/menu', async (req, res) => {
  try {
    const newItem = new MenuItem(req.body);
    await newItem.save();
    res.status(201).json({ success: true, data: newItem });
  } catch (error) {
    res.status(400).json({ success: false, error: error.message });
  }
});

app.delete('/api/menu/:id', async (req, res) => {
  try {
    await MenuItem.findByIdAndDelete(req.params.id);
    res.json({ success: true, message: 'Menu item deleted' });
  } catch (error) {
    res.status(500).json({ success: false, error: error.message });
  }
});

const PORT = process.env.PORT || 5000;

async function startServer() {
  try {
    await ensureMongoRunning();
    const mongoUri = process.env.MONGO_URI || 'mongodb://127.0.0.1:27017/letoile_db';
    await mongoose.connect(mongoUri);
    console.log('[Database] MongoDB Connected Successfully to:', mongoUri);
    
    // Seed initial data (default admin & menu items)
    await seedInitialData();

    app.listen(PORT, () => {
      console.log(`[Server] Express API server running on http://localhost:${PORT}`);
    });
  } catch (err) {
    console.error('[Error] Server startup failed:', err);
  }
}

startServer();