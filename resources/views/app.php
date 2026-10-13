<!DOCTYPE html>
<html lang="<?php echo str_replace('_', '-', app()->getLocale()); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia><?php echo config('app.name', 'Laravel'); ?></title>

    <?php echo app('Illuminate\\Foundation\\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <?php
        // If the Inertia head data is present, output it.
        // This keeps the view framework-agnostic (no Blade directives).
        if (isset($page) && is_array($page) && isset($page['head']) && is_array($page['head'])) {
            echo implode("\n", $page['head']);
        }
    ?>
</head>
<body class="font-sans antialiased">
    <div id="app" data-page="<?php echo htmlspecialchars(json_encode($page ?? []), ENT_QUOTES, 'UTF-8'); ?>"></div>
</body>
</html>
