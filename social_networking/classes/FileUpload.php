<?php
class FileUpload {
    private $upload_dir = 'uploads/';
    private $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    private $max_size = 5000000; // 5

    public function __construct() {
        if (!is_dir($this->upload_dir)) {
            mkdir($this->upload_dir, 0755, true);
        }
    }


    public function validate($file) {
        if (empty($file) || $file['error'] != 0) {
            return [
                'success' => false,
                'message' => '❌ Опитай пак да ме качиш!'
            ];
        }

        $filename = $file['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $this->allowed_types)) {
            return [
                'success' => false,
                'message' => '❌ Позволени формати: JPG, PNG, GIF'
            ];
        }

        if ($file['size'] > $this->max_size) {
            return [
                'success' => false,
                'message' => '❌ Да не искаш да ме счупиш? Макс 5MB!'
            ];
        }

        return [
            'success' => true,
            'ext' => $ext
        ];
    }

    public function upload($file) {
        $validation = $this->validate($file);
        if (!$validation['success']) {
            return $validation;
        }

        $ext = $validation['ext'];
        $new_filename = 'img_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        $file_path = $this->upload_dir . $new_filename;

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            return [
                'success' => true,
                'path' => $file_path,
                'message' => '✅ Браво! Качи ме! Сега всички ще ме видят!'
            ];
        } else {
            return [
                'success' => false,
                'message' => '❌ Опитай пак! Не можа да ме качиш'
            ];
        }
    }


    public function delete($file_path) {
        if (!empty($file_path) && file_exists($file_path)) {
            if (unlink($file_path)) {
                return [
                    'success' => true,
                    'message' => '✅ Успя да ме изтриеш! Доволен/а ли си?'
                ];
            }
        }
        return [
            'success' => false,
            'message' => '❌ ХА! Не можа да ме изтриеш! Опитай пак!'
        ];
    }


    public function getUploadDir() {
        return $this->upload_dir;
    }
}
?>
