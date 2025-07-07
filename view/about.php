<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - BCAS Campus</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: white;
            color: black;
            overflow-x: hidden;
        }
        
        .hero-section {
            height: 100vh;
            width: 100%;
            position: relative;
            overflow: hidden;
        }
        
        /* Background image layer (z-index: 1) */
        .hero-bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 120%;
            background-image: url('index1bg.jpg');
            background-size: cover;
            background-position: center;
            will-change: transform;
            z-index: 1;
            transition: transform 0.8s cubic-bezier(0.22, 1, 0.36, 1);
        }
        
        /* Text content layer (z-index: 2) */
        .hero-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 2;
            text-align: center;
            width: 100%;
            transition: all 0.8s cubic-bezier(0.22, 1, 0.36, 1);
        }
        
        .hero-title {
            font-size: 8vw;
            font-weight: 700;
            color: white;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.8);
            will-change: transform;
            margin-bottom: 2rem;
            transition: all 0.8s cubic-bezier(0.22, 1, 0.36, 1);
        }
        
        .hero-subtitle {
            font-size: 1.5rem;
            color: white;
            opacity: 0.9;
            max-width: 800px;
            margin: 0 auto;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.8);
            transition: all 0.8s cubic-bezier(0.22, 1, 0.36, 1);
        }
        
        /* Top overlay image layer (z-index: 3) */
        .hero-overlay-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 120%;
            background-image: url('index3bg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            will-change: transform;
            z-index: 3;
            transition: transform 0.8s cubic-bezier(0.22, 1, 0.36, 1);
            opacity: 0.8;
        }
        
        .scroll-indicator {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 4;
            color: white;
            font-size: 14px;
            animation: bounce 2s infinite;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.8);
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0) translateX(-50%);
            }
            40% {
                transform: translateY(-20px) translateX(-50%);
            }
            60% {
                transform: translateY(-10px) translateX(-50%);
            }
        }
        
        .content-section {
            padding: 100px 5%;
            position: relative;
            z-index: 5;
            background-color: white;
        }
        
        .section-title {
            text-align: center;
            font-size: 3rem;
            font-weight: 700;
            color: #e63946;
            margin-bottom: 50px;
            opacity: 0;
            transform: translateY(50px);
            will-change: transform, opacity;
        }
        
        .cards-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .card {
            flex: 1 1 300px;
            min-height: 400px;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            background-color: white;
            border-top: 4px solid #e63946;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }
        
        .card h2 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: #e63946;
        }
        
        .card p {
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #e63946;
        }
        
        footer {
            padding: 40px 5%;
            text-align: center;
            background-color: black;
            color: white;
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 12vw;
            }
            
            .hero-subtitle {
                font-size: 1.2rem;
                padding: 0 20px;
            }
            
            .card {
                padding: 30px 20px;
                min-height: auto;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="hero-section">
        <!-- Background image layer (z-index: 1) -->
        <div class="hero-bg-image" id="heroBgImage"></div>
        
        <!-- Text content layer (z-index: 2) -->
        <div class="hero-content" id="heroContent">
            <h1 class="hero-title">About Us</h1>
            <p class="hero-subtitle">Sri Lanka's premier institution for internationally recognized higher education</p>
        </div>
        
        <!-- Top overlay image layer (z-index: 3) -->
        <div class="hero-overlay-image" id="heroOverlayImage"></div>
        
        <div class="scroll-indicator">Scroll down</div>
    </div>
    
    <div class="content-section">
        <h1 class="section-title" id="sectionTitle">About Us</h1>
        <div class="cards-container">
            <div class="card">
                <div class="card-icon"><i class="fas fa-university"></i></div>
                <h2>Who We Are</h2>
                <p>Leading higher education provider in Sri Lanka offering Pearson-accredited programs in Business, Computing, Engineering, Law and more across four campuses.</p>
                <p>Our globally recognized qualifications combine academic excellence with practical skills for career success.</p>
            </div>
            
            <div class="card">
                <div class="card-icon"><i class="fas fa-history"></i></div>
                <h2>Our Journey</h2>
                <p>Founded in 1999 to provide computer and English education, we've grown into a pathway for 10,000+ students to UK higher education.</p>
                <p>Pioneers of Sri Lanka's first ECDL/ICDL certification program.</p>
            </div>
            
            <div class="card">
                <div class="card-icon"><i class="fas fa-bullseye"></i></div>
                <h2>Our Mission</h2>
                <p>To develop ethical, skilled professionals through innovative education that serves both national needs and global humanity.</p>
            </div>
            
            <div class="card">
                <div class="card-icon"><i class="fas fa-eye"></i></div>
                <h2>Our Vision</h2>
                <p>To be South Asia's top private university, transforming education through technology and creative teaching methods.</p>
                <p>We make learning engaging and accessible for all.</p>
            </div>
        </div>
    </div>
    
    <footer>
        <p>© 2023 British College of Applied Studies. All rights reserved.</p>
    </footer>
    
    <script>
        window.addEventListener('scroll', function() {
            const scrollPosition = window.scrollY;
            const heroBgImage = document.getElementById('heroBgImage');
            const heroContent = document.getElementById('heroContent');
            const heroOverlayImage = document.getElementById('heroOverlayImage');
            const heroTitle = document.querySelector('.hero-title');
            const heroSubtitle = document.querySelector('.hero-subtitle');
            const sectionTitle = document.getElementById('sectionTitle');
            
            // Background image (z-index: 1) - moves up 
            heroBgImage.style.transform = `translateY(${-scrollPosition * 0.3}px) scale(${1 + scrollPosition * 0.0003})`;
            heroBgImage.style.filter = `brightness(${1 - scrollPosition * 0.001})`;
            
            // Text content (z-index: 2) - moves down 
            heroContent.style.transform = `translate(-50%, calc(-50% + ${Math.min(scrollPosition * 0.6, 300)}px))`;
            heroTitle.style.transform = `translateY(${Math.min(scrollPosition * 0.4, 150)}px)`;
            heroSubtitle.style.transform = `translateY(${Math.min(scrollPosition * 0.5, 180)}px)`;
            
            // Top overlay image (z-index: 3) - moves up 
            heroOverlayImage.style.transform = `translateY(${-scrollPosition * 0.5}px) scale(${1 + scrollPosition * 0.0004})`;
            
            // Fade effects
            const fadeProgress = Math.min(scrollPosition / 400, 1);
            heroContent.style.opacity = 1 - fadeProgress * 0.9;
            heroTitle.style.fontSize = `${8 - fadeProgress * 2}vw`;
            
            // Overlay image fade
            heroOverlayImage.style.opacity = 0.8 - fadeProgress * 0.3;
            
            // Background darkening
            heroBgImage.style.backgroundColor = `rgba(0,0,0,${fadeProgress * 0.2})`;
            
            // Section title animation - appears progressively on scroll
            const contentSection = document.querySelector('.content-section');
            const contentSectionTop = contentSection.offsetTop;
            const windowHeight = window.innerHeight;
            
            // Calculate progress based on scroll position relative to content section
            const scrollProgress = Math.max(0, Math.min(1, (scrollPosition + windowHeight - contentSectionTop) / (windowHeight * 0.5)));
            
            // Apply progressive animation
            sectionTitle.style.opacity = scrollProgress;
            sectionTitle.style.transform = `translateY(${50 * (1 - scrollProgress)}px)`;
            
            // Optional: Add slight scale effect
            sectionTitle.style.transform += ` scale(${0.8 + 0.2 * scrollProgress})`;
        });
    </script>
</body>
</html>