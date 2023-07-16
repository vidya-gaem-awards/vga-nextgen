<?php

return [
    'proxies' => explode(',', env('APP_TRUSTED_PROXIES', ''))
];
