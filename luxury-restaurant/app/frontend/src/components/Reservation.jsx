import React, { useState } from 'react';
import { createReservation } from '../api/restaurantApi';

export default function Reservation({ cartItems = [], onClearCart, onOpenCart }) {
  const [loading, setLoading] = useState(false);
  const [activeReservation, setActiveReservation] = useState(() => {
    try {
      const saved = localStorage.getItem('letoile_last_reservation');
      return saved ? JSON.parse(saved) : null;
    } catch {
      return null;
    }
  });

  const [formData, setFormData] = useState({
    name: '',
    email: '',
    date: '',
    time: '19:00',
    guests: '2',
    specialRequests: ''
  });

  const cartSubtotal = cartItems.reduce((acc, item) => acc + item.price * item.quantity, 0);
  const cartTotal = cartSubtotal * 1.1; // 10% tax included

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);

    const payload = {
      name: formData.name,
      email: formData.email,
      date: formData.date,
      time: formData.time,
      guests: parseInt(formData.guests, 10),
      orders: cartItems.map(item => ({
        id: item._id || item.id,
        name: item.name,
        price: item.price,
        quantity: item.quantity,
        category: item.category
      })),
      orderTotal: cartItems.length > 0 ? parseFloat(cartTotal.toFixed(2)) : 0,
      specialRequests: formData.specialRequests
    };

    try {
      const response = await createReservation(payload);
      if (response.success) {
        const savedData = {
          ...response.data,
          name: formData.name,
          email: formData.email,
          date: formData.date,
          time: formData.time,
          guests: formData.guests,
          confirmedOrder: [...cartItems],
          confirmedTotal: cartTotal,
          createdAt: new Date().toISOString()
        };

        setActiveReservation(savedData);
        try {
          localStorage.setItem('letoile_last_reservation', JSON.stringify(savedData));
        } catch (err) {
          console.error(err);
        }

        if (onClearCart) onClearCart();
      } else {
        alert('Failed to submit reservation: ' + (response.error || 'Server error'));
      }
    } catch (err) {
      console.error('Reservation submission error:', err);
      alert('Could not reach backend server to place booking. Please check that server is running.');
    } finally {
      setLoading(false);
    }
  };

  const handleBookAnother = () => {
    setActiveReservation(null);
    try {
      localStorage.removeItem('letoile_last_reservation');
    } catch (err) {
      console.error(err);
    }
    setFormData({ name: '', email: '', date: '', time: '19:00', guests: '2', specialRequests: '' });
  };

  return (
    <section id="reservation" className="py-16 sm:py-24 bg-gradient-to-b from-[#0b0f19] via-[#080b12] to-[#0b0f19] border-t border-b border-amber-500/20 scroll-mt-20 sm:scroll-mt-24">
      <div className="max-w-4xl mx-auto px-4 sm:px-6">
        <div className="text-center mb-10 sm:mb-16">
          <p className="text-amber-400 text-xs uppercase tracking-[0.25em] mb-3 font-semibold">Reserve Your Table</p>
          <h2 className="text-3xl sm:text-4xl md:text-5xl font-serif text-white mb-4 break-words">Book An Experience</h2>
          <p className="text-gray-400 text-xs sm:text-base">Secure your seat at our most intimate dining venue</p>
        </div>

        {activeReservation ? (
          <div className="bg-gradient-to-br from-[#111625] to-[#0b0f19] border-2 border-amber-500/50 p-5 sm:p-10 rounded-lg text-center shadow-2xl shadow-amber-500/20 animate-fade-in">
            <div className="w-16 h-16 bg-amber-500/20 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-amber-500/50">
              <svg className="w-8 h-8 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
              </svg>
            </div>
            <div className="inline-block px-3 py-1 bg-amber-500/10 border border-amber-500/30 rounded text-amber-300 text-xs uppercase tracking-widest font-semibold mb-3">
              Status: {activeReservation.status || 'Pending'}
            </div>
            <h3 className="text-2xl font-serif mb-2 text-amber-200">Table Reservation Confirmed!</h3>
            <p className="text-gray-300 mb-6 max-w-lg mx-auto text-sm">
              Thank you, <span className="text-amber-400 font-semibold">{activeReservation.name}</span>. We have reserved a table for <span className="text-amber-400 font-semibold">{activeReservation.guests}</span> guest(s) on <span className="text-amber-400 font-semibold">{activeReservation.date}</span> at <span className="text-amber-400 font-semibold">{activeReservation.time}</span>.
            </p>

            {activeReservation.confirmedOrder && activeReservation.confirmedOrder.length > 0 && (
              <div className="max-w-md mx-auto bg-[#0b0f19] border border-amber-500/30 rounded-lg p-5 mb-6 text-left">
                <h4 className="text-xs uppercase tracking-widest text-amber-400 font-semibold mb-3 border-b border-gray-800 pb-2">
                  Confirmed Pre-Ordered Dishes
                </h4>
                <div className="space-y-2 mb-3">
                  {activeReservation.confirmedOrder.map((item, idx) => (
                    <div key={idx} className="flex justify-between text-xs text-gray-300">
                      <span>{item.quantity}x {item.name}</span>
                      <span className="text-amber-300 font-mono">${(item.price * item.quantity).toFixed(2)}</span>
                    </div>
                  ))}
                </div>
                <div className="border-t border-gray-800 pt-2 flex justify-between text-sm font-semibold text-white">
                  <span>Total (Incl. Tax)</span>
                  <span className="text-amber-400 font-serif">${Number(activeReservation.confirmedTotal || activeReservation.orderTotal || 0).toFixed(2)}</span>
                </div>
              </div>
            )}

            <p className="text-gray-500 text-xs mb-8">
              This reservation remains active across browser refreshes. Our Maître d' is preparing for your arrival.
            </p>
            <button 
              onClick={handleBookAnother} 
              className="text-xs uppercase tracking-widest text-amber-400 border-2 border-amber-400 px-6 sm:px-8 py-3 hover:bg-amber-400 hover:text-black transition-all duration-300 font-semibold cursor-pointer"
            >
              Make Another Booking
            </button>
          </div>
        ) : (
          <form onSubmit={handleSubmit} className="bg-gradient-to-br from-[#111625] to-[#0b0f19] p-5 sm:p-10 rounded-lg border-2 border-amber-500/30 grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 shadow-2xl shadow-amber-500/10">
            
            {/* Attached Pre-Orders Notification Banner */}
            {cartItems.length > 0 && (
              <div className="md:col-span-2 bg-gradient-to-r from-amber-500/15 via-amber-500/5 to-transparent border-l-4 border-amber-500 p-4 rounded flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                  <p className="text-xs uppercase tracking-wider text-amber-400 font-bold flex items-center gap-2">
                    <span>✨</span> Culinary Pre-Order Attached ({cartItems.reduce((a, b) => a + b.quantity, 0)} items)
                  </p>
                  <p className="text-xs text-gray-300 mt-1">
                    Your selected dishes will be reserved and prepared for your arrival. Total: <strong className="text-amber-300">${cartTotal.toFixed(2)}</strong>
                  </p>
                </div>
                {onOpenCart && (
                  <button
                    type="button"
                    onClick={onOpenCart}
                    className="text-[11px] uppercase tracking-wider text-amber-400 hover:text-amber-300 underline font-semibold cursor-pointer"
                  >
                    View / Edit Dishes
                  </button>
                )}
              </div>
            )}

            <div className="space-y-2">
              <label className="block text-xs uppercase text-amber-400 mb-3 font-semibold tracking-widest">Full Name <span className="text-red-400">*</span></label>
              <input required type="text" value={formData.name} onChange={e => setFormData({...formData, name: e.target.value})} className="w-full bg-[#0b0f19] border-2 border-gray-700 hover:border-amber-500/50 focus:border-amber-400 p-4 text-white text-sm outline-none transition-all duration-300 rounded" placeholder="Lord/Lady John Doe" />
            </div>

            <div className="space-y-2">
              <label className="block text-xs uppercase text-amber-400 mb-3 font-semibold tracking-widest">Email Address <span className="text-red-400">*</span></label>
              <input required type="email" value={formData.email} onChange={e => setFormData({...formData, email: e.target.value})} className="w-full bg-[#0b0f19] border-2 border-gray-700 hover:border-amber-500/50 focus:border-amber-400 p-4 text-white text-sm outline-none transition-all duration-300 rounded" placeholder="john@domain.com" />
            </div>

            <div className="space-y-2">
              <label className="block text-xs uppercase text-amber-400 mb-3 font-semibold tracking-widest">Date <span className="text-red-400">*</span></label>
              <input required type="date" value={formData.date} onChange={e => setFormData({...formData, date: e.target.value})} className="w-full bg-[#0b0f19] border-2 border-gray-700 hover:border-amber-500/50 focus:border-amber-400 p-4 text-white text-sm outline-none transition-all duration-300 rounded" />
            </div>

            <div className="space-y-2">
              <label className="block text-xs uppercase text-amber-400 mb-3 font-semibold tracking-widest">Time <span className="text-red-400">*</span></label>
              <select value={formData.time} onChange={e => setFormData({...formData, time: e.target.value})} className="w-full bg-[#0b0f19] border-2 border-gray-700 hover:border-amber-500/50 focus:border-amber-400 p-4 text-white text-sm outline-none transition-all duration-300 rounded">
                <option value="17:00">5:00 PM</option>
                <option value="18:00">6:00 PM</option>
                <option value="19:00">7:00 PM</option>
                <option value="20:00">8:00 PM</option>
                <option value="21:00">9:00 PM</option>
                <option value="22:00">10:00 PM</option>
              </select>
            </div>

            <div className="space-y-2">
              <label className="block text-xs uppercase text-amber-400 mb-3 font-semibold tracking-widest">Guests <span className="text-red-400">*</span></label>
              <select value={formData.guests} onChange={e => setFormData({...formData, guests: e.target.value})} className="w-full bg-[#0b0f19] border-2 border-gray-700 hover:border-amber-500/50 focus:border-amber-400 p-4 text-white text-sm outline-none transition-all duration-300 rounded">
                <option value="1">1 Person</option>
                <option value="2">2 People</option>
                <option value="4">4 People</option>
                <option value="6">6 People (Private Dining)</option>
                <option value="8">8+ Grand Banquet</option>
              </select>
            </div>

            <div className="space-y-2">
              <label className="block text-xs uppercase text-amber-400 mb-3 font-semibold tracking-widest">Special Requests</label>
              <input 
                type="text" 
                value={formData.specialRequests} 
                onChange={e => setFormData({...formData, specialRequests: e.target.value})} 
                className="w-full bg-[#0b0f19] border-2 border-gray-700 hover:border-amber-500/50 focus:border-amber-400 p-4 text-white text-sm outline-none transition-all duration-300 rounded" 
                placeholder="e.g. Window booth, birthday, allergies..." 
              />
            </div>

            <div className="md:col-span-2 mt-4">
              <button 
                type="submit" 
                disabled={loading}
                className="w-full bg-gradient-to-r from-amber-500 to-amber-600 text-black font-semibold py-4 px-6 uppercase text-xs tracking-widest hover:shadow-2xl hover:shadow-amber-500/50 transition-all duration-300 rounded group disabled:opacity-50 cursor-pointer"
              >
                <span className="inline-flex items-center gap-2">
                  {loading ? 'Submitting Reservation...' : cartItems.length > 0 ? 'Place Pre-Order & Book Table' : 'Confirm Table Reservation'}
                  <span className="group-hover:translate-x-1 transition-transform duration-300">→</span>
                </span>
              </button>
            </div>
          </form>
        )}
      </div>
    </section>
  );
}
