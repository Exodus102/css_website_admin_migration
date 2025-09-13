<?php
$unit_id = htmlspecialchars($_GET['unit_id'] ?? '');
$quarter = htmlspecialchars($_GET['quarter'] ?? '');
$year = htmlspecialchars($_GET['year'] ?? '');

// This URL points to a script that will *stream* a PDF, not return JSON.
// We need a separate script for this, or add a parameter to generate-report.php
$pdf_url = "stream-report.php?unit_id=$unit_id&quarter=$quarter&year=$year";
?>

<div class="bg-[#F1F7F9] rounded h-[80vh]">
    <object data="<?php echo $pdf_url; ?>" type="application/pdf" width="100%" height="100%">
        <p>Your browser does not support PDFs. <a href="<?php echo $pdf_url; ?>">Download the PDF</a>.</p>
    </object>
</div>