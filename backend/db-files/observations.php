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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_latest') {
    $patientId = $_GET['patient_id'] ?? null;

    if (!$patientId) {
        respond(false, ['message' => 'Patient ID is required']);
    }

    try {
        $stmt = $pdo->prepare('
            SELECT o.*, u.name as doctor_name 
            FROM observations o 
            LEFT JOIN users u ON o.doctor_id = u.id 
            WHERE o.patient_id = :patient_id 
            ORDER BY o.created_at DESC 
            LIMIT 1
        ');
        $stmt->execute(['patient_id' => $patientId]);
        $observation = $stmt->fetch();

        if (!$observation) {
            respond(false, ['message' => 'No observations found']);
        }

        $observation['behavioral_patterns'] = [
            'Fidgeting' => $observation['fidgeting_score'],
            'Leaving Seat Inappropriately' => $observation['leaving_seat_score'],
            'Difficulty Waiting for Turns' => $observation['waiting_turns_score'],
            'Eye Gaze Shifting' => $observation['eye_gaze_score']
        ];

        $observation['speech_patterns'] = [
            'Excessive Interruptions During Conversations' => $observation['interruptions_score'],
            'Rapid or Excessive Talking' => $observation['excessive_talking_score']
        ];

        respond(true, ['observation' => $observation]);
    } catch (Exception $e) {
        respond(false, ['message' => 'Error fetching observation: ' . $e->getMessage()]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_all') {
    $patientId = $_GET['patient_id'] ?? null;

    if (!$patientId) {
        respond(false, ['message' => 'Patient ID is required']);
    }

    try {
        $stmt = $pdo->prepare('
            SELECT o.*, u.name as doctor_name 
            FROM observations o 
            LEFT JOIN users u ON o.doctor_id = u.id 
            WHERE o.patient_id = :patient_id 
            ORDER BY o.created_at DESC
        ');
        $stmt->execute(['patient_id' => $patientId]);
        $observations = $stmt->fetchAll();

        foreach ($observations as &$observation) {
            $observation['behavioral_patterns'] = [
                'Fidgeting' => $observation['fidgeting_score'],
                'Leaving Seat Inappropriately' => $observation['leaving_seat_score'],
                'Difficulty Waiting for Turns' => $observation['waiting_turns_score'],
                'Eye Gaze Shifting' => $observation['eye_gaze_score']
            ];

            $observation['speech_patterns'] = [
                'Excessive Interruptions During Conversations' => $observation['interruptions_score'],
                'Rapid or Excessive Talking' => $observation['excessive_talking_score']
            ];
        }

        respond(true, ['observations' => $observations]);
    } catch (Exception $e) {
        respond(false, ['message' => 'Error fetching observations: ' . $e->getMessage()]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $patientId = $_POST['patient_id'] ?? null;
    $doctorId = $_POST['doctor_id'] ?? null;
    $behavioralPatterns = $_POST['behavioral_patterns'] ?? [];
    $speechPatterns = $_POST['speech_patterns'] ?? [];
    $remarks = $_POST['remarks'] ?? '';

    if (!$patientId || !$doctorId) {
        respond(false, ['message' => 'Patient ID and Doctor ID are required']);
    }

    try {
        $stmt = $pdo->prepare('
            INSERT INTO observations (
                patient_id, 
                doctor_id,
                fidgeting_score,
                leaving_seat_score,
                waiting_turns_score,
                eye_gaze_score,
                interruptions_score,
                excessive_talking_score,
                remarks, 
                created_at
            ) VALUES (
                :patient_id, 
                :doctor_id,
                :fidgeting_score,
                :leaving_seat_score,
                :waiting_turns_score,
                :eye_gaze_score,
                :interruptions_score,
                :excessive_talking_score,
                :remarks, 
                NOW()
            )
        ');

        $stmt->execute([
            'patient_id' => $patientId,
            'doctor_id' => $doctorId,
            'fidgeting_score' => $behavioralPatterns['Fidgeting'] ?? 0,
            'leaving_seat_score' => $behavioralPatterns['Leaving Seat Inappropriately'] ?? 0,
            'waiting_turns_score' => $behavioralPatterns['Difficulty Waiting for Turns'] ?? 0,
            'eye_gaze_score' => $behavioralPatterns['Eye Gaze Shifting'] ?? 0,
            'interruptions_score' => $speechPatterns['Excessive Interruptions During Conversations'] ?? 0,
            'excessive_talking_score' => $speechPatterns['Rapid or Excessive Talking'] ?? 0,
            'remarks' => $remarks
        ]);

        respond(true, ['message' => 'Observation added successfully']);
    } catch (Exception $e) {
        respond(false, ['message' => 'Error adding observation: ' . $e->getMessage()]);
    }
}

respond(false, ['message' => 'Invalid request']);
?>