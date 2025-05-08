console.log('Search script is running');

document.addEventListener("DOMContentLoaded", function () {
    const zapEffect = document.getElementById('zap-effect');
    const zapPolyline = zapEffect.querySelector('polyline');

    // Function to generate random points for a wiggling effect
    function generateZapPoints() {
        // Define some base points, and add randomness to make it wiggle
        const points = [
            { x: 32, y: 0 },
            { x: 32 + (Math.random() * 15 - 12), y: 16 },
            { x: 40 + (Math.random() * 15 - 12), y: 24 },
            { x: 24 + (Math.random() * 15 - 12), y: 40 },
            { x: 32 + (Math.random() * 15 - 12), y: 56 },
            { x: 16 + (Math.random() * 15 - 12), y: 70 },
            { x: 20, y: 80 }
        ];
        
        return points.map(point => `${point.x},${point.y}`).join(' ');
    }

    function updateZapPoints() {
        zapPolyline.setAttribute('points', generateZapPoints());
    }
    
    // Function to make the zap flicker and update points multiple times per flicker
    function flicker() {
        const flickerDuration = Math.random() * 500; // Flicker time between 0 and 500ms
        zapEffect.style.display = 'block';

        // Update the zap points multiple times during the flicker
        let flickerStartTime = Date.now();
        const flickerInterval = setInterval(() => {
            updateZapPoints();
            if (Date.now() - flickerStartTime > flickerDuration) {
                clearInterval(flickerInterval);
                zapEffect.style.display = 'none';

                // Randomly decide when the next flicker will occur
                const nextFlickerDelay = Math.random() * 200; // Random delay between 500ms to 3500ms
                setTimeout(flicker, nextFlickerDelay);
            }
        }, 50); // Update every 50ms for rapid point changes during flicker
    }

    // Start the flickering effect
    flicker();
});