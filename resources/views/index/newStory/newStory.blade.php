@extends('layouts.front.newStory.main')
@section('content')
    <div class="main">
        <!-- STORY CANVAS -->
        <div class="story-stage">
            <div class="story-progress"><div class="story-progress-fill"></div></div>

            <div class="story-topbar">
                <div class="story-av"></div>
                <div>
                    <div class="story-user">Your Story</div>
                    <div class="story-label">Preview</div>
                </div>
                <div class="story-spacer"></div>
                <button class="icon-btn">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <!-- Placeholder canvas content (acts as empty-state until photo/video chosen) -->
            <div class="story-canvas-content">
                <div class="canvas-placeholder-icon">
                    <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                </div>
                <div class="canvas-placeholder-text">Tap to add a photo or video</div>
                <div class="canvas-placeholder-sub">Disappears after 24 hours</div>
            </div>
        </div>

        <!-- EDITOR PANEL -->
        <div class="editor-panel">
            <div>
                <div class="panel-title">Create Story</div>
                <div class="panel-sub">Share a moment that lasts 24 hours.</div>
            </div>

            <!-- Privacy -->
            <div class="panel-card">
                <div class="panel-card-label">Share With</div>
                <div class="privacy-row">
                    <label class="privacy-opt selected">
                        <input type="radio" name="privacy" value="followers" checked style="display: none;">
                        <div class="privacy-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                        </div>
                        <div class="privacy-info">
                            <div class="privacy-name">Your followers</div>
                            <div class="privacy-desc">Visible to everyone who follows you</div>
                        </div>
                        <div class="privacy-radio"></div>
                    </label>
                    <label class="privacy-opt">
                        <input type="radio" name="privacy" value="closeFriends" style="display: none;">
                        <div class="privacy-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                        </div>
                        <div class="privacy-info">
                            <div class="privacy-name">Close Friends</div>
                            <div class="privacy-desc">Only your close friends list</div>
                        </div>
                        <div class="privacy-radio"></div>
                    </label>
                </div>
            </div>
            <!-- Actions -->
            <div class="btn-row">
                <button class="btn-primary">Share to Story</button>
            </div>
        </div>
    </div>

@endsection
