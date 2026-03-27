<?php $activeNav = 'students'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student registry — School Management</title>
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

        .nav-link.active {
            background: #fff1f2;
            color: #ff4757;
        }

        .nav-link:hover:not(.active) {
            background: #f1f5f9;
        }

        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <?php require __DIR__ . '/partials/sidebar.php'; ?>

        <div class="main-content flex-grow-1">
            <header class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <h5 class="mb-0 fw-bold me-2 text-dark" id="userNameDisplay">Loading...</h5>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded px-2 py-1"
                        style="font-size: 0.70rem;">Registry</span>
                </div>
                <div class="d-flex align-items-center text-muted fw-medium font-sm">
                    <a href="dashboard.php" class="text-decoration-none text-muted me-3">Dashboard</a>
                    <div class="dropdown">
                        <a href="#" class="text-decoration-none text-dark d-flex align-items-center"
                            data-bs-toggle="dropdown">
                            <img src="https://ui-avatars.com/api/?name=User" id="userAvatar" class="rounded-circle me-1"
                                width="30" height="30" alt="">
                            <i class="fa-solid fa-chevron-down ms-1 text-muted" style="font-size: 0.70rem;"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 12px;">
                            <li><a class="dropdown-item py-2 text-danger" href="#" onclick="logout(); return false;"><i
                                        class="fa-solid fa-arrow-right-from-bracket me-2"></i>Sign out</a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">Student registry</h4>
                    <p class="text-muted small mb-0">CRUD for <code>student_registry</code> (code, name, gender, DOB,
                        contact, school / stage / section).</p>
                </div>
                <button type="button" class="btn btn-danger rounded-pill px-4 d-none" id="btnAdd">
                    <i class="fa-solid fa-plus me-2"></i>Add student
                </button>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-3 mb-3 bg-white">
                <div class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label small text-muted mb-1">Search</label>
                        <input type="text" class="form-control rounded-3" id="searchInput"
                            placeholder="Name, code, or email…">
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-outline-secondary rounded-3 w-100"
                            id="btnSearch">Apply</button>
                    </div>
                </div>
            </div>

            <div class="alert alert-danger d-none rounded-4" id="errorBanner" role="alert"></div>

            <div class="table-responsive bg-white shadow-sm rounded-4">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>Full name</th>
                            <th>Gender</th>
                            <th>DOB</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>School</th>
                            <th>Stage</th>
                            <th>Section</th>
                            <th class="text-end" id="thActions">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="registryBody">
                        <tr>
                            <td colspan="11" class="text-center py-4 text-muted">Loading…</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="registryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalTitle">Add student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <form id="registryForm">
                        <input type="hidden" name="student_id" id="field_student_id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small">Student code *</label>
                                <input type="text" class="form-control rounded-3" name="student_code"
                                    id="field_student_code" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Full name *</label>
                                <input type="text" class="form-control rounded-3" name="student_full_name"
                                    id="field_student_full_name" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Gender</label>
                                <select class="form-select rounded-3" name="gender" id="field_gender">
                                    <option value="">—</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Date of birth</label>
                                <input type="date" class="form-control rounded-3" name="dob" id="field_dob">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Phone</label>
                                <input type="text" class="form-control rounded-3" name="phone" id="field_phone">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Email</label>
                                <input type="email" class="form-control rounded-3" name="email" id="field_email">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">School</label>
                                <select class="form-select rounded-3" name="school_id" id="field_school_id">
                                    <option value="">— Select School —</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Grade (Stage)</label>
                                <select class="form-select rounded-3" name="stage_id" id="field_stage_id">
                                    <option value="">— Select Grade —</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Section</label>
                                <select class="form-select rounded-3" name="section_id" id="field_section_id">
                                    <option value="">— Select Sec —</option>
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
    <script src="assets/js/app.js?v=6"></script>
    <script>
        (function() {
            const bodyEl = document.getElementById('registryBody');
            const errEl = document.getElementById('errorBanner');
            const btnAdd = document.getElementById('btnAdd');
            const btnSave = document.getElementById('btnSave');
            const modalTitle = document.getElementById('modalTitle');
            const form = document.getElementById('registryForm');
            const modalEl = document.getElementById('registryModal');
            const modal = new bootstrap.Modal(modalEl);

            let user = {};
            try {
                user = JSON.parse(localStorage.getItem('user') || '{}');
            } catch (e) {}
            const canEdit = user.role === 'admin' || user.role === 'teacher';

            document.getElementById('userNameDisplay').textContent = (user.name || 'User') + "'s registry";
            document.getElementById('userAvatar').src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user
                .name || 'U') + '&background=f1f5f9&color=475569';

            const colCount = canEdit ? 11 : 10;
            if (canEdit) {
                btnAdd.classList.remove('d-none');
            } else {
                document.getElementById('thActions').classList.add('d-none');
            }

            function showErr(msg) {
                errEl.textContent = msg;
                errEl.classList.remove('d-none');
            }

            function hideErr() {
                errEl.classList.add('d-none');
            }

            function esc(s) {
                const d = document.createElement('div');
                d.textContent = s == null ? '' : String(s);
                return d.innerHTML;
            }

            function valNum(el) {
                const v = el.value.trim();
                return v === '' ? null : parseInt(v, 10);
            }

            function payloadFromForm() {
                return {
                    student_code: document.getElementById('field_student_code').value.trim(),
                    student_full_name: document.getElementById('field_student_full_name').value.trim(),
                    gender: document.getElementById('field_gender').value || null,
                    dob: document.getElementById('field_dob').value || null,
                    email: document.getElementById('field_email').value.trim() || null,
                    phone: document.getElementById('field_phone').value.trim() || null,
                    school_id: valNum(document.getElementById('field_school_id')),
                    stage_id: valNum(document.getElementById('field_stage_id')),
                    section_id: valNum(document.getElementById('field_section_id')),
                };
            }

            async function loadList() {
                hideErr();
                bodyEl.innerHTML = '<tr><td colspan="' + colCount +
                    '" class="text-center py-4 text-muted">Loading…</td></tr>';
                const q = document.getElementById('searchInput').value.trim();
                const url = '/student-registry' + (q ? '?search=' + encodeURIComponent(q) : '');
                const res = await apiFetch(url);
                if (!res) return;
                if (!res.success) {
                    showErr(res.message || 'Failed to load');
                    bodyEl.innerHTML = '<tr><td colspan="' + colCount +
                        '" class="text-center py-4 text-danger">Error</td></tr>';
                    return;
                }
                const rows = res.data || [];
                if (!rows.length) {
                    bodyEl.innerHTML = '<tr><td colspan="' + colCount +
                        '" class="text-center py-4 text-muted">No records yet.</td></tr>';
                    return;
                }
                bodyEl.innerHTML = rows.map(r => {
                    const actions = canEdit ?
                        `<button type="button" class="btn btn-sm btn-outline-primary me-1 btn-edit" data-id="${r.student_id}">Edit</button>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-del" data-id="${r.student_id}">Delete</button>` :
                        '';
                    const actionCell = canEdit ? `<td class="text-end">${actions}</td>` : '';
                    return `<tr>
                <td>${esc(r.student_id)}</td>
                <td>${esc(r.student_code)}</td>
                <td>${esc(r.student_full_name)}</td>
                <td>${esc(r.gender || '—')}</td>
                <td>${esc(r.dob || '—')}</td>
                <td>${esc(r.email || '—')}</td>
                <td>${esc(r.phone || '—')}</td>
                <td>${esc(r.school_name || '—')}</td>
                <td>${esc(r.stage_label || '—')}</td>
                <td>${esc(r.section_label || '—')}</td>
                ${actionCell}
            </tr>`;
                }).join('');

                bodyEl.querySelectorAll('.btn-edit').forEach(btn => {
                    btn.addEventListener('click', () => openEdit(parseInt(btn.dataset.id, 10)));
                });
                bodyEl.querySelectorAll('.btn-del').forEach(btn => {
                    btn.addEventListener('click', () => delRow(parseInt(btn.dataset.id, 10)));
                });
            }

            async function openEdit(id) {
                hideErr();
                const res = await apiFetch('/student-registry/' + id);
                if (!res || !res.success) {
                    showErr((res && res.message) || 'Not found');
                    return;
                }
                const r = res.data;
                modalTitle.textContent = 'Edit student';
                document.getElementById('field_student_id').value = r.student_id;
                document.getElementById('field_student_code').value = r.student_code || '';
                document.getElementById('field_student_full_name').value = r.student_full_name || '';
                document.getElementById('field_gender').value = r.gender || '';
                document.getElementById('field_dob').value = r.dob || '';
                document.getElementById('field_email').value = r.email || '';
                document.getElementById('field_phone').value = r.phone || '';
                document.getElementById('field_school_id').value = r.school_id != null ? r.school_id : '';
                document.getElementById('field_stage_id').value = r.stage_id != null ? r.stage_id : '';
                document.getElementById('field_section_id').value = r.section_id != null ? r.section_id : '';
                modal.show();
            }

            btnAdd.addEventListener('click', () => {
                modalTitle.textContent = 'Add student';
                form.reset();
                document.getElementById('field_student_id').value = '';
                modal.show();
            });

            btnSave.addEventListener('click', async () => {
                hideErr();
                const id = document.getElementById('field_student_id').value;
                const payload = payloadFromForm();
                const method = id ? 'PUT' : 'POST';
                const path = id ? '/student-registry/' + id : '/student-registry';
                let res = null;
                try {
                    res = await apiFetch(path, {
                        method,
                        body: JSON.stringify(payload)
                    });
                } catch (e) {
                    alert(e.message || 'Save failed');
                    return;
                }
                if (!res) return;
                if (!res.success) {
                    const extra = res.errors ? ('\n' + Object.values(res.errors).join('\n')) : '';
                    alert((res.message || 'Save failed') + extra);
                    return;
                }
                modal.hide();
                loadList();
            });

            async function delRow(id) {
                if (!confirm('Delete this registry row?')) return;
                hideErr();
                const res = await apiFetch('/student-registry/' + id, {
                    method: 'DELETE'
                });
                if (!res) return;
                if (!res.success) {
                    alert(res.message || 'Delete failed');
                    return;
                }
                loadList();
            }

            document.getElementById('btnSearch').addEventListener('click', loadList);
            document.getElementById('searchInput').addEventListener('keydown', e => {
                if (e.key === 'Enter') loadList();
            });

            async function loadMetadata() {
                const res = await apiFetch('/student-registry/metadata');
                if (res && res.success) {
                    const m = res.data;
                    document.getElementById('field_school_id').innerHTML += m.schools.map(s => `<option value="${s.id}">${esc(s.name)}</option>`).join('');
                    document.getElementById('field_stage_id').innerHTML += m.stages.map(s => `<option value="${s.id}">${esc(s.name)}</option>`).join('');
                    document.getElementById('field_section_id').innerHTML += m.sections.map(s => `<option value="${s.id}">${esc(s.name)}</option>`).join('');
                }
            }

            loadMetadata();
            loadList();
        })();
    </script>

</body>

</html>