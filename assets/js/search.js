function performSearch() {
    var searchInput = document.getElementById('search-input').value;
    var searchType = document.querySelector('input[name="search_type"]:checked').value;

    // Check if the input is not empty and at least a certain number of characters
    if (searchInput.length >= 1) { // Adjust the minimum length as needed
        var xhr = new XMLHttpRequest();
        xhr.onload = function() {
            if (this.status === 200) {
                // Parse the response to DOM elements
                var parser = new DOMParser();
                var resultDoc = parser.parseFromString(this.responseText, 'text/html');
                var resultItems = resultDoc.querySelectorAll('.recipe-card');

                // Clear previous results
                var resultsContainer = document.getElementById('search-results');
                resultsContainer.innerHTML = '';

                // Append only the first two suggested results
                for (var i = 0; i < resultItems.length && i < 2; i++) {
                    resultsContainer.appendChild(resultItems[i]);
                }
            } else {
                document.getElementById('search-results').innerHTML = "Error retrieving results.";
            }
        };
        xhr.open('GET', 'search_backend.php?search=' + encodeURIComponent(searchInput) + '&search_type=' + searchType, true);
        xhr.send();
    } else {
        // Clear results if input is empty
        document.getElementById('search-results').innerHTML = '';
    }
}

// Trigger search when typing
document.getElementById('search-input').addEventListener('input', performSearch);
