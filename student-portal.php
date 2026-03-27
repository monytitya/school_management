<?php $activeNav = 'portal'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal — School Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f0f2f5; }
        .sidebar { width: 280px; height: 100vh; position: fixed; background: white; border-right: 1px solid #e2e8f0; }
        .main-content { margin-left: 280px; padding: 40px; }
        .portal-card { border: none; border-radius: 24px; transition: transform 0.3s ease, box-shadow 0.3s ease; background: white; overflow: hidden; }
        .portal-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.05); }
        .stat-icon { width: 48px; height: 48px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
        .btn-premium { background: linear-gradient(135deg, #ff4757, #ff6b81); color: white; border: none; border-radius: 12px; padding: 12px 24px; font-weight: 600; }
        .profile-header { background: linear-gradient(135deg, #1e293b, #334155); color: white; border-radius: 24px; padding: 40px; margin-bottom: 30px; position: relative; }
        .attendance-badge { padding: 4px 12px; border-radius: 100px; font-weight: 600; font-size: 0.75rem; }
    </style>
</head>
<body>
    <div class="d-flex">
        <?php require __DIR__ . '/partials/sidebar.php'; ?>
        
        <div class="main-content flex-grow-1">
            <div class="profile-header shadow-lg">
                <div class="d-flex align-items-center gap-4">
                    <img src="https://ui-avatars.com/api/?name=Student&background=random&size=128" id="profileImg" class="rounded-circle border border-4 border-white shadow" width="100" height="100">
                    <div>
                        <h2 class="fw-bold mb-1" id="studentName">Loading Profile...</h2>
                        <div class="d-flex align-items-center gap-3 opacity-75">
                            <span><i class="fa-solid fa-graduation-cap me-2"></i><span id="studentGrade">Class 10-A</span></span>
                            <span><i class="fa-solid fa-id-badge me-2"></i><span id="studentCode">ST-2024-001</span></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="portal-card p-4 shadow-sm h-100">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="stat-icon bg-primary-subtle text-primary"><i class="fa-solid fa-calendar-check"></i></div>
                            <span class="attendance-badge bg-success-subtle text-success">98% Attendance</span>
                        </div>
                        <h6 class="text-muted fw-bold small">ACADEMIC JOURNEY</h6>
                        <h3 class="fw-bold">Excellent</h3>
                        <div class="progress mt-3" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: 85%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="portal-card p-4 shadow-sm h-100">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="stat-icon bg-warning-subtle text-warning"><i class="fa-solid fa-star"></i></div>
                            <span class="attendance-badge bg-warning-subtle text-warning">Top 5 in Class</span>
                        </div>
                        <h6 class="text-muted fw-bold small">OVERALL GRADE</h6>
                        <h3 class="fw-bold" id="avgGrade">A+</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="portal-card p-4 shadow-sm h-100">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="stat-icon bg-danger-subtle text-danger"><i class="fa-solid fa-bell"></i></div>
                        </div>
                        <h6 class="text-muted fw-bold small">NOTIFICATIONS</h6>
                        <h3 class="fw-bold">2 New</h3>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-8">
                    <div class="portal-card p-4 shadow-sm mb-4">
                        <h5 class="fw-bold mb-4">My Subjects & Schedule</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr class="text-muted small">
                                        <th>SUBJECT</th>
                                        <th>TEACHER</th>
                                        <th>ROOM</th>
                                        <th class="text-end">ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody id="subjectTable">
                                    <tr><td colspan="4" class="text-center py-4">Loading your curriculum...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="portal-card p-4 shadow-sm">
                        <h5 class="fw-bold mb-4">Upcoming Exams</h5>
                        <div class="d-flex flex-column gap-3">
                            <div class="p-3 bg-light rounded-4 d-flex align-items-center gap-3">
                                <div class="bg-white p-2 rounded-3 text-center shadow-sm" style="min-width: 50px;">
                                    <div class="fw-bold text-danger">MAR</div>
                                    <div class="h5 fw-bold mb-0">28</div>
                                </div>
                                <div>
                                    <div class="fw-bold">Mathematics</div>
                                    <div class="small text-muted">Final Mid-term Exam</div>
                                </div>
                            </div>
                            <div class="p-3 bg-light rounded-4 d-flex align-items-center gap-3">
                                <div class="bg-white p-2 rounded-3 text-center shadow-sm" style="min-width: 50px;">
                                    <div class="fw-bold text-primary">APR</div>
                                    <div class="h5 fw-bold mb-0">05</div>
                                </div>
                                <div>
                                    <div class="fw-bold">Physics</div>
                                    <div class="small text-muted">Lab Practical</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script>
        (async function() {
            const user = JSON.parse(localStorage.getItem('user') || '{}');
            document.getElementById('studentName').textContent = user.name || 'Student';
            document.getElementById('profileImg').src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name || 'S') + '&background=random&size=128';

            // Fetch Student Details if they are a student
            if (user.role === 'student') {
               // More logic to fetch specific classes/grades can be added here
               // For now, load some sample data to make it look active
               document.getElementById('subjectTable').innerHTML = `
                <tr>
                    <td><div class="fw-bold">English Language</div></td>
                    <td>Mr. Bernard</td>
                    <td>Room 402</td>
                    <td class="text-end"><button class="btn btn-sm btn-light rounded-pill px-3">View Syllabus</button></td>
                </tr>
                <tr>
                    <td><div class="fw-bold">Mathematics</div></td>
                    <td>Ms. Clara</td>
                    <td>Room 101</td>
                    <td class="text-end"><button class="btn btn-sm btn-light rounded-pill px-3">View Syllabus</button></td>
                </tr>
               `;
            }
        })();
    </script>
</body>
</html>
