<?php echo $header; ?>

<style>
    /* --- Solo afecta a este módulo --- */
    .estado-cuenta-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 70vh;
        text-align: center;
        background: rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(12px);
        border-radius: 20px;
        padding: 60px 30px;
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.15);
        color: #333;
    }

    .estado-cuenta-wrapper h1 {
        font-size: 2.8rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #1e293b;
        text-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
        animation: glowPulse 2.5s infinite alternate;
    }

    @keyframes glowPulse {
        from {
            text-shadow: 0 0 10px rgba(0, 136, 255, 0.3);
        }
        to {
            text-shadow: 0 0 18px rgba(0, 204, 255, 0.6);
        }
    }

  

    .buscador-box {
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.85);
        border-radius: 50px;
        padding: 10px 20px;
        width: 100%;
        max-width: 600px;
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.1);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .buscador-box:hover {
        transform: scale(1.02);
        box-shadow: 0 0 25px rgba(0, 128, 255, 0.25);
    }

    .buscador-box input {
        flex: 1;
        border: none;
        outline: none;
        background: transparent;
        color: #333;
        font-size: 1.5rem;
        padding: 10px 15px;
    }

    .buscador-box input::placeholder {
        color: #777;
    }

    .buscador-box button {
        border: none;
        background: linear-gradient(45deg, #007bff, #00c6ff);
        color: white;
        padding: 12px 25px;
        font-size: 1.1rem;
        border-radius: 50px;
        cursor: pointer;
        transition: 0.3s ease;
    }

    .buscador-box button:hover {
        background: linear-gradient(45deg, #00c6ff, #007bff);
        transform: scale(1.05);
    }

    /* Responsivo */
    @media (max-width: 768px) {
        .estado-cuenta-wrapper h1 {
            font-size: 2rem;
        }

        .buscador-box input {
            font-size: 1.2rem;
        }
    }
</style>

<div class="right_col" role="main">
            <div class="estado-cuenta-wrapper">
                <h1>Consulta de Pagos</h1>
                <p>Introduce el <strong>código del cliente o crédito</strong> para ver su estado de cuenta.</p>

                <form action="/AhorroSimple/EstadoCuenta/" method="GET" class="buscador-box">
                    <input type="text" id="cdgns" name="cdgns" placeholder="Ejemplo: 006592" autofocus required value="<?php echo $CDGNS; ?>">
                    <button type="submit"><i class="fa fa-search"></i> Buscar</button>
                </form>
            </div>
</div>

<?php echo $footer; ?>
