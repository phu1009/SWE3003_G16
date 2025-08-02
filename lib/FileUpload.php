<?php
class FileUpload
{
    /**
     * Saves an uploaded JPG / PNG and returns the web-relative path
     * (e.g. "images/avatars/abc123.jpg").
     *
     * @param array  $file     The $_FILES[...] element.
     * @param string $destDir  Absolute directory inside /public where the
     *                         image will be stored.  Defaults to
     *                         “…/public/images/products”.
     *
     * @throws RuntimeException on validation or move errors.
     * @return string|null      Relative path for <img src>, or NULL if
     *                          no file was supplied.
     */
    public static function saveImage(
        array  $file,
        string $destDir = __DIR__ . '/../public/images/products'
    ): ?string {

        /* ---------- no file selected ---------- */
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        /* ---------- hard errors from PHP upload ---------- */
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload error');
        }

        /* ---------- basic validation ---------- */
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $ext  = match ($mime) {
            'image/jpeg' => '.jpg',
            'image/png'  => '.png',
            default      => null
        };
        if (!$ext) {
            throw new RuntimeException('Only JPG/PNG allowed');
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new RuntimeException('Max 5 MB');
        }

        /* ---------- make sure destination exists ---------- */
        if (!is_dir($destDir) && !mkdir($destDir, 0775, true)) {
            throw new RuntimeException('Cannot create destination folder');
        }

        /* ---------- move with a unique name ---------- */
        $filename = bin2hex(random_bytes(8)) . $ext;
        $destPath = rtrim($destDir, '/\\') . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            throw new RuntimeException('Move failed');
        }

        /* ---------- build web-relative path ---------- */
        $publicRoot = str_replace('\\', '/', realpath(__DIR__ . '/../public')) . '/';
        $relative   = str_replace('\\', '/', realpath($destPath));
        $relative   = str_replace($publicRoot, '', $relative);

        return $relative;                   // e.g. "images/avatars/abc123.jpg"
    }
}
