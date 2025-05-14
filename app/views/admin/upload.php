<?php require_once 'app/views/layouts/main.php'; ?>

<div class="bg-dark-100 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-white">Upload New Content</h1>
        </div>

        <div class="bg-dark-200 rounded-lg shadow-lg overflow-hidden">
            <form action="<?= BASE_URL ?>/admin/upload" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-300">Title</label>
                    <input type="text" name="title" id="title" required
                           class="mt-1 block w-full border-dark-300 rounded-md bg-dark-300 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-300">Description</label>
                    <textarea name="description" id="description" rows="4" required
                              class="mt-1 block w-full border-dark-300 rounded-md bg-dark-300 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300">Content File</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dark-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" 
                                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-400">
                                <label for="content" class="relative cursor-pointer rounded-md font-medium text-indigo-400 hover:text-indigo-300">
                                    <span>Upload a file</span>
                                    <input id="content" name="content" type="file" class="sr-only" accept="video/*,image/*" required>
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-400">
                                Video (MP4, WebM) or Image (PNG, JPG, WebP) up to 100MB
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4">
                    <a href="<?= BASE_URL ?>/admin" 
                       class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-300 hover:text-white">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Upload Content
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Drag and drop functionality
const dropZone = document.querySelector('form');
const fileInput = document.querySelector('input[type="file"]');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, unhighlight, false);
});

function highlight(e) {
    dropZone.classList.add('border-indigo-500');
}

function unhighlight(e) {
    dropZone.classList.remove('border-indigo-500');
}

dropZone.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    fileInput.files = files;
}
</script>