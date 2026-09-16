<?php
require_once "config.php";

$mensaje = "";
$tipo = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST["accion"] ?? "";

    if ($accion === "crear") {
        $destinatario = trim($_POST["destinatario"] ?? "");
        $direccion = trim($_POST["direccion"] ?? "");
        $descripcion = trim($_POST["descripcion"] ?? "");

        if ($destinatario === "" || $direccion === "" || $descripcion === "") {
            $mensaje = "Completa todos los campos.";
            $tipo = "error";
        } else {
            $stmt = $conn->prepare("INSERT INTO envios (destinatario, direccion, descripcion) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $destinatario, $direccion, $descripcion);
            if ($stmt->execute()) {
                $mensaje = "Envío registrado correctamente.";
                $tipo = "success";
            } else {
                $mensaje = "No se pudo registrar el envío.";
                $tipo = "error";
            }
            $stmt->close();
        }
    }

    if ($accion === "actualizar") {
        $id = (int)($_POST["id"] ?? 0);
        $destinatario = trim($_POST["destinatario"] ?? "");
        $direccion = trim($_POST["direccion"] ?? "");
        $descripcion = trim($_POST["descripcion"] ?? "");

        if ($id <= 0 || $destinatario === "" || $direccion === "" || $descripcion === "") {
            $mensaje = "Datos inválidos. Completa todos los campos.";
            $tipo = "error";
        } else {
            $stmt = $conn->prepare("UPDATE envios SET destinatario=?, direccion=?, descripcion=? WHERE id=?");
            $stmt->bind_param("sssi", $destinatario, $direccion, $descripcion, $id);
            if ($stmt->execute()) {
                $mensaje = "Envío actualizado correctamente.";
                $tipo = "success";
            } else {
                $mensaje = "No se pudo actualizar el envío.";
                $tipo = "error";
            }
            $stmt->close();
        }
    }

    if ($accion === "eliminar") {
        $id = (int)($_POST["id"] ?? 0);
        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM envios WHERE id=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $mensaje = "Envío eliminado correctamente.";
                $tipo = "success";
            } else {
                $mensaje = "No se pudo eliminar el envío.";
                $tipo = "error";
            }
            $stmt->close();
        }
    }
}

$editar = null;
if (isset($_GET["editar"])) {
    $id = (int)$_GET["editar"];
    $stmt = $conn->prepare("SELECT id, destinatario, direccion, descripcion FROM envios WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $editar = $resultado->fetch_assoc();
    $stmt->close();
}

$buscar = trim($_GET["buscar"] ?? "");
if ($buscar !== "") {
    $like = "%" . $buscar . "%";
    $stmt = $conn->prepare("SELECT * FROM envios WHERE destinatario LIKE ? OR direccion LIKE ? OR descripcion LIKE ? ORDER BY id DESC");
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $envios = $stmt->get_result();
} else {
    $envios = $conn->query("SELECT * FROM envios ORDER BY id DESC");
}

$total = $conn->query("SELECT COUNT(*) AS total FROM envios")->fetch_assoc()["total"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EnvíaPro | Gestión de Envíos</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="hero">
    <nav class="navbar">
        <div class="brand"><span class="brand-icon">✦</span> Envía<span>Pro</span></div>
        <a href="#nuevo" class="nav-btn">+ Nuevo envío</a>
    </nav>
    <div class="hero-content">
        <div>
            <p class="eyebrow">GESTIÓN SIMPLE · RÁPIDA · PROFESIONAL</p>
            <h1>Controla tus envíos<br><span>en un solo lugar.</span></h1>
            <p class="hero-text">Registra destinatarios, direcciones y descripciones de forma organizada para administrar tus entregas.</p>
            <a class="primary-btn" href="#nuevo">Registrar envío <span>→</span></a>
        </div>
        <div class="hero-card">
            <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=900&q=85" alt="Paquetes listos para envío">
            <div class="hero-card-overlay">
                <strong>Logística organizada</strong>
                <span>Todo bajo control</span>
            </div>
        </div>
    </div>
</header>

<main class="container">
    <?php if ($mensaje): ?>
        <div class="alert <?= htmlspecialchars($tipo) ?>"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <section class="stats">
        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div><small>Total de envíos</small><strong><?= (int)$total ?></strong></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✓</div>
            <div><small>Gestión</small><strong>CRUD completo</strong></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⚡</div>
            <div><small>Base de datos</small><strong>MySQL</strong></div>
        </div>
    </section>

    <section class="form-section" id="nuevo">
        <div class="section-heading">
            <div>
                <p class="eyebrow dark">REGISTRO DE ENVÍOS</p>
                <h2><?= $editar ? "Editar envío" : "Nuevo envío" ?></h2>
            </div>
            <?php if ($editar): ?><a href="index.php" class="cancel-link">Cancelar edición</a><?php endif; ?>
        </div>

        <form method="POST" class="shipment-form">
            <input type="hidden" name="accion" value="<?= $editar ? "actualizar" : "crear" ?>">
            <?php if ($editar): ?><input type="hidden" name="id" value="<?= (int)$editar["id"] ?>"><?php endif; ?>

            <div class="field">
                <label for="destinatario">Destinatario</label>
                <input id="destinatario" name="destinatario" type="text" maxlength="150" required
                       placeholder="Ej. Juan Pérez"
                       value="<?= htmlspecialchars($editar["destinatario"] ?? "") ?>">
            </div>
            <div class="field">
                <label for="direccion">Dirección</label>
                <input id="direccion" name="direccion" type="text" maxlength="255" required
                       placeholder="Ej. Calle 10 # 20-30, Cali"
                       value="<?= htmlspecialchars($editar["direccion"] ?? "") ?>">
            </div>
            <div class="field field-full">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="4" required placeholder="Describe el contenido o las indicaciones del envío..."><?= htmlspecialchars($editar["descripcion"] ?? "") ?></textarea>
            </div>
            <button class="primary-btn submit-btn" type="submit"><?= $editar ? "Guardar cambios" : "Guardar envío" ?> <span>→</span></button>
        </form>
    </section>

    <section class="list-section">
        <div class="list-header">
            <div>
                <p class="eyebrow dark">HISTORIAL</p>
                <h2>Envíos registrados</h2>
            </div>
            <form method="GET" class="search">
                <input name="buscar" value="<?= htmlspecialchars($buscar) ?>" placeholder="Buscar envío...">
                <button type="submit">⌕</button>
            </form>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Destinatario</th>
                        <th>Dirección</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($envios && $envios->num_rows > 0): ?>
                    <?php while ($e = $envios->fetch_assoc()): ?>
                        <tr>
                            <td><span class="id-badge">#<?= (int)$e["id"] ?></span></td>
                            <td><strong><?= htmlspecialchars($e["destinatario"]) ?></strong></td>
                            <td><?= htmlspecialchars($e["direccion"]) ?></td>
                            <td class="description"><?= htmlspecialchars($e["descripcion"]) ?></td>
                            <td><?= date("d/m/Y H:i", strtotime($e["creado_en"])) ?></td>
                            <td class="actions">
                                <a class="edit" href="?editar=<?= (int)$e["id"] ?>#nuevo">Editar</a>
                                <form method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este envío?');">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="id" value="<?= (int)$e["id"] ?>">
                                    <button class="delete" type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="empty">No hay envíos registrados<?= $buscar ? " para esa búsqueda" : "" ?>.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<footer>
    <div class="footer-brand">✦ EnvíaPro</div>
    <p>Sistema de gestión de envíos · PHP + MySQL</p>
</footer>
</body>
</html>