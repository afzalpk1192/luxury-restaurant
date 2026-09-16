const mongoose = require('mongoose');

const orderItemSchema = new mongoose.Schema({
  id: { type: mongoose.Schema.Types.Mixed },
  name: { type: String, required: true },
  price: { type: Number, required: true },
  quantity: { type: Number, required: true, default: 1 },
  category: { type: String },
  dietary: [{ type: String }]
}, { _id: false });

const reservationSchema = new mongoose.Schema({
  name: { type: String, required: true },
  email: { type: String, required: true },
  date: { type: String, required: true },
  time: { type: String, required: true },
  guests: { type: Number, required: true },
  orders: [orderItemSchema],
  orderTotal: { type: Number, default: 0 },
  specialRequests: { type: String, default: '' },
  status: { type: String, enum: ['Pending', 'Confirmed', 'Cancelled'], default: 'Pending' }
}, { timestamps: true });

module.exports = mongoose.model('Reservation', reservationSchema);
