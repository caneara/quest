<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Enable/Disable Match Only
    |--------------------------------------------------------------------------
    | If searching large tables to only confirm whether matches exist, removing
    | sorting and relevance checking will significantly increase query performance.
    | Here you may specify whether you want to sort by relevance and return the
    | matches or just return the relevance data to be handled.
    |
    | Set sort-and-return-matches => false to remove sorting and relevance checking
    |
    | To adjust the relevance threshold you can filter the relevance data Manually
    */

    'sort-and-return-matches' => true,
];
