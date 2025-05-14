<?php require_once 'app/views/layouts/main.php'; ?>

<div class="bg-dark-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Content Section -->
            <div class="lg:col-span-2">
                <div class="bg-dark-200 rounded-lg shadow-lg overflow-hidden">
                    <?php if (strpos($content->content_type, 'video') !== false): ?>
                        <div class="relative pt-[56.25%]">
                            <video 
                                class="absolute top-0 left-0 w-full h-full"
                                controls
                                controlsList="nodownload"
                                oncontextmenu="return false;"
                            >
                                <source src="<?= BASE_URL ?>/content/stream/<?= $content->id ?>/<?= $accessToken ?>" 
                                        type="<?= $content->content_type ?>">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    <?php else: ?>
                        <div class="relative">
                            <img 
                                src="<?= BASE_URL ?>/content/stream/<?= $content->id ?>/<?= $accessToken ?>"
                                class="w-full h-auto"
                                alt="<?= htmlspecialchars($content->title) ?>"
                                oncontextmenu="return false;"
                            >
                        </div>
                    <?php endif; ?>

                    <div class="p-6">
                        <h1 class="text-2xl font-bold text-white mb-4">
                            <?= htmlspecialchars($content->title) ?>
                        </h1>
                        <p class="text-gray-400">
                            <?= nl2br(htmlspecialchars($content->description)) ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Chat Section -->
            <?php if ($chatEnabled): ?>
            <div class="lg:col-span-1">
                <div class="bg-dark-200 rounded-lg shadow-lg h-full flex flex-col">
                    <div class="p-4 border-b border-dark-300">
                        <h2 class="text-lg font-medium text-white">AI Chat Assistant</h2>
                    </div>
                    
                    <div id="chat-messages" class="flex-1 p-4 space-y-4 overflow-y-auto" style="max-height: 600px;">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="bg-dark-300 rounded-lg p-3 text-gray-300">
                                Hello! I'm your AI assistant. Feel free to ask me any questions about the content you're watching.
                            </div>
                        </div>
                    </div>

                    <div class="p-4 border-t border-dark-300">
                        <form id="chat-form" class="flex space-x-3">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            <input 
                                type="text" 
                                id="chat-input"
                                class="flex-1 rounded-md border-gray-700 bg-dark-300 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Type your message..."
                            >
                            <button 
                                type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Send
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');
    
    if (chatForm) {
        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const message = chatInput.value.trim();
            if (!message) return;
            
            // Add user message to chat
            appendMessage('user', message);
            chatInput.value = '';
            
            try {
                const response = await fetch('<?= BASE_URL ?>/content/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?= $csrf_token ?>'
                    },
                    body: JSON.stringify({ message })
                });
                
                const data = await response.json();
                
                if (data.error) {
                    throw new Error(data.error);
                }
                
                // Add AI response to chat
                appendMessage('ai', data.response);
            } catch (error) {
                appendMessage('system', 'Sorry, there was an error processing your message. Please try again.');
            }
        });
    }
    
    function appendMessage(type, message) {
        const div = document.createElement('div');
        div.className = 'flex items-start space-x-3';
        
        let iconSvg, bgColor;
        if (type === 'user') {
            iconSvg = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>`;
            bgColor = 'bg-green-600';
        } else if (type === 'ai') {
            iconSvg = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>`;
            bgColor = 'bg-indigo-600';
        } else {
            iconSvg = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>`;
            bgColor = 'bg-red-600';
        }
        
        div.innerHTML = `
            <div class="flex-shrink-0">
                <div class="w-8 h-8 rounded-full ${bgColor} flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${iconSvg}
                    </svg>
                </div>
            </div>
            <div class="bg-dark-300 rounded-lg p-3 text-gray-300">
                ${message}
            </div>
        `;
        
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
});</script>