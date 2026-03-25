<?php $activeNav = 'attendance'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management — School Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .sidebar { width: 260px; height: 100vh; position: fixed; background: white; border-right: 1px solid #edf2f7; }
        .main-content { margin-left: 260px; padding: 30px; }
        .nav-link.active { background: #fff1f2; color: #ff4757; }
        .nav-link:hover:not(.active) { background: #f1f5f9; }
        .table-responsive { border-radius: 12px; overflow: hidden; }
        .status-badge { width: 20px; height: 20px; border-radius: 50%; display: inline-block; cursor: pointer; border: 2px solid transparent; }
        .status-badge.active { border-color: #000; box-shadow: 0 0 5px rgba(0,0,0,0.2); }
        .bg-present { background-color: #28a745; }
        .bg-absent { background-color: #dc3545; }
        .bg-late { background-color: #ffc107; }
        .bg-excused { background-color: #17a2b8; }
        .day-label { font-size: 0.70rem; font-weight: 700; text-transform: uppercase; color: #94a3b8; }
    </style>
</head>

<body>
    <div class="d-flex">
        <?php require __DIR__ . '/partials/sidebar.php'; ?>

        <div class="main-content flex-grow-1">
            <header class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <h5 class="mb-0 fw-bold me-2 text-dark" id="userNameDisplay">Loading...</h5>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded px-2 py-1" style="font-size: 0.70rem;">Attendance</span>
                </div>
                <div class="d-flex align-items-center text-muted fw-medium font-sm">
                    <a href="dashboard.php" class="text-decoration-none text-muted me-3">Dashboard</a>
                    <div class="dropdown">
                        <a href="#" class="text-decoration-none text-dark d-flex align-items-center" data-bs-toggle="dropdown">
                            <img src="https://ui-avatars.com/api/?name=User" id="userAvatar" class="rounded-circle me-1" width="30" height="30" alt="">
                            <i class="fa-solid fa-chevron-down ms-1 text-muted" style="font-size: 0.70rem;"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 12px;">
                            <li><a class="dropdown-item py-2 text-danger" href="#" onclick="logout(); return false;"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Sign out</a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">Daily Attendance</h4>
                    <p class="text-muted small mb-0">Record and track student attendance by class.</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label small text-muted mb-1">Select Class</label>
                        <select class="form-select rounded-3 shadow-none border-light-subtle" id="classSelect">
                            <option value="">Choose a class...</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-1">Date</label>
                        <input type="date" class="form-control rounded-3 shadow-none border-light-subtle" id="dateInput" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-dark rounded-3 w-100 py-2" id="btnFilter">View Students</button>
                    </div>
                </div>
            </div>

            <div class="alert alert-danger d-none rounded-4" id="errorBanner" role="alert"></div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden d-none" id="attendanceCard">
                <div class="card-header bg-white border-bottom-0 py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0" id="classTitle">Class Attendance</h6>
                    <div class="d-flex gap-2">
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill font-sm pointer" onclick="markAll('present')">All Present</span>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill font-sm pointer" onclick="markAll('absent')">All Absent</span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Student</th>
                                <th>Status</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody id="attendanceBody">
                            <!-- JS injected -->
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-top-0 p-4 text-end">
                    <button type="button" class="btn btn-danger rounded-pill px-5" id="btnSaveBulk">Save Attendance</button>
                </div>
            </div>
            
            <div id="placeholder" class="text-center py-5 text-muted">
                <i class="fa-solid fa-calendar-days fa-3x mb-3 opacity-25"></i>
                <p>Select a class and date to start recording attendance.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js?v=7"></script>
    <script>
        (function() {
            const classSelect = document.getElementById('classSelect');
            const dateInput = document.getElementById('dateInput');
            const btnFilter = document.getElementById('btnFilter');
            const btnSave = document.getElementById('btnSaveBulk');
            const bodyEl = document.getElementById('attendanceBody');
            const cardEl = document.getElementById('attendanceCard');
            const placeholderEl = document.getElementById('placeholder');
            const errorEl = document.getElementById('errorBanner');
            const classTitle = document.getElementById('classTitle');

            let user = {};
            try { user = JSON.parse(localStorage.getItem('user') || '{}'); } catch (e) {}
            document.getElementById('userNameDisplay').textContent = (user.name || 'User') + "'s School";
            document.getElementById('userAvatar').src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name || 'U') + '&background=f1f5f9&color=475569';

            let currentStudents = [];

            async function loadClasses() {
                try {
                    const res = await apiFetch('/classes');
                    if (res && res.success) {
                        classSelect.innerHTML = '<option value="">Choose a class...</option>' + 
                            res.data.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
                    }
                } catch(e) {}
            }

            window.markAll = function(status) {
                document.querySelectorAll('.status-btn').forEach(btn => {
                    if (btn.dataset.status === status) btn.click();
                });
            }

            btnFilter.addEventListener('click', async () => {
                const classId = classSelect.value;
                const date = dateInput.value;
                if (!classId || !date) { alert('Select class and date.'); return; }

                errorEl.classList.add('d-none');
                placeholderEl.classList.add('d-none');
                cardEl.classList.remove('d-none');
                bodyEl.innerHTML = '<tr><td colspan="3" class="text-center py-4">Loading...</td></tr>';

                try {
                    // Get class students first
                    const stRes = await apiFetch(`/classes/${classId}/students`);
                    // Get existing attendance
                    const attRes = await apiFetch(`/attendance?class_id=${classId}&date=${date}`);

                    if (!stRes.success) { 
                        errorEl.textContent = stRes.message; 
                        errorEl.classList.remove('d-none');
                        cardEl.classList.add('d-none');
                        return;
                    }

                    const students = stRes.data || [];
                    const attMap = {};
                    (attRes.data || []).forEach(a => attMap[a.student_id] = a);

                    currentStudents = students;
                    classTitle.textContent = classSelect.options[classSelect.selectedIndex].text + ' — ' + date;

                    if (!students.length) {
                        bodyEl.innerHTML = '<tr><td colspan="3" class="text-center py-4">No students in this class.</td></tr>';
                        return;
                    }

                    bodyEl.innerHTML = students.map(s => {
                        const att = attMap[s.id] || { status: 'present', note: '' };
                        return `
                            <tr data-student-id="${s.id}">
                                <td class="ps-4">
                                    <div class="fw-bold">${s.name}</div>
                                    <div class="text-muted small">${s.student_id}</div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <input type="radio" class="btn-check status-btn" name="status_${s.id}" data-status="present" id="att_${s.id}_p" value="present" ${att.status === 'present' ? 'checked' : ''}>
                                        <label class="btn btn-outline-success px-3" for="att_${s.id}_p">P</label>
                                        
                                        <input type="radio" class="btn-check status-btn" name="status_${s.id}" data-status="absent" id="att_${s.id}_a" value="absent" ${att.status === 'absent' ? 'checked' : ''}>
                                        <label class="btn btn-outline-danger px-3" for="att_${s.id}_a">A</label>
                                        
                                        <input type="radio" class="btn-check status-btn" name="status_${s.id}" data-status="late" id="att_${s.id}_l" value="late" ${att.status === 'late' ? 'checked' : ''}>
                                        <label class="btn btn-outline-warning px-3" for="att_${s.id}_l">L</label>
                                        
                                        <input type="radio" class="btn-check status-btn" name="status_${s.id}" data-status="excused" id="att_${s.id}_e" value="excused" ${att.status === 'excused' ? 'checked' : ''}>
                                        <label class="btn btn-outline-info px-3" for="att_${s.id}_e">E</label>
                                    </div>
                                </td>
                                <td class="pe-4">
                                    <input type="text" class="form-control form-control-sm rounded-pill note-input shadow-none border-light-subtle" placeholder="Private note..." value="${att.note || ''}">
                                </td>
                            </tr>
                        `;
                    }).join('');
                } catch(e) {
                    errorEl.textContent = e.message;
                    errorEl.classList.remove('d-none');
                }
            });

            btnSave.addEventListener('click', async () => {
                const classId = classSelect.value;
                const date = dateInput.value;
                
                const records = [];
                document.querySelectorAll('#attendanceBody tr').forEach(tr => {
                    const studentId = tr.dataset.studentId;
                    const status = tr.querySelector('input[type="radio"]:checked')?.value || 'present';
                    const note = tr.querySelector('.note-input').value.trim();
                    records.push({ student_id: studentId, status, note });
                });

                if (!records.length) return;

                btnSave.disabled = true;
                btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

                try {
                    const res = await apiFetch('/attendance/bulk', {
                        method: 'POST',
                        body: JSON.stringify({ class_id: classId, date, records })
                    });
                    if (res && res.success) {
                        alert('Attendance saved successfully!');
                    } else {
                        alert('Error: ' + (res?.message || 'Unknown error'));
                    }
                } catch(e) {
                    alert('Error: ' + e.message);
                } finally {
                    btnSave.disabled = false;
                    btnSave.innerHTML = 'Save Attendance';
                }
            });

            loadClasses();
        })();
    </script>
</body>
</html>
