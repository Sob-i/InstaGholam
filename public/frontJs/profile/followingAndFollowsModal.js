
    const followersButton = document.getElementById('user-followers');
    const followingButton = document.getElementById('user-followings');

    const followModal = document.getElementById('followModal');
    const closeFollowModal = document.getElementById('closeFollowModal');

    const followModalTitle = document.getElementById('followModalTitle');
    const followList = document.getElementById('followList');

    const followLoading = document.getElementById('followLoading');
    const followError = document.getElementById('followError');


    followersButton.addEventListener('click', function () {
    openFollowModal(
        'Followers',
        "{{ route('profile.followers.show' , $user->username) }}"
    );
});


    followingButton.addEventListener('click', function () {
    openFollowModal(
        'Following',
        "{{ route('profile.followings.show', $user->username) }}"
    );
});


    async function openFollowModal(title, url) {

    followModalTitle.textContent = title;

    followModal.classList.add('active');

    // Reset modal
    followList.innerHTML = '';
    followError.style.display = 'none';
    followLoading.style.display = 'block';

    try {

    const response = await fetch(url, {
    method: 'GET',
    headers: {
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
}
});

    const result = await response.json();

    followLoading.style.display = 'none';

    if (!result.status) {
    followError.textContent = result.message || 'Something went wrong!';
    followError.style.display = 'block';
    return;
}

    renderFollowUsers(result.data);

} catch (error) {

    console.error(error);

    followLoading.style.display = 'none';

    followError.textContent = 'Something went wrong!';
    followError.style.display = 'block';
}
}


    function renderFollowUsers(users) {

    followList.innerHTML = '';

    if (!users || users.length === 0) {

    followList.innerHTML = `
            <div class="follow-empty">
                No users found.
            </div>
        `;

    return;
}

    users.forEach(follow => {

    const user = follow.follower_info ?? follow.following_info;

    if (!user) {
    return;
}

    const username = user.username ?? '';
    const name = user.name ?? '';

    const avatar = user.avatar
    ? `/users/avatar/${user.avatar}`
    : '/images/default-avatar.png';

    followList.innerHTML += `
            <a href="/profile/${encodeURIComponent(username)}" class="follow-user" style="text-decoration: none;">

                <img
                    src="${avatar}"
                    class="follow-user-avatar"
                    alt="${username}"
                >

                <div class="follow-user-info">

                    <div class="follow-user-username">
                        ${username}
                    </div>

                    <div class="follow-user-name">
                        ${name}
                    </div>
                </div>

            </a>
        `;
});
}


    // Close button
    closeFollowModal.addEventListener('click', function () {
    closeModal();
});


    // Click outside modal
    followModal.addEventListener('click', function (event) {

    if (event.target === followModal) {
    closeModal();
}

});


    // ESC key
    document.addEventListener('keydown', function (event) {

    if (event.key === 'Escape') {
    closeModal();
}

});


    function closeModal() {
    followModal.classList.remove('active');
}
