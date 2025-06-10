<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('init_pdf')) {
    function init_pdf($orientation = 'P', $title = 'Laporan') {
        require_once APPPATH . 'third_party/tcpdf/tcpdf.php';
        
        $pdf = new TCPDF($orientation, PDF_UNIT, 'A4', true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('Salon Rainbrow');
        $pdf->SetAuthor('Salon Rainbrow');
        $pdf->SetTitle($title);
        $pdf->SetSubject($title);
        
        // Set margins
        $pdf->SetMargins(15, 20, 15);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(15);
        
        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 20);
        
        // Set font
        $pdf->SetFont('arial', '', 10);
        
        return $pdf;
    }
}

if (!function_exists('pdf_header')) {
    function pdf_header($pdf, $title) {
        $pdf->AddPage();
        
        // Logo (jika ada)
        // $pdf->Image('assets/img/logo.png', 15, 15, 30, 0, 'PNG');
        
        // Header text
        $pdf->SetFont('arial', 'B', 16);
        $pdf->Cell(0, 10, 'SALON RAINBROW', 0, 1, 'C');
        
        $pdf->SetFont('arial', '', 12);
        $pdf->Cell(0, 8, 'Sistem Rekrutmen Karyawan', 0, 1, 'C');
        
        $pdf->Ln(5);
        
        // Title
        $pdf->SetFont('arial', 'B', 14);
        $pdf->Cell(0, 10, strtoupper($title), 0, 1, 'C');
        
        $pdf->Ln(5);
        
        return $pdf;
    }
}

if (!function_exists('pdf_footer_info')) {
    function pdf_footer_info($pdf, $filter_info = '') {
        $pdf->SetFont('arial', '', 9);
        
        if (!empty($filter_info)) {
            $pdf->Cell(0, 6, $filter_info, 0, 1, 'L');
        }
        
        $pdf->Cell(0, 6, 'Dicetak pada: ' . format_tanggal_indonesia(date('Y-m-d'), 'd/m/Y H:i'), 0, 1, 'L');
        
        $pdf->Ln(5);
        
        return $pdf;
    }
}

if (!function_exists('create_table_header')) {
    function create_table_header($pdf, $headers, $widths) {
        $pdf->SetFillColor(230, 230, 230);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.3);
        $pdf->SetFont('arial', 'B', 9);
        
        for ($i = 0; $i < count($headers); $i++) {
            $pdf->Cell($widths[$i], 8, $headers[$i], 1, 0, 'C', true);
        }
        $pdf->Ln();
        
        // Reset font for data
        $pdf->SetFont('arial', '', 8);
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        
        return $pdf;
    }
}

if (!function_exists('add_table_row')) {
    function add_table_row($pdf, $data, $widths, $fill = false) {
        $height = 6;
        
        // Calculate the height needed for this row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++) {
            $nb = max($nb, pdf_nb_lines($pdf, $widths[$i], $data[$i]));
        }
        $h = $height * $nb;
        
        // Check if we need a new page
        if ($pdf->GetY() + $h > $pdf->getPageHeight() - $pdf->getBreakMargin()) {
            $pdf->AddPage();
        }
        
        // Draw the cells
        for ($i = 0; $i < count($data); $i++) {
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            
            $pdf->Rect($x, $y, $widths[$i], $h);
            $pdf->MultiCell($widths[$i], $height, $data[$i], 0, 'L', $fill);
            
            $pdf->SetXY($x + $widths[$i], $y);
        }
        
        $pdf->Ln($h);
        
        return $pdf;
    }
}

if (!function_exists('pdf_nb_lines')) {
    function pdf_nb_lines($pdf, $w, $txt) {
        $cw = $pdf->getCurrentFont()['cw'];
        if ($w == 0) $w = $pdf->w - $pdf->rMargin - $pdf->x;
        $wmax = ($w - 2 * $pdf->cMargin) * 1000 / $pdf->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb-1] == "\n") $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') $sep = $i;
            $l += isset($cw[$c]) ? $cw[$c] : 600;
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) $i++;
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }
}