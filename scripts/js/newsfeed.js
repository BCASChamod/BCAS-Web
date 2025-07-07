window.addEventListener('DOMContentLoaded', () => {
    
    console.log(window.eventData);
    const allData = window.eventData;
    const newsData = allData.filter(item => item.type === 'news');
    const eventData = allData.filter(item => item.type === 'event');

    const newsContainer = document.getElementById('newsContainer');
    newsContainer.innerHTML = "";

    newsData.forEach(news => {
        const banner = document.createElement('div');
        banner.className = 'news-banner';

        const textDiv = document.createElement('div');
        textDiv.className = 'text-content';

        const h1 = document.createElement('h1');
        h1.textContent = news.title;

        const p = document.createElement('p');
        p.textContent = news.overview;

        textDiv.appendChild(h1);
        textDiv.appendChild(p);

        const imgContainer = document.createElement('div');
        imgContainer.className = 'img-container';

        const img = document.createElement('img');
        img.id = news.coverimg;
        img.src = "";
        img.alt = "";

        imgContainer.appendChild(img);

        banner.appendChild(textDiv);
        banner.appendChild(imgContainer);

        newsContainer.appendChild(banner);
    });

    const nextBtn = document.getElementById('newsBtnNext');
    const prevBtn = document.getElementById('newsBtnPrevious');
    const scrollAmount = 300; // how much to scroll per click

    const updateButtonVisibility = () => {
        const maxScrollLeft = newsContainer.scrollWidth - newsContainer.clientWidth;

        prevBtn.style.display = newsContainer.scrollLeft > 0 ? 'inline-block' : 'none';
        nextBtn.style.display = newsContainer.scrollLeft < maxScrollLeft ? 'inline-block' : 'none';
    };

    nextBtn.addEventListener('click', () => {
        newsContainer.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    });

    prevBtn.addEventListener('click', () => {
        newsContainer.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    });

    // Update button visibility on scroll
    newsContainer.addEventListener('scroll', updateButtonVisibility);

    // Initial update when page loads
    updateButtonVisibility();

});