<div class="navidad-luces-container">
    <ul class="navidad-luces">
        <li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li>
    </ul>
</div>

<div id="snow-container" class="absolute inset-0 pointer-events-none z-50 overflow-hidden"></div>

<style>
    /* Guirnalda de luces vintage animadas */
    .navidad-luces-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 40;
        pointer-events: none;
    }
    .navidad-luces {
        text-align: center;
        white-space: nowrap;
        margin: 0;
        padding: 0;
        pointer-events: none;
        width: 100%;
        display: flex;
        justify-content: space-around;
    }
    .navidad-luces li {
        position: relative;
        list-style: none;
        margin: 0;
        padding: 0;
        display: inline-block;
        width: 12px;
        height: 24px;
        border-radius: 50%;
        top: -5px;
        background: #047857;
        animation-name: luzParpadeo;
        animation-duration: 2s;
        animation-iteration-count: infinite;
        animation-fill-mode: both;
    }
    /* Alternancia de colores para simular luces reales */
    .navidad-luces li:nth-child(2n+1) { background: #dc2626; animation-duration: 1.5s; }
    .navidad-luces li:nth-child(3n+1) { background: #eab308; animation-duration: 1.8s; }
    .navidad-luces li:nth-child(4n+1) { background: #22d3ee; animation-duration: 2.2s; }

    @keyframes luzParpadeo {
        0%, 100% { opacity: 0.3; transform: scale(0.9); box-shadow: 0px 2px 4px transparent; }
        50% { opacity: 1; transform: scale(1.1); box-shadow: 0px 4px 15px currentColor; }
    }

    /* Copos de nieve estilizados */
    .snowflake {
        position: absolute;
        top: -10px;
        color: #ffffff;
        font-family: serif;
        pointer-events: none;
        animation: fall linear infinite;
        user-select: none;
    }
    @keyframes fall {
        0% { transform: translateY(0) rotate(0deg); opacity: 1; }
        100% { transform: translateY(105vh) rotate(360deg); opacity: 0.3; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const snowContainer = document.getElementById('snow-container');
        const flakes = ['❄', '❅', '❆', '•'];
        const maxFlakes = 45; // Cantidad justa para no matar el rendimiento

        for (let i = 0; i < maxFlakes; i++) {
            const flake = document.createElement('div');
            flake.className = 'snowflake';
            flake.innerText = flakes[Math.floor(Math.random() * flakes.length)];
            
            // Atributos aleatorios para dar profundidad 2D (Efecto Parallax)
            flake.style.left = Math.random() * 100 + '%';
            flake.style.fontSize = (Math.random() * 14 + 10) + 'px';
            flake.style.animationDuration = (Math.random() * 7 + 5) + 's'; 
            flake.style.animationDelay = (Math.random() * 5) + 's';
            flake.style.opacity = Math.random();
            
            snowContainer.appendChild(flake);
        }
    });
</script>