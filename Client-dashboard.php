<?php
session_start();

// Prevent browser caching of this page
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard - View Projects</title>
    <link rel="icon" type="image/png" href="Chromaspark.png.jpeg">
    <h1>Welcome to Chroma Spark</h1>
    
    <style>
        body { 
            font-family: Arial, sans-serif; 
            text-align: center; 
            margin: 20px; 
            background-color: #f8f9fa;
        }
        .container { 
            max-width: 900px; 
            margin: auto; 
        }
        h2 { 
            font-size: 28px; 
            color: #333; 
            margin-bottom: 20px;
        }
        .project-list { 
            display: flex; 
            flex-direction: column; 
            gap: 20px;
        }
        .project-item { 
            background: white; 
            border-radius: 10px; 
            padding: 20px; 
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
            transition: transform 0.2s; 
            cursor: pointer;
        }
        .project-item:hover { 
            transform: scale(1.02); 
        }
        .project-img { 
            width: 100%; 
            max-height: 400px; 
            object-fit: cover; 
            border-radius: 10px;
        }
        .project-details { 
            padding: 15px 0; 
            text-align: left;
        }
        .project-name { 
            font-size: 22px; 
            font-weight: bold; 
            color: #222;
        }
        .project-description { 
            font-size: 16px; 
            color: #555; 
            margin-top: 10px;
        }

        /* Advertisements */
        .ads-section {
            margin-top: 30px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .ad-item {
            margin: 10px 0;
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            max-width: 80%;
            text-align: center;
            position: relative;
        }
        .modal img {
            max-width: 100%;
            max-height: 500px;
            border-radius: 10px;
        }
        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: black;
        }
        .close-btn:hover {
            color: red;
        }

        /* Ratings & Feedback */
        .rating {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin: 10px 0;
        }
        .rating span {
            font-size: 24px;
            cursor: pointer;
            color: #ccc;
        }
        .rating span.active {
            color: gold;
        }
        textarea {
            width: 100%;
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        button {
            margin-top: 10px;
            padding: 10px;
            background: #0d0235;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #fdb43b;
        }



.game-button {
    background-color: #05013d;;
    color: #fdb43b;
    padding: 10px 20px;
    text-decoration: none;
    font-size: 16px;
    font-weight: bold;
    border-radius: 5px;
    transition: background-color 0.3s;
    position: absolute; /* Allows positioning */
    top: 10px;   /* Distance from the top */
    left: 10px;  /* Distance from the left */
}

.game-button:hover {
    background-color: #05013d;;
}


    </style>
</head>
<body style="background-color: #fdb43b;">

    <h2>Client Dashboard - Projects</h2>
    <p>By clicking on the project you can rate our projects and give feedbacks</p>

    <div class="container">
        <div class="project-list" id="projectList"></div>


    <!-- Modal for Fullscreen View -->
    <div class="modal" id="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <img id="modalImg" src="" alt="Project Image">
            <div class="project-name" id="modalTitle"></div>
            <div class="project-description" id="modalDescription"></div>

            <!-- Client's Name Input -->
            <input type="text" id="clientName" placeholder="Enter your name" required>

            <!-- Project Selection Dropdown -->
            <select id="projectSelect" required>
                <option value="Bil Arabi">Bil Arabi</option>
                <option value="DSA (uniform)">DSA(uniform)</option>
                <option value="Crunch Cream Envelop">Crunch Cream Envelop</option>
                <option value="DSA(Bag)">DSA (Bag)</option>
                <option value="Crunchy Cream">Crunchy Cream Bil Board</option>
                <option value="S">S</option>
                <option value="Traveluxe">Traveluxe</option>
                <option value="Book Cover">Book Cover</option>
                <option value="DSA">DSA Logo</option>
                <option value="بالعربي">بالعربي</option>
            </select>

            <!-- Ratings -->
            <div class="rating">
                <span class="star" data-rating="1" onclick="rateProject(1)">★</span>
                <span class="star" data-rating="2" onclick="rateProject(2)">★</span>
                <span class="star" data-rating="3" onclick="rateProject(3)">★</span>
                <span class="star" data-rating="4" onclick="rateProject(4)">★</span>
                <span class="star" data-rating="5" onclick="rateProject(5)">★</span>
            </div>
            <p id="ratingText">Rate this project!</p>
            <input type="hidden" id="selectedRating" name="rating" value="">
            <!-- Feedback -->
            <textarea id="feedback" placeholder="Leave your feedback..."></textarea>
            <button onclick="submitFeedback()">Submit Feedback</button>
        </div>
     </div>

    <script>
        let selectedRating = 0; // Initialize to 0

function rateProject(stars) {
    selectedRating = stars;
    document.getElementById("selectedRating").value = selectedRating; // Update hidden input

    document.getElementById("ratingText").innerText = `You rated: ${"★".repeat(stars)}`;

    // Reset all stars and apply gold color to selected ones
    let starsElements = document.querySelectorAll(".rating span.star");
    starsElements.forEach((star, index) => {
        star.classList.toggle("active", index < stars);
    });

    console.log("Selected Rating:", selectedRating); // Debugging step
}


     function submitFeedback() {
      let clientName = document.getElementById("clientName").value.trim();
      let projectName = document.getElementById("projectSelect").value;
      let feedbackText = document.getElementById("feedback").value.trim();

     // Debugging - Check Values in Console
     console.log("Client Name:", clientName);
     console.log("Project Name:", projectName);
     console.log("Rating:", selectedRating);
     console.log("Feedback:", feedbackText);

     if (!clientName) {
        alert("Please enter your name.");
        return;
     }
     if (!projectName) {
        alert("Please select a project.");
        return;
     }
     if (selectedRating === 0) {
         alert("Please provide a rating.");
        return;
     }
     if (!feedbackText) {
        alert("Please enter your feedback.");
         return;
     }
 
     let feedbackData = { 
        client_name: clientName, 
        project_name: projectName, 
        rating: selectedRating, 
        feedback: feedbackText 
     };

     fetch("submit_feedback.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(feedbackData)
     })
     .then(response => response.json())
     .then(data => {
        console.log("Server Response:", data); // Debugging
        if (data.success) {
            alert("Feedback submitted successfully!");
            closeModal();
        } else {
            alert("Error: " + data.error);
        }
     })
     .catch(error => console.error("Error submitting feedback:", error));
     }   


        function closeModal() {
            document.getElementById("modal").style.display = "none";
        }

        // Function to open project modal and view project details
        function openModal(imgSrc, title, description, projId) {
            document.getElementById("modalImg").src = imgSrc;
            document.getElementById("modalTitle").innerText = title;
            document.getElementById("modalDescription").innerText = description;
            selectedProjectId = projId;  // Store the project ID

            // Populate the project select dropdown
            const selectElement = document.getElementById("projectSelect");
            selectElement.innerHTML = `<option value="${projId}">${title}</option>`;

            selectedRating = 0;   // Reset rating when opening modal
            document.getElementById("ratingText").innerText = "Rate this project!";
            document.getElementById("feedback").value = ""; // Clear feedback
            document.getElementById("modal").style.display = "block";
        }
    </script>
    <script>
        let currentProjectIndex = 0;

        function loadProjects() {
     fetch("fetch_project.php") // Make sure this fetches data correctly
        .then(response => response.json())
        .then(data => {
            let projectList = document.getElementById("projectList");
            projectList.innerHTML = ""; 

            data.forEach((project, index) => {
                projectList.innerHTML += `
                    <div class="project-item" onclick="openModal(${project.id}, '${project.image_url}', '${project.name}', '${project.description}')">
                        <img src="${project.image_url}" alt="Project Image" class="project-img">
                        <div class="project-details">
                            <div class="project-name">${project.name}</div>
                            <div class="project-description">${project.description}</div>
                        </div>
                    </div>
                `;
            });
        });
}
function openModal(projectId) {
    fetch(`increment_views.php?id=${projectId}`, { method: "POST" });

    fetch(`fetch_project.php?id=${projectId}`)
        .then(response => response.json())
        .then(project => {
            document.getElementById("modalImg").src = project.image_url;
            document.getElementById("modalTitle").innerText = project.name;
            document.getElementById("modalDescription").innerText = project.description;

            document.getElementById("modal").style.display = "flex";
            currentProjectId = projectId;
        });
    }

  function openModal(id, imageUrl, name, description) {
    document.getElementById("modalImg").src = imageUrl;
    document.getElementById("modalTitle").innerText = name;
    document.getElementById("modalDescription").innerText = description;
    
    // Save project ID for submitting feedback
    document.getElementById("modal").dataset.projectId = id;

    // Update views in database
    updateProjectViews(id);

    document.getElementById("modal").style.display = "flex";
 }

 function closeModal() {
    document.getElementById("modal").style.display = "none";
 }

 function updateProjectViews(projectId) {
    fetch("update_views.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `project_id=${projectId}`
    });
}


 loadProjects();
    loadProjects();
</script>
<!-- Screenshot Prevention Script -->
<script>
    // Prevent right-click context menu
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        alert("Screenshots or copying are disabled on this page.");
    });

    // Prevent common keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'PrtSc SysRq') {
            alert("Screenshots are disabled.");
            e.preventDefault();
        }
        if (e.ctrlKey && (e.key === 's' || e.key === 'p' || e.key === 'S' || e.key === 'P')) {
            alert("Saving and printing are disabled.");
            e.preventDefault();
        }
    });

    // Transparent overlay to deter screen capture
    const overlay = document.createElement('div');
    overlay.style.position = 'fixed';
    overlay.style.top = '0';
    overlay.style.left = '0';
    overlay.style.width = '100%';
    overlay.style.height = '100%';
    overlay.style.backgroundColor = 'rgba(255, 255, 255, 0.01)';
    overlay.style.zIndex = '9999';
    overlay.style.pointerEvents = 'none';
    document.body.appendChild(overlay);
</script>

<script>
    window.history.forward();
    function noBack() {
        window.history.forward();
    }
    window.onload = noBack;
    window.onpageshow = function(evt) {
        if (evt.persisted) noBack();
    };
    window.onunload = function() {};
</script>
</body>

<form action="logout.php" method="post" onsubmit="return confirmLogout()">
    <button type="submit" style="background-color: #0d0235; color: white; padding: 10px; border: none; cursor: pointer;">
        Logout
    </button>
</form>

<!-- Inside your client-dashboard.html -->
<div class="top-game-button-container">
    <a href="game.html" class="game-button">Play Logo Guessing Game</a>
</div>

<script>
    function confirmLogout() {
        // Show a confirmation alert when the user tries to logout
        var confirmAction = confirm("Are you sure you want to logout?");
        
        // If the user clicks "OK" (i.e., confirms the action), return true to submit the form
        if (confirmAction) {
            return true;
        } else {
            // If the user clicks "Cancel", prevent form submission
            return false;
        }
    }
</script>



</html>
