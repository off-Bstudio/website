(function(){
  function getLangFromURL(){
    var p = new URLSearchParams(window.location.search).get('lang');
    return (p === 'fr') ? 'fr' : 'en';
  }

  function updateNavHrefs(lang){
    document.querySelectorAll('[data-nav]').forEach(function(a){
      a.setAttribute('href', a.getAttribute('data-nav') + '?lang=' + lang);
    });
  }

  function setLang(lang, pushState){
    document.documentElement.lang = lang;
    document.querySelectorAll('[data-en]').forEach(function(el){
      el.textContent = el.getAttribute('data-' + lang);
    });
    var btnEn = document.getElementById('btn-en');
    var btnFr = document.getElementById('btn-fr');
    if(btnEn) btnEn.classList.toggle('active', lang === 'en');
    if(btnFr) btnFr.classList.toggle('active', lang === 'fr');
    updateNavHrefs(lang);

    if(pushState){
      var url = new URL(window.location.href);
      url.searchParams.set('lang', lang);
      window.history.replaceState({}, '', url);
    }
  }
  window.setLang = function(lang){ setLang(lang, true); };

  document.addEventListener('DOMContentLoaded', function(){
    setLang(getLangFromURL(), false);

    // Mark current nav link
    var here = window.location.pathname.split('/').pop() || 'index.php';
    document.querySelectorAll('.nav-links a[data-nav]').forEach(function(a){
      if(a.getAttribute('data-nav') === here){ a.classList.add('current'); }
    });

    // Mobile menu
    var burger = document.getElementById('burger');
    var navLinks = document.getElementById('navLinks');
    if(burger && navLinks){
      burger.addEventListener('click', function(){ navLinks.classList.toggle('open'); });
      navLinks.querySelectorAll('a').forEach(function(a){
        a.addEventListener('click', function(){ navLinks.classList.remove('open'); });
      });
    }

    // Scroll reveal
    var revealEls = document.querySelectorAll('.reveal');
    if('IntersectionObserver' in window){
      var io = new IntersectionObserver(function(entries){
        entries.forEach(function(entry){
          if(entry.isIntersecting){
            entry.target.classList.add('in');
            io.unobserve(entry.target);
          }
        });
      }, { threshold: 0.15 });
      revealEls.forEach(function(el){ io.observe(el); });
    } else {
      revealEls.forEach(function(el){ el.classList.add('in'); });
    }
  });
})();
