<?php
// app/models/HealthModel.php

class HealthModel {
    private $db;
    private $dbConnected = false;

    public function __construct($pdo) {
        if ($pdo instanceof PDO) {
            $this->db = $pdo;
            $this->dbConnected = true;
        } else {
            error_log("HealthModel initialized without valid DB connection.");
            $this->db = null;
            $this->dbConnected = false;
        }
    }

    private function isConnected() {
        return $this->dbConnected;
    }

    /* =========================
       PATIENT QUERIES
    ========================== */

    public function getAllPatients() {
        if (!$this->isConnected()) return [];
        $stmt = $this->db->query("SELECT * FROM patients ORDER BY admission_date DESC");
        return $stmt->fetchAll();
    }

    public function getCriticalPatients() {
        if (!$this->isConnected()) return [];
        $stmt = $this->db->query("SELECT * FROM patients WHERE status = 'critical'");
        return $stmt->fetchAll();
    }

    public function getUrgentPatients() {
        if (!$this->isConnected()) return [];
        $stmt = $this->db->query("SELECT * FROM patients WHERE status = 'warning'");
        return $stmt->fetchAll();
    }

    public function getStablePatients() {
        if (!$this->isConnected()) return [];
        $stmt = $this->db->query("SELECT * FROM patients WHERE status = 'stable'");
        return $stmt->fetchAll();
    }

    public function getSyncLogs() {
        if (!$this->isConnected()) return [];
        $sql = "SELECT event_type, description, severity FROM system_logs ORDER BY created_at DESC LIMIT 10";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getPatientById($id) {
        if (!$this->isConnected()) return null;
        $stmt = $this->db->prepare("SELECT * FROM patients WHERE id_patient = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getVitalsByPatient($id) {
        if (!$this->isConnected()) return [];
        $stmt = $this->db->prepare("SELECT * FROM vitals WHERE patient_id = :id ORDER BY reading_time DESC");
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll();
    }

    public function getPatientSyncSummary() {
        if (!$this->isConnected()) return [];
        $stmt = $this->db->query("SELECT * FROM patient_sync_summary ORDER BY last_updated DESC");
        return $stmt->fetchAll();
    }

    /* =========================
       INSERTS
    ========================== */

    public function insertFhirRaw($payload, $count, $latency) {
        if (!$this->isConnected()) return false;

        $sql = "INSERT INTO fhir_raw_ingestion (raw_payload, records_extracted, api_latency_ms)
                VALUES (:payload, :count, :latency)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'payload' => json_encode($payload),
            'count' => $count,
            'latency' => $latency
        ]);
        return $this->db->lastInsertId();
    }

        public function logFhirCall($patient_id, $status, $responseTime) {
        if (!$this->isConnected()) return false;

        $stmt = $this->db->prepare("
            INSERT INTO fhir_logs (patient_id, status, response_time_ms)
            VALUES (:patient_id, :status, :response_time_ms)
        ");

        return $stmt->execute([
            'patient_id' => $patient_id,
            'status' => $status,
            'response_time_ms' => $responseTime
        ]);
    }
    
    public function insertFhirSummary($ingestionId, $avg, $min, $max, $count) {
        if (!$this->isConnected()) return false;

        $sql = "INSERT INTO fhir_analytics_summary
                (ingestion_id, avg_heart_rate, min_heart_rate, max_heart_rate, total_records)
                VALUES (:id, :avg, :min, :max, :count)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $ingestionId,
            'avg' => $avg,
            'min' => $min,
            'max' => $max,
            'count' => $count
        ]);
    }

    /* =========================
       ANALYTICS
    ========================== */

    public function getFhirTrend() {
        if (!$this->isConnected()) return [];
        $sql = "SELECT DATE(created_at) as run_date,
                       AVG(avg_heart_rate) as avg_hr
                FROM fhir_analytics_summary
                GROUP BY DATE(created_at)
                ORDER BY run_date ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getLastVitalsTimestamp($patientId) {
        if (!$this->isConnected()) return null;
        $sql = "SELECT MAX(reading_time) as last_time
                FROM vitals
                WHERE patient_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $patientId]);
        return $stmt->fetchColumn();
    }

    public function getLastSuccessfulSync($patientId) {
        if (!$this->isConnected()) return null;
        $sql = "SELECT created_at
                FROM system_logs
                WHERE patient_id = :id
                AND validation_status = 'VALID'
                ORDER BY created_at DESC
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $patientId]);
        return $stmt->fetchColumn();
    }

    public function getFailedMessagesLast24h() {
        if (!$this->isConnected()) return 0;
        $sql = "SELECT COUNT(*) FROM system_logs
                WHERE severity = 'error'
                AND created_at >= NOW() - INTERVAL 1 DAY";
        return $this->db->query($sql)->fetchColumn();
    }

    public function getMeanSyncTime() {
        if (!$this->isConnected()) return 0;
        $sql = "SELECT ROUND(AVG(response_time_ms),1) FROM system_logs";
        return $this->db->query($sql)->fetchColumn();
    }

    public function getBridgeData() {
        if (!$this->isConnected()) return [];
        $sql = "SELECT DISTINCT patient_id, facility_code, message_type,
                       schema_version, validation_status, retry_count, response_time_ms,
                       created_at
                FROM system_logs
                ORDER BY created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    /* =========================
    CARD BORDER CLASSES
    ========================== */

    function getSyncRateClass($rate) {
        if ($rate >= 98) return 'card-success';
        if ($rate >= 95) return 'card-warning';
        return 'card-error';
    }

    function getFailedMessagesClass($messages) {
        if ($messages >= 6) return 'card-error';
        if ($messages >= 3) return 'card-warning';
        return 'card-success';
    }

    function getMeanSyncTimeClass($syncTime) {
        if ($syncTime >= 500) return 'card-error';
        if ($syncTime >= 250) return 'card-warning';
        return 'card-success';
    }

    function getLatencyClass($latency) {
        if ($latency >= 900) return 'card-error';
        if ($latency >= 400) return 'card-warning';
        return 'card-success';
    }

    /* =========================
       POPULATE DENORMALIZED DATA TABLE
    ========================== */

    public function refreshSyncSummary() {
        if (!$this->isConnected()) return false;

        $sql = "
        REPLACE INTO patient_sync_summary
        (
            patient_id,
            latest_status,
            total_sync_attempts,
            failed_sync_count,
            avg_response_time,
            last_event_timestamp
        )
        SELECT
            p.id_patient,
            (
                SELECT sl.severity
                FROM system_logs sl
                WHERE sl.patient_id = p.id_patient
                ORDER BY sl.created_at DESC
                LIMIT 1
            ),
            COUNT(sl.id_system_log),
            SUM(CASE WHEN sl.severity = 'error' THEN 1 ELSE 0 END),
            ROUND(AVG(sl.response_time_ms),2),
            MAX(sl.created_at)
        FROM patients p
        LEFT JOIN system_logs sl ON p.id_patient = sl.patient_id
        GROUP BY p.id_patient
        ";

        return $this->db->exec($sql);
    }

    public function getReportingMetrics() {
        if (!$this->isConnected()) {
            return [
                'total_events' => 0,
                'successful_events' => 0,
                'failed_events' => 0
            ];
        }

        $sql = "
            SELECT
                COUNT(*) AS total_events,
                SUM(CASE WHEN validation_status = 'VALID' THEN 1 ELSE 0 END) AS successful_events,
                SUM(CASE WHEN validation_status = 'FAILED' THEN 1 ELSE 0 END) AS failed_events
            FROM system_logs
        ";

        return $this->db->query($sql)->fetch();
    }

    /* =========================
       DB STATUS CHECK
    ========================== */

    public function isDatabaseOnline() {
        return $this->dbConnected;
    }

    /* =========================
       API STATUS CHECK
    ========================== */

    public function checkApiStatus() {

    $url = "https://hapi.fhir.org/baseR4/Observation?_count=1";

    $context = stream_context_create([
        'http' => [
            'timeout' => 2
        ]
    ]);

    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        return false;
    }

    return true;
    }
}
