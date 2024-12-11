<?php

// Database connection settings
$servername = "127.0.0.1";
$username = "root";
$password = "oE0aIbr]x1(.LH1o";
$dbname = "mysql";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Output buffering
ob_start();

// Set content type header
header('Content-Type: application/json');

function listarRegistros() {
    global $conn;
    $sql = "SELECT * FROM mi_tabla ORDER BY id DESC";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->execute();
        $result = $stmt->get_result();
        
        echo "<table class='table table-striped' id='registrosTabla'>";
        echo "<thead><tr><th>ID</th><th>Campo1</th><th>Campo2</th><th>Campo3</th><th>Campo4</th><th>Campo5</th><th>Acciones</th></tr></thead>";
        echo "<tbody>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['campo1']) . "</td>";
            echo "<td>" . htmlspecialchars($row['campo2']) . "</td>";
            echo "<td>" . htmlspecialchars($row['campo3']) . "</td>";
            echo "<td>" . htmlspecialchars($row['campo4']) . "</td>";
            echo "<td>" . htmlspecialchars($row['campo5']) . "</td>";
            echo "<td>
                <button type='button' onclick=\"editarRegistro(" . $row['id'] . ")\" class='btn btn-primary'>Editar</button>
                <button type='button' onclick=\"eliminarRegistro(" . $row['id'] . ")\" class='btn btn-danger'>Eliminar</button>
              </td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        
        $stmt->close();
    } else {
        echo "Error al ejecutar la consulta: " . $conn->error;
    }
}

function guardarRegistro($campo1, $campo2, $campo3, $campo4, $campo5) {
    global $conn;
    $sql = "INSERT INTO mi_tabla (campo1, campo2, campo3, campo4, campo5) VALUES (?, ?, ?, ?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssss", $campo1, $campo2, $campo3, $campo4, $campo5);
        if ($stmt->execute()) {
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('error' => "Error al guardar el registro: " . $stmt->error));
        }
        $stmt->close();
    } else {
        echo json_encode(array('error' => "Error al preparar la consulta: " . $conn->error));
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_GET['action'])) {
    guardarRegistro($_POST['campo1'], $_POST['campo2'], $_POST['campo3'], $_POST['campo4'], $_POST['campo5']);
} elseif (isset($_GET['action']) && $_GET['action'] == 'listar') {
    listarRegistros();
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && $_GET['action'] == 'eliminar') {
    eliminarRegistro($_POST['id']);
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET['action'] == 'obtener') {
    obtenerRegistro($_GET['id']);
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && $_GET['action'] == 'editar') {
    editarRegistro($_POST);
}

function eliminarRegistro($id) {
    global $conn;
    $sql = "DELETE FROM mi_tabla WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('error' => "Error al eliminar el registro: " . $stmt->error));
        }
        $stmt->close();
    } else {
        echo json_encode(array('error' => "Error al preparar la consulta: " . $conn->error));
    }
}

function obtenerRegistro($id) {
    global $conn;
    $sql = "SELECT * FROM mi_tabla WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row) {
            echo json_encode($row);
        } else {
            echo json_encode(array('error' => "Registro no encontrado"));
        }
        $stmt->close();
    } else {
        echo json_encode(array('error' => "Error al preparar la consulta: " . $conn->error));
    }
}

function editarRegistro($data) {
    global $conn;
    $id = $data['id'];
    $campos = [];

    if (!empty($data['campo1'])) {
        $campos[] = "campo1 = '". htmlspecialchars($data['campo1']) . "'";
    }
    if (!empty($data['campo2'])) {
        $campos[] = "campo2 = '". htmlspecialchars($data['campo2']) . "'";
    }
    if (!empty($data['campo3'])) {
        $campos[] = "campo3 = '". htmlspecialchars($data['campo3']) . "'";
    }
    if (!empty($data['campo4'])) {
        $campos[] = "campo4 = '". htmlspecialchars($data['campo4']) . "'";
    }
    if (!empty($data['campo5'])) {
        $campos[] = "campo5 = '". htmlspecialchars($data['campo5']) . "'";
    }

    if (count($campos) > 0) {
        $sql = "UPDATE mi_tabla SET " . implode(", ", $campos) . " WHERE id = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo json_encode(array('success' => true, 'clearFields' => true));
            } else {
                echo json_encode(array('error' => "Error al actualizar el registro: " . $stmt->error));
            }
            $stmt->close();
        } else {
            echo json_encode(array('error' => "Error al preparar la consulta: " . $conn->error));
        }
    } else {
        echo json_encode(array('error' => "No se proporcionaron datos para actualizar"));
    }
}

$conn->close();
?>
