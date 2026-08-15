
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
<script>
    function toggleDropdown(event) {
        event.stopPropagation();
        const dropdown = document.getElementById('dropdownMenu');
        if (dropdown.style.display === 'none' || dropdown.style.display === '') {
            dropdown.style.display = 'block';
        } else {
            dropdown.style.display = 'none';
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('dropdownMenu');
        const wrapper = document.querySelector('.dropdown-wrapper');

        if (dropdown && wrapper && !wrapper.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });
</script>
<script>
    window.savedPostsUrl = @json(
        route('profile.posts.saved.show', [
            'username' => $user->username
        ])
    );

    window.savedPostsAssetUrl = @json(
        asset('users/posts')
    );


    window.savedPostUrl = @json(
        url('post')
    );
</script>
<script src="{{asset('frontJs/profile/savedTab.js')}}"></script>
<script src="{{asset('frontJs/profile/follow.js')}}"></script>
<script src="{{asset('frontJs/profile/followingAndFollowsModal.js')}}"></script>
<script src="{{asset('frontJs/profile/highlightModal.js')}}"></script>

</body>
</html>
