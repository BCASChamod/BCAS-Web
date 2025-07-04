<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
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
      overflow-x: hidden;
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
      border: 2px solid transparent;
      border-radius: 20px;
      padding: 25px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      position: relative;
      overflow: visible;
      opacity: 0;
      transform: translateY(50px);
      transition:
        transform 0.6s cubic-bezier(0.22, 1, 0.36, 1),
        opacity 0.6s cubic-bezier(0.22, 1, 0.36, 1),
        box-shadow 0.6s ease-in-out;
    }

    .persona-card::before {
      content: '';
      position: absolute;
      top: -3px;
      left: -3px;
      right: -3px;
      bottom: -3px;
      border-radius: 23px;
      background: conic-gradient(from 0deg, #e74c3c 0deg, transparent 0deg);
      z-index: -1;
      opacity: 0;
    }

    .persona-card::after {
      content: '';
      position: absolute;
      top: -1px;
      left: -1px;
      right: -1px;
      bottom: -1px;
      border-radius: 21px;
      background: white;
      z-index: -1;
    }

    .persona-card.animate-border::before {
      animation: drawBorder 1.5s cubic-bezier(0.5, 0.4, 0.4, 1) forwards;
      opacity: 1;
    }

    .persona-card.animate-content {
      animation: slideUp 1.2s cubic-bezier(0.4, 0, 0.2, 1) forwards;
      opacity: 1;
      transform: translateY(0);
    }

    .persona-card h2 {
      color: #333;
      font-size: 1.3rem;
      margin-bottom: 15px;
      font-weight: bold;
      opacity: 0;
      transform: translateY(20px);
      animation-fill-mode: forwards;
      animation-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }

    .persona-card p,
    .persona-card li {
      color: #666;
      font-size: 0.95rem;
      margin-bottom: 10px;
      opacity: 0;
      transform: translateY(20px);
      animation-fill-mode: forwards;
      animation-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }

    .persona-card ul {
      padding-left: 20px;
    }

    .persona-card li {
      margin-bottom: 8px;
    }

    .persona-card.animate-content h2 {
      animation-name: slideUpText;
      animation-duration: 0.8s;
      animation-delay: 0.4s;
    }

    .persona-card.animate-content li:nth-child(1) {
      animation-name: slideUpText;
      animation-duration: 0.6s;
      animation-delay: 0.5s;
    }

    .persona-card.animate-content li:nth-child(2) {
      animation-name: slideUpText;
      animation-duration: 0.6s;
      animation-delay: 0.6s;
    }

    .persona-card.animate-content li:nth-child(3) {
      animation-name: slideUpText;
      animation-duration: 0.6s;
      animation-delay: 0.7s;
    }

    .persona-card.animate-content li:nth-child(4) {
      animation-name: slideUpText;
      animation-duration: 0.6s;
      animation-delay: 0.8s;
    }

    .persona-card.animate-content li:nth-child(5) {
      animation-name: slideUpText;
      animation-duration: 0.6s;
      animation-delay: 0.9s;
    }

    .persona-card.animate-content li:nth-child(6) {
      animation-name: slideUpText;
      animation-duration: 0.6s;
      animation-delay: 1s;
    }

    /* Center circle with logo */
    .center-circle {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) scale(0);
      width: 120px;
      height: 120px;
      background: #e74c3c;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
      z-index: 10;
      animation: scaleIn 0.9s cubic-bezier(0.4, 0, 0.2, 1) forwards;
      opacity: 0;
    }

    .center-circle {
      animation-delay: 0.3s;
      animation-fill-mode: forwards;
    }

    .center-circle img {
      width: 80px;
      height: 80px;
      object-fit: contain;
      filter: brightness(0) invert(1);
      opacity: 0;
      transform: rotate(-180deg);
      animation: logoAppear 0.7s cubic-bezier(0.4, 0, 0.2, 1) 0.9s forwards;
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

    /* Animated connecting lines (only top two) */
    .connecting-line {
      position: absolute;
      background: #e74c3c;
      z-index: 5;
      opacity: 0;
    }

    /* Animations */
    @keyframes scaleIn {
      from {
        transform: translate(-50%, -50%) scale(0);
        opacity: 0;
      }

      to {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
      }
    }

    @keyframes logoAppear {
      from {
        opacity: 0;
        transform: rotate(-180deg) scale(0.5);
      }

      to {
        opacity: 1;
        transform: rotate(0deg) scale(1);
      }
    }

    @keyframes growVertical {
      from {
        opacity: 0;
        transform: translateX(-50%) scaleY(0);
      }

      to {
        opacity: 1;
        transform: translateX(-50%) scaleY(1);
      }
    }

    @keyframes growVerticalRight {
      from {
        opacity: 0;
        transform: translateX(50%) scaleY(0);
      }

      to {
        opacity: 1;
        transform: translateX(50%) scaleY(1);
      }
    }

    @keyframes drawBorder {
      0% {
        opacity: 1;
        background: conic-gradient(from 0deg, #e74c3c 0deg, #e74c3c 0deg, transparent 0deg);
      }

      25% {
        background: conic-gradient(from 0deg, #e74c3c 90deg, #e74c3c 90deg, transparent 90deg);
      }

      50% {
        background: conic-gradient(from 0deg, #e74c3c 180deg, #e74c3c 180deg, transparent 180deg);
      }

      75% {
        background: conic-gradient(from 0deg, #e74c3c 270deg, #e74c3c 270deg, transparent 270deg);
      }

      100% {
        opacity: 1;
        background: #e74c3c;
      }
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(50px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes slideUpText {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
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
        animation: scaleInMobile 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
      }

      @keyframes scaleInMobile {
        from {
          transform: scale(0);
          opacity: 0;
        }

        to {
          transform: scale(1);
          opacity: 1;
        }
      }

      .about-card,
      .history-card,
      .mission-card,
      .vision-card {
        grid-column: 1;
        grid-row: auto;
      }

      .connecting-line {
        display: none;
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
        <div class="center-text">BCAS</div>
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

  <script>
    // Animation sequence controller
    class AnimationController {
      constructor() {
        this.init();
      }

      init() {
        window.addEventListener('load', () => {
          this.startAnimationSequence();
        });
      }

      startAnimationSequence() {
        // Step 1: Center circle appears (CSS handles this)
        // Step 2: Animate borders and content together for smoothness
        setTimeout(() => {
          this.animateCards();
        }, 800); // Slightly delayed to let circle appear
      }

      animateCards() {
        const cards = document.querySelectorAll('.persona-card');
        cards.forEach((card, index) => {
          setTimeout(() => {
            card.classList.add('animate-border', 'animate-content');
            // Smooth pulse effect using CSS transition
            card.style.boxShadow = '0 4px 18px rgba(231,76,60,0.16)';
            setTimeout(() => {
              card.style.boxShadow = '0 4px 10px rgba(0,0,0,0.1)';
            }, 600);
          }, index * 600 + 800); // stagger with 600ms delay and initial offset
        });
      }
    }
    new AnimationController();
  </script>
</body>

</html>
