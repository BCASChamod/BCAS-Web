window.addEventListener('DOMContentLoaded', function () {

// '?activetab=postgraduate&school=it&duration=12&studymode=partime&branch=col'

    const programmeData = window.productAllData || [];
    const totalPrograms = programmeData.length;

    const urlParams = new URLSearchParams(window.location.search);

    // Default to Undergraduate Tab if not set
    if (!urlParams.has('activetab')) {
        urlParams.set('activetab', 'undergraduate');
    }
    const activeTab = urlParams.get('activetab');

    // Set Default Branch
    let branch = urlParams.get('branch');
    if (!branch) {
        const branchData = JSON.parse(sessionStorage.getItem('activeBranch') || 'null');
        branch = branchData?.id || null;
        if (branch) urlParams.set('branch', branch);
        window.history.replaceState({}, '', window.location.pathname + '?' + urlParams.toString());
    }

    // Listen for URL Changes
    function onUrlChange(callback) {
        ['pushState', 'replaceState'].forEach(type => {
            const orig = history[type];
            history[type] = function () {
                const rv = orig.apply(this, arguments);
                window.dispatchEvent(new Event('urlchange'));
                return rv;
            };
        });
        window.addEventListener('popstate', () => window.dispatchEvent(new Event('urlchange')));
        window.addEventListener('hashchange', () => window.dispatchEvent(new Event('urlchange')));
        window.addEventListener('urlchange', callback);
    }

    onUrlChange(filterProgrammes);


// #region Filter: Tab Switching

    if (activeTab) {
        document.querySelectorAll('[data-tab]').forEach(tab => {
            tab.classList.toggle('active', tab.getAttribute('data-tab') === activeTab);
        });
    }

    document.querySelectorAll('[data-tab]').forEach(tab => {
        tab.addEventListener('click', (e) => {
            e.preventDefault();
            document.querySelectorAll('[data-tab]').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            urlParams.set('activetab', tab.getAttribute('data-tab'));
            window.history.replaceState({}, '', window.location.pathname + '?' + urlParams.toString());
        });
    });

// #endregion

// #region Filtering

    function populateFilters() {
        const fieldList = document.getElementById('fieldList');
        categoriesData = window.categoriesAllData;

        fieldList.innerHTML = '<li data-filter="all">All Programmes</li>';

        if (Array.isArray(categoriesData)) {
            categoriesData.forEach(category => {
                const li = document.createElement('li');
                li.textContent = category.name;
                li.setAttribute('data-filter', category.id);
                fieldList.appendChild(li);
            });
        }
        
        const fieldItem = document.querySelectorAll('.filter-list li[data-filter]');
        fieldItem.addEventListener('click', function() {
            
        });

    }

    populateFilters();

    function filterProgrammes() {

        const queries = {
            activetab: urlParams.get('activetab'),
            school: urlParams.get('school'),
            duration: urlParams.get('duration'),
            studymode: urlParams.get('studymode'),
            branch: urlParams.get('branch')
        };



        let filtered = programmeData.filter(program => {
            if (queries.activetab && program.program_type) {
            if (program.program_type.toLowerCase() !== queries.activetab.toLowerCase()) return false;
            }
            // Filter by school (category_id or category_name)
            // if (queries.school && program.category_name) {
            // if (program.category_name.toLowerCase().indexOf(queries.school.toLowerCase()) === -1) return false;
            // }
            // Filter by duration (exact match or contains)
            if (queries.duration && program.duration) {
                if (String(program.duration) !== String(queries.duration)) return false;
            }
            // Filter by studymode (study_mode)
            if (queries.studymode && program.study_mode) {
            if (program.study_mode.toLowerCase().indexOf(queries.studymode.toLowerCase()) === -1) return false;
            }
            // Filter by branch (branch_id)
            if (branch === 'any') {
                // No Branch Filterations
            } else {
            if (queries.branch && program.branch_id) {
            if (String(program.branch_id) !== String(queries.branch)) return false;
            }
            }
            return true;
        });

        populateProgrammes(filtered);
        console.log('Filtered Data:', filtered);
    }

// #endregion

    function populateProgrammes(data) {
        const programmeContainer = document.getElementById('programsGrid');
        let maxitem = programmeContainer.getAttribute('data-maxitem') || 12;
        
        window.filteredPrograms = data;
        window.currentIndex = 0;
        window.itemsPerLoad = 10;

        programmeContainer.innerHTML = '';

        loadMoreProgrammes();
        setupInfiniteScroll();
    }

    function loadMoreProgrammes() {
        const programmeContainer = document.getElementById('programsGrid');
        const startIndex = window.currentIndex;
        const endIndex = Math.min(startIndex + window.itemsPerLoad, window.filteredPrograms.length);
        
        for (let i = startIndex; i < endIndex; i++) {
            const program = window.filteredPrograms[i];
            
            let branchNames = [];
            if (program.branch_id) {
                const branchMap = {
                    col: 'Colombo',
                    kan: 'Kandy',
                    kal: 'Kalmunei',
                    jaf: 'Jaffna'
                };
                if (typeof program.branch_id === 'string') {
                    branchNames = program.branch_id
                        .split(',')
                        .map(id => branchMap[id.trim()])
                        .filter(Boolean)
                        .join(', ');
                }
            }
            
            const programCard = document.createElement('div');
            programCard.className = 'program-card';
            programCard.innerHTML = 
            `
                <img id="${program.image_id}" src="" alt="Program Image" />
                <div class="icon info" data-tooltipper="branch">
                    <svg aria-hidden="true" focusable="false" data-prefix="fa-solid" data-icon="circle-info" class="svg-inline--fa fa-circle-info fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M256 16C123.449 16 16 123.508 16 256C16 388.578 123.449 496 256 496S496 388.578 496 256C496 123.508 388.551 16 256 16ZM256 128C273.674 128 288 142.326 288 160C288 177.672 273.674 192 256 192S224 177.672 224 160C224 142.326 238.326 128 256 128ZM296 384H216C202.75 384 192 373.25 192 360S202.75 336 216 336H232V272H224C210.75 272 200 261.25 200 248S210.75 224 224 224H256C269.25 224 280 234.75 280 248V336H296C309.25 336 320 346.75 320 360S309.25 384 296 384Z" fill="currentColor"/></svg>
                </div>

                <a href="${program.affiliation_link}" target="_blank" rel="noopener noreferrer" ><div class="icon logo">
                    <img id="${program.affiliation_logo_img}" src="" alt="Pearson" />
                </div></a>
                
                <div class="tooltip" id="infoTooltip">Available at: ${branchNames}</div>

                <div class="content">
                    <h3>${program.name}</h3>
                    <p>${program.course_overview}</p>
                </div>
            `;

            programmeContainer.appendChild(programCard);
        }
        
        window.currentIndex = endIndex;
    }

    function setupInfiniteScroll() {
        const programmeContainer = document.getElementById('programsGrid');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    if (window.currentIndex < window.filteredPrograms.length) {
                        loadMoreProgrammes();
                        observer.disconnect();
                        setupIntersectionObserver();
                    }
                }
            });
        }, {
            rootMargin: '100px'
        });
        
        try {
            setupIntersectionObserver();
        } catch (err) {
            console.error('IntersectionObserver failed, falling back to scroll:', err);
            setupInfiniteScrollAlternative();
        }
        
        function setupIntersectionObserver() {
            const cards = programmeContainer.querySelectorAll('.program-card');
            if (cards.length > 0) {
                const lastCard = cards[cards.length - 1];
                observer.observe(lastCard);
            }
        }
    }

    function setupInfiniteScrollAlternative() {
        let isLoading = false;
        
        window.addEventListener('scroll', () => {
            if (isLoading) return;
            
            const programmeContainer = document.getElementById('programsGrid');
            const cards = programmeContainer.querySelectorAll('.program-card');
            
            if (cards.length === 0) return;
            
            const lastCard = cards[cards.length - 1];
            const lastCardRect = lastCard.getBoundingClientRect();
            const windowHeight = window.innerHeight;
            
            if (lastCardRect.top <= windowHeight + 100) {
                if (window.currentIndex < window.filteredPrograms.length) {
                    isLoading = true;
                    loadMoreProgrammes();
                    
                    setTimeout(() => {
                        isLoading = false;
                    }, 100);
                }
            }
        });
    }

    filterProgrammes();



});






        // Mobile sidebar toggle
// const mobileToggle = document.getElementById('mobileToggle');
// const sidebar = document.getElementById('sidebar');
// const overlay = document.getElementById('sidebarOverlay');

// function toggleSidebar() {
//     sidebar.classList.toggle('open');
//     overlay.classList.toggle('show');
    
//     // Change icon
//     const icon = mobileToggle.querySelector('i');
//     if (sidebar.classList.contains('open')) {
//         icon.className = 'bi bi-x-lg';
//     } else {
//         icon.className = 'bi bi-funnel';
//     }
// }

// mobileToggle.addEventListener('click', toggleSidebar);
// overlay.addEventListener('click', toggleSidebar);



        // Search functionality
        // const searchInput = document.getElementById('searchInput');
        // searchInput.addEventListener('input', (e) => {
        //     const searchTerm = e.target.value.toLowerCase();
        //     const cards = document.querySelectorAll('.program-card');
            
        //     cards.forEach(card => {
        //         const title = card.querySelector('h5').textContent.toLowerCase();
        //         const description = card.querySelector('p').textContent.toLowerCase();
                
        //         if (title.includes(searchTerm) || description.includes(searchTerm)) {
        //             card.style.display = 'flex';
        //         } else {
        //             card.style.display = 'none';
        //         }
        //     });
        // });

        // // Filter functionality
        // document.querySelectorAll('[data-filter]').forEach(filter => {
        //     filter.addEventListener('click', () => {
        //         // Remove active state from siblings
        //         filter.parentNode.querySelectorAll('li').forEach(li => li.style.color = '');
                
        //         // Add active state
        //         filter.style.color = '#3498db';
                
        //         console.log('Filter selected:', filter.getAttribute('data-filter'));
        //         // Implement filtering logic here
        //     });
        // });

        // // Close sidebar when clicking outside on mobile
        // document.addEventListener('click', (e) => {
        //     if (window.innerWidth <= 991) {
        //         if (!sidebar.contains(e.target) && !mobileToggle.contains(e.target) && sidebar.classList.contains('open')) {
        //             toggleSidebar();
        //         }
        //     }
        // });

 