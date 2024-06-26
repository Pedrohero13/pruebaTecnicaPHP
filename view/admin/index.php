<?php
session_start();

if (!isset($_SESSION['cargo']) || $_SESSION['cargo'] != 1) {

  header('location: ../../index.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Prueba tecnica</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <style>
    #mapa {
      height: 720px;
      width: 100%;
    }

    #search-bar {
      width: 200px;
      margin-bottom: 10px;
    }
  </style>

  <script src="https://maps.googleapis.com/maps/api/js"></script>

  <nav class="navbar navbar-expand-lg bg-body-tertiary fixed-top">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Posiciones</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">Inicio</a>
          </li>

          <li class="nav-item dropdown active">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Perfil
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item disabled" aria-disabled="true"><?php echo $_SESSION['nombre']; ?></a></li>
              <li><a class="dropdown-item disabled" aria-disabled="true"><?php
              if (!isset($_SESSION['cargo']) || $_SESSION['cargo'] == 1) {

                echo "Administrador";
              } else {
                echo "Agente";
              }
              ?></a></li>


              <li><a class="dropdown-item active" href="../../controller/cerrarSesion.php">Cerrar sesion</a></li>
            </ul>
          </li>
          <?php
          if (!isset($_SESSION['cargo']) || $_SESSION['cargo'] == 1) {

          }
          ?>
          <li class="nav-item">
            <a class="nav-link active " aria-current="page" href="../../registro.php">Usuarios</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</head>

<body>
  <div class="row" style="margin-top: 4em;">
    <div class="col">
      <h2>Tabla con los datos</h2>
      <input type="text" id="search-bar" placeholder="Buscar">
      <table class="table" id="data-table">
        <thead>
          <tr>
            <th scope="col">eco</th>
            <th scope="col">lat</th>
            <th scope="col">lng</th>
            <th scope="col">state</th>
            <th scope="col">country</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $datos = [
            "user" => "csm",
            "password" => "exam1csm",
          ];

          $ch = curl_init();

          curl_setopt($ch, CURLOPT_URL, "http://104.154.142.250/apis/exam/auth");
          curl_setopt($ch, CURLOPT_POST, true);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

          $datos_consulta = http_build_query($datos);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $datos_consulta);

          $respuesta = curl_exec($ch);
          $json = json_decode($respuesta, true);
          $user = $json["data"];
          $tokenBearer = $user["jwt"];
          curl_close($ch);

          $url = "http://104.154.142.250/apis/exam/positions";

          $curl = curl_init();
          curl_setopt($curl, CURLOPT_URL, $url);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

          $headers = [
            "Authorization: Bearer $tokenBearer",
          ];

          curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

          $respuesta = curl_exec($curl);
          $json = json_decode($respuesta, true);
          $data = $json["data"];
          foreach ($data as $dato) {
            echo "<tr>";
            echo "<td>" . $dato['eco'] . "</td>";
            echo "<td>" . $dato['lat'] . "</td>";
            echo "<td>" . $dato['lng'] . "</td>";
            echo "<td>" . $dato['state'] . "</td>";
            echo "<td>" . $dato['country'] . "</td>";
            echo "</tr>";
          }
          ?>

        </tbody>
      </table>
    </div>
    <script>

      function initMap() {

        var latitud = 18.86994180794766;
        var longitud = -97.04532423388906;
        var markers = [];

        var opcionesMapa = {
          center: { lat: latitud, lng: longitud },
          zoom: 10
        };

        // Crear el mapa y asociarlo al elemento "mapa"
        var map = new google.maps.Map(document.getElementById('mapa'), opcionesMapa);
        <?php foreach ($data as $marker): ?>
          var marker = new google.maps.Marker({
            position: { lat: <?php echo $marker['lat']; ?>, lng: <?php echo $marker['lng']; ?> },
            title: '<?php echo $marker['state']; ?>',
            map: map
          });



          var content = '<p> State: <?php echo $marker['state']; ?></p> <br> <p> Country: <?php echo $marker['country']; ?></p> <br> <p> Lat: <?php echo $marker['lat']; ?></p> <br> <p> lng: <?php echo $marker['lng']; ?></p>';
          (function (marker) {
            google.maps.event.addListener(marker, 'click', function () {

              infowindow = new google.maps.InfoWindow({
                content: '<p> State: <?php echo $marker['state']; ?></p> <br> <p> Country: <?php echo $marker['country']; ?></p>'
              });

              infowindow.open(map, marker);
            });
          })(marker);


          markers.push(marker);
        <?php endforeach; ?>
      }
    </script>
    <div class="col">
      <h2>Mapa con marcadores</h2>
      <div id="mapa"></div>
    </div>
  </div>
  <div class="row" style="margin-top: 2em;">
    <div class="col">
      <?php
      $conteoEstados = [];

      foreach ($data as $objeto) {
        $state = $objeto["state"];

        if (!isset($conteoEstados[$state])) {
          $conteoEstados[$state] = 0;
        }

        $conteoEstados[$state]++;
      }
      asort($conteoEstados);

      ?>
      <h2>Estados ordenados</h2>
      <canvas id="myChart"></canvas>

    </div>
    <div class="col">
        <?php
        $conteoEstadosDos = [];

        foreach ($data as $objeto) {
          $state = $objeto["country"];

          if (!isset($conteoEstadosDos[$state])) {
            $conteoEstadosDos[$state] = 0;
          }

          $conteoEstadosDos[$state]++;
        }
        asort($conteoEstadosDos);

        ?>
        <h2>Ciudades ordenadas</h2>
        <canvas id="myChartdos"></canvas>
      </div>
  </div>
  <script>
    $("#search-bar").keyup(function () {
      var searchTerm = $(this).val().toLowerCase();
      var tableRows = $("#data-table tbody tr");

      tableRows.each(function () {
        var rowText = $(this).text().toLowerCase();

        if (rowText.indexOf(searchTerm) === -1) {
          $(this).hide();
        } else {
          $(this).show();
        }
      });
    });
  </script>
  <script>
    initMap();
  </script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
    // Obtener el contexto del elemento canvas
    const ctx = document.getElementById('myChart').getContext('2d');

    // Crear una nueva instancia de la gráfica de barras
    const chart = new Chart(ctx, {
      type: 'bar', // Tipo de gráfica (barras, líneas, etc.)
      data: {
        labels: <?php echo json_encode(array_keys($conteoEstados)); ?>, // Etiquetas de los ejes X (nombres de estados)
        datasets: [{
          label: 'Número de Estados',
          data: <?php echo json_encode($conteoEstados); ?>, // Datos para las barras (número de estados)
          backgroundColor: 'rgba(54, 162, 235, 0.2)', // Color de las barras
          borderColor: 'rgba(54, 162, 235, 1)', // Color del borde de las barras
          borderWidth: 1 // Ancho del borde de las barras
        }]
      },
      options: {
        // Opciones de la gráfica (títulos, ejes, leyendas, etc.)
        scales: {
          yAxes: [{
            ticks: {
              beginAtZero: true // Iniciar el eje Y en cero
            }
          }]
        }
      }
    });

    const ctxdos = document.getElementById('myChartdos').getContext('2d');
    const chartdos = new Chart(ctxdos, {
      type: 'line', // Tipo de gráfica (barras, líneas, etc.)
      data: {
        labels: <?php echo json_encode(array_keys($conteoEstadosDos)); ?>, // Etiquetas de los ejes X (nombres de estados)
        datasets: [{
          label: 'Número de Estados',
          data: <?php echo json_encode($conteoEstadosDos); ?>, // Datos para las barras (número de estados)
          backgroundColor: 'rgba(54, 162, 235, 0.2)', // Color de las barras
          borderColor: 'rgba(54, 162, 235, 1)', // Color del borde de las barras
          borderWidth: 1 // Ancho del borde de las barras
        }]
      },
      options: {
        // Opciones de la gráfica (títulos, ejes, leyendas, etc.)
        scales: {
          yAxes: [{
            ticks: {
              beginAtZero: true // Iniciar el eje Y en cero
            }
          }]
        }
      }
    });
  </script>
</body>

</html>