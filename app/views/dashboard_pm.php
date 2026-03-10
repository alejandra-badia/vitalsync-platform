<?php if (!isset($viewContent)) {
    header('Location: ../../public/index.php?view=home');
    exit;
} ?>

<main>
    <section class="card-demo">
        <?php    include __DIR__ . '/layout/demo_nav.php'; ?>
        <p class="desc">HL7-style event tracking, validation status, retry handling, and latency monitoring.</p>
    </section>

    <section class="card-section">
        <h2>System Health Overview</h2>
        <div class="kpi-grid">
            <div class="base-status-card kpi-card <?= $syncRateClass ?? 'card-disabled' ?>">
                <p class="kpi-label">HL7 Sync Success Rate</p>
                <p class="kpi-value"><?= $stats['sync_rate'] ?>%</p>
            </div>

            <div class="base-status-card kpi-card <?= $failedMessagesClass ?? 'card-disabled' ?>">
                <p class="kpi-label">Failed Messages (24h)</p>
                <p class="kpi-value"><?= $stats['failed_24h'] ?></p>
            </div>

            <div class="base-status-card kpi-card <?= $meanSyncTimeClass ?? 'card-disabled' ?>">
                <p class="kpi-label">Mean Sync Time</p>
                <p class="kpi-value"><?= $stats['mean_sync_time'] ?> ms</p>
            </div>
        </div>
    </section>

    <div class="card-grid">
        <section class="card-section">
            <header class="card-header">
                <h3 class="card-title">Interoperability Bridge: Active HL7 Feeds</h3>
            </header>
            <div class="table-container">
                <table class="main-table">
                    <thead>
                        <tr>
                        <th>Patient</th>
                        <th>Facility Code</th>
                        <th>Message Type</th>
                        <th>Schema Version</th>
                        <th>Validation</th>
                        <th>Retry Count</th>
                        <th>Response Time (ms)</th>
                        <th>Last Handshake</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bridgeData as $row): ?>
                        <tr>
                        <td><code>PID-<?= str_pad($row['patient_id'],5,'0',STR_PAD_LEFT) ?></code></td>
                        <td><?= $row['facility_code'] ?></td>
                        <td><?= $row['message_type'] ?></td>
                        <td><?= $row['schema_version'] ?></td>
                        <td><?= $row['validation_status'] ?></td>
                        <td><?= $row['retry_count'] ?></td>
                        <td><?= $row['response_time_ms'] ?></td>
                        <td><?= date('Y-m-d H:i', strtotime($row['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <section class="card-section">
            <header class="card-header">
                <h3 class="card-title">System Events</h3>
            </header>
            <div class="log-container">
                <?php foreach($syncLogs as $log): ?>
                <div class="log">
                    <div class="log-view">
                        <p class="log-text"><?= $log['event_type'] ?></p>
                        <span class="pill pill-<?= $log['severity'] ?>">
                            <?= strtoupper($log['severity']) ?>
                        </span>                    </div>
                    <p class="log-text"><?= $log['description'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</main>