<?php
function zipDirectory($sourceDir, $zipFilePath) {
    $zip = new ZipArchive();
    if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourceDir),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($sourceDir) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
        return true;
    }
    return false;
}

// ==== Settings ====
$bundleName = "frontend";
$version = "1.0.0";
$sourceDir = __DIR__ . "/packages/$bundleName";
$zipDir = __DIR__ . "/bundles";
$zipFile = "$zipDir/{$bundleName}-v$version.zip";


try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=testdb;charset=utf8mb4", "testUser", "12345");
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// ==== Create bundles directory if needed ====
if (!file_exists($zipDir)) {
    mkdir($zipDir, 0755, true);
}

// ==== Zip the bundle ====
if (zipDirectory($sourceDir, $zipFile)) {
    echo "Created package: $zipFile\n";
} else {
    die("Failed to create package.\n");
}

// ==== Insert into Version table ====
$sql = "INSERT INTO Version (bundle_name, version, filename, status) 
        VALUES (:bundle_name, :version, :filename, :status)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'bundle_name' => $bundleName,
    'version' => $version,
    'filename' => basename($zipFile),
    'status' => 'new'
]);

echo "Version $version for $bundleName added to Version table.\n";
?>
