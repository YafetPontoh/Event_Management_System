</div> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        
        const sidebarWrapper = document.getElementById("wrapper");
        const menuToggle = document.getElementById("menu-toggle");

        if (menuToggle) {
            menuToggle.addEventListener("click", function(e) {
                e.preventDefault();
                
                sidebarWrapper.classList.toggle("toggled");
                
                console.log("Tombol diklik, class toggled: " + sidebarWrapper.classList.contains("toggled"));
            });
        }
    });
</script>
</body>
</html>