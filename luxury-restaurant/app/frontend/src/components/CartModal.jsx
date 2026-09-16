import React, { useState } from 'react';
import { createOrder } from '../api/restaurantApi';

export default function CartModal({ 
  items, 
  onClose, 
  onUpdateQuantity, 
  onClearCart, 
  onOrderPlaced,
  onProceedToBooking 
}) {
  const [checkoutMode, setCheckoutMode] = useState('cart'); // 'cart', 'direct_order', 'order_success'
  const [loading, setLoading] = useState(false);
  const [orderError, setOrderError] = useState('');
  const [confirmedOrder, setConfirmedOrder] = useState(null);

  const [formData, setFormData] = useState({
    customerName: '',
    customerEmail: '',
    customerPhone: '',
    orderType: 'Delivery',
    tableNumber: '',
    deliveryAddress: '',
    specialInstructions: '',
    paymentMethod: 'Cash on Delivery'
  });

  const subtotal = items.reduce((acc, item) => acc + item.price * item.quantity, 0);
  const tax = subtotal * 0.1; // 10% tax
  const total = subtotal + tax;

  const handleDirectOrderSubmit = async (e) => {
    e.preventDefault();
    setOrderError('');
    setLoading(true);

    if (items.length === 0) {
      setOrderError('Your cart is empty. Please add dishes to order.');
      setLoading(false);
      return;
    }

    const payload = {
      customerName: formData.customerName,
      customerEmail: formData.customerEmail,
      customerPhone: formData.customerPhone,
      orderType: formData.orderType,
      tableNumber: formData.orderType === 'Dine-in' ? formData.tableNumber : '',
      deliveryAddress: formData.orderType === 'Delivery' ? formData.deliveryAddress : '',
      items: items.map(item => ({
        id: item._id || item.id,
        name: item.name,
        price: item.price,
        quantity: item.quantity,
        category: item.category || 'Main Course'
      })),
      specialInstructions: formData.specialInstructions,
      paymentMethod: formData.paymentMethod
    };

    try {
      const response = await createOrder(payload);
      if (response.success && response.data) {
        setConfirmedOrder(response.data);
        setCheckoutMode('order_success');
        if (onOrderPlaced) {
          onOrderPlaced(response.data);
        }
        if (onClearCart) {
          onClearCart();
        }
      } else {
        setOrderError(response.message || response.error || 'Failed to place order. Please try again.');
      }
    } catch (err) {
      console.error('Order submission error:', err);
      setOrderError('Could not reach the server to place order. Ensure backend is running.');
    } finally {
      setLoading(false);
    }
  };

  const handleProceedToTableBooking = () => {
    if (onProceedToBooking) {
      onProceedToBooking();
    } else {
      onClose();
      const reservationEl = document.getElementById('reservation');
      if (reservationEl) {
        reservationEl.scrollIntoView({ behavior: 'smooth' });
      }
    }
  };

  return (
    <div className="fixed inset-0 z-50 bg-black/70 backdrop-blur-md flex justify-end animate-fade-in">
      <div className="bg-gradient-to-b from-[#111625] to-[#0b0f19] w-full max-w-lg h-full p-4 sm:p-8 flex flex-col justify-between border-l-2 border-amber-500/30 shadow-2xl overflow-y-auto">
        
        <div>
          {/* Header */}
          <div className="flex justify-between items-center mb-6 pb-4 border-b-2 border-amber-500/30">
            <div>
              <h3 className="font-serif text-2xl text-white">
                {checkoutMode === 'order_success' ? 'Order Confirmed' : checkoutMode === 'direct_order' ? 'Place Your Order' : 'Your Selected Dishes'}
              </h3>
              <p className="text-[11px] text-amber-400 mt-1">
                {checkoutMode === 'order_success' ? 'Your culinary dishes are being prepared' : 'Finest gourmet dining prepared upon order'}
              </p>
            </div>
            <button 
              onClick={onClose} 
              className="text-gray-400 hover:text-amber-400 hover:scale-125 text-2xl transition-all duration-300 font-light cursor-pointer"
            >
              &times;
            </button>
          </div>

          {/* --- VIEW 1: CART ITEMS LIST --- */}
          {checkoutMode === 'cart' && (
            <>
              {items.length === 0 ? (
                <div className="flex flex-col items-center justify-center py-16">
                  <div className="w-16 h-16 rounded-full bg-amber-500/10 border border-amber-500/20 flex items-center justify-center mb-4">
                    <svg className="w-8 h-8 text-amber-500/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                  </div>
                  <p className="text-gray-400 text-center text-sm font-serif">Your order basket is empty.</p>
                  <p className="text-gray-500 text-center text-xs mt-2 max-w-xs">
                    Explore our À La Carte Menu and click "+ Add" on any exquisite culinary creation.
                  </p>
                </div>
              ) : (
                <div className="space-y-4 max-h-[50vh] overflow-y-auto pr-2 custom-scrollbar">
                  {items.map(item => {
                    const itemKey = item._id || item.id || item.name;
                    return (
                      <div 
                        key={itemKey} 
                        className="flex justify-between items-center border-b border-gray-800/60 pb-4 group hover:border-amber-500/30 transition-colors duration-300"
                      >
                        <div className="flex-1 pr-3">
                          <h4 className="text-sm font-medium text-white group-hover:text-amber-300 transition-colors duration-300">
                            {item.name}
                          </h4>
                          <p className="text-xs text-amber-400 font-semibold mt-0.5">
                            ${Number(item.price).toFixed(2)} each
                          </p>
                        </div>
                        <div className="flex items-center space-x-2 bg-[#0b0f19] p-2 rounded border border-gray-700 group-hover:border-amber-500/50 transition-all duration-300">
                          <button 
                            type="button"
                            onClick={() => onUpdateQuantity(itemKey, -1)} 
                            className="w-6 h-6 flex items-center justify-center hover:text-amber-400 text-gray-400 transition-colors duration-200 font-bold cursor-pointer"
                          >
                            −
                          </button>
                          <span className="text-xs text-white font-semibold w-6 text-center">{item.quantity}</span>
                          <button 
                            type="button"
                            onClick={() => onUpdateQuantity(itemKey, 1)} 
                            className="w-6 h-6 flex items-center justify-center hover:text-amber-400 text-gray-400 transition-colors duration-200 font-bold cursor-pointer"
                          >
                            +
                          </button>
                        </div>
                      </div>
                    );
                  })}
                </div>
              )}
            </>
          )}

          {/* --- VIEW 2: DIRECT ORDER CHECKOUT FORM --- */}
          {checkoutMode === 'direct_order' && (
            <form onSubmit={handleDirectOrderSubmit} className="space-y-4 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
              {orderError && (
                <div className="p-3 bg-red-950/60 border border-red-500/50 text-red-300 text-xs rounded">
                  {orderError}
                </div>
              )}

              <div className="bg-amber-500/10 border border-amber-500/30 p-3 rounded text-xs flex justify-between items-center text-gray-300">
                <span>Ordering <strong>{items.reduce((a, b) => a + b.quantity, 0)}</strong> item(s)</span>
                <span className="text-amber-400 font-bold text-sm">${total.toFixed(2)}</span>
              </div>

              <div>
                <label className="block text-[11px] uppercase tracking-wider text-amber-400 font-semibold mb-1">
                  Full Name <span className="text-red-400">*</span>
                </label>
                <input
                  required
                  type="text"
                  value={formData.customerName}
                  onChange={e => setFormData({ ...formData, customerName: e.target.value })}
                  className="w-full bg-[#0b0f19] border border-gray-700 focus:border-amber-400 p-2.5 text-xs text-white outline-none rounded transition-colors"
                  placeholder="e.g. Victoria Sterling"
                />
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] uppercase tracking-wider text-amber-400 font-semibold mb-1">
                    Phone Number <span className="text-red-400">*</span>
                  </label>
                  <input
                    required
                    type="tel"
                    value={formData.customerPhone}
                    onChange={e => setFormData({ ...formData, customerPhone: e.target.value })}
                    className="w-full bg-[#0b0f19] border border-gray-700 focus:border-amber-400 p-2.5 text-xs text-white outline-none rounded transition-colors"
                    placeholder="+1 (555) 000-0000"
                  />
                </div>
                <div>
                  <label className="block text-[11px] uppercase tracking-wider text-amber-400 font-semibold mb-1">
                    Email Address <span className="text-red-400">*</span>
                  </label>
                  <input
                    required
                    type="email"
                    value={formData.customerEmail}
                    onChange={e => setFormData({ ...formData, customerEmail: e.target.value })}
                    className="w-full bg-[#0b0f19] border border-gray-700 focus:border-amber-400 p-2.5 text-xs text-white outline-none rounded transition-colors"
                    placeholder="guest@domain.com"
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] uppercase tracking-wider text-amber-400 font-semibold mb-1">
                    Dining Style <span className="text-red-400">*</span>
                  </label>
                  <select
                    value={formData.orderType}
                    onChange={e => setFormData({ ...formData, orderType: e.target.value })}
                    className="w-full bg-[#0b0f19] border border-gray-700 focus:border-amber-400 p-2.5 text-xs text-white outline-none rounded transition-colors"
                  >
                    <option value="Delivery">Delivery</option>
                    <option value="Takeaway">Takeaway</option>
                    <option value="Dine-in">Dine-in Table</option>
                  </select>
                </div>
                <div>
                  <label className="block text-[11px] uppercase tracking-wider text-amber-400 font-semibold mb-1">
                    Payment Method
                  </label>
                  <select
                    value={formData.paymentMethod}
                    onChange={e => setFormData({ ...formData, paymentMethod: e.target.value })}
                    className="w-full bg-[#0b0f19] border border-gray-700 focus:border-amber-400 p-2.5 text-xs text-white outline-none rounded transition-colors"
                  >
                    <option value="Cash on Delivery">Cash on Delivery</option>
                    <option value="Card">Card</option>
                    <option value="Pay at Counter">Pay at Counter</option>
                  </select>
                </div>
              </div>

              {formData.orderType === 'Delivery' && (
                <div>
                  <label className="block text-[11px] uppercase tracking-wider text-amber-400 font-semibold mb-1">
                    Delivery Address <span className="text-red-400">*</span>
                  </label>
                  <textarea
                    required
                    rows="2"
                    value={formData.deliveryAddress}
                    onChange={e => setFormData({ ...formData, deliveryAddress: e.target.value })}
                    className="w-full bg-[#0b0f19] border border-gray-700 focus:border-amber-400 p-2.5 text-xs text-white outline-none rounded transition-colors"
                    placeholder="Suite / Street address, City, Postal Code"
                  />
                </div>
              )}

              {formData.orderType === 'Dine-in' && (
                <div>
                  <label className="block text-[11px] uppercase tracking-wider text-amber-400 font-semibold mb-1">
                    Table Number
                  </label>
                  <input
                    type="text"
                    value={formData.tableNumber}
                    onChange={e => setFormData({ ...formData, tableNumber: e.target.value })}
                    className="w-full bg-[#0b0f19] border border-gray-700 focus:border-amber-400 p-2.5 text-xs text-white outline-none rounded transition-colors"
                    placeholder="e.g. Table 7 or VIP Booth 2"
                  />
                </div>
              )}

              <div>
                <label className="block text-[11px] uppercase tracking-wider text-gray-400 font-semibold mb-1">
                  Special Kitchen Notes
                </label>
                <input
                  type="text"
                  value={formData.specialInstructions}
                  onChange={e => setFormData({ ...formData, specialInstructions: e.target.value })}
                  className="w-full bg-[#0b0f19] border border-gray-700 focus:border-amber-400 p-2.5 text-xs text-white outline-none rounded transition-colors"
                  placeholder="Allergies, extra truffle glaze, utensils..."
                />
              </div>

              <div className="pt-2 flex gap-3">
                <button
                  type="button"
                  onClick={() => setCheckoutMode('cart')}
                  className="w-1/3 border border-gray-700 text-gray-300 hover:border-amber-400 hover:text-amber-300 py-3 text-xs uppercase tracking-wider rounded transition-colors"
                >
                  &larr; Back
                </button>
                <button
                  type="submit"
                  disabled={loading}
                  className="w-2/3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-black py-3 text-xs uppercase tracking-widest font-bold rounded shadow-lg shadow-amber-500/20 disabled:opacity-50 transition-all"
                >
                  {loading ? 'Submitting Order...' : `Confirm & Place ($${total.toFixed(2)})`}
                </button>
              </div>
            </form>
          )}

          {/* --- VIEW 3: ORDER SUCCESS CONFIRMATION --- */}
          {checkoutMode === 'order_success' && confirmedOrder && (
            <div className="text-center py-6 animate-fade-in space-y-5">
              <div className="w-16 h-16 bg-amber-500/20 border-2 border-amber-500/60 rounded-full flex items-center justify-center mx-auto text-amber-400">
                <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>

              <div>
                <span className="text-[10px] uppercase tracking-[0.25em] text-amber-400 font-semibold block">Order Successfully Placed</span>
                <h4 className="text-2xl font-serif text-white mt-1">Order #{confirmedOrder.orderNumber}</h4>
                <div className="inline-block mt-2 px-3 py-1 bg-amber-500/10 border border-amber-500/40 rounded text-amber-300 text-xs font-semibold uppercase tracking-wider">
                  Status: {confirmedOrder.status || 'Pending'}
                </div>
              </div>

              <div className="bg-[#0b0f19] border border-amber-500/30 rounded p-4 text-left space-y-2 text-xs">
                <div className="flex justify-between border-b border-gray-800 pb-2">
                  <span className="text-gray-400">Guest:</span>
                  <span className="text-white font-medium">{confirmedOrder.customerName}</span>
                </div>
                <div className="flex justify-between border-b border-gray-800 pb-2">
                  <span className="text-gray-400">Dining Style:</span>
                  <span className="text-amber-300 font-medium">{confirmedOrder.orderType}</span>
                </div>
                {confirmedOrder.deliveryAddress && (
                  <div className="flex justify-between border-b border-gray-800 pb-2">
                    <span className="text-gray-400">Address:</span>
                    <span className="text-gray-200 text-right max-w-[200px] truncate">{confirmedOrder.deliveryAddress}</span>
                  </div>
                )}
                <div className="pt-2">
                  <p className="text-gray-400 text-[11px] uppercase tracking-wider mb-1">Dishes ({confirmedOrder.items?.length}):</p>
                  {confirmedOrder.items?.map((item, idx) => (
                    <div key={idx} className="flex justify-between text-gray-300 py-0.5">
                      <span>{item.quantity}x {item.name}</span>
                      <span className="text-amber-300 font-mono">${(item.price * item.quantity).toFixed(2)}</span>
                    </div>
                  ))}
                </div>
                <div className="border-t border-gray-800 pt-2 flex justify-between font-bold text-white">
                  <span>Grand Total:</span>
                  <span className="text-amber-400 font-serif text-sm">${confirmedOrder.total?.toFixed(2)}</span>
                </div>
              </div>

              <p className="text-gray-400 text-xs">
                Your order is safely saved in our kitchen queue. Even if you refresh your browser, you can track it live!
              </p>

              <button
                type="button"
                onClick={onClose}
                className="w-full bg-amber-500 hover:bg-amber-400 text-black py-3 text-xs uppercase tracking-widest font-bold rounded transition-colors"
              >
                Continue Browsing
              </button>
            </div>
          )}

        </div>

        {/* Footer Actions (Only on cart view when items exist) */}
        {checkoutMode === 'cart' && items.length > 0 && (
          <div className="pt-6 border-t-2 border-amber-500/30 space-y-4">
            <div className="space-y-1.5">
              <div className="flex justify-between text-xs text-gray-400 uppercase tracking-wider">
                <span>Subtotal</span>
                <span className="text-white font-semibold">${subtotal.toFixed(2)}</span>
              </div>
              <div className="flex justify-between text-xs text-gray-400 uppercase tracking-wider">
                <span>Estimated Tax (10%)</span>
                <span className="text-white font-semibold">${tax.toFixed(2)}</span>
              </div>
              <div className="flex justify-between bg-amber-500/10 border border-amber-500/30 rounded p-3 mt-2">
                <span className="text-xs text-gray-300 uppercase tracking-widest font-semibold">Total</span>
                <span className="text-lg font-serif font-bold text-amber-400">${total.toFixed(2)}</span>
              </div>
            </div>

            <div className="space-y-2">
              <button 
                type="button"
                onClick={() => setCheckoutMode('direct_order')}
                className="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-black py-3.5 text-xs uppercase tracking-widest font-bold hover:shadow-xl hover:shadow-amber-500/40 transition-all rounded flex items-center justify-center gap-2 cursor-pointer"
              >
                <span>Direct Food Order & Checkout</span>
                <span>→</span>
              </button>
              
              <button 
                type="button"
                onClick={handleProceedToTableBooking}
                className="w-full border border-amber-500/40 text-amber-400 hover:bg-amber-500/10 py-2.5 text-xs uppercase tracking-wider font-semibold transition-all rounded cursor-pointer"
              >
                Attach To Table Reservation
              </button>
            </div>
          </div>
        )}

      </div>
    </div>
  );
}
