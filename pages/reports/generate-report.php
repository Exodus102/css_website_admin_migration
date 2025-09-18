<?php
// --- Enhanced Error Reporting for Debugging ---
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set header to JSON, as this script will now respond with a status message.
header('Content-Type: application/json');

// Use absolute paths for reliability
require_once __DIR__ . '/../../fpdf186/fpdf.php';
require_once __DIR__ . '/../../function/_databaseConfig/_dbConfig.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get parameters from the URL
$unit_id = $_GET['unit_id'] ?? null;
$quarter = $_GET['quarter'] ?? null;
$year = $_GET['year'] ?? null;
$user_campus = $_SESSION['user_campus'] ?? null;

if (!$unit_id || !$quarter || !$year || !$user_campus) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters. Please ensure you are logged in and all report criteria are selected.']);
    exit;
}

// --- Main PDF Generation Logic with Error Handling ---
try {
    // --- FPDF Custom Class ---
    class PDF extends FPDF
    {
        // Page header
        function Header()
        {
            // --- Dynamic Logo and Text Centering ---
            $logoPath = '../../resources/img/urs-logo.png';
            $logoWidth = 15;
            $logoGap = 3; // Space between logo and text

            // Set font to calculate the width of the main title, which is the widest part of the text block.
            $this->SetFont('Arial', 'B', 12);
            $titleWidth = $this->GetStringWidth('University of Rizal System');

            // Calculate the starting X position to center the entire block (logo + gap + text).
            // A small negative offset is subtracted to shift the block slightly to the left.
            $blockWidth = $logoWidth + $logoGap + $titleWidth;
            $start_x = (($this->GetPageWidth() - $blockWidth) / 2) - 8;
            $this->Image($logoPath, $start_x, 8, $logoWidth);

            // --- Centered Header Text ---
            $this->SetY(10); // Move the cursor up to align text with the logo
            // Set font for the first line
            $this->SetFont('Arial', '', 10);
            // Add the cell, 0 width means it spans the page, 1 means new line after, 'C' is for center.
            $this->Cell(0, 5, 'Republic of the Philippines', 0, 1, 'C');

            // Set font for the main title
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 6, 'University of Rizal System', 0, 1, 'C');

            // Set font for the third line
            $this->SetFont('Arial', '', 10);
            $this->Cell(0, 5, 'Province of Rizal', 0, 1, 'C');

            // Draw a line below the header
            $this->Ln(5); // Add a smaller space before the line
            $this->SetLineWidth(0.5); // Make the line bold
            $y = $this->GetY();
            $this->Line($this->lMargin, $y, $this->GetPageWidth() - $this->rMargin, $y);
            $this->SetLineWidth(0.2); // Reset line width to default for other elements

            // Line break
            $this->Ln(15);
        }

        // Page footer
        function Footer()
        {
            // Position at 1.5 cm from bottom
            $this->SetY(-15);
            // Arial italic 8
            $this->SetFont('Arial', 'I', 8);
            // Page number
            $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
        }
    }

    // --- PDF Generation ---

    // Instanciation of inherited class
    $pdf = new PDF();
    $pdf->SetMargins(23, 23, 23); // Set 23mm margins (left, top, right)
    $pdf->SetAutoPageBreak(true, 23); // Set 23mm bottom margin for page breaks
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Times', '', 12);

    // Fetch office name
    $office_name = 'N/A';
    try {
        $stmt = $pdo->prepare("SELECT unit_name FROM tbl_unit WHERE id = ?");
        $stmt->execute([$unit_id]);
        $office_name = $stmt->fetchColumn();
    } catch (PDOException $e) {
        // Log the database error but don't stop the whole script
        error_log("Database error fetching office name: " . $e->getMessage());
    }

    // Determine the quarter text based on the quarter number
    $quarter_text = '';
    switch ($quarter) {
        case 1:
            $quarter_text = "January to March";
            break;
        case 2:
            $quarter_text = "April to June";
            break;
        case 3:
            $quarter_text = "July to September";
            break;
        case 4:
            $quarter_text = "October to December";
            break;
    }
    $period_display = "$quarter_text $year";

    // Display Office and Period information
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(20, 7, 'Office:', 0, 0);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 7, $office_name, 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(20, 7, '', 0, 0); // Add an empty cell for indentation
    $pdf->Cell(0, 7, $period_display, 0, 1);
    $pdf->Ln(10);

    $pdf->SetFont('Times', '', 12);
    $pdf->MultiCell(0, 10, 'This is a sample report generated with FPDF. You can add your report data, tables, and charts here. The data for this report would be fetched from the database based on the provided parameters. Jenrick The GREWAT');

    // --- Save and Output PDF ---

    // 1. Get the PDF content as a string.
    // The 'S' parameter returns the document as a string without outputting it.
    $pdfContent = $pdf->Output('S');

    // 2. Define the file path for saving.
    // Sanitize campus and unit names for the filename by replacing spaces and invalid characters with hyphens.
    $safe_campus_name = preg_replace('/[\s\/\\?%*:|"<>]+/', '-', $user_campus);
    $safe_unit_name = preg_replace('/[\s\/\\?%*:|"<>]+/', '-', $office_name);

    // Construct the new, more descriptive filename.
    $filename = "report_{$safe_campus_name}_{$safe_unit_name}_{$year}_q{$quarter}.pdf";
    $savePath = __DIR__ . '/../../upload/pdf/' . $filename;

    // 3. Ensure the destination directory exists.
    $directory = dirname($savePath);
    if (!is_dir($directory)) {
        // Create the directory recursively with safe permissions (0755).
        if (!mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new Exception('Failed to create PDF storage directory. Please check server permissions for the "upload" folder.');
        }
    }

    // 4. Save the PDF content to the file on the server.
    if (file_put_contents($savePath, $pdfContent) === false) {
        throw new Exception('Failed to save the PDF file. Please check server permissions for the "upload/pdf" folder.');
    }

    // 5. Insert the file path into the database.
    // We'll store the relative path from the project root for better portability.
    $relativePath = 'upload/pdf/' . $filename;
    try {
        $stmt = $pdo->prepare("INSERT INTO tbl_report (file_path) VALUES (?)");
        $stmt->execute([$relativePath]);
    } catch (PDOException $e) {
        // Log this DB error, but don't fail the whole operation since the PDF was created.
        error_log("Database error inserting report path: " . $e->getMessage());
    }

    // 6. If we reach here, it was successful. Send a success response.
    echo json_encode([
        'success' => true,
        'message' => 'PDF report created successfully!',
        'filePath' => $relativePath // Return the path to the generated file
    ]);
} catch (Exception $e) {
    // If any error occurs, catch it and send a JSON error response.
    $errorMessage = "An error occurred while generating the PDF: " . $e->getMessage();

    // Log the detailed error to the server's error log for the developer
    error_log("PDF Generation Failed: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());

    // Send a user-friendly error message back to the JavaScript
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $errorMessage]);
}
