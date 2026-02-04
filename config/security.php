<?php

return [
    // Fail-closed enforcement for permission middleware.
    // Set to false only in controlled debugging environments.
    'fail_closed' => (bool) env('SECURITY_FAIL_CLOSED', true),
];
