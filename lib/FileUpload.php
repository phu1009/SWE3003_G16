<?php
class FileUpload {
    public static function saveImage(array $file, string $destDir = __DIR__.'/../public/images/products'): ?string {
        if ($file['error'] === UPLOAD_ERR_NO_FILE) return null;
        if ($file['error'] !== UPLOAD_ERR_OK)       throw new RuntimeException('Upload error');

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $ext  = match($mime) { 'image/jpeg'=>'.jpg', 'image/png'=>'.png', default=>null };
        if (!$ext)               throw new RuntimeException('Only JPG/PNG');
        if ($file['size'] > 5*1024*1024) throw new RuntimeException('Max 5 MB');

        $filename = bin2hex(random_bytes(8)).$ext;
        $destPath = $destDir.'/'.$filename;
        if (!move_uploaded_file($file['tmp_name'], $destPath))
            throw new RuntimeException('Move failed');

        return 'images/products/'.$filename;   // relative to /public
    }
}
