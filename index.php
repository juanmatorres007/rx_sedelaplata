<style>
    body{
        display: flex;
        align-items: center;
        align-content: center;
        justify-content: center;
    }
    .cards {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .cards .red {
        text-decoration: none;
        background-color:  #307750;
    }

    .cards .blue {
        text-decoration: none;
        background-color: #3b82f6;
    }

    .cards .card {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        text-align: center;
        height: 150px;
        width: 500px;
        border-radius: 10px;
        color: white;
        cursor: pointer;
        transition: 400ms;
    }

    .cards .card p.tip {
        font-size: 1em;
        font-weight: 700;
    }

    .cards .card p.second-text {
        font-size: .7em;
    }

    .cards .card:hover {
        transform: scale(1.1, 1.1);
    }

    .cards:hover>.card:not(:hover) {
        filter: blur(10px);
        transform: scale(0.9, 0.9);
    }
</style>
<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Page Title</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='main.css'>
    <script src='main.js'></script>
</head>

<body>
    <div class="cards">
        <img src="img/logorayos-imageonline.co-7110215.bmp" alt=""class="card" >
        <a href="factura.php" class="card red">
            <div>
                <p class="tip">consulta de facturacion</p>
            </div>
        </a>
        <a href="obtenerProcedimiento.php" class="card blue">
            <div>
                <p class="tip">consuta de procedimientos y precios</p>
            </div>
        </a>
    </div>
</body>

</html>