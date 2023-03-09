window.onload = (e) => {
    const input = document.querySelector('#inputTest');
    const btn = document.querySelector('#btn');
    const text = document.querySelector('#text');

    btn.addEventListener('mousedown', (e) => {
        let a = input.value;
        fetch('https://localhost/php-mvc/public/getDBTimbre', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: input.value
        })
        .then((data) => data.text())
        .then(data => {
            text.innerHTML = data;
        });
    });
}