<?php
ob_start();
date_default_timezone_set('Asia/Manila');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once(__DIR__ . '/../auth/dbconnect.php');

header('Content-Type: application/json');

function respond($success, $data = [])
{
    echo json_encode(array_merge(['success' => $success], $data));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_about_sections') {
    try {
        $stmt = $pdo->query('SELECT * FROM about_sections ORDER BY display_order');
        $sections = $stmt->fetchAll();
        respond(true, ['sections' => $sections]);
    } catch (Exception $e) {
        respond(false, ['message' => 'Error fetching about sections: ' . $e->getMessage()]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_about_section') {
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $type = $_POST['type'] ?? 'text';
    $displayOrder = (int) ($_POST['display_order'] ?? 0);

    if (empty($title) || empty($content)) {
        respond(false, ['message' => 'Title and content are required']);
    }

    try {
        $stmt = $pdo->prepare('INSERT INTO about_sections (title, content, type, display_order) VALUES (?, ?, ?, ?)');
        $stmt->execute([$title, $content, $type, $displayOrder]);
        respond(true, ['message' => 'Section added successfully']);
    } catch (Exception $e) {
        respond(false, ['message' => 'Error adding section: ' . $e->getMessage()]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_about_section') {
    $id = $_POST['id'] ?? null;
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $type = $_POST['type'] ?? 'text';
    $displayOrder = (int) ($_POST['display_order'] ?? 0);

    if (!$id || empty($title) || empty($content)) {
        respond(false, ['message' => 'ID, title, and content are required']);
    }

    try {
        $stmt = $pdo->prepare('UPDATE about_sections SET title = ?, content = ?, type = ?, display_order = ? WHERE id = ?');
        $stmt->execute([$title, $content, $type, $displayOrder, $id]);
        respond(true, ['message' => 'Section updated successfully']);
    } catch (Exception $e) {
        respond(false, ['message' => 'Error updating section: ' . $e->getMessage()]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_about_section') {
    $id = $_POST['id'] ?? null;

    if (!$id) {
        respond(false, ['message' => 'Section ID is required']);
    }

    try {
        $stmt = $pdo->prepare('DELETE FROM about_sections WHERE id = ?');
        $stmt->execute([$id]);
        respond(true, ['message' => 'Section deleted successfully']);
    } catch (Exception $e) {
        respond(false, ['message' => 'Error deleting section: ' . $e->getMessage()]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_faqs') {
    try {
        $stmt = $pdo->query('SELECT * FROM faqs ORDER BY category, display_order');
        $faqs = $stmt->fetchAll();
        respond(true, ['faqs' => $faqs]);
    } catch (Exception $e) {
        respond(false, ['message' => 'Error fetching FAQs: ' . $e->getMessage()]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_faq') {
    $category = trim($_POST['category'] ?? '');
    $question = trim($_POST['question'] ?? '');
    $answer = $_POST['answer'] ?? '';
    $displayOrder = (int) ($_POST['display_order'] ?? 0);

    if (empty($category) || empty($question) || empty($answer)) {
        respond(false, ['message' => 'Category, question, and answer are required']);
    }

    try {
        $stmt = $pdo->prepare('INSERT INTO faqs (category, question, answer, display_order) VALUES (?, ?, ?, ?)');
        $stmt->execute([$category, $question, $answer, $displayOrder]);
        respond(true, ['message' => 'FAQ added successfully']);
    } catch (Exception $e) {
        respond(false, ['message' => 'Error adding FAQ: ' . $e->getMessage()]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_faq') {
    $id = $_POST['id'] ?? null;
    $category = trim($_POST['category'] ?? '');
    $question = trim($_POST['question'] ?? '');
    $answer = $_POST['answer'] ?? '';
    $displayOrder = (int) ($_POST['display_order'] ?? 0);

    if (!$id || empty($category) || empty($question) || empty($answer)) {
        respond(false, ['message' => 'ID, category, question, and answer are required']);
    }

    try {
        $stmt = $pdo->prepare('UPDATE faqs SET category = ?, question = ?, answer = ?, display_order = ? WHERE id = ?');
        $stmt->execute([$category, $question, $answer, $displayOrder, $id]);
        respond(true, ['message' => 'FAQ updated successfully']);
    } catch (Exception $e) {
        respond(false, ['message' => 'Error updating FAQ: ' . $e->getMessage()]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_faq') {
    $id = $_POST['id'] ?? null;

    if (!$id) {
        respond(false, ['message' => 'FAQ ID is required']);
    }

    try {
        $stmt = $pdo->prepare('DELETE FROM faqs WHERE id = ?');
        $stmt->execute([$id]);
        respond(true, ['message' => 'FAQ deleted successfully']);
    } catch (Exception $e) {
        respond(false, ['message' => 'Error deleting FAQ: ' . $e->getMessage()]);
    }
}

respond(false, ['message' => 'Invalid request']);
?>