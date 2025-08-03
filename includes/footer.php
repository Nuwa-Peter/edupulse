<?php
/**
 * EduPulse - Global Footer
 *
 * This file is included at the bottom of all user-facing pages.
 * It closes the main content containers and includes global JavaScript files.
 */
?>
            </div> <!-- /#content -->
        </div> <!-- /.container-fluid -->
    </div> <!-- /#page-content-wrapper -->
</div> <!-- /#wrapper -->

<!-- Footer -->
<footer class="footer mt-auto py-3 bg-light text-center">
    <div class="container">
        <span class="text-muted">Powered by <a href="#">EduPulse</a> &copy; <?= date('Y') ?> [Your Developer Name/Organization]</span>
    </div>
</footer>

<!-- JavaScript CDNs -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- Custom JS -->
<script src="<?= BASE_URL ?>/assets/js/custom.js"></script>

<script>
// Sidebar toggle script
$("#menu-toggle").click(function(e) {
  e.preventDefault();
  $("#wrapper").toggleClass("toggled");
});
</script>

</body>
</html>
