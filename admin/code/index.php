<?php
require_once('../config/tce_config.php');
$pagelevel = K_AUTH_INDEX;
require_once('../../shared/code/tce_authorization.php');

// Fetch Statistics
$count_students = F_count_rows(K_TABLE_USERS, "WHERE user_level < 10");
$count_exams = F_count_rows(K_TABLE_TESTS);
$count_questions = F_count_rows(K_TABLE_QUESTIONS);
$count_results = F_count_rows(K_TABLE_TESTUSER_STAT);

require_once('tce_page_header.php');
?>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 24px;
        margin-bottom: 40px;
    }
    .stat-card {
        background: white;
        padding: 24px;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
        transition: transform 0.2s;
    }
    .stat-card:hover { transform: translateY(-5px); }
    .stat-label { color: #64748b; font-size: 0.875rem; font-weight: 600; text-transform: uppercase; }
    .stat-value { font-size: 2rem; font-weight: 800; color: #1e293b; margin: 8px 0; }
    .stat-icon { font-size: 1.5rem; float: right; opacity: 0.5; }
    
    .charts-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 24px;
    }
    .chart-container {
        background: white;
        padding: 24px;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
    }
    .license-banner {
        background: #f1f5f9;
        padding: 15px;
        border-radius: 8px;
        font-size: 0.8rem;
        margin-top: 40px;
        color: #475569;
    }
</style>

<div class="body">
    <h1>System Dashboard</h1>

    <div class="dashboard-grid">
        <div class="stat-card">
            <span class="stat-icon">🎓</span>
            <div class="stat-label">Total Students</div>
            <div class="stat-value"><?php echo $count_students; ?></div>
        </div>
        <div class="stat-card">
            <span class="stat-icon">📝</span>
            <div class="stat-label">Total Exams</div>
            <div class="stat-value"><?php echo $count_exams; ?></div>
        </div>
        <div class="stat-card">
            <span class="stat-icon">❓</span>
            <div class="stat-label">Question Bank</div>
            <div class="stat-value"><?php echo $count_questions; ?></div>
        </div>
        <div class="stat-card">
            <span class="stat-icon">🏆</span>
            <div class="stat-label">Results Issued</div>
            <div class="stat-value"><?php echo $count_results; ?></div>
        </div>
    </div>

    <div style="margin-bottom: 40px;">
        <h3 style="color: #475569; margin-bottom: 20px;">Quick Actions</h3>
        <div style="display: flex; gap: 16px; flex-wrap: wrap;">
            <a href="tce_ai_importer.php" style="text-decoration: none; background: #4f46e5; color: white; padding: 16px 24px; border-radius: 12px; font-weight: 600; display: flex; align-items: center; gap: 12px; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); transition: all 0.3s ease;" class="quick-action-btn">
                <span>✨ AI Question Importer</span>
                <span style="background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 6px; font-size: 0.7rem;">Word to XML</span>
            </a>
        </div>
    </div>

    <div class="charts-row">
        <div class="chart-container">
            <h3 style="margin-top:0">System Distribution</h3>
            <canvas id="distributionChart"></canvas>
        </div>
        <div class="chart-container">
            <h3 style="margin-top:0">Engagement Trends</h3>
            <canvas id="activityChart"></canvas>
        </div>
    </div>

    <div class="license-banner">
        TCEXAM IS SUBJECT TO THE <a href="http://www.fsf.org/licensing/licenses/agpl-3.0.html">GNU-AGPL v.3 LICENSE</a>. 
        Logo and trademarks are property of Tecnick.com LTD.
    </div>
</div>

<script>
    // Distribution Chart (Pie)
    new Chart(document.getElementById('distributionChart'), {
        type: 'doughnut',
        data: {
            labels: ['Students', 'Exams', 'Questions'],
            datasets: [{
                data: [<?php echo $count_students; ?>, <?php echo $count_exams; ?>, <?php echo $count_questions; ?>],
                backgroundColor: ['#6366f1', '#a855f7', '#ec4899'],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '70%',
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // Activity Chart (Bar)
    new Chart(document.getElementById('activityChart'), {
        type: 'bar',
        data: {
            labels: ['Tests Taken', 'Results Processed'],
            datasets: [{
                label: 'Volume',
                data: [<?php echo $count_results; ?>, <?php echo $count_results; ?>],
                backgroundColor: '#6366f1',
                borderRadius: 8
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { display: false } }
        }
    });
</script>

<?php
require_once('tce_page_footer.php');
?>
