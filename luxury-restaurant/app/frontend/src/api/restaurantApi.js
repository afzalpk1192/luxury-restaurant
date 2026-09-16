const API_BASE_URL = 'http://localhost:5000/api';

// --- MENU APIS ---
export const fetchMenuItems = async () => {
  const response = await fetch(`${API_BASE_URL}/menu`);
  const data = await response.json();
  return data.data;
};

export const addMenuItem = async (menuData) => {
  const response = await fetch(`${API_BASE_URL}/menu`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(menuData)
  });
  return await response.json();
};

export const deleteMenuItem = async (id) => {
  const response = await fetch(`${API_BASE_URL}/menu/${id}`, {
    method: 'DELETE'
  });
  return await response.json();
};

// --- DIRECT ORDER PLACE APIS ---
export const createOrder = async (orderData) => {
  const response = await fetch(`${API_BASE_URL}/orders`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(orderData)
  });
  return await response.json();
};

export const fetchOrders = async () => {
  const response = await fetch(`${API_BASE_URL}/orders`);
  const data = await response.json();
  return data.data || [];
};

export const trackOrder = async (orderNumber) => {
  const response = await fetch(`${API_BASE_URL}/orders/track/${encodeURIComponent(orderNumber)}`);
  return await response.json();
};

export const updateOrderStatus = async (id, status, paymentStatus) => {
  const response = await fetch(`${API_BASE_URL}/orders/${id}/status`, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ status, paymentStatus })
  });
  return await response.json();
};

export const deleteOrder = async (id) => {
  const response = await fetch(`${API_BASE_URL}/orders/${id}`, {
    method: 'DELETE'
  });
  return await response.json();
};

// --- RESERVATION APIS ---
export const createReservation = async (reservationData) => {
  const response = await fetch(`${API_BASE_URL}/reservations`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(reservationData)
  });
  return await response.json();
};

export const fetchReservations = async () => {
  const response = await fetch(`${API_BASE_URL}/reservations`);
  const data = await response.json();
  return data.data || [];
};

export const updateReservationStatus = async (id, status) => {
  const response = await fetch(`${API_BASE_URL}/reservations/${id}/status`, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ status })
  });
  return await response.json();
};

export const deleteReservation = async (id) => {
  const response = await fetch(`${API_BASE_URL}/reservations/${id}`, {
    method: 'DELETE'
  });
  return await response.json();
};

// --- ADMIN AUTH APIS ---
export const adminLogin = async (email, password) => {
  const response = await fetch(`${API_BASE_URL}/admin/login`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, password })
  });
  return await response.json();
};

export const adminRegister = async (adminData) => {
  const response = await fetch(`${API_BASE_URL}/admin/register`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(adminData)
  });
  return await response.json();
};

export const verifyAdmin = async (token) => {
  const response = await fetch(`${API_BASE_URL}/admin/verify`, {
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });
  return await response.json();
};