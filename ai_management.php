<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session only if it hasn't been started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['login_type'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$host = 'localhost'; // Replace with your database host
$dbname = 'tms_db'; // Replace with your database name
$username = 'root'; // Replace with your database username
$password = ''; // Replace with your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Google API Key
$apiKey = 'AIzaSyA2jmHkVK0VO-Xh6gK2jbUyoztTUCNXENU'; // Replace with your actual Google API key

$responseText = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputText = $_POST['input_text'];

    // Check if the input relates to the database
    $queryResult = '';
    if (stripos($inputText, 'project') !== false) {
        // Query the database for projects, their managers, and team members
        $stmt = $pdo->query("
            SELECT 
                p.id AS project_id,
                p.name AS project_name,
                p.status AS project_status,
                p.start_date,
                p.end_date,
                CONCAT(m.firstname, ' ', m.lastname) AS project_manager,
                GROUP_CONCAT(CONCAT(u.firstname, ' ', u.lastname) SEPARATOR ', ') AS team_members
            FROM project_list p
            LEFT JOIN users m ON p.manager_id = m.id
            LEFT JOIN users u ON FIND_IN_SET(u.id, p.user_ids)
            GROUP BY p.id
        ");
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Map status values to their textual representations
        $statusMap = [
            0 => 'Pending',
            1 => 'On-Hold',
            2 => 'Done'
        ];

        $queryResult = "Here are the projects, their managers, and their team members:\n";
        foreach ($projects as $project) {
            $statusText = $statusMap[$project['project_status']] ?? 'Unknown'; // Map status or default to 'Unknown'
            $queryResult .= "- Project ID: {$project['project_id']}, Name: {$project['project_name']}, Status: {$statusText}, Start Date: {$project['start_date']}, End Date: {$project['end_date']}\n";
            $queryResult .= "  Project Manager: {$project['project_manager']}\n";
            $queryResult .= "  Team Members: {$project['team_members']}\n";
        }
    } elseif (stripos($inputText, 'user') !== false) {
        // Query the database for users and their roles
        $stmt = $pdo->query("SELECT * FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $queryResult = "Here are the users in the database and their roles:\n";
        foreach ($users as $user) {
            $role = ($user['type'] == 1) ? 'Admin' : (($user['type'] == 2) ? 'Project Manager' : 'Employee');
            $queryResult .= "- User ID: {$user['id']}, Name: {$user['firstname']} {$user['lastname']}, Role: {$role}\n";
        }
    } elseif (stripos($inputText, 'task') !== false) {
        // Query the database for tasks
        $stmt = $pdo->query("
            SELECT 
                t.id AS task_id,
                t.task AS task_name,
                t.status AS task_status,
                t.project_id,
                p.name AS project_name
            FROM task_list t
            LEFT JOIN project_list p ON t.project_id = p.id
        ");
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Map task status values to their textual representations
        $taskStatusMap = [
            0 => 'Pending',
            1 => 'In Progress',
            2 => 'Completed'
        ];

        $queryResult = "Here are the tasks in the database:\n";
        foreach ($tasks as $task) {
            $taskStatusText = $taskStatusMap[$task['task_status']] ?? 'Unknown';
            $queryResult .= "- Task ID: {$task['task_id']}, Name: {$task['task_name']}, Status: {$taskStatusText}, Project: {$task['project_name']} (ID: {$task['project_id']})\n";
        }
    } elseif (stripos($inputText, 'productivity') !== false) {
        // Query the database for user productivity
        $stmt = $pdo->query("SELECT * FROM user_productivity");
        $productivity = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $queryResult = "Here is the user productivity data:\n";
        foreach ($productivity as $entry) {
            $queryResult .= "- User ID: {$entry['user_id']}, Project ID: {$entry['project_id']}, Task ID: {$entry['task_id']}, Time Rendered: {$entry['time_rendered']} hours\n";
        }
    }

    // Prepare the prompt for the AI
    $prompt = $queryResult ? $queryResult . "\n\n" . $inputText : $inputText;

    // Call the Google Generative Language API
    try {
        // Correct endpoint for the API
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$apiKey";

        // Prepare the data payload
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];

        // Set up cURL
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Execute the cURL request
        $result = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            $curlError = curl_error($ch);
            file_put_contents('error.log', "cURL Error: $curlError\n");
            throw new Exception("cURL Error: $curlError");
        }

        // Close the cURL session
        curl_close($ch);

        // Decode the JSON response
        $response = json_decode($result, true);

        // Extract the AI response
        if (isset($response['candidates'][0]['content']['parts'][0]['text'])) {
            $responseText = $response['candidates'][0]['content']['parts'][0]['text'];

            // Store the user input and AI response in the database
            $stmt = $pdo->prepare("INSERT INTO chat_history (user_input, ai_response) VALUES (:user_input, :ai_response)");
            $stmt->execute([
                ':user_input' => $inputText,
                ':ai_response' => $responseText
            ]);
        } else {
            $responseText = 'No response from the AI. Check debug.log for details.';
        }
    } catch (Exception $e) {
        $responseText = 'Error: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chat</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f4f8;
            color: #333;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #007bff;
            font-weight: 700;
        }

        .chat-container {
            flex: 1;
            overflow-y: auto;
            margin-bottom: 20px;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .chat-bubble {
            padding: 10px 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            font-size: 16px;
            line-height: 1.5;
            max-width: 70%;
        }

        .user-bubble {
            background-color: #007bff;
            color: #fff;
            margin-left: auto;
            text-align: right;
        }

        .ai-bubble {
            background-color: #e9f5ff;
            color: #333;
            margin-right: auto;
        }

        form {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        textarea {
            flex: 1;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            resize: none;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        button {
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 500;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>AI Chat</h1>
        <div class="chat-container">
            <?php if ($responseText): ?>
                <div class="chat-bubble user-bubble">
                    <?php echo nl2br(htmlspecialchars($inputText)); ?>
                </div>
                <div class="chat-bubble ai-bubble">
                    <?php echo nl2br(htmlspecialchars($responseText)); ?>
                </div>
            <?php endif; ?>
        </div>
        <form method="POST">
            <textarea name="input_text" rows="2" placeholder="Type your message..."></textarea>
            <button type="submit">Send</button>
        </form>
    </div>
</body>
</html>