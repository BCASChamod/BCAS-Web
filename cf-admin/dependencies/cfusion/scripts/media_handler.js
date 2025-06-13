
// Export the functions so they can be imported in other scripts
export function loadMedia() {
    fetch('http://localhost/bcas-web/cf-admin/server/scripts/php/mediahandler.php')
        .then(response => response.json())
        .then(mediaData => {
            const mediaElements = document.querySelectorAll('img, video');

            mediaElements.forEach(element => {
                const mediaItem = mediaData[element.id];
                if (mediaItem) {
                    if (mediaItem.is_active) {
                        element.src = mediaItem.src;
                        element.alt = mediaItem.alt;
                        if (mediaItem.srcset) {
                            element.srcset = mediaItem.srcset;
                        }
                    } else {
                        console.log(`Element ID ${element.id} is not loaded with an image cause its disabled! Re-enable it or change it.`);
                        element.id = 'placeholder_img';
                        element.src = 'http://localhost/careercompass/resources/img/placeholder.webp';
                        element.alt = 'Placeholder: Image is disabled!';
                        if (element.srcset) {
                            element.srcset = '';
                        }
                    }
                }
            });

            checkAllMediaElements();
        })
        .catch(error => {
            console.error("Error fetching media data:", error);
        });
}

export function checkAllMediaElements() {
    console.log("Media checking...");
    // Select all img and video elements that do not have the data-lazy attribute set to true
    const mediaElements = document.querySelectorAll('img:not([data-lazy="true"]), video:not([data-lazy="true"])');

    let allLoaded = true;

    // Iterate over each media element
    mediaElements.forEach(element => {
        // Check if the element does not have the data-lazy attribute and its src is not set
        if (!element.hasAttribute('data-lazy') && !element.src) {
            allLoaded = false;
            loadMedia();  // Re-load media if any element is missing src
            console.log("Media Element is not loaded!");
        }
    });

    // If not all media elements are loaded, check again after 1 second
    if (!allLoaded) {
        setTimeout(checkAllMediaElements, 1000);  // Check again after 1 second
    }
}