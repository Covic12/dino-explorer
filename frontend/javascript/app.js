/**
 * C:\Users\ismet\OneDrive\Desktop\dino-explorer\frontend\javascript\app.js
 * * Core file for managing SPA routing and dynamic content loading.
 */

// --- 1. CONFIGURATION: Define Routes ---
// Map paths to the corresponding HTML file names (relative to the base HTML location)
const routes = {
    '/': 'dashboard.html',
    '/dashboard.html': 'dashboard.html',
    '/dinosaurs.html': 'dinosaurs.html',
    '/eras.html': 'eras.html',
    '/locations.html': 'locations.html',
    '/researchers.html': 'researchers.html',
    '/profile.html': 'profile.html',
    // Special case for detail pages (dino-details.html) - we'll handle parameters separately
    '/dino-details.html': 'dino-details.html', 
    '/login.html': 'login.html' // Generally, you'd handle login outside the main SPA loop
};

const mainContent = document.querySelector('.main-content');
const appHeader = document.querySelector('header');

// --- 2. CORE FUNCTIONS ---

/**
 * Loads content dynamically into the main content area.
 * @param {string} path - The URL path to determine the content to load.
 */
async function loadContent(path) {
    // 1. Determine the content file based on the path
    let fileName = routes[path] || routes[path.split('?')[0]]; // Handles paths with query strings (e.g., /dino-details.html?id=1)
    
    if (!fileName) {
        console.error('Route not found:', path);
        // Load a 404 page or default to dashboard
        fileName = 'dashboard.html'; 
    }
    
    // Check if the content is the login page (which usually breaks the SPA flow)
    if (fileName === 'login.html') {
        // If navigating to login, allow full page reload (or handle session cleanup)
        window.location.href = 'login.html';
        return; 
    }
    
    try {
        // 2. Fetch the content
        const response = await fetch(fileName);
        if (!response.ok) {
            throw new Error(`Failed to load ${fileName}: ${response.statusText}`);
        }
        
        const html = await response.text();
        
        // 3. Extract the content you need (e.g., everything inside the .main-content div)
        // This is crucial to avoid loading the header/body/scripts again.
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newContent = doc.querySelector('.main-content')?.innerHTML || '';

        // 4. Update the DOM
        if (mainContent) {
            mainContent.innerHTML = newContent;
            console.log(`Content loaded for: ${path}`);
            
            // 5. Re-initialize any dynamic scripts needed for the new content (optional, but good practice)
            // For example, if dino-details.html has unique JS, you'd call an init function here.
            // initDetailPageScripts(); 
            
            // 6. Update active navigation link
            updateActiveLink(path);
        }

    } catch (error) {
        console.error('Error loading content:', error);
        if (mainContent) {
             mainContent.innerHTML = `<div class="alert alert-danger">Error loading content for ${fileName}.</div>`;
        }
    }
}

/**
 * Handles navigation clicks to prevent full page reload.
 * @param {Event} event - The click event.
 */
function handleNavigation(event) {
    const target = event.target.closest('a');
    if (target && target.href) {
        // Only intercept internal links
        if (target.origin === window.location.origin) {
            event.preventDefault(); 

            // Get the relative path (e.g., /profile.html)
            const path = target.pathname + target.search; 
            
            // Update the browser history
            window.history.pushState({ path }, '', path); 
            
            // Load the new content
            loadContent(path);
        }
    }
}

/**
 * Updates the 'active' class on the navigation links.
 * @param {string} currentPath - The current URL path.
 */
function updateActiveLink(currentPath) {
    const navLinks = appHeader.querySelectorAll('nav a');
    const basePath = currentPath.split('?')[0]; // Ignore query string for active state

    navLinks.forEach(link => {
        link.classList.remove('active');
        const linkPath = link.getAttribute('href');
        
        if (linkPath === basePath) {
            link.classList.add('active');
        }
    });
}

// --- 3. INITIALIZATION ---

/**
 * Initializes the SPA framework.
 */
function initSPA() {
    // 1. Attach listener to the header/navigation for link clicks
    appHeader.addEventListener('click', handleNavigation);
    
    // 2. Attach listener for the main content area (to handle links within the loaded content)
    // We attach it to the body or main container for delegation
    document.body.addEventListener('click', handleNavigation);

    // 3. Handle browser back/forward buttons
    window.addEventListener('popstate', (event) => {
        // The event.state contains the data from history.pushState
        if (event.state && event.state.path) {
            loadContent(event.state.path);
        } else {
             // For initial load or history manipulation outside pushState
            loadContent(window.location.pathname + window.location.search);
        }
    });

    // 4. Initial content load
    const initialPath = window.location.pathname + window.location.search;
    
    // Check if the current page is the base path (e.g., /index.html or just /)
    if (initialPath === '/' || initialPath.endsWith('/')) {
        // If opening the base URL, default to the dashboard content
        window.history.replaceState({ path: routes['/'] }, '', routes['/']);
        loadContent(routes['/']);
    } else {
        // If the user navigates directly to a specific page (e.g., /profile.html)
        loadContent(initialPath);
        // Replace current history state to match the loaded path
        window.history.replaceState({ path: initialPath }, '', initialPath); 
    }
}

// Start the SPA when the document is ready
document.addEventListener('DOMContentLoaded', initSPA);