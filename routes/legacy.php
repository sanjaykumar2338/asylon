<?php

use Illuminate\Support\Facades\Route;

// Legacy redirects for updated paths.
Route::redirect('/reviews', '/reports/all')->middleware(['auth', 'verified']);
