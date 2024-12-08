class ZCaptchaClient {
    constructor() {
        this.captchaImage = document.getElementById('captchaImage');
        this.refreshButton = document.getElementById('refreshCaptcha');
        this.verifyButton = document.getElementById('verifyCaptcha');
        this.captchaInput = document.getElementById('captchaInput');
        this.timerElement = document.getElementById('timer');
        this.resultElement = document.getElementById('result');
        this.difficultyInfo = document.getElementById('difficultyInfo');
        this.refreshCountElement = document.getElementById('refreshCount');

        this.timer = null;
        this.startTime = null;
        this.refreshCount = 0;
        this.maxRefreshAttempts = 3;

        this.initEventListeners();
        this.generateCaptcha();
    }

    initEventListeners() {
        this.refreshButton.addEventListener('click', () => this.generateCaptcha());
        this.verifyButton.addEventListener('click', () => this.verifyCaptcha());
        
        this.captchaInput.addEventListener('keypress', (event) => {
            if (event.key === 'Enter') {
                this.verifyCaptcha();
            }
        });
    }

    async generateCaptcha() {
        if (this.refreshCount >= this.maxRefreshAttempts) {
            this.showErrorToast(`Maximum refresh limit (${this.maxRefreshAttempts}) reached`);
            return;
        }

        try {
            const response = await fetch('../backend/generate_captcha.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ refreshCount: this.refreshCount })
            });
            
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
            }
            
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Response is not JSON');
            }
            
            const data = await response.json();
            
            if (!data || !data.image) {
                throw new Error('Invalid captcha generation response');
            }
            
            this.captchaImage.src = data.image;
            this.startTimer();
            this.clearResult();

            this.refreshCount++;
            this.updateRefreshCount();
            this.updateDifficultyInfo(data.difficulty || 'medium');
        } catch (error) {
            console.error('Captcha generation error:', error);
            this.showErrorToast('Failed to generate captcha. Please try again.');
        }
    }

    updateRefreshCount() {
        this.refreshCountElement.textContent = `Refresh: ${this.refreshCount}/${this.maxRefreshAttempts}`;
        if (this.refreshCount >= this.maxRefreshAttempts) {
            this.refreshButton.disabled = true;
            this.refreshButton.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    async verifyCaptcha() {
        const captchaCode = this.captchaInput.value;
        const returnUrl = document.getElementById('returnUrl').value;
        
        if (!captchaCode.trim()) {
            this.showErrorToast('Please enter the captcha code');
            return;
        }
        
        try {
            const response = await fetch('../backend/verify_captcha.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    captchaCode,
                    refreshCount: this.refreshCount,
                    returnUrl: returnUrl
                })
            });

            const result = await response.json();

            if (result.success) {
                this.showSuccessModal(result.difficulty);
                setTimeout(() => {
                    window.location.href = returnUrl;
                }, 2000);
            } else {
                this.showErrorModal(result.message);
            }
        } catch (error) {
            console.error('Verification error:', error);
            this.showErrorToast('Verification failed. Please try again.');
        }
    }
    
    showSuccessModal(difficulty) {
        const modal = this.createModal(
            'Verification Successful', 
            `Captcha verified!`, 
            'text-green-600'
        );
        document.body.appendChild(modal);
        setTimeout(() => document.body.removeChild(modal), 2000);
    }

    showErrorModal(message) {
        const modal = this.createModal(
            'Captcha Failed', 
            message || 'Verification unsuccessful. Please try again.', 
            'text-red-600'
        );
        
        document.body.appendChild(modal);
        setTimeout(() => {
            document.body.removeChild(modal);
            this.generateCaptcha();
            this.captchaInput.value = '';
        }, 3000);
    }

    updateDifficultyInfo(difficulty) {
        const difficultyMap = {
            'easy': 'Easy',
            'medium': 'Medium',
            'hard': 'Hard'
        };
        this.difficultyInfo.innerHTML = `
            <i class="fas fa-chart-bar mr-2"></i>
            Difficulty Level: ${difficultyMap[difficulty]}
        `;
    }

    createModal(title, message, textColorClass) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-white/10 backdrop-blur-lg p-8 rounded-lg shadow-xl text-center max-w-sm w-full border border-white/20">
                <h2 class="text-2xl font-bold ${textColorClass} mb-4">${title}</h2>
                <p class="text-white/80">${message}</p>
            </div>
        `;
        return modal;
    }

    showErrorToast(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-red-600/80 backdrop-blur-lg text-white px-4 py-2 rounded-lg shadow-lg z-50';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => document.body.removeChild(toast), 3000);
    }

    startTimer() {
        if (this.timer) clearInterval(this.timer);
        
        const duration = 30;
        this.startTime = Date.now();

        this.timer = setInterval(() => {
            const elapsed = Math.floor((Date.now() - this.startTime) / 1000);
            const remaining = duration - elapsed;

            if (remaining <= 0) {
                clearInterval(this.timer);
                this.timerElement.textContent = 'Time expired!';
                this.showErrorModal('Time expired. Please try again.');
                this.generateCaptcha();
                return;
            }

            this.timerElement.textContent = `Remaining Time: ${remaining} seconds`;
        }, 1000);
    }

    clearResult() {
        this.timerElement.textContent = '';
        this.captchaInput.value = '';
    }
    
}

document.addEventListener('DOMContentLoaded', () => {
    new ZCaptchaClient();
});