<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmasphere - Soluciones Farmacéuticas Integrales</title>
    
    <!-- Fuentes e Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* --- 1. SISTEMA DE DISEÑO (VARIABLES) --- */
        :root {
            /* Colores Corporativos */
            --primary: #0066cc;        /* Azul Farmacia */
            --primary-light: #e6f0fa;
            --secondary: #00c853;      /* Verde Salud */
            --secondary-dark: #009624;
            --accent: #ff6d00;         /* Naranja Acción */
            
            /* Fondos y Textos */
            --bg-body: #f4f7f9;
            --bg-card: #ffffff;
            --text-main: #2c3e50;
            --text-muted: #637381;
            --border-color: #e1e4e8;
            
            /* Efectos */
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.04);
            --shadow-md: 0 8px 24px rgba(0,0,0,0.08);
            --shadow-hover: 0 20px 40px rgba(0,0,0,0.12);
            --radius-md: 16px;
            --radius-lg: 24px;
        }

        /* TEMA OSCURO */
        body.dark-mode {
            --bg-body: #0f1115;
            --bg-card: #1c2128;
            --text-main: #ffffff;
            --text-muted: #a0a6ac;
            --border-color: #30363d;
            --primary-light: rgba(0, 102, 204, 0.2);
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.4);
            --shadow-md: 0 8px 24px rgba(0,0,0,0.6);
        }

        /* --- 2. BASE --- */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--bg-body); 
            color: var(--text-main); 
            line-height: 1.6;
            overflow-x: hidden;
            transition: background 0.3s, color 0.3s;
        }

        a { text-decoration: none; color: inherit; transition: 0.2s; }
        ul { list-style: none; }
        
        .wrapper { max-width: 1240px; margin: 0 auto; padding: 0 24px; position: relative; }
        .text-center { text-align: center; }
        
        /* Botones Globales */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 14px 32px; border-radius: 50px; font-weight: 700; cursor: pointer;
            transition: all 0.3s ease; border: none; font-size: 1rem; letter-spacing: 0.3px;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), #0052a3); 
            color: white;
            box-shadow: 0 4px 15px rgba(0, 102, 204, 0.3);
        }
        .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0, 102, 204, 0.4); }
        
        .btn-outline {
            background: transparent; border: 2px solid var(--border-color); color: var(--text-main);
        }
        .btn-outline:hover { border-color: var(--text-main); background: var(--bg-card); }

        /* --- 3. NAV --- */
        nav {
            position: fixed; top: 0; width: 100%; z-index: 1000;
            background: rgba(var(--bg-card), 0.95); backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color); padding: 15px 0;
            transition: all 0.3s;
        }
        .nav-content { display: flex; justify-content: space-between; align-items: center; }
        .brand { font-size: 1.5rem; font-weight: 800; display: flex; align-items: center; gap: 12px; color: var(--primary); }
        .brand img { height: 40px; }
        .brand span { color: var(--text-main); }
        
        .nav-links { display: flex; gap: 30px; align-items: center; }
        .nav-links a { font-weight: 500; color: var(--text-muted); font-size: 0.95rem; }
        .nav-links a:hover { color: var(--primary); }

        .nav-actions { display: flex; align-items: center; gap: 20px; }
        .theme-toggle { 
            width: 40px; height: 40px; border-radius: 50%; display: grid; place-items: center;
            background: var(--bg-body); cursor: pointer; color: var(--text-main); 
            border: 1px solid var(--border-color); transition: 0.3s;
        }
        .theme-toggle:hover { background: var(--border-color); transform: rotate(15deg); }

        /* --- 4. HERO SECTION --- */
        .hero { 
            padding: 160px 0 100px; text-align: center; position: relative; overflow: hidden;
        }
        .hero::before {
            content: ''; position: absolute; top: -50%; left: 50%; transform: translateX(-50%);
            width: 100%; height: 100%; max-width: 1200px;
            background: radial-gradient(circle, var(--primary-light) 0%, transparent 70%);
            z-index: -1; opacity: 0.6; pointer-events: none;
        }

        .hero h1 {
            font-size: 4.5rem; line-height: 1.1; margin-bottom: 24px; letter-spacing: -1.5px;
            color: var(--text-main); font-weight: 800;
        }
        .text-gradient {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        
        .hero p { 
            font-size: 1.35rem; color: var(--text-muted); max-width: 750px; margin: 0 auto 40px; 
            font-weight: 400;
        }
        
        .hero-badges { 
            display: flex; justify-content: center; gap: 20px; margin-top: 50px; flex-wrap: wrap;
        }
        .badge-pill { 
            background: var(--bg-card); border: 1px solid var(--border-color);
            padding: 8px 16px; border-radius: 30px; font-size: 0.9rem; font-weight: 600;
            color: var(--text-muted); display: flex; align-items: center; gap: 8px;
            box-shadow: var(--shadow-sm);
        }
        .badge-pill i { color: var(--secondary); }

        /* --- PARTNERS --- */
        .partners-bar {
            padding: 40px 0; border-bottom: 1px solid var(--border-color); background: var(--bg-body);
            overflow: hidden; opacity: 0.7;
        }
        .partners-track {
            display: flex; gap: 60px; justify-content: center; align-items: center; flex-wrap: wrap;
        }
        .partner-logo {
            font-size: 1.5rem; font-weight: 800; color: var(--text-muted);
            display: flex; align-items: center; gap: 10px; opacity: 0.6; transition: 0.3s;
        }
        .partner-logo:hover { opacity: 1; color: var(--primary); }

        /* --- 5. CARRUSEL CATEGORÍAS --- */
        .products-marquee {
            padding: 80px 0; background: var(--bg-card);
            border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);
            overflow: hidden;
        }
        .section-intro { text-align: center; margin-bottom: 50px; }
        .section-intro h2 { font-size: 2.5rem; margin-bottom: 10px; }
        .section-intro p { color: var(--text-muted); font-size: 1.1rem; }

        .marquee-track {
            display: flex; gap: 30px; width: max-content;
            animation: scroll 50s linear infinite;
            padding: 20px 0;
        }
        .marquee-track:hover { animation-play-state: paused; }

        .cat-card {
            width: 280px; height: 360px;
            background: var(--bg-body); border-radius: var(--radius-lg);
            padding: 30px; border: 1px solid var(--border-color);
            display: flex; flex-direction: column; align-items: center; justify-content: space-between;
            text-align: center; cursor: pointer; transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative; overflow: hidden;
        }
        .cat-card:hover {
            transform: translateY(-10px); box-shadow: var(--shadow-hover);
            border-color: var(--primary); background: var(--bg-card);
        }
        .cat-icon {
            font-size: 5rem; margin-top: 20px;
            background: linear-gradient(135deg, var(--primary), #a0cfff);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            transition: 0.3s;
        }
        .cat-card:hover .cat-icon { transform: scale(1.1); }
        .cat-info h3 { font-size: 1.5rem; margin-bottom: 5px; }
        .cat-info p { color: var(--text-muted); font-size: 0.95rem; }
        .cat-btn {
            width: 100%; padding: 12px; border-radius: 12px;
            background: var(--primary-light); color: var(--primary);
            font-weight: 700; border: none; transition: 0.3s;
        }
        .cat-card:hover .cat-btn { background: var(--primary); color: white; }

        /* --- 6. POR QUÉ NOSOTROS --- */
        .features-section { padding: 100px 0; }
        .features-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 40px;
        }
        .feature-item {
            background: var(--bg-card); padding: 40px; border-radius: var(--radius-lg);
            border: 1px solid var(--border-color); text-align: left;
            transition: 0.3s; position: relative;
        }
        .feature-item:hover { transform: translateY(-5px); box-shadow: var(--shadow-md); border-color: var(--secondary); }
        .f-icon-circle {
            width: 70px; height: 70px; border-radius: 20px; background: var(--primary-light);
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; color: var(--primary); margin-bottom: 25px;
        }
        .f-green .f-icon-circle { background: rgba(0, 200, 83, 0.1); color: var(--secondary); }
        .feature-item h2 { font-size: 1.6rem; margin-bottom: 15px; font-weight: 700; }
        .feature-item p { color: var(--text-muted); font-size: 1.05rem; margin-bottom: 20px; }
        .feature-img-placeholder {
            width: 100%; height: 150px; background: var(--bg-body); border-radius: 12px;
            display: flex; align-items: center; justify-content: center; color: var(--text-muted);
            border: 2px dashed var(--border-color); font-weight: 600;
        }

        /* --- 7. OPINIONES --- */
        .reviews-section { 
            padding: 100px 0; background: var(--bg-card); 
            border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); 
        }
        .reviews-header {
            display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 50px; flex-wrap: wrap; gap: 30px;
        }
        .rating-summary { display: flex; align-items: center; gap: 20px; }
        .big-score { font-size: 4rem; font-weight: 800; color: var(--text-main); line-height: 1; }
        .score-details { display: flex; flex-direction: column; }
        .stars-row { color: #ffc107; font-size: 1.4rem; margin-bottom: 5px; }
        .total-reviews { color: var(--text-muted); font-weight: 500; }
        
        .review-filters {
            display: flex; gap: 10px; background: var(--bg-body); padding: 5px; border-radius: 50px; border: 1px solid var(--border-color);
        }
        .filter-btn {
            padding: 8px 20px; border-radius: 40px; border: none; background: transparent;
            color: var(--text-muted); font-weight: 600; cursor: pointer; transition: 0.2s;
        }
        .filter-btn.active { background: var(--bg-card); color: var(--text-main); box-shadow: 0 2px 5px rgba(0,0,0,0.1); }

        .reviews-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px;
        }
        .review-card {
            background: var(--bg-body); padding: 35px; border-radius: var(--radius-md);
            border: 1px solid var(--border-color); transition: 0.3s;
        }
        .review-card:hover {
            background: var(--bg-card); transform: translateY(-8px); box-shadow: var(--shadow-md); border-color: var(--primary);
        }
        .reviewer-profile { display: flex; gap: 15px; align-items: center; margin-bottom: 20px; }
        .r-avatar { width: 50px; height: 50px; border-radius: 50%; background: #ddd; object-fit: cover; }
        .r-meta h4 { font-size: 1rem; margin: 0; }
        .r-meta span { font-size: 0.85rem; color: var(--secondary); font-weight: 600; display:flex; align-items:center; gap:4px; }
        .r-content p { font-style: italic; color: var(--text-main); font-size: 1.1rem; line-height: 1.6; }
        .r-date { display: block; margin-top: 20px; font-size: 0.85rem; color: var(--text-muted); }
        
        .pagination-container {
            display: flex; justify-content: center; align-items: center; gap: 10px; margin-top: 60px;
        }
        .page-num {
            width: 40px; height: 40px; border-radius: 10px; display: grid; place-items: center;
            border: 1px solid var(--border-color); cursor: pointer; font-weight: 600; transition: 0.2s;
        }
        .page-num.active { background: var(--primary); color: white; border-color: var(--primary); }
        .page-num:hover:not(.active) { background: var(--border-color); }

        /* --- 8. NOTICIAS (NUEVO) --- */
        .news-section { 
            padding: 100px 0; background: var(--bg-body); 
            border-top: 1px solid var(--border-color);
        }
        .news-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;
        }
        .news-card {
            background: var(--bg-card); border-radius: var(--radius-md); overflow: hidden;
            border: 1px solid var(--border-color); transition: 0.3s;
            display: flex; flex-direction: column; cursor: pointer;
        }
        .news-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-md); border-color: var(--primary); }
        
        .news-img {
            height: 200px; background-color: var(--primary-light);
            display: flex; align-items: center; justify-content: center; color: var(--primary);
            font-size: 3rem;
        }
        .news-content { padding: 25px; flex: 1; display: flex; flex-direction: column; }
        .news-tag { 
            display: inline-block; background: var(--primary-light); color: var(--primary); 
            padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; margin-bottom: 10px; width: fit-content;
        }
        .news-date { font-size: 0.85rem; color: var(--text-muted); margin-bottom: 10px; display: block; }
        .news-title { font-size: 1.2rem; font-weight: 700; margin-bottom: 10px; line-height: 1.4; color: var(--text-main); }
        .news-excerpt { font-size: 0.95rem; color: var(--text-muted); margin-bottom: 20px; flex: 1; }
        .news-link { color: var(--primary); font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 5px; }

        /* --- 9. TIMELINE --- */
        .goals-section { padding: 100px 0; background: var(--bg-card); border-top: 1px solid var(--border-color); }
        .timeline-container {
            position: relative; max-width: 1000px; margin: 60px auto 0;
            display: flex; justify-content: space-between;
        }
        .timeline-container::before {
            content: ''; position: absolute; top: 18px; left: 0; right: 0; height: 4px;
            background: var(--border-color); z-index: 0; border-radius: 4px;
        }
        .timeline-container::after {
            content: ''; position: absolute; top: 18px; left: 0; width: 38%; height: 4px;
            background: var(--primary); z-index: 0; border-radius: 4px;
        }
        .t-step { position: relative; width: 22%; text-align: center; z-index: 1; }
        .t-marker {
            width: 40px; height: 40px; background: var(--bg-card);
            border: 4px solid var(--border-color); border-radius: 50%;
            margin: 0 auto 20px; transition: 0.3s; display: grid; place-items: center;
            font-weight: 700; color: var(--text-muted);
        }
        .t-step.completed .t-marker { border-color: var(--primary); background: var(--primary); color: white; }
        .t-step.active .t-marker { 
            border-color: var(--primary); background: var(--bg-card); color: var(--primary); 
            transform: scale(1.2); box-shadow: 0 0 0 5px rgba(0, 102, 204, 0.2);
        }
        .t-content h3 { font-size: 1.1rem; margin-bottom: 5px; font-weight: 700; }
        .t-date { 
            display: inline-block; background: var(--bg-card); padding: 4px 12px; border-radius: 20px;
            font-size: 0.8rem; font-weight: 800; color: var(--primary); border: 1px solid var(--border-color);
            margin-bottom: 10px;
        }
        .t-content p { font-size: 0.95rem; color: var(--text-muted); line-height: 1.5; }

        /* --- 10. NEWSLETTER --- */
        .newsletter-section {
            padding: 80px 0; background: linear-gradient(135deg, var(--primary), #004a99); color: white;
            text-align: center;
        }
        .newsletter-box { max-width: 600px; margin: 0 auto; }
        .newsletter-form { display: flex; gap: 10px; margin-top: 30px; }
        .newsletter-input {
            flex: 1; padding: 15px 25px; border-radius: 50px; border: none; font-size: 1rem;
        }
        .newsletter-btn {
            padding: 15px 30px; border-radius: 50px; background: var(--accent); color: white; border: none;
            font-weight: 700; cursor: pointer; transition: 0.3s;
        }
        .newsletter-btn:hover { background: #e65100; transform: translateY(-2px); }

        /* --- 11. FAQ --- */
        .faq-section { padding: 100px 0; max-width: 900px; margin: 0 auto; }
        .faq-card {
            background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md);
            margin-bottom: 15px; overflow: hidden; transition: 0.3s;
        }
        .faq-head {
            padding: 25px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;
            font-weight: 700; font-size: 1.1rem;
        }
        .faq-head:hover { color: var(--primary); background: var(--bg-body); }
        .faq-body {
            max-height: 0; overflow: hidden; transition: max-height 0.3s ease; padding: 0 25px;
            color: var(--text-muted); line-height: 1.7;
        }
        .faq-card.active .faq-body { max-height: 200px; padding-bottom: 25px; }
        .faq-card.active .icon-faq { transform: rotate(180deg); }
        .icon-faq { transition: 0.3s; }

        /* --- 12. FOOTER --- */
        footer {
            background: #1a1a1a; color: #ccc; padding: 80px 0 40px;
        }
        .footer-grid {
            display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr; gap: 50px; margin-bottom: 60px;
        }
        .f-info h3 { color: white; font-size: 1.8rem; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .f-header { color: white; margin-bottom: 25px; font-weight: 700; font-size: 1.1rem; }
        .f-list li { margin-bottom: 12px; }
        .f-list a { color: #999; }
        .f-list a:hover { color: var(--primary); padding-left: 5px; }
        .contact-row { display: flex; gap: 12px; margin-bottom: 15px; }
        .contact-row i { color: var(--primary); margin-top: 5px; }

        @keyframes scroll { 0% { transform: translateX(0); } 100% { transform: translateX(calc(-280px * 7 - 30px * 7)); } }
        
        /* Mobile */
        @media (max-width: 900px) {
            .hero h1 { font-size: 3rem; }
            .reviews-header { flex-direction: column; align-items: flex-start; }
            .footer-grid { grid-template-columns: 1fr; gap: 40px; }
            .timeline-container { flex-direction: column; gap: 40px; margin-left: 20px; }
            .timeline-container::before { width: 4px; height: 100%; left: 18px; top: 0; }
            .timeline-container::after { width: 4px; height: 40%; left: 18px; top: 0; }
            .t-step { width: 100%; text-align: left; padding-left: 60px; }
            .t-marker { position: absolute; left: 0; margin: 0; }
            .newsletter-form { flex-direction: column; }
        }
    </style>
</head>
<body class="light-mode">

    <!-- NAV -->
    <nav>
        <div class="wrapper nav-content">
            <div class="brand">
                <img src="media/pharmasphere_sinfondo.png" alt="Logo">
                <span>Pharmasphere</span>
            </div>
            <div class="nav-links" style="display: none;">
                <a href="#productos">Productos</a>
                <a href="#nosotros">Servicios</a>
                <a href="#opiniones">Opiniones</a>
            </div>
            <div class="nav-actions">
                <div class="theme-toggle" id="themeBtn" title="Cambiar Tema">
                    <i class="fa-solid fa-moon"></i>
                </div>
                <a href="login.php" class="btn btn-primary">Acceso Clientes</a>
            </div>
        </div>
    </nav>

    <!-- 1. HERO -->
    <section class="hero">
        <div class="wrapper">
            <h1>Soluciones Integrales para<br><span class="text-gradient">Farmacias Modernas</span></h1>
            <p>Simplifica la gestión diaria, optimiza tu inventario y mejora la atención al paciente con la plataforma líder del sector farmacéutico.</p>
            <div style="display:flex; justify-content:center; gap:20px;">
                <a href="login.php" class="btn btn-primary">Acceder al Portal</a>
                <a href="#nosotros" class="btn btn-outline">Descubrir Servicios</a>
            </div>
            <div class="hero-badges">
                <div class="badge-pill"><i class="fa-solid fa-check-circle"></i> Gestión de Stock</div>
                <div class="badge-pill"><i class="fa-solid fa-check-circle"></i> Ventas & Caja</div>
                <div class="badge-pill"><i class="fa-solid fa-check-circle"></i> IA Asistencial</div>
                <div class="badge-pill"><i class="fa-solid fa-check-circle"></i> Auditoría 24/7</div>
            </div>
        </div>
    </section>

    <!-- PARTNERS -->
    <div class="partners-bar">
        <div class="wrapper partners-track">
            <div class="partner-logo"><i class="fa-solid fa-handshake"></i> COFARES</div>
            <div class="partner-logo"><i class="fa-solid fa-truck-medical"></i> BIDAFARMA</div>
            <div class="partner-logo"><i class="fa-solid fa-building-columns"></i> HEFAME</div>
            <div class="partner-logo"><i class="fa-solid fa-hospital"></i> ALLIANCE</div>
            <div class="partner-logo"><i class="fa-solid fa-capsules"></i> PFIZER</div>
        </div>
    </div>

    <!-- 2. CARRUSEL DE CATEGORÍAS -->
    <section class="products-marquee" id="productos">
        <div class="wrapper section-intro">
            <h2>Catálogo de Suministros</h2>
            <p>Accede a miles de referencias organizadas inteligentemente.</p>
        </div>
        <div class="marquee-track">
            <?php 
            $cats = [
                ['pills', 'Medicamentos', 'Prescripción y genéricos', 'Revisar Stock'],
                ['pump-medical', 'Dermocosmética', 'Cuidado de la piel', 'Ver Catálogo'],
                ['baby', 'Mundo Infantil', 'Alimentación e higiene', 'Ver Ofertas'],
                ['kit-medical', 'Primeros Auxilios', 'Curas y botiquines', 'Reponer'],
                ['flask', 'Fórmulas', 'Laboratorio magistral', 'Gestionar'],
                ['apple-whole', 'Nutrición', 'Dietética y vitaminas', 'Ver Gama'],
                ['crutch', 'Ortopedia', 'Ayudas técnicas', 'Consultar']
            ];
            for($x=0; $x<2; $x++): 
                foreach($cats as $c): ?>
                <div class="cat-card" onclick="window.location.href='login.php'">
                    <div class="cat-icon"><i class="fa-solid fa-<?= $c[0] ?>"></i></div>
                    <div class="cat-info">
                        <h3><?= $c[1] ?></h3>
                        <p><?= $c[2] ?></p>
                    </div>
                    <button class="cat-btn"><?= $c[3] ?></button>
                </div>
            <?php endforeach; endfor; ?>
        </div>
    </section>

    <!-- 3. POR QUÉ PHARMASPHERE -->
    <section class="features-section" id="nosotros">
        <div class="wrapper">
            <div class="section-intro">
                <h2>Tu Socio Estratégico</h2>
                <p>Más que un software, somos la infraestructura que tu farmacia necesita.</p>
            </div>
            <div class="features-grid">
                <div class="feature-item f-green">
                    <div class="f-icon-circle"><i class="fa-solid fa-boxes-stacked"></i></div>
                    <h2>Logística y Stock</h2>
                    <p>Controla caducidades, lotes y faltas en tiempo real. Sistema predictivo de stock para no perder ventas.</p>
                    <div class="feature-img-placeholder">
                        <i class="fa-solid fa-chart-area fa-2x" style="margin-right:10px;"></i> Gráfico de Rotación
                    </div>
                </div>
                <div class="feature-item">
                    <div class="f-icon-circle"><i class="fa-solid fa-cash-register"></i></div>
                    <h2>Punto de Venta</h2>
                    <p>Sistema de caja rápido. Gestiona recetas electrónicas y ventas libres con una interfaz ágil.</p>
                    <div class="feature-img-placeholder">
                        <i class="fa-solid fa-receipt fa-2x" style="margin-right:10px;"></i> Interfaz de Caja
                    </div>
                </div>
                <div class="feature-item">
                    <div class="f-icon-circle"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
                    <h2>Asistente IA</h2>
                    <p>Apoyo inmediato en el mostrador. Resuelve dudas sobre interacciones y posología al instante.</p>
                    <div class="feature-img-placeholder">
                        <i class="fa-solid fa-robot fa-2x" style="margin-right:10px;"></i> Chat Asistencial
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- NUEVO: NOTICIAS -->
    <section class="news-section">
        <div class="wrapper">
            <div class="section-intro">
                <h2>Actualidad Farmacéutica</h2>
                <p>Novedades del sector y actualizaciones de la plataforma.</p>
            </div>
            <div class="news-grid">
                <!-- Noticia 1 -->
                <div class="news-card">
                    <div class="news-img"><i class="fa-solid fa-file-medical"></i></div>
                    <div class="news-content">
                        <span class="news-tag">Regulación</span>
                        <span class="news-date">10 Octubre, 2023</span>
                        <h3 class="news-title">Cambios en la normativa de receta electrónica para 2024</h3>
                        <p class="news-excerpt">El Ministerio de Sanidad anuncia nuevas medidas para la interoperabilidad...</p>
                        <a href="#" class="news-link">Leer más <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
                <!-- Noticia 2 -->
                <div class="news-card">
                    <div class="news-img"><i class="fa-solid fa-microscope"></i></div>
                    <div class="news-content">
                        <span class="news-tag">I+D</span>
                        <span class="news-date">28 Septiembre, 2023</span>
                        <h3 class="news-title">Pharmasphere integra Gemini para detección de interacciones</h3>
                        <p class="news-excerpt">Nuestra nueva actualización permite escanear prospectos automáticamente...</p>
                        <a href="#" class="news-link">Leer más <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
                <!-- Noticia 3 -->
                <div class="news-card">
                    <div class="news-img"><i class="fa-solid fa-user-doctor"></i></div>
                    <div class="news-content">
                        <span class="news-tag">Consejo</span>
                        <span class="news-date">15 Septiembre, 2023</span>
                        <h3 class="news-title">Cómo optimizar el stock de invierno y evitar roturas</h3>
                        <p class="news-excerpt">Guía práctica para preparar tu farmacia ante la temporada de gripe...</p>
                        <a href="#" class="news-link">Leer más <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. TIMELINE DE OBJETIVOS -->
    <section class="goals-section">
        <div class="wrapper">
            <div class="section-intro">
                <h2>Nuestra Hoja de Ruta</h2>
                <p>De un pequeño proyecto local a la revolución sanitaria.</p>
            </div>
            <div class="timeline-container">
                <div class="t-step completed">
                    <div class="t-marker"><i class="fa-solid fa-check"></i></div>
                    <div class="t-content">
                        <h3>Inicios</h3>
                        <span class="t-date">2023</span>
                        <p>Proyecto piloto en Cartagena.</p>
                    </div>
                </div>
                <div class="t-step active">
                    <div class="t-marker"><i class="fa-solid fa-location-dot"></i></div>
                    <div class="t-content">
                        <h3>Expansión</h3>
                        <span class="t-date">2025</span>
                        <p>Lanzamiento v2.5 con IA integrada.</p>
                    </div>
                </div>
                <div class="t-step">
                    <div class="t-marker">3</div>
                    <div class="t-content">
                        <h3>Futuro Próximo</h3>
                        <span class="t-date">2026</span>
                        <p>App para pacientes y red regional.</p>
                    </div>
                </div>
                <div class="t-step">
                    <div class="t-marker">4</div>
                    <div class="t-content">
                        <h3>Visión Global</h3>
                        <span class="t-date">2030</span>
                        <p>Estándar nacional de salud digital.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. OPINIONES -->
    <section class="reviews-section" id="opiniones">
        <div class="wrapper">
            <div class="reviews-header">
                <div class="rating-summary">
                    <div class="big-score">4.9</div>
                    <div class="score-details">
                        <div class="stars-row">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-stroke"></i>
                        </div>
                        <div class="total-reviews">Basado en <strong>2,540 opiniones</strong></div>
                    </div>
                </div>
                <div class="review-filters">
                    <button class="filter-btn active">Más Recientes</button>
                    <button class="filter-btn">Más Útiles</button>
                </div>
            </div>
            <div class="reviews-grid">
                <div class="review-card">
                    <div class="reviewer-profile">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" class="r-avatar" alt="User">
                        <div class="r-meta">
                            <h4>Elena Martínez</h4>
                            <span><i class="fa-solid fa-certificate"></i> Farmacia Lda. Martínez</span>
                        </div>
                    </div>
                    <p class="r-content">"La gestión de caducidades es impecable. El sistema nos avisa con meses de antelación."</p>
                    <span class="r-date">Hace 2 días</span>
                </div>
                <div class="review-card">
                    <div class="reviewer-profile">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" class="r-avatar" alt="User">
                        <div class="r-meta">
                            <h4>Carlos Ruiz</h4>
                            <span><i class="fa-solid fa-certificate"></i> Farmacia Central</span>
                        </div>
                    </div>
                    <p class="r-content">"El soporte técnico es excelente y la rapidez del buscador es inigualable."</p>
                    <span class="r-date">Hace 1 semana</span>
                </div>
                <div class="review-card">
                    <div class="reviewer-profile">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" class="r-avatar" alt="User">
                        <div class="r-meta">
                            <h4>Ana Soler</h4>
                            <span><i class="fa-solid fa-certificate"></i> Farmacia del Puerto</span>
                        </div>
                    </div>
                    <p class="r-content">"Muy intuitivo. Mis auxiliares nuevos aprendieron a usarlo en una tarde."</p>
                    <span class="r-date">Hace 3 semanas</span>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. FAQ -->
    <section class="faq-section">
        <div class="section-intro">
            <h2>Preguntas Frecuentes</h2>
        </div>
        <div class="faq-card active" onclick="toggleFaq(this)">
            <div class="faq-head">¿Puedo importar mi stock desde Excel?<i class="fa-solid fa-chevron-down icon-faq"></i></div>
            <div class="faq-body"><p>Sí, absolutamente. Incluimos una herramienta de migración masiva.</p></div>
        </div>
        <div class="faq-card" onclick="toggleFaq(this)">
            <div class="faq-head">¿Es compatible con lectores de códigos?<i class="fa-solid fa-chevron-down icon-faq"></i></div>
            <div class="faq-body"><p>Totalmente. Pharmasphere es compatible con el 99% de los lectores USB y Bluetooth.</p></div>
        </div>
        <div class="faq-card" onclick="toggleFaq(this)">
            <div class="faq-head">¿Qué incluye el soporte técnico?<i class="fa-solid fa-chevron-down icon-faq"></i></div>
            <div class="faq-body"><p>Ofrecemos soporte por email y chat en vivo para todas las farmacias, además de una base de conocimientos completa.</p></div>
        </div>
    </section>

    <!-- NUEVO: NEWSLETTER -->
    <section class="newsletter-section">
        <div class="wrapper newsletter-box">
            <h2>Mantente Actualizado</h2>
            <p>Recibe las últimas novedades sobre gestión farmacéutica y actualizaciones de la plataforma.</p>
            <form class="newsletter-form" onsubmit="event.preventDefault(); alert('¡Gracias por suscribirte!');">
                <input type="email" class="newsletter-input" placeholder="Tu correo electrónico..." required>
                <button type="submit" class="newsletter-btn">Suscribirme</button>
            </form>
        </div>
    </section>

    <!-- 7. FOOTER -->
    <footer>
        <div class="wrapper">
            <div class="footer-grid">
                <div class="f-info">
                    <h3><img src="media/pharmasphere_sinfondo.png" style="height:35px;"> Pharmasphere</h3>
                    <p>Plataforma líder en gestión farmacéutica. Innovación y cercanía.</p>
                    <div style="margin-top:20px;">
                        <div class="contact-row"><i class="fa-solid fa-location-dot"></i> Parque Tecnológico, Cartagena</div>
                        <div class="contact-row"><i class="fa-solid fa-envelope"></i> contacto@pharmasphere.com</div>
                        <div class="contact-row"><i class="fa-solid fa-phone"></i> +34 968 00 00 00</div>
                    </div>
                </div>
                <div class="f-col">
                    <div class="f-header">Producto</div>
                    <ul class="f-list">
                        <li><a href="#">Características</a></li>
                        <li><a href="#">Planes</a></li>
                        <li><a href="#">Hardware</a></li>
                    </ul>
                </div>
                <div class="f-col">
                    <div class="f-header">Legal</div>
                    <ul class="f-list">
                        <li><a href="#">Aviso Legal</a></li>
                        <li><a href="#">Privacidad</a></li>
                        <li><a href="#">Cookies</a></li>
                    </ul>
                </div>
                <div class="f-col">
                    <div class="f-header">Síguenos</div>
                    <div style="margin-top:10px; font-size:1.5rem;">
                        <a href="#" style="color:white; margin-right:15px;"><i class="fa-brands fa-linkedin"></i></a>
                        <a href="#" style="color:white;"><i class="fa-brands fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            <div style="border-top:1px solid #333; padding-top:20px; text-align:center; font-size:0.9rem; color:#666;">
                &copy; <?= date('Y') ?> Pharmasphere Solutions S.L. Todos los derechos reservados.
            </div>
        </div>
    </footer>

    <script>
        const themeBtn = document.getElementById('themeBtn');
        const body = document.body;
        
        themeBtn.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            const icon = themeBtn.querySelector('i');
            if (body.classList.contains('dark-mode')) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        });

        function toggleFaq(card) {
            document.querySelectorAll('.faq-card').forEach(c => {
                if(c !== card) c.classList.remove('active');
            });
            card.classList.toggle('active');
        }
    </script>

</body>
</html>