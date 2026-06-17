<?php

/*
|--------------------------------------------------------------------------
| Repository Portal
|--------------------------------------------------------------------------
|
| Internal Repository Management System
|
| Developed By:
| Kamrang
| Infrastructure Engineer
| PT. Global Inti Corporatama
|
| Storage Backend:
| - TrueNAS Storage
| - Local Storage Fallback
|
| Version: 1.0.0
| Last Updated: June 2026
|
| Copyright (c) <?= date('Y') ?>
| Kamrang. All Rights Reserved.
|
*/

$truenasPath = '/mnt/truenas/repository';
$localPath   = __DIR__ . '/repository';

if (is_dir($truenasPath)) {

    $baseDir = realpath($truenasPath);

    $storageName = 'TrueNAS Storage';
} else {

    $baseDir = realpath($localPath);

    $storageName = 'Local Storage';
}

if (!$baseDir) {
    die('Repository folder not found');
}

$current = trim($_GET['path'] ?? '', '/');

$targetDir = $baseDir;

if ($current !== '') {

    $candidate = realpath($baseDir . DIRECTORY_SEPARATOR . $current);

    if (
        $candidate === false ||
        strncmp($candidate, $baseDir, strlen($baseDir)) !== 0
    ) {
        die('Invalid path');
    }

    $targetDir = $candidate;
}

$items = scandir($targetDir);

function formatBytes($bytes): string
{
    if (!is_numeric($bytes) || $bytes <= 0) {
        return '-';
    }

    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

    $power = min(
        floor(log($bytes, 1024)),
        count($units) - 1
    );

    return round(
        $bytes / pow(1024, $power),
        2
    ) . ' ' . $units[$power];
}

function getTotalFiles($dir)
{
    $rii = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir)
    );

    $count = 0;

    foreach ($rii as $file) {
        if (!$file->isDir()) {
            $count++;
        }
    }

    return $count;
}

function getTotalFolders($dir)
{
    $rii = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir),
        RecursiveIteratorIterator::SELF_FIRST
    );

    $count = 0;

    foreach ($rii as $file) {
        if ($file->isDir()) {
            $count++;
        }
    }

    return $count - 1;
}

$totalFiles = getTotalFiles($baseDir);
$totalFolders = getTotalFolders($baseDir);

?>
<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>GIC Repository</title>

    <meta name="description"
        content="Repository resmi Kamrang">

    <meta name="keywords"
        content="repository,mikrotik,linux,proxmox,vmware">

    <meta name="author"
        content="Kamrang">

    <link rel="icon"
        href="assets/img/favicon.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">

    <link rel="stylesheet"
        href="assets/css/style.css">

</head>

<body>

    <header class="top-header">

        <div class="container">

            <div class="header-wrapper">

                <div class="company-info">

                    <img src="assets/img/logo-repo.png"
                        class="company-logo"
                        alt="Logo">

                    <div class="company-text">

                        <h4 class="company-title">
                            Logo Company Repository
                        </h4>

                        <div class="company-subtitle">
                            Repository Center & Software Distribution
                        </div>

                    </div>

                </div>

                <div class="header-actions">

                    <div class="storage-badge d-none d-md-flex">

                        <i class="bi bi-hdd-network"></i>

                        <?= htmlspecialchars($storageName) ?>

                    </div>

                    <button id="themeToggle"
                        class="theme-btn">

                        <i class="bi bi-moon-fill"></i>

                    </button>

                </div>

            </div>

        </div>

    </header>

    <main>

        <div class="container repository-container">

            <?php
            $currentPath = $current
                ? '/' . trim($current, '/') . '/'
                : '/';
            ?>

            <div class="mb-4">

                <h2 class="fw-bold">

                    Index of
                    <?= htmlspecialchars($currentPath) ?>

                </h2>

            </div>

            <div class="row mb-4">

                <div class="col-lg-5 ms-auto">

                    <div class="input-group">

                        <input
                            type="text"
                            id="searchInput"
                            class="form-control"
                            placeholder="Cari file atau folder...">

                        <button
                            class="btn btn-primary"
                            id="searchBtn"
                            type="button">

                            <i class="bi bi-search"></i>
                            Search

                        </button>

                    </div>

                </div>

            </div>

            <div class="card repository-card">

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-hover mb-0">

                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th width="180">Date</th>
                                    <th width="140">Size</th>
                                </tr>
                            </thead>

                            <tbody id="repoTable">

                                <?php

                                if ($current) {

                                    $parent = dirname($current);

                                    if ($parent === '.') {
                                        $parent = '';
                                    }
                                ?>
                                    <tr>
                                        <td>
                                            <a href="?path=<?= urlencode($parent) ?>">
                                                <i class="bi bi-arrow-left-circle"></i>
                                                Kembali
                                            </a>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>

                                    <?php
                                }

                                $entries = [];

                                foreach ($items as $item) {

                                    if ($item === '.' || $item === '..') {
                                        continue;
                                    }

                                    $entries[] = $item;
                                }

                                usort($entries, function ($a, $b) use ($targetDir) {

                                    $aDir = is_dir($targetDir . '/' . $a);
                                    $bDir = is_dir($targetDir . '/' . $b);

                                    if ($aDir && !$bDir) return -1;
                                    if (!$aDir && $bDir) return 1;

                                    return strcasecmp($a, $b);
                                });

                                $hasData = false;

                                foreach ($entries as $item) {

                                    $hasData = true;

                                    $full = $targetDir . '/' . $item;

                                    $relative = ltrim($current . '/' . $item, '/');

                                    $date = date(
                                        'd M Y H:i',
                                        filemtime($full)
                                    );

                                    if (is_dir($full)) {
                                    ?>

                                        <tr class="repo-row">

                                            <td>
                                                <a href="?path=<?= urlencode($relative) ?>"
                                                    class="repo-link">

                                                    <i class="bi bi-folder-fill folder-icon"></i>

                                                    <?= htmlspecialchars($item) ?>

                                                </a>
                                            </td>

                                            <td><?= $date ?></td>

                                            <td>-</td>

                                        </tr>

                                    <?php

                                    } else {

                                        $size = formatBytes(filesize($full));

                                    ?>

                                        <tr class="repo-row">

                                            <td>

                                                <a href="download.php?file=<?= urlencode($relative) ?>"
                                                    class="repo-link">

                                                    <i class="bi bi-file-earmark file-icon"></i>

                                                    <?= htmlspecialchars($item) ?>

                                                </a>

                                            </td>

                                            <td><?= $date ?></td>

                                            <td><?= $size ?></td>

                                        </tr>

                                    <?php
                                    }
                                }

                                if (!$hasData) {
                                    ?>

                                    <tr>
                                        <td colspan="3"
                                            class="text-center py-5">

                                            <i class="bi bi-folder-x fs-1 d-block mb-3"></i>

                                            Folder kosong

                                        </td>
                                    </tr>

                                <?php } ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </main>

    <footer class="footer">

        <div class="container">

            <div class="row g-4">

                <div class="col-md-6">

                    <h5>
                        Logo Company Repository
                    </h5>

                    <p>

                        Repository resmi perusahaan untuk
                        software, firmware, ISO installer,
                        documentation dan tools operasional.

                    </p>

                </div>

                <div class="col-md-3">

                    <h6>Repository</h6>

                    <ul class="list-unstyled">

                        <li>Linux</li>
                        <li>Mikrotik</li>
                        <li>Proxmox</li>
                        <li>VMware</li>

                    </ul>

                </div>

                <div class="col-md-3">

                    <h6>System</h6>

                    <ul class="list-unstyled">

                        <li>TrueNAS Storage</li>
                        <li>PHP Repository Browser</li>

                    </ul>

                </div>

            </div>

            <hr>

            <div class="text-center">

                © <?= date('Y') ?>
                Kamrang

            </div>

        </div>

    </footer>

    <script src="assets/js/app.js"></script>

</body>

</html>