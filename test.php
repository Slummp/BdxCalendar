<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Mon super emploi du temps</title>

        <style type="text/css">
            caption /* Titre du tableau */
            {
                margin: auto; /* Centre le titre du tableau */
                font-family: Arial, Times, "Times New Roman", serif;
                font-weight: bold;
                font-size: 1.2em;
                color: #009900;
                margin-bottom: 20px; /* Pour éviter que le titre ne soit trop collé au tableau en-dessous */
            }

            table /* Le tableau en lui-même */
            {
                margin: auto; /* Centre le tableau */
                border: 4px outset green; /* Bordure du tableau avec effet 3D (outset) */
                border-collapse: collapse; /* Colle les bordures entre elles */
                width:100%;
            }

            th /* Les cellules d'en-tête */
            {
                background-color: #006600;
                color: white;
                font-size: 1.1em;
                font-family: Arial, "Arial Black", Times, "Times New Roman", serif;
                border:1px solid red;
            }

            td /* Les cellules normales */
            {
                border: 1px solid black;
                font-family: "Comic Sans MS", "Trebuchet MS", Times, "Times New Roman", serif;
                text-align: center; /* Tous les textes des cellules seront centrés*/
                padding: 5px; /* Petite marge intérieure aux cellules pour éviter que le texte touche les bordures */
            }

            td.time
            {
                width:5%;
            }
        </style>
    </head>

    <body>
        <table>
            <?php
            $jour = array(null, "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche");
            $rdv["Dimanche"]["16:30"] = "Dermatologue";
            $rdv["Lundi"]["9"] = "Mémé -_-";

            echo "<tr><th>Heure</th>";

            for($x = 1; $x <= 5; $x++)
                echo "<th>".$jour[$x]."</th>";

            echo "</tr>";

            for($j = 8; $j < 20; $j += 0.5) {
                echo "<tr>";

                for($i = 0; $i < 5; $i++) {
                    if($i == 0) {
                        $heure = str_replace(".5", ":30", $j);
                        echo "<td class=\"time\">".$heure."</td>";
                    }

                    echo "<td>";

                    if(isset($rdv[$jour[$i+1]][$heure])) {
                        echo $rdv[$jour[$i+1]][$heure];
                    }

                    echo "</td>";
                }

                echo "</tr>";
            }
            ?>
        </table>
    </body>
</html>
