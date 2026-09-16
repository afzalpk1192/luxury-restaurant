
import React from 'react';
import { chefs } from '../data/restaurantData';

export default function ChefSection() {
  return (
    <section id="chefs" className="py-16 sm:py-24 bg-gradient-to-b from-[#080b12] to-[#0b0f19] scroll-mt-20 sm:scroll-mt-24">
      <div className="max-w-7xl mx-auto px-4 sm:px-6">
        
        <div className="text-center mb-10 sm:mb-16">
          <p className="text-amber-400 text-xs uppercase tracking-[0.25em] mb-3 font-semibold">Master Culinary Artists</p>
          <h2 className="text-3xl sm:text-4xl md:text-5xl font-serif text-white mb-3 break-words">Meet Our Culinary Masters</h2>
          <p className="text-gray-400 text-xs sm:text-sm max-w-2xl mx-auto px-2">World-renowned chefs bringing decades of expertise and passion to every dish</p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-8 sm:gap-12 max-w-5xl mx-auto">
          {chefs.map((chef, idx) => (
            <div key={idx} className="group bg-gradient-to-br from-[#111625] to-[#0b0f19] border-2 border-gray-800/60 p-5 sm:p-8 rounded-lg flex flex-col sm:flex-row gap-6 sm:gap-8 items-start sm:items-center hover:border-amber-500/50 hover:shadow-2xl hover:shadow-amber-500/20 transition-all duration-500">
              <div className="w-full sm:w-40 h-48 sm:h-40 flex-shrink-0 overflow-hidden border-2 border-amber-500/30 rounded-lg group-hover:border-amber-400 transition-all duration-300">
                <img 
                  src={chef.image} 
                  alt={chef.name} 
                  className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                />
              </div>
              <div className="space-y-3 sm:space-y-4 flex-1">
                <div className="flex flex-wrap gap-2 sm:gap-3">
                  <span className="text-amber-400 text-[10px] sm:text-[11px] uppercase tracking-wider sm:tracking-widest border border-amber-500/40 px-2.5 py-1 sm:px-3 sm:py-1.5 rounded bg-amber-500/5 font-semibold">
                    {chef.experience}
                  </span>
                </div>
                <h3 className="text-2xl sm:text-3xl font-serif text-white group-hover:text-amber-300 transition-colors duration-300 break-words">{chef.name}</h3>
                <p className="text-amber-300/80 text-xs uppercase tracking-wider font-semibold">{chef.role}</p>
                <p className="text-gray-400 text-xs sm:text-sm leading-relaxed italic border-l-2 sm:border-l-4 border-amber-500/30 pl-3 sm:pl-4">
                  "Cooking is an art form driven by passion, perfection, and reverence for ingredients."
                </p>
              </div>
            </div>
          ))}
        </div>

      </div>
    </section>
  );
}