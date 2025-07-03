let branchData = [];
let programData = [];

// Quill editors
let quillEditors = {};

// Initialize the dashboard
document.addEventListener('DOMContentLoaded', function() {
    initializeQuillEditors();
    loadAllData();
});

// Load all data from the combined API
function loadAllData() {
    // Load branch data (keeping existing branch loading)
    fetch('../server/scripts/php/propertyhandling.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Raw branch data received:', data);
            branchData = processBranchData(data);
            console.log('Branch data processed:', branchData);
            loadBranches();
        })
        .catch(error => {
            console.error('Error fetching branch data:', error);
            branchData = [];
            loadBranches(); // Still load to show empty state
        });

    // Load program data from the combined API
    fetch('../server/scripts/php/productmanager.php', {
        method: 'GET'
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Raw combined data received:', data);
            
            if (data.success && data.programs) {
                programData = data.programs;
                console.log('Program data processed:', programData);
                loadPrograms();
            } else {
                console.warn('Unexpected data structure:', data);
                programData = [];
                loadPrograms();
            }
        })
        .catch(error => {
            console.error('Error fetching program data:', error);
            programData = [];
            loadPrograms(); // Still load to show empty state
        });
}

// Process branch data to handle the values_json structure
function processBranchData(data) {
    if (!data) return [];
    
    // If data is already an array, return it
    if (Array.isArray(data)) {
        return data;
    }
    
    // If data has values_json property, parse it
    if (data.values_json) {
        try {
            const parsedBranches = JSON.parse(data.values_json);
            return Array.isArray(parsedBranches) ? parsedBranches : [];
        } catch (e) {
            console.error('Error parsing branch values_json:', e);
            return [];
        }
    }
    
    // If data is a single object that might represent a branch
    if (data.id && data.name) {
        return [data];
    }
    
    return [];
}

function initializeQuillEditors() {
    const editorConfigs = {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{ 'header': 1 }, { 'header': 2 }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'script': 'sub'}, { 'script': 'super' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                ['link', 'image'],
                ['clean']
            ]
        }
    };

    const editorIds = [
        'descriptionEditor',
        'preRequirementsEditor', 
        'courseOverviewEditor',
        'programStructureEditor',
        'careerPathEditor',
        'studentGuidanceEditor'
    ];

    editorIds.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            quillEditors[id] = new Quill(`#${id}`, editorConfigs);
        } else {
            console.warn(`Editor element not found: ${id}`);
        }
    });
}

function loadPrograms() {
    const tbody = document.getElementById('programTableBody');
    if (!tbody) {
        console.error('Program table body not found');
        return;
    }
    
    tbody.innerHTML = '';

    if (!Array.isArray(programData) || programData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="empty-state">
                    <h3>No programs found</h3>
                    <p>Click "Add New Program" to create your first program</p>
                </td>
            </tr>
        `;
        return;
    }

    programData.forEach(program => {
        const row = document.createElement('tr');
        const branches = getBranchNames(program.branch_id);

        // Handle different possible field names and ensure proper display
        const programName = program.name || program.program_name || 'N/A';
        const programType = program.type || program.program_type || program.category_name || '';
        const programPrice = program.price ? `Rs. ${Number(program.price).toLocaleString()}` : 'N/A';
        const programDuration = program.duration ? `${program.duration} months` : 'N/A';
        const programLevel = program.level || '';
        const isActive = program.is_active == 1 || program.is_active === true || program.is_active === '1';

        row.innerHTML = `
            <td>${program.id}</td>
            <td><strong>${programName}</strong></td>
            <td><span class="status-badge">${programType}</span></td>
            <td>${programPrice}</td>
            <td>${programDuration}</td>
            <td><span class="status-badge">${programLevel}</span></td>
            <td>
                <div class="branch-tags">
                    ${branches.map(branch => `<span class="branch-tag">${branch}</span>`).join('')}
                </div>
            </td>
            <td>
                <span class="status-badge ${isActive ? 'status-active' : 'status-inactive'}">
                    ${isActive ? 'Active' : 'Inactive'}
                </span>
            </td>
            <td>
                <div class="action-buttons">
                    <button class="btn btn-sm btn-primary" onclick="editProgram('${program.id}')">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteProgram('${program.id}')">Delete</button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function loadBranches() {
    const branchSelection = document.getElementById('branchSelection');
    if (!branchSelection) {
        console.error('Branch selection element not found');
        return;
    }
    
    branchSelection.innerHTML = '';

    if (!Array.isArray(branchData) || branchData.length === 0) {
        branchSelection.innerHTML = `
            <div class="no-branches">
                <p>No branches available</p>
            </div>
        `;
        return;
    }

    branchData.forEach(branch => {
        const branchItem = document.createElement('div');
        branchItem.className = 'branch-item';
        branchItem.innerHTML = `
            <label>
                <input type="checkbox" value="${branch.id}" onchange="toggleBranch(this)">
                <strong>${branch.name}</strong>
                <div class="branch-info">
                    <div>${branch.region || ''}</div>
                    ${branch.address ? `<div class="branch-address">${branch.address}</div>` : ''}
                    ${branch.phone ? `<div class="branch-phone">${branch.phone}</div>` : ''}
                </div>
            </label>
        `;
        branchSelection.appendChild(branchItem);
    });
}

function toggleBranch(checkbox) {
    const branchItem = checkbox.closest('.branch-item');
    if (checkbox.checked) {
        branchItem.classList.add('selected');
    } else {
        branchItem.classList.remove('selected');
    }
}

function getBranchNames(branchIds) {
    if (!branchIds) return [];
    
    const ids = typeof branchIds === 'string' ? 
        branchIds.split(',').map(id => id.trim()) : 
        [branchIds.toString()];
    
    return ids.map(id => {
        if (!Array.isArray(branchData)) {
            return id; // Return the ID if branchData is not properly loaded
        }
        
        const branch = branchData.find(b => b.id === id || b.id === parseInt(id));
        return branch ? branch.name : id;
    });
}

function getSelectedBranches() {
    const checkboxes = document.querySelectorAll('#branchSelection input[type="checkbox"]:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add New Program';
    const form = document.getElementById('programForm');
    if (form) form.reset();
    document.getElementById('programId').value = '';
    
    // Clear all Quill editors
    Object.values(quillEditors).forEach(editor => {
        if (editor && editor.setText) {
            editor.setText('');
        }
    });
    
    // Clear branch selections
    document.querySelectorAll('#branchSelection input[type="checkbox"]').forEach(cb => {
        cb.checked = false;
        const branchItem = cb.closest('.branch-item');
        if (branchItem) {
            branchItem.classList.remove('selected');
        }
    });
    
    const modal = document.getElementById('programModal');
    if (modal) modal.style.display = 'block';
}

function editProgram(id) {
    const program = programData.find(p => p.id === id || p.id === parseInt(id));
    if (!program) {
        console.error('Program not found:', id);
        return;
    }

    document.getElementById('modalTitle').textContent = 'Edit Program';
    document.getElementById('programId').value = program.id;
    
    // Set form values with fallbacks
    const setFieldValue = (fieldId, value) => {
        const field = document.getElementById(fieldId);
        if (field) field.value = value || '';
    };

    setFieldValue('programName', program.name || program.program_name);
    setFieldValue('programType', program.type || program.program_type);
    setFieldValue('programPrice', program.price);
    setFieldValue('programDuration', program.duration);
    setFieldValue('programLevel', program.level);
    setFieldValue('programStudyMode', program.study_mode);
    setFieldValue('programStatus', program.is_active);

    // Set Quill editor content
    const setEditorContent = (editorId, content) => {
        if (quillEditors[editorId] && quillEditors[editorId].root) {
            quillEditors[editorId].root.innerHTML = content || '';
        }
    };

    setEditorContent('descriptionEditor', program.description);
    setEditorContent('preRequirementsEditor', program.pre_requirements);
    setEditorContent('courseOverviewEditor', program.course_overview);
    setEditorContent('programStructureEditor', program.program_structure);
    setEditorContent('careerPathEditor', program.career_path);
    setEditorContent('studentGuidanceEditor', program.student_guidance);

    // Set branch selections
    const branchIds = program.branch_id ? 
        (typeof program.branch_id === 'string' ? 
            program.branch_id.split(',').map(id => id.trim()) : 
            [program.branch_id.toString()]) : 
        [];
    
    document.querySelectorAll('#branchSelection input[type="checkbox"]').forEach(cb => {
        cb.checked = branchIds.includes(cb.value);
        const branchItem = cb.closest('.branch-item');
        if (branchItem) {
            if (cb.checked) {
                branchItem.classList.add('selected');
            } else {
                branchItem.classList.remove('selected');
            }
        }
    });

    const modal = document.getElementById('programModal');
    if (modal) modal.style.display = 'block';
}

function deleteProgram(id) {
    if (!confirm('Are you sure you want to delete this program? This action cannot be undone.')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('program_id', id);

    fetch('../server/scripts/php/productmanager.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Delete response:', data);
        
        if (data.success) {
            programData = programData.filter(p => p.id !== id && p.id !== parseInt(id));
            loadPrograms();
            alert('Program deleted successfully!');
        } else {
            throw new Error(data.message || 'Failed to delete program');
        }
    })
    .catch(error => {
        console.error('Error deleting program:', error);
        alert('Error deleting program: ' + error.message);
    });
}

function saveProgram() {
    const programId = document.getElementById('programId').value;
    const selectedBranches = getSelectedBranches();
    
    if (selectedBranches.length === 0) {
        alert('Please select at least one branch');
        return;
    }

    const programData = {
        id: programId || null,
        name: document.getElementById('programName').value,
        type: document.getElementById('programType').value,
        price: parseFloat(document.getElementById('programPrice').value) || 0,
        duration: parseInt(document.getElementById('programDuration').value) || 0,
        level: document.getElementById('programLevel').value,
        description: quillEditors.descriptionEditor.root.innerHTML,
        pre_requirements: quillEditors.preRequirementsEditor.root.innerHTML,
        course_overview: quillEditors.courseOverviewEditor.root.innerHTML,
        program_structure: quillEditors.programStructureEditor.root.innerHTML,
        career_path: quillEditors.careerPathEditor.root.innerHTML,
        student_guidance: quillEditors.studentGuidanceEditor.root.innerHTML,
        study_mode: document.getElementById('programStudyMode').value,
        branch_ids: selectedBranches,
        is_active: parseInt(document.getElementById('programStatus').value) || 0
    };

    const saveButton = document.querySelector('#programModal .btn-primary');
    if (saveButton) {
        saveButton.disabled = true;
        saveButton.textContent = 'Saving...';
    }

    // Create FormData for PHP compatibility
    const formData = new FormData();
    formData.append('action', programId ? 'update' : 'create');
    formData.append('program_data', JSON.stringify(programData));

    fetch('../server/scripts/php/productmanager.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Save response:', data);
        
        if (data.success) {
            loadAllData();
            closeModal();
            alert(programId ? 'Program updated successfully!' : 'Program created successfully!');
        } else {
            throw new Error(data.message || 'Failed to save program');
        }
    })
    .catch(error => {
        console.error('Error saving program:', error);
        alert('Error saving program: ' + error.message);
    })
    .finally(() => {
        if (saveButton) {
            saveButton.disabled = false;
            saveButton.textContent = 'Save Program';
        }
    });
}

function closeModal() {
    const modal = document.getElementById('programModal');
    if (modal) modal.style.display = 'none';
}

function searchPrograms() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;
    
    const query = searchInput.value.toLowerCase();
    const rows = document.querySelectorAll('#programTableBody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
    });
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('programModal');
    if (event.target === modal) {
        closeModal();
    }
};

// Debug function to inspect current program data
function debugProgramData() {
    console.log('Current programData:', programData);
    console.log('Current branchData:', branchData);
    console.log('Processed branches:', branchData);
    return { programData, branchData };
}

// Make debug function available globally
window.debugProgramData = debugProgramData;