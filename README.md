
<html lang="en">
<head>
   <meta charset="UTF-8">
</head>
<body>
   <h1>🛡️ Z-Captcha: Open Source Adaptive CAPTCHA System</h1>

   <h2>🌟 Project Overview</h2>
   <p>Z-Captcha is a sophisticated, customizable, and secure CAPTCHA (Completely Automated Public Turing test to tell Computers and Humans Apart) solution designed to protect web applications from automated bot attacks while providing a user-friendly experience.</p>

   <h2>✨ Key Features</h2>

   <h3>🔐 Security Features</h3>
   <ul>
       <li>Dynamic difficulty levels (Easy, Medium, Hard)</li>
       <li>Intelligent IP blocking mechanism</li>
       <li>Session-based verification</li>
       <li>Time-limited CAPTCHA codes</li>
       <li>Secure cookie-based verification</li>
       <li>Configurable refresh and attempt limits</li>
   </ul>

   <h3>🎨 User Experience</h3>
   <ul>
       <li>Responsive and modern UI</li>
       <li>Adaptive difficulty generation</li>
       <li>Refresh and retry options</li>
       <li>Detailed error handling</li>
       <li>Elegant modal notifications</li>
   </ul>

   <h3>💡 Flexible Configuration</h3>
   <ul>
       <li>Easily customizable through JSON configuration</li>
       <li>Adjustable difficulty parameters</li>
       <li>Configurable security settings</li>
       <li>Customizable redirect URLs</li>
   </ul>

   <h2>🚀 Prerequisites</h2>
   <ul>
       <li>PHP 7.4+</li>
       <li>GD Extension</li>
       <li>Web Browser with JavaScript support</li>
       <li>Modern web server (Apache/Nginx)</li>
   </ul>

   <h2>📦 Installation</h2>

   <h3>1. Clone the Repository</h3>
   <pre><code>git clone https://github.com/yourusername/z-captcha.git
cd z-captcha</code></pre>

   <h3>2. Configuration</h3>
   <p>Edit <code>config/config.json</code> to customize CAPTCHA behavior:</p>

   <pre><code>{
   "captcha_settings": {
       "difficulty_levels": {
           "easy": { "length": 4, "noise_level": 1, "complexity": 3 },
           "medium": { "length": 5, "noise_level": 2, "complexity": 2 },
           "hard": { "length": 6, "noise_level": 3, "complexity": 1 }
       },
       "timeout_seconds": 30,
       "max_attempts": 3,
       "max_refresh_attempts": 3
   },
   "security": {
       "block_duration": 300,
       "verification_cookie_duration": 3600
   },
   "redirect": {
       "success_url": "../return.html",
       "default_url": "../index.php"
   }
}</code></pre>

   <h3>3. Server Setup</h3>
   <ul>
       <li>Enable PHP sessions</li>
       <li>Configure web server to point to project directory</li>
       <li>Ensure GD extension is enabled</li>
   </ul>

   <h2>🛠️ How It Works</h2>
   <ol>
       <li>When a user visits a protected page, Z-Captcha generates a dynamic CAPTCHA</li>
       <li>User must solve the CAPTCHA within the time limit</li>
       <li>Successful verification grants temporary access via secure cookies</li>
       <li>Failed attempts trigger progressive security measures</li>
   </ol>

   <h2>🔒 Security Mechanisms</h2>
   <ul>
       <li>Configurable difficulty levels</li>
       <li>Random background image selection</li>
       <li>Text distortion and noise</li>
       <li>IP-based blocking</li>
       <li>Session management</li>
       <li>Secure, HTTP-only cookies</li>
   </ul>

   <h2>📊 Difficulty Levels</h2>
   <table>
       <thead>
           <tr>
               <th>Level</th>
               <th>Code Length</th>
               <th>Noise Level</th>
               <th>Complexity</th>
           </tr>
       </thead>
       <tbody>
           <tr>
               <td>Easy</td>
               <td>4 chars</td>
               <td>Low</td>
               <td>High</td>
           </tr>
           <tr>
               <td>Medium</td>
               <td>5 chars</td>
               <td>Medium</td>
               <td>Medium</td>
           </tr>
           <tr>
               <td>Hard</td>
               <td>6 chars</td>
               <td>High</td>
               <td>Low</td>
           </tr>
       </tbody>
   </table>

   <h2>🧩 Components</h2>
   <ul>
       <li><code>generate_captcha.php</code>: CAPTCHA image generation</li>
       <li><code>verify_captcha.php</code>: CAPTCHA code verification</li>
       <li><code>script.js</code>: Client-side CAPTCHA handling</li>
       <li><code>index.php</code>: Protected page access control</li>
   </ul>

   <h2>📝 Example Usage</h2>
   <pre><code>// In your protected page
if (!$isCaptchaVerified) {
   redirectToCaptchaPage();
}
// Access granted after successful verification</code></pre>

   <h2>🤝 Contributing</h2>
   <ol>
       <li>Fork the repository</li>
       <li>Create your feature branch</li>
       <li>Commit your changes</li>
       <li>Push to the branch</li>
       <li>Create a Pull Request</li>
   </ol>

   <h2>🔧 Customization</h2>
   <p>Easily modify:</p>
   <ul>
       <li>Difficulty settings</li>
       <li>Security thresholds</li>
       <li>Styling</li>
       <li>Redirect URLs</li>
   </ul>

   <h2>⚠️ Limitations</h2>
   <ul>
       <li>Requires JavaScript</li>
       <li>Depends on PHP GD extension</li>
       <li>Not recommended for high-traffic sites without optimization</li>
   </ul>

   <h2>📜 License</h2>
   <p>MIT License - See <code>LICENSE</code> file for details</p>

   <h2>🌐 Support</h2>
   <ul>
       <li>GitHub Issues</li>
       <li>Email: spexdw@icloud.com</li>
       <li>Website: www.z-clients.org</li>
   </ul>

   <h2>🚨 Disclaimer</h2>
   <p>Use responsibly. Not a complete security solution, but an additional layer of protection.</p>

   <hr>

   <p><strong>Created with ❤️ by SpeX - Z-Clients</strong></p>
</body>
</html>
