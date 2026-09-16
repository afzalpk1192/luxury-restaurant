import React, { useState } from 'react';
import { adminLogin } from './api/restaurantApi';

export default function AdminLogin({ onLoginSuccess, onBackToSite }) {
  const [email, setEmail] = useState('admin@letoile.com');
  const [password, setPassword] = useState('admin123');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      const data = await adminLogin(email, password);
      if (data.success && data.token) {
        localStorage.setItem('letoile_admin_token', data.token);
        localStorage.setItem('letoile_admin_user', JSON.stringify(data.admin));
        onLoginSuccess(data.admin);
      } else {
        setError(data.message || 'Invalid email or password');
      }
    } catch (err) {
      setError('Unable to reach server. Please ensure backend is running.');
    } finally {
      setLoading(false);
    }
  };

  const handleFillDemo = () => {
    setEmail('admin@letoile.com');
    setPassword('admin123');
    setError('');
  };

  return (
    <div className="min-h-screen bg-[#070a11] flex items-center justify-center p-6 relative overflow-hidden">
      {/* Background Decorative Glow */}
      <div className="absolute w-96 h-96 bg-amber-500/10 rounded-full blur-3xl -top-20 -left-20 pointer-events-none"></div>
      <div className="absolute w-96 h-96 bg-amber-600/10 rounded-full blur-3xl -bottom-20 -right-20 pointer-events-none"></div>

      <div className="w-full max-w-md bg-gradient-to-b from-[#111625] to-[#0b0f19] border-2 border-amber-500/30 rounded-lg p-8 shadow-2xl shadow-black/80 relative z-10">
        <div className="text-center mb-8">
          <p className="text-amber-400 text-[11px] uppercase tracking-[0.25em] font-semibold mb-2">Restricted Access</p>
          <h1 className="text-3xl font-serif text-white tracking-wide">L'ÉTOILE Executive</h1>
          <p className="text-gray-400 text-xs mt-2">Management & Culinary Control Portal</p>
        </div>

        {error && (
          <div className="mb-6 p-3 bg-red-950/50 border border-red-500/40 text-red-300 text-xs rounded text-center">
            {error}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-5">
          <div>
            <label className="block text-[11px] uppercase tracking-wider text-gray-300 font-semibold mb-2">
              Manager Email
            </label>
            <input
              type="email"
              required
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              placeholder="admin@letoile.com"
              className="w-full bg-[#0b0f19] border-2 border-gray-700 focus:border-amber-400 p-3.5 text-xs text-white outline-none rounded transition-all duration-300"
            />
          </div>

          <div>
            <label className="block text-[11px] uppercase tracking-wider text-gray-300 font-semibold mb-2">
              Password
            </label>
            <input
              type="password"
              required
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              placeholder="••••••••"
              className="w-full bg-[#0b0f19] border-2 border-gray-700 focus:border-amber-400 p-3.5 text-xs text-white outline-none rounded transition-all duration-300"
            />
          </div>

          <div className="pt-2">
            <button
              type="submit"
              disabled={loading}
              className="w-full bg-gradient-to-r from-amber-500 to-amber-600 text-black py-3.5 text-xs uppercase tracking-widest font-bold hover:shadow-xl hover:shadow-amber-500/30 transition-all duration-300 rounded disabled:opacity-50"
            >
              {loading ? 'Authenticating...' : 'Sign In To Portal →'}
            </button>
          </div>
        </form>

        {/* Demo Credentials Quick Fill */}
        <div className="mt-6 pt-6 border-t border-gray-800/80 flex flex-col items-center gap-3">
          <div className="text-[11px] text-gray-500 text-center">
            Default credentials: <span className="text-amber-400/90 font-mono">admin@letoile.com</span> / <span className="text-amber-400/90 font-mono">admin123</span>
          </div>
          <button
            type="button"
            onClick={handleFillDemo}
            className="text-[11px] text-amber-400 hover:text-amber-300 underline tracking-wider"
          >
            Auto-fill Default Credentials
          </button>
        </div>

        <div className="mt-6 text-center">
          <button
            type="button"
            onClick={onBackToSite}
            className="text-xs text-gray-400 hover:text-amber-300 tracking-wider uppercase transition-colors"
          >
            &larr; Return to Guest Website
          </button>
        </div>
      </div>
    </div>
  );
}
