<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('format_tanggal_indonesia')) {
    function format_tanggal_indonesia($tanggal, $format = 'd/m/Y') {
        if (empty($tanggal) || $tanggal == '0000-00-00') {
            return '-';
        }
        
        $date = new DateTime($tanggal);
        return $date->format($format);
    }
}

if (!function_exists('format_tanggal_database')) {
    function format_tanggal_database($tanggal) {
        if (empty($tanggal)) {
            return null;
        }
        
        // Convert from DD/MM/YYYY to Y-m-d
        $parts = explode('/', $tanggal);
        if (count($parts) == 3) {
            return $parts[2] . '-' . str_pad($parts[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($parts[0], 2, '0', STR_PAD_LEFT);
        }
        
        return $tanggal;
    }
}

if (!function_exists('hitung_usia')) {
    function hitung_usia($tanggal_lahir) {
        if (empty($tanggal_lahir) || $tanggal_lahir == '0000-00-00') {
            return 0;
        }
        
        $lahir = new DateTime($tanggal_lahir);
        $sekarang = new DateTime();
        $usia = $sekarang->diff($lahir);
        
        return $usia->y;
    }
}

if (!function_exists('hitung_sisa_hari')) {
    function hitung_sisa_hari($tanggal_selesai) {
        if (empty($tanggal_selesai) || $tanggal_selesai == '0000-00-00') {
            return 0;
        }
        
        $selesai = new DateTime($tanggal_selesai);
        $sekarang = new DateTime();
        $selisih = $selesai->diff($sekarang);
        
        if ($selesai < $sekarang) {
            return -$selisih->days; // Minus jika sudah lewat
        }
        
        return $selisih->days;
    }
}

if (!function_exists('status_sisa_hari')) {
    function status_sisa_hari($sisa_hari) {
        if ($sisa_hari < 0) {
            return 'Terlambat';
        } elseif ($sisa_hari < 3) {
            return 'Kritis';
        } elseif ($sisa_hari <= 7) {
            return 'Perhatian';
        } else {
            return 'Normal';
        }
    }
}

if (!function_calls('warna_sisa_hari')) {
    function warna_sisa_hari($sisa_hari) {
        if ($sisa_hari < 0) {
            return 'danger';
        } elseif ($sisa_hari < 3) {
            return 'danger';
        } elseif ($sisa_hari <= 7) {
            return 'warning';
        } else {
            return 'success';
        }
    }
}

if (!function_exists('rentang_tanggal')) {
    function rentang_tanggal($dari, $sampai) {
        $dari_format = format_tanggal_indonesia($dari);
        $sampai_format = format_tanggal_indonesia($sampai);
        
        if ($dari_format == $sampai_format) {
            return $dari_format;
        }
        
        return $dari_format . ' s/d ' . $sampai_format;
    }
}