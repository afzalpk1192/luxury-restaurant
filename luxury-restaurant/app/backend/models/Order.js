const mongoose = require('mongoose');

const orderItemSchema = new mongoose.Schema({
  id: { type: mongoose.Schema.Types.Mixed },
  name: { type: String, required: true },
  price: { type: Number, required: true },
  quantity: { type: Number, required: true, default: 1 },
  category: { type: String, default: 'Main Course' }
}, { _id: false });

const orderSchema = new mongoose.Schema({
  orderNumber: { 
    type: String, 
    required: true, 
    unique: true, 
    index: true 
  },
  customerName: { 
    type: String, 
    required: true, 
    trim: true 
  },
  customerEmail: { 
    type: String, 
    required: true, 
    trim: true, 
    lowercase: true 
  },
  customerPhone: { 
    type: String, 
    required: true, 
    trim: true 
  },
  orderType: { 
    type: String, 
    enum: ['Dine-in', 'Takeaway', 'Delivery'], 
    default: 'Delivery' 
  },
  tableNumber: { 
    type: String, 
    default: '' 
  },
  deliveryAddress: { 
    type: String, 
    default: '' 
  },
  items: { 
    type: [orderItemSchema], 
    required: true, 
    validate: [arr => arr.length > 0, 'Order must contain at least one dish'] 
  },
  subtotal: { 
    type: Number, 
    required: true, 
    default: 0 
  },
  tax: { 
    type: Number, 
    required: true, 
    default: 0 
  },
  total: { 
    type: Number, 
    required: true, 
    default: 0 
  },
  specialInstructions: { 
    type: String, 
    default: '' 
  },
  paymentMethod: { 
    type: String, 
    enum: ['Card', 'Cash on Delivery', 'Pay at Counter'], 
    default: 'Cash on Delivery' 
  },
  paymentStatus: { 
    type: String, 
    enum: ['Pending', 'Paid'], 
    default: 'Pending' 
  },
  status: { 
    type: String, 
    enum: ['Pending', 'Confirmed', 'Preparing', 'Ready', 'Delivered', 'Cancelled'], 
    default: 'Pending' 
  }
}, { timestamps: true });

module.exports = mongoose.model('Order', orderSchema);
