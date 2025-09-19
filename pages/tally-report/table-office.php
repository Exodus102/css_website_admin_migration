<?php

// This file is included from generate-report-tally.php and has access to:
// $pdf, $pdo, $year, $quarter, and $user_campus.

try {
    // --- 1. Fetch all offices for the current campus ---
    $stmt_offices = $pdo->prepare("SELECT id, unit_name FROM tbl_unit WHERE campus_name = ? ORDER BY unit_name ASC");
    $stmt_offices->execute([$user_campus]);
    $all_offices = $stmt_offices->fetchAll(PDO::FETCH_ASSOC);

    // --- 2. Fetch all active questions once to avoid re-querying in the loop ---
    $stmt_questions = $pdo->prepare("
        SELECT question_id, question, question_type, question_rendering, header 
        FROM tbl_questionaire 
        WHERE status = 1 
        AND (question_rendering IN ('QoS', 'Su') OR header = 1)
        ORDER BY 
            CASE 
                WHEN question_rendering = 'QoS' THEN 1
                WHEN question_rendering = 'Su' THEN 2
                ELSE 3
            END, 
            question_id ASC
    ");
    $stmt_questions->execute();
    $all_questions = $stmt_questions->fetchAll(PDO::FETCH_ASSOC);

    // --- 3. Define Helper functions if they don't exist ---
    if (!function_exists('getVerbalInterpretation')) {
        function getVerbalInterpretation($mean)
        {
            if ($mean >= 4.50) return 'E';
            if ($mean >= 3.50) return 'VS';
            if ($mean >= 2.50) return 'S';
            if ($mean >= 1.50) return 'US';
            if ($mean >= 1.00) return 'P/NI';
            return '';
        }
    }

    if (!function_exists('drawSummaryRow')) {
        function drawSummaryRow($pdf, $label, $average, $col1_width, $col2_width, $col3_width, $col4_width)
        {
            $vi = getVerbalInterpretation($average);
            $pdf->SetFont('Arial', 'B', 11);
            if ($pdf->GetY() + 6 > $pdf->getPageBreakTrigger()) {
                $pdf->AddPage($pdf->getCurOrientation());
            }
            $pdf->Cell($col1_width, 6, '', 1, 0, 'C');
            $pdf->Cell($col2_width, 6, $label, 1, 0, 'C');
            $pdf->Cell($col3_width, 6, number_format($average, 2), 1, 0, 'C');
            $pdf->Cell($col4_width, 6, $vi, 1, 1, 'C');
            $pdf->SetFont('Arial', '', 11);
        }
    }

    // --- Define the period display string ---
    $period_display = "$quarter_text_for_footer $year";

    // --- 4. Loop through each office and generate its table ---
    foreach ($all_offices as $office) {
        $pdf->AddPage();
        $office_name = $office['unit_name'];

        // --- Display Office Header ---
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(20, 7, 'Office:', 0, 0);
        $pdf->SetFont('Arial', 'BU', 12);
        $pdf->Cell(0, 7, $office_name, 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(20, 7, '', 0, 0); // Add an empty cell for indentation
        $pdf->Cell(0, 7, $period_display, 0, 1);
        $pdf->Ln(10);

        // --- Fetch means for this specific office ---
        $sql_means = "
            SELECT r.question_id, AVG(CAST(r.response AS DECIMAL(10,2))) AS mean_value
            FROM tbl_responses r
            WHERE r.response_id IN (
                SELECT response_id FROM tbl_responses WHERE question_id = -3 AND response = :office_name_param
            ) AND r.response_id IN (
                SELECT response_id FROM tbl_responses WHERE question_id = -1 AND response = :campus_name_param
            )
            AND r.question_rendering IN ('QoS', 'Su')
            AND r.response REGEXP '^[0-9\.]+$'
            AND YEAR(r.timestamp) = :year AND QUARTER(r.timestamp) = :quarter
            GROUP BY r.question_id
        ";
        $stmt_means = $pdo->prepare($sql_means);
        $stmt_means->execute([
            ':office_name_param' => $office_name,
            ':campus_name_param' => $user_campus,
            ':year' => $year,
            ':quarter' => $quarter
        ]);
        $means = $stmt_means->fetchAll(PDO::FETCH_KEY_PAIR);

        // --- Fetch comments for this specific office ---
        $sql_comments = "
            SELECT DISTINCT r.comment
            FROM tbl_responses r
            JOIN
                (SELECT response_id FROM tbl_responses WHERE question_id = -3 AND response = :office_name_param) AS office_responses ON r.response_id = office_responses.response_id
            JOIN
                (SELECT response_id FROM tbl_responses WHERE question_id = -1 AND response = :office_campus_param) AS campus_responses ON r.response_id = campus_responses.response_id
            WHERE
                YEAR(r.timestamp) = :year AND QUARTER(r.timestamp) = :quarter
                AND r.comment IS NOT NULL AND r.comment != ''
        ";
        $stmt_comments = $pdo->prepare($sql_comments);
        $stmt_comments->execute([
            ':office_name_param' => $office_name,
            ':office_campus_param' => $user_campus,
            ':year' => $year,
            ':quarter' => $quarter
        ]);
        $comments = $stmt_comments->fetchAll(PDO::FETCH_COLUMN);

        // --- Draw Table Header ---
        $pdf->SetFont('Arial', 'B', 11);
        $col1_width = 10;
        $col3_width = 20;
        $col4_width = 20;
        $col2_width = 164 - $col1_width - $col3_width - $col4_width; // 114mm
        $pdf->Cell($col1_width, 6, '', 1, 0, 'C');
        $pdf->Cell($col2_width, 6, 'Question', 1, 0, 'C');
        $pdf->Cell($col3_width, 6, 'Mean', 1, 0, 'C');
        $pdf->Cell($col4_width, 6, 'VI', 1, 1, 'C');

        // --- Draw Table Body ---
        $pdf->SetFont('Arial', '', 11);
        $qos_means = [];
        $su_means = [];
        $current_rendering_group = null;

        foreach ($all_questions as $question) {
            $question_rendering = $question['question_rendering'];

            if ($current_rendering_group === 'QoS' && $question_rendering !== 'QoS') {
                $qos_average = !empty($qos_means) ? array_sum($qos_means) / count($qos_means) : 0;
                drawSummaryRow($pdf, 'Average for QoS', $qos_average, $col1_width, $col2_width, $col3_width, $col4_width);
            }
            $current_rendering_group = $question_rendering;

            $question_type = $question['question_type'];
            $question_text = $question['question'];
            $question_id = $question['question_id'];
            $mean_value = $means[$question_id] ?? 0;

            $is_computable = !in_array($question_type, ['Text', 'Description']);

            if ($is_computable && $mean_value > 0) {
                if ($question_rendering === 'QoS') $qos_means[] = $mean_value;
                if ($question_rendering === 'Su') $su_means[] = $mean_value;
            }

            $display_mean = $is_computable ? number_format($mean_value, 2) : '';
            $verbal_interpretation = $is_computable ? getVerbalInterpretation($mean_value) : '';

            $line_count = $pdf->NbLines($col2_width, $question_text);
            $row_height = 6 * $line_count;

            if ($pdf->GetY() + $row_height > $pdf->getPageBreakTrigger()) {
                $pdf->AddPage($pdf->getCurOrientation());
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->Cell($col1_width, 6, '', 1, 0, 'C');
                $pdf->Cell($col2_width, 6, 'Question', 1, 0, 'C');
                $pdf->Cell($col3_width, 6, 'Mean', 1, 0, 'C');
                $pdf->Cell($col4_width, 6, 'VI', 1, 1, 'C');
                $pdf->SetFont('Arial', '', 11);
            }

            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->Cell($col1_width, $row_height, '', 1, 0, 'C');
            $pdf->MultiCell($col2_width, 6, $question_text, 1, 'L');
            $pdf->SetXY($x + $col1_width + $col2_width, $y);
            $pdf->Cell($col3_width, $row_height, $display_mean, 1, 0, 'C');
            $pdf->Cell($col4_width, $row_height, $verbal_interpretation, 1, 1, 'C');
        }

        // --- Draw Final Summary Rows for the office ---
        if ($current_rendering_group === 'Su') {
            $su_average = !empty($su_means) ? array_sum($su_means) / count($su_means) : 0;
            drawSummaryRow($pdf, 'Average for Su', $su_average, $col1_width, $col2_width, $col3_width, $col4_width);
        }

        $all_means = array_merge($qos_means, $su_means);
        $grand_mean = !empty($all_means) ? array_sum($all_means) / count($all_means) : 0;
        drawSummaryRow($pdf, 'Grand Mean', $grand_mean, $col1_width, $col2_width, $col3_width, $col4_width);

        $pdf->Ln(10); // Add more space
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, 'Comments:', 0, 1, 'L');
        $pdf->Ln(2);

        // --- Display Comments ---
        $pdf->SetFont('Arial', '', 11);
        if (!empty($comments)) {
            $comment_number = 1;
            foreach ($comments as $comment) {
                $clean_comment = trim(html_entity_decode($comment, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

                // Calculate height and check for page break
                $line_count = $pdf->NbLines(0, $comment_number . '. ' . $clean_comment);
                $cell_height = 5 * $line_count;
                if ($pdf->GetY() + $cell_height > $pdf->getPageBreakTrigger()) {
                    $pdf->AddPage($pdf->getCurOrientation());
                }

                $pdf->MultiCell(0, 5, $comment_number . '. ' . $clean_comment, 0, 'L');
                $pdf->Ln(2); // Space between comments
                $comment_number++;
            }
        } else {
            $pdf->Cell(0, 5, 'None', 0, 1, 'L');
        }
    }
} catch (PDOException $e) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetTextColor(255, 0, 0); // Red text
    $pdf->MultiCell(0, 10, 'Database Error in table-office.php: ' . $e->getMessage());
    $pdf->SetTextColor(0, 0, 0); // Reset text color
    error_log("Error in table-office.php: " . $e->getMessage());
}
