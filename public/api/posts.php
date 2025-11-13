<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
session_start();

header('Content-Type: application/json');

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch posts visible to 'all' for the public index page
    $stmt = $pdo->prepare("SELECT p.id, p.title, p.body, p.created_at, u.name as author_name FROM posts p LEFT JOIN users u ON p.user_id = u.id WHERE p.visible_to = 'all' ORDER BY p.created_at DESC");
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($posts);
    exit;
}

// The following POST, PUT, DELETE methods are for API-driven post management,
// but the admin/posts.php already handles these directly.
// This section is kept for potential future API-driven admin functionality.

// Check if user is authenticated and is an admin for write operations
if (!isLoggedIn() || current_user()['role'] !== 'admin') {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'Unauthorized access.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid JSON input.']);
    exit;
}

$user_id = current_user()['id']; // Admin user ID

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $data['title'] ?? '';
    $body = $data['body'] ?? '';
    $visible_to = $data['visible_to'] ?? 'all';

    if (empty($title) || empty($body)) {
        http_response_code(400);
        echo json_encode(['error' => 'Title and body cannot be empty.']);
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO posts (user_id, title, body, visible_to) VALUES (?, ?, ?, ?)');
    if ($stmt->execute([$user_id, $title, $body, $visible_to])) {
        echo json_encode(['success' => 'Post added successfully!', 'id' => $pdo->lastInsertId()]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to add post.']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $post_id = $data['id'] ?? 0;
    $title = $data['title'] ?? '';
    $body = $data['body'] ?? '';
    $visible_to = $data['visible_to'] ?? 'all';

    if (empty($post_id) || empty($title) || empty($body)) {
        http_response_code(400);
        echo json_encode(['error' => 'Post ID, title, and body cannot be empty.']);
        exit;
    }

    $stmt = $pdo->prepare('UPDATE posts SET title = ?, body = ?, visible_to = ? WHERE id = ?');
    if ($stmt->execute([$title, $body, $visible_to, $post_id])) {
        echo json_encode(['success' => 'Post updated successfully!']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update post.']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $post_id = $data['id'] ?? 0;

    if (empty($post_id)) {
        http_response_code(400);
        echo json_encode(['error' => 'Post ID cannot be empty.']);
        exit;
    }

    $stmt = $pdo->prepare('DELETE FROM posts WHERE id = ?');
    if ($stmt->execute([$post_id])) {
        echo json_encode(['success' => 'Post deleted successfully!']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete post.']);
    }
    exit;
}

http_response_code(405); // Method Not Allowed
echo json_encode(['error' => 'Method not allowed.']);
