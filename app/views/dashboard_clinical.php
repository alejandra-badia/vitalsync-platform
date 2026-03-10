<?php if (!isset($viewContent)) {
    header('Location: ../../public/index.php?view=home');
    exit;
} ?>

<main>
    <section class="card-demo">
        <?php    include __DIR__ . '/layout/demo_nav.php'; ?>
        <p class="desc">Simulation of Hospital Information System (HIS) using a normalized MySQL database to store patient and corresponding health record information.</p>
    </section>
    
    <section class="card-section">
    <h2>Patient Overview</h2>
        <div class="kpi-grid">
            <div class="base-status-card kpi-card card-info">
                <p class="kpi-label">Total Patients</p>
                <p class="kpi-value"><?= $stats['total'] ?></p>
            </div>

            <div class="base-status-card kpi-card card-error">
                <p class="kpi-label">Critical Patients</p>
                <p class="kpi-value"><?= $stats['critical'] ?></p>
            </div>        

            <div class="base-status-card kpi-card card-warning">
                <p class="kpi-label">Urgent Patients</p>
                <p class="kpi-value"><?= $stats['urgent'] ?></p>
            </div>
        </div>
    </section>

    <section class="card-section">
        <header class="card-header">
            <h3 class="card-title">Patient Status Overview</h3>
        </header>
        <div class="table-container">
            <table class="main-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Patient Status</th>
                            <th>Admission Date</th>
                            <th>Vitals Last Taken</th>
                            <th>Last Successful Sync</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($allPatients as $p): ?>
                        <tr>
                            <td>
                                <a class="table-text" href="index.php?patient_id=<?= $p['id_patient'] ?>">
                                    <?= htmlspecialchars($p['full_name']) ?>
                                </a>
                            </td>
                            <td><p class="pill pill-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></p></td>
                            <td>
                                <p><?=$p['admission_date'] ?></p>
                            </td>
                            <td>
                                <?php if (empty($p['last_vitals'])){ ?>
                            <p>No Vitals Recorded</p>
                                <?php } else { ?>
                                <p><?=$p['last_vitals'] ?></p>
                                <?php } ?>
                            </td>
                            <td >
                                <p>
                                    <?php
                                    if (!empty($p['last_successful_sync'])) {
                                        echo date('Y-m-d H:i', strtotime($p['last_successful_sync']));
                                    } else {
                                        echo 'No Sync';
                                    }
                                    ?>
                                </p>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                </tbody>
            </table>
        </div>    
    </section>
</main>