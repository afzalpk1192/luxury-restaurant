
import React from 'react';

export default function About() {
  return (
    <section id="about" className="py-16 sm:py-24 bg-gradient-to-b from-[#0b0f19] to-[#080b12] border-b border-gray-800/30 scroll-mt-20 sm:scroll-mt-24">
      <div className="max-w-7xl mx-auto px-4 sm:px-6">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">
          
          {/* Image Showcase */}
          <div className="relative group">
            <div className="aspect-[4/5] overflow-hidden border-2 border-amber-500/30 relative shadow-2xl">
              <img 
                src="https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?auto=format&fit=crop&q=80&w=1000" 
                alt="Luxury Dining Room" 
                className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent group-hover:from-black/20 transition-all duration-500"></div>
            </div>
            <div className="absolute -bottom-6 -right-6 hidden md:block w-64 h-64 border-4 border-amber-400 p-4 bg-[#0b0f19] shadow-2xl group-hover:-bottom-8 group-hover:-right-8 transition-all duration-500">
              <img 
                src="https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?auto=format&fit=crop&q=80&w=600" 
                alt="Fine Wine Selection" 
                className="w-full h-full object-cover"
              />
            </div>
          </div>

          {/* Text Content */}
          <div className="space-y-6 sm:space-y-8">
            <div>
              <p className="text-amber-400 text-xs uppercase tracking-[0.3em] font-semibold mb-3">Our Legacy</p>
              <h2 className="text-3xl sm:text-5xl md:text-6xl font-serif text-white leading-tight break-words">
                A Symphony of <span className="text-transparent bg-gradient-to-r from-amber-200 to-amber-400 bg-clip-text">Culinary Innovation</span>
              </h2>
            </div>
            <p className="text-gray-300 text-sm sm:text-base leading-relaxed font-light">
              Founded in 2012, L'ÉTOILE represents the pinnacle of modern gastronomy. Every plate served is a meticulous balance of French tradition, global flavors, and sustainable seasonal produce.
            </p>
            <p className="text-gray-400 text-sm sm:text-base leading-relaxed font-light">
              Our wine cellar houses over 1,200 rare vintages curated by world-champion sommelier staff to pair seamlessly with your tasting experience.
            </p>

            {/* Metrics Grid */}
            <div className="grid grid-cols-3 gap-2 sm:gap-6 pt-6 sm:pt-8 border-t-2 border-amber-500/30">
              <div className="min-w-0 overflow-hidden space-y-1 sm:space-y-2 p-2 sm:p-4 rounded-lg bg-gradient-to-br from-amber-500/10 to-transparent border border-amber-500/20 hover:border-amber-500/40 transition-all duration-300 text-center sm:text-left flex flex-col justify-center">
                <span className="block font-serif text-xl sm:text-3xl md:text-4xl text-amber-400 font-bold truncate">3</span>
                <span className="block text-[9px] sm:text-xs uppercase tracking-normal sm:tracking-widest text-gray-400 font-semibold leading-tight break-words">Michelin Stars</span>
              </div>
              <div className="min-w-0 overflow-hidden space-y-1 sm:space-y-2 p-2 sm:p-4 rounded-lg bg-gradient-to-br from-amber-500/10 to-transparent border border-amber-500/20 hover:border-amber-500/40 transition-all duration-300 text-center sm:text-left flex flex-col justify-center">
                <span className="block font-serif text-xl sm:text-3xl md:text-4xl text-amber-400 font-bold truncate">12+</span>
                <span className="block text-[9px] sm:text-xs uppercase tracking-normal sm:tracking-widest text-gray-400 font-semibold leading-tight break-words">Culinary Awards</span>
              </div>
              <div className="min-w-0 overflow-hidden space-y-1 sm:space-y-2 p-2 sm:p-4 rounded-lg bg-gradient-to-br from-amber-500/10 to-transparent border border-amber-500/20 hover:border-amber-500/40 transition-all duration-300 text-center sm:text-left flex flex-col justify-center">
                <span className="block font-serif text-xl sm:text-3xl md:text-4xl text-amber-400 font-bold truncate">1,200</span>
                <span className="block text-[9px] sm:text-xs uppercase tracking-normal sm:tracking-widest text-gray-400 font-semibold leading-tight break-words">Vintage Wines</span>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>
  );
}
