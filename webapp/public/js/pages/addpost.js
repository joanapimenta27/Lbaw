document.addEventListener("DOMContentLoaded", function() {
    let selectedFiles = [];
    const fileInput = document.getElementById('content');
    const fileList = document.getElementById('file-list');
    const contentButton = document.getElementById('content-btn');
    let placeholder;
    let currentMediaIndex = 0;

    initializeExistingFiles();
    updateFileOrderInput();

    function initializeExistingFiles() {
    const existingFiles = document.querySelectorAll('.file-item.existing-file');
    existingFiles.forEach(fileElement => {
        const fileId = fileElement.getAttribute('data-id');
        const filePath = fileElement.getAttribute('data-path');
        const fileName = fileElement.getAttribute('data-name');
        const fileType = fileElement.getAttribute('data-type');

        selectedFiles.push({
            id: fileId,
            name: fileName,
            path: filePath,
            type: fileType,
            isExisting: true
        });
    });

    renderFileList(selectedFiles);
    updateFileOrderInput();
}

    function updateFileOrderInput() {
        const fileOrder = selectedFiles.map(file => file.isExisting ? `existing_${file.id}` : `new_${file.name}`);
        document.getElementById('file-order').value = JSON.stringify(fileOrder);
    }

    function renderFileList(files) {
        fileList.innerHTML = '';

        files.forEach((file, index) => {
            const listItem = document.createElement('div');
            listItem.className = 'file-item';
            listItem.draggable = true;
            listItem.dataset.index = index;

            const fileNumber = document.createElement('span');
            fileNumber.className = 'file-number';
            fileNumber.textContent = `${index + 1}. `;

            const fileName = document.createElement('span');
            fileName.className = 'file-name';
            fileName.textContent = file.name;

            const removeButton = document.createElement('button');
            removeButton.className = 'remove-btn';
            removeButton.textContent = '✖';
            currentMediaIndex = 0;
            removeButton.onclick = function () {
                if (file.isExisting) {
                    removeExistingMedia(file.id);
                } else {
                    selectedFiles.splice(index, 1);
                    renderFileList(selectedFiles);
                    updateFileInput(selectedFiles);
                    updatePreview();
                }
            };

            listItem.appendChild(fileNumber);
            listItem.appendChild(fileName);
            listItem.appendChild(removeButton);

            if (file.isExisting) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'existing_files[]';
                hiddenInput.value = file.id;  // Assign the file id for existing media
                listItem.appendChild(hiddenInput);
            }
            
            fileList.appendChild(listItem);
        });

        if (files.length === 0) {
            const placeholderText = document.createElement('div');
            placeholderText.className = 'placeholder-text';
            placeholderText.textContent = 'No files chosen';
            fileList.appendChild(placeholderText);
        }
    }


    function removeExistingMedia(mediaId) {
        const fileItem = document.querySelector(`.file-item[data-id='${mediaId}']`);
        if (fileItem) {
            fileItem.remove();
        }

        // Also remove the hidden input for this mediaId
        const hiddenInput = document.querySelector(`input[name='existing_files[]'][value='${mediaId}']`);
        if (hiddenInput) {
            hiddenInput.remove();
        }

        // Remove the media from selectedFiles array
        console.log(selectedFiles);
        console.log(mediaId);
        selectedFiles = selectedFiles.filter(file => !(file.isExisting && file.id == mediaId));
        renderFileList(selectedFiles);
        updateFileOrderInput();
        updatePreview();
    }



    function handleFileSelection(event) {
        const newFiles = Array.from(event.target.files);
        const totalFiles = selectedFiles.length + newFiles.length;

        if (totalFiles > 7) {
            alert("You can only select up to 7 files.");
            return;
        }

        newFiles.forEach((newFile) => {
            if (!selectedFiles.some(file => file.name === newFile.name && file.size === newFile.size)) {
                selectedFiles.push({
                    file: newFile,
                    name: newFile.name,
                    type: newFile.type,
                    url: URL.createObjectURL(newFile),
                    isExisting: false // Indicate that it's a new file
                });
            }
        });

        renderFileList(selectedFiles);
        updateFileInput(selectedFiles);
        updatePreview();
    }

    function updateFileInput(files) {
        const dataTransfer = new DataTransfer();

        files.forEach(file => {
            if (!file.isExisting) {
                dataTransfer.items.add(file.file);
            }
        });
    
        fileInput.files = dataTransfer.files;
    }

    function handleDragStart(event) {
        event.dataTransfer.setData('text/plain', event.target.dataset.index);
        placeholder = document.createElement('div');
        placeholder.className = 'placeholder';
        placeholder.style.height = `${event.target.offsetHeight}px`;

        event.target.classList.add('dragged');
        setTimeout(() => {
            event.target.style.display = 'none';
        }, 0);
    }

    function handleDragOver(event) {
        event.preventDefault();
        const dropPositionY = event.clientY;

        let targetIndex = selectedFiles.length;

        for (let i = 0; i < fileList.children.length; i++) {
            const child = fileList.children[i];
            const rect = child.getBoundingClientRect();

            if (dropPositionY < rect.bottom) {
                targetIndex = i;

                if (child !== placeholder) {
                    fileList.insertBefore(placeholder, child);
                }
                break;
            }
        }

        if (targetIndex === selectedFiles.length) {
            fileList.appendChild(placeholder);
        }
    }

    function handleDrop(event) {
        event.preventDefault();
        const draggedIndex = parseInt(event.dataTransfer.getData('text/plain'));
        const dropPositionY = event.clientY;

        let targetIndex = selectedFiles.length;

        for (let i = 0; i < fileList.children.length; i++) {
            const child = fileList.children[i];
            const rect = child.getBoundingClientRect();
            if (dropPositionY < rect.bottom) {
                targetIndex = i;
                break;
            }
        }

        if (draggedIndex === targetIndex) {
            return;
        }

        const [draggedFile] = selectedFiles.splice(draggedIndex, 1);
        const insertIndex = targetIndex > draggedIndex ? targetIndex - 1 : targetIndex;
        selectedFiles.splice(insertIndex, 0, draggedFile);

        renderFileList(selectedFiles);
        updateFileInput(selectedFiles);
        updateFileOrderInput();
        updatePreview();
    }

    function handleDragEnd() {
        if (placeholder && placeholder.parentNode) {
            placeholder.parentNode.removeChild(placeholder);
        }

        const draggedElement = fileList.querySelector('.dragged');
        if (draggedElement) {
            draggedElement.classList.remove('dragged');
            draggedElement.style.display = 'block';
        }
    }

    contentButton.addEventListener('click', function() {
        fileInput.click();
    });

    fileInput.addEventListener('change', handleFileSelection);
    fileList.addEventListener('dragstart', handleDragStart);
    fileList.addEventListener('dragover', handleDragOver);
    fileList.addEventListener('drop', handleDrop);
    fileList.addEventListener('dragend', handleDragEnd);


    //------------------------ CONTENT PREVIEW ---------------------------//

    const titleInput = document.getElementById('title');
    const descriptionInput = document.getElementById('description');
    const fileInputPrev = document.getElementById('content');
    const postPreview = document.getElementById('post-preview');
    let lastTruncatedLength = null;
    let truncationApplied = false;

    function updatePreview() {
        postPreview.innerHTML = ''; // Clear previous preview

        //HEADER
        const dateElement = document.getElementById("current-date");

        if (dateElement) {
            const currentDate = new Date();

            const day = String(currentDate.getDate()).padStart(2, '0');
            const month = String(currentDate.getMonth() + 1).padStart(2, '0');
            const year = String(currentDate.getFullYear()).slice(-2);

            dateElement.textContent = `${day}/${month}/${year}`;
        }

        // Title Preview
        const title = titleInput.value.trim();
        const titleElement = document.createElement('h2');
        titleElement.className = 'post-title';
        titleElement.textContent = title ? title : 'Preview Title';
        postPreview.appendChild(titleElement);
        const mediaFiles = selectedFiles;

        // Media Preview
        if (mediaFiles.length > 0) {
            const slideContainer = document.createElement('div');
            slideContainer.className = 'post-slide-container';
            const mediaContainer = document.createElement('div');
            mediaContainer.className = 'post-media-container';

            // Create media element (either image or video)
            const mediaFile = mediaFiles[currentMediaIndex];
            let mediaElement;
            if (mediaFile.isExisting) {
                // Handle existing media
                if (mediaFile.type.startsWith('image')) {
                    mediaElement = document.createElement('img');
                    mediaElement.className = 'post-media';
                    mediaElement.src = mediaFile.path;
                } else if (mediaFile.type.startsWith('video')) {
                    mediaElement = document.createElement('video');
                    mediaElement.className = 'post-media';
                    mediaElement.controls = true;
                    const sourceElement = document.createElement('source');
                    sourceElement.src = mediaFile.path;
                    sourceElement.type = mediaFile.type;
                    mediaElement.appendChild(sourceElement);
                }
            } else {
                // Handle newly uploaded files
                if (mediaFile.type.startsWith('image')) {
                    mediaElement = document.createElement('img');
                    mediaElement.className = 'post-media';
                    mediaElement.src = mediaFile.url;
                } else if (mediaFile.type.startsWith('video')) {
                    mediaElement = document.createElement('video');
                    mediaElement.className = 'post-media';
                    mediaElement.controls = true;
                    mediaElement.src = mediaFile.url;
                }
            }

            mediaContainer.appendChild(mediaElement);
            slideContainer.appendChild(mediaContainer);
            postPreview.appendChild(slideContainer);

            // Add navigation arrows if there are multiple files
            if (mediaFiles.length > 1) {
                const leftArrow = document.createElement('button');
                leftArrow.className = 'nav-arrow';
                leftArrow.textContent = '<';
                leftArrow.onclick = () => handleMediaNavigation('left');
                mediaContainer.appendChild(leftArrow);

                const rightArrow = document.createElement('button');
                rightArrow.className = 'nav-arrow';
                rightArrow.textContent = '>';
                rightArrow.onclick = () => handleMediaNavigation('right');
                mediaContainer.appendChild(rightArrow);
            }

            // Add dots for navigation
            const dotsContainer = document.createElement('div');
            dotsContainer.className = 'dots-container';

            mediaFiles.forEach((_, i) => {
                const dot = document.createElement('span');
                dot.className = 'dot';
                dot.dataset.index = i;
                dot.onclick = () => handleDotClick(i);
                dotsContainer.appendChild(dot);
            });

            slideContainer.appendChild(dotsContainer);
            updateDots(dotsContainer);

            const sepLine = document.createElement('div');
            sepLine.className = 'sepLine';
            postPreview.appendChild(sepLine);
        }


        // DESCRIPTI>ON
        const description = descriptionInput.value.trim();
        const descriptionContainer = document.createElement('div');
        descriptionContainer.className = 'post-description-container';
        const descriptionElement = document.createElement('p');
        descriptionElement.className = 'post-description';
        if (mediaFiles.length > 0) {
            descriptionElement.style.whiteSpace = "nowrap"; // Ensure it stays on one line
        } else {
            descriptionElement.style.whiteSpace = "normal"; // Allow wrapping
        }
        descriptionContainer.appendChild(descriptionElement);
        postPreview.appendChild(descriptionContainer);

        // Set the description text
        if (description) {
            descriptionElement.textContent = description;

            // Dynamically truncate if the description overflows
            truncateText(descriptionElement, descriptionContainer, mediaFiles.length > 0);
        } else {
            descriptionElement.textContent = 'Preview description will appear here...';
        }
    }

    function updateDots(dotsContainer) {
        const dots = dotsContainer.getElementsByClassName('dot');
        for (let i = 0; i < dots.length; i++) {
            if (i === currentMediaIndex) {
                dots[i].classList.add('active');
            } else {
                dots[i].classList.remove('active');
            }
        }
    }

    function handleDotClick(index) {
        currentMediaIndex = index;
        updatePreview();
    }

    function truncateText(descriptionElement, containerElement, hasMedia) {
        let text = descriptionElement.textContent;
    
        descriptionElement.textContent = text;
    
        if (hasMedia) {
            if (descriptionElement.scrollWidth <= containerElement.clientWidth) {
                truncationApplied = false;
                return;
            }
            if (truncationApplied && text.length <= lastTruncatedLength) {
                return;
            }
            let start = 0;
            let end = text.length;
            let truncatedText = text;
    
            while (start <= end) {
                const mid = Math.floor((start + end) / 2);
                truncatedText = text.slice(0, mid) + "...";
                descriptionElement.textContent = truncatedText;
    
                if (descriptionElement.scrollWidth > containerElement.clientWidth) {
                    end = mid - 1; // Too long, reduce the length
                } else {
                    start = mid + 1; // Fits, try to include more characters
                }
            }
            let fineTuneIndex = end;
            truncatedText = text.slice(0, fineTuneIndex) + "...";
            descriptionElement.textContent = truncatedText;
    
            while (descriptionElement.scrollWidth > containerElement.clientWidth && fineTuneIndex > 0) {
                fineTuneIndex--;
                truncatedText = text.slice(0, fineTuneIndex) + "...";
                descriptionElement.textContent = truncatedText;
            }
            lastTruncatedLength = truncatedText.length;
            truncationApplied = true;
    
        } else {   
            if (descriptionElement.offsetHeight <= containerElement.offsetHeight) {
                truncationApplied = false;
                return;
            }
    
            if (truncationApplied && text.length <= lastTruncatedLength) {
                return;
            }
    
            // Start truncation process to fit within container height
            let start = 0;
            let end = text.length;
            let truncatedText = text;
    
            while (start <= end) {
                const mid = Math.floor((start + end) / 2);
                truncatedText = text.slice(0, mid) + "...";
                descriptionElement.textContent = truncatedText;
    
                if (descriptionElement.offsetHeight > containerElement.offsetHeight) {
                    end = mid - 1; // Too long, reduce the length
                } else {
                    start = mid + 1; // Fits, try to include more characters
                }
            }
    
            // Fine-tune the final truncation to ensure an exact fit
            let fineTuneIndex = end;
            truncatedText = text.slice(0, fineTuneIndex) + "...";
            descriptionElement.textContent = truncatedText;
    
            while (descriptionElement.offsetHeight > containerElement.offsetHeight && fineTuneIndex > 0) {
                fineTuneIndex--;
                truncatedText = text.slice(0, fineTuneIndex) + "...";
                descriptionElement.textContent = truncatedText;
            }
    
            // Record the truncated state
            lastTruncatedLength = truncatedText.length;
            truncationApplied = true;
        }
    }

    // Media Navigation
    function handleMediaNavigation(direction) {
        const mediaFiles = selectedFiles;
        if (direction === 'left') {
            currentMediaIndex = (currentMediaIndex - 1 + mediaFiles.length) % mediaFiles.length;
        } else if (direction === 'right') {
            currentMediaIndex = (currentMediaIndex + 1) % mediaFiles.length;
        }
        updatePreview(); // Update the preview after changing the index
    }

    //CHECKBOX
    const checkbox = document.getElementById('is_public');
    const stateIconElement = document.getElementById('state-icon');
    if (stateIconElement) {
        if (checkbox.checked) {
            stateIconElement.src = '/images/icon/public.png';
            stateIconElement.alt = 'Public';
        } else {
            stateIconElement.src = '/images/icon/lock.png';
            stateIconElement.alt = 'Private';
        }
    }

    // Add an event listener to handle checkbox changes
    checkbox.addEventListener('change', function () {
        if (checkbox.checked) {
            stateIconElement.src = '/images/icon/public.png';
            stateIconElement.alt = 'Public';
        } else {
            stateIconElement.src = '/images/icon/lock.png';
            stateIconElement.alt = 'Private';
        }
    });

    // Event Listeners
    titleInput.addEventListener('input', updatePreview);
    descriptionInput.addEventListener('input', updatePreview);
    fileInputPrev.addEventListener('change', function () {
        currentMediaIndex = 0; // Reset index when files change
        updatePreview();
    });

    // Initial preview rendering
    updatePreview();
});
