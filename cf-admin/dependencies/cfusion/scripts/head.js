const loadHeadData = (pageKey) => {
  fetch('http://localhost/careercompass//cf_admin/resources/json/data.json')
    .then(response => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then(headData => {
      const data = headData[pageKey];

      if (!data) {
        console.error(`No head data found for page key: ${pageKey}`);
        return;
      }

      if (data.title) {
        document.title = data.title;
      }

      data.meta?.forEach(meta => {
        const metaElement = document.createElement('meta');
        Object.entries(meta).forEach(([key, value]) => {
          metaElement.setAttribute(key, value);
        });
        document.head.appendChild(metaElement);
      });

      data.link?.forEach(link => {
        const linkElement = document.createElement('link');
        Object.entries(link).forEach(([key, value]) => {
          linkElement.setAttribute(key, value);
        });
        document.head.appendChild(linkElement);
      });

      data.script?.forEach(script => {
        const scriptElement = document.createElement('script');
        if (script.type === "application/ld+json") {
          scriptElement.type = "application/ld+json";
          scriptElement.textContent = JSON.stringify(script.content);
        } else {
          Object.entries(script).forEach(([key, value]) => {
            if (key !== "content") {
              scriptElement.setAttribute(key, value);
            }
          });
          if (script.content) {
            scriptElement.textContent = script.content;
          }
        }
        document.head.appendChild(scriptElement);
      });
    });
};

