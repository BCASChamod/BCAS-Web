<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About BCAS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 40px 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
        }

        .persona-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 60px;
        }

        .persona-card {
            background: white;
            border: 2px solid #e74c3c;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            position: relative;
        }

        .persona-card h2 {
            color: #333;
            font-size: 1.3rem;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .persona-card p, .persona-card li {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 10px;
        }

        .persona-card ul {
            padding-left: 20px;
        }

        .persona-card li {
            margin-bottom: 8px;
        }

        /* Center circle with logo */
        .center-circle {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 120px;
            height: 120px;
            background: #e74c3c;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 15px rgba(0,0,0,0.2);
            z-index: 10;
        }

        .center-circle img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        .center-text {
            color: white;
            font-weight: bold;
            font-size: 1.1rem;
            text-align: center;
        }

        /* Positioning for specific cards */
        .about-card {
            grid-column: 1;
            grid-row: 1;
        }

        .history-card {
            grid-column: 2;
            grid-row: 1;
        }

        .mission-card {
            grid-column: 1;
            grid-row: 2;
        }

        .vision-card {
            grid-column: 2;
            grid-row: 2;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .persona-grid {
                grid-template-columns: 1fr;
                gap: 20px;
                margin-bottom: 40px;
            }

            .center-circle {
                position: static;
                transform: none;
                margin: 20px auto;
            }

            .about-card,
            .history-card,
            .mission-card,
            .vision-card {
                grid-column: 1;
                grid-row: auto;
            }
        }

        hr {
            border: none;
            height: 2px;
            background: #e74c3c;
            margin: 40px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="persona-grid">
            <!-- About Us Card -->
            <div class="persona-card about-card">
                <h2>About Us</h2>
                <ul>
                    <li>Leading higher education institution in Sri Lanka</li>
                    <li>Internationally recognised programmes with top global universities</li>
                    <li>Accredited Pearson centre for Diplomas, HNDs, and degrees</li>
                    <li>Programmes in Business, Computing, Civil Engineering, Law, and more</li>
                    <li>Campuses in Colombo, Kandy, Kalmunai, and Jaffna</li>
                    <li>World-class education with expert faculty and modern facilities</li>
                </ul>
            </div>

            <!-- Our History Card -->
            <div class="persona-card history-card">
                <h2>Our History</h2>
                <ul>
                    <li>Founded in 1999 with mission of providing basic computer education</li>
                    <li>Started with teaching English and IT to school leavers</li>
                    <li>Created flagship "Access Programme" for post-AL students</li>
                    <li>Over 10,000 students gained admission to UK universities</li>
                    <li>First in Sri Lanka to obtain ECDL/ICDL franchise</li>
                    <li>Focused on preparing students for modern job market</li>
                </ul>
            </div>

            <!-- Center Circle with Logo -->
            <div class="center-circle">
                <img src="../images/bcas_logo.png" alt="BCAS Logo">
            </div>

            <!-- Mission Card -->
            <div class="persona-card mission-card">
                <h2>Mission</h2>
                <ul>
                    <li>Produce market-relevant quality human resources</li>
                    <li>Focus on ethics and social responsibility</li>
                    <li>Innovation, research and skills development</li>
                    <li>Serve the nation and humanity at large</li>
                    <li>Prepare students for competitive job market</li>
                    <li>Provide globally respected education</li>
                </ul>
            </div>

            <!-- Vision Card -->
            <div class="persona-card vision-card">
                <h2>Vision</h2>
                <ul>
                    <li>Become the premier private university in South Asian Region</li>
                    <li>Revolutionize the way people learn and see the world</li>
                    <li>Make education engaging and accessible to everyone</li>
                    <li>Use cutting-edge technology and creative teaching methods</li>
                    <li>Help learners understand and retain information effectively</li>
                    <li>Unlock students' full potential for success</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>