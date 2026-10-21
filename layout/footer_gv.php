<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const sidebar = document.querySelector('.sidebar');
const toggleBtn = document.getElementById('toggleSidebar');
if (sidebar && toggleBtn) {
  toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
  });
}
</script>
</body>
</html>
