function performSearch() {
    //get the search input value
    var searchInput = document.getElementById('search-input').value;

    console.log("Search for:", searchInput);
    var searchResults = document.getElementById('search-results');
    searchResults.innerHTML = 'Results for "' + searchInput + '" would be displayed here.';
}
