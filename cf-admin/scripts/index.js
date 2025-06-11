
const popupContainer = document.querySelector('.popup-container');
const popup = document.querySelector('.popup');

function openpopup() {
    popup.style.display = "flex";
    popupContainer.style.display = 'flex';
}

function closepopup() {
    const isVisible = popup.style.display === 'flex';
    if (isVisible) {
        popupContainer.style.display = 'none';
        popup.style.display = 'none';
    } else {
        popup.style.display = 'flex';
        popupContainer.style.display = 'flex';

    }
}

document.querySelectorAll('a[href="#"]').forEach((link) => {
    link.addEventListener('click', (event) => {
        event.preventDefault();
        openpopup();
    });
});