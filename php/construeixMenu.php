
<nav class="navbar navbar-inverse">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="#"><strong>Smart Classroom</strong></a>
        </div>
        <ul class="nav navbar-nav">
            <?php
            //obtenim el perfil del docent
            require '../classes/Databases.php';

            session_start();

            $codiProf = $_SESSION['prof_actual'];

            //establim la connexió
            $conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
            if ($conn->connect_error)
                die($conn->connect_error);

            //triem el charset de la cerca
            mysqli_set_charset($conn, "utf8");

            $query = "select ga40_codi_perfil as perfil from ga40_perfils_usuaris_rel where ga40_codi_usuari=" . $codiProf;

            //executem la consulta
            $result = $conn->query($query);

            $perfil = [];
            $cont = 0;

            if (!$result)
                die($conn->error);

            if ($result->num_rows > 0) {
                // desen els perfils del profe en un array

                while ($row = $result->fetch_assoc()) {
                    $perfil[$cont] = $row['perfil'];
                    $cont++;
                }
            }

            $result->close();
            $conn->close();
            
            $xml = simplexml_load_file("../xml/configuracio/mainMenu.xml") or die("No es pot crear l'objecte");
            foreach ($xml->children() as $menuOption) {

                if (count($menuOption->children()) > 0) {
                    echo '<li class="dropdown">';
                    echo '<a class="dropdown-toggle" data-toggle="dropdown" href="' . $menuOption['referencia'] . '">' . $menuOption['caption'];
                    echo '<span class="caret"></span></a>';
                    echo '<ul class="dropdown-menu">';
                } else {

                    $perfils = explode('-', $menuOption['permis']);
                    //mirem si algun dels perfils està permès
                    $desabilitat = 'class="disabled"';

                    for ($i = 0; $i < count($perfil); $i++) {

                        if (array_search($perfil[$i], $perfils) !== false) {
                            //si trobem un perfil habilitat sortim
                            $desabilitat = '';
                            break;
                        }
                    }
                    if(isset($menuOption['nova'])){
                        $novaFinestra='target="_blank"';
                        
                    }else{
                        $novaFinestra='';
                    }
                    echo '<li><a href="' . $menuOption['referencia'] . '" ' . $desabilitat . ' '.$novaFinestra.'>' . $menuOption['caption'] . '</a></li>';
                }
                foreach ($menuOption->children() as $menuItem) {
                    $subMenu = false;
                    $i = 0;
                    foreach ($menuItem->children() as $subMenuItem) {
                        $subMenu = true;
                        if ($i === 0) {
                            //posem la capçalera del submenu
                            echo '<li class="dropdown-submenu">';
                            echo '<a class="test" tabindex="-1" href="#">' . $menuItem['caption'] . '<span class="caret"></span></a>';
                            echo '<ul class="dropdown-menu">';
                        }

                        //mirem si cal desabilitar

                        $perfils = explode('-', $subMenuItem['permis']);
                        $desabilitat = 'class="disabled"';

                        for ($i = 0; $i < count($perfil); $i++) {

                            if (array_search($perfil[$i], $perfils) !== false) {

                                //si trobem un perfil habilitat sortim
                                $desabilitat = '';
                                break;
                            }
                        }

                        echo '<li><a href="' . $subMenuItem['referencia'] . '" ' . $desabilitat . '>' . $subMenuItem['caption'] . '</a></li>';
                        $i++;
                    }
                    if ($subMenu === false) {

                        //mirem si cal desabilitar

                        $perfils = explode('-', $menuItem['permis']);
                        $desabilitat = 'class="disabled"';

                        for ($i = 0; $i < count($perfil); $i++) {

                            if (array_search($perfil[$i], $perfils) !== false) {

                                //si trobem un perfil habilitat sortim
                                $desabilitat = '';
                                break;
                            }
                        }

                        echo '<li><a href="' . $menuItem['referencia'] . '" ' . $desabilitat . '>' . $menuItem['caption'] . '</a></li>';
                    } else {
                        echo '</ul>';
                        echo '</li>';
                    }
                }
                if (count($menuOption->children()) > 0) {
                    echo '</ul>';
                }
            }
            ?>


        </ul>
        <ul class="nav navbar-nav navbar-right">

            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">Compte
                    <span class="caret"></span></a> 

                <ul class="dropdown-menu">
                    <li><a href="#" onclick="logout()"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                    <li><a href="canviPassword" onclick="" data-toggle="modal" data-target="#canviaPassword"><span class="glyphicon glyphicon-user"></span> Canvia Contrasenya</a></li>
                </ul>
            </li>
        </ul>

    </div>
</nav>

<script>
    $(document).ready(function () {

        $('.dropdown-submenu a.test').on("click", function (e) {
            $(this).next('ul').toggle();
            e.stopPropagation();
            e.preventDefault();
        });

        $('.disabled').click(function (e) {
            e.preventDefault();
        });

    });


</script>


