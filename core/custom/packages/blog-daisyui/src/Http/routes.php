<?php

use EvolutionCMS\BlogDaisyui\Controllers\ContactRequestController;
use Illuminate\Support\Facades\Route;

Route::post('contact-submit', [ContactRequestController::class, 'submit'])->name('blog-daisyui.contact.submit');
