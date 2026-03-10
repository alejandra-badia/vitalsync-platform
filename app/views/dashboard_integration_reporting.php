<?php if (!isset($viewContent)) {
    header('Location: ../../public/index.php?view=home');
    exit;
} ?>

<main>
    <section class="card-demo">
        <?php    include __DIR__ . '/layout/demo_nav.php'; ?>
        <p class="desc">Executive reporting layer built on denormalized synchronization metrics for consolidated performance visibility.</p>
    </section>

    <section class="card-section">
        <h2>Integration Performance Overview</h2>

        <div class="kpi-grid">
            <div class="base-status-card kpi-card card-info">
                <p class="kpi-label">Total Active Patients</p>
                <p class="kpi-value"><?= count($syncSummary) ?></p>
                <span class="pill pill-info">System Reach</span>
            </div>
        
            <div class="base-status-card kpi-card <?= $syncRateClass ?? 'card-disabled' ?>">
                <p class="kpi-label">Overall Sync Success Rate</p>
                <p class="kpi-value"><?= $overallSuccessRate ?>%</p>
                <span class="pill pill-info">All HL7 Messages</span>
            </div>

            <div class="base-status-card kpi-card <?= $meanSyncTimeClass ?? 'card-disabled' ?>">
                <p class="kpi-label">Mean Response Time</p>
                <p class="kpi-value"><?= $meanSyncTime ?> ms</p>
                <span class="pill pill-info">Interface Latency</span>
            </div>

            <div class="base-status-card kpi-card <?= $totalFailedMessagesClass ?? 'card-disabled' ?>">
                <p class="kpi-label">Total Failed Messages</p>
                <p class="kpi-value"><?= $totalFailures ?></p>
                <span class="pill pill-info">Validation Failures</span>
            </div>

            <div class="base-status-card kpi-card <?= $failedMessagesClass ?? 'card-disabled' ?>">
                <p class="kpi-label">Failed Messages (24h)</p>
                <p class="kpi-value"><?= $failed24h ?></p>
                <span class="pill pill-info">Operational Risk</span>
            </div>
        </div>
    </section>
    <section class="card-section">
        <header class="card-header">
            <h3 class="card-title">Reporting Layer (Analytical View)</h3>
        </header>
        <div class="table-container">
            <table class="main-table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Latest Status</th>
                        <th>Total Attempts</th>
                        <th>Failed Syncs</th>
                        <th>Avg Response (ms)</th>
                        <th>Last Event</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($syncSummary as $row): ?>
                <tr>
                    <td><code>PID-<?= str_pad($row['patient_id'],5,'0',STR_PAD_LEFT) ?></code></td>
                    <td><?= strtoupper($row['latest_status']) ?></td>
                    <td><?= $row['total_sync_attempts'] ?></td>
                    <td><?= $row['failed_sync_count'] ?></td>
                    <td><?= $row['avg_response_time'] ?> ms</td>
                    <td><?= $row['last_event_timestamp'] ? date('Y-m-d H:i', strtotime($row['last_event_timestamp'])) : 'N/A' ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>    
    </section>
</main>