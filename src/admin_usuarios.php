<?php
include 'includes/header.php';

if (!isset($_SESSION['es_admin']) || !$_SESSION['es_admin']) {
    header("Location: index.php");
    exit();
}

$usuarios = $conexion->query("SELECT id, username, email, es_admin FROM usuarios ORDER BY id DESC")->fetchAll();
?>

<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?= $_SESSION['mensaje_tipo'] ?? 'info' ?> alert-dismissible fade show mb-4" role="alert">
        <?= $_SESSION['mensaje'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php
    unset($_SESSION['mensaje']);
    unset($_SESSION['mensaje_tipo']);
?>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-5">
    <h2 class="fw-black m-0">GESTIÓN DE USUARIOS</h2>
    <span class="badge bg-dark px-3">
        <?= count($usuarios) ?> registrados
    </span>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr class="small fw-bold text-uppercase">
                <th class="px-4 py-3">ID</th>
                <th class="py-3">Usuario</th>
                <th class="py-3">Email</th>
                <th class="text-center py-3">Administrador</th>
                <th class="text-end px-4 py-3">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td class="px-4 text-muted small">#U-<?= $u['id'] ?></td>
                    <td><span class="fw-bold"><?= htmlspecialchars($u['username']) ?></span></td>
                    <td class="text-muted"><?= htmlspecialchars($u['email'] ?: 'No registrado') ?></td>
                    <td class="text-center">
                        <?php if ($u['es_admin']): ?>
                            <span class="badge bg-primary px-3">ADMIN</span>
                        <?php else: ?>
                            <span class="badge bg-light text-dark border px-3">ESTÁNDAR</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end px-4">
                        <div class="btn-group">
                            <a href="admin_logs.php?usuario=<?= urlencode($u['username']) ?>"
                                class="btn btn-sm btn-outline-dark" title="Ver Logs">📜</a>
                            <a href="admin_usuario_editar.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-dark"
                                title="Editar">✏️</a>
                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                <a href="actions/admin_user_delete.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.')"
                                    title="Eliminar">🗑️</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="mt-4">
    <a href="admin_productos.php" class="text-muted small text-decoration-none">← Ir a gestión de productos</a>
</div>

<?php include 'includes/footer.php'; ?>