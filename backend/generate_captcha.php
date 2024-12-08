<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

if (!extension_loaded('gd')) {
    die('GD extension is not loaded. Please enable it in php.ini');
}

class ZCaptcha {
    private $config;

    public function __construct() {
        $configPath = dirname(__FILE__) . '/../config/config.json';
        if (!file_exists($configPath)) {
            $this->sendErrorResponse("Config file not found");
        }
        
        $configContents = file_get_contents($configPath);
        if ($configContents === false) {
            $this->sendErrorResponse("Unable to read config file");
        }
        
        $this->config = json_decode($configContents, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->sendErrorResponse("Invalid JSON in config file");
        }
    }

    private function sendErrorResponse($message) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode([
            'error' => true,
            'message' => $message
        ]);
        exit;
    }

    private function getRandomDifficulty() {
        $difficulties = ['easy', 'medium', 'hard'];
        $weights = [0.4, 0.4, 0.2];
        
        $randomValue = (float)rand() / (float)getrandmax();
        $cumulativeProbability = 0;
        
        foreach ($difficulties as $index => $difficulty) {
            $cumulativeProbability += $weights[$index];
            if ($randomValue <= $cumulativeProbability) {
                return $difficulty;
            }
        }
        
        return 'medium';
    }

    public function generateCaptcha($refreshCount = 0) {
        try {

            $maxRefreshAttempts = $this->config['captcha_settings']['max_refresh_attempts'];
            if ($refreshCount >= $maxRefreshAttempts) {
                throw new Exception('Maximum refresh attempts reached');
            }

            $difficulty = $this->getRandomDifficulty();
            $settings = $this->config['captcha_settings']['difficulty_levels'][$difficulty];
            
            $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
            $captchaCode = '';
            
            for ($i = 0; $i < $settings['length']; $i++) {
                $captchaCode .= $characters[random_int(0, strlen($characters) - 1)];
            }

            $backgroundImage = $this->generateBackgroundImage($settings);
            $this->addCaptchaText($backgroundImage, $captchaCode, $settings);

            $_SESSION['z_captcha_code'] = strtoupper($captchaCode);
            $_SESSION['z_captcha_start_time'] = time();
            $_SESSION['z_captcha_difficulty'] = $difficulty;
            $_SESSION['z_captcha_refresh_count'] = $refreshCount;

            echo json_encode([
                'image' => $this->imageToBase64($backgroundImage),
                'timestamp' => $_SESSION['z_captcha_start_time'],
                'difficulty' => $difficulty,
                'refreshCount' => $refreshCount
            ]);
        } catch (Exception $e) {
            $this->sendErrorResponse($e->getMessage());
        }
    }

    private function generateBackgroundImage($settings) {
        $width = 300;
        $height = 150;
        $image = imagecreatetruecolor($width, $height);
    
        $backgroundsPath = dirname(__FILE__) . '/assets/backgrounds/';
        
        $backgrounds = array_merge(
            glob($backgroundsPath . '*.png'),
            glob($backgroundsPath . '*.jpg'),
            glob($backgroundsPath . '*.jpeg')
        );
        
        if (empty($backgrounds)) {
            $backgroundColor = imagecolorallocate($image, 255, 255, 255);
            imagefill($image, 0, 0, $backgroundColor);
        } else {
            $backgroundPath = $backgrounds[array_rand($backgrounds)];
            
            $extension = strtolower(pathinfo($backgroundPath, PATHINFO_EXTENSION));
            switch($extension) {
                case 'png':
                    $background = imagecreatefrompng($backgroundPath);
                    break;
                case 'jpg':
                case 'jpeg':
                    $background = imagecreatefromjpeg($backgroundPath);
                    break;
                default:
                    $backgroundColor = imagecolorallocate($image, 255, 255, 255);
                    imagefill($image, 0, 0, $backgroundColor);
                    return $image;
            }
            
            imagecopyresized($image, $background, 0, 0, 0, 0, $width, $height, imagesx($background), imagesy($background));
            imagedestroy($background);
        }
    
        $this->addNoiseAndDistortion($image, $settings);
        return $image;
    }

    private function addNoiseAndDistortion($image, $settings) {
        $width = imagesx($image);
        $height = imagesy($image);
        $noiseLevel = $settings['noise_level'];
        
        for ($i = 0; $i < $width * $height * $noiseLevel; $i++) {
            $color = imagecolorallocate($image, 
                rand(0, 255), 
                rand(0, 255), 
                rand(0, 255)
            );
            imagesetpixel($image, rand(0, $width), rand(0, $height), $color);
        }
    }

    private function addCaptchaText($image, $text, $settings) {
        $textColor = imagecolorallocate($image, 0, 0, 0);
        $fontPath = dirname(__FILE__) . '/../assets/fonts/arial.ttf';
        
        if (!file_exists($fontPath)) {
            throw new Exception("Font file not found");
        }
        
        $fontSize = 10 + ($settings['complexity'] * 7);
        $angle = rand(-15, 15);
        
        imagettftext($image, $fontSize, $angle, 50, 100, $textColor, $fontPath, $text);
    }

    private function imageToBase64($image) {
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);
        return 'data:image/png;base64,' . base64_encode($imageData);
    }
}

$data = json_decode(file_get_contents('php://input'), true);
$refreshCount = $data['refreshCount'] ?? 0;

try {
    $captcha = new ZCaptcha();
    $captcha->generateCaptcha($refreshCount);
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}