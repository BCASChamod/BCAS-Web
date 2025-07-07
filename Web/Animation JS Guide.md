# GSAP Scroll Animation Framework - Usage Guide

A lightweight, easy-to-use framework for creating smooth scroll-triggered animations using GSAP and ScrollTrigger.

## Quick Start

### 1. Include Required Libraries

Add these script tags to your HTML file:

```html
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
```

### 2. Copy the Framework

Copy the `ScrollAnimationFramework` class from the provided HTML file into your project.

### 3. Initialize the Framework

```javascript
const scrollFramework = new ScrollAnimationFramework({
    duration: 1.2,
    ease: "power3.out"
});
```

## Available Animation Classes

### Basic Animations

| Class | Effect | Initial State |
|-------|--------|---------------|
| `.scroll-fade-in` | Simple fade in | `opacity: 0` |
| `.scroll-fade-up` | Fade in from bottom | `opacity: 0, translateY(50px)` |
| `.scroll-fade-down` | Fade in from top | `opacity: 0, translateY(-50px)` |
| `.scroll-fade-left` | Fade in from left | `opacity: 0, translateX(-50px)` |
| `.scroll-fade-right` | Fade in from right | `opacity: 0, translateX(50px)` |
| `.scroll-scale` | Scale up effect | `opacity: 0, scale(0.8)` |
| `.scroll-rotate` | Rotate effect | `opacity: 0, rotate(15deg)` |
| `.scroll-skew` | Skew effect | `opacity: 0, skewX(15deg)` |

### Special Components

#### Progress Bars
```html
<div class="progress-bar">
    <div class="progress-fill scroll-progress" data-progress="75"></div>
</div>
```

#### Counters
```html
<div class="scroll-counter" data-target="150">0</div>
```

## HTML Usage Examples

### Basic Animation
```html
<div class="scroll-fade-up">
    <h2>This will fade up when scrolled into view</h2>
</div>
```

### With Custom Timing
```html
<div class="scroll-fade-left" data-delay="0.3" data-duration="1.5">
    <p>This will animate with a 0.3s delay and 1.5s duration</p>
</div>
```

### Multiple Elements with Staggered Animation
```html
<div class="feature-grid">
    <div class="feature-item scroll-fade-up" data-delay="0.1">First item</div>
    <div class="feature-item scroll-fade-up" data-delay="0.2">Second item</div>
    <div class="feature-item scroll-fade-up" data-delay="0.3">Third item</div>
</div>
```

## Data Attributes

### Timing Controls
- `data-delay="0.2"` - Delay animation by 0.2 seconds
- `data-duration="1.5"` - Set animation duration to 1.5 seconds
- `data-ease="power2.out"` - Use custom easing function

### Component-Specific
- `data-progress="75"` - For progress bars (0-100)
- `data-target="150"` - For counters (target number)

## CSS Requirements

Add these CSS classes to your stylesheet:

```css
.scroll-fade-in { opacity: 0; }
.scroll-fade-up { opacity: 0; transform: translateY(50px); }
.scroll-fade-down { opacity: 0; transform: translateY(-50px); }
.scroll-fade-left { opacity: 0; transform: translateX(-50px); }
.scroll-fade-right { opacity: 0; transform: translateX(50px); }
.scroll-scale { opacity: 0; transform: scale(0.8); }
.scroll-rotate { opacity: 0; transform: rotate(15deg); }
.scroll-skew { opacity: 0; transform: skewX(15deg); }
```

## JavaScript API

### Initialization Options
```javascript
const scrollFramework = new ScrollAnimationFramework({
    duration: 1,        // Default animation duration
    ease: "power2.out", // Default easing
    threshold: 0.1      // Scroll threshold
});
```

### Methods

#### Animate Dynamic Elements
```javascript
// Animate a single element
scrollFramework.animateElement(element, 'fade-up', {
    duration: 1.5,
    delay: 0.3,
    ease: "power3.out"
});
```

#### Refresh ScrollTrigger
```javascript
// Call when adding new content dynamically
scrollFramework.refresh();
```

#### Destroy Framework
```javascript
// Clean up all scroll triggers
scrollFramework.destroy();
```

## Common Use Cases

### Loading Content Dynamically
```javascript
// Add new content
const newElement = document.createElement('div');
newElement.className = 'scroll-fade-up';
newElement.textContent = 'New content';
document.body.appendChild(newElement);

// Refresh to include new element
scrollFramework.refresh();
```

### Custom Animation Timing
```javascript
// Create staggered animations
document.querySelectorAll('.my-items').forEach((item, index) => {
    scrollFramework.animateElement(item, 'fade-up', {
        delay: index * 0.1
    });
});
```

### Conditional Animations
```javascript
// Only animate on desktop
if (window.innerWidth > 768) {
    const scrollFramework = new ScrollAnimationFramework();
}
```

## Best Practices

1. **Performance**: Don't animate too many elements simultaneously
2. **Accessibility**: Consider `prefers-reduced-motion` for users who prefer less animation
3. **Mobile**: Test animations on mobile devices and adjust timing if needed
4. **Content**: Ensure content is readable even if animations fail to load

## Troubleshooting

### Animations Not Working
- Check if GSAP libraries are loaded
- Verify CSS classes are applied
- Ensure framework is initialized after DOM is ready

### Performance Issues
- Reduce number of animated elements
- Use simpler animations on mobile
- Consider using `will-change` CSS property sparingly

### Dynamic Content
- Always call `refresh()` after adding new content
- Use `animateElement()` for programmatically added elements

## Browser Support

Works in all modern browsers that support:
- ES6 Classes
- GSAP 3.12+
- ScrollTrigger plugin

For older browsers, consider including polyfills or using the legacy GSAP syntax.