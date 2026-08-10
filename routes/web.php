<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\auth\authController;

use App\Http\Controllers\front\frontController;
use App\Http\Controllers\front\profile\profileController;
use App\Http\Controllers\front\posts\postController;
use App\Http\Controllers\front\posts\postsLikeController;
use App\Http\Controllers\front\posts\postsSaveController;
use App\Http\Controllers\front\comments\commentController;
use App\Http\Controllers\front\follow\followController;
use App\Http\Controllers\front\explore\exploreController;
use App\Http\Controllers\front\story\storyController;

use App\Http\Controllers\admin\adminDashboardController;
use App\Http\Controllers\admin\adminDashboard\usersDashboardController;
use App\Http\Controllers\admin\adminDashboard\postsDashboardController;

use App\Http\Controllers\front\report\reportController;

use App\Http\Controllers\front\messages\messageController;



    // Login && SignUp Form
    Route::get('', [authController::class, 'showLoginAndSingUpForm'])->name('login');

    Route::prefix('auth')->group(function () {

        Route::post('login', [authController::class, 'login'])->name('admin.login');

        Route::post('signup', [authController::class, 'signup'])->name('admin.signup');

    });

Route::middleware('auth')->group(function () {

    Route::prefix('')->group(function () {

        // Index
        Route::get('index', [frontController::class, 'index'])->name('homepage');

        // Explore
        Route::get('explore', [exploreController::class, 'showExplore'])->name('explore');
        Route::get('explore/search', [exploreController::class, 'search'])->name('explore.search');

        // Post
        Route::get('post/{id}', [postController::class, 'showPost'])->name('post.show');
        Route::post('post/{id}/comments', [commentController::class, 'sendComment'])->name('post.comment.send');
        Route::post('/post/{post}/like', [postsLikeController::class, 'toggle'])->name('post.like');
        Route::post('/post/{post}/save', [postsSaveController::class, 'toggle'])->name('post.save');

        // New Post
        Route::get('newPost', [frontController::class, 'newPost'])->name('newPost');
        Route::post('newPost/create', [postController::class, 'createPost'])->name('newPost.create');

        // Story
        Route::get('newStory', [frontController::class, 'newStoryShow'])->name('story.new.show');
        Route::post('newStory/add', [storyController::class, 'newStory'])->name('story.new');

        // Notifications
        Route::get('notifications', [frontController::class, 'notificationsShow'])->name('notifications.show');

        // Messages (chat)
        Route::get('messages', [frontController::class, 'messagesShow'])->name('messages.show');
        Route::get('message/{userId}', [messageController::class, 'messagePageShow'])->name('message.page.show');
        Route::post('message/{userId}/send', [messageController::class, 'sendMessage'])->name('message.send');
        Route::get('message/{chatId}/search', [messageController::class, 'searchMessage'])->name('message.search');

        // Profile
        Route::get('profile/{username}', [frontController::class, 'profile'])->name('profile');
        Route::get('profile/{username}/edit', [profileController::class, 'showEditProfile'])->name('profile.edit.show');
        Route::put('profile/{username}/edit', [profileController::class, 'editProfile'])->name('profile.edit');
        Route::get('profile/{username}/edit/password', [profileController::class, 'showEditPassword'])->name('profile.edit.password.show');
        Route::put('profile/{username}/edit/password', [profileController::class, 'editPassword'])->name('profile.edit.password');
        Route::post('profile/{username}/follow', [followController::class, 'toggle'])->name('profile.user.follow');
        Route::put('profile/{id}/follow/acceptRequest', [followController::class, 'accept'])->name('profile.user.follow.accept');
        Route::get('profile/{username}/closeFriends', [profileController::class, 'closeFriendShow'])->name('profile.closeFriend.show');
        Route::post('profile/{username}/closeFriends', [profileController::class, 'toggle'])->name('profile.closeFriend.toggle');
        Route::get('profile/{username}/showFollowers', [profileController::class, 'showFollowers'])->name('profile.followers.show');
        Route::get('profile/{username}/showFollowings', [profileController::class, 'showFollowings'])->name('profile.followings.show');
        // Highlights
        Route::get('profile/{username}/highlights', [profileController::class, 'showHighlights'])->name('profile.highlights.show');
        Route::post('profile/{username}/highlight/create', [profileController::class, 'createHighlight'])->name('profile.highlights.create');
        Route::get('profile/{username}/{highlight}/show', [profileController::class, 'showHighlight'])->name('profile.highlight.show');

        // Report
        Route::post('report/{Uid}', [reportController::class, 'Report'])->name('report.create');

    });

    Route::prefix('admin')->group(function () {

        // Dashboard
        Route::get('dashboard', [adminDashboardController::class, 'showDashboard'])->name('admin.dashboard');

        // Users
        Route::get('users', [adminDashboardController::class, 'showUsers'])->name('admin.users');
        Route::put('users/{id}/statusToActive', [usersDashboardController::class, 'userStatusToActive'])->name('admin.users.status.active');
        Route::put('users/{id}/statusToSuspended', [usersDashboardController::class, 'userStatusToSuspended'])->name('admin.users.status.suspended');
        Route::put('users/{id}/statusToBanned', [usersDashboardController::class, 'userStatusToBanned'])->name('admin.users.status.banned');

        // Posts
        Route::get('posts', [adminDashboardController::class, 'showPosts'])->name('admin.posts');
        Route::get('postsHidden', [adminDashboardController::class, 'showPostsHidden'])->name('admin.posts.hidden');
        Route::get('postsFlagged', [adminDashboardController::class, 'showPostsFlagged'])->name('admin.posts.flagged');

        Route::get('posts/search', [postsDashboardController::class, 'search'])->name('admin.posts.search');
        Route::get('posts/searchFlagged', [postsDashboardController::class, 'searchInFlagged'])->name('admin.posts.search.flagged');
        Route::get('posts/searchHidden', [postsDashboardController::class, 'searchInHidden'])->name('admin.posts.search.hidden');

        Route::put('posts/statusToActive/{id}', [postsDashboardController::class, 'postStatusToActive'])->name('admin.posts.status.to.active');
        Route::put('posts/statusToFlagged/{id}', [postsDashboardController::class, 'postStatusToFlagged'])->name('admin.posts.status.to.flagged');
        Route::put('posts/statusToHidden/{id}', [postsDashboardController::class, 'postStatusToHidden'])->name('admin.posts.status.to.hidden');

        // Reports
        Route::get('reports', [adminDashboardController::class, 'showReports'])->name('admin.reports');

    });

});
