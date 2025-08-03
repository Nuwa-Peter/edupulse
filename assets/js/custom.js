// EduPulse Custom JavaScript

$(document).ready(function() {
    // This is a good place for any initializations
    // For example, initializing tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});

// Add any other global custom scripts here.
// For example, a function to handle live search can be defined here.
function handleLiveSearch(query) {
    if (query.length > 2) {
        // Perform an AJAX call to a search API endpoint
        console.log("Searching for: " + query);
        // fetch('/api/search.php?q=' + query)
        //     .then(response => response.json())
        //     .then(data => {
        //         // update UI with search results
        //     });
    }
}
