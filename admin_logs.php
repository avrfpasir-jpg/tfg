<?php
include 'includes/header.php';
include 'includes/seguridad.php';

if (!isset($_SESSION['es_admin']) || !$_SESSION['es_admin']) {
    header("Location: index.php");
    exit();
}

$user_filter = $_GET['usuario'] ?? null;
$event_filter = $_GET['evento'] ?? null;

// Obtener tipos de eventos únicos para el filtro
$tipos_evento = $conexion->query("SELECT DISTINCT evento FROM logs_seguridad ORDER BY evento ASC")->fetchAll(PDO::FETCH_COLUMN);

// Construir consulta dinámica
$sql = "SELECT * FROM logs_seguridad WHERE 1=1";
$params = [];

if ($user_filter) {
    $sql .= " AND detalles LIKE ?";
    $params[] = "%$user_filter%";
}

if ($event_filter) {
    $sql .= " AND evento = ?";
    $params[] = $event_filter;
}

$sql .= " ORDER BY fecha DESC LIMIT 100";
$stmt = $conexion->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-5">
    <h2 class="fw-black m-0">LOGS DE SEGURIDAD</h2>
    <div class="d-flex align-items-center gap-2">
        <?php if ($user_filter || $event_filter): ?>
            <a href="admin_logs.php" class="btn btn-sm btn-outline-danger me-2">Limpiar Filtros</a>
        <?php endif; ?>

        <form method="GET" class="d-flex gap-2 align-items-center">
            <?php if ($user_filter): ?>
                <input type="hidden" name="usuario" value="<?= htmlspecialchars($user_filter) ?>">
                <span class="badge bg-primary px-3">Usuario: <?= htmlspecialchars($user_filter) ?></span>
            <?php endif; ?>

            <select name="evento" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Filtrar por Evento --</option>
                <?php foreach ($tipos_evento as $t): ?>
                    <option value="<?= $t ?>" <?= $event_filter === $t ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if (!$user_filter && !$event_filter): ?>
            <span class="badge bg-dark px-3">Últimas 100 alertas</span>
        <?php endif; ?>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr class="small fw-bold text-uppercase">
                <th class="px-4 py-3">Fecha</th>
                <th class="py-3">Evento</th>
                <th class="py-3 w-50">Detalles</th>
                <th class="text-center px-4 py-3">Riesgo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log):
                $riesgo_class = 'bg-success';
                if ($log['evento'] === 'NUEVO_PEDIDO')
                    $riesgo_class = 'bg-info text-dark';
                if ($log['nivel_riesgo'] >= 3)
                    $riesgo_class = 'bg-warning text-dark';
                if ($log['nivel_riesgo'] >= 5)
                    $riesgo_class = 'bg-danger';
                ?>
                <tr>
                    <td class="px-4 text-muted small">
                        <?= $log['fecha'] ?>
                    </td>
                    <td><span class="fw-bold">
                            <?= htmlspecialchars($log['evento']) ?>
                        </span></td>
                    <td class="small">
                        <?= htmlspecialchars($log['detalles']) ?>
                    </td>
                    <td class="text-center px-4">
                        <span class="badge <?= $riesgo_class ?> px-3">
                            Nivel
                            <?= $log['nivel_riesgo'] ?>
                        </span>
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