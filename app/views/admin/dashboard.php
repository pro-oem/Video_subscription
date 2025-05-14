<?php require_once 'app/views/layouts/main.php'; ?>

<div class="bg-dark-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-white">Content Management</h1>
            <a href="<?= BASE_URL ?>/admin/upload" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                Upload New Content
            </a>
        </div>

        <div class="bg-dark-200 rounded-lg shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-dark-300">
                    <thead class="bg-dark-300">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Title
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Type
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Created
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-300">
                        <?php foreach ($content as $item): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-white"><?= htmlspecialchars($item->title) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?= strpos($item->content_type, 'video') !== false ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' ?>">
                                        <?= strpos($item->content_type, 'video') !== false ? 'Video' : 'Image' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    <?= date('Y-m-d H:i', strtotime($item->created_at)) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="<?= BASE_URL ?>/admin/edit/<?= $item->id ?>" class="text-indigo-400 hover:text-indigo-300 mr-4">Edit</a>
                                    <a href="#" onclick="deleteContent(<?= $item->id ?>)" class="text-red-400 hover:text-red-300">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function deleteContent(id) {
    if (confirm('Are you sure you want to delete this content? This action cannot be undone.')) {
        window.location.href = `${BASE_URL}/admin/delete/${id}`;
    }
}
</script>