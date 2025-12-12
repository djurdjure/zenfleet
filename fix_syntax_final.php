<?php

$files = [
    'resources/views/admin/drivers/create.blade.php',
    'resources/views/admin/drivers/edit.blade.php'
];

foreach ($files as $path) {
    if (!file_exists($path)) {
        echo "File not found: $path\n";
        continue;
    }

    $content = file_get_contents($path);
    $original = $content;

    // Fix 1: Remove spaces around arrow operator ' - >'
    $content = str_replace(' - >', '->', $content);
    // Fix 1b: Remove space AFTER arrow operator '-> ' 
    $content = str_replace('-> ', '->', $content);

    // Fix 2: Flatten multi-line currentStep initialization
    // Pattern matches:
    // currentStep: {
    //    {
    //        old('current_step', 1)
    //    }
    // }
    // And replaces with: currentStep: {{ old('current_step', 1) }}
    $pattern = '/currentStep:\s*\{\s*\{\s*old\(\'current_step\',\s*1\)\s*\}\s*\}/s';
    $replacement = "currentStep: {{ old('current_step', 1) }}";

    $content = preg_replace($pattern, $replacement, $content);

    if ($content !== $original) {
        file_put_contents($path, $content);
        echo "Fixed bugs in $path\n";
    } else {
        echo "No changes needed for $path\n";
    }
}
