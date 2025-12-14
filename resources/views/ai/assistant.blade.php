@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center mb-6">
                <svg class="w-8 h-8 text-purple-600 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
                <h1 class="text-3xl font-bold text-gray-800">المساعد الذكي</h1>
            </div>

            <div id="chat-container" class="bg-gray-50 rounded-lg p-4 mb-4 h-96 overflow-y-auto">
                <div class="text-center text-gray-500 py-8">
                    <p class="mb-4">مرحباً! أنا المساعد الذكي لنظام PEMS</p>
                    <p class="text-sm">اسألني عن الأرصدة، الميزانيات، المصروفات، أو التقارير</p>
                </div>
            </div>

            <div class="flex gap-2">
                <input 
                    type="text" 
                    id="query-input" 
                    placeholder="اكتب سؤالك هنا..."
                    class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    onkeypress="if(event.key === 'Enter') sendQuery()"
                >
                <button 
                    onclick="sendQuery()"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                    إرسال
                </button>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                <button onclick="quickQuery('كم رصيدي؟')" class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded text-sm">كم رصيدي؟</button>
                <button onclick="quickQuery('ما حالة الميزانية؟')" class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded text-sm">حالة الميزانية</button>
                <button onclick="quickQuery('مصروفات اليوم')" class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded text-sm">مصروفات اليوم</button>
                <button onclick="quickQuery('ملخص المشاريع')" class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded text-sm">ملخص المشاريع</button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4 flex items-center">
                    <svg class="w-5 h-5 ml-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    التحليلات التنبؤية
                </h3>
                <p class="text-gray-600 text-sm mb-4">توقع تجاوز الميزانية والمصروفات المستقبلية</p>
                <a href="{{ route('ai.analytics') }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">عرض التحليلات ←</a>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4 flex items-center">
                    <svg class="w-5 h-5 ml-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    كشف الاحتيال
                </h3>
                <p class="text-gray-600 text-sm mb-4">اكتشاف المصروفات المشبوهة والفواتير المكررة</p>
                <a href="{{ route('ai.fraud') }}" class="text-red-600 hover:text-red-800 text-sm font-semibold">عرض التقرير ←</a>
            </div>
        </div>
    </div>
</div>

<script>
function sendQuery() {
    const input = document.getElementById('query-input');
    const query = input.value.trim();
    
    if (!query) return;
    
    addMessage(query, 'user');
    input.value = '';
    
    fetch('/api/ai/assistant', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ query })
    })
    .then(res => res.json())
    .then(data => {
        addMessage(data.message, 'assistant', data.data);
    })
    .catch(err => {
        addMessage('عذراً، حدث خطأ. حاول مرة أخرى.', 'error');
    });
}

function quickQuery(query) {
    document.getElementById('query-input').value = query;
    sendQuery();
}

function addMessage(text, type, data = null) {
    const container = document.getElementById('chat-container');
    const firstMessage = container.querySelector('.text-center');
    if (firstMessage) firstMessage.remove();
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `mb-4 ${type === 'user' ? 'text-left' : 'text-right'}`;
    
    const bubble = document.createElement('div');
    bubble.className = `inline-block px-4 py-2 rounded-lg max-w-md ${
        type === 'user' ? 'bg-purple-600 text-white' : 
        type === 'error' ? 'bg-red-100 text-red-800' :
        'bg-gray-200 text-gray-800'
    }`;
    bubble.innerHTML = text.replace(/\n/g, '<br>');
    
    messageDiv.appendChild(bubble);
    
    if (data && Object.keys(data).length > 0) {
        const dataDiv = document.createElement('div');
        dataDiv.className = 'mt-2 text-sm bg-white p-3 rounded border inline-block';
        dataDiv.innerHTML = formatData(data);
        messageDiv.appendChild(dataDiv);
    }
    
    container.appendChild(messageDiv);
    container.scrollTop = container.scrollHeight;
}

function formatData(data) {
    if (Array.isArray(data)) {
        return data.map(item => `<div class="mb-1">• ${JSON.stringify(item)}</div>`).join('');
    }
    return Object.entries(data).map(([key, value]) => 
        `<div class="mb-1"><strong>${key}:</strong> ${typeof value === 'object' ? JSON.stringify(value) : value}</div>`
    ).join('');
}
</script>
@endsection
