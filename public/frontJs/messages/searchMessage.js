// message-search.js

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-wrap input');
    const threadMessages = document.querySelector('.thread-messages');
    let searchTimeout;
    let currentMatches = [];
    let currentMatchIndex = -1;

    const chatId = data.chat_id;

    // Create search navigation with better styling
    const searchNav = document.createElement('div');
    searchNav.className = 'search-navigation';
    searchNav.style.cssText = `
        display: none;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 10px 16px;
        background: #ffffff;
        border-bottom: 1px solid #e0e0e0;
        font-size: 13px;
        color: #666;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    `;
    searchNav.innerHTML = `
        <span class="result-count" style="font-weight: 500; min-width: 80px; text-align: center;">0 results</span>
        <div style="display: flex; gap: 4px;">
            <button class="prev-btn" style="
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 6px 12px;
                background: #f5f5f5;
                border: 1px solid #ddd;
                border-radius: 6px;
                cursor: pointer;
                font-size: 13px;
                color: #333;
                transition: all 0.2s;
            ">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
                Prev
            </button>
            <button class="next-btn" style="
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 6px 12px;
                background: #f5f5f5;
                border: 1px solid #ddd;
                border-radius: 6px;
                cursor: pointer;
                font-size: 13px;
                color: #333;
                transition: all 0.2s;
            ">
                Next
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
        </div>
        <button class="clear-btn" style="
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 12px;
            background: transparent;
            border: 1px solid #ddd;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            color: #666;
            transition: all 0.2s;
        ">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
            Clear
        </button>
    `;

    threadMessages.parentNode.insertBefore(searchNav, threadMessages);

    // Add hover effects
    searchNav.querySelectorAll('button').forEach(btn => {
        btn.addEventListener('mouseenter', () => {
            btn.style.background = '#e8e8e8';
        });
        btn.addEventListener('mouseleave', () => {
            if (btn.classList.contains('clear-btn')) {
                btn.style.background = 'transparent';
            } else {
                btn.style.background = '#f5f5f5';
            }
        });
    });

    // Search on input with debounce
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const term = this.value.trim();

        if (!term) {
            clearSearch();
            return;
        }

        searchTimeout = setTimeout(() => {
            fetchMessages(term);
        }, 500);
    });

    // Navigation events
    searchNav.querySelector('.prev-btn').addEventListener('click', () => navigate(-1));
    searchNav.querySelector('.next-btn').addEventListener('click', () => navigate(1));
    searchNav.querySelector('.clear-btn').addEventListener('click', clearSearch);

    async function fetchMessages(term) {
        try {
            const url = `/message/${chatId}/search?word=${encodeURIComponent(term)}`;

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            removeHighlights();

            if (data.success && Array.isArray(data.wordsFound) && data.wordsFound.length > 0) {
                highlightMatches(term);
                searchNav.style.display = 'flex';
                updateCounter();
            } else {
                searchNav.style.display = 'flex';
                searchNav.querySelector('.result-count').textContent = 'No results found';
                currentMatches = [];
                currentMatchIndex = -1;
            }
        } catch (error) {
            console.error('Search error:', error);
            searchNav.style.display = 'flex';
            searchNav.querySelector('.result-count').textContent = 'Error searching';
        }
    }

    function highlightMatches(term) {
        currentMatches = [];
        const messages = threadMessages.querySelectorAll('.message');

        messages.forEach(msg => {
            if (!msg.dataset.originalHtml) {
                msg.dataset.originalHtml = msg.innerHTML;
            }

            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = msg.dataset.originalHtml;

            function highlightTextNodes(node) {
                if (node.nodeType === 3) {
                    if (node.parentElement &&
                        (node.parentElement.classList.contains('time') ||
                            node.parentElement.tagName === 'SPAN' && node.parentElement.classList.contains('time'))) {
                        return;
                    }

                    const text = node.textContent;
                    if (text.toLowerCase().includes(term.toLowerCase())) {
                        const regex = new RegExp(`(${escapeRegex(term)})`, 'gi');
                        const highlightedHTML = text.replace(regex,
                            '<mark style="background:#FFEB3B;padding:1px 2px;border-radius:2px;">$1</mark>'
                        );

                        const span = document.createElement('span');
                        span.innerHTML = highlightedHTML;
                        node.parentNode.replaceChild(span, node);
                    }
                } else if (node.nodeType === 1) {
                    if (node.classList && node.classList.contains('time')) {
                        return;
                    }
                    if (node.tagName !== 'SCRIPT' && node.tagName !== 'STYLE' && node.tagName !== 'MARK') {
                        Array.from(node.childNodes).forEach(child => highlightTextNodes(child));
                    }
                }
            }

            Array.from(tempDiv.childNodes).forEach(child => highlightTextNodes(child));
            msg.innerHTML = tempDiv.innerHTML;

            const messageText = msg.cloneNode(true);
            const timeSpan = messageText.querySelector('.time');
            if (timeSpan) {
                timeSpan.remove();
            }

            if (messageText.textContent.toLowerCase().includes(term.toLowerCase())) {
                currentMatches.push(msg);
            }
        });

        if (currentMatches.length > 0) {
            // Start from the last match (most recent message at the bottom)
            currentMatchIndex = currentMatches.length - 1;
            scrollToMatch(currentMatches[currentMatchIndex]);
        }
    }

    function navigate(direction) {
        if (currentMatches.length === 0) return;

        // Remove active state from current
        if (currentMatchIndex >= 0 && currentMatchIndex < currentMatches.length) {
            currentMatches[currentMatchIndex].style.backgroundColor = '';
            currentMatches[currentMatchIndex].style.borderRadius = '';
            currentMatches[currentMatchIndex].style.padding = '';
            currentMatches[currentMatchIndex].style.transition = '';
        }

        // Move index in reverse
        currentMatchIndex -= direction; // Changed from += to -=
        if (currentMatchIndex >= currentMatches.length) {
            currentMatchIndex = 0;
        }
        if (currentMatchIndex < 0) {
            currentMatchIndex = currentMatches.length - 1;
        }

        // Highlight and scroll
        scrollToMatch(currentMatches[currentMatchIndex]);
        updateCounter();
    }

    function scrollToMatch(element) {
        element.style.backgroundColor = '#70653f';
        element.style.borderRadius = '4px';
        element.style.padding = '8px';
        element.style.transition = 'all 0.3s ease';
        element.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function updateCounter() {
        searchNav.querySelector('.result-count').textContent =
            `${currentMatchIndex + 1} of ${currentMatches.length}`;
    }

    function removeHighlights() {
        const messages = threadMessages.querySelectorAll('.message');
        messages.forEach(msg => {
            if (msg.dataset.originalHtml) {
                msg.innerHTML = msg.dataset.originalHtml;
            }
            msg.style.outline = '';
            msg.style.backgroundColor = '';
            msg.style.borderRadius = '';
            msg.style.padding = '';
            msg.style.transition = '';
        });
        currentMatches = [];
        currentMatchIndex = -1;
    }

    function clearSearch() {
        searchInput.value = '';
        removeHighlights();
        searchNav.style.display = 'none';
    }

    function escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (currentMatches.length > 0) {
                navigate(e.shiftKey ? -1 : 1);
            }
        }
    });
});
