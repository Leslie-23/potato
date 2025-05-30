<?php
http_response_code(404);
session_start();

if (isset($_GET['action']) && $_GET['action'] === 'get_file_tree') {
    // Optional: restrict access (local IP or dev session)
    $allowed_ips = ['127.0.0.1', '::1'];
    if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
        die('Access denied');
    }

    function generateFileTree($dir, $prefix = '') {
        $tree = '';
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                $tree .= '<div class="file-item directory">' . $prefix . '📁 ' . htmlspecialchars($file) . '</div>';
                $tree .= generateFileTree($path, $prefix . '&nbsp;&nbsp;&nbsp;&nbsp;');
            } else {
                $tree .= '<div class="file-item file">' . $prefix . '📄 ' . htmlspecialchars($file) . '</div>';
            }
        }
        return $tree;
    }

    echo generateFileTree(__DIR__);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>404 - Page Not Found</title>
    <style>
        body { font-family: Arial; background: #f8f9fa; text-align: center; padding: 40px; }
        .container { max-width: 600px; margin: auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #dc3545; font-size: 3em; }
        p { font-size: 1.2em; }
        .btn { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; margin: 10px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        #fileTree { display: none; margin-top: 20px; background: #f1f1f1; padding: 10px; text-align: left; overflow-y: auto; max-height: 400px; border-radius: 4px; font-family: monospace; }
        .directory { color: #007bff; font-weight: bold; }
        .file { color: #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <p>The page you're looking for doesn't exist.</p>
        <a href="/" class="btn">Go to Homepage</a>
        <button id="showTreeBtn" class="btn">Show Project Files</button>
        <div id="fileTree"></div>
    </div>

    <script>
        document.getElementById('showTreeBtn').addEventListener('click', () => {
            const fileTree = document.getElementById('fileTree');
            if (fileTree.style.display === 'none' || !fileTree.style.display) {
                if (!fileTree.innerHTML) {
                    fetch('?action=get_file_tree')
                        .then(res => res.text())
                        .then(data => {
                            fileTree.innerHTML = data;
                            fileTree.style.display = 'block';
                        });
                } else {
                    fileTree.style.display = 'block';
                }
            } else {
                fileTree.style.display = 'none';
            }
        });
    </script>
</body>
</html>
