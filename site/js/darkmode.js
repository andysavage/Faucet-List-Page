(function () {
    var btn  = document.getElementById('dark-mode-toggle');
    var body = document.body;
    if (localStorage.getItem('darkMode') === 'true') {
        body.classList.add('dark-mode');
        btn.textContent = '\u2600\uFE0F';
    }
    btn.addEventListener('click', function () {
        body.classList.toggle('dark-mode');
        var isDark = body.classList.contains('dark-mode');
        localStorage.setItem('darkMode', isDark);
        btn.textContent = isDark ? '\u2600\uFE0F' : '\uD83C\uDF19';
    });
}());
