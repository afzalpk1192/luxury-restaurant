import React, { useState, useEffect } from 'react';
import { 
  updateReservationStatus, 
  deleteReservation, 
  fetchReservations, 
  fetchOrders, 
  updateOrderStatus, 
  deleteOrder, 
  fetchMenuItems, 
  addMenuItem, 
  deleteMenuItem 
} from './api/restaurantApi';

export default function AdminDashboard({ onBackToSite, onLogout, adminUser }) {
  const [activeTab, setActiveTab] = useState('orders');
  const [orders, setOrders] = useState([]);
  const [reservations, setReservations] = useState([]);
  const [menuItems, setMenuItems] = useState([]);
  const [orderFilter, setOrderFilter] = useState('All');
  const [loading, setLoading] = useState(true);
  const [actionLoading, setActionLoading] = useState(false);

  // Form State for Adding New Menu Item
  const [newItem, setNewItem] = useState({
    name: '',
    category: 'Main Course',
    price: '',
    description: '',
    image: '',
    dietary: ''
  });

  // Fetch initial data
  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    setLoading(true);
    try {
      const [ordersData, resData, menuData] = await Promise.all([
        fetchOrders().catch(() => []),
        fetchReservations().catch(() => []),
        fetchMenuItems().catch(() => [])
      ]);
      
      setOrders(ordersData || []);
      setReservations(resData || []);
      setMenuItems(menuData || []);
    } catch (err) {
      console.error('Error fetching dashboard data:', err);
    } finally {
      setLoading(false);
    }
  };

  // Status update for Orders
  const handleOrderStatusChange = async (orderId, newStatus) => {
    setActionLoading(true);
    try {
      const result = await updateOrderStatus(orderId, newStatus);
      if (result.success) {
        setOrders(prev =>
          prev.map(o => (o._id === orderId ? { ...o, status: newStatus } : o))
        );
      }
    } catch (err) {
      console.error('Failed to update order status:', err);
    } finally {
      setActionLoading(false);
    }
  };

  // Delete Order
  const handleDeleteOrder = async (orderId) => {
    if (!window.confirm('Are you sure you want to delete this order?')) return;
    try {
      const result = await deleteOrder(orderId);
      if (result.success) {
        setOrders(prev => prev.filter(o => o._id !== orderId));
      }
    } catch (err) {
      console.error('Failed to delete order:', err);
    }
  };

  // Status update for Reservations
  const handleReservationStatusChange = async (reservationId, newStatus) => {
    setActionLoading(true);
    try {
      const result = await updateReservationStatus(reservationId, newStatus);
      if (result.success) {
        setReservations(prev =>
          prev.map(r => (r._id === reservationId ? { ...r, status: newStatus } : r))
        );
      }
    } catch (err) {
      console.error('Failed to update reservation status:', err);
    } finally {
      setActionLoading(false);
    }
  };

  // Delete Reservation
  const handleDeleteReservation = async (reservationId) => {
    if (!window.confirm('Are you sure you want to delete this reservation?')) return;
    try {
      const result = await deleteReservation(reservationId);
      if (result.success) {
        setReservations(prev => prev.filter(r => r._id !== reservationId));
      }
    } catch (err) {
      console.error('Failed to delete reservation:', err);
    }
  };

  // Add Menu Item
  const handleAddMenuItem = async (e) => {
    e.preventDefault();
    const formattedItem = {
      ...newItem,
      price: parseFloat(newItem.price),
      dietary: newItem.dietary ? newItem.dietary.split(',').map(d => d.trim()) : []
    };

    try {
      const data = await addMenuItem(formattedItem);
      if (data.success) {
        setMenuItems([data.data, ...menuItems]);
        setNewItem({ name: '', category: 'Main Course', price: '', description: '', image: '', dietary: '' });
      }
    } catch (err) {
      console.error('Error adding menu item:', err);
    }
  };

  // Delete Menu Item
  const handleDeleteMenuItem = async (id) => {
    if (!window.confirm('Are you sure you want to delete this menu dish?')) return;
    try {
      const data = await deleteMenuItem(id);
      if (data.success) {
        setMenuItems(menuItems.filter(item => item._id !== id && item.id !== id));
      }
    } catch (err) {
      console.error('Error deleting item:', err);
    }
  };

  // Filtered orders list
  const filteredOrders = orderFilter === 'All'
    ? orders
    : orders.filter(o => o.status === orderFilter);

  return (
    <div className="min-h-screen bg-[#070a11] text-gray-200 p-4 sm:p-8 md:p-12">
      {/* Dashboard Header */}
      <div className="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8 pb-6 border-b border-gray-800">
        <div>
          <div className="flex items-center gap-3">
            <span className="text-amber-400 text-xs font-semibold tracking-[0.25em] uppercase">Executive Control</span>
            {adminUser && (
              <span className="bg-amber-500/10 border border-amber-500/30 text-amber-300 text-[10px] px-2.5 py-0.5 rounded uppercase tracking-wider">
                {adminUser.name} &bull; {adminUser.role || 'Manager'}
              </span>
            )}
          </div>
          <h1 className="text-3xl font-serif text-white mt-1">L'ÉTOILE Restaurant Command Center</h1>
        </div>

        <div className="flex items-center gap-3">
          <button
            onClick={fetchData}
            className="border border-gray-700 hover:border-amber-500/40 text-gray-300 hover:text-amber-300 px-4 py-2 text-xs uppercase tracking-wider transition-all rounded cursor-pointer"
          >
            Refresh
          </button>
          <button
            onClick={onBackToSite}
            className="border border-amber-500/40 text-amber-400 hover:bg-amber-500 hover:text-black px-4 py-2 text-xs uppercase tracking-widest transition-all duration-300 rounded cursor-pointer"
          >
            &larr; Guest Website
          </button>
          {onLogout && (
            <button
              onClick={onLogout}
              className="bg-red-950/40 border border-red-500/40 text-red-300 hover:bg-red-900/60 px-4 py-2 text-xs uppercase tracking-wider transition-all rounded cursor-pointer"
            >
              Sign Out
            </button>
          )}
        </div>
      </div>

      <div className="max-w-7xl mx-auto">
        {/* Navigation Tabs */}
        <div className="flex flex-wrap gap-3 mb-8">
          <button
            onClick={() => setActiveTab('orders')}
            className={`px-6 py-3 text-xs uppercase tracking-widest transition-all rounded cursor-pointer ${
              activeTab === 'orders'
                ? 'bg-amber-500 text-black font-bold shadow-lg shadow-amber-500/20'
                : 'bg-[#111625] text-gray-400 border border-gray-800 hover:border-amber-500/30'
            }`}
          >
            Food Orders ({orders.length})
          </button>
          <button
            onClick={() => setActiveTab('reservations')}
            className={`px-6 py-3 text-xs uppercase tracking-widest transition-all rounded cursor-pointer ${
              activeTab === 'reservations'
                ? 'bg-amber-500 text-black font-bold shadow-lg shadow-amber-500/20'
                : 'bg-[#111625] text-gray-400 border border-gray-800 hover:border-amber-500/30'
            }`}
          >
            Table Bookings ({reservations.length})
          </button>
          <button
            onClick={() => setActiveTab('menu')}
            className={`px-6 py-3 text-xs uppercase tracking-widest transition-all rounded cursor-pointer ${
              activeTab === 'menu'
                ? 'bg-amber-500 text-black font-bold shadow-lg shadow-amber-500/20'
                : 'bg-[#111625] text-gray-400 border border-gray-800 hover:border-amber-500/30'
            }`}
          >
            Menu Management ({menuItems.length})
          </button>
        </div>

        {loading ? (
          <div className="text-center py-24 text-gray-500 text-sm tracking-wider uppercase">Loading Management Data...</div>
        ) : (
          <>
            {/* ================= TAB 1: FOOD ORDERS ================= */}
            {activeTab === 'orders' && (
              <div className="bg-[#111625] border border-gray-800 rounded overflow-hidden">
                <div className="p-6 border-b border-gray-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                  <div>
                    <h2 className="text-lg font-serif text-white">Live Food Orders Queue</h2>
                    <p className="text-xs text-gray-400 mt-0.5">Real-time orders placed by guests across the website</p>
                  </div>

                  {/* Filter Pills */}
                  <div className="flex flex-wrap gap-1.5">
                    {['All', 'Pending', 'Confirmed', 'Preparing', 'Ready', 'Delivered', 'Cancelled'].map(st => (
                      <button
                        key={st}
                        onClick={() => setOrderFilter(st)}
                        className={`text-[10px] uppercase tracking-wider px-2.5 py-1 rounded transition-colors ${
                          orderFilter === st
                            ? 'bg-amber-500 text-black font-bold'
                            : 'bg-[#0b0f19] text-gray-400 border border-gray-800 hover:border-gray-700'
                        }`}
                      >
                        {st}
                      </button>
                    ))}
                  </div>
                </div>

                {filteredOrders.length === 0 ? (
                  <div className="p-12 text-center text-gray-500 text-sm">
                    No orders matching filter "{orderFilter}".
                  </div>
                ) : (
                  <div className="overflow-x-auto">
                    <table className="w-full text-left text-xs">
                      <thead className="bg-[#0b0f19] uppercase tracking-wider text-gray-400 border-b border-gray-800">
                        <tr>
                          <th className="p-4">Order #</th>
                          <th className="p-4">Guest Info</th>
                          <th className="p-4">Type & Details</th>
                          <th className="p-4 min-w-[220px]">Dishes</th>
                          <th className="p-4">Total</th>
                          <th className="p-4">Payment</th>
                          <th className="p-4">Status</th>
                          <th className="p-4 text-right">Actions</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-gray-800/60">
                        {filteredOrders.map((ord) => (
                          <tr key={ord._id} className="hover:bg-[#161c2e] transition-colors">
                            <td className="p-4 font-mono font-bold text-amber-400">
                              #{ord.orderNumber}
                              <p className="text-[10px] text-gray-500 font-sans mt-0.5">
                                {new Date(ord.createdAt).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                              </p>
                            </td>
                            <td className="p-4">
                              <p className="font-semibold text-white">{ord.customerName}</p>
                              <p className="text-[11px] text-gray-400">{ord.customerPhone}</p>
                              <p className="text-[10px] text-gray-500">{ord.customerEmail}</p>
                            </td>
                            <td className="p-4">
                              <span className={`inline-block px-2 py-0.5 rounded text-[10px] uppercase tracking-wider font-semibold ${
                                ord.orderType === 'Delivery' 
                                  ? 'bg-blue-950/60 text-blue-300 border border-blue-500/40' 
                                  : ord.orderType === 'Dine-in'
                                  ? 'bg-purple-950/60 text-purple-300 border border-purple-500/40'
                                  : 'bg-emerald-950/60 text-emerald-300 border border-emerald-500/40'
                              }`}>
                                {ord.orderType}
                              </span>
                              {ord.deliveryAddress && (
                                <p className="text-[11px] text-gray-400 mt-1 max-w-[160px] truncate" title={ord.deliveryAddress}>
                                  {ord.deliveryAddress}
                                </p>
                              )}
                              {ord.tableNumber && (
                                <p className="text-[11px] text-amber-300 mt-1">Table: {ord.tableNumber}</p>
                              )}
                              {ord.specialInstructions && (
                                <p className="text-[10px] text-amber-200/80 italic mt-0.5">"{ord.specialInstructions}"</p>
                              )}
                            </td>
                            <td className="p-4">
                              <div className="space-y-1">
                                {ord.items?.map((it, idx) => (
                                  <div key={idx} className="flex items-center gap-2">
                                    <span className="bg-amber-500/20 text-amber-300 font-bold px-1.5 py-0.5 rounded text-[10px]">
                                      {it.quantity}x
                                    </span>
                                    <span className="text-gray-200">{it.name}</span>
                                    <span className="text-gray-500 text-[10px]">(${Number(it.price).toFixed(2)})</span>
                                  </div>
                                ))}
                              </div>
                            </td>
                            <td className="p-4 font-semibold font-serif text-amber-400 text-sm">
                              ${Number(ord.total || 0).toFixed(2)}
                            </td>
                            <td className="p-4 text-[11px] text-gray-300">
                              {ord.paymentMethod}
                            </td>
                            <td className="p-4">
                              <select
                                value={ord.status || 'Pending'}
                                onChange={(e) => handleOrderStatusChange(ord._id, e.target.value)}
                                disabled={actionLoading}
                                className={`text-[11px] font-semibold uppercase px-2.5 py-1.5 rounded border outline-none cursor-pointer transition-colors ${
                                  ord.status === 'Ready' || ord.status === 'Delivered'
                                    ? 'bg-emerald-950/60 text-emerald-300 border-emerald-500/50'
                                    : ord.status === 'Cancelled'
                                    ? 'bg-red-950/60 text-red-300 border-red-500/50'
                                    : ord.status === 'Preparing'
                                    ? 'bg-blue-950/60 text-blue-300 border-blue-500/50'
                                    : 'bg-amber-950/60 text-amber-300 border-amber-500/50'
                                }`}
                              >
                                <option value="Pending" className="bg-[#111625] text-amber-300">Pending</option>
                                <option value="Confirmed" className="bg-[#111625] text-sky-300">Confirmed</option>
                                <option value="Preparing" className="bg-[#111625] text-blue-300">Preparing</option>
                                <option value="Ready" className="bg-[#111625] text-emerald-300">Ready</option>
                                <option value="Delivered" className="bg-[#111625] text-emerald-400">Delivered</option>
                                <option value="Cancelled" className="bg-[#111625] text-red-400">Cancelled</option>
                              </select>
                            </td>
                            <td className="p-4 text-right">
                              <button
                                onClick={() => handleDeleteOrder(ord._id)}
                                className="text-red-400 hover:text-red-300 text-xs hover:underline cursor-pointer"
                              >
                                Delete
                              </button>
                            </td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                )}
              </div>
            )}

            {/* ================= TAB 2: TABLE RESERVATIONS ================= */}
            {activeTab === 'reservations' && (
              <div className="bg-[#111625] border border-gray-800 rounded overflow-hidden">
                <div className="p-6 border-b border-gray-800 flex justify-between items-center">
                  <div>
                    <h2 className="text-lg font-serif text-white">Table Reservations & Pre-Ordered Banquets</h2>
                    <p className="text-xs text-gray-400 mt-0.5">Dining hall reservations registered in the guest portal</p>
                  </div>
                  <span className="text-xs text-amber-400">{reservations.length} total entries</span>
                </div>

                {reservations.length === 0 ? (
                  <p className="p-12 text-center text-gray-500 text-sm">No reservations recorded yet.</p>
                ) : (
                  <div className="overflow-x-auto">
                    <table className="w-full text-left text-xs">
                      <thead className="bg-[#0b0f19] uppercase tracking-wider text-gray-400 border-b border-gray-800">
                        <tr>
                          <th className="p-4">Guest</th>
                          <th className="p-4">Contact</th>
                          <th className="p-4">Date & Time</th>
                          <th className="p-4">Party Size</th>
                          <th className="p-4 min-w-[220px]">Pre-Ordered Dishes</th>
                          <th className="p-4">Order Total</th>
                          <th className="p-4">Status</th>
                          <th className="p-4 text-right">Actions</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-gray-800/60">
                        {reservations.map((res, i) => (
                          <tr key={res._id || i} className="hover:bg-[#161c2e] transition-colors">
                            <td className="p-4 font-medium text-white">
                              {res.name}
                              {res.specialRequests && (
                                <p className="text-[10px] text-gray-400 italic mt-0.5">Note: "{res.specialRequests}"</p>
                              )}
                            </td>
                            <td className="p-4 text-gray-400">{res.email}</td>
                            <td className="p-4 text-amber-200">{res.date} at {res.time}</td>
                            <td className="p-4 text-gray-300">{res.guests} Guests</td>
                            <td className="p-4">
                              {res.orders && res.orders.length > 0 ? (
                                <div className="space-y-1">
                                  {res.orders.map((ord, idx) => (
                                    <div key={idx} className="flex items-center gap-2">
                                      <span className="bg-amber-500/20 text-amber-300 font-bold px-1.5 py-0.5 rounded text-[10px]">
                                        {ord.quantity}x
                                      </span>
                                      <span className="text-gray-200">{ord.name}</span>
                                      <span className="text-gray-500 text-[10px]">(${ord.price})</span>
                                    </div>
                                  ))}
                                </div>
                              ) : (
                                <span className="text-gray-500 italic text-[11px]">Table booking only</span>
                              )}
                            </td>
                            <td className="p-4 font-semibold text-amber-400">
                              {res.orderTotal && res.orderTotal > 0 ? `$${res.orderTotal.toFixed(2)}` : '—'}
                            </td>
                            <td className="p-4">
                              <select
                                value={res.status || 'Pending'}
                                onChange={(e) => handleReservationStatusChange(res._id, e.target.value)}
                                disabled={actionLoading}
                                className={`text-[11px] font-semibold uppercase px-2 py-1 rounded border outline-none cursor-pointer transition-colors ${
                                  res.status === 'Confirmed'
                                    ? 'bg-emerald-950/40 text-emerald-300 border-emerald-500/40'
                                    : res.status === 'Cancelled'
                                    ? 'bg-red-950/40 text-red-300 border-red-500/40'
                                    : 'bg-amber-950/40 text-amber-300 border-amber-500/40'
                                }`}
                              >
                                <option value="Pending" className="bg-[#111625] text-amber-300">Pending</option>
                                <option value="Confirmed" className="bg-[#111625] text-emerald-300">Confirmed</option>
                                <option value="Cancelled" className="bg-[#111625] text-red-300">Cancelled</option>
                              </select>
                            </td>
                            <td className="p-4 text-right">
                              <button
                                onClick={() => handleDeleteReservation(res._id)}
                                className="text-red-400 hover:text-red-300 text-xs hover:underline cursor-pointer"
                              >
                                Delete
                              </button>
                            </td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                )}
              </div>
            )}

            {/* ================= TAB 3: MENU MANAGEMENT ================= */}
            {activeTab === 'menu' && (
              <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {/* Form to Add New Menu Item */}
                <div className="bg-[#111625] border border-gray-800 p-6 rounded h-fit">
                  <h2 className="text-lg font-serif text-white mb-6 pb-2 border-b border-gray-800">Add New Dish</h2>
                  <form onSubmit={handleAddMenuItem} className="space-y-4">
                    <div>
                      <label className="block text-[10px] uppercase tracking-wider text-gray-400 mb-1">Dish Name</label>
                      <input
                        required
                        type="text"
                        value={newItem.name}
                        onChange={e => setNewItem({ ...newItem, name: e.target.value })}
                        className="w-full bg-[#0b0f19] border border-gray-700 p-2.5 text-xs text-white focus:border-amber-400 outline-none rounded"
                        placeholder="e.g. Wagyu Tartare"
                      />
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                      <div>
                        <label className="block text-[10px] uppercase tracking-wider text-gray-400 mb-1">Category</label>
                        <select
                          value={newItem.category}
                          onChange={e => setNewItem({ ...newItem, category: e.target.value })}
                          className="w-full bg-[#0b0f19] border border-gray-700 p-2.5 text-xs text-white focus:border-amber-400 outline-none rounded"
                        >
                          <option value="Starters">Starters</option>
                          <option value="Main Course">Main Course</option>
                          <option value="Desserts">Desserts</option>
                          <option value="Signature Drinks">Signature Drinks</option>
                        </select>
                      </div>

                      <div>
                        <label className="block text-[10px] uppercase tracking-wider text-gray-400 mb-1">Price ($)</label>
                        <input
                          required
                          type="number"
                          step="0.01"
                          value={newItem.price}
                          onChange={e => setNewItem({ ...newItem, price: e.target.value })}
                          className="w-full bg-[#0b0f19] border border-gray-700 p-2.5 text-xs text-white focus:border-amber-400 outline-none rounded"
                          placeholder="120"
                        />
                      </div>
                    </div>

                    <div>
                      <label className="block text-[10px] uppercase tracking-wider text-gray-400 mb-1">Description</label>
                      <textarea
                        required
                        rows="3"
                        value={newItem.description}
                        onChange={e => setNewItem({ ...newItem, description: e.target.value })}
                        className="w-full bg-[#0b0f19] border border-gray-700 p-2.5 text-xs text-white focus:border-amber-400 outline-none rounded"
                        placeholder="Detailed flavor profile, cooking methods..."
                      ></textarea>
                    </div>

                    <div>
                      <label className="block text-[10px] uppercase tracking-wider text-gray-400 mb-1">Image URL</label>
                      <input
                        required
                        type="url"
                        value={newItem.image}
                        onChange={e => setNewItem({ ...newItem, image: e.target.value })}
                        className="w-full bg-[#0b0f19] border border-gray-700 p-2.5 text-xs text-white focus:border-amber-400 outline-none rounded"
                        placeholder="https://images.unsplash.com/..."
                      />
                    </div>

                    <div>
                      <label className="block text-[10px] uppercase tracking-wider text-gray-400 mb-1">Dietary Tags (comma-separated)</label>
                      <input
                        type="text"
                        value={newItem.dietary}
                        onChange={e => setNewItem({ ...newItem, dietary: e.target.value })}
                        className="w-full bg-[#0b0f19] border border-gray-700 p-2.5 text-xs text-white focus:border-amber-400 outline-none rounded"
                        placeholder="Gluten-Free, Vegetarian, Dairy-Free"
                      />
                    </div>

                    <button
                      type="submit"
                      className="w-full bg-amber-500 hover:bg-amber-400 text-black py-3 text-xs uppercase tracking-widest font-semibold transition-all mt-4 rounded cursor-pointer"
                    >
                      Publish Dish To Live Menu
                    </button>
                  </form>
                </div>

                {/* List of Current Menu Items */}
                <div className="lg:col-span-2 space-y-4">
                  <div className="flex justify-between items-center pb-2 border-b border-gray-800">
                    <h2 className="text-lg font-serif text-white">Live Menu Roster ({menuItems.length})</h2>
                    <span className="text-xs text-gray-500">Real-time MongoDB documents</span>
                  </div>

                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {menuItems.map(item => (
                      <div key={item._id || item.id} className="bg-[#111625] border border-gray-800 p-4 rounded flex gap-4 relative group">
                        <img src={item.image} alt={item.name} className="w-20 h-20 object-cover rounded bg-black flex-shrink-0" />
                        <div className="flex-1 min-w-0">
                          <div className="flex justify-between items-start">
                            <h3 className="text-sm font-serif text-white truncate">{item.name}</h3>
                            <span className="text-amber-400 font-semibold text-xs ml-2">${item.price}</span>
                          </div>
                          <span className="inline-block text-[9px] uppercase tracking-wider text-gray-400 border border-gray-800 px-1.5 py-0.5 my-1 rounded">
                            {item.category}
                          </span>
                          <p className="text-[11px] text-gray-400 line-clamp-2">{item.description}</p>
                        </div>
                        <button
                          onClick={() => handleDeleteMenuItem(item._id || item.id)}
                          className="absolute top-2 right-2 text-gray-600 hover:text-red-400 p-1 opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"
                          title="Delete Dish"
                        >
                          &times;
                        </button>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            )}
          </>
        )}
      </div>
    </div>
  );
}