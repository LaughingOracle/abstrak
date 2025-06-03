<!DOCTYPE html>
<html>
<head><title>Upload Zip</title></head>
<body>
    <h1>Upload a ZIP File</h1>
    @if(session('error')) <p style="color:red;">{{ session('error') }}</p> @endif
    <form method="POST" action="/upload" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="title" id="title" value="proposal">
        <input type="file" name="zip_file" required>
        <button type="submit">Upload</button>
    </form>
</body>
</html>