<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - School Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            background: white;
            border-right: 1px solid #edf2f7;
        }

        .main-content {
            margin-left: 260px;
            padding: 30px;
        }

        .nav-link {
            color: #64748b;
            font-weight: 500;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 5px;
        }

        .nav-link.active {
            background: #fff1f2;
            color: #ff4757;
        }

        .nav-link:hover:not(.active) {
            background: #f1f5f9;
        }

        .stat-card {
            transition: transform 0.2s;
            border: none;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .font-sm {
            font-size: 0.85rem;
        }

        .gradient-red {
            background: linear-gradient(135deg, #ff4d6b, #ff758c);
        }

        .gradient-blue {
            background: linear-gradient(135deg, #4d94ff, #82b1ff);
        }

        .gradient-purple {
            background: linear-gradient(135deg, #9a4dff, #b388ff);
        }

        canvas {
            max-width: 100% !important;
        }
    </style>
</head>

<body>

    <div class="d-flex">

        <?php $activeNav = 'dashboard';
include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="main-content flex-grow-1">

            <header class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <h5 class="mb-0 fw-bold me-2 text-dark" id="userNameDisplay">Loading...</h5>
                    <span class="badge bg-danger text-white border border-danger rounded px-2 py-1"
                        style="font-size: 0.70rem; font-weight: 500;" id="roleBadge">Super Admin</span>
                </div>
                <div class="d-flex align-items-center text-muted fw-medium font-sm">
                    <a href="#" class="text-decoration-none text-muted me-4">Notice</a>
                    <a href="#" class="text-decoration-none text-muted me-4 border-start ps-4">Help center</a>
                    <div class="dropdown">
                        <a href="#" class="text-decoration-none text-dark d-flex align-items-center"
                            data-bs-toggle="dropdown">
                            <img src="https://ui-avatars.com/api/?name=User" id="userAvatar" class="rounded-circle me-1"
                                width="30" height="30">
                            <i class="fa-solid fa-chevron-down ms-1 text-muted" style="font-size: 0.70rem;"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 12px;">
                            <li><a class="dropdown-item py-2" href="#"><i
                                        class="fa-regular fa-user me-2"></i>Profile</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item py-2 text-danger" href="#" onclick="logout()"><i
                                        class="fa-solid fa-arrow-right-from-bracket me-2"></i>Sign out</a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <div
                class="card border-0 mb-4 p-4 rounded-4 d-flex flex-row align-items-center justify-content-between bg-white shadow-sm">
                <div>
                    <h4 class="fw-bold mb-1" style="color: #ff4757;">Well begun is half done</h4>
                    <p class="text-muted mb-0 font-sm">Complete the following steps to set up</p>
                </div>
                <div class="d-flex gap-3">
                    <div class="gradient-red rounded-3 p-3 text-white d-flex align-items-center justify-content-between"
                        style="width: 200px;">
                        <div class="fw-bold font-sm">1. Add Teachers</div>
                        <a href="teachers.php"
                            class="btn btn-light btn-sm rounded text-danger border-0">GO</a>
                    </div>
                    <div class="gradient-blue rounded-3 p-3 text-white d-flex align-items-center justify-content-between"
                        style="width: 200px;">
                        <div class="fw-bold font-sm">2. Create Classes</div>
                        <a href="classes.php"
                            class="btn btn-light btn-sm rounded text-primary border-0">GO</a>
                    </div>
                    <div class="gradient-blue rounded-3 p-3 text-white d-flex align-items-center justify-content-between"
                        style="width: 200px;">
                        <div class="fw-bold font-sm">3. Student Register</div>
                        <a href="students.php" class="btn btn-light btn-sm rounded text-primary border-0">GO</a>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="stat-card p-3 rounded-4 d-flex justify-content-between bg-white shadow-sm">
                        <div>
                            <div class="text-muted font-sm mb-1">students</div>
                            <h3 class="fw-bold mb-0" id="statStudents">—</h3>
                        </div>
                        <div class="icon-circle text-white d-flex justify-content-center align-items-center bg-danger rounded-circle"
                            style="width: 40px; height: 40px;">
                            <i class="fa-solid fa-user-graduate"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card p-3 rounded-4 d-flex justify-content-between bg-white shadow-sm">
                        <div>
                            <div class="text-muted font-sm mb-1">Teachers</div>
                            <h3 class="fw-bold mb-0" id="statTeachers">—</h3>
                        </div>
                        <div class="icon-circle text-white d-flex justify-content-center align-items-center bg-primary rounded-circle"
                            style="width: 40px; height: 40px;">
                            <i class="fa-solid fa-chalkboard-user"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card p-3 rounded-4 d-flex justify-content-between bg-white shadow-sm">
                        <div>
                            <div class="text-muted font-sm mb-1">Active Classes</div>
                            <h3 class="fw-bold mb-0" id="statClasses">—</h3>
                        </div>
                        <div class="icon-circle text-white d-flex justify-content-center align-items-center bg-warning rounded-circle"
                            style="width: 40px; height: 40px;">
                            <i class="fa-solid fa-school"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card p-3 rounded-4 d-flex justify-content-between bg-white shadow-sm">
                        <div>
                            <div class="text-muted font-sm mb-1">Attendance</div>
                            <h3 class="fw-bold mb-0 text-success">95%</h3>
                        </div>
                        <div class="icon-circle text-white d-flex justify-content-center align-items-center bg-success rounded-circle"
                            style="width: 40px; height: 40px;">
                            <i class="fa-solid fa-check"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Analytics Overview</h6>
                <a href="#" class="text-danger text-decoration-none font-sm fw-bold">View Report</a>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                        <h6 class="fw-bold mb-4">Enrollment Growth (2026)</h6>
                        <div style="height: 300px;">
                            <canvas id="enrollmentChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white text-center">
                        <h6 class="fw-bold mb-4 text-start">Student Distribution</h6>
                        <div style="height: 220px;">
                            <canvas id="distributionChart"></canvas>
                        </div>
                        <div class="mt-4 pt-3 border-top text-start">
                            <div class="d-flex justify-content-between font-sm mb-2">
                                <span><i class="fa-solid fa-circle text-primary me-2"></i>Primary</span>
                                <span class="fw-bold">45%</span>
                            </div>
                            <div class="d-flex justify-content-between font-sm">
                                <span><i class="fa-solid fa-circle text-danger me-2"></i>Secondary</span>
                                <span class="fw-bold">55%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Operations Assistant</h6>
                <a href="#" class="text-danger text-decoration-none font-sm fw-bold">More</a>
            </div>

            <div class="row g-3">
                <div class="col-md-3">
                    <a href="#" class="card border-0 shadow-sm rounded-3 p-3 bg-white text-decoration-none d-flex flex-row align-items-center h-100">
                        <div class="p-2 bg-light rounded-3 me-3 text-danger"><i class="fa-solid fa-bullhorn"></i></div>
                        <div>
                            <div class="fw-bold font-sm text-dark">Broadcast</div>
                            <div class="text-muted" style="font-size: 0.65rem;">Send announcements</div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="student-portal.php" class="card border-0 shadow-sm rounded-3 p-3 bg-white text-decoration-none d-flex flex-row align-items-center h-100">
                        <div class="p-2 bg-light rounded-3 me-3 text-primary"><i class="fa-solid fa-users-viewfinder"></i></div>
                        <div>
                            <div class="fw-bold font-sm text-dark">Portal View</div>
                            <div class="text-muted" style="font-size: 0.65rem;">Switch to Student View</div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="classrooms.php" class="card border-0 shadow-sm rounded-3 p-3 bg-white text-decoration-none d-flex flex-row align-items-center h-100">
                        <div class="p-2 bg-light rounded-3 me-3 text-success"><i class="fa-solid fa-door-open"></i></div>
                        <div>
                            <div class="fw-bold font-sm text-dark">Facility Manage</div>
                            <div class="text-muted" style="font-size: 0.65rem;">Rooms & Schools</div>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/js/app.js?v=6"></script>

    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            try {
                const userStr = localStorage.getItem('user');
                const userNameDisplay = document.getElementById('userNameDisplay');
                const userAvatar = document.getElementById('userAvatar');

                if (userStr) {
                    const user = JSON.parse(userStr);
                    userNameDisplay.textContent = (user.name || "Admin") + "'s School";
                    userAvatar.src =
                        `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name || 'Admin')}&background=f1f5f9&color=475569`;
                    
                    document.getElementById('roleBadge').textContent = user.role.toUpperCase();
                }
            } catch (e) {
                console.error(e);
            }

            let regCount = 2992;
            try {
                const st = await apiFetch('/stats/dashboard');
                if (st && st.success && st.data) {
                    const d = st.data;
                    regCount = typeof d.student_registry_count === 'number' ? d.student_registry_count :
                        regCount;
                    document.getElementById('statStudents').textContent = d.student_registry_count
                        .toLocaleString();
                    document.getElementById('statTeachers').textContent = d.teachers_count.toLocaleString();
                    document.getElementById('statClasses').textContent = d.classes_count.toLocaleString();
                }
            } catch (e) {
                console.error(e);
            }


            const ctxLine = document.getElementById('enrollmentChart').getContext('2d');
            new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'New Students',
                        data: [400, 1200, 900, 1800, 1500, Math.max(0, regCount)],
                        borderColor: '#ff4757',
                        backgroundColor: 'rgba(255, 71, 87, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#ff4757'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            grid: {
                                borderDash: [5, 5]
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
            const ctxDoughnut = document.getElementById('distributionChart').getContext('2d');
            new Chart(ctxDoughnut, {
                type: 'doughnut',
                data: {
                    labels: ['Primary', 'Secondary'],
                    datasets: [{
                        data: [45, 55],
                        backgroundColor: ['#4d94ff', '#ff4d6b'],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    cutout: '75%',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>