<?php
$zipFile = $argv[1] ?? null;

if (!$zipFile || !file_exists($zipFile)) {
    die("Zip file not found.\n");
}

// === Extract to temp ===
$tempPath = "/tmp/deploy_tmp_" . uniqid();
mkdir($tempPath, 0755, true);

$zip = new ZipArchive();
if ($zip->open($zipFile) === TRUE) {
    $zip->extractTo($tempPath);
    $zip->close();
} else {
    die("Failed to unzip file.\n");
}

// === Load deploy.json ===
$configFile = "$tempPath/deploy.json";
if (!file_exists($configFile)) {
    die("Missing deploy.json in package.\n");
}
$config = json_decode(file_get_contents($configFile), true);

// === Copy files to deploy path ===
$deployPath = $config['deploy_path'];
if (!file_exists($deployPath)) {
    mkdir($deployPath, 0755, true);
}
shell_exec("cp -r $tempPath/* $deployPath");

// === Run custom commands ===
foreach ($config['commands'] as $cmd) {
    echo "Running: $cmd\n";
    $output = shell_exec("cd $deployPath && $cmd 2>&1");
    echo $output;
}

// === Restart the systemd service ===
$service = $config['service_name'];
echo "Restarting service: $service\n";
$output = shell_exec("sudo systemctl restart $service 2>&1");
echo $output;

echo "Deployment complete.\n";
?>
