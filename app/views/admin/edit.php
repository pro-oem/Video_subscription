<?php require_once 'app/views/layouts/main.php'; ?>

<div class="bg-dark-100 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-white">Edit Content</h1>
        </div>

        <div class="bg-dark-200 rounded-lg shadow-lg overflow-hidden">
            <form action="<?= BASE_URL ?>/admin/edit/<?= $content->id ?>" method="POST" class="p-6 space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-300">Title</label>
                    <input type="text" name="title" id="title" required
                           value="<?= htmlspecialchars($content->title) ?>"
                           class="mt-1 block w-full border-dark-300 rounded-md bg-dark-300 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-300">Description</label>
                    <textarea name="description" id="description" rows="4" required
                              class="mt-1 block w-full border-dark-300 rounded-md bg-dark-300 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    ><?= htmlspecialchars($content->description) ?></textarea>
                </div>

                <!-- Preview current content -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Current Content</label>
                    <?php if (strpos($content->content_type, 'video') !== false): ?>
                        <div class="relative pt-[56.25%] bg-dark-300 rounded-lg overflow-hidden">
                            <video 
                                class="absolute top-0 left-0 w-full h-full object-contain"
                                controls
                                controlsList="nodownload"
                                oncontextmenu="return false;"
                            >
                                <source src="<?= BASE_URL ?>/content/stream/<?= $content->id ?>" type="<?= $content->content_type ?>">
                            </video>
                        </div>
                    <?php else: ?>
                        <div class="relative bg-dark-300 rounded-lg overflow-hidden">
                            <img 
                                src="<?= BASE_URL ?>/content/stream/<?= $content->id ?>"
                                class="max-w-full h-auto"
                                oncontextmenu="return false;"
                            >
                        </div>
                    <?php endif; ?>
                </div>

                <div class="flex items-center justify-end space-x-4">
                    <a href="<?= BASE_URL ?>/admin" 
                       class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-300 hover:text-white">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>