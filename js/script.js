

// Objet Clipboard pour gérer la copie
var clipboard = new Clipboard('#copyButton');

// Gestion des erreurs
document.getElementById("copyButton").onclick = function () {
    // Variables...
    var span;
    var strong;

    // Semestre invalide
    if (document.getElementById("contentModules").innerHTML == "error") {
        span = document.createElement("span");

        strong = document.createElement("strong");
        strong.innerHTML = "Attention !";

        span.appendChild(strong);
        span.appendChild(document.createTextNode(" Veuillez choisir un semestre en cours."));

        erreur('danger', span);
    }

    // Semestre non sélectionné
    else if (document.getElementById("selectSemestre").value == "") {
        span = document.createElement("span");

        strong = document.createElement("strong");
        strong.innerHTML = "Attention !";

        span.appendChild(strong);
        span.appendChild(document.createTextNode(" Vous avez oublié de sélectionner un semestre."));

        erreur('danger', span);
    }

    // Groupe non sélectionné
    else if (document.getElementById("selectGroupe").value == "") {
        span = document.createElement("span");

        strong = document.createElement("strong");
        strong.innerHTML = "Attention !";

        span.appendChild(strong);
        span.appendChild(document.createTextNode(" Vous avez oublié de sélectionner un groupe de TD."));

        erreur('danger', span);
    }

    // Copie...
    else {
        // ...réussie
        clipboard.on('success', function () {
            span = document.createElement("span");

            span.appendChild(document.createTextNode("Url du calendrier copiée dans le presse-papier."));

            erreur('success', span);
        });

        // ...échouée
        clipboard.on('error', function () {
            span = document.createElement("span");

            strong = document.createElement("strong");
            strong.innerHTML = "Attention !";

            span.appendChild(strong);
            span.appendChild(document.createTextNode(" La copie n'a pas fonctionnée, l'URL n'a pas été copiée dans le presse-papier."));

            erreur('warning', span);
        });
    }
};

// Affichage d'un message d'erreur (type = {danger, warning, info, success})
function erreur(type, message) {
    // Nettoyage de la div d'erreurs
    document.getElementById("errors").innerHTML = '';

    var error = document.createElement("div");
    error.setAttribute("class", "alert alert-" + type + " alert-dismissible fade in");
    error.setAttribute("role", "alert");

    var button = document.createElement("button");
    button.setAttribute("class", "close");
    button.setAttribute("type", "button");
    button.setAttribute("data-dismiss", "alert");
    button.setAttribute("aria-label", "Close");

    var span = document.createElement("span");
    span.setAttribute("aria-hidden", "true");
    span.innerHTML = "x";

    //

    document.getElementById("errors").appendChild(error);
    error.appendChild(button);
    button.appendChild(span);
    error.appendChild(message);
}

// Récupération de la liste des modules
$('#selectSemestre').change(function () {
    // Récupération du code du module
    var sem = document.getElementById("selectSemestre").value;

    // Récupération de la liste des modules en AJAX
    $.post("modules.php", {semester: sem}, function (data) {
        // Si le semestre n'est pas valide
        if (data == "error") {
            // Afficher l'erreur correspondante
            var span = document.createElement("span");

            var strong = document.createElement("strong");
            strong.innerHTML = "Attention !";

            span.appendChild(strong);
            span.appendChild(document.createTextNode(" Veuillez choisir un semestre en cours."));

            erreur('danger', span);

            document.getElementById("contentModules").innerHTML = "error";

            document.getElementById("divModules").style.display = 'none';
            document.getElementById("divAnglais").style.display = 'none';
        }

        // Si le semestre est valide
        else {
            // Nettoyage de la div d'erreurs et de la liste des modules
            document.getElementById("errors").innerHTML = '';
            document.getElementById('contentModules').innerHTML = '';

            // Afficahge de la div contenant la liste des modules
            document.getElementById("divModules").style.display = 'block';
            document.getElementById("divAnglais").style.display = 'block';

            // Création du contenu de la liste des modules
            var dataParse = JSON.parse(data);

            for (var key in dataParse) {
                var checkboxDiv = document.createElement("div");
                checkboxDiv.setAttribute("class", "checkbox");

                var checkboxLabel = document.createElement("label");
                checkboxLabel.setAttribute("for", "checkboxes-" + key);

                var checkboxInput = document.createElement("input");
                checkboxInput.setAttribute("id", "checkboxes-" + key);
                checkboxInput.setAttribute("name", "modules[]");
                checkboxInput.setAttribute("type", "checkbox");
                checkboxInput.setAttribute("value", key);

                //

                document.getElementById("contentModules").appendChild(checkboxDiv);
                checkboxDiv.appendChild(checkboxLabel);
                checkboxLabel.appendChild(checkboxInput);
                checkboxLabel.appendChild(document.createTextNode(dataParse[key]));
            }
        }
    });
});

// Génération de l'URL du calendrier en fonction des paramètres du formulaire
document.getElementById("params").addEventListener("change", function () {
    // Récupération de l'input contenant le résultat
    var result = document.getElementById("inputResultat");

    // Base de l'URL
    result.value = window.location.href;
    result.value += "calendar.php";
    result.value += "?";

    // Ajout du paramètre semestre
    result.value += "semester=";
    result.value += document.getElementById("selectSemestre").value;

    // Ajout du paramètre groupe de TD (si précisé)
    if (document.getElementById("selectGroupe").value != "") {
        result.value += "&";

        result.value += "group=";
        result.value += document.getElementById("selectGroupe").value;
    }

    // Ajout du paramètre filtres
    result.value += "&";

    result.value += "filters=";
    var mod = document.getElementsByName('modules[]');
    for (var i = 0; i < mod.length; i++) {
        if (mod[i].checked == 1) {
            result.value += mod[i].value;
            result.value += ",";
        }
    }

    // Suppresion de la virgule de fin inutile
    if (result.value.charAt(result.value.length - 1) != "=") {
        result.value = result.value.substring(0, result.value.length - 1);
    }

    // Suppression du paramètre inutile (parce que vide..)
    else {
        result.value = result.value.substring(0, result.value.length - 9);
    }

    // Choix de la salle d'anglais
    if (document.getElementById("checkboxeAnglais").checked == 1) {
        result.value += "&";

        result.value += "anglais=";
        result.value += document.getElementById("inputAnglais").value;
    }

    // Affichage des sous-groupes spécifiques au S6
    if (document.getElementById("selectSemestre").value == "IN601") {
        // Affichage des sous-groupes de BD si la BD n'est pas filtrée
        if (document.getElementById("checkboxes-J1IN6013").checked == 0) {
            document.getElementById("divGroupeBD").style.display = "block";

            result.value += "&";

            result.value += "groupBD=";
            if (document.getElementById("groupeBD-1").checked == 1)
                result.value += "1";
            if (document.getElementById("groupeBD-2").checked == 1)
                result.value += "2";
        }
        else {
            document.getElementById("divGroupeBD").style.display = "none";
        }

        // Affichage du "groupe 4" d'Algo si l'Algo n'est pas filtré
        if (document.getElementById("checkboxes-J1IN6011").checked == 0) {
            document.getElementById("divGroupeAlgo").style.display = "block";

            result.value += "&";

            result.value += "groupAlgo=";
            if (document.getElementById("groupeAlgo-4").checked == 1)
                result.value += "4";
            if (document.getElementById("groupeAlgo-0").checked == 1)
                result.value += "0";
        }
        else {
            document.getElementById("divGroupeAlgo").style.display = "none";
        }

        // Affichage du "groupe 4" d'analyse syntaxique si l'analyse syntaxique n'est pas filtrée
        if (document.getElementById("checkboxes-J1IN6012").checked == 0) {
            document.getElementById("divGroupeAS").style.display = "block";

            result.value += "&";

            result.value += "groupAS=";
            if (document.getElementById("groupeAS-4").checked == 1)
                result.value += "4";
            if (document.getElementById("groupeAS-0").checked == 1)
                result.value += "0";
        }
        else {
            document.getElementById("divGroupeAS").style.display = "none";
        }
    }
});

// Affichage des sous-groupes spécifiques au S6 et de la liste des groupes
document.getElementById("selectSemestre").addEventListener("change", function (e) {
    // Affichage des sous-groupes spécifiques au S6
    var semestre = e.target[e.target.selectedIndex].value;
    var groupes = document.getElementById("selectGroupe");
    var max = 4;
    if (semestre == "IN601") {
        document.getElementById("divSousGroupes").style.display = "block";
        max = 3;
    } else {
        document.getElementById("divSousGroupes").style.display = "none";
    }

    // Affichage de la liste des groupes
    while (groupes.firstChild) {
        groupes.removeChild(groupes.firstChild);
    }

    var option = document.createElement("option");
    option.setAttribute("value", "");
    option.setAttribute("selected", "selected");
    option.setAttribute("disabled", "disabled");
    option.setAttribute("hidden", "hidden");
    option.innerHTML = "Groupe";
    groupes.appendChild(option);

    for (var i = 1; i <= max; i++) {
        var opt = document.createElement("option");
        opt.value = i;
        opt.innerHTML = "Groupe " + i;
        groupes.appendChild(opt);
    }
    document.getElementById("divGroupe").style.display = "block";
});