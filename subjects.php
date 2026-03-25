<?php $activeNav = 'subjects'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subjects Management — School Management</title>
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
    </style>
</head>

<body>
    <div class="d-flex">
        <?php require __DIR__ . '/partials/sidebar.php'; ?>

        <div class="main-content flex-grow-1">
            <header class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <h5 class="mb-0 fw-bold me-2 text-dark" id="userNameDisplay">Loading...</h5>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded px-2 py-1" style="font-size: 0.70rem;">Subjects</span>
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
                    <h4 class="fw-bold mb-1 text-dark">School Subjects</h4>
                    <p class="text-muted small mb-0">Manage teaching subjects, codes, and faculty assignments.</p>
                </div>
                <button type="button" class="btn btn-danger rounded-pill px-4 d-none" id="btnAdd">
                    <i class="fa-solid fa-plus me-2"></i>Add Subject
                </button>
            </div>

            <div class="alert alert-danger d-none rounded-4" id="errorBanner" role="alert"></div>

            <div class="table-responsive bg-white shadow-sm rounded-4">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Subject Name</th>
                            <th>Subject Code</th>
                            <th>Assigned Teacher</th>
                            <th>Class</th>
                            <th class="text-end" id="thActions">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="listBody">
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Loading…</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="mainModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalTitle">Add Subject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <form id="mainForm">
                        <input type="hidden" name="id" id="field_id">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small">Subject Name *</label>
                                <input type="text" class="form-control rounded-3" name="name" id="field_name" required placeholder="e.g. Mathematics, History">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small">Subject Code *</label>
                                <input type="text" class="form-control rounded-3" name="code" id="field_code" required placeholder="e.g. MATH101, HIST202">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small">Assign Teacher</label>
                                <select class="form-select rounded-3" name="teacher_id" id="field_teacher_id">
                                    <option value="">(None)</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small">Class (Optional)</label>
                                <select class="form-select rounded-3" name="class_id" id="field_class_id">
                                    <option value="">(None)</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger rounded-pill px-4" id="btnSave">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js?v=7"></script>
    <script>
        (function() {
            const bodyEl = document.getElementById('listBody');
            const errEl = document.getElementById('errorBanner');
            const btnAdd = document.getElementById('btnAdd');
            const btnSave = document.getElementById('btnSave');
            const modalTitle = document.getElementById('modalTitle');
            const form = document.getElementById('mainForm');
            const modalEl = document.getElementById('mainModal');
            const modal = new bootstrap.Modal(modalEl);
            const teacherSelect = document.getElementById('field_teacher_id');
            const classSelect = document.getElementById('field_class_id');

            let user = {};
            try { user = JSON.parse(localStorage.getItem('user') || '{}'); } catch (e) {}
            const isAdmin = user.role === 'admin';

            document.getElementById('userNameDisplay').textContent = (user.name || 'User') + "'s School";
            document.getElementById('userAvatar').src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name || 'U') + '&background=f1f5f9&color=475569';

            if (isAdmin) {
                btnAdd.classList.remove('d-none');
            } else {
                document.getElementById('thActions').classList.add('d-none');
            }

            function showErr(msg) { errEl.textContent = msg; errEl.classList.remove('d-none'); }
            function hideErr() { errEl.classList.add('d-none'); }
            function esc(s) { const d = document.createElement('div'); d.textContent = s == null ? '' : String(s); return d.innerHTML; }

            async function loadTeachers() {
                try {
                    const res = await apiFetch('/teachers');
                    if (res && res.success && res.data) {
                        teacherSelect.innerHTML = '<option value="">(None)</option>' + 
                            res.data.map(t => `<option value="${t.id}">${esc(t.name)}</option>`).join('');
                    }
                } catch (e) {}
            }
            async function loadClasses() {
                try {
                    const res = await apiFetch('/classes');
                    if (res && res.success && res.data) {
                        classSelect.innerHTML = '<option value="">(None)</option>' + 
                            res.data.map(c => `<option value="${c.id}">${esc(c.name)}</option>`).join('');
                    }
                } catch (e) {}
            }

            async function loadList() {
                hideErr();
                bodyEl.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">Loading…</td></tr>';
                try {
                    const res = await apiFetch('/subjects');
                    if (!res) return;
                    if (!res.success) {
                        showErr(res.message || 'Failed to load');
                        bodyEl.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-danger">Error</td></tr>';
                        return;
                    }
                    const rows = res.data || [];
                    if (!rows.length) {
                        bodyEl.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No subjects found.</td></tr>';
                        return;
                    }
                    bodyEl.innerHTML = rows.map(r => {
                        const actions = isAdmin ?
                            `<button type="button" class="btn btn-sm btn-outline-primary me-1 btn-edit" data-id="${r.id}">Edit</button>
                             <button type="button" class="btn btn-sm btn-outline-danger btn-del" data-id="${r.id}">Delete</button>` : '';
                        return `<tr>
                            <td>${esc(r.id)}</td>
                            <td><div class="fw-bold text-danger">${esc(r.name)}</div></td>
                            <td><code>${esc(r.code)}</code></td>
                            <td>${esc(r.teacher_name || '(None)')}</td>
                            <td>${esc(r.class_name || '(None)')}</td>
                            ${isAdmin ? `<td class="text-end">${actions}</td>` : ''}
                        </tr>`;
                    }).join('');

                    bodyEl.querySelectorAll('.btn-edit').forEach(btn => {
                        btn.addEventListener('click', () => openEdit(parseInt(btn.dataset.id, 10)));
                    });
                    bodyEl.querySelectorAll('.btn-del').forEach(btn => {
                        btn.addEventListener('click', () => delRow(parseInt(btn.dataset.id, 10)));
                    });
                } catch (e) {
                    showErr(e.message);
                    bodyEl.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-danger">Error</td></tr>';
                }
            }

            async function openEdit(id) {
                hideErr();
                const res = await apiFetch('/subjects/' + id);
                if (!res || !res.success) { alert('Not found'); return; }
                const r = res.data;
                modalTitle.textContent = 'Edit Subject';
                document.getElementById('field_id').value = r.id;
                document.getElementById('field_name').value = r.name || '';
                document.getElementById('field_code').value = r.code || '';
                document.getElementById('field_teacher_id').value = r.teacher_id || '';
                document.getElementById('field_class_id').value = r.class_id || '';
                modal.show();
            }

            btnAdd.addEventListener('click', () => {
                modalTitle.textContent = 'Add Subject';
                form.reset();
                document.getElementById('field_id').value = '';
                modal.show();
            });

            btnSave.addEventListener('click', async () => {
                hideErr();
                const id = document.getElementById('field_id').value;
                const path = id ? '/subjects/' + id : '/subjects';
                const method = id ? 'PUT' : 'POST';
                const payload = {
                    name: document.getElementById('field_name').value.trim(),
                    code: document.getElementById('field_code').value.trim(),
                    teacher_id: document.getElementById('field_teacher_id').value || null,
                    class_id: document.getElementById('field_class_id').value || null
                };
                if (!payload.name || !payload.code) { alert('Name and Code are required.'); return; }

                try {
                    const res = await apiFetch(path, { method, body: JSON.stringify(payload) });
                    if (!res) return;
                    if (!res.success) { alert(res.message || 'Save failed'); return; }
                    modal.hide();
                    loadList();
                } catch (e) { alert(e.message || 'Save failed'); }
            });

            async function delRow(id) {
                if (!confirm('Delete this subject?')) return;
                try {
                    const res = await apiFetch('/subjects/' + id, { method: 'DELETE' });
                    if (!res) return;
                    if (!res.success) { alert(res.message || 'Delete failed'); return; }
                    loadList();
                } catch (e) { alert(e.message); }
            }

            Promise.all([loadTeachers(), loadClasses(), loadList()]);
        })();
    </script>
</body>
</html>
