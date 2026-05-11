<footer class="text-white mt-5 pt-5 pb-3" style="background-color: var(--negro-suave, #111); border-top: 2px solid var(--rojo-stake, #d20a0a);">
        <div class="container">
            <div class="row gy-4">
                
                <div class="col-lg-4 col-md-6">
                    <h5 class="fw-bold text-uppercase mb-3 d-flex align-items-center gap-2" style="font-family: 'Oswald', sans-serif;">
                        <img src="<?php echo isset($ruta_raiz) ? $ruta_raiz : '../'; ?>images/logo.png" alt="Logo" height="30">
                        STAKE <span style="color: var(--rojo-stake, #d20a0a);">STATS</span>
                    </h5>
                    <p class="text-white-50" style="font-size: 0.9rem;">
                        La arena definitiva. Estadísticas en tiempo real, rankings de los mejores peleadores y el Fight Club para que dejes tu marca.
                    </p>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <h5 class="fw-bold text-uppercase mb-3" style="font-family: 'Oswald', sans-serif;">La Arena</h5>
                    <ul class="list-unstyled">
                        <?php 
                            // Usamos la variable inteligente del header, o un valor por defecto si falla
                            $base_url = isset($ruta_public) ? $ruta_public : '../'; 
                        ?>
                        <li class="mb-2"><a href="<?php echo $base_url; ?>index.php" class="text-white-50 text-decoration-none nav-link-animado"><i class="bi bi-chevron-right text-danger" style="font-size: 0.7rem;"></i> Eventos</a></li>
                        <li class="mb-2"><a href="<?php echo $base_url; ?>rankings.php" class="text-white-50 text-decoration-none nav-link-animado"><i class="bi bi-chevron-right text-danger" style="font-size: 0.7rem;"></i> Rankings</a></li>
                        <li class="mb-2"><a href="<?php echo $base_url; ?>peleadores.php" class="text-white-50 text-decoration-none nav-link-animado"><i class="bi bi-chevron-right text-danger" style="font-size: 0.7rem;"></i> Peleadores</a></li>
                        <li class="mb-2"><a href="<?php echo $base_url; ?>club/index.php" class="text-white-50 text-decoration-none nav-link-animado"><i class="bi bi-chevron-right text-danger" style="font-size: 0.7rem;"></i> Fight Club</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-12">
                    <h5 class="fw-bold text-uppercase mb-3" style="font-family: 'Oswald', sans-serif;">Síguenos</h5>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white-50 nav-link-animado fs-4"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="text-white-50 nav-link-animado fs-4"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white-50 nav-link-animado fs-4"><i class="bi bi-youtube"></i></a>
                        <a href="#" class="text-white-50 nav-link-animado fs-4"><i class="bi bi-tiktok"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4 pt-3" style="border-top: 1px solid #333;">
                <div class="col-12 text-center">
                    <p class="text-white-50 mb-0" style="font-size: 0.85rem;">&copy; <?php echo date('Y'); ?> Stake Stats. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php $js_path = isset($ruta_raiz) ? $ruta_raiz : '../'; ?>
    </body>
</html>