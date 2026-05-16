<?php
require_once('../config/tce_config.php');
// Start session to prevent undefined session errors
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$thispage_title = "School Assessment Portal";
$pagelevel = 1;
require_once('../../shared/code/tce_authorization.php');
require_once('tce_xhtml_header.php');
?>

<style>
    .portal-container {
        width: 100%;
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
        box-sizing: border-box;
        font-family: 'Inter', sans-serif;
    }

    .portal-header {
        text-align: center;
        margin-bottom: 40px;
        animation: fadeInDown 0.8s ease-out;
    }

    .portal-header h1 {
        font-size: clamp(2rem, 8vw, 3.5rem);
        font-weight: 800;
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 10px;
        line-height: 1.2;
    }

    .portal-header p {
        color: #64748b;
        font-size: clamp(1rem, 3vw, 1.2rem);
    }

    .grade-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 50px;
    }

    .grade-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 24px;
        padding: 30px;
        text-align: center;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px -10px rgba(0,0,0,0.1);
    }

    .grade-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px -15px rgba(99, 102, 241, 0.3);
        background: rgba(255, 255, 255, 0.9);
        border-color: #6366f1;
    }

    .grade-card h2 {
        font-size: clamp(3rem, 10vw, 4.5rem);
        margin: 0;
        color: #1e293b;
        font-weight: 900;
    }

    .grade-card span {
        display: block;
        font-size: 0.9rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 10px;
    }

    /* Mobile Adjustments */
    @media (max-width: 768px) {
        .portal-container {
            margin: 20px auto;
        }
        .grade-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .grade-card {
            padding: 20px;
            border-radius: 20px;
        }
    }

    @media (max-width: 480px) {
        .grade-grid {
            grid-template-columns: 1fr;
        }
        .course-modal {
            padding: 20px;
            width: 95%;
        }
    }

    .course-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.9);
        backdrop-filter: blur(8px);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        padding: 20px;
    }

    .course-modal {
        background: #f8fafc;
        width: 100%;
        max-width: 700px;
        border-radius: 32px;
        padding: 40px;
        animation: scaleIn 0.3s ease-out;
        position: relative;
        max-height: 90vh;
        overflow-y: auto;
    }

    .close-modal {
        position: absolute;
        top: 20px;
        right: 20px;
        background: #e2e8f0;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        font-weight: bold;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .course-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }

    .course-item {
        background: white;
        padding: 20px;
        border-radius: 16px;
        text-decoration: none;
        color: #1e293b;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.2s;
        border: 1px solid #e2e8f0;
        cursor: pointer;
    }

    .course-item:hover {
        background: #6366f1;
        color: white;
        transform: scale(1.05);
    }

    .course-icon {
        font-size: 1.5rem;
    }

    .student-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 20px 30px;
        border-radius: 24px;
        margin-bottom: 30px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
    }

    .student-profile {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .student-avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        object-fit: cover;
    }

    .welcome-msg h2 {
        margin: 0;
        font-size: 1.5rem;
        color: #1e293b;
    }

    .welcome-msg p {
        margin: 0;
        color: #64748b;
        font-size: 0.9rem;
    }

    .portal-tag {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
    }

    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes scaleIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }
</style>

<div class="portal-container">
    <!-- Student Header -->
    <div class="student-header">
        <div class="student-profile">
            <img src="../images/student_avatar.png" alt="Student Avatar" class="student-avatar">
            <div class="welcome-msg">
                <h2>Welcome Back!</h2>
                <p>Ready to excel in your assessments today?</p>
            </div>
        </div>
        <div class="portal-tag">Student Portal</div>
    </div>

    <div class="portal-header">
        <p>Welcome to</p>
        <h1>Secondary School Assessment</h1>
        <p>Select your Grade to start the exam</p>
    </div>

    <div class="grade-grid">
        <div class="grade-card" onclick="showCourses(9)">
            <span>Grade</span>
            <h2>09</h2>
        </div>
        <div class="grade-card" onclick="showCourses(10)">
            <span>Grade</span>
            <h2>10</h2>
        </div>
        <div class="grade-card" onclick="showCourses(11)">
            <span>Grade</span>
            <h2>11</h2>
        </div>
        <div class="grade-card" onclick="showCourses(12)">
            <span>Grade</span>
            <h2>12</h2>
        </div>
    </div>
</div>

<div class="course-overlay" id="courseOverlay">
    <div class="course-modal">
        <button class="close-modal" onclick="closeModal()">✕</button>
        <h2 id="modalTitle" style="font-size: 2rem; color: #1e293b;">Available Courses</h2>
        <p style="color: #64748b;">Click on a course to proceed to the exam login.</p>
        
        <div class="course-list" id="courseList">
            <!-- Courses will be injected here -->
        </div>
    </div>
</div>

<script>
    const courses = {
        9: [
            { name: 'Mathematics', icon: '📐' },
            { name: 'General Science', icon: '🔬' },
            { name: 'English Language', icon: '📚' },
            { name: 'Geography', icon: '🌍' }
        ],
        10: [
            { name: 'Algebra', icon: '📊' },
            { name: 'Biology', icon: '🧬' },
            { name: 'History', icon: '📜' },
            { name: 'Literature', icon: '📖' }
        ],
        11: [
            { name: 'Calculus', icon: '📉' },
            { name: 'Physics', icon: '⚡' },
            { name: 'Chemistry', icon: '🧪' },
            { name: 'Economics', icon: '💰' }
        ],
        12: [
            { name: 'Advanced Math', icon: '♾️' },
            { name: 'Quantum Physics', icon: '🌌' },
            { name: 'Organic Chem', icon: '🧪' },
            { name: 'Social Studies', icon: '🤝' }
        ]
    };

    function showCourses(grade) {
        const overlay = document.getElementById('courseOverlay');
        const list = document.getElementById('courseList');
        const title = document.getElementById('modalTitle');
        
        title.innerText = `Grade ${grade} Courses`;
        list.innerHTML = '';
        
        courses[grade].forEach(course => {
            const item = document.createElement('a');
            item.href = 'index.php?group=Grade ' + grade + '&test=' + course.name;
            item.className = 'course-item';
            item.innerHTML = `
                <span class="course-icon">${course.icon}</span>
                <span>${course.name}</span>
            `;
            list.appendChild(item);
        });
        
        overlay.style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('courseOverlay').style.display = 'none';
    }
</script>

<?php
echo '</body>'.K_NEWLINE;
echo '</html>'.K_NEWLINE;
?>
