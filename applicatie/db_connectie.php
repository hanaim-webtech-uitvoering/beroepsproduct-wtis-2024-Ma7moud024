<?php
// Security headers - plaats dit bovenaan elke pagina
function setSecurityHeaders() {
    // Prevent content type sniffing
    header('X-Content-Type-Options: nosniff');
    
    // Prevent page from being displayed in frames (clickjacking protection)
    header('X-Frame-Options: DENY');
    
    // Enable XSS filtering
    header('X-XSS-Protection: 1; mode=block');
    
    // Force HTTPS (alleen als site HTTPS gebruikt)
    // header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    
    // Content Security Policy - basis configuratie
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'");
    
    // Referrer policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // Remove server information
    header_remove('X-Powered-By');
    header('Server: WebServer');
}

// Configureer veilige sessie instellingen
function configureSecureSession() {
    // Configureer sessie voordat session_start() wordt aangeroepen
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Strict');
    
    // Alleen als HTTPS wordt gebruikt:
    // ini_set('session.cookie_secure', 1);
    
    // Regenereer session ID regelmatig
    if (session_status() === PHP_SESSION_ACTIVE) {
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        } elseif (time() - $_SESSION['last_regeneration'] > 300) { // 5 minuten
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }
}

// Laad omgevingsvariabelen (maak een .env bestand aan)
function loadEnvironmentVariables() {
    $envFile = __DIR__ . '/.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }
    }
}

// Initialiseer omgevingsvariabelen
loadEnvironmentVariables();

// Database configuratie via omgevingsvariabelen
$db_host = $_ENV['DB_HOST'] ?? 'database_server';
$db_name = $_ENV['DB_NAME'] ?? 'pizzeria';
$db_user = $_ENV['DB_USER'] ?? 'sa';
$db_password = $_ENV['DB_PASSWORD'] ?? 'abc123!@#';

// Verbinding maken met betere beveiliging
try {
    // Verbeterde PDO opties voor beveiliging
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false, // Gebruik echte prepared statements
        PDO::ATTR_STRINGIFY_FETCHES => false
    ];
    
    // Maak verbinding (nog steeds TrustServerCertificate=1 voor development)
    // Voor productie: verwijder TrustServerCertificate=1 en gebruik valide SSL certificaten
    $verbinding = new PDO(
        'sqlsrv:Server=' . $db_host . ';Database=' . $db_name . ';ConnectionPooling=0;TrustServerCertificate=1',
        $db_user,
        $db_password,
        $options
    );
    
} catch (PDOException $e) {
    // Log de echte fout voor debugging (niet tonen aan gebruiker)
    error_log('Database connection error: ' . $e->getMessage());
    
    // Toon generieke foutmelding aan gebruiker
    die('Er is een technische fout opgetreden. Probeer het later opnieuw.');
}

// Wis wachtwoord uit geheugen
unset($db_password);

// Functie om verbinding te krijgen
function maakVerbinding() {
    global $verbinding;
    return $verbinding;
}

// Hulp functie voor veilige database queries
function executeQuery($pdo, $query, $params = []) {
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        // Log fout voor debugging
        error_log('Query error: ' . $e->getMessage() . ' | Query: ' . $query);
        
        // Toon generieke foutmelding
        throw new Exception('Er is een fout opgetreden bij het verwerken van uw verzoek.');
    }
}

// Input sanitization functie
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// CSRF token generatie en verificatie
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>