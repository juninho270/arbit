<?php
$title = 'Gerenciar Usuários - CryptoArb Pro';
$currentPage = 'admin';
?>

<div class="fade-in">
    <!-- Header -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Gerenciamento de Usuários</h2>
            <button onclick="showAddUserModal()" class="btn btn-primary">
                <span>➕</span>
                <span>Adicionar Usuário</span>
            </button>
        </div>
        
        <!-- Users Table -->
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>Saldo Principal</th>
                        <th>Saldo Bot</th>
                        <th>Status</th>
                        <th>Último Login</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $userItem): ?>
                        <tr>
                            <td>
                                <div>
                                    <div style="color: #E6E6E6; font-weight: 500;"><?php echo htmlspecialchars($userItem['name']); ?></div>
                                    <div style="color: #A6A6A6; font-size: 14px;"><?php echo htmlspecialchars($userItem['email']); ?></div>
                                    <div style="color: #A6A6A6; font-size: 12px; text-transform: capitalize;"><?php echo htmlspecialchars($userItem['role']); ?></div>
                                </div>
                            </td>
                            <td style="color: #E6E6E6;">$<?php echo number_format($userItem['balance'], 2); ?></td>
                            <td style="color: #E6E6E6;">$<?php echo number_format($userItem['bot_balance'], 2); ?></td>
                            <td>
                                <?php
                                $statusClass = $userItem['status'] === 'active' ? 'badge-success' : 
                                              ($userItem['status'] === 'suspended' ? 'badge-danger' : 'badge-warning');
                                $statusText = $userItem['status'] === 'active' ? 'Ativo' : 
                                             ($userItem['status'] === 'suspended' ? 'Suspenso' : 'Pendente');
                                ?>
                                <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                            </td>
                            <td style="color: #A6A6A6;">
                                <?php echo $userItem['last_login'] ? date('d/m/Y H:i', strtotime($userItem['last_login'])) : 'Nunca'; ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <button onclick="editUser(<?php echo htmlspecialchars(json_encode($userItem)); ?>)" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">
                                        ✏️ Editar
                                    </button>
                                    <?php if ($userItem['role'] !== 'admin'): ?>
                                        <form method="POST" action="/admin/users/<?php echo $userItem['id']; ?>/login-as" style="display: inline;">
                                            <button type="submit" class="btn btn-info" style="padding: 6px 12px; font-size: 12px;">
                                                👤 Login
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if ($userItem['id'] != $user['id']): ?>
                                        <button onclick="deleteUser(<?php echo $userItem['id']; ?>, '<?php echo htmlspecialchars($userItem['name']); ?>')" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;">
                                            🗑️ Excluir
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" style="display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background-color: #1A1A1A; border: 1px solid #444; border-radius: 8px; padding: 24px; width: 90%; max-width: 500px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h3 style="color: #E6E6E6; margin: 0;">Adicionar Usuário</h3>
            <button onclick="hideAddUserModal()" style="background: none; border: none; color: #A6A6A6; font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        
        <form method="POST" action="/admin/users/create">
            <div class="form-group">
                <label>Nome Completo</label>
                <input type="text" name="name" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-input" required>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label>Saldo Principal</label>
                    <input type="number" name="balance" class="form-input" value="1000" step="0.01">
                </div>
                
                <div class="form-group">
                    <label>Saldo Bot</label>
                    <input type="number" name="bot_balance" class="form-input" value="0" step="0.01">
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label>Função</label>
                    <select name="role" class="form-select">
                        <option value="user">Usuário</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-select">
                        <option value="active">Ativo</option>
                        <option value="suspended">Suspenso</option>
                        <option value="pending">Pendente</option>
                    </select>
                </div>
            </div>
            
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="hideAddUserModal()" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Criar Usuário</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" style="display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background-color: #1A1A1A; border: 1px solid #444; border-radius: 8px; padding: 24px; width: 90%; max-width: 500px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h3 style="color: #E6E6E6; margin: 0;">Editar Usuário</h3>
            <button onclick="hideEditUserModal()" style="background: none; border: none; color: #A6A6A6; font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        
        <form id="editUserForm" method="POST">
            <div class="form-group">
                <label>Nome Completo</label>
                <input type="text" name="name" id="editName" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="editEmail" class="form-input" required>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label>Saldo Principal</label>
                    <input type="number" name="balance" id="editBalance" class="form-input" step="0.01">
                </div>
                
                <div class="form-group">
                    <label>Saldo Bot</label>
                    <input type="number" name="bot_balance" id="editBotBalance" class="form-input" step="0.01">
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label>Função</label>
                    <select name="role" id="editRole" class="form-select">
                        <option value="user">Usuário</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="editStatus" class="form-select">
                        <option value="active">Ativo</option>
                        <option value="suspended">Suspenso</option>
                        <option value="pending">Pendente</option>
                    </select>
                </div>
            </div>
            
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="hideEditUserModal()" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<script>
function showAddUserModal() {
    document.getElementById('addUserModal').style.display = 'flex';
}

function hideAddUserModal() {
    document.getElementById('addUserModal').style.display = 'none';
}

function editUser(user) {
    document.getElementById('editUserForm').action = '/admin/users/' + user.id + '/update';
    document.getElementById('editName').value = user.name;
    document.getElementById('editEmail').value = user.email;
    document.getElementById('editBalance').value = user.balance;
    document.getElementById('editBotBalance').value = user.bot_balance;
    document.getElementById('editRole').value = user.role;
    document.getElementById('editStatus').value = user.status;
    document.getElementById('editUserModal').style.display = 'flex';
}

function hideEditUserModal() {
    document.getElementById('editUserModal').style.display = 'none';
}

function deleteUser(userId, userName) {
    if (confirm('Tem certeza que deseja excluir o usuário ' + userName + '? Esta ação não pode ser desfeita.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/users/' + userId + '/delete';
        document.body.appendChild(form);
        form.submit();
    }
}

// Show success/error messages
<?php if (isset($_GET['success'])): ?>
    window.cryptoApp?.showNotification('Operação realizada com sucesso!', 'success');
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    window.cryptoApp?.showNotification('<?php echo htmlspecialchars($_GET['error']); ?>', 'danger');
<?php endif; ?>
</script>