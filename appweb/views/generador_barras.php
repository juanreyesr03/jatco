<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generador de Códigos de Barras</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
        }
        input {
            padding: 10px;
            margin: 10px;
            font-size: 16px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
        svg {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <h2>Generador de Códigos de Barras</h2>
    <input type="text" id="codigo" placeholder="Introduce el código">
    <button onclick="generarCodigo()">Generar</button>

    <svg id="barcode"></svg>

    <script>
        function generarCodigo() {
            let codigo = document.getElementById("codigo").value;
            if (codigo.trim() === "") {
                alert("Introduce un código válido");
                return;
            }
            JsBarcode("#barcode", codigo, {
                format: "CODE128",
                lineColor: "#000",
                width: 2,
                height: 50,
                displayValue: true
            });
        }
    </script>

</body>
</html>
