@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="module-header">
            <h1 class="text-2xl font-semibold">Users Management</h1>
            <div>
                <button id="openCreateUser" class="btn btn-primary">Create user</button>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h3>All users</h3>
                <div>
                    <input id="usersSearch" class="form-input" placeholder="Search users..." style="width:220px;" />
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Name</th>
                        <th>Roles</th>
                        <th>Status</th>
                        <th style="width:160px; text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <tr>
                        <td colspan="5">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal uses layout modal overlay available in app layout -->
        <div id="userModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="userModalTitle">Create User</h3>
                    <button class="close" onclick="hideUserModal()">&times;</button>
                </div>
                <div id="userModalBody">
                    <form id="userForm">
                        <input type="hidden" name="user_id" />
                        <div class="grid" style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                            <div>
                                <label class="form-label">First name</label>
                                <input name="first_name" class="form-input" required />
                            </div>
                            <div>
                                <label class="form-label">Last name</label>
                                <input name="last_name" class="form-input" required />
                            </div>
                            <div>
                                <label class="form-label">Email</label>
                                <input name="email" type="email" class="form-input" required />
                            </div>
                            <div>
                                <label class="form-label">Password</label>
                                <input name="password" type="password" class="form-input" />
                            </div>
                            <div>
                                <label class="form-label">Role</label>
                                <select name="role_id" id="modalRoleSelect" class="form-select">
                                    <option value="">-- Select role --</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Phone</label>
                                <input name="phone" class="form-input" />
                            </div>
                            <div>
                                <label class="form-label">Job title</label>
                                <input name="job_title" class="form-input" />
                            </div>
                            <div
                                style="grid-column: 1 / -1; display:flex; gap:8px; justify-content:flex-end; margin-top:8px;">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <button type="button" class="btn" onclick="hideUserModal()">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let rolesCache = [];

            function showUserModal(mode = 'create', user = null) {
                const modal = document.getElementById('userModal');
                document.getElementById('userModalTitle').textContent = mode === 'create' ? 'Create User' : (mode === 'edit' ?
                    'Edit User' : 'View User');
                const form = document.getElementById('userForm');
                form.reset();
                form.querySelector('[name=user_id]').value = '';
                if (user) {
                    form.querySelector('[name=user_id]').value = user.id;
                    form.querySelector('[name=first_name]').value = user.first_name || '';
                    form.querySelector('[name=last_name]').value = user.last_name || '';
                    form.querySelector('[name=email]').value = user.email || '';
                    form.querySelector('[name=status]').value = user.status || 'active';
                    form.querySelector('[name=phone]').value = user.phone || '';
                    form.querySelector('[name=job_title]').value = user.job_title || '';
                    const roleId = (user.roles && user.roles[0]) ? user.roles[0].id : '';
                    document.getElementById('modalRoleSelect').value = roleId;
                }
                modal.classList.add('show');
            }

            function hideUserModal() {
                document.getElementById('userModal').classList.remove('show');
            }

            async function loadRolesIntoSelect() {
                const sel = document.getElementById('modalRoleSelect');
                sel.innerHTML = '<option value="">-- Select role --</option>';
                try {
                    const res = await fetch('/api/roles', {
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (res.status === 401 || res.status === 419) {
                        // Not authenticated; redirect to login
                        window.location = '/login';
                        return;
                    }
                    if (!res.ok) {
                        console.error('roles load failed', res.status, await safeText(res));
                        return;
                    }
                    const data = await res.json();
                    rolesCache = data;
                    data.forEach(r => {
                        const opt = document.createElement('option');
                        opt.value = r.id;
                        opt.textContent = r.display_name || r.name;
                        sel.appendChild(opt);
                    });
                } catch (e) {
                    console.error('Failed to load roles', e);
                }
            }

            async function loadUsers(q = '') {
                const tb = document.getElementById('usersTableBody');
                tb.innerHTML = '<tr><td colspan="5">Loading...</td></tr>';
                try {
                    // request many rows for simple datatable behavior; adjust per_page if needed
                    const res = await fetch('/api/users?per_page=1000', {
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (res.status === 401 || res.status === 419) {
                        window.location = '/login';
                        return;
                    }
                    if (!res.ok) {
                        console.error('users load failed', res.status, await safeText(res));
                        tb.innerHTML = '<tr><td colspan="5">Failed to load</td></tr>';
                        return;
                    }
                    const contentType = res.headers.get('content-type') || '';
                    const data = contentType.includes('application/json') ? await res.json() : null;
                    if (!data) {
                        console.error('users load returned non-json', await safeText(res));
                        tb.innerHTML = '<tr><td colspan="5">Failed to load</td></tr>';
                        return;
                    }
                    const users = data.data || data;
                    const filtered = users.filter(u => !(u.roles || []).some(r => r.name === 'customer'));
                    const ql = q.trim().toLowerCase();
                    const shown = ql ? filtered.filter(u => (u.first_name + ' ' + u.last_name + ' ' + (u.email || ''))
                        .toLowerCase().includes(ql)) : filtered;
                    if (shown.length === 0) {
                        tb.innerHTML = '<tr><td colspan="5">No users found</td></tr>';
                        return;
                    }
                    tb.innerHTML = '';
                    shown.forEach(u => {
                        const tr = document.createElement('tr');
                        const roles = (u.roles || []).map(r => r.display_name || r.name).join(', ');
                        tr.innerHTML = `
                    <td>${escapeHtml(u.email)}</td>
                    <td>${escapeHtml(u.first_name||'')} ${escapeHtml(u.last_name||'')}</td>
                    <td>${escapeHtml(roles)}</td>
                    <td>${escapeHtml(u.status||'')}</td>
<td style="text-align: right;">
    <div style="display: flex; justify-content: flex-end; gap: 5px;">
        <button class="btn btn-secondary" onclick='viewUser(${u.id})'>View</button>
        <button class="btn btn-primary" onclick='editUser(${u.id})'>Edit</button>
        <button class="btn btn-danger" onclick='deleteUser(${u.id})'>Delete</button>
    </div>
</td>
                `;
                        tb.appendChild(tr);
                    });
                } catch (e) {
                    console.error(e);
                    tb.innerHTML = '<tr><td colspan="5">Failed to load</td></tr>';
                }
            }

            async function safeText(res) {
                try {
                    return await res.text();
                } catch (e) {
                    return '<no body>';
                }
            }

            function escapeHtml(s) {
                if (s === null || s === undefined) return '';
                return String(s).replace(/[&<>"']/g, c => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": "&#39;"
                } [c]));
            }

            // CSRF handling: read token from meta tag and ensure the sanctum CSRF cookie is set.
            let _csrfReady = false;

            function getCsrfTokenFromMeta() {
                const m = document.querySelector('meta[name="csrf-token"]');
                return m ? m.getAttribute('content') : null;
            }
            async function ensureCsrf() {
                if (_csrfReady) return;
                try {
                    // Call sanctum endpoint to set XSRF-TOKEN cookie for same-site session auth
                    await fetch('/sanctum/csrf-cookie', {
                        credentials: 'same-origin'
                    });
                    // Also refresh token from meta tag (server-rendered) as a fallback
                    const t = getCsrfTokenFromMeta();
                    if (t) _csrfReady = true; // token presence means we're ready to send header
                    else _csrfReady = true; // even if meta not present, cookie was requested; allow requests
                } catch (e) {
                    console.error('Failed to get CSRF cookie', e);
                }
            }

            async function viewUser(id) {
                try {
                    const res = await fetch('/api/users/' + id, {
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (!res.ok) {
                        showToaster('error', 'Error', 'Failed to load user');
                        return;
                    }
                    const user = await res.json();
                    showUserModal('view', user);
                    // make form readonly
                    document.querySelectorAll('#userForm input, #userForm select').forEach(el => el.setAttribute('disabled',
                        'disabled'));
                } catch (e) {
                    console.error(e);
                    showToaster('error', 'Error', 'Failed to load user');
                }
            }

            async function editUser(id) {
                try {
                    const res = await fetch('/api/users/' + id, {
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (!res.ok) {
                        showToaster('error', 'Error', 'Failed to load user');
                        return;
                    }
                    const user = await res.json();
                    showUserModal('edit', user);
                    document.querySelectorAll('#userForm input, #userForm select').forEach(el => el.removeAttribute(
                        'disabled'));
                } catch (e) {
                    console.error(e);
                    showToaster('error', 'Error', 'Failed to load user');
                }
            }

            async function deleteUser(id) {
                if (!(await showConfirm('Delete this user? This cannot be undone.'))) return;
                try {
                    await ensureCsrf();
                    const headers = {
                        'Accept': 'application/json'
                    };
                    const metaToken = getCsrfTokenFromMeta();
                    if (metaToken) headers['X-CSRF-TOKEN'] = metaToken;
                    const res = await fetch('/api/users/' + id, {
                        method: 'DELETE',
                        credentials: 'same-origin',
                        headers
                    });
                    if (res.status === 204) {
                        showToaster('success', 'Deleted', 'User deleted');
                        loadUsers();
                        return;
                    }
                    const err = await res.json();
                    showToaster('error', 'Error', 'Delete failed: ' + (err.message || JSON.stringify(err)));
                } catch (e) {
                    console.error(e);
                    showToaster('error', 'Error', 'Delete failed');
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                document.getElementById('openCreateUser').addEventListener('click', () => {
                    showUserModal('create', null);
                    document.querySelectorAll('#userForm input, #userForm select').forEach(el => el
                        .removeAttribute('disabled'));
                });
                document.getElementById('usersSearch').addEventListener('input', e => loadUsers(e.target.value));
                document.getElementById('userForm').addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const fd = new FormData(e.target);
                    const data = Object.fromEntries(fd.entries());
                    const userId = data.user_id;
                    // remove empty password to avoid overriding
                    if (!data.password) delete data.password;
                    try {
                        await ensureCsrf();
                        const method = userId ? 'PUT' : 'POST';
                        const url = userId ? '/api/users/' + userId : '/api/users';
                        const headers = {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        };
                        const metaToken = getCsrfTokenFromMeta();
                        if (metaToken) headers['X-CSRF-TOKEN'] = metaToken;
                        const res = await fetch(url, {
                            method,
                            credentials: 'same-origin',
                            headers,
                            body: JSON.stringify(data)
                        });
                        if (res.ok) {
                            hideUserModal();
                            showToaster('success', 'Saved', 'User saved');
                            loadUsers();
                            return;
                        }
                        const err = await res.json();
                        showToaster('error', 'Error', 'Failed: ' + (err.message || JSON.stringify(err.errors || err)));
                    } catch (ex) {
                        console.error(ex);
                        showToaster('error', 'Error', 'Save failed');
                    }
                });

                loadRolesIntoSelect();
                loadUsers();
            });
        </script>
    @endpush
@endsection
