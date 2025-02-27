<?php
    session_start();
    if (empty($_SESSION['active'])) {
        echo "<link href='../css/sb-admin-2.css' rel='stylesheet'>";
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Debes iniciar sesión para acceder a esta página.',
                        confirmButtonText: 'Iniciar Sesión'
                    }).then(() => {
                        window.location.href = '../index.php';
                    });
                });
            </script>";
        $conn->close();
        exit();
    }
?>
