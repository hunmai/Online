<?php
// --- အပိုင်း ၁: PHP Proxy ---
if (isset($_GET['fetch_url'])) {
    
    $allowed_domains = [
        'jp01.dnstt.site',
        'sg01.dnstt.site',
        'shan11.myshan.site',
        'shan12.myshan.site',
        'shan13.myshan.site',
        'shan14.myshan.site',
        'shan15.myshan.site',
        'shan16.myshan.site',
        'shan17.myshan.site',
        'shan18.myshan.site',
        'shan19.myshan.site',
        'shan20.myshan.site'
    ];
    
    $url_to_fetch = $_GET['fetch_url'];
    $domain = parse_url($url_to_fetch, PHP_URL_HOST);

    if (in_array($domain, $allowed_domains)) {
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'ignore_errors' => true
            ]
        ]);
        
        $response = @file_get_contents($url_to_fetch, false, $context);
        
        if ($response !== false && is_numeric(trim($response))) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'online' => intval(trim($response))]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error']);
        }
        
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Domain not allowed']);
    }
    
    exit;
}

header("Content-Security-Policy: frame-ancestors *;");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHAN VPN - Server Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* กำหนดฟอนต์จาก URL ที่ให้มา */
        @font-face {
            font-family: 'MyanmarFont';
            src: url('https://shanvpn.site/font/myfont.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --warning-color: #f8961e;
            --danger-color: #f72585;
            --dark-color: #212529;
            --light-color: #f8f9fa;
            --bg-color: #ffffff;
            --card-bg: rgba(255, 255, 255, 0.95);
            --card-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            --card-border: 1px solid #e0e0e0;
            
            --dark-primary: #7209b7;
            --dark-secondary: #560bad;
            --dark-bg-color: #1e1e2e;
            --dark-card-bg: rgba(30, 30, 46, 0.9);
            --dark-card-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            --dark-card-border: 1px solid #3a3a4a;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'MyanmarFont', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: var(--bg-color);
            color: var(--dark-color);
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        body.dark-mode {
            background: var(--dark-bg-color);
            color: var(--light-color);
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            padding: 15px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        body.dark-mode .navbar {
            background: rgba(30, 30, 46, 0.9) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            border-bottom: 1px solid #3a3a4a;
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.5rem;
        }

        .navbar-brand img {
            width: 40px;
            height: auto;
        }

        .theme-switch-wrapper {
            display: flex;
            align-items: center;
        }

        .theme-switch {
            display: inline-block;
            height: 24px;
            position: relative;
            width: 50px;
            margin: 0 10px;
        }

        .theme-switch input { 
            display: none; 
        }

        .slider {
            background-color: #ccc;
            bottom: 0;
            cursor: pointer;
            left: 0;
            position: absolute;
            right: 0;
            top: 0;
            transition: .4s;
        }

        .slider:before {
            background-color: #fff;
            bottom: 4px;
            content: "";
            height: 16px;
            left: 4px;
            position: absolute;
            transition: .4s;
            width: 16px;
        }

        input:checked + .slider {
            background-color: var(--primary-color);
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .slider.round { 
            border-radius: 34px; 
        }

        .slider.round:before { 
            border-radius: 50%; 
        }

        .hero-section {
            padding: 30px 0 20px;
            text-align: center;
            color: var(--dark-color);
        }

        .hero-section h1 {
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 2.2rem;
        }

        .hero-section p {
            font-size: 1rem;
            max-width: 600px;
            margin: 0 auto 20px;
            opacity: 0.9;
        }

        body.dark-mode .hero-section {
            color: var(--light-color);
        }

        /* Total Users Card - ปรับแต่งพิเศษ */
        .total-users-card {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            padding: 25px;
            transition: all 0.3s ease;
            border: var(--card-border);
            text-align: center;
            max-width: 400px;
            margin: 0 auto 30px;
            position: relative;
            overflow: hidden;
        }

        body.dark-mode .total-users-card {
            background: var(--dark-card-bg);
            box-shadow: var(--dark-card-shadow);
            border: var(--dark-card-border);
        }

        .total-users-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }

        body.dark-mode .total-users-card:hover {
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
        }

        .total-users-card h3 {
            font-size: 1.1rem;
            margin-bottom: 15px;
            color: var(--primary-color);
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        body.dark-mode .total-users-card h3 {
            color: var(--success-color);
        }

        .total-users-card .number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--success-color);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin: 10px 0;
        }

        .total-users-card .number i {
            font-size: 2.2rem;
            color: var(--primary-color);
        }

        body.dark-mode .total-users-card .number i {
            color: var(--success-color);
        }

        .total-users-card .subtitle {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 5px;
        }

        body.dark-mode .total-users-card .subtitle {
            color: rgba(255, 255, 255, 0.7);
        }

        .servers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .server-card {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            transition: all 0.3s ease;
            border: var(--card-border);
            position: relative;
            overflow: hidden;
        }

        body.dark-mode .server-card {
            background: var(--dark-card-bg);
            box-shadow: var(--dark-card-shadow);
            border: var(--dark-card-border);
        }

        .server-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        body.dark-mode .server-card:hover {
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
        }

        .server-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .server-name {
            font-weight: 600;
            font-size: 1.2rem;
            color: var(--dark-color);
        }

        body.dark-mode .server-name {
            color: var(--light-color);
        }

        .server-status {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .dot-online {
            background-color: var(--success-color);
            box-shadow: 0 0 10px var(--success-color);
        }

        .dot-warning {
            background-color: var(--warning-color);
            box-shadow: 0 0 10px var(--warning-color);
        }

        .dot-danger {
            background-color: var(--danger-color);
            box-shadow: 0 0 10px var(--danger-color);
        }

        .dot-loading {
            background-color: #adb5bd;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .status-text {
            font-weight: 500;
            font-size: 0.9rem;
        }

        .online-normal {
            color: var(--success-color);
        }

        .online-warning {
            color: var(--warning-color);
        }

        .online-danger {
            color: var(--danger-color);
        }

        .offline {
            color: #6c757d;
        }

        .server-details {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        body.dark-mode .server-details {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .user-count {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .online-number {
            color: var(--success-color);
        }

        .limit-number {
            color: var(--danger-color);
        }

        .load-indicator {
            font-size: 0.85rem;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 500;
        }

        .load-normal {
            background-color: rgba(76, 201, 240, 0.2);
            color: var(--success-color);
        }

        .load-busy {
            background-color: rgba(248, 150, 30, 0.2);
            color: var(--warning-color);
        }

        .load-high {
            background-color: rgba(247, 37, 133, 0.2);
            color: var(--danger-color);
        }

        /* Progress Bar Styles */
        .server-usage {
            margin-top: 15px;
        }

        .usage-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.85rem;
        }

        .usage-label {
            color: #6c757d;
            font-weight: 500;
        }

        body.dark-mode .usage-label {
            color: rgba(255, 255, 255, 0.7);
        }

        .usage-percent {
            color: var(--primary-color);
            font-weight: 600;
        }

        body.dark-mode .usage-percent {
            color: var(--success-color);
        }

        .progress-container {
            height: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
            overflow: hidden;
            position: relative;
        }

        body.dark-mode .progress-container {
            background-color: #3a3a4a;
        }

        .progress-bar {
            height: 100%;
            border-radius: 5px;
            transition: width 0.5s ease;
            position: relative;
            overflow: hidden;
        }

        .progress-normal {
            background: linear-gradient(90deg, var(--success-color), #4cd9f0);
        }

        .progress-warning {
            background: linear-gradient(90deg, var(--warning-color), #f9a845);
        }

        .progress-danger {
            background: linear-gradient(90deg, var(--danger-color), #f93785);
        }

        .progress-loading {
            background: linear-gradient(90deg, #adb5bd, #ced4da);
            animation: pulse 1.5s infinite;
        }

        .progress-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 5px,
                rgba(255, 255, 255, 0.2) 5px,
                rgba(255, 255, 255, 0.2) 10px
            );
        }

        .last-updated {
            text-align: center;
            margin-top: 20px;
            color: rgba(0, 0, 0, 0.7);
            font-size: 0.9rem;
        }

        body.dark-mode .last-updated {
            color: rgba(255, 255, 255, 0.7);
        }

        .refresh-btn {
            background: var(--primary-color);
            border: 1px solid var(--primary-color);
            color: white;
            padding: 8px 16px;
            border-radius: 30px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .refresh-btn:hover {
            background: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }

        .footer {
            padding: 30px 0;
            text-align: center;
            color: rgba(0, 0, 0, 0.7);
            font-size: 0.9rem;
            border-top: 1px solid #e0e0e0;
        }

        body.dark-mode .footer {
            color: rgba(255, 255, 255, 0.7);
            border-top: 1px solid #3a3a4a;
        }

        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 1.8rem;
            }
            
            .servers-grid {
                grid-template-columns: 1fr;
            }
            
            .total-users-card .number {
                font-size: 2rem;
            }
            
            .navbar-brand {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="https://raw.githubusercontent.com/hunmai/Icon/refs/heads/main/icon_shan.png" alt="SHAN VPN Logo">
                SHAN VPN
            </a>
            <div class="theme-switch-wrapper">
                <i class="bi bi-sun-fill"></i>
                <label class="theme-switch">
                    <input type="checkbox" id="theme-toggle">
                    <span class="slider round"></span>
                </label>
                <i class="bi bi-moon-fill"></i>
            </div>
        </div>
    </nav>

    <div class="hero-section">
        <div class="container">
            <h1>SHAN VPN Server Status</h1>
            
            <div class="total-users-card">
                <h3><i class="bi bi-people-fill"></i> Total Online Users</h3>
                <div class="number" id="total-users-display">
                    <i class="bi bi-person-check-fill"></i>
                    <span>Loading...</span>
                </div>
                <div class="subtitle">Across all servers</div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="servers-grid" id="servers-grid">
            <!-- Server cards will be dynamically inserted here -->
        </div>
        
        <div class="text-center mt-4">
            <button class="refresh-btn" id="refresh-btn">
                <i class="bi bi-arrow-clockwise"></i> Refresh Status
            </button>
            <p class="last-updated mt-3" id="last-updated-text">Last updated: Just now</p>
        </div>
    </div>

    <div class="footer">
        <div class="container">
            <p>© 2023 SHAN VPN. All rights reserved.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Server list with limits
        const servers = {
        	'🇸🇬 SG-01': { 
                url: 'http://sg01.dnstt.site:82/server/online',
                limit: 250
            },
            '🇯🇵 JP-01': { 
                url: 'http://jp01.dnstt.site:82/server/online',
                limit: 250
            },
            '🇹🇭 TH-01': { 
                url: 'http://shan11.myshan.site:82/server/online',
                limit: 250
            },
            '🇹🇭 TH-02': { 
                url: 'http://shan12.myshan.site:82/server/online',
                limit: 250
            },
            '🇹🇭 TH-03': { 
                url: 'http://shan13.myshan.site:82/server/online',
                limit: 250
            },
            '🇹🇭 TH-04': { 
                url: 'http://shan14.myshan.site:82/server/online',
                limit: 250
            },
            '🇹🇭 TH-05': { 
                url: 'http://shan15.myshan.site:82/server/online',
                limit: 250
            },
            '🇹🇭 TH-06': { 
                url: 'http://shan16.myshan.site:82/server/online',
                limit: 250
            },
            '🇹🇭 TH-07': { 
                url: 'http://shan17.myshan.site:82/server/online',
                limit: 250
            },
            '🇹🇭 TH-08': { 
                url: 'http://shan18.myshan.site:82/server/online',
                limit: 250
            },
            '🇹🇭 TH-09': { 
                url: 'http://shan19.myshan.site:82/server/online',
                limit: 250
            },
            '🇹🇭 TH-10': { 
                url: 'http://shan20.myshan.site:82/server/online',
                limit: 250
            }
        };

        // Global variables
        let totalOnlineCount = 0;
        let serversResponded = 0;
        const serverGroupCount = Object.keys(servers).length;

        // DOM elements
        const serversGrid = document.getElementById('servers-grid');
        const totalDisplay = document.getElementById('total-users-display');
        const lastUpdatedText = document.getElementById('last-updated-text');
        const refreshBtn = document.getElementById('refresh-btn');
        const themeToggle = document.getElementById('theme-toggle');
        const body = document.body;

        // Theme Toggle Logic
        function setTheme(theme) {
            if (theme === 'dark') {
                body.classList.add('dark-mode');
                themeToggle.checked = true;
                localStorage.setItem('theme', 'dark');
            } else {
                body.classList.remove('dark-mode');
                themeToggle.checked = false;
                localStorage.setItem('theme', 'light');
            }
        }

        themeToggle.addEventListener('change', () => {
            if (themeToggle.checked) {
                setTheme('dark');
            } else {
                setTheme('light');
            }
        });

        // Load saved theme from localStorage
        const savedTheme = localStorage.getItem('theme') || 'light';
        setTheme(savedTheme);

        // Format time for last updated
        function formatTime() {
            const now = new Date();
            return now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        }

        // Update last updated time
        function updateLastUpdated() {
            const timeString = formatTime();
            lastUpdatedText.textContent = `Last updated: ${timeString}`;
        }

        // Calculate usage percentage
        function calculateUsagePercentage(onlineCount, limit) {
            return Math.min(100, Math.round((onlineCount / limit) * 100));
        }

        // Get progress bar class based on usage percentage
        function getProgressBarClass(percentage) {
            if (percentage <= 60) {
                return 'progress-normal';
            } else if (percentage <= 85) {
                return 'progress-warning';
            } else {
                return 'progress-danger';
            }
        }

        // Create server card HTML
        function createServerCard(serverName, status, onlineCount, limit, loadStatus) {
            let statusClass = '';
            let dotClass = '';
            let statusText = '';
            let loadClass = '';
            let loadText = '';
            let usagePercentage = 0;
            let progressBarClass = '';
            let progressBarHtml = '';

            if (status === 'loading') {
                statusClass = 'offline';
                dotClass = 'dot-loading';
                statusText = 'Checking...';
                loadClass = '';
                loadText = '';
                usagePercentage = 0;
                progressBarClass = 'progress-loading';
                progressBarHtml = `
                    <div class="server-usage">
                        <div class="usage-header">
                            <span class="usage-label">Usage</span>
                            <span class="usage-percent">?</span>
                        </div>
                        <div class="progress-container">
                            <div class="progress-bar ${progressBarClass}" style="width: 0%"></div>
                        </div>
                    </div>
                `;
            } else if (status === 'online') {
                if (loadStatus === 'normal') {
                    statusClass = 'online-normal';
                    dotClass = 'dot-online';
                    statusText = 'Online';
                    loadClass = 'load-normal';
                    loadText = 'Normal';
                } else if (loadStatus === 'busy') {
                    statusClass = 'online-warning';
                    dotClass = 'dot-warning';
                    statusText = 'Online';
                    loadClass = 'load-busy';
                    loadText = 'Busy';
                } else {
                    statusClass = 'online-danger';
                    dotClass = 'dot-danger';
                    statusText = 'Online';
                    loadClass = 'load-high';
                    loadText = 'High Load';
                }
                
                usagePercentage = calculateUsagePercentage(onlineCount, limit);
                progressBarClass = getProgressBarClass(usagePercentage);
                progressBarHtml = `
                    <div class="server-usage">
                        <div class="usage-header">
                            <span class="usage-label">Usage</span>
                            <span class="usage-percent">${usagePercentage}%</span>
                        </div>
                        <div class="progress-container">
                            <div class="progress-bar ${progressBarClass}" style="width: ${usagePercentage}%">
                                <div class="progress-pattern"></div>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                statusClass = 'offline';
                dotClass = 'dot-danger';
                statusText = 'Offline';
                loadClass = '';
                loadText = 'Unavailable';
                progressBarHtml = `
                    <div class="server-usage">
                        <div class="usage-header">
                            <span class="usage-label">Usage</span>
                            <span class="usage-percent">0%</span>
                        </div>
                        <div class="progress-container">
                            <div class="progress-bar progress-danger" style="width: 0%"></div>
                        </div>
                    </div>
                `;
            }

            // Format user count display with colors
            let userCountDisplay = 'No data';
            if (status === 'online') {
                userCountDisplay = `<span class="online-number">${onlineCount}</span><span class="limit-number">/${limit}</span>`;
            } else if (status === 'loading') {
                userCountDisplay = `<span class="online-number">?</span><span class="limit-number">/${limit}</span>`;
            }

            return `
                <div class="server-card">
                    <div class="server-header">
                        <div class="server-name">${serverName}</div>
                        <div class="server-status">
                            <span class="status-dot ${dotClass}"></span>
                            <span class="status-text ${statusClass}">${statusText}</span>
                        </div>
                    </div>
                    <div class="server-details">
                        <div class="user-count">${userCountDisplay}</div>
                        ${loadText ? `<div class="load-indicator ${loadClass}">${loadText}</div>` : ''}
                    </div>
                    ${progressBarHtml}
                </div>
            `;
        }

        // Fetch all server statuses
        function fetchAllServerStatuses() {
            // Reset counters
            totalOnlineCount = 0;
            serversResponded = 0;
            
            // Clear and reinitialize server cards with loading state
            serversGrid.innerHTML = '';
            Object.entries(servers).forEach(([serverName, serverData]) => {
                serversGrid.innerHTML += createServerCard(serverName, 'loading', 0, serverData.limit, '');
            });

            // Update displays
            totalDisplay.innerHTML = `<i class="bi bi-person-check-fill"></i><span>Loading...</span>`;

            // Fetch data for each server
            Object.entries(servers).forEach(([serverName, serverData]) => {
                fetchServerStatus(serverName, serverData);
            });
        }

        // Fetch server status
        async function fetchServerStatus(serverName, serverData) {
            const { url, limit } = serverData;
            let onlineCount = 0;
            let connectFailed = true;

            try {
                const cacheBustUrl = `${url}${url.includes('?') ? '&' : '?'}t=${new Date().getTime()}`;
                const response = await fetch(`?fetch_url=${encodeURIComponent(cacheBustUrl)}`);
                
                if (response.ok) {
                    const result = await response.json();
                    if (result.status === 'success') {
                        onlineCount = result.online;
                        connectFailed = false;
                    }
                }
            } catch (error) {
                console.error(`Error fetching ${serverName}:`, error);
            }

            // Determine load status based on online count
            let loadStatus = 'normal';
            if (onlineCount <= 150) {
                loadStatus = 'normal';      // สีเขียว (0-150)
            } else if (onlineCount <= 220) {
                loadStatus = 'busy';        // สีเหลือง (151-220)
            } else {
                loadStatus = 'high';        // สีแดง (221 ขึ้นไป)
            }

            // Update counters
            if (!connectFailed) {
                totalOnlineCount += onlineCount;
            }

            // Update the specific server card
            updateServerCard(serverName, connectFailed ? 'offline' : 'online', onlineCount, limit, loadStatus);

            // Check if all servers have responded
            serversResponded++;
            if (serversResponded === serverGroupCount) {
                updateTotalDisplay();
                updateLastUpdated();
            }
        }

        // Update a specific server card
        function updateServerCard(serverName, status, onlineCount, limit, loadStatus) {
            const serverCards = document.querySelectorAll('.server-card');
            for (let card of serverCards) {
                const nameElement = card.querySelector('.server-name');
                if (nameElement && nameElement.textContent === serverName) {
                    card.outerHTML = createServerCard(serverName, status, onlineCount, limit, loadStatus);
                    break;
                }
            }
        }

        // Update total display
        function updateTotalDisplay() {
            totalDisplay.innerHTML = `<i class="bi bi-person-check-fill"></i><span>${totalOnlineCount}</span>`;
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', () => {
            fetchAllServerStatuses();
            
            // Set up auto-refresh every 30 seconds
            setInterval(fetchAllServerStatuses, 30000);
            
            // Manual refresh button
            refreshBtn.addEventListener('click', fetchAllServerStatuses);
        });
    </script>
</body>
</html>