// AI Assistant Chat
class AIAssistant {
    constructor() {
        this.isOpen = false;
        this.init();
    }
    
    init() {
        this.createChatWidget();
        this.bindEvents();
    }
    
    createChatWidget() {
        const widget = document.createElement('div');
        widget.innerHTML = `
            <div id="ai-assistant" class="fixed bottom-4 right-4 z-50">
                <button id="chat-toggle" class="bg-blue-600 hover:bg-blue-700 text-white rounded-full p-4 shadow-lg transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </button>
                
                <div id="chat-window" class="hidden absolute bottom-16 right-0 w-80 bg-white rounded-lg shadow-xl border border-gray-200">
                    <div class="p-4 border-b border-gray-200 bg-blue-600 text-white rounded-t-lg">
                        <h3 class="font-medium">المساعد الذكي</h3>
                        <p class="text-xs opacity-90">اسأل عن الأرصدة والميزانيات</p>
                    </div>
                    
                    <div id="chat-messages" class="h-64 overflow-y-auto p-4 space-y-3">
                        <div class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">
                            مرحباً! يمكنني مساعدتك في الاستفسار عن:<br>
                            • الأرصدة والعهد<br>
                            • حالة الميزانيات<br>
                            • المصروفات اليومية<br>
                            • التقارير السريعة
                        </div>
                    </div>
                    
                    <div class="p-4 border-t border-gray-200">
                        <div class="flex gap-2">
                            <input type="text" id="chat-input" placeholder="اكتب سؤالك هنا..." 
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button id="send-message" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                                إرسال
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(widget);
    }
    
    bindEvents() {
        document.getElementById('chat-toggle').addEventListener('click', () => {
            this.toggleChat();
        });
        
        document.getElementById('send-message').addEventListener('click', () => {
            this.sendMessage();
        });
        
        document.getElementById('chat-input').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.sendMessage();
            }
        });
    }
    
    toggleChat() {
        const chatWindow = document.getElementById('chat-window');
        this.isOpen = !this.isOpen;
        
        if (this.isOpen) {
            chatWindow.classList.remove('hidden');
        } else {
            chatWindow.classList.add('hidden');
        }
    }
    
    async sendMessage() {
        const input = document.getElementById('chat-input');
        const message = input.value.trim();
        
        if (!message) return;
        
        this.addMessage(message, 'user');
        input.value = '';
        
        try {
            const response = await fetch('/api/assistant/query', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ query: message })
            });
            
            const data = await response.json();
            this.addMessage(data.message, 'assistant', data.data);
            
        } catch (error) {
            this.addMessage('عذراً، حدث خطأ في الاتصال', 'assistant');
        }
    }
    
    addMessage(message, sender, data = null) {
        const messagesContainer = document.getElementById('chat-messages');
        const messageDiv = document.createElement('div');
        
        if (sender === 'user') {
            messageDiv.className = 'text-sm bg-blue-100 text-blue-900 p-3 rounded-lg ml-8';
        } else {
            messageDiv.className = 'text-sm bg-gray-100 text-gray-900 p-3 rounded-lg mr-8';
        }
        
        messageDiv.innerHTML = message;
        
        if (data && Object.keys(data).length > 0) {
            const dataDiv = document.createElement('div');
            dataDiv.className = 'mt-2 text-xs bg-white p-2 rounded border';
            dataDiv.innerHTML = this.formatData(data);
            messageDiv.appendChild(dataDiv);
        }
        
        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    formatData(data) {
        if (Array.isArray(data)) {
            return data.map(item => `• ${JSON.stringify(item)}`).join('<br>');
        }
        
        let formatted = '';
        for (const [key, value] of Object.entries(data)) {
            formatted += `<strong>${key}:</strong> ${value}<br>`;
        }
        return formatted;
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new AIAssistant();
});