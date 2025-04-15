<?php

/**
 * Create a .zip archive from a file or directory
 *
 * @param string $source - The source file or directory to zip
 * @param string $destination - Path to the resulting .zip file
 * @return bool - Returns true on success, false on failure
 */
function createZipBundle(string $source, string $destination): bool {
    if (!file_exists($source)) {
        echo "Source path does not exist.\n";
        return false;
    }

    $zip = new ZipArchive();
    if ($zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
        echo "Failed to create zip file.\n";
        return false;
    }

    $source = realpath($source);

    if (is_dir($source)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($source) + 1);
            $zip->addFile($filePath, $relativePath);
        }
    } else {
        // It's a single file
        $zip->addFile($source, basename($source));
    }

    $zip->close();
    echo "Bundle created: $destination\n";
    return true;
}

/**
 * Extracts a .zip archive to a target folder
 *
 * @param string $zipFile - Path to the .zip file
 * @param string $extractTo - Destination folder to extract contents
 * @return bool - Returns true on success, false on failure
 */
function extractZipBundle(string $zipFile, string $extractTo): bool {
    if (!file_exists($zipFile)) {
        echo "Zip file does not exist.\n";
        return false;
    }

    $zip = new ZipArchive();
    if ($zip->open($zipFile) !== TRUE) {
        echo "Failed to open zip file.\n";
        return false;
    }

    $zip->extractTo($extractTo);
    $zip->close();
    echo "Bundle extracted to: $extractTo\n";
    return true;
}

// Example usage:
$sourcePath = 'example_dir_or_file';  // file or folder to zip
$outputZip = 'bundle.zip';            // resulting zip file
$unzipPath = 'unpacked_bundle';       // destination folder to extract

// Create a zip
createZipBundle($sourcePath, $outputZip);

// Extract it
extractZipBundle($outputZip, $unzipPath);
?>