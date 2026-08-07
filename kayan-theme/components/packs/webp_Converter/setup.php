<?php 
add_filter('wp_handle_upload_prefilter', 'convert_image_to_webp_before_upload');

function convert_image_to_webp_before_upload($file) {
    $file_info = wp_check_filetype($file['name']);
    $mime_type = $file_info['type'];

    if ($mime_type === 'image/jpeg' || $mime_type === 'image/png') {
        $uploaded_file = $file['tmp_name'];
        $converted_file = convert_image_to_webp($uploaded_file);

        if ($converted_file !== false) {
            $file['tmp_name'] = $converted_file;
            $file['name'] = pathinfo($file['name'], PATHINFO_FILENAME) . '.webp';
            $file['type'] = 'image/webp';
        } else {
            return $file;
        }
    }

    return $file;
}

function convert_image_to_webp($image_path) {
    $image = imagecreatefromstring(file_get_contents($image_path));
    if ($image !== false) {
        $webp_image_path = $image_path;
        if (imagewebp($image, $webp_image_path)) {
            imagedestroy($image);
            return $webp_image_path;
        }
    }

    return false;
}
