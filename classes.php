<?php $activeNav = 'classes'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Management — School Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        :root {
            --primary: #ff4757;
            --primary-hover: #ff6b81;
            --bg-body: #f8fafc;
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
            --glass-bg: rgba(255, 255, 255, 0.8);
            --glass-border: rgba(255, 255, 255, 0.5);
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--bg-body);
            color: #1e293b;
        }

        .main-content { 
            margin-left: 260px; 
            padding: 40px;
            transition: all 0.3s ease;
        }

        .premium-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: var(--card-shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border-radius: 12px;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 4px rgba(255, 71, 87, 0.1);
            border-color: var(--primary);
        }

        .btn-premium {
            background: var(--primary);
            color: white;
            border-radius: 14px;
            padding: 0.8rem 2rem;
            font-weight: 700;
            border: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-premium:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(255, 71, 87, 0.3);
            color: white;
        }

        .class-card {
            border-radius: 20px;
            border: 1px solid #f1f5f9;
            background: #fff;
            padding: 1.5rem;
            transition: all 0.3s;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .class-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow);
        }

        .class-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 4px;
            background: linear-gradient(90deg, var(--primary), #ffa502);
        }

        .badge-soft {
            padding: 6px 12px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .badge-soft-primary { background: #fff1f2; color: #ff4757; }
        .badge-soft-info { background: #f0f9ff; color: #0ea5e9; }
        .badge-soft-success { background: #f0fdf4; color: #16a34a; }

        .form-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
        }

        #loadingOverlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255,255,255,0.7);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(5px);
        }

        .action-btns {
            position: absolute;
            top: 15px;
            right: 15px;
            display: flex;
            gap: 5px;
            opacity: 0;
            transition: 0.3s;
        }

        .class-card:hover .action-btns { opacity: 1; }

        .btn-circle {
            width: 32px; height: 32px;
            padding: 0; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div id="loadingOverlay">
        <div class="spinner-border text-primary" role="status"></div>
    </div>

    <div class="d-flex">
        <?php require __DIR__ . '/partials/sidebar.php'; ?>

        <div class="main-content flex-grow-1">
            <header class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h2 class="fw-bold mb-1" id="userNameDisplay">Loading...</h2>
                    <p class="text-muted mb-0">Academic Class Management System</p>
                </div>
                <div class="dropdown">
                    <a href="#" class="text-decoration-none d-flex align-items-center gap-2 bg-white p-2 rounded-pill shadow-sm" data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name=User" id="userAvatar" class="rounded-circle" width="35" height="35">
                        <i class="fa-solid fa-chevron-down text-muted" style="font-size: 0.8rem;"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4">
                        <li><a class="dropdown-item py-2 text-danger rounded-4" href="#" onclick="logout(); return false;"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Sign out</a></li>
                    </ul>
                </div>
            </header>

            <div class="form-container" id="formView">
                <div class="premium-card">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h4 class="fw-bold mb-0" id="formTitle"><i class="fa-solid fa-plus-circle me-2 text-primary"></i>Define New Class</h4>
                        <button class="btn btn-light rounded-pill px-3" onclick="toggleView()"><i class="fa-solid fa-list me-2"></i>View All Classes</button>
                    </div>

                    <form id="classForm">
                        <input type="hidden" name="id" id="field_id">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label">CLASS IDENTIFIER *</label>
                                <input type="text" class="form-control" name="class_name" id="field_class_name" required placeholder="e.g. Grade 10-A Advanced Mathematics">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">ACADEMIC STAGE (GRADE) *</label>
                                <select class="form-select" name="stage_id" id="field_stage_id" required>
                                    <option value="">Select Stage...</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">SECTION *</label>
                                <select class="form-select" name="section_id" id="field_section_id" required>
                                    <option value="">Select Section...</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">PRIMARY SUBJECT</label>
                                <select class="form-select" name="subject_id" id="field_subject_id">
                                    <option value="">(Not Assigned)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">ASSIGNED TEACHER</label>
                                <select class="form-select" name="teacher_id" id="field_teacher_id">
                                    <option value="">(Not Assigned)</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">ASSIGNED CLASSROOM</label>
                                <select class="form-select" name="classroom_id" id="field_classroom_id">
                                    <option value="">(Not Assigned)</option>
                                </select>
                            </div>

                            <div class="col-md-12 pt-3 border-top d-flex justify-content-end gap-3">
                                <button type="reset" class="btn btn-light rounded-pill px-4" onclick="resetForm()">Discard Changes</button>
                                <button type="button" class="btn btn-premium px-5" id="btnSave">
                                    <span id="saveLabel">Save Class Information</span>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div id="listView" class="d-none">
                <div class="section-header">
                    <div>
                        <h4 class="fw-bold mb-1">Active Classes</h4>
                        <p class="text-muted small">Overview of all active academic sections.</p>
                    </div>
                    <button class="btn btn-premium rounded-pill" onclick="toggleView()"><i class="fa-solid fa-plus me-2"></i>Add New Class</button>
                </div>

                <div class="row g-4" id="classGrid">
                    <!-- Cards will be injected here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js?v=9"></script>
    <script>
        (function() {
            const gridEl = document.getElementById('classGrid');
            const form = document.getElementById('classForm');
            const btnSave = document.getElementById('btnSave');
            const overlay = document.getElementById('loadingOverlay');
            
            const stageSelect = document.getElementById('field_stage_id');
            const sectionSelect = document.getElementById('field_section_id');
            const teacherSelect = document.getElementById('field_teacher_id');
            const subjectSelect = document.getElementById('field_subject_id');
            const classroomSelect = document.getElementById('field_classroom_id');

            let user = {};
            try { user = JSON.parse(localStorage.getItem('user') || '{}'); } catch (e) {}
            
            document.getElementById('userNameDisplay').textContent = (user.name || 'Admin') + "'s School";
            document.getElementById('userAvatar').src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name || 'U') + '&background=ff4757&color=fff&bold=true';

            function showLoader() { overlay.classList.remove('d-none'); }
            function hideLoader() { overlay.classList.add('d-none'); }
            function esc(s) { const d = document.createElement('div'); d.textContent = s == null ? '' : String(s); return d.innerHTML; }

            async function loadMetadata() {
                try {
                    const res = await apiFetch('/classes/metadata');
                    if (res && res.success) {
                        const { stages, sections, classrooms, subjects } = res.data;
                        stageSelect.innerHTML = '<option value="">Select Stage...</option>' + 
                            stages.map(s => `<option value="${s.id}">${esc(s.name)}</option>`).join('');
                        sectionSelect.innerHTML = '<option value="">Select Section...</option>' + 
                            sections.map(s => `<option value="${s.id}">${esc(s.name)}</option>`).join('');
                        classroomSelect.innerHTML = '<option value="">(Not Assigned)</option>' + 
                            classrooms.map(s => `<option value="${s.id}">${esc(s.name)}</option>`).join('');
                        subjectSelect.innerHTML = '<option value="">(Not Assigned)</option>' + 
                            subjects.map(s => `<option value="${s.id}">${esc(s.name)}</option>`).join('');
                    }
                    
                    const tRes = await apiFetch('/teachers');
                    if (tRes && tRes.success) {
                        teacherSelect.innerHTML = '<option value="">(Not Assigned)</option>' + 
                            tRes.data.map(t => `<option value="${t.id}">${esc(t.name)}</option>`).join('');
                    }
                } catch (e) {
                    console.error('Metadata load failed', e);
                }
            }

            async function loadGrid() {
                showLoader();
                try {
                    const res = await apiFetch('/classes');
                    if (res && res.success) {
                        const rows = res.data || [];
                        if (!rows.length) {
                            gridEl.innerHTML = '<div class="col-12 text-center py-5"><p class="text-muted">No classes defined yet.</p></div>';
                        } else {
                            gridEl.innerHTML = rows.map(r => `
                                <div class="col-lg-4 col-md-6">
                                    <div class="class-card">
                                        <div class="action-btns">
                                            <button class="btn btn-circle btn-light text-primary btn-edit" data-id="${r.id}"><i class="fa-solid fa-pen"></i></button>
                                            <button class="btn btn-circle btn-light text-danger btn-del" data-id="${r.id}"><i class="fa-solid fa-trash"></i></button>
                                        </div>
                                        <div class="mb-3">
                                            <span class="badge badge-soft badge-soft-primary mb-2">${esc(r.grade_level)}</span>
                                            <span class="badge badge-soft badge-soft-info mb-2">Sec ${esc(r.section_name)}</span>
                                        </div>
                                        <h5 class="fw-bold mb-3">${esc(r.class_name)}</h5>
                                        <div class="small text-muted mb-2"><i class="fa-solid fa-user-tie me-2"></i>Teacher: <span class="text-dark fw-medium">${esc(r.teacher_name)}</span></div>
                                        <div class="small text-muted mb-2"><i class="fa-solid fa-book me-2"></i>Subject: <span class="text-dark fw-medium">${esc(r.subject_title)}</span></div>
                                        <div class="small text-muted"><i class="fa-solid fa-school me-2"></i>Room: <span class="text-dark fw-medium">${esc(r.room_name)}</span></div>
                                    </div>
                                </div>
                            `).join('');
                            
                            gridEl.querySelectorAll('.btn-edit').forEach(btn => btn.onclick = () => openEdit(btn.dataset.id));
                            gridEl.querySelectorAll('.btn-del').forEach(btn => btn.onclick = () => delClass(btn.dataset.id));
                        }
                    }
                } finally { hideLoader(); }
            }

            async function openEdit(id) {
                if (!id || id === 'undefined') return;
                showLoader();
                try {
                    const res = await apiFetch('/classes/' + id);
                    if (res && res.success) {
                        const r = res.data;
                        document.getElementById('field_id').value = r.class_id || r.id;
                        document.getElementById('field_class_name').value = r.class_name || r.name;
                        document.getElementById('field_stage_id').value = r.stage_id || '';
                        document.getElementById('field_section_id').value = r.section_id || '';
                        document.getElementById('field_teacher_id').value = r.teacher_id || '';
                        document.getElementById('field_subject_id').value = r.subject_id || '';
                        document.getElementById('field_classroom_id').value = r.classroom_id || '';
                        
                        document.getElementById('formTitle').innerHTML = '<i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Update Class';
                        document.getElementById('saveLabel').textContent = 'Update Class Information';
                        window.toggleView('form');
                    }
                } finally { hideLoader(); }
            }

            async function delClass(id) {
                if (!confirm('Are you absolutely sure you want to delete this class section?')) return;
                showLoader();
                try {
                    const res = await apiFetch('/classes/' + id, { method: 'DELETE' });
                    if (res && res.success) loadGrid();
                    else alert(res.message || 'Delete failed');
                } finally { hideLoader(); }
            }

            btnSave.onclick = async () => {
                const id = document.getElementById('field_id').value;
                const path = id ? '/classes/' + id : '/classes';
                const method = id ? 'PUT' : 'POST';
                
                const payload = {
                    class_name: document.getElementById('field_class_name').value.trim(),
                    stage_id: document.getElementById('field_stage_id').value || null,
                    section_id: document.getElementById('field_section_id').value || null,
                    teacher_id: document.getElementById('field_teacher_id').value || null,
                    subject_id: document.getElementById('field_subject_id').value || null,
                    classroom_id: document.getElementById('field_classroom_id').value || null
                };

                if (!payload.class_name || !payload.stage_id || !payload.section_id) {
                    alert('Class Name, Stage, and Section are mandatory.');
                    return;
                }

                showLoader();
                try {
                    const res = await apiFetch(path, { method, body: JSON.stringify(payload) });
                    if (res && res.success) {
                        resetForm();
                        toggleView('list');
                        loadGrid();
                    } else alert(res.message || 'Save failed');
                } finally { hideLoader(); }
            };

            window.toggleView = (v) => {
                const list = document.getElementById('listView');
                const form = document.getElementById('formView');
                if (v === 'form') {
                    list.classList.add('d-none');
                    form.classList.remove('d-none');
                } else if (v === 'list') {
                    list.classList.remove('d-none');
                    form.classList.add('d-none');
                } else {
                    list.classList.toggle('d-none');
                    form.classList.toggle('d-none');
                }
            };

            window.resetForm = () => {
                form.reset();
                document.getElementById('field_id').value = '';
                document.getElementById('formTitle').innerHTML = '<i class="fa-solid fa-plus-circle me-2 text-primary"></i>Define New Class';
                document.getElementById('saveLabel').textContent = 'Save Class Information';
            };

            loadMetadata().then(loadGrid);
        })();
    </script>
</body>
</html>
