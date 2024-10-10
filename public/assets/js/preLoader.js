document.addEventListener("DOMContentLoaded", function () {
    hideLoader(); // Ensure loader is hidden on page load
});

function showLoader() {
    document.getElementById("loader").style.display = "flex"; // Show the loader
}

function hideLoader() {
    document.getElementById("loader").style.display = "none"; // Hide the loader
}
