import React, { useState, useEffect } from 'react';

export default function Navbar({ cartCount, onOpenCart, onOpenAdmin, adminUser, onLogout }) {
  const [scrolled, setScrolled] = useState(false);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  useEffect(() => {
    const handleScroll = () => setScrolled(window.scrollY > 30);
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  return (
    <header className={`fixed top-0 left-0 w-full z-50 transition-all duration-300 ${scrolled ? 'bg-[#0b0f19]/95 backdrop-blur-xl py-3 border-b border-amber-500/40 shadow-2xl' : 'bg-[#0b0f19]/90 backdrop-blur-md py-4 border-b border-amber-500/20'}`}>
      <div className="max-w-7xl mx-auto px-3 sm:px-6 flex justify-between items-center">
          
          {/* Brand Logo */}
          <a href="#" className="text-base sm:text-2xl font-serif font-bold tracking-wider sm:tracking-widest text-amber-400 uppercase hover:text-amber-300 transition-colors duration-300 flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
            <span>L'ÉTOILE</span>
            <span className="hidden md:inline-block text-[10px] tracking-normal text-amber-200/60 font-sans border-l border-amber-500/30 pl-2">
              Fine Dining & Lounge
            </span>
          </a>

          {/* Desktop Navigation Links */}
          <nav className="hidden lg:flex space-x-8 text-xs uppercase tracking-widest text-gray-300 font-medium">
            <a href="#about" className="hover:text-amber-400 transition-colors duration-300 relative group py-1">
              About
              <span className="absolute bottom-0 left-0 w-0 h-0.5 bg-amber-400 group-hover:w-full transition-all duration-300"></span>
            </a>
            <a href="#menu" className="hover:text-amber-400 transition-colors duration-300 relative group py-1">
              Menu
              <span className="absolute bottom-0 left-0 w-0 h-0.5 bg-amber-400 group-hover:w-full transition-all duration-300"></span>
            </a>
            <a href="#chefs" className="hover:text-amber-400 transition-colors duration-300 relative group py-1">
              Culinary Team
              <span className="absolute bottom-0 left-0 w-0 h-0.5 bg-amber-400 group-hover:w-full transition-all duration-300"></span>
            </a>
            <a href="#reservation" className="hover:text-amber-400 transition-colors duration-300 relative group py-1">
              Reservations
              <span className="absolute bottom-0 left-0 w-0 h-0.5 bg-amber-400 group-hover:w-full transition-all duration-300"></span>
            </a>
          </nav>

          {/* Header Action Buttons */}
          <div className="flex items-center space-x-1.5 sm:space-x-3 flex-shrink-0">
            
            {/* Cart Icon Button */}
            <button 
              type="button"
              onClick={onOpenCart}
              className="relative p-1.5 sm:p-2 text-amber-400 hover:text-amber-300 transition-all duration-300 hover:scale-105 cursor-pointer flex-shrink-0"
              title="View Order Cart"
            >
              <svg className="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
              </svg>
              {cartCount > 0 && (
                <span className="absolute -top-1 -right-1 bg-gradient-to-br from-amber-400 to-amber-600 text-black text-[10px] sm:text-[11px] font-bold w-4 h-4 sm:w-5 sm:h-5 rounded-full flex items-center justify-center shadow-lg animate-pulse">
                  {cartCount}
                </span>
              )}
            </button>

            {/* Prominent High-Visibility Solid Gold Admin Login Button */}
            <button
              type="button"
              onClick={onOpenAdmin}
              className="flex items-center gap-1 sm:gap-2 bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600 hover:from-amber-300 hover:to-amber-500 text-black font-extrabold px-2.5 sm:px-4 py-1.5 sm:py-2 rounded shadow-lg shadow-amber-500/30 hover:shadow-amber-500/50 text-[10px] sm:text-xs uppercase tracking-wider sm:tracking-widest transition-all duration-300 hover:scale-105 cursor-pointer flex-shrink-0"
              title="Sign in as Restaurant Administrator"
            >
              <svg className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-black flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
              </svg>
              <span>
                <span className="hidden sm:inline">{adminUser ? 'Dashboard' : 'Admin Login'}</span>
                <span className="sm:hidden">{adminUser ? 'Admin' : 'Admin'}</span>
              </span>
            </button>

            {/* Book A Table Link */}
            <a 
              href="#reservation" 
              className="hidden md:inline-block border-2 border-amber-400/80 text-amber-400 hover:bg-amber-400 hover:text-black px-4 py-1.5 text-xs uppercase tracking-widest font-semibold transition-all duration-300 rounded"
            >
              Book Table
            </a>

            {/* Mobile Menu Toggle Button */}
            <button
              type="button"
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
              className="lg:hidden text-amber-400 hover:text-amber-300 p-1.5 focus:outline-none cursor-pointer flex-shrink-0"
              aria-label="Toggle Navigation Menu"
            >
              <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {mobileMenuOpen ? (
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                ) : (
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16M4 18h16" />
                )}
              </svg>
            </button>

          </div>
        </div>

      {/* Mobile Drawer Menu */}
      {mobileMenuOpen && (
        <div className="lg:hidden bg-[#0b0f19]/98 border-b border-amber-500/40 px-5 py-5 space-y-3 animate-fade-in backdrop-blur-2xl shadow-2xl">
          <a 
            href="#about" 
            onClick={() => setMobileMenuOpen(false)} 
            className="block text-xs uppercase tracking-widest text-gray-300 hover:text-amber-400 py-2 border-b border-gray-800/40"
          >
            About Us
          </a>
          <a 
            href="#menu" 
            onClick={() => setMobileMenuOpen(false)} 
            className="block text-xs uppercase tracking-widest text-amber-400 hover:text-amber-300 font-semibold py-2 border-b border-gray-800/40"
          >
            À La Carte Menu
          </a>
          <a 
            href="#chefs" 
            onClick={() => setMobileMenuOpen(false)} 
            className="block text-xs uppercase tracking-widest text-gray-300 hover:text-amber-400 py-2 border-b border-gray-800/40"
          >
            Culinary Masters
          </a>
          <a 
            href="#reservation" 
            onClick={() => setMobileMenuOpen(false)} 
            className="block text-xs uppercase tracking-widest text-gray-300 hover:text-amber-400 py-2 border-b border-gray-800/40"
          >
            Book Table & Pre-Order
          </a>
          <div className="pt-2 flex flex-col gap-2">
            <a 
              href="#reservation"
              onClick={() => setMobileMenuOpen(false)}
              className="w-full text-center bg-gradient-to-r from-amber-500 to-amber-600 text-black py-2.5 rounded font-bold text-xs uppercase tracking-wider"
            >
              Book Table
            </a>
          </div>
        </div>
      )}
    </header>
  );
}