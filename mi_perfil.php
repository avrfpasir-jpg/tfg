<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $bio = $_POST['bio'];
    $ciudad = $_POST['ciudad'];

    $stmt_up = $conexion->prepare("UPDATE usuarios SET email = ?, bio = ?, ciudad = ? WHERE id = ?");
    if ($stmt_up->execute([$email, $bio, $ciudad, $user_id])) {
        $success = "SYSTEM_UPDATE: SUCCESSFUL";
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
    } else {
        $error = "SYSTEM_UPDATE: FAILED";
    }
}

$pageTitle = "IDENTITY_CONFIG";
include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="bg-white border border-4 border-dark p-5 shadow-lg position-relative">
            <div class="position-absolute top-0 start-0 w-100 p-2 bg-dark text-white fw-bold small text-uppercase mb-4">
                [ CONFIG_IDENTITY_0.1 ]
            </div>

            <h4 class="fw-black mb-5 mt-4 text-uppercase">Modificar Metadatos</h4>

            <?php if ($success): ?>
                <div class="bg-success text-white p-3 mb-4 fw-bold">[OK] <?= $success ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="bg-danger text-white p-3 mb-4 fw-bold">[ERR] <?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-4">
                    <label class="small fw-bold mb-2 text-uppercase">Email_Contact //</label>
                    <input type="email" name="email" class="form-control"
                        value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="mb-4">
                    <label class="small fw-bold mb-2 text-uppercase">Manifesto_Bio //</label>
                    <textarea name="bio" class="form-control" rows="5"
                        placeholder="Define tu visión estética aquí..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="small fw-bold mb-2 text-uppercase">Base_Operations //</label>
                    <input type="text" name="ciudad" class="form-control" placeholder="Ciudad, País..."
                        value="<?= htmlspecialchars($user['ciudad'] ?? '') ?>">
                </div>

                <div class="pt-4 border-top border-dark mt-5">
                    <button type="submit" class="btn btn-primary w-100 py-3">SAVE_CHANGES</button>
                    <a href="index.php" class="btn btn-dark w-100 mt-3 py-2">CANCEL_PROC</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>