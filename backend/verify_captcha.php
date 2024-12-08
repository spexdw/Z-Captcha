<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');


class CaptchaVerification {
    private $config;
    private $clientIp;
    private $bannedIps;

    public function __construct() {
        $this->config = json_decode(file_get_contents('../config/config.json'), true);
        $this->clientIp = $_SERVER['REMOTE_ADDR'];
        $this->bannedIps = json_decode(file_get_contents('../config/banned.json') ?: '{}', true);
    }

    public function verifyCode($userInput, $refreshCount) {
        if ($this->isIpBanned()) {
            return [
                'success' => false, 
                'message' => 'IP Blocked'
            ];
        }

        $maxRefreshAttempts = $this->config['captcha_settings']['max_refresh_attempts'];
        if ($refreshCount > $maxRefreshAttempts) {
            return [
                'success' => false, 
                'message' => 'Maximum refresh attempts exceeded'
            ];
        }

        $elapsedTime = time() - $_SESSION['z_captcha_start_time'];
        $maxTime = $this->config['captcha_settings']['timeout_seconds'];

        if ($elapsedTime > $maxTime) {
            return [
                'success' => false, 
                'message' => 'Time Expired'
            ];
        }

        $correctCode = $_SESSION['z_captcha_code'];
        $userCode = strtoupper(trim($userInput));
        $difficulty = $_SESSION['z_captcha_difficulty'];

        if ($userCode === $correctCode) {
            $this->resetAttempts();
            
            // Set verification cookie
            $cookieDuration = $this->config['security']['verification_cookie_duration'];

            setcookie('z_captcha_verified', 'true', [
                'expires' => time() + $cookieDuration,
                'path' => '/',
                'secure' => true, 
                'httponly' => true,
                'samesite' => 'Strict'
            ]);

            setcookie('z_captcha_verified_time', time(), [
                'expires' => time() + $cookieDuration,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);

            return [
                'success' => true, 
                'message' => 'Verification Successful',
                'difficulty' => $difficulty,
                'redirect_url' => $this->config['redirect']['success_url']
            ];
        } else {
            $this->incrementFailedAttempts();
            return [
                'success' => false, 
                'message' => 'Invalid Code',
                'difficulty' => $difficulty
            ];
        }
    }

    private function isIpBanned() {
        return isset($this->bannedIps[$this->clientIp]) && 
               $this->bannedIps[$this->clientIp]['until'] > time();
    }

    private function incrementFailedAttempts() {
        $attempts = $_SESSION['failed_attempts'] ?? 0;
        $_SESSION['failed_attempts'] = $attempts + 1;

        if ($attempts >= $this->config['captcha_settings']['max_attempts']) {
            $this->banIp();
        }
    }

    private function banIp() {
        $this->bannedIps[$this->clientIp] = [
            'until' => time() + $this->config['security']['block_duration']
        ];
        file_put_contents('../config/banned.json', json_encode($this->bannedIps));
    }

    private function resetAttempts() {
        unset($_SESSION['failed_attempts']);
    }
}

$data = json_decode(file_get_contents('php://input'), true);
$verification = new CaptchaVerification();
echo json_encode($verification->verifyCode(
    $data['captchaCode'], 
    $data['refreshCount'] ?? 0
));