
<script>
    function switchTab(tabName, element) {
        // Remove active class from all tabs
        document.querySelectorAll('.gtab').forEach(tab => {
            tab.classList.remove('active');
        });

        // Add active class to clicked tab
        element.classList.add('active');

        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.style.display = 'none';
        });

        // Show selected tab content
        document.getElementById(tabName + '-content').style.display = 'block';
    }
</script>
<script src="{{asset('frontJs/profile/follow.js')}}"></script>
</body>
</html>
