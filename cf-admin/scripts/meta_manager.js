let jsonData = {};

async function fetchData() {
    const response = await fetch('./resources/json/data.json');
    jsonData = await response.json();
    populateDropdown();
    populateData();
}

function populateDropdown() {
    const pageSelector = document.getElementById('pageSelector');
    pageSelector.innerHTML = '';
    for (const key in jsonData) {
        const option = document.createElement('option');
        option.value = key;
        option.textContent = key.charAt(0).toUpperCase() + key.slice(1);
        pageSelector.appendChild(option);
    }
}

function populateData() {
    const selectedPage = document.getElementById('pageSelector').value;
    const pageData = jsonData[selectedPage];
    const metaTagContainer = document.getElementById('metaTagContainer');
    const linkTagContainer = document.getElementById('linkTagContainer');
    metaTagContainer.innerHTML = '';
    linkTagContainer.innerHTML = '';

    if (pageData.meta) {
        pageData.meta.forEach((metaItem, index) => {
            const metaDiv = document.createElement('div');
            for (const key in metaItem) {
                const value = metaItem[key];
                metaDiv.innerHTML += `<label>${key}</label>
                                      <input type="text" value="${value}" id="${selectedPage}_meta_${index}_${key}">`;
            }
            metaTagContainer.appendChild(metaDiv);
        });
    }

    if (pageData.links) {
        pageData.links.forEach((linkItem, index) => {
            const linksDiv = document.createElement('div');
            for (const key in linkItem) {
                const value = linkItem[key];
                linksDiv.innerHTML += `<label>${key}</label>
                                       <input type="text" value="${value}" id="${selectedPage}_links_${index}_${key}">`;
            }
            linkTagContainer.appendChild(linksDiv);
        });
    }
}

function saveData() {
    const selectedPage = document.getElementById('pageSelector').value;
    const pageData = jsonData[selectedPage];
    
    pageData.meta.forEach((metaItem, index) => {
        for (const key in metaItem) {
            const inputElem = document.getElementById(`${selectedPage}_meta_${index}_${key}`);
            metaItem[key] = inputElem.value;
        }
    });

    pageData.links.forEach((linkItem, index) => {
        for (const key in linkItem) {
            const inputElem = document.getElementById(`${selectedPage}_links_${index}_${key}`);
            linkItem[key] = inputElem.value;
        }
    });

    fetch('./scripts/tmsave.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(jsonData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Success:', data);
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}

window.onload = fetchData;
