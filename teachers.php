<?php $activeNav = 'teachers'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teachers Management — School Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" referrerpolicy="no-referrer">
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

        .avatar-img {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            object-fit: cover;
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
                        style="font-size: 0.70rem;">Teachers</span>
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
                    <h4 class="fw-bold mb-1 text-dark">Teachers list</h4>
                    <p class="text-muted small mb-0">Manage school faculty members and their credentials.</p>
                </div>
                <button type="button" class="btn btn-danger rounded-pill px-4 d-none" id="btnAdd">
                    <i class="fa-solid fa-plus me-2"></i>Add Teacher
                </button>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-3 mb-3 bg-white">
                <div class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label small text-muted mb-1">Search</label>
                        <input type="text" class="form-control rounded-3" id="searchInput"
                            placeholder="Name, Employee ID, or email…">
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
                            <th>Photo</th>
                            <th>Full Name</th>
                            <th>Employee ID</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Joined Date</th>
                            <th class="text-end" id="thActions">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="listBody">
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Loading…</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="mainModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalTitle">Add Teacher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <form id="mainForm">
                        <input type="hidden" name="id" id="field_id">
                        <div class="mb-3 text-center">
                            <div class="position-relative d-inline-block">
                                <img src="https://ui-avatars.com/api/?name=T&background=f1f5f9&color=475569"
                                    id="preview_img" class="rounded-4 border shadow-sm"
                                    style="width: 100px; height: 100px; object-fit: cover;">
                                <label for="field_profile_image"
                                    class="btn btn-sm btn-light position-absolute bottom-0 end-0 rounded-circle border shadow-sm"
                                    style="transform: translate(25%, 25%);">
                                    <i class="fa-solid fa-camera"></i>
                                </label>
                                <input type="file" id="field_profile_image" name="profile_image" class="d-none"
                                    accept="image/*">
                            </div>
                            <div class="small text-muted mt-2">Teacher Photo</div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small">Full Name *</label>
                                <input type="text" class="form-control rounded-3" name="name" id="field_name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Email *</label>
                                <input type="email" class="form-control rounded-3" name="email" id="field_email"
                                    required>
                            </div>
                            <div class="col-md-6" id="passwordContainer">
                                <label class="form-label small">Password *</label>
                                <input type="password" class="form-control rounded-3" name="password"
                                    id="field_password" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Employee ID *</label>
                                <input type="text" class="form-control rounded-3" name="employee_id"
                                    id="field_employee_id" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Phone</label>
                                <input type="text" class="form-control rounded-3" name="phone" id="field_phone">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Gender</label>
                                <select class="form-select rounded-3" name="gender" id="field_gender">
                                    <option value="">- Select -</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Date of Birth</label>
                                <input type="date" class="form-control rounded-3" name="dob" id="field_dob">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Joined Date</label>
                                <input type="date" class="form-control rounded-3" name="joined_date"
                                    id="field_joined_date">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small">Address</label>
                                <textarea class="form-control rounded-3" name="address" id="field_address"
                                    rows="2"></textarea>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
            const passContainer = document.getElementById('passwordContainer');

            let user = {};
            try {
                user = JSON.parse(localStorage.getItem('user') || '{}');
            } catch (e) {}
            const isAdmin = user.role === 'admin';

            document.getElementById('userNameDisplay').textContent = (user.name || 'User') + "'s School";
            document.getElementById('userAvatar').src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user
                .name || 'U') + '&background=f1f5f9&color=475569';

            if (isAdmin) {
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

            async function loadList() {
                hideErr();
                bodyEl.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">Loading…</td></tr>';
                const q = document.getElementById('searchInput').value.trim();
                const url = '/teachers' + (q ? '?search=' + encodeURIComponent(q) : '');
                try {
                    const res = await apiFetch(url);
                    if (!res) return;
                    if (!res.success) {
                        showErr(res.message || 'Failed to load');
                        bodyEl.innerHTML =
                            '<tr><td colspan="8" class="text-center py-4 text-danger">Error</td></tr>';
                        return;
                    }
                    const rows = res.data || [];
                    if (!rows.length) {
                        bodyEl.innerHTML =
                            '<tr><td colspan="8" class="text-center py-4 text-muted">No teachers found.</td></tr>';
                        return;
                    }
                    bodyEl.innerHTML = rows.map(r => {
                        const actions = isAdmin ?
                            `<button type="button" class="btn btn-sm btn-outline-primary me-1 btn-edit" data-id="${r.id}">Edit</button>
                             <button type="button" class="btn btn-sm btn-outline-danger btn-del" data-id="${r.id}">Delete</button>` :
                            '';
                        const photo = r.profile_image ? (window.location.origin +
                                '/Kingardent/school-management/' + r.profile_image) :
                            `https://ui-avatars.com/api/?name=${encodeURIComponent(r.name)}&background=random`;
                        return `<tr>
                            <td>${esc(r.id)}</td>
                            <td><img src="${photo}" class="avatar-img"></td>
                            <td><div class="fw-bold">${esc(r.name)}</div></td>
                            <td>${esc(r.employee_id)}</td>
                            <td>${esc(r.email)}</td>
                            <td>${esc(r.phone || '—')}</td>
                            <td>${esc(r.joined_date || '—')}</td>
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
                    bodyEl.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-danger">Error</td></tr>';
                }
            }

            async function openEdit(id) {
                hideErr();
                const res = await apiFetch('/teachers/' + id);
                if (!res || !res.success) {
                    alert((res && res.message) || 'Not found');
                    return;
                }
                const r = res.data;
                modalTitle.textContent = 'Edit Teacher';
                document.getElementById('field_id').value = r.id;
                document.getElementById('field_name').value = r.name || '';
                document.getElementById('field_email').value = r.email || '';
                document.getElementById('field_employee_id').value = r.employee_id || '';
                document.getElementById('field_phone').value = r.phone || '';
                document.getElementById('field_gender').value = r.gender || '';
                document.getElementById('field_dob').value = r.dob || '';
                document.getElementById('field_joined_date').value = r.joined_date || '';
                document.getElementById('field_address').value = r.address || '';

                if (r.profile_image) {
                    document.getElementById('preview_img').src = window.location.origin +
                        '/Kingardent/school-management/' + r.profile_image;
                } else {
                    document.getElementById('preview_img').src =
                        `https://ui-avatars.com/api/?name=${encodeURIComponent(r.name || 'T')}&background=f1f5f9&color=475569`;
                }
                passContainer.classList.add('d-none');
                document.getElementById('field_password').required = false;
                modal.show();
            }

            btnAdd.addEventListener('click', () => {
                modalTitle.textContent = 'Add Teacher';
                form.reset();
                document.getElementById('field_id').value = '';
                document.getElementById('preview_img').src =
                    'https://ui-avatars.com/api/?name=T&background=f1f5f9&color=475569';
                passContainer.classList.remove('d-none');
                document.getElementById('field_password').required = true;
                modal.show();
            });

            btnSave.addEventListener('click', async () => {
                hideErr();
                const id = document.getElementById('field_id').value;
                const path = id ? '/teachers/' + id : '/teachers';
                const method = 'POST'; // Use POST for both to support FormData/Uploads

                const formData = new FormData();
                if (id) formData.append('_method', 'PUT'); // Spoof PUT for routing

                formData.append('name', document.getElementById('field_name').value.trim());
                formData.append('email', document.getElementById('field_email').value.trim());
                formData.append('employee_id', document.getElementById('field_employee_id').value.trim());
                formData.append('phone', document.getElementById('field_phone').value.trim());
                formData.append('gender', document.getElementById('field_gender').value || '');
                formData.append('dob', document.getElementById('field_dob').value || '');
                formData.append('joined_date', document.getElementById('field_joined_date').value || '');
                formData.append('address', document.getElementById('field_address').value.trim());

                const imgFile = document.getElementById('field_profile_image').files[0];
                if (imgFile) formData.append('profile_image', imgFile);

                if (!id) formData.append('password', document.getElementById('field_password').value);

                try {
                    const res = await apiFetch(path, {
                        method,
                        body: formData
                    });
                    if (!res) return;
                    if (!res.success) {
                        alert(res.message || 'Save failed');
                        return;
                    }
                    modal.hide();
                    loadList();
                } catch (e) {
                    alert(e.message || 'Save failed');
                }
            });

            async function delRow(id) {
                if (!confirm('Delete this teacher and their user account?')) return;
                try {
                    const res = await apiFetch('/teachers/' + id, {
                        method: 'DELETE'
                    });
                    if (!res) return;
                    if (!res.success) {
                        alert(res.message || 'Delete failed');
                        return;
                    }
                    loadList();
                } catch (e) {
                    alert(e.message);
                }
            }

            document.getElementById('btnSearch').addEventListener('click', loadList);
            document.getElementById('searchInput').addEventListener('keydown', e => {
                if (e.key === 'Enter') loadList();
            });

            document.getElementById('field_profile_image').addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(ex) {
                        document.getElementById('preview_img').src = ex.target.result;
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });

            loadList();
        })();
    </script>
</body>

</html>
