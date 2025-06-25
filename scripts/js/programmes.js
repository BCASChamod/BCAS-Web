window.addEventListener('DOMContentLoaded', function() {

    programmeContainer = document.getElementById('programContainer');
    
    function populatePrograms() {
        const data = window.productAllData || [];
        programmeContainer.innerHTML = '';
        data.forEach(program => {
            const div = document.createElement('div');
            div.className = 'program-item';
            div.innerHTML = `
                <h3>${program.name}</h3>
                <img id="${program.image_id}" src="" />

                
            `;
            programmeContainer.appendChild(div);
        });
    }
    populatePrograms();
});