<div id="spooky-container" class="absolute inset-0 pointer-events-none z-50 overflow-hidden"></div>

<style>
    /* Partículas de fuego fatuo / almas flotantes */
    .wisp {
        position: absolute;
        bottom: -20px;
        border-radius: 50%;
        pointer-events: none;
        animation: floatUp linear infinite;
        user-select: none;
        filter: blur(1px);
    }

    @keyframes floatUp {
        0% {
            transform: translateY(0) scale(0.5) translateX(0);
            opacity: 0;
        }
        10% {
            opacity: 0.6;
        }
        50% {
            transform: translateY(-50vh) scale(1.2) translateX(15px);
        }
        90% {
            opacity: 0.2;
        }
        100% {
            transform: translateY(-105vh) scale(0.8) translateX(-15px);
            opacity: 0;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const spookyContainer = document.getElementById('spooky-container');
        // Usamos emojis temáticos o burbujas mágicas de colores del inframundo
        const spookyElements = ['👻', '🎃', '🦇', '✨', '•'];
        const maxElements = 30; // Controlado para mantener los FPS altos

        for (let i = 0; i < maxElements; i++) {
            const wisp = document.createElement('div');
            wisp.className = 'wisp';
            
            // Selección aleatoria de elemento
            const el = spookyElements[Math.floor(Math.random() * spookyElements.length)];
            wisp.innerText = el;

            // Si es un punto físico plano, le damos color de fuego fatuo (morado/naranja)
            if (el === '•') {
                wisp.style.color = Math.random() > 0.5 ? '#a855f7' : '#f97316';
                wisp.style.fontSize = (Math.random() * 20 + 10) + 'px';
            } else {
                wisp.style.fontSize = (Math.random() * 16 + 12) + 'px';
            }
            
            // Posiciones y velocidad aleatorias estilo partículas RPG
            wisp.style.left = Math.random() * 100 + '%';
            wisp.style.animationDuration = (Math.random() * 8 + 6) + 's'; 
            wisp.style.animationDelay = (Math.random() * 6) + 's';
            
            spookyContainer.appendChild(wisp);
        }
    });
</script>