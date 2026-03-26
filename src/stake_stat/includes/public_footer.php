<style>
        .footer-link {
            transition: color 0.3s ease, padding-left 0.3s ease;
            color: #bbb !important; /* Gris claro muy legible */
        }
        .footer-link:hover {
            color: var(--rojo-stake, #d20a0a) !important;
            padding-left: 5px;
        }
        .social-icon {
            transition: transform 0.3s ease, color 0.3s ease;
            color: #bbb !important;
        }
        .social-icon:hover {
            color: var(--rojo-stake, #d20a0a) !important;
            transform: translateY(-5px);
        }
        .footer-texto {
            color: #bbb;
            font-size: 0.95rem;
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
        }
    </style>

    <footer class="mt-5 pt-5 pb-4 shadow-lg" style="background-color: var(--negro-suave, #111); border-top: 2px solid var(--rojo-stake, #d20a0a);">
        <div class="container">
            <div class="row text-center text-md-start mb-4">
                
                <div class="col-12 col-md-4 mb-4 mb-md-0">
                    <h4 class="text-white fw-bold d-flex align-items-center justify-content-center justify-content-md-start gap-2" style="font-family: 'Oswald', sans-serif; letter-spacing: 2px;">
                        STAKE <span style="color: var(--rojo-stake, #d20a0a);">STATS</span>
                    </h4>
                    <p class="footer-texto mt-3 pe-md-4">
                        Tu plataforma definitiva para estadísticas, rankings y resultados del mundo de las artes marciales mixtas. La jaula de los datos no miente.
                    </p>
                </div>

                <div class="col-12 col-md-4 mb-4 mb-md-0">
                    <h5 class="text-white fw-bold mb-3" style="font-family: 'Oswald', sans-serif; letter-spacing: 1px;">ENLACES RÁPIDOS</h5>
                    <ul class="list-unstyled" style="font-family: 'Roboto', sans-serif; font-size: 0.95rem;">
                        <li class="mb-2"><a href="index.php" class="text-decoration-none footer-link fw-bold">Cartelera de Eventos</a></li>
                        <li class="mb-2"><a href="rankings.php" class="text-decoration-none footer-link fw-bold">Rankings Globales</a></li>
                        <li class="mb-2"><a href="peleadores.php" class="text-decoration-none footer-link fw-bold">Directorio de Peleadores</a></li>
                        <li class="mb-2"><a href="tienda.php" class="text-decoration-none footer-link fw-bold">Tienda Oficial</a></li>
                    </ul>
                </div>

                <div class="col-12 col-md-4">
                    <h5 class="text-white fw-bold mb-3" style="font-family: 'Oswald', sans-serif; letter-spacing: 1px;">ÚNETE A LA CONVERSACIÓN</h5>
                    <p class="footer-texto mb-3">Síguenos para contenido exclusivo y actualizaciones en vivo.</p>
                    <div class="d-flex gap-3 justify-content-center justify-content-md-start">
                        <a href="#" class="fs-4 social-icon"><i class="bi bi-youtube"></i></a>
                        <a href="#" class="fs-4 social-icon"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="fs-4 social-icon"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="fs-4 social-icon"><i class="bi bi-tiktok"></i></a>
                    </div>
                </div>

            </div>

            <hr style="border-color: #333;">
            <div class="text-center mt-4 pt-2">
                <p class="mb-1" style="color: #999; font-family: 'Roboto', sans-serif; font-size: 0.9rem;">
                    &copy; <?php echo date('Y'); ?> Stake Stats. Todos los derechos reservados.
                </p>
                <p class="mb-0 mt-2" style="color: var(--rojo-stake, #d20a0a); font-weight: bold; font-family: 'Oswald', sans-serif; letter-spacing: 2px; font-size: 1.1rem;">
                    ARENA OFICIAL DE ESTADÍSTICAS
                </p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>