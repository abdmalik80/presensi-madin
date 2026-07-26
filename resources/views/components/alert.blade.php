@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
    class="flex items-center p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200" role="alert">
    <i class="fas fa-check-circle mr-2"></i>
    <span>{{ session('success') }}</span>
    <button @click="show = false" class="ml-auto text-green-600 hover:text-green-900"><i class="fas fa-times"></i></button>
</div>
@endif
@if(session('error'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
    class="flex items-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
    <i class="fas fa-exclamation-circle mr-2"></i>
    <span>{{ session('error') }}</span>
    <button @click="show = false" class="ml-auto text-red-600 hover:text-red-900"><i class="fas fa-times"></i></button>
</div>
@endif

@if($errors->any())
<div x-data="{ show: true }" x-show="show"
    class="flex items-start p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
    <i class="fas fa-exclamation-circle mt-0.5 mr-2"></i>
    <div>
        <span class="font-medium">Terdapat beberapa kesalahan:</span>
        <ul class="mt-1 list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    <button @click="show = false" class="ml-auto text-red-600 hover:text-red-900"><i class="fas fa-times"></i></button>
</div>
@endif
