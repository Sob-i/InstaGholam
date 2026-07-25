<script>
    function selectFilter(el){document.querySelectorAll('.filter').forEach(f=>f.classList.remove('active'));el.classList.add('active');}
    const dz=document.getElementById('dz');
    dz.addEventListener('click',()=>{const p=document.getElementById('preview');p.classList.toggle('show');});
    dz.addEventListener('dragover',e=>{e.preventDefault();dz.classList.add('drag');});
    dz.addEventListener('dragleave',()=>dz.classList.remove('drag'));
    dz.addEventListener('drop',e=>{e.preventDefault();dz.classList.remove('drag');document.getElementById('preview').classList.add('show');});
</script>

<script>
    const files = [];
    let activeIdx = 0;

    const dz        = document.getElementById('dz');
    const strip     = document.getElementById('thumbStrip');
    const mainImg   = document.getElementById('mainImg');
    const mainVid   = document.getElementById('mainVid');
    const mainOverlay = document.getElementById('mainOverlay');
    const mainLabel = document.getElementById('mainLabel');
    const changeHint = document.getElementById('changeHint');
    const emptyState = document.getElementById('emptyState');
    const uploadFile = document.getElementById('uploadFile');

    /* ── file input change ── */
    uploadFile.addEventListener('change', e => {
        Array.from(e.target.files).forEach(f => files.push({ file: f, url: URL.createObjectURL(f) }));
        uploadFile.value = '';
        if (files.length) {
            renderStrip();
            setActive(activeIdx);
        }
    });

    /* ── drag & drop ── */
    dz.addEventListener('dragover',  e => { e.preventDefault(); dz.classList.add('drag'); });
    dz.addEventListener('dragleave', ()  => dz.classList.remove('drag'));
    dz.addEventListener('drop', e => {
        e.preventDefault();
        dz.classList.remove('drag');
        Array.from(e.dataTransfer.files)
            .filter(f => f.type.startsWith('image/') || f.type.startsWith('video/'))
            .forEach(f => files.push({ file: f, url: URL.createObjectURL(f) }));
        if (files.length) { renderStrip(); setActive(activeIdx); }
    });

    function isVideo(f) { return f.file.type.startsWith('video/'); }

    /* ── show selected file in main dropzone ── */
    function setActive(idx) {
        activeIdx = idx;
        const f = files[idx];

        emptyState.style.display = 'none';
        mainOverlay.classList.add('visible');
        mainLabel.classList.add('visible');
        changeHint.classList.add('visible');

        if (isVideo(f)) {
            mainImg.classList.remove('visible');
            mainVid.src = f.url;
            mainVid.classList.add('visible');
            mainLabel.textContent = f.file.name.split('.').pop().toUpperCase();
        } else {
            mainVid.classList.remove('visible');
            mainVid.src = '';
            mainImg.src = f.url;
            mainImg.classList.add('visible');
            mainLabel.textContent = (idx + 1) + ' / ' + files.length;
        }

        document.querySelectorAll('.strip-thumb').forEach((el, i) => {
            el.classList.toggle('active', i === idx);
        });
    }

    /* ── render thumbnail strip ── */
    function renderStrip() {
        strip.innerHTML = '';

        files.forEach((f, i) => {
            const thumb = document.createElement('div');
            thumb.className = 'strip-thumb' + (i === activeIdx ? ' active' : '');
            thumb.onclick = () => setActive(i);

            if (isVideo(f)) {
                const v = document.createElement('video');
                v.src = f.url;
                v.style.cssText = 'width:100%;height:100%;object-fit:cover;border-radius:8px;';
                thumb.appendChild(v);
            } else {
                const img = document.createElement('img');
                img.src = f.url;
                img.alt  = '';
                thumb.appendChild(img);
            }

            /* remove button */
            const del = document.createElement('div');
            del.className = 'strip-del';
            del.innerHTML = '&times;';
            del.onclick = e => { e.stopPropagation(); removeFile(i); };
            thumb.appendChild(del);

            strip.appendChild(thumb);
        });

        /* "+ add more" button */
        const addBtn = document.createElement('div');
        addBtn.className = 'strip-add';
        addBtn.innerHTML = '+';
        addBtn.onclick = () => uploadFile.click();
        strip.appendChild(addBtn);

        strip.style.display = files.length ? 'flex' : 'none';
    }

    /* ── remove a file ── */
    function removeFile(idx) {
        URL.revokeObjectURL(files[idx].url);
        files.splice(idx, 1);

        if (!files.length) {
            strip.style.display = 'none';
            mainImg.classList.remove('visible');
            mainVid.classList.remove('visible');
            mainVid.src = '';
            mainOverlay.classList.remove('visible');
            mainLabel.classList.remove('visible');
            changeHint.classList.remove('visible');
            emptyState.style.display = '';
            activeIdx = 0;
        } else {
            activeIdx = Math.min(activeIdx, files.length - 1);
            renderStrip();
            setActive(activeIdx);
        }
    }
</script>
</body>
</html>
