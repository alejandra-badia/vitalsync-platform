<?php
// app/controllers/DashboardController.php

require_once '../app/models/HealthModel.php';

class DashboardController {
    private $model;

    public function __construct($pdo) {
        $this->model = new HealthModel($pdo);
    }

    public function index() {
        $viewMode = $_GET['view'] ?? 'home';
        $patient_id = $_GET['patient_id'] ?? null;

        if ($patient_id) {
            $patient = $this->model->getPatientById($patient_id);
            $vitals = $this->model->getVitalsByPatient($patient_id);

            $dbOnline = $this->model->isDatabaseOnline();
            $apiOnline = $this->model->checkApiStatus();
            include __DIR__ . '/../views/layout/header.php';
            include __DIR__ . '/../views/patient_details.php';
            include __DIR__ . '/../views/layout/footer.php';
        } else {
            // Fetch the raw data
            $allPatients = $this->model->getAllPatients();
            $criticalPatients = $this->model->getCriticalPatients();
            $urgentPatients = $this->model->getUrgentPatients();
            $stablePatients = $this->model->getStablePatients();
            $syncLogs = $this->model->getSyncLogs();

            foreach ($allPatients as &$patient) {
                $patient['last_vitals'] = $this->model->getLastVitalsTimestamp($patient['id_patient']);
                $patient['last_successful_sync']= $this->model->getLastSuccessfulSync($patient['id_patient']);
            }
            

            // Get Sync Summary

            $this->model->refreshSyncSummary();
            $syncSummary = $this->model->getPatientSyncSummary();

        
            // DEFINE THE STATS
            $totalSyncAttempts = 0;
            $totalSuccess = 0;
            $totalFailure = 0;
            
            $failed24h = 0;
            $meanSyncTime =0;

            $failed24h = $this->model->getFailedMessagesLast24h();
            $meanSyncTime = $this->model->getMeanSyncTime();
            $bridgeData = $this->model->getBridgeData();
            $metrics = $this->model->getReportingMetrics();

            $overallSuccessRate = $metrics['total_events'] > 0
                ? round(($metrics['successful_events'] / $metrics['total_events']) * 100, 2)
                : 0;

            $totalFailures = $metrics['failed_events'];

            foreach ($syncSummary as $row) {
                $totalSyncAttempts += $row['total_sync_attempts'];
                $totalFailure += $row['failed_sync_count'];
            }

            $stats = [
                'total' => count($allPatients),
                'critical' => count($criticalPatients),
                'urgent' => count($urgentPatients),
                'stable' => count($stablePatients),
                'sync_rate' => $totalSyncAttempts > 0 
                    ? round((($totalSyncAttempts-$totalFailure) / $totalSyncAttempts) * 100, 2) 
                    : 0,
                'failed_24h' => $failed24h,
                'mean_sync_time' => $meanSyncTime
            ];

            $syncRateClass=$this->model->getSyncRateClass($stats['sync_rate']);
            $failedMessagesClass=$this->model->getFailedMessagesClass($stats['failed_24h']);
            $meanSyncTimeClass=$this->model->getMeanSyncTimeClass($stats['mean_sync_time']);
            $totalFailedMessagesClass=$this->model->getFailedMessagesClass($totalFailures);

            $dbOnline = $this->model->isDatabaseOnline();
            $apiOnline = $this->model->checkApiStatus();
            include __DIR__ . '/../views/layout/header.php';
            
            // View selection based on GET
            if ($viewMode === 'clinical') {
                $viewContent = true;
                include __DIR__ . '/../views/dashboard_clinical.php';
            } elseif ($viewMode === 'pm') {
                $viewContent = true;
                include __DIR__ . '/../views/dashboard_pm.php';
            } elseif ($viewMode === 'integration_reporting') {
                $viewContent = true;
                include __DIR__ . '/../views/dashboard_integration_reporting.php';
            } elseif ($viewMode === 'research') {
                $viewContent = true;
                $researchData = $this->runFhirResearchIngestion();
                $latencyClass=$this->model->getLatencyClass($researchData['latency']);
                include __DIR__ . '/../views/dashboard_research.php';
            }
            else {
                $viewContent = true;
                include __DIR__ . '/../views/home.php';
            }

            
            include __DIR__ . '/../views/layout/footer.php';
        }
    }

    private function runFhirResearchIngestion() {

    $url = "https://hapi.fhir.org/baseR4/Observation?code=8867-4&_count=20";

    $start = microtime(true);
    $response = @file_get_contents($url);
    $latency = round((microtime(true) - $start) * 1000);

    if (!$response) {
        return ['success' => false];
    }

    $data = json_decode($response, true);

    $heartRates = [];

    foreach ($data['entry'] ?? [] as $entry) {
        $value = $entry['resource']['valueQuantity']['value'] ?? null;
        if ($value) {
            $heartRates[] = $value;
        }
    }

    if (empty($heartRates)) {
        return ['success' => false];
    }

    //TRANSFORM
    $count = count($heartRates);
    $avg = round(array_sum($heartRates) / $count, 2);
    $min = min($heartRates);
    $max = max($heartRates);
    $stdDev = round($this->calculateStandardDeviation($heartRates), 2);

    // LOAD (raw storage)
    $ingestionId = $this->model->insertFhirRaw($data, $count, $latency);

    // LOAD/STORE SUMMARY)
    $this->model->insertFhirSummary($ingestionId, $avg, $min, $max, $count);

    return [
        'success' => true,
        'count' => $count,
        'avg' => $avg,
        'min' => $min,
        'max' => $max,
        'latency' => $latency,
        'values' => $heartRates,
        'std_dev' => $stdDev,
    ];
    }

    private function calculateStandardDeviation($array) {
        $count = count($array);
        if ($count === 0) return 0;

        $mean = array_sum($array) / $count;
        $variance = 0;

        foreach ($array as $value) {
            $variance += pow($value - $mean, 2);
        }

        $variance /= $count;

        return sqrt($variance);
    }
}