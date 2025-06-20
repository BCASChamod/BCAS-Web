export function injectDependencies(...deps) {
  const assetMap = {
    fontawesome: {
      css: [
        'http://localhost/bcas-web/cf-admin/dependencies/FontAwesome/css/all.min.css',
      ],
      js: [
        'http://localhost/bcas-web/cf-admin/dependencies/FontAwesome/js/all.min.js',
      ],
    },
    cfusion: {
      css: [
        'http://localhost/bcas-web/cf-admin/dependencies/cfusion/cfusion.css',
      ],
      js: [],
    },
    gsap: {
      css: [],
      js: [
        'http://localhost/bcas-web/cf-admin/dependencies/gsap/gsap.min.js',
        'http://localhost/bcas-web/cf-admin/dependencies/gsap/ScrollTrigger.min.js',
      ],
    },
    bootstrapGrid: {
      css: [
        'http://localhost/bcas-web/cf-admin/dependencies/bootstrap/bootstrap-grid.css',
      ],
      js: [
        ''
      ],
    }
  };

  window.addEventListener('DOMContentLoaded', () => {
    console.log('Injecting dependencies:', deps);
    if (!deps || deps.length === 0) {
      console.warn('No dependencies provided to inject.');
      return;
    }
    deps.forEach(dep => {
      const assets = assetMap[dep];
      if (!assets) return;
      (assets.css || []).forEach(href => {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        document.head.appendChild(link);
      });
      (assets.js || []).forEach(src => {
        const script = document.createElement('script');
        script.src = src;
        script.defer = true;
        document.body.appendChild(script);
      });
    });
  });
}
