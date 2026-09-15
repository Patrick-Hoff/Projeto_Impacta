</main>
</div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Tema claro/escuro -->
<script src="../js/theme.js"></script>

<?php if (!empty($pageScript)): ?>
    <script>
        <?= $pageScript ?>
    </script>
<?php endif; ?>

<script>
    document.querySelectorAll('.alert-dismissible').forEach(function(el) {
        setTimeout(function() {
            bootstrap.Alert.getOrCreateInstance(el).close();
        }, 4000);
    });
</script>

</body>

</html>