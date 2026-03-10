<main>
    <div>
        <a href="index.php?view=clinical" class="btn btn-secondary">← Back to Clinical View</a>
    </div>

    <section class="card card-patient">
        <p class="fs-sm text-muted">Cross-system observation verification using public FHIR R4 endpoint (LOINC 8867-4).</p>

        <header class="card-header card-patient-header">
            <h2 class="card-title"><?= htmlspecialchars($patient['full_name']) ?> FHIR R4 External Validation</h2>
            <span class="pill pill-<?= $patient['status'] ?>"><?= ucfirst($patient['status']) ?></span>
        </header>
        
        <div class="patient-info">
            <p class="text-muted">Room: <strong><?= htmlspecialchars($patient['room_number']) ?></strong></p>
            <p class="fs-sm text-muted">Internal ID: <code>#<?= str_pad($patient['id_patient'], 5, '0', STR_PAD_LEFT) ?></code></p>
        </div>
    </section>

    <section class="card-section">
        <header class="card-header">
            <h3 class="card-title">Vitals History (HL7 Feed)</h3>
        </header>

        <div class="table-container">
            <?php if (empty($vitals)): ?>
                <div class="no-record">
                    <p class="text-muted">No vital readings found for this patient.</p>
                    <span class="pill pill-info">Awaiting next HL7 sync cycle</span>
                </div>
            <?php else: ?>
                <table class="main-table">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Blood Pressure</th>
                            <th>Heart Rate</th>
                            <th>Oxygen (SpO2)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vitals as $v): ?>
                        <tr>
                            <td><?= date('M d, Y - H:i', strtotime($v['reading_time'])) ?></td>
                            <td>
                                <strong><?= $v['bp_systolic'] ?>/<?= $v['bp_diastolic'] ?></strong> 
                                <span class="fs-xs text-muted">mmHg</span>
                            </td>
                            <td><?= $v['heart_rate'] ?> <span class="fs-xs text-muted">bpm</span></td>
                            <td>
                                <span>
                                    <?= $v['oxygen_level'] ?>%
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </section>
  
    <p class="data-source">Data Source: EHR (Simulated HIS).</p>
</main>