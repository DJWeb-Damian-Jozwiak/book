<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    @vite(['resources/js/app.js'])
    @inertiaHead
    <script>
        window.initialPage = <?php echo json_encode($page); ?>;
    </script>
</head>
<body>
<div id="app" data-page='<?php echo json_encode($page); ?>'></div>
</body>
</html>