<?php
$rootDirectory = __DIR__;

/**
 * Recursively get the file/folder structure as an array.
 * Exclude this script (explorer.php) from the listing.
 */
function getDirectoryStructure($directory, $scriptName) {
    $result = [];
    $files = @scandir($directory);

    if ($files === false) {
        return $result;
    }

    foreach ($files as $file) {
        // Skip . and ..
        if ($file === '.' || $file === '..') {
            continue;
        }

        // Exclude the script itself from the list
        if ($file === $scriptName) {
            continue;
        }

        $fullPath = $directory . DIRECTORY_SEPARATOR . $file;
        if (is_dir($fullPath)) {
            // It's a folder, recursively build structure
            $result[] = [
                'type' => 'dir',
                'name' => $file,
                'path' => $fullPath,
                'children' => getDirectoryStructure($fullPath, $scriptName)
            ];
        } else {
            // It's a file
            $result[] = [
                'type' => 'file',
                'name' => $file,
                'path' => $fullPath
            ];
        }
    }

    return $result;
}

/**
 * Render the file/folder structure as nested HTML list items
 */
function renderDirectoryTree($structure) {
    echo '<ul class="tree">';
    foreach ($structure as $item) {
        if ($item['type'] === 'dir') {
            echo '<li class="folder">';
            // Folder name with toggle onclick
            echo '<span class="folder-name" onclick="toggleFolder(this)">'
                 . htmlspecialchars($item['name'])
                 . '</span>';
            // Recursively render children
            renderDirectoryTree($item['children']);
            echo '</li>';
        } else {
            // File
            echo '<li class="file" onclick="toggleFileContent(this)" data-filepath="'.htmlspecialchars($item['path']).'">';
            echo '<span class="file-name">'
                 . htmlspecialchars($item['name'])
                 . '</span>';
            // Placeholder where file content will be loaded
            echo '<div class="file-content" style="display:none;"></div>';
            echo '</li>';
        }
    }
    echo '</ul>';
}

/**
 * If a file content request is made via AJAX, return its contents (sanitized).
 */
if (isset($_GET['fetchFile']) && !empty($_GET['fetchFile'])) {
    $fileToFetch = $_GET['fetchFile'];

    // Basic security check: ensure the file is in the $rootDirectory subtree
    $realRoot = realpath($rootDirectory);
    $realFile = realpath($fileToFetch);

    if ($realFile && strpos($realFile, $realRoot) === 0 && is_file($realFile)) {
        // Read the file content
        $content = @file_get_contents($realFile);
        // Convert special characters to HTML entities
        echo nl2br(htmlspecialchars($content));
    } else {
        echo 'Error: Cannot open the file or file is outside allowed directory.';
    }
    exit; // End AJAX response
}

// --------------------------------------------------
// Normal page load
// --------------------------------------------------
$scriptName = basename(__FILE__); // The name of this script
$structure = getDirectoryStructure($rootDirectory, $scriptName);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>PHP File Explorer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .controls {
            margin-bottom: 10px;
        }
        .tree {
            list-style-type: none;
            margin: 0;
            padding: 0 20px;
        }
        .folder, .file {
            margin: 5px 0;
            cursor: pointer;
            position: relative;
        }
        .folder-name:before {
            content: "📁 ";
            margin-right: 5px;
        }
        .file-name:before {
            content: "📄 ";
            margin-right: 5px;
        }
        /* Indent children */
        .tree ul {
            margin-left: 20px;
            display: none; /* Initially collapsed */
        }
    </style>
</head>
<body>

<div class="controls">
    <button onclick="expandAll()">Expand All</button>
    <button onclick="collapseAll()">Collapse All</button>
    <button onclick="openAllFileContents()">Open All File Contents</button>
</div>

<div id="file-explorer">
    <?php
    // Render the directory tree
    renderDirectoryTree($structure);
    ?>
</div>

<script>
/**
 * Toggle showing/hiding of folder contents (child <ul>)
 */
function toggleFolder(element) {
    // The <li> that has class "folder" is the parent of the span.
    const folderItem = element.closest('.folder');
    if (!folderItem) return;

    // The <ul> inside the folder <li> is the folder's content.
    const childUl = folderItem.querySelector(':scope > ul');
    if (childUl) {
        // Toggle display
        childUl.style.display = (childUl.style.display === 'none' || childUl.style.display === '') 
                                ? 'block' : 'none';
    }
}

/**
 * Toggle file content. If hidden, fetch from server (if not already fetched)
 */
function toggleFileContent(fileLi) {
    const contentDiv = fileLi.querySelector('.file-content');
    if (!contentDiv) return;

    // Toggle display
    if (contentDiv.style.display === 'none' || contentDiv.style.display === '') {
        // If content is empty, fetch from server
        if (!contentDiv.innerHTML.trim()) {
            const filePath = fileLi.getAttribute('data-filepath');
            fetch(`?fetchFile=${encodeURIComponent(filePath)}`)
                .then(response => response.text())
                .then(data => {
                    contentDiv.innerHTML = data;
                    contentDiv.style.display = 'block';
                })
                .catch(err => {
                    contentDiv.innerHTML = 'Error fetching file content.';
                    contentDiv.style.display = 'block';
                });
        } else {
            contentDiv.style.display = 'block';
        }
    } else {
        contentDiv.style.display = 'none';
    }
}

/**
 * Expand all folders: show all child <ul>
 */
function expandAll() {
    const allUls = document.querySelectorAll('.tree ul');
    allUls.forEach(ul => {
        ul.style.display = 'block';
    });
}

/**
 * Collapse all folders: hide all child <ul> and file content divs
 */
function collapseAll() {
    const allUls = document.querySelectorAll('.tree ul');
    allUls.forEach(ul => {
        ul.style.display = 'none';
    });
    // Also hide file content
    const allFileContents = document.querySelectorAll('.file-content');
    allFileContents.forEach(div => {
        div.style.display = 'none';
    });
}

/**
 * Open all file contents:
 *  1. Expand all folders so all files are visible
 *  2. For each file, fetch and display its content (if not already fetched)
 */
function openAllFileContents() {
    // First, expand all folders so files are visible
    expandAll();

    // For each file, ensure content is fetched and shown
    const allFileItems = document.querySelectorAll('.file');
    allFileItems.forEach(fileLi => {
        const contentDiv = fileLi.querySelector('.file-content');
        if (!contentDiv) return;

        // If content is empty, fetch from server
        if (!contentDiv.innerHTML.trim()) {
            const filePath = fileLi.getAttribute('data-filepath');
            fetch(`?fetchFile=${encodeURIComponent(filePath)}`)
                .then(response => response.text())
                .then(data => {
                    contentDiv.innerHTML = data;
                    contentDiv.style.display = 'block';
                })
                .catch(err => {
                    contentDiv.innerHTML = 'Error fetching file content.';
                    contentDiv.style.display = 'block';
                });
        } else {
            // If it’s already fetched, just ensure it’s displayed
            contentDiv.style.display = 'block';
        }
    });
}
</script>

</body>
</html>
