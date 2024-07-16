<html>
<head>
    <title>Formulario de AUTOEVALUACIÓN</title>
</head>
<body>
    <?php
    if (!isset($_POST['step'])) {
        // Primer paso: solicitar nombre y apellido, y correo electrónico
        echo '<form action="" method="post">';
        echo '<h2>1. Datos Personales</h2>';
        echo '<label for="nombre">Nombre y Apellidos:</label>';
        echo '<input type="text" name="NombreyApellidos" required>';
        echo '<br><br>';
        echo '<label for="email">Correo Electrónico:</label>';
        echo '<input type="email" name="email" required>';
        echo '<br><br>';
        echo  '<input type="checkbox" id="privacy-policy-checkbox" required>';
        echo  '<label for="privacy-policy-checkbox">Acepto la <a href="politica de privacidad">política de privacidad</a></label>';
        echo '<br><br>';
        echo '<input type="hidden" name="step" value="2">';
        echo '<input type="submit" value="Siguiente">';
        echo '</form>';
    } elseif ($_POST['step'] == 2) {
        // Segundo paso: mostrar una pregunta con respuesta Sí o No
        echo '<form action="" method="post">';
        echo '<h2>Paso 2: ¿Dispones de formación académica?</h2>';
        echo '<input type="radio" name="autoevaluacion" value="true" required> Sí';
        echo '<input type="radio" name="autoevaluacion" value="false" required> No';
        echo '<br><br>';
        echo '<input type="hidden" name="step" value="3">';
        echo '<input type="submit" value="Siguiente">';
        echo '</form>';
} elseif ($_POST['step'] == 3) {
    // Tercer paso: si la respuesta es No, terminar aquí
    if ($_POST['autoevaluacion'] == "false") {
        echo '<h2>Gracias por realizar tu Autoevaluación. Lo sentimos, actualmente no existe ninguna certificación compatible con tu perfil.

Permanece atento a las próximas certificaciones que ofreceremos.</h2>';
        exit;
    }
    // Tercer paso (continuación): si la respuesta es Sí, mostrar los tipos de titulaciones
    echo '<form action="" method="post">';
    echo '<h4>3. Titulación académica</h4>';
    echo '<br>';
    echo '<p>¿Qué tipo de titulación?<p>';
    echo '<br>';
    echo '<select name="titulo">';
    echo '<option value="edificacion">Título universitario en edificación (arquitectura, arquitectura técnica, ingeniería industrial o similar)</option>';
    echo '<option value="construccion">Título universitario en construcción (ingeniería civil o similar)</option>';
    echo '<option value="gestion">Título relacionado con la gestión (derecho, económicas, empresariales o similar)</option>';
    echo '<option value="otros">Otros</option>';
    echo '</select>';
    echo '<input type="hidden" name="step" value="4">';
    echo '<input type="submit" value="Siguiente">';
    echo '</form>';
} elseif ($_POST['step'] == 4) {

        // Tercer paso
        $client = new SoapClient("servicioWSDL");
        $result = $client->getTramos(array("idArea" => $_POST["area"]));
        $tramos = $result->getTramosResult->acpCategoria;
        echo '<form action="" method="post">';
        echo '<h2>4. Experiencia profesional</h2>';
    echo '<br>';
    echo '<p>¿Cuántos años de experiencia sobre tu titulación tienes?<p>';
    echo '<br>';
        echo '<select name="tramo">';
        foreach ($tramos as $tramo) {
            echo "<option value='" . $tramo->ID . "'>" . $tramo->Titulo . " Desde " . $tramo->MesesDesde . " meses hasta " . $tramo->MesesHasta . " meses</option>";
        }
        echo '</select>';
        echo '<input type="hidden" name="area" value="' . $_POST["area"] . '">';
        echo '<input type="hidden" name="step" value="5">';
        echo '<input type="submit" value="Siguiente">';
        echo '</form>';
    } elseif ($_POST['step'] == 5) {
        // Quinto paso: ambito
        $client = new SoapClient('servicioWSDL');
        $response = $client->getAreas();
        $areas = $response->getAreasResult->acpArea;
        echo '<form action="" method="post">';
        echo '<h4>5. Ámbito Profesinal</h4>';
        echo '<br>';
		echo '<p>¿En qué ámbito has desarrollado tu experiencia profesional?</p>';
        echo '<br>';
        echo '<select name="area">';
        foreach ($areas as $area) {
            echo '<option value="' . $area->ID . '">' . $area->Titulo . '</option>';
        }
        echo '</select>';
        echo '<input type="hidden" name="step" value="6">';
        echo '<input type="hidden" name="tramo" value="' . $_POST["tramo"] . '">'; // Agrega este campo oculto
        echo '<input type="hidden" name="titulo" value="' . $_POST["titulo"] . '">'; // Agrega este campo oculto
        echo '<input type="submit" value="Siguiente">';
        echo '</form>';
} elseif ($_POST['step'] == 6) {
        // Sexto Paso
        //Llamada a getCertificacionesActuales
        $client = new SoapClient("servicioWSDL");
        $params = array(
            "Area" => $_POST["area"],
            "Experiencia" => $_POST["tramo"]
        );
        $response = $client->__soapCall("getCertificacionesActuales", array($params));
        $resultados = array($response->getCertificacionesActualesResult->acpProductoReturn);
        usort($resultados, function($a, $b) {
            return $a->Id - $b->Id;
        });

        $tableCounter = 1;
        foreach ($resultados as $resultado) {
            echo '<table id="tabla-resultados-' . $tableCounter . '">'; // Abre tabla
            echo '<tr><th>Título</th><th>Abreviatura</th><th>Descripción</th><th>Categoria</th><th>Perfil</th></tr>'; // Encabezado
            echo '<tr>';
            //echo "Id: " . $resultado->Id . "<br>";
            echo "<td>" . $resultado->Titulo . "</td>";
            echo "<td>" . $resultado->Abreviatura . "</td>";
            echo "<td>" . $resultado->Descripcion . "</td>";
            echo "<td>" . $resultado->Categoria . "</td>";
            echo "<td>" . $resultado->Perfil . "</td>";
            echo '</tr>';
            echo '</table>'; // Cierra tabla
            $tableCounter++;
        }
        $client = new SoapClient("servicioWSDL");
        $params = array(
        "NombreyApellidos" => $_POST["NombreyApellidos"],
        "Email" => $_POST["email"],
        "FormacionAcademica" => "autoevaluacion",
        "Titulacion" => $_POST["area"],
        "Experiencia" => $_POST["tramo"],
        "Ambito" => $_POST["ambito"],
        );
        $response = $client->__soapCall("setAutoevaluacion", array($params));
        if ($response == 1) {
        echo "Gracias por completar la autoevaluación.";
        echo "<a href='web' target='_blank'>  Descubre todas las certificaciones Actuales</a>";
        }
    }
?>
</body>
</html>
