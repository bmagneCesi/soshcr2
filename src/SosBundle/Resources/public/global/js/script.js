$(document).ready(function(){

	if (typeof error != "undefined") {
        sweetAlert("Oups...", error, "error");
    }

});


//fonction qui coche toutes les checkboxes
function CocheTout(ref, name) {
    var form = ref;

    while (form.parentNode && form.nodeName.toLowerCase() != 'form'){
        form = form.parentNode;
    }

    var elements = form.getElementsByTagName('input');

    for (var i = 0; i < elements.length; i++) {
        if (elements[i].type == 'checkbox' && elements[i].name == name) {
            elements[i].checked = ref.checked;
        }
    }
}



//fonction pour imprimer les resultats
function imprime_bloc(titre, objet) {
// Définition de la zone à imprimer
    var zone = document.getElementById(objet).innerHTML;

// Ouverture du popup
    var fen = window.open("", "", "height=500, width=600,toolbar=0, menubar=0, scrollbars=1, resizable=1,status=0, location=0, left=10, top=10");

// style du popup
    fen.document.body.style.color = '#000000';
    fen.document.body.style.backgroundColor = '#FFFFFF';
    fen.document.body.style.padding = "20px";

// Ajout des données a imprimer
    fen.document.title = titre;
    fen.document.body.innerHTML += " " + zone + " ";

// Impression du popup
    fen.window.print();

//Fermeture du popup
    fen.window.close();
    return true;
}

