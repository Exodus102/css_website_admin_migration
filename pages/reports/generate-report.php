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
            // Logo
            // $this->Image('logo.png',10,6,30);
            // Arial bold 15
            $this->SetFont('Arial', 'B', 15);
            // Move to the right
            $this->Cell(80);
            // Title
            $this->Cell(30, 10, 'CSS Report', 1, 0, 'C');
            // Line break
            $this->Ln(20);
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

    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Report for: ' . $office_name, 0, 1);
    $pdf->Cell(0, 10, "Campus: $user_campus", 0, 1);
    $pdf->Cell(0, 10, "Period: $year - Quarter $quarter", 0, 1);
    $pdf->Ln(10);

    $pdf->SetFont('Times', '', 12);
    $pdf->MultiCell(0, 10, 'This is a sample report generated with FPDF. You can add your report data, tables, and charts here. The data for this report would be fetched from the database based on the provided parameters.');

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

    // 5. If we reach here, it was successful. Send a success response.
    echo json_encode(['success' => true, 'message' => 'PDF report created successfully!']);
} catch (Exception $e) {
    // If any error occurs, catch it and send a JSON error response.
    $errorMessage = "An error occurred while generating the PDF: " . $e->getMessage();

    // Log the detailed error to the server's error log for the developer
    error_log("PDF Generation Failed: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());

    // Send a user-friendly error message back to the JavaScript
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $errorMessage]);
}
