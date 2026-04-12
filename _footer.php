    <footer>
        <p>&copy; 2026 POP Mart Assignment</p>
    </footer>
    <script src="/JS/base.js"></script>
    <?php if(isset($_jsFileName) && !empty($_jsFileName)):?>
        <script src="/JS/<?= $_jsFileName ?>.js"></script>
    <?php endif ?>
    </body>

    </html>