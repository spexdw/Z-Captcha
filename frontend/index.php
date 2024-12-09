<?php
session_start();

$returnUrl = isset($_GET['return']) ? urldecode($_GET['return']) : '../passed.html';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Z-Captcha Security Verification</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #4A148C 0%, #6A1B9A 50%, #8E24AA 100%);
        }
        .captcha-card {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.125);
            transition: all 0.3s ease-in-out;
            color: white;
        }
        .captcha-card:hover {
            transform: scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        #captchaInput {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }
        #captchaInput::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="captcha-card shadow-2xl rounded-2xl p-8">
            <div class="flex items-center justify-center mb-6">
                <h1 class="text-3xl font-bold ml-4 text-white">Z-Captcha</h1>
            </div>
            
            <div class="mb-6">
                <div class="relative">
                    <img id="captchaImage" class="w-full h-40 object-cover rounded-lg shadow-md" alt="CAPTCHA">
                    <div class="absolute top-2 right-2">
                        <button id="refreshCaptcha" class="bg-white/20 hover:bg-white/30 rounded-full p-2 transition-all">
                            <i class="fas fa-sync text-black"></i>
                        </button>
                    </div>
                </div>
                <div class="flex justify-between mt-3">
                    <div id="difficultyInfo" class="text-sm text-white/80 font-semibold flex items-center">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Difficulty Level: Generating...
                    </div>
                    <div id="refreshCount" class="text-sm text-white/80 font-semibold">
                        Refresh: 0/3
                    </div>
                </div>
            </div>

            <input 
                type="text" 
                id="captchaInput" 
                placeholder="Enter CAPTCHA code" 
                class="w-full p-3 border-2 border-white/20 rounded-lg mb-4 text-center uppercase tracking-widest focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
            >
            
            <div id="timer" class="text-center text-red-400 font-bold mb-4"></div>

            <input type="hidden" id="returnUrl" value="<?php echo htmlspecialchars($returnUrl); ?>">

            <button 
                id="verifyCaptcha" 
                class="w-full bg-gradient-to-r from-purple-600 to-purple-800 text-white py-3 rounded-lg hover:opacity-90 transition-all flex items-center justify-center"
            >
                <i class="fas fa-shield-alt mr-2"></i>
                Verify Captcha
            </button>

            <div id="result" class="mt-4 text-center text-white"></div>
        </div>
        
        <div class="text-center text-white/80 mt-4 text-sm">
            www.z-clients.org
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
