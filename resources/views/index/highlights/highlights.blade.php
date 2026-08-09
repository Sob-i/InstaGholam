@extends('layouts.front.highlights.main')
@section('content')
    <div class="highlight-page">

        <!-- HEADER -->
        <header class="highlight-header">

            <div class="header-left">

                <a class="back-btn" href="{{route('profile',Auth()->user()->username)}}">

                    <svg
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path
                            d="M19 12H5M12 19l-7-7 7-7"
                        />
                    </svg>

                </a>

                <h1 class="header-title">
                    New Highlight
                </h1>

            </div>

        </header>

        <!-- COVER -->
        <input
            type="file"
            id="cover-input"
            accept="image/*"
            hidden
        >
        <section class="cover-section">

            <div class="cover-wrapper">

                <div class="cover">

                    <div class="cover-inner">

                        <!-- Replace this with the selected story image -->
                        <img
                            src="https://images.unsplash.com/photo-1500534623283-312aade485b7?w=400"
                            alt="Highlight cover"
                        >
                        <div class="cover-inner">
                            <img src="" alt="Highlight Cover">
                        </div>
                    </div>

                </div>

                <div class="cover-edit">

                    <svg
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M12 20h9"/>
                        <path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/>
                    </svg>

                </div>

            </div>

            <span class="cover-label">
            Highlight Cover
        </span>

        </section>


        <!-- NAME -->
        <section class="name-section">

            <label class="input-label">
                Highlight Name
            </label>

            <input
                class="name-input"
                type="text"
                placeholder="Summer"
                id="name-input"
            >

        </section>


        <!-- STORIES HEADER -->
        <div class="stories-header">

        <span class="stories-title">
            Stories
        </span>

            <span class="selected-count">
            3 selected
        </span>

        </div>

        <!-- STORIES -->
        <main class="stories-grid">

            <!-- STORY  -->
            @forelse($highlights as $highlight)
                @php
                    $folder = strstr(Auth()->user()->email, '@',true);
                    $fileExtension = pathinfo($highlight->media, PATHINFO_EXTENSION);
                    $isVideo = in_array(strtolower($fileExtension), ['mp4', 'mov', 'avi', 'webm']);
                @endphp
                <div class="story" data-story-id="{{ $highlight->id }}">
                    @if($isVideo)
                        <video style="object-fit: cover; object-position: center; width: 100%; height: 100%; pointer-events: none;"
                               muted
                               preload="metadata"
                               disablepictureinpicture
                               disableremoteplayback>
                            <source src="{{asset('users/stories/'.$highlight->media_type . "/$folder/$highlight->media")}}"
                                    type="video/{{ $fileExtension }}">
                        </video>
                    @else
                        <img
                            src="{{asset('users/stories/'.$highlight->media_type . "/$folder/$highlight->media")}}"
                            alt="Story"
                        >
                    @endif

                    <div class="story-check">

                        <svg
                            fill="none"
                            stroke="currentColor"
                            stroke-width="3"
                            viewBox="0 0 24 24"
                        >
                            <path d="M5 12l4 4L19 6"/>
                        </svg>

                    </div>

                    <span class="story-date">
                {{$highlight->created_at->format('M-Y')}}
            </span>

                </div>
            @empty

            @endforelse

        </main>


        <!-- BOTTOM ACTION -->
        <div class="bottom-action">

            <button class="create-btn">
                Create Highlight
            </button>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            /*
            |--------------------------------------------------------------------------
            | Elements
            |--------------------------------------------------------------------------
            */

            const stories = document.querySelectorAll('.story');

            const selectedCount =
                document.querySelector('.selected-count');

            const createButton =
                document.querySelector('.create-btn');

            const coverWrapper =
                document.querySelector('.cover-wrapper');

            const coverImage =
                document.querySelector('.cover-inner img');

            const coverInput =
                document.querySelector('#cover-input');

            const title = document.getElementById('name-input');


            /*
            |--------------------------------------------------------------------------
            | Selected stories
            |--------------------------------------------------------------------------
            |
            | We store the story indexes here.
            |
            */

            const selectedStories = new Set();


            /*
            |--------------------------------------------------------------------------
            | Selected cover
            |--------------------------------------------------------------------------
            |
            | This is completely independent from the stories.
            |
            */

            let selectedCover = null;


            /*
            |--------------------------------------------------------------------------
            | Update selected story count
            |--------------------------------------------------------------------------
            */

            function updateSelectedCount() {

                const count = selectedStories.size;

                if (count === 0) {

                    selectedCount.textContent =
                        'No stories selected';

                } else if (count === 1) {

                    selectedCount.textContent =
                        '1 story selected';

                } else {

                    selectedCount.textContent =
                        `${count} stories selected`;

                }
            }


            /*
            |--------------------------------------------------------------------------
            | Story selection
            |--------------------------------------------------------------------------
            */

            stories.forEach((story) => {

                story.addEventListener('click', function () {

                    const storyId = this.dataset.storyId;

                    if (selectedStories.has(storyId)) {

                        selectedStories.delete(storyId);

                        this.classList.remove('selected');

                    } else {

                        selectedStories.add(storyId);

                        this.classList.add('selected');

                    }

                    updateSelectedCount();

                });

            });


            /*
            |--------------------------------------------------------------------------
            | Cover click
            |--------------------------------------------------------------------------
            |
            | Clicking the cover opens the user's local file picker.
            |
            */

            coverWrapper.addEventListener('click', function () {

                coverInput.click();

            });


            /*
            |--------------------------------------------------------------------------
            | Cover file selected
            |--------------------------------------------------------------------------
            */

            coverInput.addEventListener('change', function () {

                const file = this.files[0];

                /*
                 * User cancelled the file picker
                 */

                if (!file) {
                    return;
                }


                /*
                 * Make sure it is an image
                 */

                if (!file.type.startsWith('image/')) {

                    alert('Please select an image.');

                    coverInput.value = '';

                    selectedCover = null;

                    return;
                }


                /*
                 * Store the actual File object
                 *
                 * This will be used when sending the
                 * highlight to Laravel.
                 */

                selectedCover = file;


                /*
                 * Preview the selected image
                 */

                const reader = new FileReader();

                reader.onload = function (event) {

                    coverImage.src = event.target.result;

                };

                reader.readAsDataURL(file);

            });


            /*
            |--------------------------------------------------------------------------
            | Initial counter
            |--------------------------------------------------------------------------
            */

            updateSelectedCount();


            /*
            |--------------------------------------------------------------------------
            | Create highlight
            |--------------------------------------------------------------------------
            */

            createButton.addEventListener('click', function () {

                /*
                 * Check stories
                 */

                if (selectedStories.size === 0) {

                    alert('Please select at least one story.');

                    return;
                }


                /*
                 * Check cover
                 */

                if (!selectedCover) {

                    alert('Please choose a cover image.');

                    return;
                }


                /*
                 * Get selected story indexes
                 */

                const storiesArray =
                    [...selectedStories];


                console.log('Selected stories:', storiesArray);

                console.log('Selected cover:', selectedCover);


                /*
                 * Example FormData for Laravel
                 */

                const formData = new FormData();


                formData.append('title', title.value);
                /*
                 * Add cover
                 */

                formData.append(
                    'cover',
                    selectedCover
                );


                /*
                 * Add stories
                 */

                storiesArray.forEach(function (storyId) {
                    formData.append('stories[]', storyId);
                });

                for (const [key, value] of formData.entries()) {
                    console.log(key, value);
                }
                /*
                 * You can now send formData with fetch()
                 *
                 * Example:
                 *
                 * fetch('/your-route', {
                 *     method: 'POST',
                 *     body: formData
                 * });
                 */

            });
            createButton.addEventListener('click', function (event) {

                event.preventDefault();

                /*
                |--------------------------------------------------------------------------
                | Validate stories
                |--------------------------------------------------------------------------
                */

                if (selectedStories.size === 0) {

                    alert('Please select at least one story.');

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Validate cover
                |--------------------------------------------------------------------------
                */

                if (!selectedCover) {

                    alert('Please choose a cover image.');

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Get title
                |--------------------------------------------------------------------------
                */

                const highlightTitle = title.value.trim();

                if (!highlightTitle) {

                    alert('Please enter a highlight name.');

                    title.focus();

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Create FormData
                |--------------------------------------------------------------------------
                */

                const formData = new FormData();


                /*
                | Title
                */

                formData.append(
                    'title',
                    highlightTitle
                );


                /*
                | Cover
                */

                formData.append(
                    'cover',
                    selectedCover
                );


                /*
                | Stories
                */

                selectedStories.forEach(function (storyId) {

                    formData.append(
                        'stories[]',
                        storyId
                    );

                });


                /*
                |--------------------------------------------------------------------------
                | Debug
                |--------------------------------------------------------------------------
                */

                console.log('FORM DATA');

                for (const [key, value] of formData.entries()) {

                    console.log(
                        key,
                        value
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | Send to Laravel
                |--------------------------------------------------------------------------
                |
                | Your route:
                |
                | profile/{username}/highlight/create
                |
                */

                const username = "{{ Auth::user()->username }}";

                fetch(
                    `/profile/${username}/highlight/create`,
                    {
                        method: 'POST',

                        headers: {
                            'X-CSRF-TOKEN':
                                document.querySelector(
                                    'meta[name="csrf-token"]'
                                ).getAttribute('content'),

                            'Accept': 'application/json'
                        },

                        body: formData
                    }
                )
                    .then(response => response.json())
                    .then(data => {

                        console.log('SERVER RESPONSE:', data);

                        if (data.status) {

                            alert(data.message);

                            /*
                             * Optional:
                             * redirect/reload after successful creation
                             */

                            window.location.reload();

                        } else {

                            alert(data.message);

                        }

                    })
                    .catch(error => {

                        console.error(
                            'Create highlight error:',
                            error
                        );

                        alert('Something went wrong.');

                    });

            });
        });
    </script>
@endsection
