<?php
require_once 'db_connect.php';
require_once 'auth.php';

function bookAppointment($patient_id, $doctor_id, $date, $time, $reason) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, time_slot, reason) VALUES (?, ?, ?, ?, ?)");
    $success = $stmt->execute([$patient_id, $doctor_id, $date, $time, $reason]);
    
    if ($success) {
        // Notify doctor
        $message = "New appointment booked by patient ID $patient_id for $date at $time.";
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $stmt->execute([$doctor_id, $message]);
    }
    return $success;
}

function getAppointments($user_id, $user_type) {
    global $pdo;
    if ($user_type == 'patient') {
        $stmt = $pdo->prepare("SELECT a.*, u.name as doctor_name FROM appointments a JOIN users u ON a.doctor_id = u.id WHERE a.patient_id = ?");
    } else {
        $stmt = $pdo->prepare("SELECT a.*, u.name as patient_name FROM appointments a JOIN users u ON a.patient_id = u.id WHERE a.doctor_id = ?");
    }
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function confirmAppointment($appointment_id) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE appointments SET status = 'confirmed' WHERE id = ?");
    $success = $stmt->execute([$appointment_id]);
    
    if ($success) {
        // Notify patient
        $stmt = $pdo->prepare("SELECT patient_id FROM appointments WHERE id = ?");
        $stmt->execute([$appointment_id]);
        $patient_id = $stmt->fetchColumn();
        
        $message = "Your appointment ID $appointment_id has been confirmed.";
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $stmt->execute([$patient_id, $message]);
    }
    return $success;
}
?>