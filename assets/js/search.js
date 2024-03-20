function performSearch() {
    var searchInput = document.getElementById('search-input').value;
    var searchType = document.querySelector('input[name="search_type"]:checked').value;


    if (searchInput.length >= 1) {
        var xhr = new XMLHttpRequest();
        xhr.onload = function() {
            if (this.status === 200) {
                var parser = new DOMParser();
                var resultDoc = parser.parseFromString(this.responseText, 'text/html');
                var resultItems = resultDoc.querySelectorAll('.recipe-card');

                var resultsContainer = document.getElementById('search-results');
                resultsContainer.innerHTML = '';

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
        document.getElementById('search-results').innerHTML = '';
    }
}

document.getElementById('search-input').addEventListener('input', performSearch);
