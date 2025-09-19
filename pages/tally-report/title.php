<?php
// --- Calculate vertical center position ---
// These values are based on the content of this file.
$content_height = 74; // Total height of all Cells and Lns in this block
$page_height = $pdf->GetPageHeight();
$top_margin = $pdf->getTMargin();
$bottom_margin = $pdf->getBMargin();
$start_y = ($page_height - $content_height) / 2;
$pdf->SetY($start_y);

$pdf->SetFont('Arial', 'B', 26);
$pdf->Cell(0, 10, 'CUSTOMER SATISFACTION', 0, 1, 'C');
$pdf->Ln(2);
$pdf->Cell(0, 10, "SURVEY", 0, 1, 'C');
$pdf->Ln(15); // Add some space after the title

// --- Dynamic Quarter and Year Display ---
// These variables ($quarter, $quarter_text_for_footer, $year) are expected to be defined
// in the script that includes this file (generate-report-tally.php).

$quarter_title = '';
switch ($quarter) {
    case 1:
        $quarter_title = "1st Quarter";
        break;
    case 2:
        $quarter_title = "2nd Quarter";
        break;
    case 3:
        $quarter_title = "3rd Quarter";
        break;
    case 4:
        $quarter_title = "4th Quarter";
        break;
}

$pdf->Cell(0, 10, $quarter_title, 0, 1, 'C');
$pdf->Ln(2);
$pdf->Cell(0, 10, $quarter_text_for_footer . " " . $year, 0, 1, 'C');

$pdf->Ln(15);
$pdf->Cell(0, 10, "URS " . strtoupper($user_campus), 0, 1, 'C');
