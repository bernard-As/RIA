<?php
$query1 = "SELECT firstname, lastname, email, profile_picture FROM users WHERE id = ?";
$stmt1 = $conn->prepare($query1);
$stmt1->bind_param("i", $_SESSION['user_id']);
$stmt1->execute();
$result1 = $stmt1->get_result();
$user1 = $result1->fetch_assoc();
$stmt1->close();
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow mb-4">
    <!-- Brand Name or Logo -->
    <a class="navbar-brand" href="http://localhost/ria">RIA
    </a>
    <!-- Toggler for mobile view -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <?php if(isset($_SESSION['user_id'])):?>

    <div class="collapse navbar-collapse" id="navbarContent">
      <!-- Search Bar -->
      <form class="d-flex me-auto ms-3 position-relative" id="searchForm">
    <input 
        class="form-control me-2" 
        type="search" 
        id="searchInput" 
        placeholder="Search for..." 
        aria-label="Search" 
        autocomplete="off">
    <button class="btn btn-primary" type="submit">Search</button>
    <div id="searchResults" class="dropdown-menu position-absolute w-100" style="z-index: 1050; top:40px"></div>
    </form>
                    <a href="http://localhost/ria/<?php echo ($_SESSION['role'] == 'admin' ? 'admin' : 'user'); ?>/dashboard.php" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm"
                      style="padding-rigth:7px;"
                    ><i
                            class="fas fa-download fa-sm text-white-50"></i>Dashboard</a>
                    </div>

      <!-- Profile Section -->
      <div class="dropdown">
        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          <span class="d-none d-lg-inline text-gray-600 small me-2">
            <?php
                echo $_SESSION['firstname'].' '.$_SESSION['lastname']
                ?>
          </span>
          <!-- <img src="../utilities/img/undraw_profile.svg" alt="Profile" class="rounded-circle" width="40" height="40"> -->
          <img src="<?php echo ($user1['profile_picture'] == NULL ? 
            'http://localhost/ria/utilities/img/undraw_profile.svg' : 
            'http://localhost/ria/utilities/uploads/' . $user1['profile_picture']); ?>" 
            alt="Profile" 
            class="rounded-circle" 
            width="40" 
            height="40">
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
          <li><a class="dropdown-item" href="http://localhost/ria/profile.php"><i class="fas fa-user fa-sm fa-fw me-2"></i>Profile</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="http://localhost/ria/includes/logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2"></i>Logout</a></li>
        </ul>
      </div>
    </div>
    <?php else:?>
      <a href="http://localhost/ria/auth/login.php" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm"
                      style="padding-rigth:7px;"
                    ><i
                            class="fas fa-download fa-sm text-white-50"></i>Login</a>
    <?php endif?>
</nav>


    <!-- Bootstrap core JavaScript-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->

  <script>
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

  </script>