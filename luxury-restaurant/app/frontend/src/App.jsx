import React, { useState, useEffect } from 'react';
import Navbar from './components/Navbar';
import Hero from './components/Hero';
import About from './components/About';
import Menu from './components/Menu';
import ChefSection from './components/ChefSection';
import Reservation from './components/Reservation';
import Reviews from './components/Reviews';
import CartModal from './components/CartModal';
import Footer from './components/Footer';
import AdminDashboard from './AdminDashboard';
import AdminLogin from './AdminLogin';
import { trackOrder } from './api/restaurantApi';

export default function App() {
  const [isAdminOpen, setIsAdminOpen] = useState(() => {
    return window.location.hash === '#admin';
  });

  useEffect(() => {
    const handleHashChange = () => {
      setIsAdminOpen(window.location.hash === '#admin');
    };
    window.addEventListener('hashchange', handleHashChange);
    return () => window.removeEventListener('hashchange', handleHashChange);
  }, []);

  const [adminUser, setAdminUser] = useState(() => {
    try {
      const savedUser = localStorage.getItem('letoile_admin_user');
      return savedUser ? JSON.parse(savedUser) : null;
    } catch {
      return null;
    }
  });

  // Persistent cart in localStorage across page refreshes
  const [cartItems, setCartItems] = useState(() => {
    try {
      const savedCart = localStorage.getItem('letoile_cart');
      return savedCart ? JSON.parse(savedCart) : [];
    } catch {
      return [];
    }
  });

  const [isCartOpen, setIsCartOpen] = useState(false);

  // Active Placed Order State across page refreshes
  const [activeOrder, setActiveOrder] = useState(() => {
    try {
      const savedOrder = localStorage.getItem('letoile_active_order');
      return savedOrder ? JSON.parse(savedOrder) : null;
    } catch {
      return null;
    }
  });

  const [showOrderModal, setShowOrderModal] = useState(false);

  // Sync cart changes to localStorage
  useEffect(() => {
    try {
      localStorage.setItem('letoile_cart', JSON.stringify(cartItems));
    } catch (err) {
      console.error('Failed to sync cart to localStorage:', err);
    }
  }, [cartItems]);

  // Sync and poll active order status from backend so page refresh never loses it
  useEffect(() => {
    if (!activeOrder?.orderNumber) return;

    const syncOrderStatus = async () => {
      try {
        const res = await trackOrder(activeOrder.orderNumber);
        if (res.success && res.data) {
          setActiveOrder(prev => {
            const updated = { ...prev, ...res.data };
            try {
              localStorage.setItem('letoile_active_order', JSON.stringify(updated));
            } catch (err) {
              console.error(err);
            }
            return updated;
          });
        }
      } catch (err) {
        console.warn('Could not sync order status:', err.message);
      }
    };

    syncOrderStatus();
    const interval = setInterval(syncOrderStatus, 10000);
    return () => clearInterval(interval);
  }, [activeOrder?.orderNumber]);

  // Add dish to cart with rock-solid duplicate prevention
  const addToCart = (product) => {
    setCartItems(prev => {
      const prodId = product._id || product.id;
      const prodName = (product.name || '').trim().toLowerCase();

      const existingIndex = prev.findIndex(item => {
        const itemId = item._id || item.id;
        const itemName = (item.name || '').trim().toLowerCase();
        return (prodId && itemId && String(prodId) === String(itemId)) || (prodName && itemName && prodName === itemName);
      });

      if (existingIndex > -1) {
        return prev.map((item, idx) =>
          idx === existingIndex
            ? { ...item, quantity: (item.quantity || 1) + 1 }
            : item
        );
      }

      return [...prev, { ...product, id: prodId, quantity: 1 }];
    });
  };

  const updateQuantity = (key, delta) => {
    setCartItems(prev =>
      prev
        .map(item => {
          const itemKey = item._id || item.id || item.name;
          if (
            itemKey === key || 
            item.name === key || 
            (item._id && String(item._id) === String(key)) || 
            (item.id && String(item.id) === String(key))
          ) {
            const newQty = (item.quantity || 1) + delta;
            return newQty > 0 ? { ...item, quantity: newQty } : null;
          }
          return item;
        })
        .filter(Boolean)
    );
  };

  const clearCart = () => {
    setCartItems([]);
    try {
      localStorage.removeItem('letoile_cart');
    } catch (err) {
      console.error(err);
    }
  };

  const handleOrderPlaced = (order) => {
    setActiveOrder(order);
    try {
      localStorage.setItem('letoile_active_order', JSON.stringify(order));
    } catch (err) {
      console.error(err);
    }
  };

  const handleDismissOrder = () => {
    setActiveOrder(null);
    try {
      localStorage.removeItem('letoile_active_order');
    } catch (err) {
      console.error(err);
    }
  };

  const handleLogout = () => {
    localStorage.removeItem('letoile_admin_token');
    localStorage.removeItem('letoile_admin_user');
    setAdminUser(null);
    setIsAdminOpen(false);
    window.location.hash = '';
  };

  const handleProceedToBooking = () => {
    setIsCartOpen(false);
    const resEl = document.getElementById('reservation');
    if (resEl) {
      resEl.scrollIntoView({ behavior: 'smooth' });
    }
  };

  const totalItems = cartItems.reduce((acc, item) => acc + item.quantity, 0);

  // If Admin portal is open
  if (isAdminOpen) {
    if (!adminUser) {
      return (
        <AdminLogin
          onLoginSuccess={(user) => setAdminUser(user)}
          onBackToSite={() => {
            setIsAdminOpen(false);
            window.location.hash = '';
          }}
        />
      );
    }

    return (
      <AdminDashboard
        adminUser={adminUser}
        onLogout={handleLogout}
        onBackToSite={() => {
          setIsAdminOpen(false);
          window.location.hash = '';
        }}
      />
    );
  }

  return (
    <div className="bg-[#0b0f19] text-gray-100 font-sans min-h-screen selection:bg-amber-500 selection:text-black overflow-x-hidden w-full max-w-full">
      <Navbar 
        cartCount={totalItems} 
        onOpenCart={() => setIsCartOpen(true)} 
        onOpenAdmin={() => {
          setIsAdminOpen(true);
          window.location.hash = '#admin';
        }} 
        adminUser={adminUser}
        onLogout={handleLogout}
      />

      {/* Active Order Live Tracker Banner across refreshes */}
      {activeOrder && (
        <div className="sticky top-20 z-40 bg-gradient-to-r from-[#141b2d] via-[#1a233a] to-[#141b2d] border-b-2 border-amber-500/40 px-4 py-3 shadow-2xl backdrop-blur-md">
          <div className="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-3">
            <div className="flex items-center gap-3">
              <span className="relative flex h-3 w-3">
                <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                <span className="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
              </span>
              <div>
                <p className="text-xs font-serif text-white">
                  Active Order <span className="text-amber-400 font-bold">#{activeOrder.orderNumber}</span> &bull;{' '}
                  <span className={`px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider ${
                    activeOrder.status === 'Ready' || activeOrder.status === 'Delivered'
                      ? 'bg-emerald-950/60 text-emerald-300 border border-emerald-500/50'
                      : activeOrder.status === 'Cancelled'
                      ? 'bg-red-950/60 text-red-300 border border-red-500/50'
                      : 'bg-amber-500/20 text-amber-300 border border-amber-500/40'
                  }`}>
                    {activeOrder.status || 'Pending'}
                  </span>
                </p>
                <p className="text-[11px] text-gray-300">
                  {activeOrder.customerName ? `For ${activeOrder.customerName} • ` : ''}
                  {activeOrder.orderType} &bull; Total: ${Number(activeOrder.total || 0).toFixed(2)}
                </p>
              </div>
            </div>

            <div className="flex items-center gap-3">
              <button
                onClick={() => setShowOrderModal(true)}
                className="text-[11px] bg-amber-500/20 hover:bg-amber-500 hover:text-black text-amber-300 border border-amber-500/40 px-3 py-1.5 rounded uppercase tracking-wider font-semibold transition-all cursor-pointer"
              >
                Track Details
              </button>
              <button
                onClick={handleDismissOrder}
                className="text-[11px] text-gray-400 hover:text-gray-200 transition-colors cursor-pointer"
                title="Dismiss banner"
              >
                &times; Close
              </button>
            </div>
          </div>
        </div>
      )}

      <main>
        <Hero />
        <About />
        <Menu onAddToCart={addToCart} />
        <ChefSection />
        <Reservation 
          cartItems={cartItems} 
          onClearCart={clearCart} 
          onOpenCart={() => setIsCartOpen(true)}
        />
        <Reviews />
      </main>

      <Footer />

      {/* Cart & Direct Checkout Modal */}
      {isCartOpen && (
        <CartModal
          items={cartItems}
          onClose={() => setIsCartOpen(false)}
          onUpdateQuantity={updateQuantity}
          onClearCart={clearCart}
          onOrderPlaced={handleOrderPlaced}
          onProceedToBooking={handleProceedToBooking}
        />
      )}

      {/* Order Details Modal (when tracking active order) */}
      {showOrderModal && activeOrder && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4">
          <div className="bg-gradient-to-b from-[#111625] to-[#0b0f19] border-2 border-amber-500/40 rounded-lg p-6 max-w-md w-full shadow-2xl">
            <div className="flex justify-between items-center pb-4 border-b border-gray-800">
              <h3 className="text-xl font-serif text-white">Order #{activeOrder.orderNumber}</h3>
              <button 
                onClick={() => setShowOrderModal(false)}
                className="text-gray-400 hover:text-white text-2xl"
              >
                &times;
              </button>
            </div>
            <div className="py-4 space-y-3 text-xs">
              <div className="flex justify-between">
                <span className="text-gray-400">Current Status:</span>
                <span className="text-amber-400 font-bold uppercase tracking-wider">{activeOrder.status || 'Pending'}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-gray-400">Guest Name:</span>
                <span className="text-white">{activeOrder.customerName}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-gray-400">Dining Style:</span>
                <span className="text-gray-200">{activeOrder.orderType}</span>
              </div>
              {activeOrder.deliveryAddress && (
                <div className="flex justify-between">
                  <span className="text-gray-400">Address:</span>
                  <span className="text-gray-200 text-right max-w-[200px]">{activeOrder.deliveryAddress}</span>
                </div>
              )}
              {activeOrder.tableNumber && (
                <div className="flex justify-between">
                  <span className="text-gray-400">Table:</span>
                  <span className="text-gray-200">{activeOrder.tableNumber}</span>
                </div>
              )}
              {activeOrder.items && activeOrder.items.length > 0 && (
                <div className="pt-2 border-t border-gray-800">
                  <p className="text-amber-400 font-semibold uppercase tracking-wider mb-2">Items:</p>
                  {activeOrder.items.map((it, idx) => (
                    <div key={idx} className="flex justify-between text-gray-300 py-1">
                      <span>{it.quantity}x {it.name}</span>
                      <span className="text-amber-300">${(it.price * it.quantity).toFixed(2)}</span>
                    </div>
                  ))}
                </div>
              )}
              <div className="pt-3 border-t border-gray-800 flex justify-between font-bold text-white text-sm">
                <span>Total Amount:</span>
                <span className="text-amber-400 font-serif">${Number(activeOrder.total || 0).toFixed(2)}</span>
              </div>
            </div>
            <div className="pt-2 flex gap-3">
              <button
                onClick={() => setShowOrderModal(false)}
                className="flex-1 bg-amber-500 hover:bg-amber-400 text-black py-2.5 rounded font-semibold uppercase text-xs tracking-wider"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}