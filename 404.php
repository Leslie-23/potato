<?php
http_response_code(404);
session_start();

if (isset($_GET['action']) && $_GET['action'] === 'get_file_tree') {
    
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
                $tree .= '<div class="folder">';
                $tree .= '<div class="folder-header" onclick="toggleFolder(this)">' . $prefix . '📁 ' . htmlspecialchars($file) . '</div>';
                $tree .= '<div class="folder-content">' . generateFileTree($path, $prefix . '&nbsp;&nbsp;&nbsp;&nbsp;') . '</div>';
                $tree .= '</div>';
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
        .folder-header { cursor: pointer; color: #007bff; font-weight: bold; padding: 2px 0; }
        .folder-header:hover { background-color: #e9ecef; }
        .folder-content { margin-left: 15px; display: none; }
        .folder.open .folder-content { display: block; }
        #securityModal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; }
        .modal-content { background: white; margin: 100px auto; padding: 20px; border-radius: 5px; max-width: 500px; }
        .security-challenge { margin: 15px 0; }
        .security-input { width: 100%; padding: 8px; margin: 5px 0; }
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

    <div id="securityModal">
        <div class="modal-content">
            <h2>Security Verification</h2>
            <p>Complete these challenges to view project files:</p>
            
            <div class="security-challenge">
                <p>1. Enter the password: <strong>dev123</strong></p>
                <input type="password" id="passwordInput" class="security-input" placeholder="Enter password">
            </div>
            
            <div class="security-challenge">
                <p>2. Arrange these letters in ascending order: <span id="lettersToSort"></span></p>
                <input type="text" id="sortedLettersInput" class="security-input" placeholder="Enter sorted letters">
            </div>
            
            <div class="security-challenge">
                <p>3. What's the next number in this sequence? <span id="numberSequence"></span></p>
                <input type="text" id="sequenceAnswerInput" class="security-input" placeholder="Enter next number">
            </div>
            
            <button id="verifyBtn" class="btn">Verify</button>
            <button id="cancelBtn" class="btn" style="background: #6c757d;">Cancel</button>
            <p id="errorMessage" style="color: red; display: none;">Verification failed. Please try again.</p>
        </div>
    </div>

    <script>
       
        const challenges = {
            password: 'dev123',
            letters: ['x', 'a', 'm', 'p', 'l', 'e'],
            sequence: [2, 4, 8, 16, 32]
        };
        
       s
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('lettersToSort').textContent = challenges.letters.join(', ');
            document.getElementById('numberSequence').textContent = challenges.sequence.join(', ');
        });
        
        function toggleFolder(element) {
            const folder = element.parentElement;
            folder.classList.toggle('open');
        }
        
        document.getElementById('showTreeBtn').addEventListener('click', () => {
            document.getElementById('securityModal').style.display = 'block';
        });
        
        document.getElementById('cancelBtn').addEventListener('click', () => {
            document.getElementById('securityModal').style.display = 'none';
            document.getElementById('errorMessage').style.display = 'none';
        });
        
        document.getElementById('verifyBtn').addEventListener('click', () => {
            const password = document.getElementById('passwordInput').value;
            const sortedLetters = document.getElementById('sortedLettersInput').value;
            const sequenceAnswer = document.getElementById('sequenceAnswerInput').value;
            
            if (password !== challenges.password) {
                showError();
                return;
            }
            
            const correctSorted = [...challenges.letters].sort().join('');
            if (sortedLetters.toLowerCase().replace(/[^a-z]/g, '') !== correctSorted) {
                showError();
                return;
            }
            
            if (sequenceAnswer.trim() !== '64') {
                showError();
                return;
            }
            
            document.getElementById('securityModal').style.display = 'none';
            loadFileTree();
        });
        
        function showError() {
            document.getElementById('errorMessage').style.display = 'block';
        }
        
        function loadFileTree() {
            const fileTree = document.getElementById('fileTree');
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
        }
    </script>
</body>
</html>