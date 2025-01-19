document.getElementById('searchInput').addEventListener('input', function () {
    const query = this.value.trim();
    const resultsDropdown = document.getElementById('searchResults');
    if (query.length > 0) {
        fetch(`http://localhost/ria/includes/search.php?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                resultsDropdown.innerHTML = ''; // Clear previous results
                if (data.length > 0) {
                    data.forEach(item => {
                        const resultItem = document.createElement('a');
                        resultItem.className = 'dropdown-item';
                        resultItem.href = `http://localhost/ria/<?php echo $_SESSION['role']=='user'?'user/':'admin/'?>dashboard.php?item_id=${item.item_id}`;
                        resultItem.textContent = item.item_name;
                        resultsDropdown.appendChild(resultItem);
                    });
                    resultsDropdown.style.display = 'block';
                } else {
                    resultsDropdown.style.display = 'none';
                }
            })
            .catch(error => console.error('Error fetching search results:', error));
    } else {
        resultsDropdown.style.display = 'none';
    }
});

// Hide results on form submit
document.getElementById('searchForm').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent page reload
    const firstResult = document.querySelector('#searchResults .dropdown-item');
    if (firstResult) {
        window.location.href = firstResult.href; // Redirect to the first result
    }
});