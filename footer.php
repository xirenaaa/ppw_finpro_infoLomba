</div>

    <!-- Footer -->
    <footer class="bg-dark text-light text-center py-3 mt-5">
        <div class="container">
            <p class="mb-0">&copy; 2024 Manajemen Lomba. Dibuat dengan <i class="bi bi-heart-fill text-danger"></i> menggunakan PHP & Bootstrap</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Konfirmasi hapus
        function confirmDelete(id, nama) {
            if (confirm(`Apakah Anda yakin ingin menghapus lomba "${nama}"?`)) {
                window.location.href = `delete.php?id=${id}`;
            }
        }

        // Auto hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                if (bsAlert) {
                    bsAlert.close();
                }
            });
        }, 5000);
    </script>
</body>
</html>