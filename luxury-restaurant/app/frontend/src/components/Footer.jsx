import React from 'react';

export default function Footer() {
  return (
    <footer className="bg-gradient-to-b from-[#05070d] via-[#0b0f19] to-[#05070d] text-gray-400 border-t-2 border-amber-500/20 pt-16 sm:pt-20 pb-12">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 grid grid-cols-1 md:grid-cols-4 gap-8 sm:gap-12 mb-12 sm:mb-16">
        
        {/* Brand */}
        <div className="space-y-6">
          <h3 className="text-2xl font-serif text-amber-400 tracking-widest uppercase hover:text-amber-300 transition-colors duration-300">L'ÉTOILE</h3>
          <p className="text-sm text-gray-500 leading-relaxed">
            An extraordinary fine dining experience bringing world-class gastronomy and timeless luxury together.
          </p>
          <div className="flex gap-4 pt-4">
            <a href="#" className="w-10 h-10 rounded-full bg-amber-500/10 border border-amber-500/30 flex items-center justify-center hover:bg-amber-500/20 hover:border-amber-500/50 transition-all duration-300 text-amber-400 hover:text-amber-300">
              <span className="text-sm">f</span>
            </a>
            <a href="#" className="w-10 h-10 rounded-full bg-amber-500/10 border border-amber-500/30 flex items-center justify-center hover:bg-amber-500/20 hover:border-amber-500/50 transition-all duration-300 text-amber-400 hover:text-amber-300">
              <span className="text-sm">𝕏</span>
            </a>
            <a href="#" className="w-10 h-10 rounded-full bg-amber-500/10 border border-amber-500/30 flex items-center justify-center hover:bg-amber-500/20 hover:border-amber-500/50 transition-all duration-300 text-amber-400 hover:text-amber-300">
              <span className="text-sm">in</span>
            </a>
          </div>
        </div>

        {/* Location & Contact */}
        <div className="space-y-5">
          <h4 className="text-sm uppercase tracking-widest text-white font-semibold border-b-2 border-amber-500/30 pb-3">Location & Contact</h4>
          <div className="space-y-4 text-sm">
            <div>
              <p className="text-gray-400 hover:text-amber-400 transition-colors cursor-pointer">742 Fifth Avenue</p>
              <p className="text-gray-400">Manhattan, NY 10019</p>
            </div>
            <div>
              <p className="text-amber-400 font-semibold hover:text-amber-300 transition-colors cursor-pointer">+1 (212) 555-0199</p>
              <p className="text-gray-400 hover:text-amber-400 transition-colors cursor-pointer">reservations@letoile-dining.com</p>
            </div>
          </div>
        </div>

        {/* Hours */}
        <div className="space-y-5">
          <h4 className="text-sm uppercase tracking-widest text-white font-semibold border-b-2 border-amber-500/30 pb-3">Opening Hours</h4>
          <div className="space-y-3 text-sm">
            <div>
              <p className="text-white font-semibold">Tue - Thu</p>
              <p className="text-gray-400">5:00 PM – 11:00 PM</p>
            </div>
            <div>
              <p className="text-white font-semibold">Fri - Sun</p>
              <p className="text-gray-400">4:30 PM – 12:00 AM</p>
            </div>
            <div>
              <p className="text-amber-400/80 font-semibold">Monday</p>
              <p className="text-gray-500">Private Events Only</p>
            </div>
          </div>
        </div>

        {/* Newsletter */}
        <div className="space-y-5">
          <h4 className="text-sm uppercase tracking-widest text-white font-semibold border-b-2 border-amber-500/30 pb-3">Private Invitations</h4>
          <p className="text-sm text-gray-500">Subscribe for seasonal tasting menu announcements and exclusive events.</p>
          <form onSubmit={e => e.preventDefault()} className="flex gap-2">
            <input 
              type="email" 
              placeholder="Your Email" 
              className="bg-[#0b0f19] border-2 border-gray-700 hover:border-amber-500/50 focus:border-amber-400 text-xs text-white p-3 outline-none w-full transition-all duration-300 rounded"
            />
            <button className="bg-gradient-to-r from-amber-500 to-amber-600 text-black px-4 text-xs uppercase font-bold hover:shadow-lg hover:shadow-amber-500/50 transition-all duration-300 rounded">
              Join
            </button>
          </form>
        </div>

      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 border-t-2 border-gray-900/50 pt-8 flex flex-col sm:flex-row justify-between items-center gap-4 sm:gap-6 text-xs text-gray-600 text-center sm:text-left">
        <p>&copy; {new Date().getFullYear()} L'ÉTOILE Luxury Fine Dining. All Rights Reserved.</p>
        <div className="flex gap-4 sm:gap-6 flex-wrap justify-center items-center">
          <a href="#" className="hover:text-amber-400 transition-colors duration-300 font-medium">Privacy Policy</a>
          <a href="#" className="hover:text-amber-400 transition-colors duration-300 font-medium">Dress Code & Policies</a>
          <a href="#" className="hover:text-amber-400 transition-colors duration-300 font-medium">Terms of Service</a>
        </div>
      </div>
    </footer>
  );
}
