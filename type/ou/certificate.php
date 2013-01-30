<?php

// This file is part of the Certificate module for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * OU statement of participation type
 *
 * @package    mod_certificate
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from view.php
}

if ($certificate->printhours) {
    $credithours =  get_string('credithours', 'certificate') . ': ' . $certificate->printhours;
} else {
    $credithours = '';
}

$pdf = new TCPDF($certificate->orientation, 'mm', 'A4', true, 'UTF-8', false);

$pdf->SetTitle($certificate->name);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(false, 0);
$pdf->AddPage();

// Define layout.
if ($certificate->orientation == 'L') { // Landscape.
    throw new coding_exception('Landscape certificates are not supported.');
}

$sigx = 440;
$sigy = 50;
$wmarkx = 10;
$wmarky = 10;
$wmarkw = 190;
$wmarkh = 277;
$brdrx = 0;
$brdry = 0;
$brdrw = 210;
$brdrh = 297;

$codex = 17;
$codey = 17;
$codew = 45;
$codeh = 12;

$seal            =  25;
$courseshortname =  68;
$coursefullname  =  76;
$title           =  93;
$username        = 145;
$date            = 160;
$customtext      = 195;
$disclaimer      = 250;
$code            = 268;

$generalx    = 20;
$titlex      = 50;
$customtextx = 30;
$disclaimerx = 50;

$sealwidth   = 40;

$borders = array(
    10   => 1.2,
    16.7 => 0.7,
    18   => 0.7,
);

make_cache_directory('tcpdf');

// Background image
certificate_print_image($pdf, $certificate, CERT_IMAGE_WATERMARK, $wmarkx, $wmarky, $wmarkw, $wmarkh);

// Borders
foreach ($borders as $pos => $width) {
    $pdf->SetLineStyle(array('width' => $width, 'color' => array(130, 130, 130)));
    $pdf->Rect($pos + $width / 2, $pos + $width / 2, 210 - 2*$pos - $width, 297 - 2*$pos - $width);
}

// OU logo.
certificate_print_image($pdf, $certificate, CERT_IMAGE_SEAL, 105 - $sealwidth / 2, $seal, $sealwidth, '');
certificate_print_image($pdf, $certificate, CERT_IMAGE_SIGNATURE, $sigx, $sigy, '', '');

// Add text.
$pdf->SetTextColor(0, 0, 0);
certificate_ou_print_text($pdf, $generalx,    $courseshortname, 'C', 'Helvetica', '',  18, $COURSE->shortname);
certificate_ou_print_text($pdf, $generalx,    $coursefullname,  'C', 'Helvetica', '',  18, $COURSE->fullname);
certificate_ou_print_text($pdf, $titlex,      $title,           'C', 'Helvetica', '',  36, get_string('statementofparticipation', 'certificate'));
certificate_ou_print_text($pdf, $generalx,    $username,        'C', 'Helvetica', '',  24, fullname($USER));
certificate_ou_print_text($pdf, $generalx,    $date,            'C', 'Helvetica', '',  18, certificate_get_date($certificate, $certrecord, $course));
certificate_ou_print_text($pdf, $customtextx, $customtext,      'C', 'Helvetica', '',  16, $certificate->customtext);
certificate_ou_print_text($pdf, $disclaimerx, $disclaimer,      'C', 'Helvetica', '',  12, get_string('disclaimer', 'certificate'));
if ($certificate->printnumber) {
    certificate_ou_print_text($pdf, $generalx, $code,           'C', 'Helvetica', '',  12, certificate_get_code($certificate, $certrecord));
}

function certificate_ou_print_text($pdf, $x, $y, $align, $font='Helvetica', $style = '', $size = 32, $text) {
    $pdf->setFont($font, $style, $size);
    $pdf->writeHTMLCell(210 - 2*$x, 0, $x, $y, $text, 0, 0, 0, true, $align);
}
