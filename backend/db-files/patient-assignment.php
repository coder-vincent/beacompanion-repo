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

// Verify user is logged in and is a doctor
$user = $_SESSION['user'] ?? null;
if (!$user || $user['role'] !== 'doctor') {
    respond(false, ['message' => 'Unauthorized access']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $patientId = $_POST['patient_id'] ?? null;

    if (!$patientId) {
        respond(false, ['message' => 'Patient ID is required']);
    }

    try {
        switch ($action) {
            case 'assign':
                $doctorId = $_POST['doctor_id'] ?? null;
                if (!$doctorId) {
                    respond(false, ['message' => 'Doctor ID is required']);
                }

                // Verify the patient exists and is not assigned to any doctor
                $stmt = $pdo->prepare('
                    SELECT id, doctor_id 
                    FROM users 
                    WHERE id = :patient_id 
                    AND role = "patient"
                ');
                $stmt->execute(['patient_id' => $patientId]);
                $patient = $stmt->fetch();

                if (!$patient) {
                    respond(false, ['message' => 'Patient not found']);
                }

                if ($patient['doctor_id'] !== null) {
                    respond(false, ['message' => 'Patient is already assigned to a doctor']);
                }

                // Assign the patient to the doctor
                $stmt = $pdo->prepare('
                    UPDATE users 
                    SET doctor_id = :doctor_id 
                    WHERE id = :patient_id 
                    AND role = "patient"
                ');
                $stmt->execute([
                    'doctor_id' => $doctorId,
                    'patient_id' => $patientId
                ]);

                if ($stmt->rowCount() === 0) {
                    respond(false, ['message' => 'Failed to assign patient']);
                }

                respond(true, ['message' => 'Patient assigned successfully']);
                break;

            case 'remove':
                // Verify the patient is assigned to this doctor
                $stmt = $pdo->prepare('
                    SELECT id, doctor_id 
                    FROM users 
                    WHERE id = :patient_id 
                    AND role = "patient" 
                    AND doctor_id = :doctor_id
                ');
                $stmt->execute([
                    'patient_id' => $patientId,
                    'doctor_id' => $user['id']
                ]);
                $patient = $stmt->fetch();

                if (!$patient) {
                    respond(false, ['message' => 'Patient not found or not assigned to you']);
                }

                // Remove the patient assignment
                $stmt = $pdo->prepare('
                    UPDATE users 
                    SET doctor_id = NULL 
                    WHERE id = :patient_id 
                    AND role = "patient"
                ');
                $stmt->execute(['patient_id' => $patientId]);

                if ($stmt->rowCount() === 0) {
                    respond(false, ['message' => 'Failed to remove patient assignment']);
                }

                respond(true, ['message' => 'Patient removed successfully']);
                break;

            default:
                respond(false, ['message' => 'Invalid action']);
        }
    } catch (Exception $e) {
        respond(false, ['message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    respond(false, ['message' => 'Invalid request method']);
}