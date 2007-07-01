<?php
include '../../tcpdf/tcpdfprotection.php';
require_once('../../tcpdf/config/lang/eng.php');
include '../../tcpdf/tcpdf.php';

// Load certificate info
$certificateid = $certificate->id;
$certrecord = certificate_get_issue($course, $USER, $certificateid);
$strreviewcertificate = get_string('reviewcertificate', 'certificate');
$strgetcertificate = get_string('getcertificate', 'certificate');
$strgrade = get_string('grade', 'certificate');
$strcoursegrade = get_string('coursegrade', 'certificate');
$strcredithours = get_string('credithours', 'certificate');

// Date formatting - can be customized if necessary
$certificatedate = '';
if ($certrecord) {
$certdate = $certrecord->certdate;
}else $certdate = certificate_generate_date($certificate, $course);
if($certificate->printdate > 0)    {
    if ($certificate->datefmt == 1)    {
    $certificatedate = str_replace(' 0', '', strftime('%B  %d, %Y', $certdate));
}   if ($certificate->datefmt == 2) {
    $certificatedate = date('F jS, Y', $certdate);
}   if ($certificate->datefmt == 3) {
    $certificatedate = str_replace(' 0', '', strftime(' %d %B %Y', $certdate));
}   if ($certificate->datefmt == 4) {
    $certificatedate = strftime('%B %Y', $certdate);
    }
}

//Grade formatting - can be customized if necessary
$grade = '';
//Print the course grade
$coursegrade = get_course_grade($course->id);    
    if($certificate->printgrade > 0) {
    if($certificate->printgrade == 1) {
    if($certificate->gradefmt == 1) {
    $grade = $strcoursegrade.':  '.$coursegrade->percentage.'%';
}   if($certificate->gradefmt == 2) {
    $grade = $strcoursegrade.':  '.$coursegrade->points;
}   if($certificate->gradefmt == 3) {
    $clg = $coursegrade->percentage;
    if ($clg <= 100.99){
    $grade = $strcoursegrade.':  '.'A';
}   if ($clg <= 92.99){
    $grade = $strcoursegrade.':  '.'A-'; 
}   if ($clg <=89.99){
    $grade = $strcoursegrade.':  '.'B+';
}   if ($clg <= 82.99){
    $grade = $strcoursegrade.':  '.'B-'; 
}   if ($clg <= 86.99){
    $grade = $strcoursegrade.':  '.'B';
}   if ($clg <= 79.99){
    $grade = $strcoursegrade.':  '.'C+'; 
}   if ($clg <= 76.99){
    $grade = $strcoursegrade.':  '.'C'; 
}   if ($clg <= 72.99){
    $grade = $strcoursegrade.':  '.'C-'; 
}   if ($clg <= 69.99){
    $grade = $strcoursegrade.':  '.'D+'; 
}   if ($clg <= 66.99){
    $grade = $strcoursegrade.':  '.'D'; 
}   if ($clg <= 59.99){
    $grade = $strcoursegrade.':  '.'F';
    }
  }
} else {
//Print the mod grade
$modinfo = certificate_mod_grade($course, $certificate->printgrade);
    if($certificate->printgrade > 1) {
    if ($certificate->gradefmt == 1) {
    $grade = $modinfo->name.' '.$strgrade.': '.$modinfo->percentage.'%';
}
    if ($certificate->gradefmt == 2) {          
    $grade = $modinfo->name.' '.$strgrade.': '.$modinfo->points;
}
    if($certificate->gradefmt == 3) {
    $mlg = $modinfo->percentage;
    if ($mlg <= 100.99){
    $grade = $modinfo->name.' '.$strgrade.': '.'A';
}   if ($mlg <= 92.99){
    $grade = $modinfo->name.' '.$strgrade.': '.'A-'; 
}   if ($mlg <=89.99){
    $grade = $modinfo->name.' '.$strgrade.': '.'B+';
}   if ($mlg <= 82.99){
    $grade = $modinfo->name.' '.$strgrade.': '.'B-'; 
}   if ($mlg <= 86.99){
    $grade = $modinfo->name.' '.$strgrade.': '.'B';
}   if ($mlg <= 79.99){
    $grade = $modinfo->name.' '.$strgrade.': '.'C+'; 
}   if ($mlg <= 76.99){
    $grade = $modinfo->name.' '.$strgrade.': '.'C'; 
}   if ($mlg <= 72.99){
    $grade = $modinfo->name.' '.$strgrade.': '.'C-'; 
}   if ($mlg <= 69.99){
    $grade = $modinfo->name.' '.$strgrade.': '.'D+'; 
}   if ($mlg <= 66.99){
    $grade = $modinfo->name.' '.$strgrade.': '.'D'; 
}   if ($mlg <= 59.99){
    $grade = $modinfo->name.' '.$strgrade.': '.'F';
    }
  }
}
}
}

// Print the code number
$code = '';
if($certificate->printnumber) {
if ($certrecord) {
$code = $certrecord->code;
}
}

//Print the student name
$studentname = '';
if ($certrecord) {
$studentname = $certrecord->studentname;
}
//Print the credit hours
if($certificate->printhours) {
$credithours =  $strcredithours.': '.$certificate->printhours;
} else $credithours = '';

//Print the html text
$customtext = $certificate->customtext;

//Create new PDF document 

    $pdf = new TCPDF_Protection('P', 'pt', 'A4', true); 
    $pdf->SetProtection(array('print'));
    $pdf->print_header = false;
    $pdf->print_footer = false;
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setLanguageArray($l); //set language items
    $pdf->AddPage();
    	if(ini_get('magic_quotes_gpc')=='1')
		$customtext=stripslashes($customtext);
	
// Add images and lines
    $orientation = "P";
    print_border($certificate->borderstyle, $orientation);
	draw_frame($certificate->bordercolor, $orientation);
    print_watermark($certificate->printwmark, $orientation);
    print_seal($certificate->printseal, $orientation, 440, 590, '', '');
    print_signature($certificate->printsignature, $orientation, 85, 530, '', '');
	
// Add text
    $pdf->SetTextColor(0,0,128);
    cert_printtext(48, 170, 'C', 'FreeSerif', 'B', 26, get_string("titleportrait", "certificate"));
    $pdf->SetTextColor(0,0,0);
    cert_printtext(45, 230, 'C', 'FreeSerif', 'B', 20, get_string("introportrait", "certificate"));
    cert_printtext(45, 280, 'C', 'FreeSerif', '', 30, $studentname);
    cert_printtext(45, 330, 'C', 'FreeSerif', '', 20, get_string("statementportrait", "certificate"));
    cert_printtext(45, 380, 'C', 'FreeSerif', '', 20, $course->fullname);
    cert_printtext(45, 420, 'C', 'FreeSerif', '', 20, get_string("ondayportrait", "certificate"));
    cert_printtext(45, 460, 'C', 'FreeSerif', '', 14, $certificatedate);
    cert_printtext(45, 540, 'C', 'FreeSerif', '', 10, $grade);
    cert_printtext(45, 552, 'C', 'FreeSerif', '', 10, $credithours);
    cert_printtext(45, 720, 'C', 'FreeSerif', '', 10, $code);
    $i = 0 ;
	if($certificate->printteacher){
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    if ($teachers = get_users_by_capability($context, 'mod/certificate:printteacher')) {
		foreach ($teachers as $teacher) {
			$i++;
			if($i <5) {
				$teachernames = fullname($teacher);
	cert_printtext(85, 590+($i *12) , 'L', 'Times', '', 12, $teachernames);
}}}}
    cert_printtext(58, 600, '', '', '', '12', '');
	$pdf->SetLeftMargin(85);
	$pdf->WriteHTML($customtext);
?>