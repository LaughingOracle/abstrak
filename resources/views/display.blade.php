@php
function buildFileTreeForTitle(array $paths, $title) {
    $tree = [];
    foreach ($paths as $path) {
        // Only keep files under the folder matching $title
        $matchPos = strpos($path, '/' . $title . '/');
        if ($matchPos === false) continue;

        // Trim everything up to $title (inclusive)
        $subPath = substr($path, $matchPos + strlen($title) + 2); // 2 = slashes

        $parts = explode('/', $subPath);
        $current = &$tree;
        foreach ($parts as $i => $part) {
            if ($i === count($parts) - 1) {
                $current[] = $path; // full path
            } else {
                $current[$part] = $current[$part] ?? [];
                $current = &$current[$part];
            }
        }
    }
    return $tree;
}

$fileTree = buildFileTreeForTitle($files, $title);
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Files for {{ $title }} ({{ $event }})</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background: #f4f7fa;
            color: #333;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h1,p {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }

        .folder {
            cursor: pointer;
            font-weight: bold;
            padding: 6px 0;
            color: #2c3e50;
        }

        .folder:hover {
            color: #3498db;
        }

        .file {
            margin: 4px 0;
            padding-left: 16px;
        }

        .file a {
            color: #2980b9;
            text-decoration: none;
        }

        .file a:hover {
            text-decoration: underline;
        }

        .tree {
            list-style-type: none;
            padding-left: 20px;
            margin: 0;
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <h1> "<strong>{{ $title }}</strong>" </h1>
    <p> {{ $description }} </p>
    @if (count($fileTree))
        <ul class="tree">
            @php
                function renderTree($node, $prefix = '') {
                    foreach ($node as $key => $value) {
                        if (is_array($value)) {
                            $id = uniqid('folder_');
                            echo "<li>";
                            echo "<div class='folder' onclick=\"toggleFolder('$id')\">📁 $key</div>";
                            echo "<ul class='tree hidden' id='$id'>";
                            renderTree($value, $prefix . $key . '/');
                            echo "</ul></li>";
                        } elseif (is_string($value)) {
                            $filename = basename($value);
                            echo "<li class='file'>📄 <a href='" . asset('storage/' . $value) . "' target='_blank'>$filename</a></li>";
                        }
                    }
                }
                renderTree($fileTree);
            @endphp
        </ul>
    @else
        <p>No files found for <strong>{{ $title }}</strong>.</p>
    @endif

    <script>
        function toggleFolder(id) {
            const el = document.getElementById(id);
            el.classList.toggle('hidden');
        }
    </script>
</body>
</html>