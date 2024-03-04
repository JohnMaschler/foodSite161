function performSearch() {
    // Get the search input value
    var searchInput = document.getElementById('search-input').value;

    // For demonstration purposes, log the input value to the console
    console.log("Search for:", searchInput);

    // You would typically make an AJAX request to your server here
    // and update the search-results div with the returned data.

    // Since we don't have a backend, we'll just display a placeholder message
    var searchResults = document.getElementById('search-results');
    searchResults.innerHTML = 'Results for "' + searchInput + '" would be displayed here.';
}
