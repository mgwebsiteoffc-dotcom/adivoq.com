<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Data file path
$dataFile = __DIR__ . '/data/waitlist.json';

// Ensure data directory exists
if (!file_exists(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0755, true);
}

// Ensure data file exists
if (!file_exists($dataFile)) {
    file_put_contents($dataFile, json_encode(['entries' => []], JSON_PRETTY_PRINT));
}

// Read existing data
function readWaitlist($file) {
    $content = file_get_contents($file);
    return json_decode($content, true) ?: ['entries' => []];
}

// Write data
function writeWaitlist($file, $data) {
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Validate phone
function isValidPhone($phone) {
    return preg_match('/^[0-9]{10}$/', preg_replace('/[^0-9]/', '', $phone));
}

// Generate unique ID
function generateId() {
    return 'CP' . time() . substr(md5(uniqid(mt_rand(), true)), 0, 8);
}

// Handle POST request (Submit to waitlist)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON input
    $json = file_get_contents('php://input');
    $input = json_decode($json, true);
    
    // If not JSON, try form data
    if (!$input) {
        $input = $_POST;
    }
    
    // Validate required fields
    $name = isset($input['name']) ? sanitize($input['name']) : '';
    $email = isset($input['email']) ? sanitize(strtolower($input['email'])) : '';
    $phone = isset($input['phone']) ? sanitize($input['phone']) : '';
    
    // Check required fields
    if (empty($name) || empty($email) || empty($phone)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Name, email, and phone are required'
        ]);
        exit();
    }
    
    // Validate email format
    if (!isValidEmail($email)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email format'
        ]);
        exit();
    }
    
    // Validate phone
    if (!isValidPhone($phone)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid phone number (10 digits required)'
        ]);
        exit();
    }
    
    // Read existing data
    $waitlistData = readWaitlist($dataFile);
    
    // Check for duplicate email
    foreach ($waitlistData['entries'] as $entry) {
        if (strtolower($entry['email']) === $email) {
            http_response_code(409);
            echo json_encode([
                'success' => false,
                'message' => 'This email is already on the waitlist!'
            ]);
            exit();
        }
    }
    
    // Create new entry
    $newEntry = [
        'id' => generateId(),
        'name' => $name,
        'email' => $email,
        'phone' => '+91' . preg_replace('/[^0-9]/', '', $phone),
        'creatorType' => isset($input['creatorType']) ? sanitize($input['creatorType']) : 'Not specified',
        'followers' => isset($input['followers']) ? sanitize($input['followers']) : 'Not specified',
        'monthlyInvoices' => isset($input['monthlyInvoices']) ? sanitize($input['monthlyInvoices']) : 'Not specified',
        'source' => isset($input['source']) ? sanitize($input['source']) : 'website',
        'timestamp' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'userAgent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ];
    
    // Add to waitlist
    $waitlistData['entries'][] = $newEntry;
    
    // Save data
    if (writeWaitlist($dataFile, $waitlistData)) {
        $position = count($waitlistData['entries']);
        
        echo json_encode([
            'success' => true,
            'message' => 'Successfully joined the waitlist!',
            'data' => [
                'id' => $newEntry['id'],
                'position' => $position,
                'totalSignups' => $position
            ]
        ]);
        
        // Optional: Send notification email
        // sendNotificationEmail($newEntry);
        
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to save data. Please try again.'
        ]);
    }
    exit();
}

// Handle GET request (Get waitlist stats)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $waitlistData = readWaitlist($dataFile);
    $count = count($waitlistData['entries']);
    
    // Check for admin access (optional)
    $showAll = isset($_GET['admin']) && $_GET['admin'] === 'your-secret-key';
    
    if ($showAll) {
        echo json_encode([
            'success' => true,
            'count' => $count,
            'entries' => $waitlistData['entries']
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'count' => $count
        ]);
    }
    exit();
}

// Invalid method
http_response_code(405);
echo json_encode([
    'success' => false,
    'message' => 'Method not allowed'
]);

// Optional: Email notification function
function sendNotificationEmail($entry) {
    $to = 'your-email@example.com';
    $subject = 'New CreatorPay Waitlist Signup: ' . $entry['name'];
    
    $message = "
    <html>
    <head>
        <title>New Waitlist Signup</title>
    </head>
    <body>
        <h2>New Waitlist Signup</h2>
        <p><strong>Name:</strong> {$entry['name']}</p>
        <p><strong>Email:</strong> {$entry['email']}</p>
        <p><strong>Phone:</strong> {$entry['phone']}</p>
        <p><strong>Creator Type:</strong> {$entry['creatorType']}</p>
        <p><strong>Followers:</strong> {$entry['followers']}</p>
        <p><strong>Monthly Invoices:</strong> {$entry['monthlyInvoices']}</p>
        <p><strong>Timestamp:</strong> {$entry['timestamp']}</p>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: CreatorPay <noreply@creatorpay.in>\r\n";
    
    mail($to, $subject, $message, $headers);
}
?>