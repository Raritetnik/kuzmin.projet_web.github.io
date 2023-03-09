window.onload = (e) => {
    const filtre = document.querySelector('#favoris');

    const options = {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      };


    // Mettre en favrois l'enchere - ACTION
    filtre.addEventListener('mousedown' , (e) => {
        let id = filtre.dataset.idenchere;
        // Action sur la base de données
        fetch('http://localhost:8080/projet_web/public/LoadDB/mettreFavoris', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: id
        })
        .then((data) => data.text())
        .then(data => {
            console.log(data);
        });
    });
}



/* POST element vers une fonction de Controller

fetch('http://localhost:8080/projet_web/public/LoadDB/Filtre', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(vars)
        })
        .then((data) => data.json())
        .then(data => {
            changerContenu(data);
        });*/