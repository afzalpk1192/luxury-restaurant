import React from 'react';

export default function Hero() {
  return (
    <section className="relative min-h-screen flex items-center justify-center bg-cover bg-center overflow-hidden pt-24 pb-16" style={{ backgroundImage: `linear-gradient(135deg, rgba(11, 15, 25, 0.6) 0%, rgba(11, 15, 25, 0.85) 50%, rgba(11, 15, 25, 0.98) 100%), url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&q=80&w=1920')` }}>
      <div className="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-[#0b0f19]"></div>
      <div className="text-center max-w-4xl px-4 z-10 animate-fade-in mt-8">
        <p className="text-amber-400 uppercase tracking-[0.3em] text-xs sm:text-sm mb-4 font-semibold">An Unrivaled Gastronomic Journey</p>
        <h1 className="text-4xl sm:text-6xl md:text-7xl font-serif text-white font-extralight tracking-wide leading-tight mb-6">
          Elegance Served <br /><span className="italic font-normal text-amber-200 bg-gradient-to-r from-amber-200 to-amber-400 bg-clip-text text-transparent">On Every Plate</span>
        </h1>
        <p className="text-gray-300 text-base sm:text-lg md:text-xl mb-8 font-light max-w-2xl mx-auto leading-relaxed">
          Experience world-class cuisine crafted by award-winning chefs in an atmosphere of refined sophistication.
        </p>

        {/* Primary Action Buttons */}
        <div className="flex flex-col sm:flex-row justify-center items-center gap-4 mt-8">
          <a 
            href="#menu" 
            className="w-full sm:w-auto text-center bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-black px-8 py-4 text-xs font-bold uppercase tracking-widest hover:shadow-2xl hover:shadow-amber-500/50 transition-all duration-300 rounded"
          >
            Explore Menu & Order
          </a>
          <a 
            href="#reservation" 
            className="w-full sm:w-auto text-center border-2 border-white/60 hover:border-amber-400 text-white hover:text-amber-300 px-8 py-4 text-xs font-bold uppercase tracking-widest hover:shadow-lg hover:shadow-amber-500/20 backdrop-blur-sm transition-all duration-300 rounded"
          >
            Reserve Table
          </a>
        </div>
      </div>
    </section>
  );
}
