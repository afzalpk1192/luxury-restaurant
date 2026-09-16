
const mongoose = require('mongoose');

const menuItemSchema = new mongoose.Schema({
  name: { type: String, required: true },
  category: { type: String, required: true },
  price: { type: Number, required: true },
  description: { type: String, required: true },
  image: { type: String, required: true },
  dietary: [{ type: String }]
}, { timestamps: true });

module.exports = mongoose.model('MenuItem', menuItemSchema);