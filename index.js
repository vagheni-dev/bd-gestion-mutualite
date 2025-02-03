/**
 

function myFunction() {
    alert("Button was clicked!");
}
//creation de objet en js
var obj={
'nom':'nehemie',
'postnom':'kawa',
'id':'1',
'fonction':'stydent'

};
//acces a un objet
obj.nom;


//creation de tableau en js avec les objets en l'interieur

eleve=[
    {
        'nom':'nehemie',
        'postnom':'kawa',
        'id':'1',
        'fonction':'student'
    },
    {
        'nom':'katembo',
        'postnom':'kaheraya',
        'id':'2',
        'fonction':'student'
    },
    {
        'nom':'kambale',
        'postnom':'dieu merci',
        'id':'3',
        'fonction':'student'
    },
    {
        'nom':'kambale',
        'postnom':'kataka',
        'id':'4',
        'fonction':'student'
    },
    {
        'nom':'elie',
        'postnom':'mwandu',
        'id':'5',
        'fonction':'student'
    }
]


// appel en un objet se trouvant dans une tableau

eleve[2].nom;
 **/

// connexion permettant le partage de données ajax avec xmlhttprequest
// instanciation de l'objet XMLHttpRequest
var xhr = new XMLHttpRequest();

// on ouvre le fichier par open qui reçoit 3 paramètres : la méthode, l'url et true pour l'asynchrone
xhr.open('GET', 'text.json', true);

// on crée la variable qui nous permet d'afficher les données sur notre page
var btn = document.getElementById('btn');
var text = document.getElementById('para');
btn.addEventListener('click', function() {
    // Configuration de la requête
    xhr.onload = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // traitement de la réponse en la convertissant au format json avec JSON.parse
            var response = JSON.parse(xhr.responseText);
            console.log(response);
           reader(response);
        }
    };

    // on récupère les données par send
    xhr.send();
});

function reader (data){
    var donnestr = '';
    for (let index = 0; index < data.length; index++) {
        donnestr += `<p>  l'étudiant ${data[index].nom} son post nom ${data[index].postnom}  son id ${data[index].id} sa fonction ${data[index].fonction} </p> `;
    }
    text.insertAdjacentHTML("beforeend",donnestr);
}
