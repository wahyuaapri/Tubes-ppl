// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    // Theme Toggle
    const themeToggle = document.querySelector('.theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const body = document.body;
    
    // Check for saved theme or prefer-color-scheme
    const savedTheme = localStorage.getItem('theme') || 'light';
    if (savedTheme === 'dark') {
        body.classList.add('dark-mode');
        themeIcon.classList.remove('fa-moon');
        themeIcon.classList.add('fa-sun');
    }
    
    themeToggle.addEventListener('click', function() {
        body.classList.toggle('dark-mode');
        
        if (body.classList.contains('dark-mode')) {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
            localStorage.setItem('theme', 'dark');
        } else {
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
            localStorage.setItem('theme', 'light');
        }
    });
    // Tab Navigation System
function setupTabNavigation() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Update active button
            tabButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Show corresponding tab content
            tabPanes.forEach(pane => {
                pane.classList.remove('active');
                if (pane.id === `${tabId}-tab`) {
                    pane.classList.add('active');
                    
                    // Initialize charts when market tab is shown
                    if (tabId === 'market') {
                        setTimeout(initSparklineCharts, 100);
                    }
                    
                    // Initialize portfolio calculator when portfolio tab is shown
                    if (tabId === 'portfolio') {
                        setTimeout(calculatePortfolio, 100);
                    }
                }
            });
            
            // Update URL hash without scrolling
            history.pushState(null, null, `#${tabId}`);
        });
    });
    
    // Check URL hash on load
    const hash = window.location.hash.substring(1);
    const validTabs = ['market', 'portfolio', 'trending', 'about'];
    
    if (validTabs.includes(hash)) {
        const tabButton = document.querySelector(`.tab-btn[data-tab="${hash}"]`);
        if (tabButton) {
            tabButton.click();
        }
    }
}

// Update DOMContentLoaded event listener
document.addEventListener('DOMContentLoaded', function() {
    // ... kode sebelumnya (theme toggle, view toggle, dll) ...
    
    // Setup tab navigation
    setupTabNavigation();
    
    // Initialize portfolio calculator on load
    calculatePortfolio();
    
    // ... kode selanjutnya ...
});
    
    
    // Table/Grid View Toggle
    const viewButtons = document.querySelectorAll('.view-btn');
    const cryptoTable = document.getElementById('crypto-table');
    const cryptoGrid = document.getElementById('crypto-grid');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const view = this.getAttribute('data-view');
            
            // Update active button
            viewButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Show/hide views
            if (view === 'table') {
                cryptoTable.closest('.table-container').style.display = 'block';
                cryptoGrid.style.display = 'none';
            } else {
                cryptoTable.closest('.table-container').style.display = 'none';
                cryptoGrid.style.display = 'block';
            }
        });
    });
    
    // Search Filter
    const searchInput = document.getElementById('search-crypto');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#crypto-table tbody tr, .crypto-card');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Initialize Sparkline Charts
    initSparklineCharts();
    
    // Portfolio Simulator
    setupPortfolioSimulator();
    
    // Smooth Scrolling for Anchor Links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            if (href === '#') return;
            
            e.preventDefault();
            const targetElement = document.querySelector(href);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
                
                // Update active nav link
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.classList.remove('active');
                });
                this.classList.add('active');
            }
        });
    });
    
    // Update active nav link on scroll
    window.addEventListener('scroll', function() {
        const sections = document.querySelectorAll('section[id]');
        const scrollPos = window.scrollY + 100;
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            const sectionId = section.getAttribute('id');
            
            if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${sectionId}`) {
                        link.classList.add('active');
                    }
                });
            }
        });
    });
});

// Initialize Sparkline Charts
function initSparklineCharts() {
    const sparklineElements = document.querySelectorAll('.sparkline-data');
    
    sparklineElements.forEach(element => {
        const data = JSON.parse(element.textContent);
        const canvasId = element.previousElementSibling.id;
        const canvas = document.getElementById(canvasId);
        
        if (!canvas) return;
        
        // Get the last 30 data points for the sparkline
        const sparklineData = data.slice(-30);
        const ctx = canvas.getContext('2d');
        
        // Determine if the trend is positive or negative
        const firstValue = sparklineData[0];
        const lastValue = sparklineData[sparklineData.length - 1];
        const isPositive = lastValue >= firstValue;
        
        // Create gradient
        const gradient = ctx.createLinearGradient(0, 0, canvas.width, 0);
        if (isPositive) {
            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
            gradient.addColorStop(1, 'rgba(16, 185, 129, 0.8)');
        } else {
            gradient.addColorStop(0, 'rgba(239, 68, 68, 0.2)');
            gradient.addColorStop(1, 'rgba(239, 68, 68, 0.8)');
        }
        
        // Find min and max for scaling
        const min = Math.min(...sparklineData);
        const max = Math.max(...sparklineData);
        const range = max - min || 1; // Avoid division by zero
        
        // Calculate points
        const points = sparklineData.map((value, index) => {
            const x = (index / (sparklineData.length - 1)) * canvas.width;
            const y = canvas.height - ((value - min) / range) * canvas.height;
            return { x, y };
        });
        
        // Draw line
        ctx.beginPath();
        ctx.moveTo(points[0].x, points[0].y);
        for (let i = 1; i < points.length; i++) {
            ctx.lineTo(points[i].x, points[i].y);
        }
        
        ctx.strokeStyle = isPositive ? '#10b981' : '#ef4444';
        ctx.lineWidth = 2;
        ctx.stroke();
        
        // Fill area under the line
        ctx.lineTo(points[points.length - 1].x, canvas.height);
        ctx.lineTo(points[0].x, canvas.height);
        ctx.closePath();
        ctx.fillStyle = gradient;
        ctx.fill();
    });
}

// Portfolio Simulator
function setupPortfolioSimulator() {
    const investmentRange = document.getElementById('investment-range');
    const investmentInput = document.getElementById('investment-input');
    const cryptoSelect = document.getElementById('crypto-select');
    const periodSelect = document.getElementById('period');
    const calculateBtn = document.getElementById('calculate-btn');
    
    const portfolioValueEl = document.getElementById('portfolio-value');
    const initialInvestmentEl = document.getElementById('initial-investment');
    const estimatedProfitEl = document.getElementById('estimated-profit');
    const roiEl = document.getElementById('roi');
    
    // Sync range and input
    investmentRange.addEventListener('input', function() {
        investmentInput.value = this.value;
    });
    
    investmentInput.addEventListener('input', function() {
        let value = parseInt(this.value);
        if (value < 100) value = 100;
        if (value > 10000) value = 10000;
        
        this.value = value;
        investmentRange.value = value;
    });
    
    // Calculate portfolio
    calculateBtn.addEventListener('click', calculatePortfolio);
    
    // Also calculate on any input change
    investmentRange.addEventListener('change', calculatePortfolio);
    investmentInput.addEventListener('change', calculatePortfolio);
    cryptoSelect.addEventListener('change', calculatePortfolio);
    periodSelect.addEventListener('change', calculatePortfolio);
    
    // Initial calculation
    calculatePortfolio();
    
    function calculatePortfolio() {
        const investment = parseFloat(investmentInput.value);
        const selectedOption = cryptoSelect.options[cryptoSelect.selectedIndex];
        const cryptoPrice = parseFloat(selectedOption.getAttribute('data-price'));
        const period = parseInt(periodSelect.value);
        
        // Simulate growth based on period (this is just a simulation)
        // In reality, this would require historical data and more complex calculations
        let growthRate;
        
        // Simple simulation: assign random growth based on period
        // These are completely fictional rates for demonstration
        const growthRates = {
            3: { min: -0.1, max: 0.2 },   // 3 months: -10% to +20%
            6: { min: -0.05, max: 0.4 },  // 6 months: -5% to +40%
            12: { min: 0, max: 1.0 },     // 12 months: 0% to +100%
            24: { min: 0.2, max: 3.0 }    // 24 months: +20% to +300%
        };
        
        const rateRange = growthRates[period];
        // Generate a deterministic "random" rate based on crypto and period
        const seed = selectedOption.value.length + period;
        const randomFactor = (Math.sin(seed * 10) + 1) / 2; // Pseudo-random between 0-1
        growthRate = rateRange.min + (rateRange.max - rateRange.min) * randomFactor;
        
        // Calculate final value
        const finalValue = investment * (1 + growthRate);
        const profit = finalValue - investment;
        const roi = (profit / investment) * 100;
        
        // Update UI
        portfolioValueEl.textContent = `$${finalValue.toFixed(2)}`;
        initialInvestmentEl.textContent = `$${investment.toFixed(2)}`;
        
        estimatedProfitEl.textContent = `$${profit.toFixed(2)}`;
        estimatedProfitEl.className = profit >= 0 ? 'positive' : 'negative';
        
        roiEl.textContent = `${roi >= 0 ? '+' : ''}${roi.toFixed(2)}%`;
        roiEl.className = roi >= 0 ? 'positive' : 'negative';
    }
}