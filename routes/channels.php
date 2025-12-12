<?php

use Illuminate\Support\Facades\Broadcast;

// Saluran untuk Pasien
Broadcast::channel('pasien.{id}', function ($user, $id) {
    // Pastikan yang akses adalah user (guard web) dan ID-nya cocok
    return auth('web')->check() && (int) $user->id === (int) $id;
});

// Saluran untuk Tenaga Medis
Broadcast::channel('tenagamedis.{id}', function ($user, $id) {
    // Pastikan yang akses adalah tenaga medis dan ID-nya cocok
    return auth('tenaga_medis')->check() && (int) $user->id === (int) $id;
});