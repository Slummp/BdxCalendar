// Objet Clipboard pour gérer la copie
var clipboard = new Clipboard('#copyButton');

// Gestion des erreurs
document.getElementById("copyButton").onclick = function () {
    // Variables...
    var span;
    var strong;

    // Diplôme non sélectionné
    if (document.getElementById("selectDiplome").value == "") {
        span = document.createElement("span");

        strong = document.createElement("strong");
        strong.innerHTML = "Attention !";

        span.appendChild(strong);
        span.appendChild(document.createTextNode(" Vous avez oublié de sélectionner un diplôme."));

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

    // Formation non sélectionnée
    else if (document.getElementById("selectFormation").value == "") {
        span = document.createElement("span");

        strong = document.createElement("strong");
        strong.innerHTML = "Attention !";

        span.appendChild(strong);
        span.appendChild(document.createTextNode(" Vous avez oublié de sélectionner une formation."));

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

// Affichage des semestres
$('#selectDiplome').change(function () {
    document.getElementById("divSemestre").style.display = 'block';
});

// Récupération de la liste des formations
$('#selectSemestre').change(function () {
    // Récupération du diplôme et du semestre
    var dip = document.getElementById("selectDiplome").value;
    var sem = document.getElementById("selectSemestre").value;

    // Récupération de la liste des formations en AJAX
    $.post("formations.php", {diplome: dip, semestre: sem}, function (data) {
        // Nettoyage de la div d'erreurs et de la liste des formations
        document.getElementById("errors").innerHTML = '';
        document.getElementById('selectFormation').innerHTML = '';

        // Affichage de la div contenant la liste des formations
        document.getElementById("divFormation").style.display = 'block';

        var selectOption = document.createElement("option");
        selectOption.setAttribute("value", "");
        selectOption.setAttribute("selected", "selected");
        selectOption.setAttribute("disabled", "disabled");
        selectOption.setAttribute("hidden", "hidden");

        document.getElementById("selectFormation").appendChild(selectOption);
        selectOption.innerHTML = "Sélectionnez une formation";

        for (var key in data) {
            var selectOption = document.createElement("option");
            selectOption.setAttribute("value", key);

            //

            document.getElementById("selectFormation").appendChild(selectOption);
            selectOption.innerHTML = data[key];
        }
    });
});

// Récupération de la liste des groupes et de la liste des modules
$('#selectFormation').change(function () {
    // Récupération du diplôme, du semestre et de la formation
    var dip = document.getElementById("selectDiplome").value;
    var sem = document.getElementById("selectSemestre").value;
    var form = document.getElementById("selectFormation").value;

    // Nettoyage de la div d'erreurs, de la liste des groupes et de la liste des modules
    document.getElementById("errors").innerHTML = '';
    document.getElementById('contentGroupes').innerHTML = '';
    document.getElementById('contentModules').innerHTML = '';

    // Récupération de la liste des groupes et des modules en AJAX
    $.post("options.php", {diplome: dip, semestre: sem, formation: form}, function (data) {
        // Création du contenu de la liste des groupes et des modules
        var groupes = data["Groupes"];
        var modules = data["Modules"];

        // Groupes
        for (var key in groupes) {
            var checkboxDiv = document.createElement("div");
            checkboxDiv.setAttribute("class", "checkbox");

            var checkboxLabel = document.createElement("label");
            checkboxLabel.setAttribute("for", "checkboxesGroupe-" + key);

            var checkboxInput = document.createElement("input");
            checkboxInput.setAttribute("id", "checkboxesGroupe-" + key);
            checkboxInput.setAttribute("name", "groupes[]");
            checkboxInput.setAttribute("type", "checkbox");
            checkboxInput.setAttribute("value", encodeURI(groupes[key]));

            //

            document.getElementById("contentGroupes").appendChild(checkboxDiv);
            checkboxDiv.appendChild(checkboxLabel);
            checkboxLabel.appendChild(checkboxInput);
            checkboxLabel.appendChild(document.createTextNode(groupes[key]));
        }

        // Modules
        for (var key in modules) {
            var checkboxDiv = document.createElement("div");
            checkboxDiv.setAttribute("class", "checkbox");

            var checkboxLabel = document.createElement("label");
            checkboxLabel.setAttribute("for", "checkboxesModule-" + key);

            var checkboxInput = document.createElement("input");
            checkboxInput.setAttribute("id", "checkboxesModule-" + key);
            checkboxInput.setAttribute("name", "modules[]");
            checkboxInput.setAttribute("type", "checkbox");
            checkboxInput.setAttribute("value", key);

            //

            document.getElementById("contentModules").appendChild(checkboxDiv);
            checkboxDiv.appendChild(checkboxLabel);
            checkboxLabel.appendChild(checkboxInput);
            checkboxLabel.appendChild(document.createTextNode(modules[key]));
        }

        // Affichage de la div contenant la liste des formations
        if (document.getElementById('contentGroupes').innerHTML != '')
            document.getElementById("divGroupes").style.display = 'block';

        // Affichage de la div contenant la liste des modules
        if (document.getElementById('contentModules').innerHTML != '')
            document.getElementById("divModules").style.display = 'block';
    });
});

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

// Génération de l'URL du calendrier en fonction des paramètres du formulaire
document.getElementById("params").addEventListener("change", function () {
    // Récupération de l'input contenant le résultat
    var result = document.getElementById("inputResultat");

    // Base de l'URL
    result.value = window.location.href;
    result.value += "calendar.php";
    result.value += "?";

    // Ajout du paramètre diplome
    result.value += "diplome=";
    result.value += document.getElementById("selectDiplome").value;

    result.value += "&";

    // Ajout du paramètre semestre
    result.value += "semestre=";
    result.value += document.getElementById("selectSemestre").value;

    if (result.value.charAt(result.value.length - 1) == "=") {
        result.value = result.value.substring(0, result.value.length - 10);
    }

    result.value += "&";

    // Ajout du paramètre formation
    result.value += "formation=";
    result.value += document.getElementById("selectFormation").value;

    if (result.value.charAt(result.value.length - 1) == "=") {
        result.value = result.value.substring(0, result.value.length - 11);
    }

    // Ajout des groupes (si précisés)
    var groupes = document.getElementsByName('groupes[]');
    if (groupes.length != 0) {
        result.value += "&groupes=";

        for (var i = 0; i < groupes.length; i++) {
            if (groupes[i].checked == 1) {
                result.value += groupes[i].value;
                result.value += ",";
            }
        }

        // Suppresion de la virgule de fin inutile
        if (result.value.charAt(result.value.length - 1) == ",") {
            result.value = result.value.substring(0, result.value.length - 1);
        }

        // Suppression du paramètre inutile (parce que vide..)
        else if (result.value.charAt(result.value.length - 1) == "=") {
            result.value = result.value.substring(0, result.value.length - 9);
        }
    }

    // Ajout des filtres (si précisés)
    var modules = document.getElementsByName('modules[]');
    if (modules.length != 0) {
        result.value += "&filtres=";

        for (var i = 0; i < modules.length; i++) {
            if (modules[i].checked == 1) {
                result.value += modules[i].value;
                result.value += ",";
            }
        }

        // Suppresion de la virgule de fin inutile
        if (result.value.charAt(result.value.length - 1) == ",") {
            result.value = result.value.substring(0, result.value.length - 1);
        }

        // Suppression du paramètre inutile (parce que vide..)
        else if (result.value.charAt(result.value.length - 1) == "=") {
            result.value = result.value.substring(0, result.value.length - 9);
        }
    }

    // Ajout du paramètre indiquant si on utilise des heures UTC ou non
    if (document.getElementById("checkboxUTC").checked) {
        result.value += "&useUTC=1";
    }
});
