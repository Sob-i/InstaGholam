<script>
    function switchTab(tab) {
        const tabs = document.querySelectorAll('.tab');
        const login = document.getElementById('login-form');
        const reg   = document.getElementById('register-form');
        if (tab === 'login') {
            tabs[0].classList.add('active'); tabs[1].classList.remove('active');
            login.style.display = 'flex'; reg.style.display = 'none';
        } else {
            tabs[1].classList.add('active'); tabs[0].classList.remove('active');
            reg.style.display = 'flex'; login.style.display = 'none';
        }
    }
</script>
</body>
</html>
