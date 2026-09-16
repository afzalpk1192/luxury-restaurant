
import React from 'react';

const reviewsData = [
  {
    quote: "An extraordinary sensory trip. The Truffle Wagyu and wine pairing were second to none in the entire city.",
    author: "Victoria Sterling",
    role: "Michelin Guide Reviewer"
  },
  {
    quote: "L'ÉTOILE sets the gold standard for hospitality and fine dining. The smoked old fashioned alone is worth the visit.",
    author: "Julian Thorne",
    role: "Gastronomy Monthly"
  },
  {
    quote: "Flawless execution from the moment you enter. The ambience, service, and gold chocolate dessert are perfection.",
    author: "Sophia Al-Mansoor",
    role: "Food & Wine Critic"
  }
];

export default function Reviews() {
  return (
    <section className="py-16 sm:py-24 bg-gradient-to-b from-[#0b0f19] via-[#080b12] to-[#0b0f19] border-t border-b border-amber-500/20">
      <div className="max-w-7xl mx-auto px-4 sm:px-6">
        
        <div className="text-center mb-10 sm:mb-16">
          <p className="text-amber-400 text-xs uppercase tracking-[0.25em] mb-3 font-semibold">Guest Experiences</p>
          <h2 className="text-3xl sm:text-4xl md:text-5xl font-serif text-white mb-3 break-words">Words From Our Critics</h2>
          <p className="text-gray-400 text-xs sm:text-sm max-w-2xl mx-auto px-2">Accolades from industry leaders and esteemed food critics worldwide</p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
          {reviewsData.map((review, idx) => (
            <div key={idx} className="group bg-gradient-to-br from-[#111625] to-[#0b0f19] p-6 sm:p-10 rounded-lg border-2 border-gray-800/60 flex flex-col justify-between hover:border-amber-500/50 hover:shadow-2xl hover:shadow-amber-500/20 transition-all duration-500">
              <div className="space-y-6">
                <div className="text-5xl text-amber-400/30 font-serif leading-none">"</div>
                <p className="text-gray-200 text-base leading-relaxed italic">
                  {review.quote}
                </p>
              </div>
              <div className="pt-8 mt-8 border-t-2 border-amber-500/30 group-hover:border-amber-500/60 transition-all duration-300">
                <h4 className="text-white font-serif text-lg group-hover:text-amber-300 transition-colors duration-300">{review.author}</h4>
                <p className="text-amber-400/70 text-xs uppercase tracking-widest mt-2 font-semibold">{review.role}</p>
              </div>
            </div>
          ))}
        </div>

      </div>
    </section>
  );
}
