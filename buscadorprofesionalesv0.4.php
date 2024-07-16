<?php 
$client = new SoapClient('serviceWSDL'); 
 
// Obtener la lista de perfiles 
$perfiles = $client->getPerfiles()->getPerfilesResult->acpPerfil; 
// Obtener la lista de categorías 
$categorias = $client->getCategorias()->getCategoriasResult->acpCategoria; 
// Obtener la lista de provincias 
$provincias = $client->getProvincias()->getProvinciasResult->acpProvincia; 
?> 
 <form method="post"> 
        <label for="perfil">Perfil:</label> 
        <select id="perfil" name="perfil"> 
            <?php foreach ($perfiles as $perfil) { ?> 
                <option value="<?php echo $perfil->ID; ?>" <?php echo (isset($_POST['perfil']) && $_POST['perfil'] == $perfil->ID) || !isset($_POST['perfil']) && $perfil === reset($perfiles) ? "selected" : "" ?>><?php echo $perfil->Titulo; ?></option> 
            <?php } ?> 
        </select> 
        <br> 
        <label for="categoria">Categoría:</label> 
        <select id="categoria" name="categoria"> 
            <?php foreach ($categorias as $categoria) { ?> 
                <option value="<?php echo $categoria->ID; ?>" <?php echo (isset($_POST['categoria']) && $_POST['categoria'] == $categoria->ID) || !isset($_POST['categoria']) && $categoria === reset($categorias) ? "selected" : "" ?>><?php echo $categoria->Titulo; ?></option> 
            <?php } ?> 
        </select> 
        <br> 
     <label for="provincia">Provincia:</label> 
    <select id="provincia" name="provincia"> 
        <option value="">Todas</option> 
        <?php foreach ($provincias as $provincia) { ?> 
            <option value="<?php echo $provincia->ID; ?>"><?php echo $provincia->Titulo; ?></option> 
        <?php } ?> 
    </select> 
    <br> 
    <input type="submit" value="Buscar profesionales" onclick="document.getElementById('loading').style.display='block'"> 
</form> 
<div id="loading" style="display: none">Cargando...</div> 
<?php 
if (isset($_POST['perfil']) && isset($_POST['categoria']) && isset($_POST['provincia'])) { 
    // Obtener la lista de profesionales 
    $profesionales = $client->getProfesionales(array( 
        'idPerfil' => $_POST['perfil'], 
        'idCategoria' => $_POST['categoria'], 
        'idProvincia' => $_POST['provincia']
        ))->getProfesionalesResult->acpProfesional; 
    // Mostrar la tabla de resultados 
    if (!empty($profesionales)) { // Hay elementos en el array
        $bodyTabla = '';
        $registrosValidos = 0;
        foreach ($profesionales as $profesional) { 
            if (!empty($profesional->NombreyApellidos) || !empty($profesional->Telefono) || !empty($profesional->Email)) {
                $bodyTabla = $bodyTabla . '<tr>'; 
                $bodyTabla = $bodyTabla . '<td>' . $profesional->NombreyApellidos . '</td>'; 
                $bodyTabla = $bodyTabla . '<td>' . $profesional->Telefono . '</td>'; 
                $bodyTabla = $bodyTabla . '<td>' . $profesional->Email . '</td>'; 
                $bodyTabla = $bodyTabla . '</tr>';
                $registrosValidos = $registrosValidos + 1;
            }
        } 
        if ($registrosValidos > 0) {
            echo '<table>'; 
            echo '<tr>'; 
            echo '<th>Nombre y Apellidos</th>'; 
            echo '<th>Telefono</th>'; 
            echo '<th>Email</th>'; 
            echo '</tr>'; 
            echo $bodyTabla;
            echo '</table>'; 
        } else {
            echo '<div id="notfound">No se han encontrado profesionales con los criterios seleccionados.</div>'; 
        }
    } else { 
        echo '<div id="notfound">No se han encontrado profesionales con los criterios seleccionados.</div>'; 
    } 
} 
?>
