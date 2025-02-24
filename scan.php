<?php
include 'db.php';

// ✅ Increase PHP limits for large file uploads
ini_set('upload_max_filesize', '50G'); // Allow up to 50GB files
ini_set('post_max_size', '50G'); // Allow up to 50GB data in a request
ini_set('memory_limit', '50G'); // Increase memory limit
ini_set('max_execution_time', '600'); // 10-minute timeout
ini_set('max_input_time', '600'); // 10-minute file processing time

// ✅ Check if a file was uploaded
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $filename = basename($file['name']);
    $filepath = "uploads/" . $filename;

    // ✅ Detect file extension for logging
    $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    echo "<p>Detected File Extension: <strong>$file_ext</strong></p>";

    // ✅ Ensure the 'uploads' directory exists
    if (!is_dir("uploads")) {
        mkdir("uploads", 0777, true);
    }

    // ✅ Check if file was actually uploaded
    if (!is_uploaded_file($file['tmp_name'])) {
        die("<p style='color:red;'>Upload Error: No file was uploaded.</p>");
    }

    // ✅ Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        echo "<p style='color:green;'>File uploaded successfully: $filename</p>";

        // ✅ Skip scanning for large binary files (videos, images, etc.)
        $text_based_exts = ['php', 'js', 'ts', 'html', 'txt', 'css', 'json', 'xml', 'log', 'md', 'csv'];
        if (in_array($file_ext, $text_based_exts)) {
            // ✅ Use fopen() to scan large text files line by line
            $handle = fopen($filepath, "r");
            if (!$handle) {
                die("<p style='color:red;'>Error: Unable to open file for scanning.</p>");
            }

            // ✅ Define malicious patterns
            $malicious_patterns = [
                '/base64_decode\s*\(/i',
                '/eval\s*\(/i',
                '/shell_exec\s*\(/i',
                '/system\s*\(/i'
            ];

            $detected = false;
            $matched_patterns = [];

            // ✅ Scan file line by line to save memory
            while (($line = fgets($handle)) !== false) {
                foreach ($malicious_patterns as $pattern) {
                    if (preg_match($pattern, $line)) {
                        $detected = true;
                        $matched_patterns[] = $pattern;
                    }
                }
            }
            fclose($handle);

            // ✅ Log intrusion if detected
            if ($detected) {
                $ip = $_SERVER['REMOTE_ADDR'];
                $patterns = implode(", ", $matched_patterns);

                $stmt = $conn->prepare("INSERT INTO intrusions (ip_address, detected_pattern, file_name) VALUES (?, ?, ?)");
                if ($stmt === false) {
                    die("<p style='color:red;'>Error: Database query preparation failed.</p>");
                }

                $stmt->bind_param("sss", $ip, $patterns, $filename);
                $stmt->execute();

                echo "<p style='color:red;'>Malicious Code Detected in $filename</p>";
            } else {
                echo "<p style='color:green;'>No Malware Found in $filename</p>";
            }
        } else {
            echo "<p style='color:blue;'>Non-text file uploaded (Skipped malware scan): $filename</p>";
        }
    } else {
        echo "<p style='color:red;'>File Upload Failed. Please check folder permissions.</p>";
    }
}
?>
