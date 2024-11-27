<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    @vite(['resources/js/app.js'])
    @inertiaHead
    <script>
        window.initialPage = <?php echo json_encode($page, JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div id="app" data-page='<?php echo json_encode($page,  JSON_HEX_APOS | JSON_HEX_QUOT); ?>'></div>
</body>
</html>