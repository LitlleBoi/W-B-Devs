function initSearchToggle() {
    const searchToggle = document.getElementById('search-toggle');
    const searchBar = document.getElementById('search-bar');

    if (searchToggle && searchBar) {
        searchToggle.addEventListener('click', (e) => {
            e.preventDefault();
            // Toggle display between block and none
            if (searchBar.style.display === 'none' || searchBar.style.display === '') {
                searchBar.style.display = 'block';
            } else {
                searchBar.style.display = 'none';
            }
        });
    }
}

// Initialize all functions when DOM is fully loaded
document.addEventListener('DOMContentLoaded', () => {
    initSearchToggle();
});