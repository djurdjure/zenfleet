#!/bin/bash
# Script to fix Alpine.js syntax errors in driver forms

cd /home/lynx/projects/zenfleet/resources/views/admin/drivers

# Backup files
cp edit.blade.php edit.blade.php.backup
cp create.blade.php create.blade.php.backup

echo "Fixing edit.blade.php..."
# Fix currentStep initialization in edit.blade.php (lines 559-563)
sed -i '559,563d' edit.blade.php
sed -i '558a\            currentStep: {{ old('\''current_step'\'', 1) }},' edit.blade.php

# Fix @if directive in edit.blade.php (line 600)
sed -i 's/@if($errors - > any())/@if($errors->any())/g' edit.blade.php
sed -i 's/@json($errors - > messages())/@json($errors->messages())/g' edit.blade.php

echo "Fixing create.blade.php..."
# Fix currentStep initialization in create.blade.php (lines 527-531)
sed -i '527,531d' create.blade.php
sed -i '526a\            currentStep: {{ old('\''current_step'\'', 1) }},' create.blade.php

# Fix @if directive in create.blade.php (line 566)
sed -i 's/@if($errors - > any())/@if($errors->any())/g' create.blade.php
sed -i 's/@json($errors - > messages())/@json($errors->messages())/g' create.blade.php

echo "Done! Files fixed."
echo "Backups created: edit.blade.php.backup and create.blade.php.backup"
