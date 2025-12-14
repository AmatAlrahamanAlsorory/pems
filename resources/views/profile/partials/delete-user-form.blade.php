<div class="space-y-4">
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-center gap-3 mb-3">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <h3 class="font-bold text-red-900">حذف الحساب</h3>
        </div>
        <p class="text-sm text-red-800 mb-4">
            عند حذف حسابك، سيتم حذف جميع بياناتك نهائياً. يرجى حفظ أي بيانات مهمة قبل الحذف.
        </p>
        
        <button onclick="showDeleteModal()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            حذف الحساب
        </button>
    </div>
</div>

<!-- نموذج تأكيد الحذف -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center gap-3 mb-4">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <h3 class="text-lg font-bold text-gray-900">تأكيد حذف الحساب</h3>
        </div>
        
        <p class="text-gray-600 mb-4">
            هل أنت متأكد من رغبتك في حذف حسابك؟ هذه العملية لا يمكن التراجع عنها.
        </p>
        
        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">اكتب كلمة المرور للتأكيد</label>
                <input name="password" type="password" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="كلمة المرور" required>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium">
                    حذف الحساب
                </button>
                <button type="button" onclick="hideDeleteModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function showDeleteModal() {
        document.getElementById('deleteModal').classList.remove('hidden');
    }
    
    function hideDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
</script>
