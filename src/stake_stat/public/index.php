<?php include 'header.php'; ?>

<section class="main-event position-relative pt-5 d-flex align-items-center" style="min-height: 85vh; background: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.6)), url('https://via.placeholder.com/1920x1080'); background-size: cover; background-position: center; margin-top: -80px;">
    
    <div style="padding-top: 80px; width: 100%;"> 
        <div class="container">
            <div class="row align-items-center justify-content-center text-center text-lg-start">
                
                <div class="col-12 col-lg-3 mb-4 mb-lg-0 text-center">
                    <div class="fighter-card">
                        <img src="../images/g.png" alt="Gaethje" class="img-fluid img-peleador" style="border-bottom: 5px solid var(--rojo-stake, #d20a0a); max-height: 400px; object-fit: contain;">
                        <h2 class="mt-3 fw-bold text-white text-uppercase" style="font-family: 'Oswald', sans-serif; letter-spacing: 2px;">CAMPEÓN</h2>
                    </div>
                </div>
                
                <div class="col-12 col-lg-6 mb-4 mb-lg-0 text-center z-3">
                    <div class="event-details d-flex flex-column align-items-center justify-content-center h-100">
                        
                        <span class="badge mb-3 d-inline-flex align-items-center gap-2 px-3 py-2 shadow" style="background-color: var(--rojo-stake, #d20a0a); font-size: 1rem; border-radius: 4px;">
                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                            EN VIVO
                        </span>
                        
                        <h1 class="text-white text-uppercase" style="font-family: 'Oswald', sans-serif; font-size: clamp(3rem, 5vw, 4.5rem); text-shadow: 2px 2px 10px rgba(0,0,0,0.8); line-height: 1.1;">
                            STAKE FIGHT NIGHT
                        </h1>
                        
                        <p class="matchup mt-2 mb-2 text-uppercase" style="color: var(--rojo-stake, #d20a0a); font-size: clamp(1.5rem, 3vw, 2.2rem); font-weight: bold; letter-spacing: 1px;">
                            GAETHJE vs TOPURIA
                        </p>
                        
                        <p class="date fs-5 text-light mb-4" style="letter-spacing: 1px;">Sábado, 20 de Mayo | Las Vegas, NV</p>
                        
                        <a href="#cartelera" class="btn btn-lg text-white fw-bold px-5 py-3 btn-animado" style="background-color: var(--rojo-stake, #d20a0a); border: none; font-family: 'Oswald', sans-serif; letter-spacing: 1px; border-radius: 4px;">
                            VER CARTELERA
                        </a>
                    </div>
                </div>

                <div class="col-12 col-lg-3 text-center">
                    <div class="fighter-card">
                        <img src="../images/t.png" alt="Topuria" class="img-fluid img-peleador" style="border-bottom: 5px solid var(--rojo-stake, #d20a0a); max-height: 400px; object-fit: contain;">
                        <h2 class="mt-3 fw-bold text-white text-uppercase" style="font-family: 'Oswald', sans-serif; letter-spacing: 2px;">CONTENDIENTE</h2>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<section id="cartelera" class="container py-5 mt-4">
    <h2 class="text-center mb-5" style="font-family: 'Oswald', sans-serif; font-size: 2.5rem; letter-spacing: 2px; color: white;">CARTELERA DE EVENTOS</h2>

    <div class="row align-items-center border-bottom border-secondary py-4 event-item" style="background-color: var(--negro-suave, #111); border-radius: 8px;">
        <div class="col-12 col-md-2 text-center text-md-start mb-4 mb-md-0 ps-md-4">
            <h5 class="fw-bold text-uppercase fst-italic text-white" style="font-family: 'Oswald', sans-serif; letter-spacing: 1px;">STAKE<br><span style="color: var(--rojo-stake, #d20a0a);">FIGHT NIGHT</span></h5>
        </div>
        
        <div class="col-12 col-md-5 d-flex justify-content-center align-items-end mb-4 mb-md-0 gap-4">
            <img src="../images/g.png" alt="Gaethje" style="height: 180px; width: auto; object-fit: cover; object-position: top;">
            <span class="fs-4 fw-bold mb-4" style="color: var(--gris-texto, #888);">VS</span>
            <img src="../images/t.png" alt="Topuria" style="height: 180px; width: auto; object-fit: cover; object-position: top;">
        </div>
        
        <div class="col-12 col-md-5 text-center text-md-start pe-md-4">
            <h4 class="fw-bold text-uppercase mb-2 text-white" style="font-family: 'Oswald', sans-serif; letter-spacing: 1px;">GAETHJE VS TOPURIA</h4>
            <p class="mb-1" style="color: var(--gris-texto, #888); font-size: 0.9rem;">Sáb, Mayo 20 / 8:00 PM CST / Cartelera Estelar</p>
            <p class="mb-3" style="color: var(--gris-texto, #888); font-size: 0.9rem;">T-Mobile Arena<br>Las Vegas, NV United States</p>
            
            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center justify-content-md-start">
                <button class="btn btn-ufc px-4 py-2 fw-bold text-uppercase" style="font-family: 'Oswald', sans-serif; letter-spacing: 1px;">CÓMO VER</button>
                <button class="btn btn-ufc px-4 py-2 fw-bold text-uppercase" style="font-family: 'Oswald', sans-serif; letter-spacing: 1px;">BOLETOS</button>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>