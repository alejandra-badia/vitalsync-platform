<?php if (!isset($viewContent)) {
    header('Location: ../../public/index.php?view=home');
    exit;
} ?>

<main>
    <section class="card-demo">
        <?php    include __DIR__ . '/layout/demo_nav.php'; ?>
        <p class="desc">Hybrid ETL simulation ingesting external FHIR R4 heart rate observations (LOINC 8867-4) with raw payload retention and analytical metric generation.</p>
    </section>

    <section class="card-section">
        <h2>FHIR External Dataset Simulation</h2>

        <?php if (!$researchData['success']): ?>

            <div style="padding:30px;text-align:center;">
                <span class="pill pill-warning">FHIR Endpoint Unavailable</span>
                <p>
                    API latency: <?= $researchData['latency'] ?> ms
                </p>
            </div>

        <?php else: ?>

            <div class="kpi-grid">
                <div class="base-status-card kpi-card card-success">
                    <p class="kpi-label">Records Extracted</p>
                    <p class="kpi-value"><?= $researchData['count'] ?></p>
                    <span class="pill pill-info">FHIR Observations</span>
                </div>
                <div class="base-status-card kpi-card <?= $latencyClass ?? 'card-disabled' ?>">
                    <p class="kpi-label">API Latency</p>
                    <p class="kpi-value"><?= $researchData['latency'] ?> ms</p>
                    <span class="pill pill-info">Extraction Stage</span>
                </div>
            </div>
        <?php endif; ?>

    </section>

    <section class="card-section">
        <h2>Research Metrics</h2>
        <?php if (!$researchData['success']): ?>

            <div>
                <span class="pill pill-warning">FHIR Endpoint Unavailable</span>
                <p>
                    API latency: <?= $researchData['latency'] ?> ms
                </p>
            </div>

        <?php else: ?>

            <div class="kpi-grid">
                <div class="card kpi-card">
                    <p class="kpi-label">Average Heart Rate</p>
                    <p class="kpi-value"><?= $researchData['avg'] ?> bpm</p>
                </div>

                <div class="card kpi-card">
                    <p class="kpi-label">Minimum Heart Rate</p>
                    <p class="kpi-value"><?= $researchData['min'] ?> bpm</p>
                </div>

                <div class="card kpi-card">
                    <p class="kpi-label">Maximum Heart Rate</p>
                    <p class="kpi-value"><?= $researchData['max'] ?> bpm</p>
                </div>

                <div class="card kpi-card">
                    <p class="kpi-label">Standard Deviation</p>
                    <p class="kpi-value"><?= $researchData['std_dev'] ?> bpm</p>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <?php if ($researchData['success']): ?>

    <div class="card-section">
        <h3>Heart Rate Distribution (FHIR External Dataset)</h3>
        <canvas id="distributionChart"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    const hrValues = <?= json_encode($researchData['values']) ?>;
    const avgValue = <?= $researchData['avg'] ?>;

    const labels = hrValues.map((_, i) => "Obs " + (i+1));

    const ctx = document.getElementById('distributionChart');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Heart Rate (bpm)',
                    data: hrValues,
                    tension: 0.3,
                    borderWidth: 2
                },
                {
                    label: 'Average',
                    data: Array(hrValues.length).fill(avgValue),
                    borderDash: [6, 6],
                    borderWidth: 2
                }
            ]
        },
        options: {
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    title: {
                        display: true,
                        text: 'Beats Per Minute (bpm)'
                    }
                }
            }
        }
    });
    </script>

    <?php endif; ?>
</main>



