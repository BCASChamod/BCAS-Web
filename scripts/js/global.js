// #region Theme

function updateLogo() {
  const logos = document.querySelectorAll('.logo');
  if (!logos.length) return;
  const theme = document.documentElement.getAttribute('data-theme');
  // Wait for branch data to be available, then update logo id based on active branch
  new Promise((resolve) => {
    const tryGetBranch = () => {
      const activeBranchData = sessionStorage.getItem('activeBranch');
      if (activeBranchData) {
        resolve(JSON.parse(activeBranchData));
      } else {
        setTimeout(tryGetBranch, 100);
      }
    };
    tryGetBranch();
  }).then((activeBranch) => {
    logos.forEach(logo => {
      if (activeBranch && activeBranch.id) {
        logo.id = `${theme === 'dark' ? 'dark' : 'light'}${activeBranch.id}logo`;
      } else {
        logo.id = theme === 'dark' ? 'darkLogo' : 'lightLogo';
      }
    });
  });
}

updateLogo();

function setupThemeSwitch(checkbox) {
  checkbox.checked = document.documentElement.getAttribute('data-theme') === 'light';
  checkbox.addEventListener('change', () => {
    const current = document.documentElement.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    setTimeout(() => window.location.reload(), 500);
  });
}

const observer = new MutationObserver((mutations, obs) => {
  updateLogo();
  const checkbox = document.getElementById('themeSwitch');
  if (checkbox) {
    setupThemeSwitch(checkbox);
    obs.disconnect();
  }
});

observer.observe(document.body, { childList: true, subtree: true });

// #endregion Theme

window.addEventListener('DOMContentLoaded', () => {

  function sliderIn(element, duration = 5000) {
    if (!element) return;
    if (!element.classList.contains(':hover')) {
      element.classList.add('slide-in');
      setTimeout(() => {
        element.classList.remove('slide-in');
      }, 4000);
    }
  }

  setTimeout(() => {
      sliderIn(document.querySelector('.geo-tracking-container'));
  }, 3000);


});

// #region Tracking

// Location User Override Modal Injection

document.addEventListener('DOMContentLoaded', () => {
  const geoTrackingHTML = `
  <div class="geo-tracking-container active">
    <div class="geo-tracking">
      <div class="slider-trigger"><i class="fa-regular fa-arrow-left-to-line"></i></div>
      <div class="content">
        <label for="branchDropdown">Showing info for your nearest branch.</label>
        <div id="branchOverrideStatus" class="auto-detected">
          <span class="status-icon"><i class="fa-regular fa-location-crosshairs"></i></span>
          <span class="status-text">Using auto-detected location</span>
        </div>
        <p>Not correct? Choose another:</p>
        <select id="branchDropdown"></select>
        <button id="resetBranchSelection" class="hidden">
          <i class="fa-solid fa-arrow-rotate-left"></i> Auto-Detect
        </button>
      </div>
    </div>
  </div>
  `;
  if (!document.querySelector('.geo-tracking-container')) {
    document.body.insertAdjacentHTML('afterbegin', geoTrackingHTML);
  }
});



// #region Branch Tracking System

window.addEventListener('DOMContentLoaded', () => {

// Global state
let isUserOverride = false;
let debugMode = false; // Set to true for debugging

// Utility function for logging
function log(...args) {
  if (debugMode) console.log('[BranchTracker]', ...args);
}

function error(...args) {
  console.error('[BranchTracker]', ...args);
}
// Initialize branch data and handle user preferences
function initializeBranchData() {
  log('Initializing branch data...');
  
  fetch("http://localhost/bcas-web/scripts/php/geoTracking.php")
    .then(res => {
      if (!res.ok) {
        throw new Error(`HTTP ${res.status}: ${res.statusText}`);
      }
      return res.text();
    })
    .then(text => {
      log('Raw response:', text);
      
      try {
        const data = JSON.parse(text);
        log('Parsed data:', data);

        // Handle different response formats
        if (data.success === false) {
          error('Server returned error:', data.error);
          handleFallbackMode();
          return;
        }

        // Validate response structure
        if (!data.allBranches || !Array.isArray(data.allBranches)) {
          error('Invalid response: allBranches missing or not an array');
          handleFallbackMode();
          return;
        }

        // Always store all branches
        sessionStorage.setItem('allBranches', JSON.stringify(data.allBranches));
        log('Stored', data.allBranches.length, 'branches');

        // Store additional info for debugging
        if (data.userLocation) {
          sessionStorage.setItem('userLocation', JSON.stringify(data.userLocation));
          log('User location:', data.userLocation);
        }

        // Determine active branch based on priority
        const activeBranch = determineActiveBranch(data);
        
        if (activeBranch) {
          sessionStorage.setItem('activeBranch', JSON.stringify(activeBranch));
          log('Active branch set to:', activeBranch.name);
        } else {
          error('No active branch could be determined');
          sessionStorage.removeItem('activeBranch');
        }

        // Initialize content after data is loaded
        handleContent();
        
      } catch (e) {
        error("Error parsing branch data JSON:", e.message);
        error("Raw response was:", text);
        handleFallbackMode();
      }
    })
    .catch(err => {
      error("Error fetching branch data:", err.message);
      handleFallbackMode();
    });
}

// Determine which branch should be active based on priority
function determineActiveBranch(data) {
  // Priority 1: Check if user has previously overridden the selection
  const userOverrideBranch = sessionStorage.getItem('userSelectedBranch');
  const hasUserOverride = sessionStorage.getItem('hasUserOverride') === 'true';

  if (hasUserOverride && userOverrideBranch) {
    try {
      const selectedBranch = JSON.parse(userOverrideBranch);
      // Verify this branch still exists in the current data
      const stillExists = data.allBranches.find(branch => 
        branch.id === selectedBranch.id || 
        (branch.name === selectedBranch.name && !branch.id)
      );
      
      if (stillExists) {
        isUserOverride = true;
        log("Using user override branch:", selectedBranch.name);
        return selectedBranch;
      } else {
        log("User override branch no longer exists, clearing override");
        clearUserOverride();
      }
    } catch (e) {
      error("Invalid user override data, clearing:", e.message);
      clearUserOverride();
    }
  }

  // Priority 2: Use auto-matched branch from server
  if (data.matchedBranch && isValidBranch(data.matchedBranch)) {
    log("Using auto-matched branch:", data.matchedBranch.name);
    return data.matchedBranch;
  }

  // Priority 3: Fallback to first branch
  if (data.allBranches && data.allBranches.length > 0) {
    const firstBranch = data.allBranches[0];
    if (isValidBranch(firstBranch)) {
      log("No match found. Defaulting to first branch:", firstBranch.name);
      return firstBranch;
    }
  }

  error("No valid branches found in data");
  return null;
}

// Validate branch object structure
function isValidBranch(branch) {
  return branch && 
         typeof branch === 'object' && 
         branch.name && 
         typeof branch.name === 'string';
}

// Clear user override settings
function clearUserOverride() {
  sessionStorage.removeItem('hasUserOverride');
  sessionStorage.removeItem('userSelectedBranch');
  isUserOverride = false;
}

// Handle fallback mode when API fails
function handleFallbackMode() {
  log("Entering fallback mode - using cached data if available");
  
  // Try to use existing cached data
  const cachedBranches = sessionStorage.getItem('allBranches');
  const cachedActiveBranch = sessionStorage.getItem('activeBranch');
  
  if (cachedBranches && cachedActiveBranch) {
    log("Using cached branch data");
    handleContent();
  } else {
    error("No cached data available - branch system not functional");
    // Could show user a message about connectivity issues
    showConnectivityError();
  }
}

// Show connectivity error to user
function showConnectivityError() {
  const errorElement = document.getElementById('branchLoadingError');
  if (errorElement) {
    errorElement.style.display = 'block';
    errorElement.textContent = 'Unable to load branch information. Please check your connection and refresh the page.';
  }
}

// Main content handler
function handleContent() {
  const activeBranchData = sessionStorage.getItem('activeBranch');
  const activeBranch = activeBranchData ? JSON.parse(activeBranchData) : null;
  
  log('Handling content with branch:', activeBranch ? activeBranch.name : 'None');

  // Hide any error messages
  const errorElement = document.getElementById('branchLoadingError');
  if (errorElement) {
    errorElement.style.display = 'none';
  }

  if (activeBranch) {
    updateBranchContent(activeBranch);
  }

  setupBranchDropdown();
  setupEventListeners();
  updateOverrideStatus();
}

// Update all branch-related content on the page
function updateBranchContent(branch) {
  log('Updating branch content for:', branch.name);

  const elements = {
    '.branch-name': branch.name,
    '.branch-address': branch.address,
    '.branch-phone': branch.phone,
    '.branch-email': branch.email,
    '.branch-region': branch.region
  };

  Object.entries(elements).forEach(([selector, value]) => {
    const element = document.querySelector(selector);
    if (element && value) {
      // Handle different element types
      if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA') {
        element.value = value;
      } else {
        element.textContent = value;
      }
      
      // Add data attribute for styling/targeting
      element.setAttribute('data-branch-field', selector.replace('.', ''));
    }
  });

  // Handle special cases like links
  const phoneLink = document.querySelector('a[href^="tel:"]');
  if (phoneLink && branch.phone) {
    phoneLink.href = `tel:${branch.phone.replace(/\s+/g, '')}`;
  }

  const emailLink = document.querySelector('a[href^="mailto:"]');
  if (emailLink && branch.email) {
    emailLink.href = `mailto:${branch.email}`;
  }
}

// Setup branch selection dropdown
function setupBranchDropdown() {
  const branchSelector = document.getElementById('branchDropdown');
  
  if (!branchSelector) {
    log('Branch dropdown not found');
    return;
  }

  const allBranchesData = sessionStorage.getItem('allBranches');
  const activeBranchData = sessionStorage.getItem('activeBranch');
  
  if (!allBranchesData) {
    log('No branch data available for dropdown');
    return;
  }

  const allBranches = JSON.parse(allBranchesData);
  const activeBranch = activeBranchData ? JSON.parse(activeBranchData) : null;

  log('Setting up dropdown with', allBranches.length, 'branches');

  // Clear existing options
  branchSelector.innerHTML = '';

  // Add default option if needed
  if (allBranches.length === 0) {
    const option = document.createElement('option');
    option.textContent = 'No branches available';
    option.disabled = true;
    branchSelector.appendChild(option);
    return;
  }

  // Add branch options
  allBranches.forEach((branch, idx) => {
    if (!isValidBranch(branch)) {
      log('Skipping invalid branch:', branch);
      return;
    }

    const option = document.createElement('option');
    option.value = branch.id !== undefined ? branch.id : idx;
    option.textContent = branch.name;
    
    // Add region info if available
    if (branch.region) {
      option.textContent += ` (${branch.region})`;
    }
    
    // Set selected option
    const isSelected = activeBranch && (
      (branch.id !== undefined && activeBranch.id !== undefined && 
       String(branch.id) === String(activeBranch.id)) ||
      (branch.id === undefined && branch.name === activeBranch.name)
    );
    
    if (isSelected) {
      option.selected = true;
    }
    
    branchSelector.appendChild(option);
  });
}

// Setup event listeners for user interactions
function setupEventListeners() {
  const branchSelector = document.getElementById('branchDropdown');
  const resetButton = document.getElementById('resetBranchSelection');

  // Remove existing event listeners to prevent duplicates
  if (branchSelector) {
    branchSelector.removeEventListener('change', handleBranchChange);
    branchSelector.addEventListener('change', handleBranchChange);
    log('Branch selector event listener attached');
  }

  if (resetButton) {
    resetButton.removeEventListener('click', resetToAutoDetected);
    resetButton.addEventListener('click', resetToAutoDetected);
    log('Reset button event listener attached');
  }
}

// Handle branch selection change
function handleBranchChange(e) {
  const selectedValue = e.target.value;
  const allBranches = JSON.parse(sessionStorage.getItem('allBranches')) || [];
  
  log('Branch selection changed to:', selectedValue);
  
  // Find selected branch
  const selectedBranch = allBranches.find((branch, idx) =>
    (branch.id !== undefined && String(branch.id) === selectedValue) ||
    (branch.id === undefined && String(idx) === selectedValue)
  );

  if (selectedBranch) {
    // Mark as user override
    isUserOverride = true;
    sessionStorage.setItem('hasUserOverride', 'true');
    sessionStorage.setItem('userSelectedBranch', JSON.stringify(selectedBranch));
    sessionStorage.setItem('activeBranch', JSON.stringify(selectedBranch));
    
    log("User selected branch:", selectedBranch.name);
    
    // Update content immediately
    updateBranchContent(selectedBranch);
    updateOverrideStatus();
    
    // Trigger custom event for other parts of the application
    dispatchBranchChangeEvent(selectedBranch, 'user-selection');

    // Reload the page after selection
    window.location.reload();
  } else {
    error('Selected branch not found:', selectedValue);
  }
}

// Update override status display
function updateOverrideStatus() {
  const statusElement = document.getElementById('branchOverrideStatus');
  const statusIcon = statusElement?.querySelector('.status-icon');
  const statusText = statusElement?.querySelector('.status-text');
  const resetButton = document.getElementById('resetBranchSelection');
  const hasOverride = sessionStorage.getItem('hasUserOverride') === 'true';

  log('Updating override status, hasOverride:', hasOverride);

  if (statusElement) {
    // Remove existing classes
    statusElement.classList.remove('auto-detected', 'user-selected');
    
    if (hasOverride) {
      statusElement.classList.add('user-selected');
      if (statusIcon) statusIcon.innerHTML = '<svg aria-hidden="true" focusable="false" data-prefix="fa-light" data-icon="location-crosshairs" class="svg-inline--fa fa-location-crosshairs fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M256 160C203 160 160 203 160 256S203 352 256 352S352 309 352 256S309 160 256 160ZM256 320C220.75 320 192 291.25 192 256S220.75 192 256 192S320 220.75 320 256S291.25 320 256 320ZM496 240H447.25C439.5 146.75 365.25 72.5 272 64.75V16C272 7.199 264.801 0 256 0C247.201 0 240 7.199 240 16V64.75C146.75 72.5 72.5 146.75 64.75 240H16C7.201 240 0 247.199 0 256C0 264.799 7.201 272 16 272H64.75C72.5 365.25 146.75 439.5 240 447.25V496C240 504.799 247.201 512 256 512C264.801 512 272 504.799 272 496V447.25C365.25 439.5 439.5 365.25 447.25 272H496C504.801 272 512 264.799 512 256C512 247.199 504.801 240 496 240ZM256 416C167.75 416 96 344.25 96 256S167.75 96 256 96S416 167.75 416 256S344.25 416 256 416Z" fill="currentColor"/></svg>';
      if (statusText) statusText.textContent = 'Using your selected location';
    } else {
      statusElement.classList.add('auto-detected');
      if (statusIcon) statusIcon.innerHTML = '<svg aria-hidden="true" focusable="false" data-prefix="fa-light" data-icon="location-check" class="svg-inline--fa fa-location-check fa-w-12" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M192 0C85.969 0 0 85.969 0 192C0 269.41 26.969 291.035 172.281 501.676C177.047 508.559 184.523 512 192 512S206.953 508.559 211.719 501.676C357.031 291.035 384 269.41 384 192C384 85.969 298.031 0 192 0ZM192 473.918C51.932 271.379 32 255.969 32 192C32 103.777 103.775 32 192 32S352 103.777 352 192C352 255.879 332.566 270.674 192 473.918ZM260.688 143.344L165.328 238.719L123.312 196.688C117.062 190.438 106.937 190.438 100.688 196.688S94.438 213.063 100.688 219.312L154.016 272.656C157.016 275.656 161.078 277.344 165.328 277.344S173.641 275.656 176.641 272.656L283.312 165.969C289.562 159.719 289.562 149.594 283.312 143.344S266.938 137.094 260.688 143.344Z" fill="currentColor"/></svg>';
      if (statusText) statusText.textContent = 'Using auto-detected location';
    }
  }

  if (resetButton) {
    if (hasOverride) {
      resetButton.classList.remove('hidden');
      resetButton.style.display = '';
    } else {
      resetButton.classList.add('hidden');
      resetButton.style.display = 'none';
    }
  }
}

// Reset to auto-detected branch
function resetToAutoDetected() {
  log("Resetting to auto-detected location");
  
  // Clear user override
  clearUserOverride();

  // Re-initialize with auto-detection
  initializeBranchData();

  const activeBranchData = sessionStorage.getItem('activeBranch');
  if (activeBranchData) {
    const activeBranch = JSON.parse(activeBranchData);
    dispatchBranchChangeEvent(activeBranch, 'auto-reset');
  }

  // Reload the page after reset
  window.location.reload();
}

// Utility function to manually set a branch (for programmatic control)
function setBranch(branchId, isUserSelection = true) {
  log('Programmatically setting branch:', branchId, 'as user selection:', isUserSelection);
  
  const allBranches = JSON.parse(sessionStorage.getItem('allBranches')) || [];
  const targetBranch = allBranches.find(branch => 
    (branch.id !== undefined && String(branch.id) === String(branchId)) ||
    (branch.id === undefined && branch.name === branchId)
  );

  if (targetBranch) {
    if (isUserSelection) {
      sessionStorage.setItem('hasUserOverride', 'true');
      sessionStorage.setItem('userSelectedBranch', JSON.stringify(targetBranch));
      isUserOverride = true;
    }
    
    sessionStorage.setItem('activeBranch', JSON.stringify(targetBranch));
    handleContent();
    
    // Trigger custom event
    dispatchBranchChangeEvent(targetBranch, isUserSelection ? 'programmatic-user' : 'programmatic-auto');
    
    log('Successfully set branch to:', targetBranch.name);
    return true;
  }
  
  error('Branch not found:', branchId);
  return false;
}

// Dispatch custom event when branch changes
function dispatchBranchChangeEvent(branch, changeType) {
  const event = new CustomEvent('branchChanged', {
    detail: {
      branch: branch,
      changeType: changeType, // 'user-selection', 'auto-detection', 'auto-reset', 'programmatic-user', 'programmatic-auto'
      isUserOverride: isUserOverride
    }
  });
  document.dispatchEvent(event);
  log('Dispatched branchChanged event:', changeType, branch.name);
}

// Get current active branch (utility function for other scripts)
function getActiveBranch() {
  const activeBranchData = sessionStorage.getItem('activeBranch');
  return activeBranchData ? JSON.parse(activeBranchData) : null;
}

// Get all available branches (utility function for other scripts)
function getAllBranches() {
  const allBranchesData = sessionStorage.getItem('allBranches');
  return allBranchesData ? JSON.parse(allBranchesData) : [];
}

// Check if user has overridden the selection (utility function)
function hasUserOverride() {
  return sessionStorage.getItem('hasUserOverride') === 'true';
}

// Enable/disable debug mode
function setDebugMode(enabled) {
  debugMode = enabled;
  log('Debug mode', enabled ? 'enabled' : 'disabled');
}

// Initialize when DOM is ready
function initializeWhenReady() {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeBranchData);
  } else {
    initializeBranchData();
  }
}

// Auto-initialize
initializeWhenReady();

// Export functions for external use
window.BranchTracker = {
  setBranch,
  getActiveBranch,
  getAllBranches,
  hasUserOverride,
  resetToAutoDetected,
  setDebugMode,
  reinitialize: initializeBranchData
};

});
// #endregion
// #endregion Tracking


// Only needed if you're using Font Awesome with JS (SVG version)
if (window.FontAwesome) {
  FontAwesome.dom.i2svg();
}
