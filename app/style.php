<?php
// ---------------------------------------------------------------------------------------------------------------\\
/* ========= 5. HELPERS DE VISTA =========== */

// Escapa el HTML para prevenir ataques XSS.
function h($str)
{
    return htmlspecialchars($str ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Imprime el HTML, los estilos CSS y la navegación.
function headerHtml($title = 'Pharmasphere') 
{
    $is_logged_in = isset($_SESSION['user_id']);
    $is_admin = $is_logged_in && isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'Administrador';
    $current_page = basename($_SERVER['SCRIPT_NAME']);

    echo "<!DOCTYPE html><html lang='es'><head><meta charset='utf-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    
    // Fuentes e Iconos
    echo "<link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css\"/>";
    echo "<link href=\"https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap\" rel=\"stylesheet\">";

    echo "<style>
            :root {
                --color-primario: #007BFF;
                --color-primario-hover: #0056b3;
                --color-secundario: #56ac47;
                --color-secundario-hover: #438a36;
                --color-peligro: #dc3545;
                --color-peligro-hover: #b02a37;
                --color-fondo: #f4f7f6;
                --color-card: #ffffff;
                --color-borde: #e0e0e0;
                --color-texto: #212529;
                --color-texto-muted: #6c757d;
                --ancho-max: 1200px;
                --radio-borde: 12px;
                --sombra: 0 4px 12px rgba(0, 0, 0, 0.05);
            }

            /* --- MODO OSCURO (Verde Pharmasphere) --- */
            body.tema-oscuro { 
                --color-fondo: #121212;
                --color-card: #1e1e1e;
                --color-texto: #e0e0e0;
                --color-texto-muted: #a0a0a0;
                --color-borde: #333;
                
                /* AQUÍ EL CAMBIO: El color principal ahora es VERDE en modo oscuro */
                --color-primario: #56ac47; 
                --color-primario-hover: #438a36;
                
                /* El secundario (acciones positivas) se mantiene igual o se ajusta */
                --color-secundario: #56ac47; 
            }

            body { 
                font-family: 'Outfit', sans-serif; 
                background-color: var(--color-fondo);
                color: var(--color-texto);
                margin: 0;
                padding-top: 80px;
                min-height: 100vh;
                display: flex; flex-direction: column;
            }

            /* --- TAMAÑO DE FUENTE --- */
            body.fuente-grande {
                font-size: 1.15rem; /* Aumenta el tamaño base un 15% */
            }
            /* Ajuste para que los iconos no se desmadren */
            body.fuente-grande i {
                font-size: 1.1em; 
            }

            /* Forzamos la escala de los enlaces de navegación y del texto del usuario */
            body.fuente-grande .nav-link,
            body.fuente-grande .nav-right strong {
                font-size: 1.15em !important; 
            }

            /* Aseguramos que los iconos también se vean grandes, pero controlados */
            body.fuente-grande .nav-right i {
                font-size: 1.4em !important; 
            }
            
            a { text-decoration: none; transition: 0.2s; }
            h1, h2, h3 { font-weight: 700; letter-spacing: -0.5px; margin-bottom: 1rem; }

            /* --- BARRA DE BÚSQUEDA COMPACTA (Izquierda) --- */
            form.search-form {
                display: flex;
                align-items: center;
                gap: 10px;
                margin: 0 0 20px 0 !important; 
                max-width: 500px !important;   
                width: 100%;
                padding: 12px !important;      
                background-color: var(--color-card);
                border-radius: var(--radio-borde);
                border: 1px solid var(--color-borde);
                box-shadow: var(--sombra);
            }

            .search-input-group {
                flex-grow: 1;
                position: relative;
            }

            form.search-form input {
                margin-bottom: 0 !important; 
                height: 38px;             
                font-size: 0.95rem;
            }

            form.search-form button {
                height: 38px;             
                padding: 0 20px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                white-space: nowrap;
            }
            
            /* NAVBAR */
            .navbar {
                position: fixed; top: 0; left: 0; right: 0; height: 70px;
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(10px);
                border-bottom: 1px solid var(--color-borde);
                display: flex; align-items: center; justify-content: space-between;
                padding: 0 4%; z-index: 1000;
                box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            }

            /* --- LOGO PRINCIPAL --- */
            .navbar-logo-img {
                height: 58px;            
                width: auto;             
                vertical-align: middle;
                transition: transform 0.3s ease, filter 0.3s ease; 
                filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
            }

            .navbar-logo-img:hover {
                transform: scale(1.1);   
                filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2)); 
            }

            body.tema-oscuro .navbar { background: rgba(30, 30, 30, 0.9); }
            .nav-left, .nav-right { display: flex; align-items: center; gap: 15px; }
            
            /* --- ESTILOS GENERALES DE LOS ENLACES DEL MENÚ --- */
            .nav-link {
                color: var(--color-texto-muted); 
                font-weight: 600; 
                font-size: 1.10rem;
                padding: 8px 16px; 
                border-radius: 50px; 
                transition: all 0.2s ease; 
                display: inline-flex; 
                align-items: center;
                justify-content: center;
            }

            .nav-link:hover { 
                color: var(--color-primario); 
                background-color: color-mix(in srgb, var(--color-primario), transparent 90%);
                transform: scale(1.08); 
            }

            .nav-link.active {
                background-color: var(--color-primario); 
                color: white !important;
                box-shadow: 0 4px 10px color-mix(in srgb, var(--color-primario), transparent 60%);
            }
            
            .nav-link.active:hover {
                transform: scale(1.08);
            }

            a.nav-link[href='logout.php'] {
                color: var(--color-peligro) !important;
                padding: 15px 8px; 
            }

            a.nav-link[href='logout.php']:hover {
                background-color: rgba(220, 53, 69, 0.15) !important;
                color: var(--color-peligro) !important;
                transform: scale(1.15); 
                box-shadow: 0 0 10px rgba(220, 53, 69, 0.2);
            }
            
            body.tema-oscuro .nav-link.active { 
                color: #121212 !important; 
                box-shadow: 0 4px 10px rgba(86, 172, 71, 0.3);
            }

            /* TABLAS */
            table {
                width: 100%; border-collapse: separate; border-spacing: 0;
                background-color: var(--color-card); border-radius: var(--radio-borde);
                overflow: hidden; margin-top: 20px; box-shadow: var(--sombra);
                border: 1px solid var(--color-borde);
            }
            th {
                background-color: var(--color-primario); 
                color: white;  
                padding: 15px;
                text-align: left;
                font-weight: 600;
                text-transform: uppercase;
                font-size: 0.85rem;
                letter-spacing: 0.5px;
                border-bottom: none; 
            }
            td { padding: 15px; border-bottom: 1px solid var(--color-borde); vertical-align: middle; }
            body.tema-oscuro tbody tr:nth-child(even) { background-color: rgba(255,255,255,0.03); }

            /* ALERTAS */
            .alert {
                padding: 15px; margin: 20px auto; max-width: 800px; width: 90%;
                border-radius: var(--radio-borde); font-weight: 500;
                animation: fadeIn 0.5s ease-out; border: 1px solid transparent;
            }
            .alert.success {
                background-color: var(--color-secundario); color: white;
                border-color: var(--color-secundario-hover);
            }
            .alert.success::before {
                font-family: 'Font Awesome 6 Free'; font-weight: 900; content: '\\f00c'; margin-right: 10px;
            }

            /* ESTILO DE ERROR */
            .alert.error, .error {
                background-color: #f8d7da;
                color: #842029; 
                border-color: #f5c6cb;
            }
            body.tema-oscuro .alert.error, body.tema-oscuro .error {
                background-color: rgba(220, 53, 69, 0.15) !important;
                color: #ff6b6b !important;
                border: 1px solid #ff6b6b !important;
            }

            /* STOCKS */
            .stock-low { 
                color: var(--color-peligro); 
                animation: pulse-low 1.5s infinite ease-in-out; 
            }
            .stock-med { color: #fd7e14; }
            .stock-high { color: var(--color-secundario); }

            /* FORMULARIOS */
            form:not(.inline) {
                background-color: var(--color-card); padding: 30px;
                border-radius: var(--radio-borde); border: 1px solid var(--color-borde);
                box-shadow: var(--sombra); max-width: 800px; margin: 0 auto;
            }

            form:not(.inline), .theme-card {
                transition: all 0.3s ease-out;
            }

            /* TARJETA DE SELECCIÓN DE TEMA */
            .theme-card {
                background-color: var(--color-card);
                border: 1px solid var(--color-borde);
                border-radius: var(--radio-borde);
                box-shadow: var(--sombra);
                max-width: 500px; 
                width: 100%;
                margin: 0;        
                padding: 30px;
            }

            .theme-options {
                display: flex;
                gap: 15px; 
                margin-top: 15px;
            }

            .theme-btn {
                flex: 1; 
                display: flex;
                flex-direction: column; 
                align-items: center;
                justify-content: center;
                padding: 20px;
                border: 2px solid var(--color-borde);
                border-radius: 12px;
                background-color: var(--color-fondo);
                color: var(--color-texto-muted);
                cursor: pointer;
                transition: all 0.2s ease;
                font-weight: 600;
            }

            .theme-btn i {
                font-size: 2rem; 
                margin-bottom: 10px;
            }

            .theme-btn:hover {
                border-color: var(--color-primario);
                color: var(--color-primario);
                background-color: color-mix(in srgb, var(--color-primario), transparent 95%);
                transform: translateY(-2px);
            }

            input[type='radio']:checked + .theme-btn {
                border-color: var(--color-primario);
                background-color: color-mix(in srgb, var(--color-primario), transparent 90%);
                color: var(--color-primario);
                box-shadow: 0 0 0 2px color-mix(in srgb, var(--color-primario), transparent 80%);
            }

            input[type='radio'] { display: none; }
            
            .btn-save-prefs {
                margin-top: 25px;
                width: 100%;
                padding: 12px;
                font-size: 1rem;
            }
            
            label {
                display: block; 
                margin-bottom: 8px;
                font-weight: 600;
                font-size: 0.9rem;
            }

            input, select, textarea {
                width: 100%; padding: 12px; margin-bottom: 20px; 
                border: 1px solid var(--color-borde); border-radius: 8px;
                background: var(--color-fondo); color: var(--color-texto);
                box-sizing: border-box; transition: 0.3s;
            }
            
            input:focus, select:focus, textarea:focus {
                border-color: var(--color-primario);
                outline: none;
                box-shadow: 0 0 0 4px color-mix(in srgb, var(--color-primario), transparent 85%);
            }
            
            /* CHECKBOX */
            .checkbox-group {
                display: flex;
                align-items: center;
                margin-bottom: 20px;
                background: rgba(0,0,0,0.02);
                padding: 10px;
                border-radius: 8px;
            }
            .checkbox-group label {
                display: inline-block;
                margin-bottom: 0;
                margin-right: 15px;
                cursor: pointer;
            }
            .checkbox-group input[type=checkbox] {
                width: 20px !important;
                height: 20px;
                margin: 0;
                cursor: pointer;
            }

            input[type=checkbox] {
                width: auto !important;   
                display: inline-block;    
                vertical-align: middle;   
                margin-left: 10px;        
                margin-top: 17px;            
                transform: scale(1.3);    
                cursor: pointer;          
                position: relative; 
                top: -1px; 
            }

            input[type=checkbox]:focus {
                outline: none !important;      
                box-shadow: none !important;   
                border-color: var(--color-borde); 
            }

            /* --- ESTILOS DEL CHAT FARMACÉUTICO --- */
            .chat-container {
                background-color: var(--color-card);
                border: 1px solid var(--color-borde);
                border-radius: 16px;
                box-shadow: 0 8px 24px rgba(0,0,0,0.08); 
                max-width: 800px;
                margin: 0 auto 40px auto;
                overflow: hidden;
                display: flex;
                flex-direction: column;
            }

            .chat-header {
                background-color: var(--color-primario);
                color: white;
                padding: 16px 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-weight: 600;
                font-size: 1.1rem;
            }

            .chat-header a {
                color: rgba(255, 255, 255, 0.8);
                font-size: 0.85rem;
                text-decoration: none;
                transition: color 0.2s ease;
                display: flex;
                align-items: center;
                gap: 6px;
                background: rgba(0,0,0,0.15);
                padding: 6px 12px;
                border-radius: 20px;
            }

            .chat-header a:hover {
                color: white;
                background: rgba(0,0,0,0.3);
            }

            .chat-window {
                height: 380px; 
                overflow-y: auto;
                padding: 20px;
                background-color: #f4f7f6; 
                display: flex;
                flex-direction: column;
                gap: 15px;
            }

            body.tema-oscuro .chat-window {
                background-color: #1a1a1a;
            }

            .msg {
                max-width: 80%;
                padding: 12px 18px;
                border-radius: 18px;
                font-size: 0.95rem;
                line-height: 1.4;
                position: relative;
                word-wrap: break-word;
                box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            }

            .msg-user {
                align-self: flex-end;
                background-color: var(--color-primario);
                color: white;
                border-bottom-right-radius: 4px; 
            }

            .msg-model {
                align-self: flex-start;
                background-color: var(--color-card);
                border: 1px solid var(--color-borde);
                color: var(--color-texto);
                border-bottom-left-radius: 4px; 
            }

            .msg-img {
                max-width: 150px;
                border-radius: 8px;
                margin-bottom: 8px;
                display: block;
            }

            .chat-controls {
                padding: 15px 20px;
                border-top: 1px solid var(--color-borde);
                background-color: var(--color-card);
            }

            .chat-form {
                display: flex;
                gap: 12px;
                align-items: center;
                margin: 0;
            }

            .btn-camera {
                color: var(--color-texto-muted);
                cursor: pointer;
                padding: 10px;
                transition: color 0.2s, transform 0.2s;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .btn-camera:hover {
                color: var(--color-primario);
                transform: scale(1.1);
            }

            .chat-input {
                flex-grow: 1;
                margin: 0 !important; 
                padding: 14px 20px !important;
                border-radius: 30px !important; 
                border: 1px solid var(--color-borde) !important;
                background-color: var(--color-fondo) !important;
                font-size: 0.95rem;
            }

            .btn-send-chat {
                width: 48px;
                height: 48px;
                border-radius: 50%;
                background-color: var(--color-primario);
                color: white;
                border: none;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: transform 0.2s, background-color 0.2s, box-shadow 0.2s;
                padding: 0;
                flex-shrink: 0;
            }

            .btn-send-chat:hover {
                background-color: var(--color-primario-hover);
                transform: scale(1.05);
                box-shadow: 0 4px 10px color-mix(in srgb, var(--color-primario), transparent 60%);
            }

            /* BOTONES */
            .btn-table {
                padding: 6px 12px; border-radius: 8px; font-size: 0.85rem; font-weight: 600;
                display: inline-flex; align-items: center; gap: 6px; border: 1px solid transparent;
            }
            .btn-edit {
                background-color: color-mix(in srgb, var(--color-primario), transparent 85%);
                color: var(--color-primario);
                border: none;
                padding: 6px 12px;
                border-radius: 8px;
                font-size: 0.85rem;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                transition: all 0.3s ease;
            }

            .btn-edit:active, button:active, .nav-link:active, .pagination a:active, .pagination strong:active {
                transform: scale(0.95) !important; 
                filter: brightness(0.9);           
            }

            .btn-edit:hover {
                background-color: var(--color-primario);
                color: white;
                transform: scale(1.05);
                cursor: pointer;
                text-decoration: none;
            }
            
            .btn-delete { background-color: rgba(239, 68, 68, 0.15); color: #ef4444; transition: all 0.3s ease;}
            .btn-delete:hover { background-color: #ef4444; color: white; transform: scale(1.05);}
            .btn-buy { background: var(--color-secundario); color: white; padding: 6px 12px; border-radius: 8px; }
            .btn-buy:hover { background: var(--color-secundario-hover); }
            button[type='submit'] { 
                padding: 12px 24px; background: var(--color-primario); color: white; 
                border: none; border-radius: 8px; font-weight: 600; cursor: pointer; 
            }
            button[type='submit']:hover { background: var(--color-primario-hover); }

            /* PELIGRO (Delete) */
            .input-danger { border-color: var(--color-peligro) !important; }
            .input-danger:focus {
                border-color: var(--color-peligro) !important; 
                box-shadow: 0 0 0 4px color-mix(in srgb, var(--color-peligro), transparent 85%) !important;
                outline: none;
            }

            button.btn-danger {
                background-color: var(--color-peligro) !important; 
                color: white !important;
                border: none;
                padding: 12px 24px;
                border-radius: 8px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            button.btn-danger:hover {
                background-color: color-mix(in srgb, var(--color-peligro), black 40%) !important;
                filter: none; 
                transform: scale(1.05);
                cursor: pointer;
            }
            
            /* PAGINACIÓN */
            .pagination {
                display: flex;             
                align-items: center;       
                justify-content: center;   
                gap: 8px;                  
                margin-top: 30px;          
                margin-bottom: 20px;
                flex-wrap: wrap;           
            }

            .pagination a, .pagination strong {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 40px;           
                height: 40px;              
                padding: 0 10px;
                border-radius: 8px;        
                text-decoration: none;
                font-weight: 600;
                font-size: 0.95rem;
                transition: all 0.2s ease;
                background-color: var(--color-card);
                border: 1px solid var(--color-borde);
                color: var(--color-texto);
            }

            .pagination a:hover {
                border-color: var(--color-primario);
                color: var(--color-primario);
                background-color: color-mix(in srgb, var(--color-primario), transparent 90%);
                transform: translateY(-2px); 
            }

            .pagination strong {
                background-color: var(--color-primario); 
                color: white;
                border-color: var(--color-primario);
                box-shadow: 0 4px 10px color-mix(in srgb, var(--color-primario), transparent 60%);
            }

            /* FILAS CLICABLES */
            tr.clickable-row {
                cursor: pointer; 
                transition: background-color 0.2s ease;
            }
            
            tr.clickable-row:hover {
                background-color: color-mix(in srgb, var(--color-primario), transparent 90%) !important;
            }
            
            tr.clickable-row .btn-table {
                position: relative;
                z-index: 2;
            }

            @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
            .main-container { animation: fadeIn 0.5s ease-out; max-width: var(--ancho-max); margin: 0 auto; padding: 0 20px; width: 100%; }
            .footer-bottom { text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid var(--color-borde); }

            /* =========================================
               DASHBOARD EVOLUCIONADO (UI MODERNA)
               ========================================= */

            /* --- Cabecera de Bienvenida --- */
            .dashboard-welcome {
                display: flex;
                justify-content: space-between;
                align-items: flex-end;
                margin-bottom: 30px;
                animation: fadeIn 0.6s ease-out;
            }

            .welcome-title {
                font-size: 2.2rem;
                margin-bottom: 5px;
                font-weight: 700;
            }

            .text-highlight { color: var(--color-primario); }
            
            .welcome-subtitle {
                color: var(--color-texto-muted);
                font-size: 1.1rem;
                margin: 0;
            }

            .welcome-date {
                background-color: var(--color-card);
                padding: 10px 20px;
                border-radius: 50px;
                border: 1px solid var(--color-borde);
                box-shadow: var(--sombra);
                display: flex;
                align-items: center;
                gap: 10px;
                font-weight: 600;
                color: var(--color-texto-muted);
            }

            /* --- Layout Principal --- */
            .dashboard-layout {
                display: grid;
                grid-template-columns: 2.5fr 1fr; 
                gap: 25px;
                margin-bottom: 40px;
            }

            @media (max-width: 992px) {
                .dashboard-layout {
                    grid-template-columns: 1fr;
                }
            }

            .dash-main-col, .dash-side-col {
                display: flex;
                flex-direction: column;
                gap: 25px;
            }

            /* --- Tarjetas Base Mejoradas --- */
            .dash-card {
                background-color: var(--color-card);
                border: 1px solid var(--color-borde);
                border-radius: 16px;
                padding: 25px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.03);
                animation: fadeIn 0.6s ease-out;
                position: relative;
                overflow: hidden;
            }

            .card-header {
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 1px solid var(--color-borde);
            }

            .card-header h3 {
                margin: 0;
                font-size: 1.2rem;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            /* Colores utilitarios */
            .text-warning { color: #f59e0b; }
            .text-danger { color: var(--color-peligro); }
            .text-success { color: var(--color-secundario); }
            .text-primary { color: var(--color-primario); }
            .bg-blue-light { background-color: color-mix(in srgb, var(--color-primario), transparent 85%); }
            .bg-green-light { background-color: color-mix(in srgb, var(--color-secundario), transparent 85%); }

            /* --- Lista de Notificaciones --- */
            .notification-list { list-style: none; padding: 0; margin: 0; }
            
            .notification-item {
                display: flex;
                align-items: flex-start;
                gap: 15px;
                padding: 15px;
                border-radius: 12px;
                transition: background-color 0.2s;
            }

            .notification-item:hover {
                background-color: color-mix(in srgb, var(--color-texto), transparent 97%);
            }

            .notification-item.unread {
                background-color: color-mix(in srgb, var(--color-primario), transparent 95%);
                border-left: 3px solid var(--color-primario);
            }

            .notif-icon {
                width: 40px; height: 40px; border-radius: 10px; display: flex;
                align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;
            }

            .notif-content { flex-grow: 1; }
            .notif-content strong { display: block; margin-bottom: 4px; color: var(--color-texto); }
            .notif-content p { margin: 0; font-size: 0.9rem; color: var(--color-texto-muted); }
            .notif-time { font-size: 0.8rem; color: #999; white-space: nowrap; }

            /* --- Categorías --- */
            .category-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 15px;
            }

            .cat-card {
                display: block;
                padding: 20px;
                border: 1px solid var(--color-borde);
                border-radius: 12px;
                text-align: center;
                transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                background-color: var(--color-fondo);
            }

            .cat-card:hover {
                transform: translateY(-5px);
                border-color: var(--color-primario);
                box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            }

            .cat-icon { font-size: 2rem; color: var(--color-texto-muted); margin-bottom: 15px; transition: color 0.3s; }
            .cat-card:hover .cat-icon { color: var(--color-primario); }
            .cat-card h4 { margin: 0 0 5px 0; color: var(--color-texto); font-size: 1.1rem; }
            .cat-card p { margin: 0; font-size: 0.85rem; color: var(--color-texto-muted); }

            /* --- Perfil Lateral --- */
            .profile-summary-card { text-align: center; display: flex; flex-direction: column; align-items: center; }
            .profile-avatar {
                width: 80px; height: 80px; border-radius: 50%;
                background: linear-gradient(135deg, var(--color-primario), color-mix(in srgb, var(--color-primario), black 20%));
                color: white; display: flex; align-items: center; justify-content: center;
                font-size: 2.5rem; margin-bottom: 15px;
                box-shadow: 0 8px 15px color-mix(in srgb, var(--color-primario), transparent 70%);
            }

            .profile-summary-card h4 { margin: 0 0 5px 0; font-size: 1.4rem; }
            
            .badge-role {
                background-color: color-mix(in srgb, var(--color-texto), transparent 90%);
                color: var(--color-texto-muted); padding: 4px 12px; border-radius: 20px;
                font-size: 0.85rem; font-weight: 600; margin-bottom: 20px;
            }

            .profile-stats {
                width: 100%; display: flex; justify-content: space-around; padding: 15px 0;
                border-top: 1px solid var(--color-borde); border-bottom: 1px solid var(--color-borde);
                margin-bottom: 20px;
            }

            .stat-item { display: flex; flex-direction: column; gap: 5px; }
            .stat-label { font-size: 0.8rem; color: var(--color-texto-muted); text-transform: uppercase; }
            .stat-val { font-weight: 700; color: var(--color-texto); }
            .stat-val.text-success i { font-size: 0.6rem; vertical-align: middle; }

            /* --- Botones Dash --- */
            .btn-dash {
                display: inline-flex; align-items: center; justify-content: center; gap: 8px;
                padding: 12px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;
            }
            .btn-outline { border: 1px solid var(--color-borde); color: var(--color-texto); background: transparent; }
            .btn-outline:hover { background: var(--color-fondo); border-color: var(--color-texto-muted); }
            .w-100 { width: 100%; box-sizing: border-box;}

            /* --- Lista de Acciones Rápidas --- */
            .action-list { display: flex; flex-direction: column; gap: 10px; }
            .action-item {
                display: flex; align-items: center; padding: 15px; border: 1px solid var(--color-borde);
                border-radius: 12px; color: var(--color-texto); transition: all 0.2s; background: var(--color-fondo);
            }
            .action-item:hover { border-color: var(--color-primario); transform: translateX(5px); }
            .action-primary {
                border-color: color-mix(in srgb, var(--color-primario), transparent 50%);
                background: color-mix(in srgb, var(--color-primario), transparent 95%);
            }
            .action-icon {
                width: 40px; height: 40px; border-radius: 8px; background: var(--color-card);
                display: flex; align-items: center; justify-content: center; margin-right: 15px;
                color: var(--color-primario); box-shadow: var(--sombra);
            }
            .action-text { flex-grow: 1; display: flex; flex-direction: column; }
            .action-text strong { font-size: 1rem; }
            .action-text span { font-size: 0.85rem; color: var(--color-texto-muted); }
            .action-arrow { color: var(--color-texto-muted); transition: transform 0.2s; }
            .action-item:hover .action-arrow { transform: translateX(3px); color: var(--color-primario); }

            /* --- Zona Admin --- */
            .admin-section-divider {
                display: flex; align-items: center; margin: 40px 0 30px;
                color: var(--color-texto-muted); font-weight: 600; text-transform: uppercase;
                letter-spacing: 1px; font-size: 0.9rem;
            }
            .admin-section-divider hr {
                flex-grow: 1; border: none; border-top: 1px dashed var(--color-borde); margin-right: 15px;
            }

            .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px; margin-bottom: 25px; }
            .kpi-card {
                background-color: var(--color-card); border: 1px solid var(--color-borde);
                border-radius: 12px; padding: 20px; display: flex; align-items: center;
                gap: 15px; box-shadow: var(--sombra);
            }
            .kpi-icon {
                width: 50px; height: 50px; border-radius: 12px;
                background-color: color-mix(in srgb, var(--color-primario), transparent 90%);
                color: var(--color-primario); display: flex; align-items: center;
                justify-content: center; font-size: 1.5rem;
            }
            .kpi-data { display: flex; flex-direction: column; }
            .kpi-title { font-size: 0.85rem; color: var(--color-texto-muted); font-weight: 600; }
            .kpi-value { font-size: 1.8rem; font-weight: 700; color: var(--color-texto); }

            /* Stock Crítico List */
            .warning-card { border-top: 4px solid var(--color-peligro); }
            .critical-stock-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; }
            .critical-stock-list li {
                display: flex; justify-content: space-between; align-items: center; padding: 12px;
                background: var(--color-fondo); border-radius: 8px;
                border: 1px solid color-mix(in srgb, var(--color-peligro), transparent 80%);
            }
            .item-name { font-weight: 600; font-size: 0.95rem; }
            .badge-danger {
                background-color: var(--color-peligro); color: white; padding: 4px 8px;
                border-radius: 6px; font-size: 0.8rem; font-weight: 700;
            }

            /* --- ANIMACIÓN PARA STOCK BAJO --- */
            @keyframes pulse-low {
                0% { transform: scale(0.9); opacity: 1; }
                50% { transform: scale(1.2); opacity: 0.9; }
                100% { transform: scale(0.9); opacity: 1; }
            }
        </style>";

    // Cookie de tema
    // 1. Tema
    $tema_cookie = 'claro';
    // 2. Fuente
    $fuente_cookie = 'normal';

    if (isset($_SESSION['user_id'])) {
        $nombre_cookie_tema = 'user_theme_' . $_SESSION['user_id'];
        $tema_cookie = $_COOKIE[$nombre_cookie_tema] ?? 'claro';

        $nombre_cookie_fuente = 'user_font_' . $_SESSION['user_id'];
        $fuente_cookie = $_COOKIE[$nombre_cookie_fuente] ?? 'normal';
    }

    // APLICAMOS AMBAS CLASES AL BODY
    echo "</head><body class='tema-" . h($tema_cookie) . " fuente-" . h($fuente_cookie) . "'>";

    // Parte izquierda del navbar
    echo "<nav class='navbar'>
            <div class='nav-left'>
                <a href='index.php' style='margin-right: 20px;'>
                    <img src='media/pharmasphere_sinfondo.png' alt='Logo' class='navbar-logo-img'>
                </a>";
                
    if ($is_logged_in) {
        echo "<a href='index.php' class='nav-link " . ($current_page == 'index.php' ? 'active' : '') . "'>Panel</a>";
        echo "<a href='items_list.php' class='nav-link " . ($current_page == 'items_list.php' ? 'active' : '') . "'>Productos</a>";
        
        if ($is_admin) {
            echo "<a href='items_form.php' class='nav-link " . ($current_page == 'items_form.php' ? 'active' : '') . "'>Nuevo</a>";
            echo "<a href='user_form.php' class='nav-link " . ($current_page == 'user_form.php' ? 'active' : '') . "'>Usuarios</a>";
            echo "<a href='items_delete.php?action=auditoria' class='nav-link " . ($current_page == 'items_delete.php' ? 'active' : '') . "'>Auditoría</a>";
        }
    }
    echo "</div>";

    // Parte derecha del navbar
    echo "<div class='nav-right'>";
    if ($is_logged_in) {
        $nombre_usuario = h($_SESSION['user_nombre_usuario'] ?? 'Usuario');
        // Usuario y Preferencias
        echo "<a href='preferencias.php' class='nav-link' title='Configuración'><i class='fa-solid fa-circle-user fa-xl' style='margin-right:5px;'></i> <strong>$nombre_usuario</strong></a>";
        // Logout
        echo "<a href='logout.php' class='nav-link' style='color:var(--color-peligro);' title='Cerrar Sesión'><i class='fa-solid fa-right-from-bracket fa-xl'></i></a>";
    } else {
        echo "<a href='login.php' class='nav-link active'>Iniciar Sesión</a>";
    }
    echo "</div></nav>";

    echo "<div class='main-container'>";

    if ($title) {
        echo "<h1>" . h($title) . "</h1>";
    }
}

// Imprime el pie de página y cierra el HTML
// function footerHtml()
// {
//     echo "<hr><small>Proyecto IAW - Pharmasphere</small>"; 

    // --- INICIO DEL WIDGET DE TAWK.TO ---
    /* ?> 
    
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/691af7ebccfc7c195b6c49fc/1ja8lgs42';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>
    <?php
    // --- FIN DEL WIDGET ---

    echo "</body></html>";
}
?> */ 