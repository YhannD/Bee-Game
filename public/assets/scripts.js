// Attache l'événement click au bouton d'attaque
const attackButton = document.getElementById('attackButton');
attackButton.addEventListener('click', hitRandomBee);

// Fonction pour attaquer une abeille aléatoire
function hitRandomBee() {
    fetch('libs/hit.php')
        .then(response => response.json())
        .then(data => updateGamePage(data))
        .catch(error => console.log(error));
}

// Fonction pour mettre à jour la page du jeu
function updateGamePage(data) {
    const remainingBees = data.remainingBees;
    for (const beeType in remainingBees) {
        if (remainingBees.hasOwnProperty(beeType)) {
            const hitPointsElement = document.getElementById(beeType + '-hitPoints');
            hitPointsElement.textContent = remainingBees[beeType].hitPoints;
        }
    }
}
