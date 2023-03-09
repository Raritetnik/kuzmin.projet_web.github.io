window.onload = (e) => {
    const menu = document.querySelector('.menu');
    const contenu = document.querySelector('.contenu');

    // Les templates
    const tmpInfoCompte = document.querySelector('#infoCompte');
    const tmpFavoris = document.querySelector('#favoris');
    const tmpListeMises = document.querySelector('#listeMises');
    const tmplisteTimbres = document.querySelector('#listeTimbres');
    let btnMotDePasse = document.querySelector("#motpasse");

    // Affichage des parties du menu
    menu.addEventListener('mousedown', (e) => {
        let option = e.target.classList.value;
        switch(option) {
            case 'info':
                contenu.innerHTML = tmpInfoCompte.innerHTML;
                btnMotDePasse = document.querySelector("#motpasse");
                btnMotDePasse.addEventListener('mousedown', chargerAction);
                break;
            case 'favoris':
                contenu.innerHTML = tmpFavoris.innerHTML;
                break;
            case 'mises':
                contenu.innerHTML = tmpListeMises.innerHTML;
                break;
            case 'timbres':
                console.log('Affiche timbres');
                contenu.innerHTML = tmplisteTimbres.innerHTML;
                break;
            default:
        }
    });

    // Action de modification de mot de passe
    function chargerAction(e) {
        const motDePasse = document.querySelector('[name="password"]');

        if(motDePasse.value != "" || motDePasse.value != null) {
            fetch('http://localhost:8080/projet_web/public/membre/motdepasse', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: motDePasse.value
            })
            .then((data) => data.text())
            .then(data => {
                console.log(data);
            });
        }
    }


}